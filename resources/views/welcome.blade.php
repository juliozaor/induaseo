<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión</title>

    <!-- Enlace al archivo CSS en la carpeta assets -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}?v={{ time() }}">
</head>
<body>
 <!-- Contenedor principal -->
 <div class="container">
    <!-- Logo centrado en la página -->
    <div class="logo">
        <img src="{{ asset('assets/images/logo_induaseo.webp') }}" alt="Logo" class="logo-img">
    </div>
</div>

<!-- Rectángulo con el texto en la parte inferior -->
<div class="bottom-bar">
    <p class="bottom-text">Sistema de gestión V 1.0</p>
</div>

<!-- Enlaces debajo del rectángulo -->
<div class="roles">
    <a href="{{ route('login') }}" class="role-link">Rol Administrador <span>>></span></a>
    <a href="{{ route('login') }}" class="role-link">Rol Supervisor <span>>></span></a>
    <a href="{{ route('login') }}" class="role-link">Rol Cliente <span>>></span></a>
</div>

</body>
</html>
