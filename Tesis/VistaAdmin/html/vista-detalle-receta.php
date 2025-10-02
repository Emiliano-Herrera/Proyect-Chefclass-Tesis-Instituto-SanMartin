<?php
session_start();
include("conexion.php");


if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];

    // Traer el id_rol y nombre_rol del usuario (usa la columna correcta: "rol")
    $sql = "SELECT r.id_rol, r.nombre_rol FROM usuarios u JOIN roles r ON u.rol = r.id_rol WHERE u.id_usuario = $ID_Usuario";
    $result = $conexion->query($sql);
    $row = $result->fetch_assoc();
    $Rol = $row['nombre_rol'];
    $RolId = $row['id_rol'];

    // Traer las secciones permitidas para ese rol
    $sql_permisos = "SELECT id_seccion FROM roles_permisos_secciones WHERE id_rol = $RolId";
    $result_permisos = $conexion->query($sql_permisos);
    $secciones_permitidas = [];
    while ($row_permiso = $result_permisos->fetch_assoc()) {
        $secciones_permitidas[] = $row_permiso['id_seccion'];
    }

    $id_seccion_usuarios = 1;

    // Traer los permisos para la sección "Usuarios" del rol actual
    $sql_permiso_usuario = "SELECT permisos FROM roles_permisos_secciones WHERE id_rol = $RolId AND id_seccion = $id_seccion_usuarios";
    $result_permiso_usuario = $conexion->query($sql_permiso_usuario);
    $permisos_usuario = [];
    if ($row_permiso_usuario = $result_permiso_usuario->fetch_assoc()) {
        // El campo permisos es un SET, lo convertimos a array
        $permisos_usuario = explode(',', str_replace("'", "", $row_permiso_usuario['permisos']));
    }
} else {
    header("Location: Login.php");
    exit();
}



$sql = "SELECT U.*, G.*, R.nombre_rol, T.telefono AS user_telefono, E.email AS user_email FROM usuarios U LEFT JOIN generos G ON U.genero = G.id_genero LEFT JOIN roles R ON U.rol = R.id_rol LEFT JOIN telefonos_usuarios T ON U.id_usuario = T.id_usuario LEFT JOIN emails_usuarios E ON U.id_usuario = E.id_usuario WHERE U.id_usuario = ? ORDER BY U.id_usuario ASC";
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

?>




<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Detalle Receta</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../VistaCliente/img/chefclassFinal.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

</head>

<body>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- //! Menu DASHBOARD=============================================================================================================== -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="admin.php" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="../../VistaCliente/img/chefclassFinal.png" alt="Logo" width="150">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2"></span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>
                <!-- TODO MENÚ LATERAL=========================================================================================================== -->
                <!-- TODO MENÚ LATERAL=========================================================================================================== -->
                <!-- TODO MENÚ LATERAL=========================================================================================================== -->
                <ul class="menu-inner py-1">
                    <!-- INICIO -->
                    <li class="menu-item">
                        <a href="admin.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Incio</div>
                        </a>
                    </li>

                    <!-- //!MI PERFIL================================================================================================== -->

                    <li class="menu-item">
                        <a href="perfil2.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div class="text-truncate" data-i18n="Users">Mi perfil</div>
                        </a>
                    </li>

                    <!-- //!GESTION DE REGISTROS -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Administración</span>
                    </li>

                    <?php
                    // Mapeo de id_seccion a archivo, icono y nombre
                    $secciones_menu = [
                        1 => ['archivo' => 'usuarios.php',      'icono' => 'bxs-user-detail',  'nombre' => 'Usuarios'],
                        2 => ['archivo' => 'roles.php',         'icono' => 'bxs-user-badge',   'nombre' => 'Roles'],
                        3 => ['archivo' => 'vista-recetas.php', 'icono' => 'bxs-food-menu',    'nombre' => 'Recetas'],
                        4 => ['archivo' => 'generos.php',       'icono' => 'bx-male-female',   'nombre' => 'Generos'],
                        5 => ['archivo' => 'categorias.php',    'icono' => 'bxs-category',     'nombre' => 'Categorias'],
                        6 => ['archivo' => 'auditoria.php',     'icono' => 'bxs-time',         'nombre' => 'Auditorias'],
                        7 => ['archivo' => 'localidades.php',   'icono' => 'bxs-map',          'nombre' => 'Localidades'],
                    ];

                    // Detectar el archivo actual
                    $archivo_actual = basename($_SERVER['PHP_SELF']);

                    // Archivos que deben marcar "Usuarios" como activo
                    $recetas_activos = [
                        'vista-editar-receta.php',
                        'vista-detalle-receta.php'
                    ];

                    // Mostrar solo las secciones permitidas para el rol
                    foreach ($secciones_menu as $id => $info) {
                        if (in_array($id, $secciones_permitidas)) {
                            // Para la sección Usuarios, marcar activo si el archivo actual está en $usuarios_activos
                            if ($id == 3) {
                                $active = in_array($archivo_actual, $recetas_activos) ? 'active' : '';
                            } else {
                                $active = ($archivo_actual == $info['archivo']) ? 'active' : '';
                            }
                    ?>
                            <li class="menu-item <?= $active ?>">
                                <a href="<?= $info['archivo'] ?>" class="menu-link">
                                    <i class='menu-icon bx <?= $info['icono'] ?>'></i>
                                    <div><?= $info['nombre'] ?></div>
                                </a>
                            </li>
                    <?php
                        }
                    }
                    ?>

                </ul>
            </aside>
            <!-- TODO MENÚ LATERAL=========================================================================================================== -->
            <!-- TODO MENÚ LATERAL=========================================================================================================== -->
            <!-- TODO MENÚ LATERAL=========================================================================================================== -->
            <!-- //! Menu=============================================================================================================== -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">

                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- //?User ========================================================================================================-->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                        </svg>

                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block"><?php echo $Nombre ?></span>
                                                    <small class="text-muted"><?php echo $Apellido ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="./perfil.php">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Mi perfil</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="./cerrar-sesion.php">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Cerrar sesión</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- //?User ========================================================================================================-->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <?php
                include('conexion.php');

                // Obtener el id_receta de la URL
                $id_receta = isset($_GET['id_receta']) ? intval($_GET['id_receta']) : 0;

                // Inicializar variables
                $titulo = $descripcion = $tiempo_preparacion = $dificultad = $categorias = $usuario = '';
                $ingredientes = $instrucciones = [];
                $imagenes = []; // Arreglo para almacenar imágenes 
                $videos = [];

                $estado_receta = '';
                if ($id_receta > 0) {
                    // Modifica la consulta para traer el estado de la receta
                    $sql = "SELECT r.titulo, r.descripcion, r.tiempo_preparacion, r.dificultad, 
                            GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias, u.nombre AS usuario, r.estado
                            FROM recetas r
                            LEFT JOIN usuarios u ON r.usuario_id = u.id_usuario
                            LEFT JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                            LEFT JOIN categoria c ON rc.categoria_id = c.id_categoria
                            WHERE r.id_receta = ?
                            GROUP BY r.id_receta";

                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("i", $id_receta);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $receta = $result->fetch_assoc();
                        $titulo = $receta['titulo'];
                        $descripcion = $receta['descripcion'];
                        $tiempo_preparacion = $receta['tiempo_preparacion'];
                        $dificultad = $receta['dificultad'];
                        $categorias = $receta['categorias'];
                        $usuario = $receta['usuario'];
                        $estado_receta = $receta['estado']; // <-- Guardamos el estado
                    } else {
                        $titulo = "Receta no encontrada";
                        $descripcion = "";
                        $tiempo_preparacion = "";
                        $dificultad = "";
                        $categorias = "";
                        $usuario = "";
                    }

                    $stmt->close();

                    // Consulta para obtener los ingredientes de la receta

                    $sql_ingredientes = "SELECT i.nombre, ri.cantidad
                     FROM ingredientes i
                     LEFT JOIN recetas_ingredientes ri ON i.id_ingrediente = ri.ingrediente_id
                     WHERE ri.receta_id = ?";
                    $stmt_ingredientes = $conexion->prepare($sql_ingredientes);
                    $stmt_ingredientes->bind_param("i", $id_receta);
                    $stmt_ingredientes->execute();
                    $result_ingredientes = $stmt_ingredientes->get_result();

                    while ($ingrediente = $result_ingredientes->fetch_assoc()) {
                        $ingredientes[] = $ingrediente;
                    }

                    $stmt_ingredientes->close();

                    // Consulta para obtener las instrucciones de la receta

                    $sql_instrucciones = "SELECT paso, descripcion 
                                          FROM instrucciones 
                                          WHERE receta_id = ? 
                                          ORDER BY paso ASC";
                    $stmt_instrucciones = $conexion->prepare($sql_instrucciones);
                    $stmt_instrucciones->bind_param("i", $id_receta);
                    $stmt_instrucciones->execute();
                    $result_instrucciones = $stmt_instrucciones->get_result();

                    while ($instruccion = $result_instrucciones->fetch_assoc()) {
                        $instrucciones[] = $instruccion;
                    }

                    $stmt_instrucciones->close();

                    // Consulta para obtener las imágenes y videos de la receta 

                    $sql_medias = "SELECT i.url_imagen 
                                   FROM img_recetas i 
                                   LEFT JOIN imagenes_recetas ir ON i.id_img = ir.img_id 
                                   WHERE ir.recetas_id = ?";
                    $stmt_medias = $conexion->prepare($sql_medias);
                    $stmt_medias->bind_param("i", $id_receta);
                    $stmt_medias->execute();
                    $result_medias = $stmt_medias->get_result();
                    while ($media = $result_medias->fetch_assoc()) {
                        $url = $media['url_imagen'];
                        $extension = pathinfo($url, PATHINFO_EXTENSION);
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $imagenes[] = $url;
                        } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                            $videos[] = $url;
                        }
                    }
                    $stmt_medias->close();
                } else {
                    echo "<p>Id de receta inválido.</p>";
                }

                $conexion->close();
                ?>



                <style>
                    .carousel-inner img,
                    .carousel-inner video {
                        width: 100%;
                        height: auto;
                        max-height: 400px;
                        /* Ajusta la altura máxima del carrusel */
                    }

                    .carousel {
                        max-width: 600px;
                        /* Ajusta el ancho máximo del carrusel */
                        margin: auto;
                        /* Centra el carrusel */
                    }
                </style>


                <div class="content-wrapper">

                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">


                        <h4 class="pt-3 mb-0">
                            <span class="text-muted fw-light">Administración / Recetas /</span> Detalle Receta
                        </h4>

                        <div class="card g-3 mt-5">
                            <div class="card-body row g-3">
                                <div class="col-lg-14">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2 gap-1">
                                        <div class="me-1">
                                            <h5 class="mb-1">Titulo de la receta: <strong><?php echo $titulo; ?></strong></h5>
                                            <p class="mb-1">Usuario <strong> <?php echo $usuario; ?> </strong></p>
                                        </div>
                                    </div>
                                    <div class="card academy-content shadow-none border">
                                        <div id="recetaCarousel" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <?php
                                                $isActive = true; // Para marcar la primera imagen o video como activa
                                                foreach ($imagenes as $imagen): ?>
                                                    <div class="carousel-item <?= $isActive ? 'active' : ''; ?>">
                                                        <img src="<?= $imagen; ?>" class="d-block w-100 rounded" alt="Imagen de la receta" style="max-height: 500px; object-fit: cover;">
                                                    </div>
                                                    <?php $isActive = false; ?>
                                                <?php endforeach; ?>

                                                <?php foreach ($videos as $video): ?>
                                                    <div class="carousel-item <?= $isActive ? 'active' : ''; ?>">
                                                        <video class="d-block w-100 rounded" controls style="max-height: 500px; object-fit: cover;">
                                                            <source src="<?= $video; ?>" type="video/mp4">
                                                            Tu navegador no soporta el elemento de video.
                                                        </video>
                                                    </div>
                                                    <?php $isActive = false; ?>
                                                <?php endforeach; ?>
                                            </div>

                                            <!-- Controles del carrusel -->
                                            <button class="carousel-control-prev" type="button" data-bs-target="#recetaCarousel" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Anterior</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#recetaCarousel" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Siguiente</span>
                                            </button>
                                        </div>

                                        <div class="card-body">

                                            <h5>Caracteristicas de la receta</h5>
                                            <div class="d-flex flex-wrap">
                                                <div class="me-5">
                                                    <p class="text-nowrap"><i class='bx bx-check-double bx-sm me-2'></i>Dificultad: <?php echo $dificultad; ?></p>
                                                    <p class="text-nowrap"><i class='bx bx-user bx-sm me-2'></i>Categoría: <?php echo $categorias; ?></p>
                                                    <p class="text-nowrap "><i class='bx bxs-watch bx-sm me-2'></i>Video: <?php echo $tiempo_preparacion; ?></p>
                                                </div>
                                            </div>
                                            <hr class="my-4">
                                            <h5>Ingredientes de la receta</h5>
                                            <div class="d-flex flex-wrap">
                                                <div class="me-5">
                                                    <?php foreach ($ingredientes as $ingrediente): ?>
                                                        <ol>
                                                            <?php echo $ingrediente['nombre'] . ': ' . $ingrediente['cantidad']; ?>
                                                        </ol>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <hr class="my-4">
                                            <h5>Instrucciones de la receta</h5>
                                            <div class="d-flex flex-wrap">
                                                <div class="me-5">
                                                    <ol>
                                                        <?php foreach ($instrucciones as $instruccion): ?>
                                                            <li><?php echo $instruccion['descripcion']; ?>
                                                            </li> <?php endforeach; ?>
                                                    </ol>

                                                </div>
                                            </div>
                                            <hr class="mb-4 mt-2">
                                            <h5>Description</h5>
                                            <p class="mb-4">
                                                <?php echo $descripcion; ?>
                                            </p>

                                            <a href="vista-recetas.php" class="btn btn-outline-secondary shadow-sm"><i class='bx bx-arrow-back'></i> Volver atrás</a>
                                            <?php if ($estado_receta === 'pendiente'): ?>
                                                <button type="button" class="btn btn-outline-success shadow-sm ms-2"
                                                    onclick="aprobarReceta(<?= $id_receta ?>, '<?= htmlspecialchars($titulo, ENT_QUOTES) ?>')">
                                                    <i class="bx bx-check"></i> Aprobar
                                                </button>
                                                <button type="button" class="btn btn-outline-danger shadow-sm ms-2"
                                                    onclick="rechazarReceta(<?= $id_receta ?>, '<?= htmlspecialchars($titulo, ENT_QUOTES) ?>')">
                                                    <i class="bx bx-x"></i> Rechazar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>


                            </div>


                        </div>



                    </div>
                    <!-- / Content -->




                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Layout page -->
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- SweetAlert2 y función aprobarReceta -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function aprobarReceta(id_receta, titulo) {
                Swal.fire({
                    title: `¿Realmente desea aprobar la receta "${titulo}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`aprobar-receta.php?id=${id_receta}`, {
                                method: 'POST'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Receta aprobada',
                                        text: data.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.href = "vista-recetas.php";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Algo salió mal. Inténtalo de nuevo.'
                                });
                            });
                    }
                });
            }

            function rechazarReceta(id_receta, titulo) {
                Swal.fire({
                    title: `¿Realmente desea rechazar la receta "${titulo}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, rechazar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`rechazar-receta.php?id=${id_receta}`, {
                                method: 'POST'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Receta rechazada',
                                        text: data.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.href = "vista-recetas.php";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Algo salió mal. Inténtalo de nuevo.'
                                });
                            });
                    }
                });
            }
        </script>


        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->
        <script src="../assets/vendor/libs/jquery/jquery.js"></script>
        <script src="../assets/vendor/libs/popper/popper.js"></script>
        <script src="../assets/vendor/js/bootstrap.js"></script>
        <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

        <script src="../assets/vendor/js/menu.js"></script>
        <!-- endbuild -->

        <!-- Vendors JS -->
        <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

        <!-- Main JS -->
        <script src="../assets/js/main.js"></script>

        <!-- Page JS -->
        <script src="../assets/js/dashboards-analytics.js"></script>

        <!-- Place this tag in your head or just before your close body tag. -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>