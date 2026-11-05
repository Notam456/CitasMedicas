# SAGECIM - Lanzador de un clic
# Levanta `php artisan serve` en segundo plano (sin consola), abre la app en
# una ventana de navegador dedicada (modo app) y, al cerrar la ventana:
#   1) mata el arbol del navegador,
#   2) cierra la sesion del usuario (POST /lanzador/cerrar-sesion),
#   3) mata el arbol de PHP.
# Ademas:
#   - Verifica que PostgreSQL este encendida antes de arrancar.
#   - Vigila el servidor: si PHP muere con la ventana abierta, lo reinicia
#     con reintentos y refresca la ventana en su lugar (sin recargarla).
# Si una instancia anterior quedo huerfana (cierre forzado), la limpia al
# iniciar para que el puerto nunca quede bloqueado.
#
# Modo host (predeterminado: la PC que comparte el sistema en la red local):
#   El servidor escucha en 0.0.0.0, se muestra la URL de la red, se crea la
#   regla de firewall acotada a la subred local y, al cerrar la ventana, el
#   servidor sigue encendido para los demas (se pregunta antes de detenerlo).
#   Arranca con APP_ENV=production y APP_DEBUG=false para no mostrar errores
#   ni credenciales a los usuarios de la red.
#   Para uso en una sola PC sin red se pone "mode": "local" en config.json:
#   escucha en 127.0.0.1 y al cerrar la ventana detiene el servidor.

param([switch]$ConfigurarRed)

$ErrorActionPreference = 'Stop'

$base = Split-Path -Parent $MyInvocation.MyCommand.Path

$config = @{ port = 8000; phpPath = $null; appPath = $null; browser = 'auto'; postgresPort = 5432; mode = 'host' }
# config.json es compartido por todo el equipo. Por defecto arranca en modo
# host; para volver a uso local se pone "mode": "local".
$configPath = Join-Path $base 'config.json'
if (Test-Path $configPath) {
    $cfg = Get-Content $configPath -Raw | ConvertFrom-Json
    if ($cfg.port)         { $config.port         = [int]$cfg.port }
    if ($cfg.phpPath)      { $config.phpPath      = [string]$cfg.phpPath }
    if ($cfg.appPath)      { $config.appPath      = [string]$cfg.appPath }
    if ($cfg.browser)      { $config.browser      = [string]$cfg.browser }
    if ($cfg.postgresPort) { $config.postgresPort = [int]$cfg.postgresPort }
    if ($cfg.mode)         { $config.mode         = [string]$cfg.mode }
}

$port = $config.port
$modoHost = ($config.mode -eq 'host')
$bindHost = if ($modoHost) { '0.0.0.0' } else { '127.0.0.1' }
$appDir = if ($config.appPath -and (Test-Path $config.appPath)) { $config.appPath } else { Join-Path $base '..' }
$appDir = (Resolve-Path $appDir).Path
$baseUrl = "http://127.0.0.1:$port"
$reglaFirewall = "SAGECIM PHP $port"

$stateDir = Join-Path $env:LOCALAPPDATA 'SAGECIM'
New-Item -ItemType Directory -Force -Path $stateDir | Out-Null
$pidFile = Join-Path $stateDir 'php.pid'
$tokenFile = Join-Path $stateDir 'token.txt'
$profileDir = Join-Path $stateDir 'edge-profile'
$logOut = Join-Path $stateDir 'server.out.log'
$logErr = Join-Path $stateDir 'server.err.log'
$logFirewall = Join-Path $stateDir 'firewall.log'

# IP del adaptador que tiene la ruta por defecto: la que se comparte.
function Obtener-IpLan() {
    $ruta = Get-NetRoute -DestinationPrefix '0.0.0.0/0' -ErrorAction SilentlyContinue | Sort-Object RouteMetric | Select-Object -First 1
    if ($ruta) {
        $ip = Get-NetIPAddress -AddressFamily IPv4 -InterfaceIndex $ruta.InterfaceIndex -ErrorAction SilentlyContinue |
            Where-Object { $_.IPAddress -notmatch '^169\.254\.' } | Select-Object -First 1
        if ($ip) { return $ip.IPAddress }
    }
    return $null
}

$ipLan = if ($modoHost) { Obtener-IpLan } else { $null }
$publicUrl = if ($ipLan) { "http://${ipLan}:$port" } else { $baseUrl }

$phpProc = $null
$browser = $null
$token = $null

function Mensaje([string]$texto, [string]$tipo = 'Information') {
    Add-Type -AssemblyName System.Windows.Forms
    [System.Windows.Forms.MessageBox]::Show($texto, 'SAGECIM', 'OK', $tipo) | Out-Null
}

function Preguntar-SN([string]$texto, [string]$porDefecto = 'Yes') {
    Add-Type -AssemblyName System.Windows.Forms
    $boton = if ($porDefecto -eq 'No') { 'Button2' } else { 'Button1' }
    $r = [System.Windows.Forms.MessageBox]::Show($texto, 'SAGECIM', 'YesNo', 'Question', $boton)
    return ($r -eq 'Yes')
}

function Test-Puerto([int]$p, [string]$destino = '127.0.0.1') {
    $cliente = New-Object System.Net.Sockets.TcpClient
    try {
        $cliente.Connect($destino, $p)
        return $true
    } catch {
        return $false
    } finally {
        $cliente.Dispose()
    }
}

function Matar-Proc([int]$id) {
    if (-not $id -or $id -le 0) { return }
    try { & taskkill /PID $id /T /F 2>$null | Out-Null } catch { }
}

function Localizar-Php() {
    if ($config.phpPath -and (Test-Path $config.phpPath)) { return (Resolve-Path $config.phpPath).Path }
    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }
    $candidatos = @('C:\php\php.exe', "$env:ProgramFiles\php\php.exe")
    foreach ($c in $candidatos) { if (Test-Path $c) { return $c } }
    return $null
}

# --- Firewall (solo modo host) ---
# Se usa netsh y no Get-/New-NetFirewallRule porque esos cmdlets exigen
# elevacion incluso para consultar: sin admin devuelven "Acceso denegado" y el
# lanzador crearia la regla en cada arranque creyendo que no existe.

function Test-ReglaFirewall() {
    & netsh advfirewall firewall show rule "name=$reglaFirewall" 2>$null | Out-Null
    return ($LASTEXITCODE -eq 0)
}

function Test-RedPrivada() {
    $otras = Get-NetConnectionProfile -ErrorAction SilentlyContinue | Where-Object { $_.NetworkCategory -ne 'Private' }
    return (-not $otras)
}

function Comando-Firewall() {
    $php = Localizar-Php
    return "netsh advfirewall firewall add rule name=`"$reglaFirewall`" dir=in action=allow program=`"$php`" protocol=TCP localport=$port profile=private remoteip=localsubnet"
}

# Crea la regla acotada al ejecutable, al puerto, al perfil privado y a la
# subred local. Requiere permisos de administrador.
function Configurar-Red() {
    $php = Localizar-Php
    if (-not $php) { throw 'No se encontro PHP.' }
    $salida = (& netsh advfirewall firewall add rule "name=$reglaFirewall" dir=in action=allow program="$php" protocol=TCP localport=$port profile=private remoteip=localsubnet 2>&1) -join ' '
    if ($LASTEXITCODE -ne 0) { throw "No se pudo crear la regla: $($salida.Trim())" }
}

# Relanza el script elevado solo para crear la regla. Devuelve el codigo de salida.
function Instalar-ReglaFirewall() {
    $script = Join-Path $base 'sagecim-launcher.ps1'
    Remove-Item $logFirewall -Force -ErrorAction SilentlyContinue
    $p = Start-Process -FilePath 'powershell.exe' -Verb RunAs -Wait -PassThru -ArgumentList "-NoProfile -ExecutionPolicy Bypass -File `"$script`" -ConfigurarRed"
    return $p.ExitCode
}

function Localizar-Navegador() {
    $rutas = @{
        edge   = @(
            "$env:ProgramFiles(x86)\Microsoft\Edge\Application\msedge.exe",
            "$env:ProgramFiles\Microsoft\Edge\Application\msedge.exe"
        )
        chrome = @(
            "$env:ProgramFiles\Google\Chrome\Application\chrome.exe",
            "$env:ProgramFiles(x86)\Google\Chrome\Application\chrome.exe",
            "$env:LOCALAPPDATA\Google\Chrome\Application\chrome.exe"
        )
        brave  = @(
            "$env:ProgramFiles\BraveSoftware\Brave-Browser\Application\brave.exe",
            "$env:ProgramFiles(x86)\BraveSoftware\Brave-Browser\Application\brave.exe",
            "$env:LOCALAPPDATA\BraveSoftware\Brave-Browser\Application\brave.exe"
        )
    }
    $orden = switch ($config.browser) {
        'brave'  { @('brave', 'edge', 'chrome') }
        'edge'   { @('edge', 'chrome', 'brave') }
        'chrome' { @('chrome', 'edge', 'brave') }
        default  { @('edge', 'chrome', 'brave') }
    }
    foreach ($nombre in $orden) {
        foreach ($ruta in $rutas[$nombre]) {
            if (Test-Path $ruta) { return $ruta }
        }
    }
    return $null
}

function Abrir-Ventana([string]$url) {
    $navegadorPath = Localizar-Navegador
    if (-not $navegadorPath) { throw 'No se encontro Microsoft Edge, Chrome ni Brave. Instala uno de ellos para usar SAGECIM.' }
    return Start-Process -FilePath $navegadorPath -ArgumentList @("--app=$url", "--user-data-dir=$profileDir", '--start-maximized', '--no-first-run', '--no-default-browser-check') -PassThru
}

# Refresca la pagina en la ventana ya abierta (F5) sin cerrarla ni recargarla.
function Recargar-Ventana([int]$id) {
    try {
        Add-Type -AssemblyName Microsoft.VisualBasic
        [Microsoft.VisualBasic.Interaction]::AppActivate($id) | Out-Null
        Start-Sleep -Milliseconds 300
        $wsh = New-Object -ComObject WScript.Shell
        $wsh.SendKeys('{F5}')
    } catch { }
}

# Arranca `php artisan serve` y espera a que responda. Devuelve el proceso.
function Iniciar-Servidor() {
    $php = Localizar-Php
    if (-not $php) { throw 'No se encontro PHP. Instala PHP 8.2+ y vuelve a abrir SAGECIM.' }

    if ($modoHost) {
        # Endurecimiento solo en modo host: Laravel no pisa estas variables con
        # el .env, asi que el modo desarrollo queda intacto.
        $env:APP_ENV = 'production'
        $env:APP_DEBUG = 'false'
        $env:APP_URL = $publicUrl

        # Con config cache, Laravel ignora las variables de arriba y seguiria
        # con APP_DEBUG=true: se elimina el cache para que el modo host mande.
        Remove-Item (Join-Path $appDir 'bootstrap\cache\config.php') -Force -ErrorAction SilentlyContinue
    }

    $proc = Start-Process -FilePath $php -ArgumentList @('artisan', 'serve', "--host=$bindHost", "--port=$port") -WorkingDirectory $appDir -WindowStyle Hidden -RedirectStandardOutput $logOut -RedirectStandardError $logErr -PassThru
    Set-Content -Path $pidFile -Value $proc.Id

    $listo = $false
    for ($i = 0; $i -lt 60; $i++) {
        Start-Sleep -Milliseconds 500
        $proc.Refresh()
        if ($proc.HasExited) { break }
        if (Test-Puerto $port) { $listo = $true; break }
    }
    if (-not $listo) {
        Matar-Proc $proc.Id
        throw 'El servidor PHP no levanto a tiempo. Revisa que PostgreSQL este encendida y vuelve a abrir SAGECIM.'
    }

    return $proc
}

# Verifica que PostgreSQL esta escuchando en el puerto configurado.
function Test-Postgres() {
    if ($config.postgresPort -le 0) { return $true }
    return Test-Puerto $config.postgresPort
}

# Modo elevado: solo crea la regla de firewall y termina. El motivo del fallo
# se guarda en firewall.log para que el lanzador lo muestre.
if ($ConfigurarRed) {
    try {
        Configurar-Red
        exit 0
    } catch {
        Set-Content -Path $logFirewall -Value $_.Exception.Message
        exit 1
    }
}

try {
    # --- Auto-recuperacion: estado de la instancia anterior ---
    $phpAnterior = $null
    if (Test-Path $pidFile) {
        $phpAnterior = [int]((Get-Content $pidFile -Raw).Trim())
    }
    $puertoActivo = Test-Puerto $port

    if ($phpAnterior -and $phpAnterior -gt 0) {
        $procAnterior = Get-Process -Id $phpAnterior -ErrorAction SilentlyContinue
        if ($procAnterior -and $puertoActivo) {
            # El sistema ya esta abierto: solo enfocar la ventana y terminar.
            Abrir-Ventana $baseUrl | Out-Null
            exit 0
        }
        # Huerfano de un cierre forzado: matarlo y seguir limpio.
        Matar-Proc $phpAnterior
        Remove-Item $pidFile, $tokenFile -Force -ErrorAction SilentlyContinue
    } elseif ($puertoActivo) {
        # Sin pid puede ser nuestro servidor (por ejemplo, tras reiniciar el
        # lanzador) o un programa ajeno. El endpoint /up lo distingue.
        $esNuestro = $false
        if ($modoHost) {
            try {
                $r = Invoke-WebRequest -Uri "$baseUrl/up" -UseBasicParsing -TimeoutSec 3
                $esNuestro = ($r.StatusCode -eq 200)
            } catch { }
        }
        if ($esNuestro) {
            Abrir-Ventana $baseUrl | Out-Null
            exit 0
        }
        throw "El puerto $port ya esta en uso por otro programa. Cierra ese programa y vuelve a abrir SAGECIM."
    }

    # --- Verificacion previa de PostgreSQL ---
    if (-not (Test-Postgres)) {
        throw "PostgreSQL no esta encendida (puerto $($config.postgresPort)). Enciende el servicio de PostgreSQL y vuelve a abrir SAGECIM."
    }

    # --- Red y firewall (solo modo host) ---
    if ($modoHost) {
        if (-not $ipLan) {
            Mensaje "No se detecto una red local, asi que SAGECIM solo sera accesible desde esta PC.`n`nConecta el equipo a la red y vuelve a abrir SAGECIM para compartirlo." 'Warning'
        }

        if (-not (Test-RedPrivada)) {
            Mensaje "La red de este equipo esta configurada como Publica, asi que Windows bloqueara a los demas equipos.`n`nCambia la red a Privada y vuelve a abrir SAGECIM." 'Warning'
        }

        if (-not (Test-ReglaFirewall)) {
            if (Preguntar-SN "Windows no tiene una regla para permitir SAGECIM en la red local.`n`n¿Crearla ahora? Se pediran permisos de administrador." 'Yes') {
                try {
                    $codigo = Instalar-ReglaFirewall
                } catch {
                    throw "Se cerro el aviso de permisos de administrador, asi que no se creo la regla de firewall. Vuelve a abrir SAGECIM y acepta el aviso."
                }
                if ($codigo -ne 0) {
                    $detalle = if (Test-Path $logFirewall) { (Get-Content $logFirewall -Raw).Trim() } else { "codigo $codigo" }
                    throw "No se pudo crear la regla de firewall.`n`n$detalle"
                }
            }
            if (-not (Test-ReglaFirewall)) {
                throw "Falta la regla de firewall para compartir SAGECIM.`n`nEjecuta este comando como administrador:`n`n$(Comando-Firewall)"
            }
        }
    }

    # --- Arranque del servidor ---
    if (-not $modoHost) {
        $token = [guid]::NewGuid().ToString('N')
        Set-Content -Path $tokenFile -Value $token
    }
    $phpProc = Iniciar-Servidor

    # Prueba de bind: si solo escuchara en 127.0.0.1, la IP LAN no responde.
    if ($modoHost -and $ipLan -and -not (Test-Puerto $port $ipLan)) {
        throw "El servidor no acepta conexiones en $ipLan. Revisa la regla de firewall y el perfil de red."
    }

    # --- Abrir la ventana y esperar a que aparezca ---
    $url = if ($token) { "$baseUrl/?_lanzador=$token" } else { $baseUrl }
    $browser = Abrir-Ventana $url
    $ventana = $false
    for ($i = 0; $i -lt 60; $i++) {
        Start-Sleep -Seconds 1
        $browser.Refresh()
        if ($browser.HasExited) { break }
        if ($browser.MainWindowHandle -ne 0) { $ventana = $true; break }
    }
    if (-not $ventana) { throw 'No se pudo abrir la ventana de SAGECIM.' }

    if ($modoHost -and $ipLan) {
        try { Set-Clipboard -Value $publicUrl } catch { }
        Mensaje "SAGECIM esta disponible para los demas equipos en:`n`n$publicUrl`n`nLa direccion quedo copiada al portapapeles." 'Information'
    }

    # --- Vigilancia: al cerrar la ventana, apagar todo ---
    while ($true) {
        Start-Sleep -Seconds 2
        $browser.Refresh()
        if ($browser.HasExited) { break }
        if ($browser.MainWindowHandle -eq 0) {
            Start-Sleep -Seconds 3
            $browser.Refresh()
            if ($browser.HasExited -or $browser.MainWindowHandle -eq 0) { break }
        }

        # Watchdog del servidor: si PHP murio con la ventana abierta, reiniciarlo.
        if (-not (Test-Puerto $port)) {
            $reiniciado = $false
            for ($intento = 1; $intento -le 3; $intento++) {
                if ($phpProc) {
                    $phpProc.Refresh()
                    if (-not $phpProc.HasExited) { Matar-Proc $phpProc.Id }
                }
                try {
                    $phpProc = Iniciar-Servidor
                    $reiniciado = $true
                    break
                } catch {
                    Start-Sleep -Seconds 2
                }
            }

            if (-not $reiniciado) {
                throw 'El servidor de SAGECIM fallo varias veces seguidas. Revisa PostgreSQL y vuelve a abrir SAGECIM.'
            }

            if ($token) { Set-Content -Path $tokenFile -Value $token }
            Recargar-Ventana $browser.Id
        }
    }
} catch {
    Mensaje $_.Exception.Message 'Error'
} finally {
    if ($browser -and -not $browser.HasExited) { Matar-Proc $browser.Id }

    # En modo host el servidor no debe morir con la ventana: los demas equipos
    # lo estarian usando. Se pregunta y, por defecto, se deja encendido.
    $detener = -not $modoHost
    if ($modoHost -and $phpProc) {
        try {
            $detener = Preguntar-SN "El servidor seguira activo para los demas equipos.`n`n¿Detenerlo ahora?" 'No'
        } catch {
            $detener = $false
        }
    }

    if ($detener -and $phpProc) {
        if ($token) {
            try {
                Invoke-WebRequest -Uri "$baseUrl/lanzador/cerrar-sesion" -Method Post -Body @{ token = $token } -UseBasicParsing -TimeoutSec 5 | Out-Null
            } catch { }
        }
        Matar-Proc $phpProc.Id
        Remove-Item $pidFile, $tokenFile -Force -ErrorAction SilentlyContinue
    }
}

exit 0
