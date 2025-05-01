{{-- filepath: /c:/laragon/www/induaseo/resources/views/emails/solicitud.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de inventario</title>
</head>
<body>
    <h1>Solicitud de inventario</h1>
    <p>Supervisor: {{ $usuario }}</p>
    <p>Cliente: {{ $cliente }}</p>
    <p>Sede: {{ $sede }}</p>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['id'] }}</td>
                    <td>{{ $item['nombre'] }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>{{ $item['tipo'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
