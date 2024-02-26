<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading</title>
</head>
<body>
<div id="contenedor-de-respuesta">
        <p>Loading...</p>
    </div>

    <script>
        $(document).ready(function() {
            // Token de autorización
            var token = 'Bearer ' + '{{$Token}}'; // Reemplaza 'tu_token_aqui' con tu token real

            // Realizar la petición una vez que el documento esté listo
            $.ajax({
                url: '/ruta-de-peticion', // Reemplaza esto con la URL de tu ruta de petición
                method: 'GET', // El método de la solicitud (GET, POST, etc.)
                headers: {
                    'Authorization': 'Bearer ' + token // Agrega el token como cabecera de autorización
                },
                success: function(response) {
                    // Coloca la respuesta en el contenido HTML de algún elemento
                    $('#contenedor-de-respuesta').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(error); // Manejar errores de la petición
                }
            });
        });
    </script>
</body>
</html>

