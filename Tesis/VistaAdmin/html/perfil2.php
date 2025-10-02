<?php
session_start();
include("conexion.php");
// Verificar si las variables de sesión están definidas antes de acceder a ellas

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
} else {
    header("Location: Login.php");
    exit();
}



$conexion->query("SET lc_time_names = 'es_ES'");

$sql = "SELECT U.*, G.*, R.nombre_rol, T.telefono AS user_telefono, E.email AS user_email, U.img, 
        DATE_FORMAT(U.fecha_creacion, '%d de %M del %Y') AS fecha_creacion_formateada, U.estado 
        FROM usuarios U 
        LEFT JOIN generos G ON U.genero = G.id_genero 
        LEFT JOIN roles R ON U.rol = R.id_rol 
        LEFT JOIN telefonos_usuarios T ON U.id_usuario = T.id_usuario 
        LEFT JOIN emails_usuarios E ON U.id_usuario = E.id_usuario 
        WHERE U.id_usuario = ? ORDER BY U.id_usuario ASC";

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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Mi perfil</title>

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
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
        }
    </style>
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

                    <li class="menu-item active">
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

                    // Mostrar solo las secciones permitidas para el rol
                    foreach ($secciones_menu as $id => $info) {
                        if (in_array($id, $secciones_permitidas)) {
                            $active = ($archivo_actual == $info['archivo']) ? 'active' : '';
                    ?>
                            <li class="menu-item <?= $active ?>">
                                <a href="<?= $info['archivo'] ?>" class="menu-link">
                                    <i class='menu-icon bx <?= $info['icono'] ?>'></i>
                                    <div data-i18n="Basic Inputs"><?= $info['nombre'] ?></div>
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

                <div class="content-wrapper">

                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">


                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Perfil</span>
                        </h4>
                        <div class="row">
                            <!-- User Sidebar -->
                            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                                <!-- User Card -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="user-avatar-section">
                                            <div class=" d-flex align-items-center flex-column">
                                                <img class="rounded-1 my-4" src="<?php echo $filas[0]['img'] ?>" height="110" width="110" alt="User avatar" />
                                                <div class="user-info text-center">
                                                    <h4 class="mb-2"><?php echo $filas[0]['nombre'], ' ', $filas[0]['apellido'] ?></h4>
                                                    <span class="badge bg-label-secondary"><?php echo $filas[0]['nombre_usuario'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-around flex-wrap my-4 py-3">
                                            <div class="d-flex align-items-start me-4 mt-3 gap-3">
                                                <span class="badge bg-label-primary p-2 rounded"><!-- <i class='bx bx-user bx-sm'></i> --><i class='bx bx-group bx-sm'></i></span>
                                                <?php

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
                                                ?>
                                                <div>
                                                    <h5 class="mb-0"><?php echo number_format($seguidores); ?> </h5>
                                                    <span>seguidores</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start mt-3 gap-3">
                                                <span class="badge bg-label-primary p-2 rounded"><!-- <i class='bx bx-customize bx-sm'></i> --><i class='bx bx-user-check bx-sm'></i></span>
                                                <div>
                                                    <h5 class="mb-0"><?php echo number_format($seguidos); ?></h5>
                                                    <span>seguidos</span>
                                                </div>
                                            </div>
                                        </div>
                                        <h5 class="pb-2 border-bottom mb-1"></h5>
                                        <div class="info-container">

                                            <div class="card-body">
                                                <small class="text-muted text-uppercase">Detalle</small>
                                                <ul class="list-unstyled mb-4 mt-3">
                                                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user"></i><span class="fw-medium mx-2">Nombre de usuario:</span> <span><?php echo $filas[0]['nombre_usuario'] ?></span></li>
                                                    <li class="d-flex align-items-center mb-3"><i class="bx bx-user"></i><span class="fw-medium mx-2">Nombre y Apellido:</span> <span><?php echo $filas[0]['nombre'], ' ', $filas[0]['apellido'] ?></span></li>
                                                    <li class="d-flex align-items-center mb-3"><i class="bx bx-male-female"></i><span class="fw-medium mx-2">Género:</span> <span><?php echo $filas[0]['nombre_genero'] ?></span></li>

                                                </ul>
                                                <small class="text-muted text-uppercase">Contacto</small>
                                                <ul class="list-unstyled mb-4 mt-3">
                                                    <li class="d-flex align-items-center mb-3"><i class="bx bx-phone"></i><span class="fw-medium mx-2">Teléfono:</span> <span><?php foreach ($telefonos as $telefono) {
                                                                                                                                                                                    echo " $telefono<br>";
                                                                                                                                                                                } ?></span></li>
                                                    <li class="d-flex align-items-center mb-3"><i class="bx bx-envelope"></i><span class="fw-medium mx-2">Email:</span> <span><?php foreach ($emails as $email) {
                                                                                                                                                                                    echo " $email<br>";
                                                                                                                                                                                } ?></span></li>
                                                </ul>
                                                <small class="text-muted text-uppercase">Detalle de cuenta</small>
                                                <ul class="list-unstyled mt-3 mb-0">
                                                    <li class="d-flex align-items-center mb-3"><i class="bx bx-calendar-alt me-2"></i>
                                                        <div class="d-flex flex-wrap"><span class="fw-medium me-2">Creación de usuario</span><span><?php echo $filas[0]['fecha_creacion_formateada']; ?></span></div>
                                                    </li>
                                                    <li class="d-flex align-items-center"><i class="bx bx-user-check me-2"></i>
                                                        <div class="d-flex flex-wrap"><span class="fw-medium me-2">Estado</span><span><?php echo $filas[0]['estado'] ?></span></div>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="d-flex justify-content-center pt-3">
                                                <!-- <a href="javascript:;" class="btn btn-primary me-3" data-bs-target="#editUser" data-bs-toggle="modal">Edit</a>
                                                <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspended</a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /User Card -->
                            </div>
                            <!--/ User Sidebar -->


                            <!-- User Content -->
                            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                                <!-- User Pills -->
                                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                                    <li class="nav-item"><a class="nav-link" href="#"><i class="bx bx-user me-1"></i>Cuenta</a></li>
                                    <!-- <li class="nav-item"><a class="nav-link" href="app-user-view-security.html"><i class="bx bx-lock-alt me-1"></i>Security</a></li>
                                    <li class="nav-item"><a class="nav-link" href="app-user-view-billing.html"><i class="bx bx-detail me-1"></i>Billing & Plans</a></li>
                                    <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="bx bx-bell me-1"></i>Notifications</a></li>
                                    <li class="nav-item"><a class="nav-link" href="app-user-view-connections.html"><i class="bx bx-link-alt me-1"></i>Connections</a></li> -->
                                </ul>
                                <!--/ User Pills -->

                                <!-- //! TABLA DE RECETAS DEL USUARIO -->

                                <?php


                                include('conexion.php');
                                // Conexión a la base de datos 
                                // Parámetros de búsqueda y filtros 
                                $search = isset($_GET['search']) ? $conexion->real_escape_string($_GET['search']) : '';
                                $filter_difficulty = isset($_GET['filter_difficulty']) ? $conexion->real_escape_string($_GET['filter_difficulty']) : '';
                                $filter_category = isset($_GET['filter_category']) ? $conexion->real_escape_string($_GET['filter_category']) : '';
                                // Parámetros de paginación 
                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                $limit = 10;
                                // Número de recetas por página 
                                $offset = ($page - 1) * $limit;
                                // Construir condiciones dinámicas para filtros 
                                $where_conditions = "1=1";
                                // Condición base 
                                if (!empty($search)) {
                                    $where_conditions .= " AND (r.titulo LIKE '%$search%' OR u.nombre LIKE '%$search%' OR c.nombre LIKE '%$search%')";
                                }
                                if (!empty($filter_difficulty)) {
                                    $where_conditions .= " AND r.dificultad = '$filter_difficulty'";
                                }
                                if (!empty($filter_category)) {
                                    $where_conditions .= " AND c.nombre = '$filter_category'";
                                }

                                $limit = 5; // Mostrar 5 datos por página

                                // Consulta para obtener recetas con filtros y paginación 
                                $sql = "SELECT r.titulo, u.nombre AS usuario, r.dificultad, r.tiempo_preparacion, r.id_receta,
                GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias
            FROM recetas r
            JOIN usuarios u ON r.usuario_id = u.id_usuario
            JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
            JOIN categoria c ON rc.categoria_id = c.id_categoria
            WHERE r.usuario_id = $ID_Usuario AND $where_conditions
            GROUP BY r.id_receta
            LIMIT $limit OFFSET $offset";
                                $result = $conexion->query($sql);
                                // Crear datos para JSON 
                                $recipes = [];
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $recipes[] = $row;
                                    }
                                }

                                // Consulta para obtener el total de recetas del usuario logeado
                                $sql_total = "SELECT COUNT(DISTINCT r.id_receta) AS total
                FROM recetas r
                JOIN usuarios u ON r.usuario_id = u.id_usuario
                JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                JOIN categoria c ON rc.categoria_id = c.id_categoria
                WHERE r.usuario_id = $ID_Usuario AND $where_conditions";

                                $result_total = $conexion->query($sql_total);
                                $total = $result_total->fetch_assoc()['total'];
                                $total_pages = ceil($total / $limit);

                                // Generar enlaces de paginación 
                                $pagination_html = '';
                                if ($total_pages > 1) {
                                    for ($i = 1; $i <= $total_pages; $i++) {
                                        $active_class = $i == $page ? 'active' : '';
                                        $pagination_html .= "<li class='page-item $active_class'><a class='page-link' href='#' data-page='$i'>$i</a></li>";
                                    }
                                }
                                // Consulta para obtener todas las categorías 
                                $sql_categorias = "SELECT nombre FROM categoria";
                                $result_categorias = $conexion->query($sql_categorias);
                                $categorias_options = "";
                                while ($row_categorias = $result_categorias->fetch_assoc()) {
                                    $selected = $filter_category == $row_categorias['nombre'] ? 'selected' : '';
                                    $categorias_options .= "<option value='{$row_categorias['nombre']}' $selected>{$row_categorias['nombre']}</option>";
                                }
                                $conexion->close();
                                ?>

                                <!-- <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title d-inline-block">Tabla de recetas</h5>
                                        <span class="card-subtitle d-inline-block ms-2">Recetas publicadas</span>
                                        <span class="ms-2">Total de recetas: <?php echo $total_recetas; ?></span>
                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <input type="text" id="buscar" class="form-control" placeholder="Buscar por título..." onkeyup="filtrarRecetas()">
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filtro_categoria" class="form-control" onchange="filtrarRecetas()">
                                                    <option value="">Todas las categorías</option>
                                                    <?php foreach ($categorias as $categoria) {
                                                        echo "<option value=\"$categoria\">$categoria</option>";
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="filtro_likes" class="form-control" onchange="filtrarRecetas()">
                                                    <option value="">Ordenar por likes</option>
                                                    <option value="asc">Menor a mayor</option>
                                                    <option value="desc">Mayor a menor</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table border-top table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">Título</th>
                                                    <th class="text-nowrap text-center">Categorías</th>
                                                    <th class="text-nowrap text-center">Likes</th>
                                                    <th class="text-nowrap text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla_recetas"> <?php foreach ($recetas as $receta): ?> <tr>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($receta['titulo']); ?></td>
                                                        <td class="text-center"><?php echo htmlspecialchars($receta['categorias']); ?></td>
                                                        <td class="text-center"><?php echo htmlspecialchars($receta['likes']); ?></td>
                                                        <td class="text-center">
                                                            <div class="dropdown">
                                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="vista-detalle-receta.php"><i class='bx bx-show-alt me-2'></i> Detalle</a>
                                                                    <a class="dropdown-item" href="vista-editar-receta.php?receta_id=<?php echo $receta['id_receta']; ?>"><i class='bx bx-edit-alt me-2'></i> Editar </a>

                                                                    <a class="dropdown-item" href="QUE SE DESHABILITE LA RECETA"><i class='bx bx-minus-circle me-2'></i> Deshabilitar</a>
                                                                    <a class="dropdown-item" href="QUE SE ELIMINE LA RECETA"><i class="bx bx-trash me-2"></i> Eliminar</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr> <?php endforeach; ?> </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body">
                                        <nav>
                                            <ul class="pagination" id="paginacion">  </ul>
                                        </nav>
                                    </div>
                                </div> -->
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <!-- <h5 class="m-0 me-2">Tabla de recetas</h5> -->
                                        </div>
                                        <div class="d-flex"> <input type="text" id="search" class="form-control me-2" placeholder="Buscar por titulo" value="<?php echo $search; ?>"> <select id="filter-difficulty" class="form-control me-2">
                                                <option value="">Todas las Dificultades</option>
                                                <option value="Fácil">Fácil</option>
                                                <option value="Intermedio">Intermedio</option>
                                                <option value="Difícil">Difícil</option>
                                            </select>
                                            <select id="filter-category" class="form-control me-2">
                                                <option value="">Todas las Categorías</option> <?php echo $categorias_options; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-datatable table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Título</th>
                                                    <!-- <th class="text-center">Usuario</th> -->
                                                    <th class="text-center">Categoría</th>
                                                    <th class="text-center">Dificultad</th>
                                                    <!-- <th class="text-center">Tiempo de preparación</th> -->
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recipes-table"> <?php foreach ($recipes as $row): ?> <tr>
                                                        <td class="text-center"><?php echo $row['titulo']; ?></td>
                                                        <!-- <td class="text-center"><?php echo $row['usuario']; ?></td> -->
                                                        <td class="text-center"><?php echo $row['categorias'] ?? ''; ?></td>
                                                        <td class="text-center"><?php echo $row['dificultad']; ?></td>
                                                        <!-- <td class="text-center"><?php echo $row['tiempo_preparacion']; ?> min</td> -->
                                                        <td class="text-center">
                                                            <div class="dropdown">
                                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="vista-detalle-receta.php?id_receta=<?php echo $row['id_receta']; ?>"><i class='bx bx-show-alt me-2'></i> Detalle</a>
                                                                    <a class="dropdown-item" href="vista-editar-receta.php?id_receta=<?php echo $row['id_receta']; ?>"><i class='bx bx-edit-alt me-2'></i> Editar</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr> <?php endforeach; ?> </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <p class="text-center">Total de recetas: <span id="total-recipes"><?php echo $total; ?></span></p>
                                        <ul class="pagination justify-content-center" id="pagination"> <?php echo $pagination_html; ?> </ul>
                                    </div>
                                </div>

                                <!-- <script>
                                    const recetasPorPagina = 10;
                                    let paginaActual = 1;
                                    const filas = document.querySelectorAll('#tabla_recetas tr');

                                    function inicializar() {
                                        mostrarPaginacion(filas.length);
                                        mostrarRecetas(filas, paginaActual);

                                    }

                                    function filtrarRecetas() {
                                        const buscar = document.getElementById('buscar').value.toLowerCase();
                                        const filtroCategoria = document.getElementById('filtro_categoria').value.toLowerCase();
                                        const filtroLikes = document.getElementById('filtro_likes').value;
                                        let recetasFiltradas = Array.from(filas);
                                        recetasFiltradas = recetasFiltradas.filter(fila => {
                                            const titulo = fila.children[0].textContent.toLowerCase();
                                            const categorias = fila.children[1].textContent.toLowerCase();
                                            const incluyeTitulo = titulo.includes(buscar);
                                            const incluyeCategoria = filtroCategoria === "" || categorias.includes(filtroCategoria);
                                            return incluyeTitulo && incluyeCategoria;
                                        });
                                        if (filtroLikes === "asc") {
                                            recetasFiltradas.sort((a, b) => parseInt(a.children[2].textContent) - parseInt(b.children[2].textContent));
                                        } else if (filtroLikes === "desc") {
                                            recetasFiltradas.sort((a, b) => parseInt(b.children[2].textContent) - parseInt(a.children[2].textContent));
                                        }
                                        mostrarRecetas(recetasFiltradas, paginaActual);
                                        mostrarPaginacion(recetasFiltradas.length);
                                    }

                                    function mostrarRecetas(recetasFiltradas, pagina) {
                                        const inicio = (pagina - 1) * recetasPorPagina;
                                        const fin = inicio + recetasPorPagina;
                                        const tablaRecetas = document.getElementById('tabla_recetas');
                                        tablaRecetas.innerHTML = "";
                                        recetasFiltradas.slice(inicio, fin).forEach(fila => {
                                            tablaRecetas.appendChild(fila);
                                        });
                                    }

                                    function mostrarPaginacion(totalRecetas) {
                                        const totalPaginas = Math.ceil(totalRecetas / recetasPorPagina);
                                        const paginacion = document.getElementById('paginacion');
                                        paginacion.innerHTML = "";
                                        for (let i = 1; i <= totalPaginas; i++) {
                                            const pagina = `<li class="page-item ${i === paginaActual ? 'active' : ''}"> <a class="page-link" href="#" onclick="cambiarPagina(${i})">${i}</a> </li>`;
                                            paginacion.insertAdjacentHTML('beforeend', pagina);
                                        }
                                    }

                                    function cambiarPagina(pagina) {
                                        paginaActual = pagina;
                                        filtrarRecetas();
                                    }
                                    // Inicializar al cargar la página 
                                    window.onload = inicializar;
                                </script> -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        const searchInput = document.getElementById('search');
                                        const filterDifficulty = document.getElementById('filter-difficulty');
                                        const filterCategory = document.getElementById('filter-category');
                                        const recipesTable = document.getElementById('recipes-table');
                                        const totalRecipes = document.getElementById('total-recipes');
                                        const pagination = document.getElementById('pagination');
                                        let currentPage = 1;
                                        // Función para cargar recetas desde el servidor 
                                        const loadRecipes = (page = 1) => {
                                            const search = searchInput.value;
                                            const difficulty = filterDifficulty.value;
                                            const category = filterCategory.value;
                                            fetch(`?search=${search}&filter_difficulty=${difficulty}&filter_category=${category}&page=${page}`).then(response => response.text()).then(data => {
                                                // Actualizar la parte del cuerpo de la tabla 
                                                const parser = new DOMParser();
                                                const doc = parser.parseFromString(data, 'text/html');
                                                const newRecipesTable = doc.getElementById('recipes-table').innerHTML;
                                                const newTotalRecipes = doc.getElementById('total-recipes').innerHTML;
                                                const newPagination = doc.getElementById('pagination').innerHTML;
                                                recipesTable.innerHTML = newRecipesTable;
                                                totalRecipes.innerHTML = newTotalRecipes;
                                                pagination.innerHTML = newPagination;
                                            }).catch(error => {
                                                console.error('Error al cargar las recetas:', error);
                                                recipesTable.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar las recetas</td></tr>';
                                            });
                                        };
                                        // Event listeners 
                                        searchInput.addEventListener('input', () => loadRecipes(1));
                                        filterDifficulty.addEventListener('change', () => loadRecipes(1));
                                        filterCategory.addEventListener('change', () => loadRecipes(1));
                                        pagination.addEventListener('click', (e) => {
                                            if (e.target.tagName === 'A') {
                                                e.preventDefault();
                                                const page = parseInt(e.target.dataset.page);
                                                loadRecipes(page);
                                            }
                                        });
                                        // Cargar recetas iniciales 
                                        loadRecipes();
                                    });
                                    // Función para manejar "Ver detalle" 
                                    function viewDetail(recipeId) {
                                        alert(`Detalle de la receta con ID: ${recipeId}`);
                                    }
                                </script>
                                <!-- /Project table -->


                                <!-- MAPA DIRECCIÓN DEL USUARIO -->

                                <?php
                                include("conexion.php");



                                // Consulta para obtener el id_localidad del usuario
                                $sql_usuario = "SELECT id_localidad FROM usuarios WHERE id_usuario = ?";
                                $statement_usuario = $conexion->prepare($sql_usuario);
                                $statement_usuario->bind_param("i", $ID_Usuario);
                                $statement_usuario->execute();
                                $resultado_usuario = $statement_usuario->get_result();
                                $usuario = $resultado_usuario->fetch_assoc();
                                $id_localidad = $usuario['id_localidad'] ?? null;

                                $latitud = null;
                                $longitud = null;
                                $provincia = null;
                                $localidad_nombre = null;
                                $barrio = null;

                                if ($id_localidad) {
                                    // Consulta para obtener las coordenadas y detalles de la localidad
                                    $sql_localidad = "SELECT latitud, longitud, provincia, localidad, barrio FROM localidades WHERE id_localidad = ?";
                                    $statement_localidad = $conexion->prepare($sql_localidad);
                                    $statement_localidad->bind_param("i", $id_localidad);
                                    $statement_localidad->execute();
                                    $resultado_localidad = $statement_localidad->get_result();
                                    $localidad = $resultado_localidad->fetch_assoc();

                                    $latitud = $localidad['latitud'] ?? null;
                                    $longitud = $localidad['longitud'] ?? null;
                                    $provincia = $localidad['provincia'] ?? null;
                                    $localidad_nombre = $localidad['localidad'] ?? null;
                                    $barrio = $localidad['barrio'] ?? null;
                                }

                                $conexion->close();
                                ?>

                                <div class="card mb-4 mt-4">
                                    <h5 class="card-header">Domicilio</h5>
                                    <div class="card-body">
                                        <div id="map"></div>
                                    </div>

                                </div>

                                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        const nombre_usuario = "<?php echo $filas[0]['nombre'], ' ', $filas[0]['apellido'] ?>"; // Reemplazar con el nombre del usuario
                                        const latitud = <?php echo $latitud !== null ? $latitud : 'null'; ?>;
                                        const longitud = <?php echo $longitud !== null ? $longitud : 'null'; ?>;
                                        const provincia = "<?php echo $provincia; ?>";
                                        const localidad = "<?php echo $localidad_nombre; ?>";
                                        const barrio = "<?php echo $barrio; ?>";

                                        if (latitud !== null && longitud !== null) {
                                            const map = L.map('map').setView([latitud, longitud], 13);

                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                            }).addTo(map);

                                            L.marker([latitud, longitud]).addTo(map)
                                                .bindPopup(`Ubicación de ${nombre_usuario}<br>Provincia: ${provincia}<br>Localidad: ${localidad}<br>Barrio: ${barrio}`)
                                                .openPopup();
                                        } else {
                                            document.getElementById('map').innerHTML = '<p class="text-center">El usuario no ha registrado su localidad.</p>';
                                        }
                                    });
                                </script>
                                <!--/ MAPA DIRECCIÓN DEL USUARIO -->

                            </div>
                            <!--/ User Content -->
                        </div>




                        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var latitud = <?php echo $usuario['latitud']; ?>;
                                var longitud = <?php echo $usuario['longitud']; ?>;

                                // Crea el mapa
                                var map = L.map('map').setView([latitud, longitud], 15);

                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                                    maxZoom: 18,
                                }).addTo(map);

                                var marker = L.marker([latitud, longitud]).addTo(map).bindPopup('<?php echo $usuario['localidad']; ?>').openPopup();
                            });
                        </script>




                        <!-- /Modals -->
                    </div>
                    <!-- / Content -->





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