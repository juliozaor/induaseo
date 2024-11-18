<!-- filepath: /c:/laragon/www/induaseo/resources/views/emails/usuario_creado.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Cuenta creada</title>
</head>
<body>
    <h1>Hola, {{ $usuario->nombres }} {{ $usuario->apellidos }}</h1>
    <p>Tu cuenta ha sido creada exitosamente. Aquí están tus detalles de inicio de sesión:</p>
    <p><strong>Correo electrónico:</strong> {{ $usuario->email }}</p>
    <p><strong>Contraseña temporal:</strong> {{ $password }}</p>
    <p>Por favor, cambia tu contraseña después de iniciar sesión.</p>
    <p>Gracias,</p>
    <p>El equipo de Induaseo</p>
</body>
</html>