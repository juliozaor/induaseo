<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mi Aplicación')</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.cdnfonts.com/css/neo-sans-std" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}?v={{ time() }}">
</head>
<body>

    <!-- Contenedor principal de la aplicación -->
    <div class="container mt-5">
        @yield('content')
    </div>

     <!-- Incluir el modal de alerta -->
     @include('components.alert_modal')

    <!-- jQuery y Bootstrap JS CDN para que el modal funcione -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

      <!-- Script para activar el modal con alertas dinámicas -->
      <script>
        function showAlertModal(iconSrc, alertText) {
            $('#alertIcon').attr('src', iconSrc); // Configurar el icono
            $('#alertText').text(alertText); // Configurar el texto
            $('#alertModal').modal('show'); // Mostrar el modal
        }
    </script>
    
</body>
</html>
