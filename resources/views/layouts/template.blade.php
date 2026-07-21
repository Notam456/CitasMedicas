<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>

    <style>
        /* esta vaina es para mostrar el spinner de carga y que no salga el html sin estilos */
        body {
            visibility: hidden;
        }
        #spinner {
            visibility: visible;
        }
        .swal2-popup {
            transform: scale(0.85);
            transform-origin: center center;
        }
    </style>
    
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link href="{{url('assets/img/favicon.ico')}}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="{{asset('assets/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/bootstrap-icons.css')}}" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{asset('assets/lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css')}}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
            
        <!-- Sidebar End -->


        <!-- Content Start -->
        <!-- AQUI DEBERIA HABER UN DIV DE CLASE content PERO GENERA UN CONFLICTO EN LAS DEMAS VISTAS -->
            <!-- Navbar Start -->
            
            <!-- Navbar End -->


            @yield('content')


            <!-- Footer Start -->

            <!-- Footer End -->
        <!-- AQUI DEBERIA TERMINAR UN DIV DE CLASE content PERO GENERA UN CONFLICTO EN LAS DEMAS VISTAS -->
        <!-- Content End -->


    </div>

    @include('sweetalert::alert')

    <!-- JavaScript Libraries -->
    <script src="{{asset('assets/js/jquery-3.4.1.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/lib/chart/chart.min.js')}}"></script>
    <script src="{{asset('assets/lib/easing/easing.min.js')}}"></script>
    <script src="{{asset('assets/lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{asset('assets/lib/owlcarousel/owl.carousel.min.js')}}"></script>
    <script src="{{asset('assets/lib/tempusdominus/js/moment.min.js')}}"></script>
    <script src="{{asset('assets/lib/tempusdominus/js/moment-timezone.min.js')}}"></script>
    <script src="{{asset('assets/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js')}}"></script>

    <!-- Template Javascript -->
    <script src="{{asset('assets/js/main.js')}}"></script>
    <script>
        function cargarNotificaciones() {
            fetch('/notificaciones/no-leidas')
                .then(r => r.json())
                .then(data => {
                    var badge = document.getElementById('notif-count');
                    var lista = document.getElementById('notif-lista');
                    if (!badge || !lista) return;

                    if (data.total > 0) {
                        badge.style.display = '';
                        badge.textContent = data.total > 99 ? '99+' : data.total;
                    } else {
                        badge.style.display = 'none';
                    }

                    lista.innerHTML = '';
                    if (data.ultimas.length === 0) {
                        lista.innerHTML = '<div class="text-center py-3 text-muted small"><i class="bi bi-bell-slash"></i> No hay notificaciones</div>';
                    } else {
                        data.ultimas.forEach(function(n) {
                            var item = document.createElement('a');
                            item.href = n.action_url || '#';
                            item.className = 'dropdown-item ' + (n.read_at ? '' : 'fw-bold');
                            item.innerHTML = '<div class="d-flex align-items-center">' +
                                '<div class="ms-2 w-100">' +
                                '<h6 class="fw-normal mb-0 small">' + escapeHtml(n.title) + '</h6>' +
                                '<small class="text-muted">' + escapeHtml(n.body) + '</small>' +
                                '<br><small class="text-secondary">' + n.created_at_diff + '</small>' +
                                '</div></div>';
                            item.addEventListener('click', function(e) {
                                marcarLeida(n.id);
                            });
                            lista.appendChild(item);
                            var divider = document.createElement('hr');
                            divider.className = 'dropdown-divider my-1';
                            lista.appendChild(divider);
                        });
                        lista.removeChild(lista.lastChild);
                    }
                })
                .catch(function() {
                    var lista = document.getElementById('notif-lista');
                    if (lista) {
                        lista.innerHTML = '<div class="text-center py-3 text-muted small">Error al cargar notificaciones</div>';
                    }
                });
        }

        function marcarLeida(id) {
            fetch('/notificaciones/' + id + '/leida', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            }).catch(function() {});
        }

        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        document.addEventListener('DOMContentLoaded', function() {
            cargarNotificaciones();
            setInterval(cargarNotificaciones, 60000);
        });
    </script>
    @stack('scripts')


</body>

</html>