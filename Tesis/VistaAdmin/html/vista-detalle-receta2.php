<?php
session_start();

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
    /* $Estado = $_SESSION['estado']; */
} else {
    // Redirigir a la página de inicio de sesión si no hay una sesión activa
    header("Location: Login.php");
    exit();
}
include("conexion.php");



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
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

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
                            <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs>
                                    <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="path-1"></path>
                                    <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z" id="path-3"></path>
                                    <path d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z" id="path-4"></path>
                                    <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z" id="path-5"></path>
                                </defs>
                                <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                                            <g id="Mask" transform="translate(0.000000, 8.000000)">
                                                <mask id="mask-2" fill="white">
                                                    <use xlink:href="#path-1"></use>
                                                </mask>
                                                <use fill="#696cff" xlink:href="#path-1"></use>
                                                <g id="Path-3" mask="url(#mask-2)">
                                                    <use fill="#696cff" xlink:href="#path-3"></use>
                                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                                </g>
                                                <g id="Path-4" mask="url(#mask-2)">
                                                    <use fill="#696cff" xlink:href="#path-4"></use>
                                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                                </g>
                                            </g>
                                            <g id="Triangle" transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                                <use fill="#696cff" xlink:href="#path-5"></use>
                                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
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

                    <li class="menu-item ">
                        <a href="usuarios.php" class="menu-link">
                            <i class='menu-icon bx bxs-user-detail'></i>
                            <div data-i18n="Basic Inputs">Usuarios</div>
                        </a>
                    </li>

                    <li class="menu-item ">
                        <a href="roles.php" class="menu-link">
                            <i class='menu-icon bx bxs-user-badge'></i>
                            <div data-i18n="Input groups">Roles</div>
                        </a>
                    </li>

                    <li class="menu-item active">
                        <a href="vista-recetas.php" class="menu-link">
                            <!-- <i class='menu-icon bx bx-donate-blood'></i> -->
                            <i class='menu-icon bx bxs-food-menu'></i>
                            <div data-i18n="Account Settings">Recetas</div>
                        </a>
                    </li>

                    <li class="menu-item ">
                        <a href="generos.php" class="menu-link">

                            <i class='menu-icon bx bx-male-female'></i>
                            <div data-i18n="Basic Inputs">Generos</div>
                        </a>
                    </li>

                    <li class="menu-item ">
                        <a href="categorias.php" class="menu-link">

                            <i class='menu-icon bx bxs-category'></i>
                            <div data-i18n="Basic Inputs">Categorias</div>
                        </a>
                    </li>

                    <li class="menu-item ">
                        <a href="auditoria.php" class="menu-link">

                            <i class='menu-icon bx bxs-time'></i>
                            <div data-i18n="Basic Inputs">Auditoria</div>
                        </a>
                    </li>

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

                if ($id_receta > 0) {
                    // Consulta para obtener los detalles de la receta
                    $sql = "SELECT r.titulo, r.descripcion, r.tiempo_preparacion, r.dificultad, 
                            GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias, u.nombre AS usuario
                        FROM recetas r
                        JOIN usuarios u ON r.usuario_id = u.id_usuario
                        JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                        JOIN categoria c ON rc.categoria_id = c.id_categoria
                        WHERE r.id_receta = ?
                        GROUP BY r.id_receta";

                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("i", $id_receta);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $receta = $result->fetch_assoc();
                        // Asignar los valores a las variables
                        $titulo = $receta['titulo'];
                        $descripcion = $receta['descripcion'];
                        $tiempo_preparacion = $receta['tiempo_preparacion'];
                        $dificultad = $receta['dificultad'];
                        $categorias = $receta['categorias'];
                        $usuario = $receta['usuario'];
                    } else {
                        echo "<p>Receta no encontrada.</p>";
                    }

                    $stmt->close();

                    // Consulta para obtener los ingredientes de la receta
                    $sql_ingredientes = "SELECT i.nombre, ri.cantidad
                                            FROM ingredientes i
                                            JOIN recetas_ingredientes ri ON i.id_ingrediente = ri.ingrediente_id
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
                    $sql_instrucciones = "SELECT paso, descripcion FROM instrucciones WHERE receta_id = ? ORDER BY paso ASC";
                    $stmt_instrucciones = $conexion->prepare($sql_instrucciones);
                    $stmt_instrucciones->bind_param("i", $id_receta);
                    $stmt_instrucciones->execute();
                    $result_instrucciones = $stmt_instrucciones->get_result();

                    while ($instruccion = $result_instrucciones->fetch_assoc()) {
                        $instrucciones[] = $instruccion;
                    }

                    $stmt_instrucciones->close();

                    // Consulta para obtener las imágenes y videos de la receta 
                    $sql_medias = "SELECT i.url_imagen FROM img_recetas i JOIN imagenes_recetas ir ON i.id_img = ir.img_id WHERE ir.recetas_id = ?";
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
                                        <div class="p-2">
                                            <div class="">

                                                <div class="row mb-5"> <?php foreach ($imagenes as $imagen): ?>
                                                        <div class="col-md-6 col-lg-4 mb-3">
                                                            <div class="card h-100">
                                                                <div class="card-body"> <img class="img-fluid d-flex mx-auto my-4 rounded" src="<?php echo $imagen; ?>" alt="Imagen" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php foreach ($videos as $video): ?>
                                                        <div class="col-md-6 col-lg-4 mb-3">
                                                            <div class="card h-100">
                                                                <div class="card-body">
                                                                    <video class="img-fluid d-flex mx-auto my-4 rounded" controls>
                                                                        <source src="<?php echo $video; ?>" type="video/mp4" /> Tu navegador no soporta el elemento de video.
                                                                    </video>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>




                                                <!-- =============================================================================== -->



                                            </div>
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


                                            <?php
                                            include("conexion.php");

                                            // Recibir los parámetros de la URL
                                            $id_receta = isset($_GET['id_receta']) ? (int)$_GET['id_receta'] : 0;

                                            if ($id_receta > 0) {
                                                // Consulta para obtener el id_usuario basado en el id_receta
                                                $sql = "SELECT usuario_id FROM recetas WHERE id_receta = ?";
                                                $statement = $conexion->prepare($sql);
                                                $statement->bind_param("i", $id_receta);
                                                $statement->execute();
                                                $resultado = $statement->get_result();
                                                $receta = $resultado->fetch_assoc();
                                                $id_usuario = $receta['usuario_id'];

                                                // Aquí puedes continuar con el resto de tu lógica para mostrar los detalles de la receta
                                                // ...
                                            } else {
                                                // Redirigir a una página de error o mostrar un mensaje de error
                                                echo "ID de receta no válido.";
                                                exit();
                                            }
                                            ?>

                                            <!-- Resto del código HTML -->
                                            <a href="PerfilUsuario.php?id_usuario=<?php echo $id_usuario; ?>" class="btn btn-outline-secondary shadow-sm"><i class='bx bx-arrow-back'></i> Volver atrás</a>
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