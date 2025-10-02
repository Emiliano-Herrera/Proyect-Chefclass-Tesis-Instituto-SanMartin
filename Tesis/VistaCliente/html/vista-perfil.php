<?php
setlocale(LC_TIME, 'es_ES.UTF-8');
session_start();

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
} else {
    // Redirigir a la página de inicio de sesión si no hay una sesión activa
    header("Location: Login.php");
    exit();
}

include("conexion.php");

$sql = "SELECT U.*, G.*, R.nombre_rol, T.telefono AS user_telefono, E.email AS user_email, 
               L.localidad ,L.provincia
        FROM usuarios U 
        LEFT JOIN generos G ON U.genero = G.id_genero 
        LEFT JOIN roles R ON U.rol = R.id_rol 
        LEFT JOIN telefonos_usuarios T ON U.id_usuario = T.id_usuario 
        LEFT JOIN emails_usuarios E ON U.id_usuario = E.id_usuario 
        LEFT JOIN localidades L ON U.id_localidad = L.id_localidad 
        WHERE U.id_usuario = ? 
        ORDER BY U.id_usuario ASC";
$statement = $conexion->prepare($sql);
$statement->bind_param("i", $ID_Usuario);
$statement->execute();
$resultado = $statement->get_result();
$filas = [];
while ($fila = $resultado->fetch_assoc()) {
    $filas[] = $fila;
}



// Obtener emails, teléfonos y roles únicos 
$emails = array_unique(array_column($filas, 'user_email'));
$telefonos = array_unique(array_column($filas, 'user_telefono'));
$roles = array_unique(array_column($filas, 'nombre_rol'));

// Consulta para contar los seguidores 
$sql_seguidores = "SELECT COUNT(*) AS total_seguidores FROM seguimiento WHERE seguido_id = ?";
$statement_seguidores = $conexion->prepare($sql_seguidores);
$statement_seguidores->bind_param("i", $ID_Usuario);
$statement_seguidores->execute();
$resultado_seguidores = $statement_seguidores->get_result();
$seguidores = $resultado_seguidores->fetch_assoc()['total_seguidores'];

// Consulta para contar los usuarios que sigue 
$sql_seguidos = "SELECT COUNT(*) AS total_seguidos FROM seguimiento WHERE seguidor_id = ?";
$statement_seguidos = $conexion->prepare($sql_seguidos);
$statement_seguidos->bind_param("i", $ID_Usuario);
$statement_seguidos->execute();
$resultado_seguidos = $statement_seguidos->get_result();
$seguidos = $resultado_seguidos->fetch_assoc()['total_seguidos'];

// Mostrar mensajes de sesión con SweetAlert
if (isset($_SESSION['mensaje_exito'])) {
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '¡Éxito!',
            text: '" . $_SESSION['mensaje_exito'] . "',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });
    </script>";
    unset($_SESSION['mensaje_exito']); // Eliminar el mensaje después de mostrarlo
}

if (isset($_SESSION['mensaje_error'])) {
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '¡Error!',
            text: '" . $_SESSION['mensaje_error'] . "',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
    </script>";
    unset($_SESSION['mensaje_error']); // Eliminar el mensaje después de mostrarlo
}

$sqlRecetasUsuario = "
    SELECT R.*, RI.url_imagen, AVG(C.calificacion) as promedio_calificacion, U.nombre_usuario
    FROM recetas R 
    LEFT JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id 
    LEFT JOIN img_recetas RI ON IR.img_id = RI.id_img 
    LEFT JOIN calificaciones C ON R.id_receta = C.receta_id
    LEFT JOIN usuarios U ON R.usuario_id = U.id_usuario
    WHERE R.usuario_id = ? AND RI.url_imagen IS NOT NULL
    AND R.estado = 'habilitado'
    GROUP BY R.id_receta
    ORDER BY R.fecha_creacion DESC
";
$statementRecetas = $conexion->prepare($sqlRecetasUsuario);
$statementRecetas->bind_param("i", $ID_Usuario);
$statementRecetas->execute();
$resultRecetas = $statementRecetas->get_result();
$recetas = [];
while ($receta = $resultRecetas->fetch_assoc()) {
    if (!isset($recetas[$receta['id_receta']])) {
        $recetas[$receta['id_receta']] = $receta;
        $recetas[$receta['id_receta']]['imagenes'] = [];
    }
    $recetas[$receta['id_receta']]['imagenes'][] = $receta['url_imagen'];
}

// Función para limitar la descripción
function limitar_descripcion($descripcion, $limite = 15)
{
    $palabras = explode(' ', $descripcion);
    if (count($palabras) > $limite) {
        return implode(' ', array_slice($palabras, 0, $limite)) . '...';
    } else {
        return $descripcion;
    }
}

// Función para generar estrellas
function generar_estrellas($promedio)
{
    $estrellas_completas = floor($promedio);
    $mitad_estrella = ($promedio - $estrellas_completas >= 0.5) ? true : false;
    $estrellas_html = '';

    for ($i = 0; $i < 5; $i++) {
        if ($i < $estrellas_completas) {
            $estrellas_html .= '<span class="fa fa-star checked"></span>';
        } elseif ($mitad_estrella && $i == $estrellas_completas) {
            $estrellas_html .= '<span class="fa fa-star-half-alt checked"></span>';
            $mitad_estrella = false; // Para que solo una estrella pueda ser media
        } else {
            $estrellas_html .= '<span class="fa fa-star"></span>';
        }
    }
    return $estrellas_html;
}

?>

<!doctype html>
<html lang="en">
<!--  -->

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/boton-naranja.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ChefClass - Perfil</title>
    <link rel="icon" href="../img/chefclassFinal.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- animate CSS -->
    <link rel="stylesheet" href="../css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <!-- themify CSS -->
    <link rel="stylesheet" href="../css/themify-icons.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="../css/flaticon.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <!-- swiper CSS -->
    <link rel="stylesheet" href="../css/slick.css">
    <link rel="stylesheet" href="../css/gijgo.min.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/all.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="../css/style.css">


    <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/pages/page-profile.css">

    <!-- Core CSS -->
    <!-- <link rel="stylesheet" href="../../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" /> -->
    <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/rtl/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="../css/perfil.css">
    <!-- <link rel="stylesheet" href="../../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" /> -->
    <!-- <link rel="stylesheet" href="../../VistaAdmin/assets/vendor/css/rtl/theme-default.css"> -->
    <!-- <link rel="stylesheet" href="../../assets/css/demo.css" /> -->

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
</head>

<body>
    <!--::header part start::-->
    <header class="main_menu">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.php">
                            <img src="../img/chefclassFinal.png" alt="logo" width="140" height="auto">
                        </a>

                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <style>
                            .navbar-nav .nav-link {
                                font-size: 1.2rem;
                                color: black;
                                transition: color 0.3s ease, transform 0.3s ease;
                            }

                            .navbar-nav .nav-link:hover {
                                color: #ff6600;
                                transform: scale(1.1);
                            }

                            .navbar-nav .nav-link.active {
                                color: #ff6600;
                                font-weight: bold;
                            }
                        </style>

                        <div class="collapse navbar-collapse main-menu-item justify-content-end" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Inicio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-nosotros.php' ? 'active' : '' ?>" href="vista-nosotros.php">Nosotros</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-categoria.php' ? 'active' : '' ?>" href="vista-categoria.php">Categorías</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-subir-receta.php' ? 'active' : '' ?>" href="<?php echo isset($_SESSION['id_usuario']) ? 'vista-subir-receta.php' : '../../VistaAdmin/html/Login.php'; ?>">Subir recetas</a>
                                </li>
                                <?php if (isset($_SESSION['id_usuario'])): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vista-perfil.php' ? 'active' : '' ?>" href="vista-perfil.php">Perfil</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="menu_btn">
                            <?php if (!isset($_SESSION['id_usuario'])): ?>
                                <a href="../../VistaAdmin/html/Login.php" class="btn-naranja d-none d-sm-block">Iniciar sesión</a>
                            <?php else: ?>
                                <a href="cerrar_sesion.php" class="btn-naranja d-none d-sm-block">Cerrar sesión</a>
                            <?php endif; ?>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- Header part end-->

    <section class="seccion-perfil-usuario">
        <div class="perfil-usuario-header">
            <div class="perfil-usuario-portada" style="background-image: url('../img/breadcrumb.png');">
                <div class="perfil-usuario-avatar">
                    <img src="<?php echo '../../VistaAdmin/html/' . $filas[0]['img']; ?>" alt="user image">
                </div>
                <div class="">
                    <button class="boton-portada d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.2rem;">
                        <i class="bi bi-list" style="font-size: 1.2rem; line-height: 1;"></i>Más
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="vista-editar-perfil.php"><i class="bi bi-pencil-square"></i> Editar perfil</a></li>
                        <li><a class="dropdown-item" href="vista-actividad.php"><i class="bi bi-activity"></i> Tú actividad</a></li>
                        <li><a class="dropdown-item" href="vista-guardados.php"><i class="bi bi-bookmark"></i> Guardado</a></li>
                        <li><a class="dropdown-item" href="../../VistaAdmin/perfil2.php"><i class="bi bi-gear"></i> Admin</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="perfil-usuario-body">
            <div class="perfil-usuario-bio">
                <!-- Nombre de usuario y nombre completo -->
                <h3 class="titulo mb-0" style="font-size: 1.4rem;"><?php echo $filas[0]['nombre_usuario']; ?></h3>
                <p class="titulo" style="font-size: 1.1rem;"><?php echo $filas[0]['nombre'], ' ', $filas[0]['apellido'] ?></p>
                <!-- Datos de contacto -->
                <ul class="lista-datos" style="font-size: 1em;">
                    <li><i class="bi bi-telephone" style="font-size: 1.3em;"></i> Teléfono: 
                        <?php if (!empty($telefonos)): ?>
                            <?php foreach ($telefonos as $telefono) { echo " $telefono" . " - "; } ?> 
                        <?php endif; ?>
                    </li>
                    <li><i class="bi bi-envelope" style="font-size: 1.3em;"></i> Email: <?php echo $filas[0]['user_email'] ?></li>
                </ul>
                <ul class="lista-datos" style="font-size: 1em;">
                    <li><i class="bi bi-calendar-check" style="font-size: 1.3em;"></i> Registro: <?php echo strftime('%e de %b del %Y', strtotime($filas[0]['fecha_creacion'])); ?></li>
                </ul>
                <!-- Botón subir receta -->
                <div class="titulo mb-2">
                    <a class="btn btn-outline-success" href="vista-subir-receta.php"><i class="bi bi-plus-square m-1"> </i> Subir receta</a>
                </div>
                <!-- Seguidores, seguidos y publicaciones -->
                <div class="seguidores-seguidos titulo mt-3">
                    <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap">
                        <?php
                            $totalPublicaciones = count($recetas);
                        ?>
                        <div class="text-center px-3 py-2 bg-white seguidores-card" style="border:none; box-shadow:none;">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalSeguidores" class="text-decoration-none text-dark">
                                <div class="mb-1"><i class="bi bi-people-fill fs-3 text-primary"></i></div>
                                <div class="fw-bold fs-5"><?php echo number_format($seguidores); ?></div>
                                <div class="small text-muted" style="font-size:0.7em;">Seguidores</div>
                            </a>
                        </div>
                        <div class="text-center px-3 py-2 bg-white seguidores-card" style="border:none; box-shadow:none;">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalSeguidos" class="text-decoration-none text-dark">
                                <div class="mb-1"><i class="bi bi-person-check-fill fs-3 text-success"></i></div>
                                <div class="fw-bold fs-5"><?php echo number_format($seguidos); ?></div>
                                <div class="small text-muted" style="font-size:0.7em;">Seguidos</div>
                            </a>
                        </div>
                        <div class="text-center px-3 py-2 bg-white seguidores-card" style="border:none; box-shadow:none;">
                            <div class="mb-1"><i class="bi bi-journal-text fs-3 text-warning"></i></div>
                            <div class="fw-bold fs-5"><?php echo $totalPublicaciones; ?></div>
                            <div class="small text-muted" style="font-size:0.7em;">Publicaciones</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .seguidores-card {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
    </style>

    <!-- //? SEGUIDORES Y SEGUIDOS MODAL ==================================================================-->

    <!-- //TODO Seguidores -------------------------------------------------------------------->
    <div class="modal fade" id="modalSeguidores" tabindex="-1" aria-labelledby="modalSeguidoresLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSeguidoresLabel">Seguidores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Barra de búsqueda -->
                    <form method="POST" id="formBuscarSeguidor">
                        <div class="mb-3 d-flex">
                            <input type="text" name="buscarSeguidor" id="buscarSeguidor" class="form-control" placeholder="Buscar seguidor...">
                        </div>
                    </form>
                    <!-- Lista de seguidores -->
                    <div style="max-height: 400px; overflow-y: auto;">
                        <ul class="list-group" id="listaSeguidores">
                            <?php
                            $filtroSeguidor = isset($_POST['buscarSeguidor']) ? '%' . $_POST['buscarSeguidor'] . '%' : '%';
                            $sql_lista_seguidores = "
                SELECT U.id_usuario, U.nombre_usuario, U.nombre, U.apellido, U.img 
                FROM seguimiento S
                INNER JOIN usuarios U ON S.seguidor_id = U.id_usuario
                WHERE S.seguido_id = ? AND 
                      (U.nombre_usuario LIKE ? OR U.nombre LIKE ? OR U.apellido LIKE ?)";
                            $stmt_lista_seguidores = $conexion->prepare($sql_lista_seguidores);
                            $stmt_lista_seguidores->bind_param("isss", $ID_Usuario, $filtroSeguidor, $filtroSeguidor, $filtroSeguidor);
                            $stmt_lista_seguidores->execute();
                            $resultado_lista_seguidores = $stmt_lista_seguidores->get_result();

                            while ($seguidor = $resultado_lista_seguidores->fetch_assoc()): ?>
                                <li class="list-group-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo '../../VistaAdmin/html/' . $seguidor['img']; ?>" alt="Imagen de usuario" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <strong><?php echo $seguidor['nombre_usuario']; ?></strong><br>
                                            <small><?php echo $seguidor['nombre'] . ' ' . $seguidor['apellido']; ?></small>
                                        </div>
                                    </div>
                                    <button class="btn btn-secondary btn-sm eliminar-seguidor" data-id="<?php echo $seguidor['id_usuario']; ?>">Eliminar</button>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .seguidores-seguidos .d-flex {
        gap: 1.2rem !important;
        flex-wrap: wrap;
    }
    .seguidores-seguidos .text-center {
        transition: box-shadow 0.2s, transform 0.2s;
        cursor: pointer;
    }
    .seguidores-seguidos .text-center:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        transform: translateY(-4px) scale(1.04);
        background: #f8f9fa;
    }
    .seguidores-seguidos .fw-bold {
        font-size: 1.2rem;
    }
    .seguidores-seguidos .small {
        font-size: 0.7em !important;
        color: #888 !important;
        letter-spacing: 0.01em;
    }
    .seguidores-seguidos .small.text-muted {
        font-size: 0.7em !important;
        color: #888 !important;
        letter-spacing: 0.01em;
    }
    /* Responsivo para los cuadros */
    .seguidores-card {
        width: 120px;
        min-width: 100px !important;
        max-width: 160px !important;
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
        font-size: 0.95em;
    }
    @media (max-width: 900px) {
        .seguidores-card {
            width: 100px;
            min-width: 90px !important;
            max-width: 120px !important;
            height: 90px;
            font-size: 0.9em;
        }
    }
    @media (max-width: 600px) {
        .seguidores-card {
            width: 90vw;
            min-width: 0 !important;
            max-width: 100vw !important;
            height: 70px;
            font-size: 0.85em;
            margin-bottom: 10px;
        }
        .seguidores-seguidos .d-flex {
            gap: 0.7rem !important;
        }
    }
    </style>

    


    <!-- //* Seguidos ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->

    <style>
        .modal-body {
            overflow-y: auto;
        }
    </style>

    <div class="modal fade" id="modalSeguidos" tabindex="-1" aria-labelledby="modalSeguidosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSeguidosLabel">Seguidos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Barra de búsqueda -->
                    <form method="POST" id="formBuscarSeguido">
                        <div class="mb-3 d-flex">
                            <input type="text" name="buscarSeguido" id="buscarSeguido" class="form-control" placeholder="Buscar seguido...">
                            <!-- <button type="submit" class="btn btn-primary ms-2">Buscar</button> -->
                        </div>
                    </form>
                    <!-- Lista de seguidos -->
                    <div style="max-height: 400px; overflow-y: auto;">
                        <ul class="list-group" id="listaSeguidos">
                            <?php
                            $filtroSeguido = isset($_POST['buscarSeguido']) ? '%' . $_POST['buscarSeguido'] . '%' : '%';
                            $sql_lista_seguidos = "
                SELECT U.id_usuario, U.nombre_usuario, U.nombre, U.apellido, U.img 
                FROM seguimiento S
                INNER JOIN usuarios U ON S.seguido_id = U.id_usuario
                WHERE S.seguidor_id = ? AND 
                      (U.nombre_usuario LIKE ? OR U.nombre LIKE ? OR U.apellido LIKE ?)";
                            $stmt_lista_seguidos = $conexion->prepare($sql_lista_seguidos);
                            $stmt_lista_seguidos->bind_param("isss", $ID_Usuario, $filtroSeguido, $filtroSeguido, $filtroSeguido);
                            $stmt_lista_seguidos->execute();
                            $resultado_lista_seguidos = $stmt_lista_seguidos->get_result();

                            while ($seguido = $resultado_lista_seguidos->fetch_assoc()): ?>
                                <li class="list-group-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo '../../VistaAdmin/html/' . $seguido['img']; ?>" alt="Imagen de usuario" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <strong><?php echo $seguido['nombre_usuario']; ?></strong><br>
                                            <small><?php echo $seguido['nombre'] . ' ' . $seguido['apellido']; ?></small>
                                        </div>
                                    </div>
                                    <button class="btn btn-secondary btn-sm dejar-seguir" data-id="<?php echo $seguido['id_usuario']; ?>">Dejar de seguir</button>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para manejar la búsqueda en el modal de seguidores
            document.getElementById('buscarSeguidor').addEventListener('input', function() {
                const input = this.value;

                fetch('buscar-seguidores.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            filtro: input,
                            tipo: 'seguidores' // Indica que es una búsqueda de seguidores
                        })
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('listaSeguidores').innerHTML = html; // Actualizar la lista
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Función para manejar la búsqueda en el modal de seguidos
            document.getElementById('buscarSeguido').addEventListener('input', function() {
                const input = this.value;

                fetch('buscar-seguidores.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            filtro: input,
                            tipo: 'seguidos' // Indica que es una búsqueda de seguidos
                        })
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('listaSeguidos').innerHTML = html; // Actualizar la lista
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>

    <style>
        .swal2-container {
            z-index: 2000 !important;
            /* Asegúrate de que sea mayor que el z-index del modal */
        }
    </style>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmación para eliminar seguidor
            const listaSeguidores = document.getElementById('listaSeguidores');
            listaSeguidores.addEventListener('click', function(e) {
                if (e.target.classList.contains('eliminar-seguidor')) {
                    const userId = e.target.getAttribute('data-id');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¿Deseas eliminar este seguidor?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`accion-seguimiento.php?accion=eliminar&id=${userId}`, {
                                    method: 'GET'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire('Eliminado', 'El seguidor ha sido eliminado.', 'success').then(() => {
                                            location.reload(); // Recargar la página o lista
                                        });
                                    }
                                });
                        }
                    });
                }
            });

            // Confirmación para dejar de seguir
            const listaSeguidos = document.getElementById('listaSeguidos');
            listaSeguidos.addEventListener('click', function(e) {
                if (e.target.classList.contains('dejar-seguir')) {
                    const userId = e.target.getAttribute('data-id');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¿Deseas dejar de seguir a este usuario?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, dejar de seguir',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`accion-seguimiento.php?accion=dejar-seguir&id=${userId}`, {
                                    method: 'GET'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire('Hecho', 'Has dejado de seguir a este usuario.', 'success').then(() => {
                                            location.reload(); // Recargar la página o lista
                                        });
                                    }
                                });
                        }
                    });
                }
            });
        });
    </script>


    <!-- //? SEGUIDORES Y SEGUIDOS MODAL ==================================================================-->



    <!-- //! post subidos ===================================================================================== -->
    <section class="seccion-publicaciones-usuario mt-5">
        <div class="container mt-5">
            <div class="row" id="recetas-container">
                <h1>Recetas subidas</h1>
                <?php
                $recetasPorPagina = 6; // Número de recetas por página (3 columnas x 2 filas)
                $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $inicio = ($paginaActual - 1) * $recetasPorPagina;
                $totalRecetas = count($recetas);
                $totalPaginas = ceil($totalRecetas / $recetasPorPagina);
                $recetasPaginadas = array_slice($recetas, $inicio, $recetasPorPagina);

                if (!empty($recetasPaginadas)): ?>
                    <?php foreach ($recetasPaginadas as $receta): ?>
                        <div class="col-md-6 mb-3 receta-item">
                            <div class="card mb-3 h-100 shadow-lg border-0 rou">
                                <div class="row g-0">
                                    <div class="col-md-5 position-relative">
                                        <img src="<?= $receta['imagenes'][0] ?>" class="img-fluid rounded-start custom-img" alt="...">
                                        <button class="btn btn-outline-light btn-eliminar position-absolute" style="top: 10px; left: 30px; padding: 5px 10px;" data-id="<?php echo $receta['id_receta']; ?>" data-titulo="<?php echo $receta['titulo']; ?>"><i class="bi bi-trash"></i></button>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <a href="vista-detalle-receta.php?id=<?= $receta['id_receta']; ?>" class="card-link">
                                                <h5 class="card-title"><?= $receta['titulo'] ?></h5>
                                                <p class="card-text"><?= generar_estrellas($receta['promedio_calificacion']) ?></p>
                                                <div class="d-flex justify-content-between">
                                                    <p class="card-text"><small class="text-muted"><i class="bi bi-person-fill"></i> <?= $receta['nombre_usuario'] ?></small></p>
                                                    <p class="card-text"><small class="text-muted"><i class="bi bi-calendar-fill"></i> <?= strftime(' %e de %b, %Y', strtotime($receta['fecha_creacion'])) ?></small></p>
                                                </div>
                                                <p class="card-text mt-3"><?= limitar_descripcion($receta['descripcion']) ?></p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay recetas disponibles en esta categoría.</p>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <nav aria-label="Page navigation example mt-5">
                <ul class="pagination justify-content-center" id="pagination-container">
                    <?php if ($paginaActual > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($paginaActual < $totalPaginas): ?>
                        <li class="page-item">
                            <a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?>" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </section>

    <style>
        .card-link {
            text-decoration: none;
            color: inherit;
        }

        /* .card-link:hover .card {
            transform: translateY(-10px);
        } */

        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card {
            transition: transform 0.3s ease;
            height: 100%;
        }

        /* .card:hover {
            transform: translateY(-10px);
        } */

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-footer {
            text-align: center;
        }

        .fa-star,
        .fa-star-half-alt {
            color: #ffeb3b;
        }

        .fa-star.checked,
        .fa-star-half-alt.checked {
            color: #ffeb3b;
        }

        .fa-star {
            color: #ccc;
        }

        .rou {
            border-radius: 15px;
        }

        .custom-img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .card .row.g-0 {
            height: 100%;
        }

        .card .col-md-5 {
            display: flex;
            align-items: stretch;
        }

        .card .col-md-5 img {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        .pagination .page-link {
            color: #ff6426;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff6426;
            border-color: #ff6426;
        }
    </style>


    <footer class="footer-area mt-5">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_1">
                        <h4>Sobre nosotros</h4>
                        <p>Bienvenidos a ChefClass, tu comunidad culinaria en línea.
                            Somos la plataforma perfecta para los amantes de la cocina que desean compartir,
                            descubrir y disfrutar de recetas únicas y deliciosas.
                            Inspirados por la pasión de cocinar y la conexión que se genera al compartir nuestras creaciones,
                            ChefClass se ha convertido en el lugar ideal para encontrar inspiración diaria..</p>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-7 col-md-4">
                    <div class="single-footer-widget footer_2">
                        <h4>Enlaces</h4>
                        <div class="contact_info">
                            <ul>
                                <li><a href="#">Inicio</a></li>
                                <li><a href="#">Nosotros</a></li>
                                <li><a href="#">Categorías</a></li>
                                <li><a href="#">Subir Recetas</a></li>
                                <li><a href="#">Perfil</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_2">
                        <h4>Contáctenos</h4>
                        <div class="contact_info">
                            <p><span> Ubicación :</span>San Martín 311, K4751 San Fernando del Valle de Catamarca, Catamarca </p>
                            <p><span> Celular :</span> +2 36 265 (8060)</p>
                            <p><span> Email : </span>tesisdesarrollodesoftware@gmail.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-md-4">
                    <div class="single-footer-widget footer_3">
                        <h4>Iniciar sesión en ChefClass</h4>
                        <form action="#">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <!-- <a href="#" class="single_page_btn d-none d-sm-block">Iniciar Sesión</a> -->
                                        <?php if (!isset($_SESSION['id_usuario'])): ?>
                                            <a href="../../VistaAdmin/html/Login.php" class="btn-naranja d-none d-sm-block">Iniciar sesión</a>
                                        <?php else: ?>
                                            <a href="cerrar_sesion.php" class="btn-naranja d-none d-sm-block">Cerrar sesión</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="copyright_part_text">
                <div class="row">
                    <div class="col-lg-8">
                        <p class="footer-text m-0">ChefClass | Proyecto realizado por <a href="#" target="_blank">Lucas Salvatierra, Emiliano Olivera.</a></p>
                    </div>
                    <div class="col-lg-4">
                        <div class="copyright_social_icon text-right">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                            <a href="#"><i class="ti-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <!-- jquery plugins here-->
    <!-- jquery -->
    <script src="../js/jquery-1.12.1.min.js"></script>
    <!-- popper js -->
    <script src="../js/popper.min.js"></script>
    <!-- bootstrap js -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- easing js -->
    <script src="../js/jquery.magnific-popup.js"></script>
    <!-- swiper js -->
    <script src="../js/swiper.min.js"></script>
    <!-- swiper js -->
    <script src="../js/masonry.pkgd.js"></script>
    <!-- particles js -->
    <script src="../js/owl.carousel.min.js"></script>
    <!-- swiper js -->
    <script src="../js/slick.min.js"></script>
    <script src="../js/gijgo.min.js"></script>
    <script src="../js/jquery.nice-select.min.js"></script>
    <!-- custom js -->
    <script src="../js/custom.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar el clic en el botón de eliminar con SweetAlert
            document.querySelectorAll('.btn-eliminar').forEach(button => {
                button.addEventListener('click', function() {
                    const recetaId = this.getAttribute('data-id');
                    const recetaTitulo = this.getAttribute('data-titulo');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: `¿Quieres eliminar la receta "${recetaTitulo}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `eliminar-receta.php?id=${recetaId}`;
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>