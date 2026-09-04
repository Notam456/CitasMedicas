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

$ErrorActionPreference = 'Stop'

$base = Split-Path -Parent $MyInvocation.MyCommand.Path
$configPath = Join-Path $base 'config.json'

$config = @{ port = 8000; phpPath = $null; appPath = $null; browser = 'auto'; postgresPort = 5432 }
if (Test-Path $configPath) {
    $cfg = Get-Content $configPath -Raw | ConvertFrom-Json
    if ($cfg.port)         { $config.port         = [int]$cfg.port }
    if ($cfg.phpPath)      { $config.phpPath      = [string]$cfg.phpPath }
    if ($cfg.appPath)      { $config.appPath      = [string]$cfg.appPath }
    if ($cfg.browser)      { $config.browser      = [string]$cfg.browser }
    if ($cfg.postgresPort) { $config.postgresPort = [int]$cfg.postgresPort }
}

$port = $config.port
$appDir = if ($config.appPath -and (Test-Path $config.appPath)) { $config.appPath } else { Join-Path $base '..' }
$appDir = (Resolve-Path $appDir).Path
$baseUrl = "http://127.0.0.1:$port"

$stateDir = Join-Path $env:LOCALAPPDATA 'SAGECIM'
New-Item -ItemType Directory -Force -Path $stateDir | Out-Null
$pidFile = Join-Path $stateDir 'php.pid'
$tokenFile = Join-Path $stateDir 'token.txt'
$profileDir = Join-Path $stateDir 'edge-profile'

$phpProc = $null
$browser = $null
$token = $null

function Mensaje([string]$texto, [string]$tipo = 'Information') {
    Add-Type -AssemblyName System.Windows.Forms
    [System.Windows.Forms.MessageBox]::Show($texto, 'SAGECIM', 'OK', $tipo) | Out-Null
}

function Test-Puerto([int]$p) {
    $cliente = New-Object System.Net.Sockets.TcpClient
    try {
        $cliente.Connect('127.0.0.1', $p)
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

    $proc = Start-Process -FilePath $php -ArgumentList @('artisan', 'serve', '--host=127.0.0.1', "--port=$port") -WorkingDirectory $appDir -WindowStyle Hidden -PassThru
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
        throw "El puerto $port ya esta en uso por otro programa. Cierra ese programa y vuelve a abrir SAGECIM."
    }

    # --- Verificacion previa de PostgreSQL ---
    if (-not (Test-Postgres)) {
        throw "PostgreSQL no esta encendida (puerto $($config.postgresPort)). Enciende el servicio de PostgreSQL y vuelve a abrir SAGECIM."
    }

    # --- Arranque del servidor ---
    $token = [guid]::NewGuid().ToString('N')
    Set-Content -Path $tokenFile -Value $token
    $phpProc = Iniciar-Servidor

    # --- Abrir la ventana y esperar a que aparezca ---
    $browser = Abrir-Ventana "$baseUrl/?_lanzador=$token"
    $ventana = $false
    for ($i = 0; $i -lt 60; $i++) {
        Start-Sleep -Seconds 1
        $browser.Refresh()
        if ($browser.HasExited) { break }
        if ($browser.MainWindowHandle -ne 0) { $ventana = $true; break }
    }
    if (-not $ventana) { throw 'No se pudo abrir la ventana de SAGECIM.' }

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

            Set-Content -Path $tokenFile -Value $token
            Recargar-Ventana $browser.Id
        }
    }
} catch {
    Mensaje $_.Exception.Message 'Error'
} finally {
    if ($phpProc) {
        if ($browser -and -not $browser.HasExited) { Matar-Proc $browser.Id }
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
