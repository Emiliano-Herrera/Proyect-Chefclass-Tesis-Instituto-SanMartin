<?php
session_start();
include("conexion.php");

// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si los datos necesarios están en la sesión
if (!isset($_SESSION['registro_email']) || !isset($_SESSION['registro_telefonos']) || !isset($_SESSION['registro_tipos']) || !isset($_SESSION['registro_contrasena']) || !isset($_SESSION['registro_usuario'])) {
    $_SESSION['registro_error'] = 'Faltan datos para completar el registro.';
    header("Location: login_cargar_usuario.php");
    exit();
}

$email = $_SESSION['registro_email'];
$telefonos = $_SESSION['registro_telefonos'];
$tipos = $_SESSION['registro_tipos'];
$contrasena = $_SESSION['registro_contrasena'];
$usuario = $_SESSION['registro_usuario'];
$nombre = $_SESSION['registro_nombre'];
$apellido = $_SESSION['registro_apellido'];
$genero = $_SESSION['registro_genero'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- <link rel="stylesheet" href="../css/cargar_ubicacion.css"> -->
    <link rel="stylesheet" href="../css/Login.css">
    <title>Cargar Ubicación</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

</head>

<body>

    <div class="container">
        <style>
            #map {
                height: 100%;
                width: 100%;
                min-height: 300px;
                border-radius: 5px;
            }

            .container {
                width: 100%;
                max-width: 1200px;
                margin: 0 auto;
                padding: 16px;
                box-sizing: border-box;
                display: flex;
                flex-wrap: wrap;
                gap: 32px;
                align-items: stretch;
            }

            .map-section {
                flex: 2 1 0;
                min-width: 400px;
                min-height: 400px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: stretch;
                height: 100%;
            }

            .form-fields {
                flex: 1 1 0;
                min-width: 300px;
                min-height: 400px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: stretch;
                height: 100%;
            }

            .form-fields form {
                display: flex;
                flex-direction: column;
                gap: 12px;
                height: 100%;
                justify-content: center;
            }

            .form-fields input,
            .form-fields button {
                width: 100%;
                padding: 12px;
                box-sizing: border-box;
                border-radius: 4px;
                border: 1px solid #ccc;
            }

            .form-fields button {
                cursor: pointer;
                color: white;
                border: none;
                transition: background-color 0.3s;
            }

            h1 {
                font-size: 1.5rem;
                margin-bottom: 16px;
                text-align: center;
            }

            /* Tablet y pantallas medianas */
            @media (max-width: 992px) {
                .container {
                    gap: 24px;
                    padding: 12px;
                }

                .map-section {
                    min-width: 350px;
                    min-height: 350px;
                }

                .form-fields {
                    min-width: 280px;
                }
            }

            /* Tablets pequeñas y móviles - Cambio clave aquí */
            @media (max-width: 768px) {
                .container {
                    flex-direction: column;
                    gap: 24px;
                    height: auto;
                }

                .map-section,
                .form-fields {
                    flex: 0 0 auto;
                    /* Cambiado para asegurar visibilidad */
                    width: 100%;
                    min-width: 100%;
                    min-height: auto;
                    /* Altura automática */
                }

                #map {
                    height: 300px;
                    min-height: 250px;
                }

                .form-fields {
                    order: 2;
                    /* Asegura que el formulario vaya después del mapa */
                    height: auto;
                    padding: 16px 0;
                }

                .form-fields form {
                    height: auto;
                    justify-content: flex-start;
                    /* Alineación superior */
                }
            }

            /* Celulares */
            @media (max-width: 576px) {
                .container {
                    padding: 8px;
                    gap: 16px;
                }

                #map {
                    height: 280px;
                    min-height: 280px;
                }

                h1 {
                    font-size: 1.3rem;
                    margin-bottom: 12px;
                }

                .form-fields input,
                .form-fields button {
                    padding: 10px;
                    font-size: 0.9rem;
                }
            }

            /* Celulares muy pequeños */
            @media (max-width: 400px) {
                #map {
                    height: 250px;
                    min-height: 250px;
                }

                h1 {
                    font-size: 1.2rem;
                }
            }
        </style>
        <div class="map-section">
            <div id="map"></div>
        </div>
        <div class="form-fields">
            <form action="guardar-ubicacion-usuario.php" method="POST">
                <h1>Seleccionar Ubicación</h1>
                <input type="hidden" id="latitud" name="latitud">
                <input type="hidden" id="longitud" name="longitud">
                <input type="text" id="provincia" name="provincia" placeholder="Provincia" readonly>
                <input type="text" id="departamento" name="departamento" placeholder="Departamento" readonly>
                <input type="text" id="localidad" name="localidad" placeholder="Localidad" readonly>
                <input type="text" id="barrio" name="barrio" placeholder="Barrio" readonly>
                <input type="text" id="pais" name="pais" placeholder="País" readonly>
                <!-- Enviar los datos del registro a través de inputs ocultos -->
                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
                <input type="hidden" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>">
                <input type="hidden" name="genero" value="<?php echo htmlspecialchars($genero); ?>">
                <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="telefonos" value="<?php echo htmlspecialchars(json_encode($telefonos)); ?>">
                <input type="hidden" name="tipos" value="<?php echo htmlspecialchars(json_encode($tipos)); ?>">
                <input type="hidden" name="contrasena" value="<?php echo htmlspecialchars($contrasena); ?>">
                <button type="button" id="buscar-ubicacion" class="btn btn-primary">Buscar mí Ubicación</button>
                <button type="submit" name="guardar" id="guardar">Guardar Ubicación</button>
            </form>
            <form action="Login.php" method="POST" style="margin-top: 10px;">
                <button type="submit" name="volver" id="volver">Volver</button>
            </form>
        </div>
    </div>
    <script>
        var map = L.map('map').setView([-34.603722, -58.381592], 13); // Coordenadas iniciales de Buenos Aires, Argentina
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker;


        // Variable para evitar múltiples solicitudes simultáneas
        let isFetching = false;

        // Función para obtener la ubicación actual del usuario
        function obtenerUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    mostrarPosicion,
                    mostrarError, {
                        enableHighAccuracy: true,
                        timeout: 20000, // Aumenta el tiempo de espera
                        maximumAge: 0
                    }
                );
            } else {
                alert("La geolocalización no es soportada por este navegador.");
            }
        }

        function mostrarPosicion(position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            console.log("Latitud obtenida:", lat, "Longitud obtenida:", lon); // Verifica las coordenadas
            var latlng = L.latLng(lat, lon);

            if (marker) {
                marker.setLatLng(latlng);
            } else {
                marker = L.marker(latlng).addTo(map);
            }

            map.setView(latlng, 13);

            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lon;

            // Obtener detalles de la ubicación usando Nominatim
            if (!isFetching) {
                isFetching = true; // Evitar múltiples solicitudes
                Swal.fire({
                    title: 'Cargando ubicación...',
                    text: 'Por favor espera mientras obtenemos los detalles de tu ubicación.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Datos de Nominatim:", data); // Verifica los datos obtenidos
                        document.getElementById('provincia').value = data.address.state || '';
                        document.getElementById('departamento').value = data.address.county || '';
                        document.getElementById('localidad').value = data.address.city || data.address.town || data.address.village || '';
                        document.getElementById('barrio').value = data.address.suburb || data.address.neighbourhood || '';
                        document.getElementById('pais').value = data.address.country || '';
                    })
                    .catch(error => {
                        console.log('Error en Nominatim:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo obtener la información de la ubicación. Inténtalo nuevamente.',
                            confirmButtonText: 'Aceptar'
                        });
                    })
                    .finally(() => {
                        isFetching = false; // Permitir nuevas solicitudes
                        Swal.close(); // Cerrar el indicador de carga
                    });
            }
        }

        function mostrarError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("El usuario denegó la solicitud de geolocalización.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("La información de ubicación no está disponible.");
                    break;
                case error.TIMEOUT:
                    alert("La solicitud de geolocalización ha caducado.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("Se ha producido un error desconocido.");
                    break;
            }
        }

        // Llamar a la función para obtener la ubicación actual del usuario después de 5 segundos
        setTimeout(() => {
            Swal.fire({
                title: 'Usar ubicación',
                text: 'Vamos a usar tu ubicación para completar el registro.',
                icon: 'info',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    obtenerUbicacion();
                }
            });
        }, 3000);

        map.on('click', function(e) {
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
            document.getElementById('latitud').value = e.latlng.lat;
            document.getElementById('longitud').value = e.latlng.lng;

            // Obtener detalles de la ubicación usando Nominatim
            if (!isFetching) {
                isFetching = true; // Evitar múltiples solicitudes
                Swal.fire({
                    title: 'Cargando ubicación...',
                    text: 'Por favor espera mientras obtenemos los detalles de tu ubicación.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('provincia').value = data.address.state || '';
                        document.getElementById('departamento').value = data.address.county || '';
                        document.getElementById('localidad').value = data.address.city || data.address.town || data.address.village || '';
                        document.getElementById('barrio').value = data.address.suburb || data.address.neighbourhood || '';
                        document.getElementById('pais').value = data.address.country || '';
                    })
                    .catch(error => {
                        console.log('Error en Nominatim:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo obtener la información de la ubicación. Inténtalo nuevamente.',
                            confirmButtonText: 'Aceptar'
                        });
                    })
                    .finally(() => {
                        isFetching = false; // Permitir nuevas solicitudes
                        Swal.close(); // Cerrar el indicador de carga
                    });
            }
        });

        // Evento para buscar la ubicación nuevamente
        document.getElementById('buscar-ubicacion').addEventListener('click', function() {
            obtenerUbicacion();
        });

        // Redibujar el mapa cuando cambie el tamaño de la ventana
        window.addEventListener('resize', function() {
            map.invalidateSize(); // Fuerza el redibujado del mapa
        });
    </script>
</body>

</html>