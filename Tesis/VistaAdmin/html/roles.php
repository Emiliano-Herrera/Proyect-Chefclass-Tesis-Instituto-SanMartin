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

    $id_seccion_roles = 2; // ID de la sección Roles

    $sql_permiso_roles = "SELECT permisos FROM roles_permisos_secciones WHERE id_rol = $RolId AND id_seccion = $id_seccion_roles";
    $result_permiso_roles = $conexion->query($sql_permiso_roles);
    $permisos_roles = [];
    if ($row_permiso_roles = $result_permiso_roles->fetch_assoc()) {
        $permisos_roles = explode(',', str_replace("'", "", $row_permiso_roles['permisos']));
    }
} else {
    header("Location: Login.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];


?>


<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Roles</title>

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

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
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
                            <span class="app-brand-logo demo">
                                <img src="../../VistaCliente/img/chefclassFinal.png" alt="Logo" width="150">
                            </span>
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
            <!-- //TODO MENÚ LATERAL=========================================================================================================== -->
            <!-- //TODO MENÚ LATERAL=========================================================================================================== -->
            <!-- //TODO MENÚ LATERAL=========================================================================================================== -->
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
                    <!-- //?Search =============================================-->
                    <!-- //?NAV MENÚ =============================================-->
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
                                        <a class="dropdown-item" href="perfil.php">
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

                include "conexion.php";

                if (!$conexion) {
                    die("Error de conexión: " . mysqli_connect_error());
                }


                // Configuración de la paginación
                $resultadosPorPagina = 8; // Establece la cantidad de resultados por página
                $totalResultados = $conexion->query("SELECT COUNT(*) as total FROM roles WHERE estado = 'habilitado'")->fetch_assoc()['total'];
                $totalPaginas = ceil($totalResultados / $resultadosPorPagina);

                // Obtiene la página actual, si no se establece, se asume la página 1
                $paginaActual = isset($_GET['page']) ? $_GET['page'] : 1;

                // Calcula el índice de inicio para la consulta
                $inicio = ($paginaActual - 1) * $resultadosPorPagina;

                /* //?----------------------------------------------------------------- */
                // Obtén el término de búsqueda si está presente en la URL
                $term = isset($_GET['search']) ? $_GET['search'] : '';

                // Añade la condición WHERE para filtrar por el rol
                $whereCondition = !empty($term) ? "WHERE nombre LIKE '%$term%' " : '';

                $sql = "SELECT * FROM roles WHERE estado = 'habilitado' 
                " . (!empty($term) ? "AND nombre_rol LIKE '%$term%' " : '') . "
                LIMIT $inicio, $resultadosPorPagina";




                $result = $conexion->query($sql);

                // Consulta para contar el total de roles 
                $totalRoles = $conexion->query("SELECT COUNT(*) as total FROM roles WHERE estado = 'habilitado' ")->fetch_assoc()['total'];






                if (!$result) {
                    die("Error en la consulta: " . $conexion->error);
                }


                ?>
                <!-- //!Content wrapper MAIN MENÚ==================================================================================== -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Administración /</span> Roles
                        </h4>
                        <!-- Hoverable Table rows -->
                        <div class="card">
                            <div class="card-header border-bottom">

                                <?php if (in_array('crear', $permisos_roles)): ?>
                                    <h6 class="card-title">Añadir un Rol</h6>
                                    <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-primary mb-3 text-nowrap add-new-role">+ Nuevo Rol</button>
                                <?php endif; ?>
                                <!-- Modal para añadir un nuevo rol -->
                                <style>
                                    /* Hacer el modal mucho más ancho */
                                    .modal-dialog.modal-add-new-role {
                                        max-width: 1100px !important;
                                        width: 95vw;
                                    }

                                    /* Mostrar los permisos en una sola línea con espacio entre ellos */
                                    .permission-row {
                                        display: flex;
                                        flex-wrap: wrap;
                                        gap: 0.2rem;
                                        /* Espacio reducido entre checks */
                                        align-items: center;
                                    }

                                    .permission-row .form-check {
                                        min-width: 110px;
                                        /* Más pequeño */
                                        margin-bottom: 0.1rem;
                                        /* Menor separación vertical */
                                    }

                                    @media (max-width: 1200px) {
                                        .permission-row {
                                            flex-wrap: wrap;
                                            gap: 0.15rem;
                                        }
                                    }
                                </style>

                                <!--//todo Modal para añadir un nuevo rol \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\-->
                                <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
                                        <div class="modal-content p-3 p-md-5">
                                            <div class="modal-body">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="text-center mb-4">
                                                    <h3 class="role-title">Agregar nuevo Rol</h3>
                                                    <p>Establecer permiso del rol</p>
                                                </div>
                                                <!-- Add role form -->
                                                <form id="addRoleForm" class="row g-3" onsubmit="return false">
                                                    <div class="col-12 mb-4">
                                                        <label class="form-label" for="modalRoleName">Nombre del Rol</label>
                                                        <input type="text" id="modalRoleName" name="modalRoleName" class="form-control" placeholder="Ingresar rol" autocomplete="off" />
                                                        <div class="invalid-feedback" id="roleNameError"></div>
                                                    </div>
                                                    <div class="col-12">
                                                        <h4>Permisos del rol</h4>
                                                        <div class="table-responsive">
                                                            <table class="table table-flush-spacing">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Acceso de administrador</td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" id="selectAll" />
                                                                                <label class="form-check-label" for="selectAll">
                                                                                    Seleccionar todo
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Gestión de usuario</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="userManagementRead" data-seccion="1" data-permiso="detalle" />
                                                                                    <label class="form-check-label" for="userManagementRead">Ver detalle</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="userManagementWrite" data-seccion="1" data-permiso="editar" />
                                                                                    <label class="form-check-label" for="userManagementWrite">Editar</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="userManagementCreate" data-seccion="1" data-permiso="crear" />
                                                                                    <label class="form-check-label" for="userManagementCreate">Crear</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="userManagementEnable" data-seccion="1" data-permiso="estado" />
                                                                                    <label class="form-check-label" for="userManagementEnable">Estado</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="userManagementDelete" data-seccion="1" data-permiso="eliminar" />
                                                                                    <label class="form-check-label" for="userManagementDelete">Eliminar</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="recipeManagementApprove" data-seccion="1" data-permiso="autorizacion" />
                                                                                    <label class="form-check-label" for="recipeManagementApprove">aprobación de usuarios</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Gestión de roles</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="roleManagementRead" data-seccion="2" data-permiso="detalle" />
                                                                                    <label class="form-check-label" for="roleManagementRead">Ver detalle</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="roleManagementWrite" data-seccion="2" data-permiso="editar" />
                                                                                    <label class="form-check-label" for="roleManagementWrite">Editar</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="roleManagementCreate" data-seccion="2" data-permiso="crear" />
                                                                                    <label class="form-check-label" for="roleManagementCreate">Crear</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="roleManagementEnable" data-seccion="2" data-permiso="estado" />
                                                                                    <label class="form-check-label" for="roleManagementEnable">Estado</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="roleManagementDelete" data-seccion="2" data-permiso="eliminar" />
                                                                                    <label class="form-check-label" for="roleManagementDelete">Eliminar</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Gestión de recetas</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="recipeManagementRead" data-seccion="3" data-permiso="detalle" />
                                                                                    <label class="form-check-label" for="recipeManagementRead">Ver detalle</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="recipeManagementWrite" data-seccion="3" data-permiso="editar" />
                                                                                    <label class="form-check-label" for="recipeManagementWrite">Editar</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="recipeManagementCreate" data-seccion="3" data-permiso="crear" />
                                                                                    <label class="form-check-label" for="recipeManagementCreate">Crear</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="recipeManagementEnable" data-seccion="3" data-permiso="estado" />
                                                                                    <label class="form-check-label" for="recipeManagementEnable">Estado</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="recipeManagementDelete" data-seccion="3" data-permiso="eliminar" />
                                                                                    <label class="form-check-label" for="recipeManagementDelete">Eliminar</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="recipeManagementApprove" data-seccion="3" data-permiso="autorizacion" />
                                                                                    <label class="form-check-label" for="recipeManagementApprove">Aprobación de recetas</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Gestión de géneros</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="genreManagementWrite" data-seccion="4" data-permiso="editar" />
                                                                                    <label class="form-check-label" for="genreManagementWrite">Editar</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="genreManagementCreate" data-seccion="4" data-permiso="crear" />
                                                                                    <label class="form-check-label" for="genreManagementCreate">Crear</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="genreManagementDelete" data-seccion="4" data-permiso="eliminar" />
                                                                                    <label class="form-check-label" for="genreManagementDelete">Eliminar</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Gestión de categorías</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="categoryManagementRead" data-seccion="5" data-permiso="detalle" />
                                                                                    <label class="form-check-label" for="categoryManagementRead">Ver detalle</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="categoryManagementWrite" data-seccion="5" data-permiso="editar" />
                                                                                    <label class="form-check-label" for="categoryManagementWrite">Editar</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="categoryManagementCreate" data-seccion="5" data-permiso="crear" />
                                                                                    <label class="form-check-label" for="categoryManagementCreate">Crear</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="categoryManagementEnable" data-seccion="5" data-permiso="estado" />
                                                                                    <label class="form-check-label" for="categoryManagementEnable">Estado</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="categoryManagementDelete" data-seccion="5" data-permiso="eliminar" />
                                                                                    <label class="form-check-label" for="categoryManagementDelete">Eliminar</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Gestión Auditorias</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="auditsRead" data-seccion="6" data-permiso="detalle" />
                                                                                    <label class="form-check-label" for="auditsRead">Ver detalle</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Gestión Localidades</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="locationsRead" data-seccion="7" data-permiso="detalle" />
                                                                                    <label class="form-check-label" for="locationsRead">Ver detalle</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-nowrap fw-medium">Frontend</td>
                                                                        <td>
                                                                            <div class="permission-row">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" type="checkbox" id="frontendAccess" data-seccion="8" data-permiso="acceso" />
                                                                                    <label class="form-check-label" for="frontendAccess">Acceso</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="invalid-feedback d-block" id="permissionsError"></div>
                                                    </div>
                                                    <div class="col-12 text-center">
                                                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Añadir</button>
                                                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                    </div>
                                                </form>
                                                <!--/ Add role form -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--//todo Modal para añadir un nuevo rol \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\-->
                                <script>
                                    function isValidRoleName(str) {
                                        // Permite varias palabras separadas por un solo espacio, sin espacios al inicio/final
                                        return /^[A-Za-zÁÉÍÓÚáéíóúÑñ]+( [A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/.test(str);
                                    }

                                    const modalRoleName = document.getElementById('modalRoleName');
                                    modalRoleName.addEventListener('input', function(e) {
                                        let value = e.target.value;

                                        // Permitir letras y espacios, pero nunca dobles espacios ni espacios al inicio/final
                                        value = value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, ''); // Solo letras y espacios
                                        value = value.replace(/\s{2,}/g, ' '); // Reemplaza dobles espacios por uno solo
                                        // Permite espacio al final mientras el usuario escribe, pero no al inicio
                                        if (value.length > 0 && value[0] === ' ') value = value.slice(1);
                                        e.target.value = value;
                                    });

                                    document.getElementById('addRoleForm').addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        let valid = true;

                                        const roleNameInput = document.getElementById('modalRoleName');
                                        const roleNameError = document.getElementById('roleNameError');
                                        const permissionsError = document.getElementById('permissionsError');
                                        const roleName = roleNameInput.value.trim();

                                        // Validación del nombre
                                        if (roleName === '') {
                                            roleNameError.textContent = 'El nombre del rol es obligatorio.';
                                            roleNameInput.classList.add('is-invalid');
                                            valid = false;
                                        } else if (!isValidRoleName(roleName)) {
                                            roleNameError.textContent = 'Solo se permiten letras y un espacio entre palabras.';
                                            roleNameInput.classList.add('is-invalid');
                                            valid = false;
                                        } else {
                                            roleNameError.textContent = '';
                                            roleNameInput.classList.remove('is-invalid');
                                        }

                                        // Validación de permisos
                                        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
                                        let checkedCount = 0;
                                        permissionCheckboxes.forEach(cb => {
                                            if (cb.checked && cb.id !== 'selectAll') checkedCount++;
                                        });

                                        if (checkedCount === 0) {
                                            permissionsError.textContent = 'Selecciona al menos un permiso.';
                                            valid = false;
                                        } else {
                                            permissionsError.textContent = '';
                                        }

                                        if (valid) {
                                            let permisos = {};
                                            permissionCheckboxes.forEach(cb => {
                                                if (cb.checked && cb.id !== 'selectAll') {
                                                    let seccion = cb.getAttribute('data-seccion');
                                                    let permiso = cb.getAttribute('data-permiso');
                                                    if (!permisos[seccion]) permisos[seccion] = [];
                                                    permisos[seccion].push(permiso);
                                                }
                                            });

                                            // Formatear el nombre: primera letra de cada palabra en mayúscula
                                            let nombreFormateado = roleName.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase());

                                            $.ajax({
                                                url: 'registrar-rol.php',
                                                method: 'POST',
                                                data: {
                                                    nombre_rol: nombreFormateado,
                                                    permisos: permisos
                                                },
                                                dataType: 'json',
                                                success: function(response) {
                                                    if (response.status === 'success') {
                                                        $('#addRoleModal').modal('hide');
                                                        setTimeout(function() {
                                                            Swal.fire({
                                                                icon: 'success',
                                                                title: 'Rol registrado',
                                                                text: `El rol "${nombreFormateado}" se ha registrado correctamente.`,
                                                                confirmButtonColor: '#3085d6'
                                                            }).then(() => {
                                                                location.reload();
                                                            });
                                                        }, 400);
                                                    } else if (response.status === 'exists') {
                                                        // Mostrar error en el modal, no en SweetAlert
                                                        roleNameError.textContent = 'El nombre del rol ya existe.';
                                                        roleNameInput.classList.add('is-invalid');
                                                    } else {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Error',
                                                            text: response.message,
                                                            confirmButtonColor: '#d33'
                                                        });
                                                    }
                                                },
                                                error: function() {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: 'No se pudo registrar el rol. Intente nuevamente.',
                                                        confirmButtonColor: '#d33'
                                                    });
                                                }
                                            });
                                        }
                                    });

                                    document.getElementById('selectAll').addEventListener('change', function() {
                                        const checked = this.checked;
                                        document.querySelectorAll('.permission-checkbox').forEach(cb => {
                                            if (cb.id !== 'selectAll') cb.checked = checked;
                                        });
                                    });
                                </script>
                                <!--//?  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\-->



                                <br>
                                <hr>
                                <!-- Buscador de usuarios por nombre -->
                                <form action="" method="GET" class="row g-3">
                                    <div class="col-auto">
                                        <input type="text" name="search" id="search" class="form-control mx-2" placeholder="Nombre del Rol">

                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">Buscar<i class="bi bi-search m-2"></i></button>
                                    </div>

                                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                        <div class="col-auto">
                                            <a href="roles.php" class="btn btn-info">Ver todos</a>
                                        </div>
                                    <?php endif; ?>

                                </form>


                            </div>






                            <!-- //!TABLA=============================================================================== -->
                            <!-- Ahora va un nav bard -->
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover" id="">
                                    <thead>
                                        <tr>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class='table-border-bottom-0'>
                                        <?php while ($row = $result->fetch_assoc()) : ?>
                                            <tr>
                                                <td><?= $row['nombre_rol'] ?></td>
                                                <td><?= $row['estado'] ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <?php if (in_array('editar', $permisos_roles)): ?>
                                                                <!-- Botón para abrir el modal de editar rol -->
                                                                <a class="dropdown-item btn-edit-role"
                                                                    href="#"
                                                                    data-id="<?= $row['id_rol'] ?>"
                                                                    data-nombre="<?= htmlspecialchars($row['nombre_rol']) ?>">
                                                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (in_array('estado', $permisos_roles)): ?>
                                                                <a class="dropdown-item" href="#" onclick="deshabilitarRol(<?= $row['id_rol'] ?>, '<?= $row['nombre_rol'] ?>')">
                                                                    <i class="bi bi-box-arrow-right me-1"></i> Deshabilitar
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (in_array('eliminar', $permisos_roles)): ?>
                                                                <a class="dropdown-item" href="#" onclick="confirmarEliminacionRol(<?= $row['id_rol'] ?>, '<?= $row['nombre_rol'] ?>')">
                                                                    <i class="bi bi-trash me-1"></i> Eliminar
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>

                                <!-- Mostrar el total de roles -->
                                <div class="d-flex justify-content-end">
                                    <p>Total de roles: <strong><?= $totalRoles ?></strong></p>
                                </div>

                                <?php
                                $paginationUrl = !empty($term) ? "&search=$term" : '';

                                // Muestra la paginación dentro del div
                                echo "<div class='d-flex justify-content-center'>";
                                echo "<ul class='pagination'>";

                                // Botón "Anterior"
                                echo "<li class='page-item " . ($paginaActual == 1 ? ' disabled' : '') . "'>";
                                /* echo "<a class='page-link' href='?page=" . ($paginaActual - 1) . "' aria-label='Previous'>" ; */
                                echo "<a class='page-link' href='?page=" . ($paginaActual - 1) . "$paginationUrl' aria-label='Previous'>";
                                echo "<span aria-hidden='true'>&laquo;</span>";
                                echo "</a>";
                                echo "</li>";

                                // Muestra solo 5 páginas a la vez
                                $inicioPaginacion = max(1, $paginaActual - 2);
                                $finPaginacion = min($inicioPaginacion + 4, $totalPaginas);

                                for ($i = $inicioPaginacion; $i <= $finPaginacion; $i++) {
                                    $activeClass = ($i == $paginaActual) ? 'active' : '';
                                    echo "<li class='page-item $activeClass'><a class='page-link' href='?page=$i'>$i</a></li>";
                                }

                                echo "<li class='page-item " . ($paginaActual == $totalPaginas ? 'disabled' : '') . "'>";
                                /* echo "<a class='page-link' href='?page=" . ($paginaActual + 1) . "' aria-label='Next'>" ; */
                                echo "<a class='page-link' href='?page=" . ($paginaActual + 1) . "$paginationUrl' aria-label='Next'>";
                                echo "<span aria-hidden='true'>&raquo;</span>";
                                echo "</a>";
                                echo "</li>";

                                echo "</ul>";
                                echo "</div>"; ?>
                            </div>
                        </div>
                        <!--/ Hoverable Table rows -->

                    </div>
                    <!-- / Tabla de Roles Desabilitados -->






                    <div class="content-backdrop fade"></div>
                </div>
                <!-- //!======================================================================================================== -->

                <?php
                $resultadosPorPagina2 = 8; // Establece la cantidad de resultados por página
                $totalResultados2 = $conexion->query("SELECT COUNT(*) as total FROM roles WHERE estado = 'deshabilitado'")->fetch_assoc()['total'];
                $totalPaginas2 = ceil($totalResultados2 / $resultadosPorPagina2);

                // Obtiene la página actual, si no se establece, se asume la página 1
                $paginaActual2 = isset($_GET['page2']) ? $_GET['page2'] : 1;

                // Calcula el índice de inicio para la consulta
                $inicio2 = ($paginaActual2 - 1) * $resultadosPorPagina2;

                // Obtén el término de búsqueda si está presente en la URL
                $term2 = isset($_GET['search2']) ? $_GET['search2'] : '';
                $sql2 = "SELECT * FROM roles WHERE estado = 'deshabilitado' " . (!empty($term2) ? "AND nombre_rol LIKE '%$term2%' " : '') . " LIMIT $inicio2, $resultadosPorPagina2";
                $result2 = $conexion->query($sql2);

                // Consulta para contar el total de roles
                $totalRoles2 = $conexion->query("SELECT COUNT(*) as total FROM roles WHERE estado = 'deshabilitado' ")->fetch_assoc()['total'];

                if (!$result2) {
                    die("Error en la consulta: " . $conexion->error);
                }

                if ($result2->num_rows > 0) {
                    // Procesar los resultados y mostrar en la tabla
                ?>
                    <!-- Content wrapper MAIN MENU -->
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">

                            <!-- Hoverable Table rows -->
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h4 class="py-3 mb-4 text-muted">
                                        Roles Deshabilitados
                                    </h4>
                                    <h6 class="card-title">Buscar Rol Deshabilitado</h6>
                                    <form action="" method="GET" class="row g-3">
                                        <div class="col-auto">
                                            <input type="text" name="search2" id="search2" class="form-control mx-2" placeholder="Nombre del Rol">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>
                                        <?php if (isset($_GET['search2']) && !empty($_GET['search2'])): ?>
                                            <div class="col-auto">
                                                <a href="roles.php" class="btn btn-info">Ver todos</a>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <hr>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-hover" id="tabla-empleados">
                                        <thead>
                                            <tr>
                                                <th>Rol</th>
                                                <th>Estado</th>
                                                <th>Habilitar</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php while ($row2 = $result2->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?= $row2['nombre_rol'] ?></td>
                                                    <td><?= $row2['estado'] ?></td>
                                                    <td>
                                                        <?php if (in_array('estado', $permisos_roles)): ?>
                                                            <a class="btn btn-label-secondary" href="#" onclick="habilitarRol(<?= $row2['id_rol'] ?>, '<?= $row2['nombre_rol'] ?>')">
                                                                <i class="bi bi-box-arrow-in-right"></i> Habilitar
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end">
                                        <p>Total de roles: <strong><?= $totalRoles2 ?></strong></p>
                                    </div>

                                    <?php
                                    $paginationUrl2 = !empty($term2) ? "&search=$term2" : '';

                                    // Muestra la paginación dentro del div
                                    echo "<div class='d-flex justify-content-center'>";
                                    echo "<ul class='pagination'>";

                                    // Botón "Anterior"
                                    echo "<li class='page-item " . ($paginaActual2 == 1 ? ' disabled' : '') . "'>";
                                    echo "<a class='page-link' href='?page=" . ($paginaActual2 - 1) . "$paginationUrl2' aria-label='Previous'>";
                                    echo "<span aria-hidden='true'>&laquo;</span>";
                                    echo "</a>";
                                    echo "</li>";

                                    // Muestra solo 5 páginas a la vez
                                    $inicioPaginacion2 = max(1, $paginaActual2 - 2);
                                    $finPaginacion2 = min($inicioPaginacion2 + 4, $totalPaginas2);

                                    for ($i = $inicioPaginacion2; $i <= $finPaginacion2; $i++) {
                                        $activeClass2 = ($i == $paginaActual2) ? 'active' : '';
                                        echo "<li class='page-item $activeClass2'><a class='page-link' href='?page=$i'>$i</a></li>";
                                    }

                                    echo "<li class='page-item " . ($paginaActual2 == $totalPaginas2 ? 'disabled' : '') . "'>";
                                    echo "<a class='page-link' href='?page=" . ($paginaActual2 + 1) . "$paginationUrl2' aria-label='Next'>";
                                    echo "<span aria-hidden='true'>&raquo;</span>";
                                    echo "</a>";
                                    echo "</li>";

                                    echo "</ul>";
                                    echo "</div>";
                                    ?>
                                </div>
                            </div>
                            <!--/ Hoverable Table rows -->
                        </div>
                    </div>
                <?php
                }
                ?>


                <!-- / Tabla de Roles Desabilitados -->






                <div class="content-backdrop fade"></div>
            </div>
            <!-- //!======================================================================================================== -->




            <!-- //TODO \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ -->
            <!-- Modal para editar un rol -->
            <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
                    <div class="modal-content p-3 p-md-5">
                        <div class="modal-body">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-center mb-4">
                                <h3 class="role-title">Editar Rol</h3>
                                <p>Modificar nombre y permisos del rol</p>
                            </div>
                            <!-- Edit role form -->
                            <form id="editRoleForm" class="row g-3" onsubmit="return false">
                                <input type="hidden" id="editRoleId" name="editRoleId" />
                                <div class="col-12 mb-4">
                                    <label class="form-label" for="editModalRoleName">Nombre del Rol</label>
                                    <input type="text" id="editModalRoleName" name="editModalRoleName" class="form-control" placeholder="Ingresar rol" autocomplete="off" />
                                    <div class="invalid-feedback" id="editRoleNameError"></div>
                                </div>
                                <div class="col-12">
                                    <h4>Permisos del rol</h4>
                                    <div class="table-responsive">
                                        <table class="table table-flush-spacing">
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Acceso de administrador</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input edit-permission-checkbox" type="checkbox" id="selectAllEdit" />
                                                            <label class="form-check-label" for="selectAllEdit">
                                                                Seleccionar todo
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Gestión de usuario</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="userManagementReadEdit" data-seccion="1" data-permiso="detalle" />
                                                                <label class="form-check-label" for="userManagementReadEdit">Ver detalle</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="userManagementWriteEdit" data-seccion="1" data-permiso="editar" />
                                                                <label class="form-check-label" for="userManagementWriteEdit">Editar</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="userManagementCreateEdit" data-seccion="1" data-permiso="crear" />
                                                                <label class="form-check-label" for="userManagementCreateEdit">Crear</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="userManagementEnableEdit" data-seccion="1" data-permiso="estado" />
                                                                <label class="form-check-label" for="userManagementEnableEdit">Estado</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="userManagementDeleteEdit" data-seccion="1" data-permiso="eliminar" />
                                                                <label class="form-check-label" for="userManagementDeleteEdit">Eliminar</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="recipeManagementApprove" data-seccion="1" data-permiso="autorizacion" />
                                                                <label class="form-check-label" for="recipeManagementApprove">aprobación de usuarios</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Gestión de roles</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="roleManagementReadEdit" data-seccion="2" data-permiso="detalle" />
                                                                <label class="form-check-label" for="roleManagementReadEdit">Ver detalle</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="roleManagementWriteEdit" data-seccion="2" data-permiso="editar" />
                                                                <label class="form-check-label" for="roleManagementWriteEdit">Editar</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="roleManagementCreateEdit" data-seccion="2" data-permiso="crear" />
                                                                <label class="form-check-label" for="roleManagementCreateEdit">Crear</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="roleManagementEnableEdit" data-seccion="2" data-permiso="estado" />
                                                                <label class="form-check-label" for="roleManagementEnableEdit">Estado</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="roleManagementDeleteEdit" data-seccion="2" data-permiso="eliminar" />
                                                                <label class="form-check-label" for="roleManagementDeleteEdit">Eliminar</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Gestión de recetas</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="recipeManagementReadEdit" data-seccion="3" data-permiso="detalle" />
                                                                <label class="form-check-label" for="recipeManagementReadEdit">Ver detalle</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="recipeManagementWriteEdit" data-seccion="3" data-permiso="editar" />
                                                                <label class="form-check-label" for="recipeManagementWriteEdit">Editar</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="recipeManagementCreateEdit" data-seccion="3" data-permiso="crear" />
                                                                <label class="form-check-label" for="recipeManagementCreateEdit">Crear</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="recipeManagementEnableEdit" data-seccion="3" data-permiso="estado" />
                                                                <label class="form-check-label" for="recipeManagementEnableEdit">Estado</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="recipeManagementDeleteEdit" data-seccion="3" data-permiso="eliminar" />
                                                                <label class="form-check-label" for="recipeManagementDeleteEdit">Eliminar</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="recipeManagementApproveEdit" data-seccion="3" data-permiso="autorizacion" />
                                                                <label class="form-check-label" for="recipeManagementApproveEdit">Aprobación de recetas</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Gestión de géneros</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="genreManagementWriteEdit" data-seccion="4" data-permiso="editar" />
                                                                <label class="form-check-label" for="genreManagementWriteEdit">Editar</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="genreManagementCreateEdit" data-seccion="4" data-permiso="crear" />
                                                                <label class="form-check-label" for="genreManagementCreateEdit">Crear</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="genreManagementDeleteEdit" data-seccion="4" data-permiso="eliminar" />
                                                                <label class="form-check-label" for="genreManagementDeleteEdit">Eliminar</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Gestión de categorías</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="categoryManagementReadEdit" data-seccion="5" data-permiso="detalle" />
                                                                <label class="form-check-label" for="categoryManagementReadEdit">Ver detalle</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="categoryManagementWriteEdit" data-seccion="5" data-permiso="editar" />
                                                                <label class="form-check-label" for="categoryManagementWriteEdit">Editar</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="categoryManagementCreateEdit" data-seccion="5" data-permiso="crear" />
                                                                <label class="form-check-label" for="categoryManagementCreateEdit">Crear</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="categoryManagementEnableEdit" data-seccion="5" data-permiso="estado" />
                                                                <label class="form-check-label" for="categoryManagementEnableEdit">Estado</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="categoryManagementDeleteEdit" data-seccion="5" data-permiso="eliminar" />
                                                                <label class="form-check-label" for="categoryManagementDeleteEdit">Eliminar</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Gestión Auditorias</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="auditsReadEdit" data-seccion="6" data-permiso="detalle" />
                                                                <label class="form-check-label" for="auditsReadEdit">Ver detalle</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Gestión Localidades</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="locationsReadEdit" data-seccion="7" data-permiso="detalle" />
                                                                <label class="form-check-label" for="locationsReadEdit">Ver detalle</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Frontend</td>
                                                    <td>
                                                        <div class="permission-row">
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" id="frontendAccessEdit" data-seccion="8" data-permiso="acceso" />
                                                                <label class="form-check-label" for="frontendAccessEdit">Acceso</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="invalid-feedback d-block" id="editPermissionsError"></div>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Guardar Cambios</button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                </div>
                            </form>
                            <!--/ Edit role form -->
                        </div>
                    </div>
                </div>
            </div>







            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->
    <?php
    // Si el botón de búsqueda se presionó sin introducir un término o no se encontraron coincidencias
    if (isset($_GET['search'])) {
        if (empty($term)) {
            // Si el campo de búsqueda está vacío

            echo "<script> window.onload = function() { Swal.fire({ icon: 'warning', title: 'Campo vacío', text: 'Por favor ingresa un nombre de rol para buscar.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'roles.php'; }); } </script>";
            exit;
        } elseif ($result->num_rows == 0) {
            // Si no se encontraron resultados
            echo "<script> window.onload = function() { Swal.fire({ icon: 'info', title: 'Sin resultados', text: 'No se encontró ningún rol con ese nombre.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'roles.php'; }); } </script>";
            exit;
        }
    }


    // Si el botón de búsqueda se presionó sin introducir un término o no se encontraron coincidencias
    if (isset($_GET['search2'])) {
        if (empty($term2)) {
            // Si el campo de búsqueda está vacío

            echo "<script> window.onload = function() { Swal.fire({ icon: 'warning', title: 'Campo vacío', text: 'Por favor ingresa un nombre de un rol deshabilitado para buscar.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'roles.php'; }); } </script>";
            exit;
        } elseif ($result2->num_rows == 0) {
            // Si no se encontraron resultados
            echo "<script> window.onload = function() { Swal.fire({ icon: 'info', title: 'Sin resultados', text: 'No se encontró ningún rol deshabilitado con ese nombre.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'roles.php'; }); } </script>";
            exit;
        }
    }


    ?>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function habilitarRol(id, nombre_rol) {
            console.log('Habilitar rol llamado para ID:', id, 'Nombre del rol:', nombre_rol);
            // Mensaje de depuración 
            Swal.fire({
                title: `¿Realmente desea habilitar el rol "${nombre_rol}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, habilitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Redirigiendo a habilitar-rol.php');
                    // Mensaje de depuración 
                    window.location.href = "habilitar-rol.php?id=" + id + "&confirmacion=si";
                }
            });
        }

        function deshabilitarRol(id, nombre_rol) {
            console.log('Deshabilitar rol llamado para ID:', id, 'Nombre del rol:', nombre_rol); // Mensaje de depuración
            Swal.fire({
                title: `¿Realmente desea deshabilitar el rol "${nombre_rol}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, deshabilitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Redirigiendo a deshabilitar-rol.php'); // Mensaje de depuración
                    window.location.href = "deshabilitar-rol.php?id=" + id + "&confirmacion=si";
                }
            });
        }

        function confirmarEliminacionRol(id_rol, nombre_rol) {
            console.log('Eliminar rol llamado para ID:', id_rol, 'Nombre del rol:', nombre_rol); // Mensaje de depuración
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Realmente quiere eliminar el rol "${nombre_rol}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Enviando petición para eliminar rol'); // Mensaje de depuración
                    fetch('eliminar-rol.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_rol=${id_rol}`
                    }).then(response => response.json()).then(data => {
                        if (data.status === "success") {
                            console.log('Rol eliminado con éxito'); // Mensaje de depuración
                            Swal.fire({
                                title: 'Éxito',
                                text: data.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            console.log('Error al eliminar el rol:', data.message); // Mensaje de depuración
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    }).catch(error => {
                        console.error('Hubo un problema al eliminar el rol:', error); // Mensaje de depuración
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al eliminar el rol. Intente nuevamente.',
                        });
                    });
                }
            });
        }
    </script>



    <script>
        let datosPendientesEdicion = null;
        let accionPendiente = null;

        $(document).on('click', '.btn-edit-role', function(e) {
            e.preventDefault();
            $('.dropdown-menu.show').removeClass('show');
            $('#editRoleForm')[0].reset();
            $('#editRoleNameError').text('');
            $('#editPermissionsError').text('');
            $('#editModalRoleName').removeClass('is-invalid');
            var idRol = $(this).data('id');
            var nombreRol = $(this).data('nombre');
            $('#editRoleId').val(idRol);
            $('#editModalRoleName').val(nombreRol);
            $('.edit-permission-checkbox').prop('checked', false);
            $.ajax({
                url: 'obtener-permisos-rol.php',
                method: 'POST',
                data: {
                    id_rol: idRol
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $.each(response.permisos, function(seccion, permisosArr) {
                            permisosArr.forEach(function(permiso) {
                                $('.edit-permission-checkbox[data-seccion="' + seccion + '"][data-permiso="' + permiso + '"]').prop('checked', true);
                            });
                        });
                    }
                    var modal = document.getElementById('editRoleModal');
                    var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                    modalInstance.show();
                }
            });
        });

        document.getElementById('selectAllEdit').addEventListener('change', function() {
            const checked = this.checked;
            document.querySelectorAll('.edit-permission-checkbox').forEach(cb => {
                if (cb.id !== 'selectAllEdit') cb.checked = checked;
            });
        });

        document.getElementById('editRoleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let valid = true;

            const roleNameInput = document.getElementById('editModalRoleName');
            const roleNameError = document.getElementById('editRoleNameError');
            const permissionsError = document.getElementById('editPermissionsError');
            const roleName = roleNameInput.value.trim();

            if (roleName === '') {
                roleNameError.textContent = 'El nombre del rol es obligatorio.';
                roleNameInput.classList.add('is-invalid');
                valid = false;
            } else if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+( [A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/.test(roleName)) {
                roleNameError.textContent = 'Solo se permiten letras y un espacio entre palabras.';
                roleNameInput.classList.add('is-invalid');
                valid = false;
            } else {
                roleNameError.textContent = '';
                roleNameInput.classList.remove('is-invalid');
            }

            const permissionCheckboxes = document.querySelectorAll('.edit-permission-checkbox');
            let checkedCount = 0;
            permissionCheckboxes.forEach(cb => {
                if (cb.checked && cb.id !== 'selectAllEdit') checkedCount++;
            });

            if (checkedCount === 0) {
                permissionsError.textContent = 'Selecciona al menos un permiso.';
                valid = false;
            } else {
                permissionsError.textContent = '';
            }

            if (valid) {
                let permisos = {};
                permissionCheckboxes.forEach(cb => {
                    if (cb.checked && cb.id !== 'selectAllEdit') {
                        let seccion = cb.getAttribute('data-seccion');
                        let permiso = cb.getAttribute('data-permiso');
                        if (!permisos[seccion]) permisos[seccion] = [];
                        permisos[seccion].push(permiso);
                    }
                });

                let nombreFormateado = roleName.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase());

                // Guarda los datos pendientes y cierra el modal
                datosPendientesEdicion = {
                    id_rol: document.getElementById('editRoleId').value,
                    nombre_rol: nombreFormateado,
                    permisos: permisos
                };
                var modal = document.getElementById('editRoleModal');
                var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                modalInstance.hide();
            }
        });

        // Cuando el modal termina de cerrarse, pregunta si está seguro y luego guarda
        document.getElementById('editRoleModal').addEventListener('hidden.bs.modal', function() {
            if (datosPendientesEdicion) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas guardar los cambios en el rol "${datosPendientesEdicion.nombre_rol}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        accionPendiente = function() {
                            const datos = datosPendientesEdicion;
                            $.ajax({
                                url: 'editar-rol.php',
                                method: 'POST',
                                data: {
                                    id_rol: datos.id_rol,
                                    nombre_rol: datos.nombre_rol,
                                    permisos: JSON.stringify(datos.permisos)
                                },
                                dataType: 'json',
                                success: function(response) {
                                    console.log('Respuesta AJAX:', response);
                                    if (response.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: '¡Éxito!',
                                            text: `El rol "${datos.nombre_rol}" se ha guardado correctamente.`,
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else if (response.status === 'exists') {
                                        var modal = document.getElementById('editRoleModal');
                                        var modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                                        modalInstance.show();
                                        document.getElementById('editRoleNameError').textContent = 'El nombre del rol ya existe.';
                                        document.getElementById('editModalRoleName').classList.add('is-invalid');
                                    } else {
                                        document.getElementById('editPermissionsError').textContent = response.message || 'Error al actualizar el rol.';
                                    }
                                },
                                error: function() {
                                    document.getElementById('editPermissionsError').textContent = 'No se pudo actualizar el rol. Intente nuevamente.';
                                }
                            });
                        };
                        setTimeout(function() {
                            if (typeof accionPendiente === 'function') {
                                accionPendiente();
                                accionPendiente = null;
                                datosPendientesEdicion = null; // <-- AQUÍ SE LIMPIA, después de ejecutar la acción
                            }
                        }, 250);
                    } else {
                        datosPendientesEdicion = null; // Si cancela, limpia aquí
                    }
                });
            }
        });
    </script>


</body>

</html>