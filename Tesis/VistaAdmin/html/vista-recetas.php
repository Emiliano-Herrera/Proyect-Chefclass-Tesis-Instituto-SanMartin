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

    $id_seccion_recetas = 3; // ID de la sección Recetas

    $sql_permiso_recetas = "SELECT permisos FROM roles_permisos_secciones WHERE id_rol = $RolId AND id_seccion = $id_seccion_recetas";
    $result_permiso_recetas = $conexion->query($sql_permiso_recetas);
    $permisos_recetas = [];
    if ($row_permiso_recetas = $result_permiso_recetas->fetch_assoc()) {
        $permisos_recetas = explode(',', str_replace("'", "", $row_permiso_recetas['permisos']));
    }
} else {
    header("Location: Login.php");
    exit();
}



$sql_recetas_pendientes = "
    SELECT r.id_receta, r.titulo, r.descripcion, r.dificultad, u.nombre AS usuario
    FROM recetas r
    JOIN usuarios u ON r.usuario_id = u.id_usuario
    WHERE r.estado = 'pendiente'
";
$result_recetas_pendientes = $conexion->query($sql_recetas_pendientes);
$recetas_pendientes = [];
if ($result_recetas_pendientes->num_rows > 0) {
    while ($row = $result_recetas_pendientes->fetch_assoc()) {
        $recetas_pendientes[] = $row;
    }
}
?>









<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Recetas</title>

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
                            <img src="../../VistaCliente/img/chefclassFinal.png" alt="Logo" width="150">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2"></span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>
                <!-- //TODO MENÚ LATERAL=========================================================================================================== -->
                <!-- //TODO MENÚ LATERAL=========================================================================================================== -->
                <!-- //TODO MENÚ LATERAL=========================================================================================================== -->
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
                                        <a class="dropdown-item" href="./Perfil.html">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Mi perfil</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="../../VistaDw2/index.php">
                                            <i class='bx bx-arrow-back me-2'></i>
                                            <span class="align-middle">Volver al sitio web</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="../../VistaDw2/Logout.php">
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

                <!-- //!Content wrapper MAIN MENÚ -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Administración /</span> Recetas
                        </h4>

                        <div class="row">

                            <!--  //!=== TABLA DE RECETAS =====================================================================================================-->


                            <?php
                            include('conexion.php');
                            // Conexión a la base de datos 
                            // Parámetros de búsqueda y filtros 
                            $search = isset($_GET['search']) ? $conexion->real_escape_string($_GET['search']) : '';
                            $filter_difficulty = isset($_GET['filter_difficulty']) ? $conexion->real_escape_string($_GET['filter_difficulty']) : '';
                            $filter_category = isset($_GET['filter_category']) ? $conexion->real_escape_string($_GET['filter_category']) : '';
                            $filter_fecha = isset($_GET['filter_fecha']) ? $conexion->real_escape_string($_GET['filter_fecha']) : '';
                            // Parámetros de paginación 
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $limit = 8; // Cambiado a 8 por página
                            $offset = ($page - 1) * $limit;
                            // Construir condiciones dinámicas para filtros 
                            $where_conditions = "1=1";
                            if (!empty($search)) {
                                $where_conditions .= " AND (r.titulo LIKE '%$search%' OR u.nombre LIKE '%$search%' OR c.nombre LIKE '%$search%')";
                            }
                            if (!empty($filter_difficulty)) {
                                $where_conditions .= " AND r.dificultad = '$filter_difficulty'";
                            }
                            if (!empty($filter_category)) {
                                $where_conditions .= " AND c.nombre = '$filter_category'";
                            }

                            // Orden por fecha de subida
                            $order_by = "r.fecha_creacion DESC";
                            if ($filter_fecha === "antigua") {
                                $order_by = "r.fecha_creacion ASC";
                            } elseif ($filter_fecha === "reciente") {
                                $order_by = "r.fecha_creacion DESC";
                            }

                            // Consulta para obtener recetas con filtros y paginación 
                            $sql = "SELECT r.titulo, u.nombre AS usuario, r.dificultad, r.fecha_creacion, r.id_receta,
                            GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias,
                            u.img AS img_perfil,
                            img.url_imagen AS receta_img
                            FROM recetas r
                            JOIN usuarios u ON r.usuario_id = u.id_usuario
                            JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                            JOIN categoria c ON rc.categoria_id = c.id_categoria
                            LEFT JOIN imagenes_recetas ir ON r.id_receta = ir.recetas_id
                            LEFT JOIN img_recetas img ON ir.img_id = img.id_img
                            WHERE $where_conditions AND r.estado = 'habilitado'
                            GROUP BY r.id_receta
                            ORDER BY $order_by
                            LIMIT $limit OFFSET $offset";
                            $result = $conexion->query($sql);
                            // Crear datos para JSON 
                            $recipes = [];
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $recipes[] = $row;
                                }
                            }

                            // Contar total de recetas para la paginación 
                            $sql_total = "SELECT COUNT(DISTINCT r.id_receta) AS total
                            FROM recetas r
                            JOIN usuarios u ON r.usuario_id = u.id_usuario
                            JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                            JOIN categoria c ON rc.categoria_id = c.id_categoria
                            WHERE $where_conditions AND r.estado = 'habilitado'";

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
                            ?>

                            <div class="col-12 order-5">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <!-- <h5 class="m-0 me-2">Tabla de recetas</h5> -->
                                        </div>
                                        <div class="d-flex">
                                            <input type="text" id="search" class="form-control me-2" placeholder="Buscar por titulo" value="<?php echo $search; ?>">
                                            <select id="filter-difficulty" class="form-control me-2">
                                                <option value="">Nivel de Dificultad</option>
                                                <option value="Fácil" <?php if ($filter_difficulty == "Fácil") echo "selected"; ?>>Fácil</option>
                                                <option value="Intermedio" <?php if ($filter_difficulty == "Intermedio") echo "selected"; ?>>Intermedio</option>
                                                <option value="Difícil" <?php if ($filter_difficulty == "Difícil") echo "selected"; ?>>Difícil</option>
                                            </select>
                                            <select id="filter-category" class="form-control me-2">
                                                <option value="">Categorías</option> <?php echo $categorias_options; ?>
                                            </select>
                                            <select id="filter-fecha" class="form-control me-2">
                                                <option value="">Fecha de subida</option>
                                                <option value="reciente" <?php if ($filter_fecha == "reciente") echo "selected"; ?>>Más reciente</option>
                                                <option value="antigua" <?php if ($filter_fecha == "antigua") echo "selected"; ?>>Más antigua</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-datatable table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Título</th>
                                                    <th class="text-center">Usuario</th>
                                                    <th class="text-center">Categoría</th>
                                                    <th class="text-center">Dificultad</th>
                                                    <th class="text-center">Fecha de subida</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recipes-table">
                                                <?php foreach ($recipes as $row): ?>
                                                    <tr>
                                                        <!-- Título + imagen receta -->
                                                        <td class="">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar me-2">
                                                                    <img src="<?php echo !empty($row['receta_img']) ? htmlspecialchars($row['receta_img']) : '../../assets/img/avatars/1.png'; ?>" alt="Receta" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                                                </div>
                                                                <span><?php echo htmlspecialchars($row['titulo']); ?></span>
                                                            </div>
                                                        </td>
                                                        <!-- Usuario + imagen perfil -->
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <div class="avatar me-2">
                                                                    <img src="<?php echo !empty($row['img_perfil']) ? htmlspecialchars($row['img_perfil']) : '../../assets/img/avatars/1.png'; ?>" alt="Perfil" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                                                </div>
                                                                <span><?php echo htmlspecialchars($row['usuario']); ?></span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center"><?php echo htmlspecialchars($row['categorias'] ?? ''); ?></td>
                                                        <td class="text-center"><?php echo htmlspecialchars($row['dificultad']); ?></td>
                                                        <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($row['fecha_creacion'])); ?></td>
                                                        <td class="text-center">
                                                            <div class="dropdown">
                                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <?php if (in_array('detalle', $permisos_recetas)): ?>
                                                                        <a class="dropdown-item" href="vista-detalle-receta.php?id_receta=<?php echo $row['id_receta']; ?>">
                                                                            <i class='bx bx-show-alt me-2'></i> Detalle
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <?php if (in_array('editar', $permisos_recetas)): ?>
                                                                        <a class="dropdown-item" href="vista-editar-receta.php?id_receta=<?php echo $row['id_receta']; ?>">
                                                                            <i class="bi bi-pencil-square me-2"></i> Editar
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <p class="text-center">Total de recetas: <span id="total-recipes"><?php echo $total; ?></span></p>
                                        <ul class="pagination justify-content-center" id="pagination"> <?php echo $pagination_html; ?> </ul>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const searchInput = document.getElementById('search');
                                    const filterDifficulty = document.getElementById('filter-difficulty');
                                    const filterCategory = document.getElementById('filter-category');
                                    const filterFecha = document.getElementById('filter-fecha');
                                    const recipesTable = document.getElementById('recipes-table');
                                    const totalRecipes = document.getElementById('total-recipes');
                                    const pagination = document.getElementById('pagination');
                                    let currentPage = 1;
                                    // Función para cargar recetas desde el servidor 
                                    const loadRecipes = (page = 1) => {
                                        const search = searchInput.value;
                                        const difficulty = filterDifficulty.value;
                                        const category = filterCategory.value;
                                        const fecha = filterFecha.value;
                                        fetch(`?search=${encodeURIComponent(search)}&filter_difficulty=${encodeURIComponent(difficulty)}&filter_category=${encodeURIComponent(category)}&filter_fecha=${encodeURIComponent(fecha)}&page=${page}`).then(response => response.text()).then(data => {
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
                                    filterFecha.addEventListener('change', () => loadRecipes(1));
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
                            </script>



                            <?php
                            include('conexion.php');
                            // Parámetros de búsqueda y filtros para pendientes
                            $pend_search = isset($_GET['pend_search']) ? $conexion->real_escape_string($_GET['pend_search']) : '';
                            $pend_filter_difficulty = isset($_GET['pend_filter_difficulty']) ? $conexion->real_escape_string($_GET['pend_filter_difficulty']) : '';
                            $pend_filter_category = isset($_GET['pend_filter_category']) ? $conexion->real_escape_string($_GET['pend_filter_category']) : '';
                            $pend_filter_fecha = isset($_GET['pend_filter_fecha']) ? $conexion->real_escape_string($_GET['pend_filter_fecha']) : '';
                            $pend_page = isset($_GET['pend_page']) ? (int)$_GET['pend_page'] : 1;
                            $pend_limit = 8;
                            $pend_offset = ($pend_page - 1) * $pend_limit;

                            // Condiciones dinámicas
                            $pend_where = "1=1";
                            if (!empty($pend_search)) {
                                $pend_where .= " AND (r.titulo LIKE '%$pend_search%' OR u.nombre LIKE '%$pend_search%' OR c.nombre LIKE '%$pend_search%')";
                            }
                            if (!empty($pend_filter_difficulty)) {
                                $pend_where .= " AND r.dificultad = '$pend_filter_difficulty'";
                            }
                            if (!empty($pend_filter_category)) {
                                $pend_where .= " AND c.nombre = '$pend_filter_category'";
                            }

                            // Orden por fecha de subida
                            $pend_order_by = "r.fecha_creacion DESC";
                            if ($pend_filter_fecha === "antigua") {
                                $pend_order_by = "r.fecha_creacion ASC";
                            } elseif ($pend_filter_fecha === "reciente") {
                                $pend_order_by = "r.fecha_creacion DESC";
                            }

                            // Consulta para obtener recetas pendientes con filtros y paginación
                            $sql_pend = "SELECT r.titulo, u.nombre AS usuario, r.dificultad, r.fecha_creacion, r.id_receta,
                            GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias,
                            u.img AS img_perfil,
                            img.url_imagen AS receta_img
                            FROM recetas r
                            JOIN usuarios u ON r.usuario_id = u.id_usuario
                            JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                            JOIN categoria c ON rc.categoria_id = c.id_categoria
                            LEFT JOIN imagenes_recetas ir ON r.id_receta = ir.recetas_id
                            LEFT JOIN img_recetas img ON ir.img_id = img.id_img
                            WHERE $pend_where AND r.estado = 'pendiente'
                            GROUP BY r.id_receta
                            ORDER BY $pend_order_by
                            LIMIT $pend_limit OFFSET $pend_offset";
                            $result_pend = $conexion->query($sql_pend);

                            $recipes_pend = [];
                            if ($result_pend->num_rows > 0) {
                                while ($row = $result_pend->fetch_assoc()) {
                                    $recipes_pend[] = $row;
                                }
                            }

                            // Contar total de recetas pendientes para la paginación
                            $sql_total_pend = "SELECT COUNT(DISTINCT r.id_receta) AS total
                                FROM recetas r
                                JOIN usuarios u ON r.usuario_id = u.id_usuario
                                JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                                JOIN categoria c ON rc.categoria_id = c.id_categoria
                                WHERE $pend_where AND r.estado = 'pendiente'";
                            $result_total_pend = $conexion->query($sql_total_pend);
                            $total_pend = $result_total_pend->fetch_assoc()['total'];
                            $total_pages_pend = ceil($total_pend / $pend_limit);

                            // Generar enlaces de paginación
                            $pagination_html_pend = '';
                            if ($total_pages_pend > 1) {
                                for ($i = 1; $i <= $total_pages_pend; $i++) {
                                    $active_class = $i == $pend_page ? 'active' : '';
                                    $pagination_html_pend .= "<li class='page-item $active_class'><a class='page-link' href='#' data-pend-page='$i'>$i</a></li>";
                                }
                            }
                            // Consulta para obtener todas las categorías
                            $sql_categorias_pend = "SELECT nombre FROM categoria";
                            $result_categorias_pend = $conexion->query($sql_categorias_pend);
                            $categorias_options_pend = "";
                            while ($row_categorias = $result_categorias_pend->fetch_assoc()) {
                                $selected = $pend_filter_category == $row_categorias['nombre'] ? 'selected' : '';
                                $categorias_options_pend .= "<option value='{$row_categorias['nombre']}' $selected>{$row_categorias['nombre']}</option>";
                            }
                            ?>

                            <div class="col-12 order-5 mt-5">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0 me-2">Solicitudes de Recetas Pendientes</h5>
                                        </div>
                                        <div class="d-flex">
                                            <input type="text" id="pend-search" class="form-control me-2" placeholder="Buscar por título" value="<?php echo htmlspecialchars($pend_search); ?>">
                                            <select id="pend-filter-difficulty" class="form-control me-2">
                                                <option value="">Nivel de Dificultad</option>
                                                <option value="Fácil" <?php if ($pend_filter_difficulty == "Fácil") echo "selected"; ?>>Fácil</option>
                                                <option value="Intermedio" <?php if ($pend_filter_difficulty == "Intermedio") echo "selected"; ?>>Intermedio</option>
                                                <option value="Difícil" <?php if ($pend_filter_difficulty == "Difícil") echo "selected"; ?>>Difícil</option>
                                            </select>
                                            <select id="pend-filter-category" class="form-control me-2">
                                                <option value="">Categorías</option> <?php echo $categorias_options_pend; ?>
                                            </select>
                                            <select id="pend-filter-fecha" class="form-control me-2">
                                                <option value="">Fecha de subida</option>
                                                <option value="reciente" <?php if ($pend_filter_fecha == "reciente") echo "selected"; ?>>Más reciente</option>
                                                <option value="antigua" <?php if ($pend_filter_fecha == "antigua") echo "selected"; ?>>Más antigua</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-datatable table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Título</th>
                                                    <th class="text-center">Usuario</th>
                                                    <th class="text-center">Categoría</th>
                                                    <th class="text-center">Dificultad</th>
                                                    <th class="text-center">Fecha de subida</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pend-table">
                                                <?php foreach ($recipes_pend as $row): ?>
                                                    <tr>
                                                        <!-- Título + imagen receta -->
                                                        <td class="">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar me-2">
                                                                    <img src="<?php echo !empty($row['receta_img']) ? htmlspecialchars($row['receta_img']) : '../../assets/img/avatars/1.png'; ?>" alt="Receta" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                                                </div>
                                                                <span><?php echo htmlspecialchars($row['titulo']); ?></span>
                                                            </div>
                                                        </td>
                                                        <!-- Usuario + imagen perfil -->
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <div class="avatar me-2">
                                                                    <img src="<?php echo !empty($row['img_perfil']) ? htmlspecialchars($row['img_perfil']) : '../../assets/img/avatars/1.png'; ?>" alt="Perfil" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                                                </div>
                                                                <span><?php echo htmlspecialchars($row['usuario']); ?></span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center"><?php echo htmlspecialchars($row['categorias'] ?? ''); ?></td>
                                                        <td class="text-center"><?php echo htmlspecialchars($row['dificultad']); ?></td>
                                                        <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($row['fecha_creacion'])); ?></td>
                                                        <td class="text-center">
                                                            <div class="dropdown">
                                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <?php if (in_array('autorizacion', $permisos_recetas)): ?>
                                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="aprobarReceta(<?= $row['id_receta'] ?>, '<?= htmlspecialchars($row['titulo'], ENT_QUOTES) ?>')">
                                                                            <i class="bx bx-check me-2"></i> Aprobar
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <?php if (in_array('autorizacion', $permisos_recetas)): ?>
                                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="rechazarReceta(<?= $row['id_receta'] ?>, '<?= htmlspecialchars($row['titulo'], ENT_QUOTES) ?>')">
                                                                            <i class="bx bx-x me-2"></i> Rechazar
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <?php if (in_array('detalle', $permisos_recetas)): ?>
                                                                        <a class="dropdown-item" href="vista-detalle-receta.php?id_receta=<?= $row['id_receta'] ?>">
                                                                            <i class="bx bx-show-alt me-2"></i> Ver Detalle
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <p class="text-center">Total de recetas pendientes: <span id="total-pend"><?php echo $total_pend; ?></span></p>
                                        <ul class="pagination justify-content-center" id="pagination-pend"><?php echo $pagination_html_pend; ?></ul>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const pendSearch = document.getElementById('pend-search');
                                    const pendFilterDifficulty = document.getElementById('pend-filter-difficulty');
                                    const pendFilterCategory = document.getElementById('pend-filter-category');
                                    const pendFilterFecha = document.getElementById('pend-filter-fecha');
                                    const pendTable = document.getElementById('pend-table');
                                    const totalPend = document.getElementById('total-pend');
                                    const paginationPend = document.getElementById('pagination-pend');
                                    let currentPendPage = 1;

                                    const loadPend = (page = 1) => {
                                        const search = pendSearch.value;
                                        const difficulty = pendFilterDifficulty.value;
                                        const category = pendFilterCategory.value;
                                        const fecha = pendFilterFecha.value;
                                        fetch(`?pend_search=${encodeURIComponent(search)}&pend_filter_difficulty=${encodeURIComponent(difficulty)}&pend_filter_category=${encodeURIComponent(category)}&pend_filter_fecha=${encodeURIComponent(fecha)}&pend_page=${page}`)
                                            .then(response => response.text())
                                            .then(data => {
                                                const parser = new DOMParser();
                                                const doc = parser.parseFromString(data, 'text/html');
                                                pendTable.innerHTML = doc.getElementById('pend-table').innerHTML;
                                                totalPend.innerHTML = doc.getElementById('total-pend').innerHTML;
                                                paginationPend.innerHTML = doc.getElementById('pagination-pend').innerHTML;
                                            }).catch(error => {
                                                pendTable.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar las recetas pendientes</td></tr>';
                                            });
                                    };

                                    pendSearch.addEventListener('input', () => loadPend(1));
                                    pendFilterDifficulty.addEventListener('change', () => loadPend(1));
                                    pendFilterCategory.addEventListener('change', () => loadPend(1));
                                    pendFilterFecha.addEventListener('change', () => loadPend(1));
                                    paginationPend.addEventListener('click', (e) => {
                                        if (e.target.tagName === 'A' && e.target.dataset.pendPage) {
                                            e.preventDefault();
                                            const page = parseInt(e.target.dataset.pendPage);
                                            loadPend(page);
                                        }
                                    });

                                    loadPend();
                                });
                            </script>






                            <script>
                                document.addEventListener('DOMContentLoaded', () => {

                                    const pendientesTable = document.getElementById('pendientes-table');
                                    const paginationPendientes = document.getElementById('pagination-pendientes');
                                    if (paginationPendientes) {
                                        paginationPendientes.addEventListener('click', (e) => {
                                            if (e.target.tagName === 'A' && e.target.dataset.pendientesPage) {
                                                e.preventDefault();
                                                const pendientesPage = parseInt(e.target.dataset.pendientesPage);
                                                // Recargar la página con el parámetro de paginación de pendientes
                                                const url = new URL(window.location.href);
                                                url.searchParams.set('pendientes_page', pendientesPage);
                                                window.location.href = url.toString();
                                            }
                                        });
                                    }
                                });
                            </script>


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
                                                            location.reload();
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
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
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
                                                            location.reload();
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

                                function verDetalle(id_receta) {
                                    window.location.href = `vista-detalle-receta.php?id_receta=${id_receta}`;
                                }

                                // Mostrar SweetAlert basado en el parámetro de estado en la URL
                                <?php if (isset($_GET['status'])): ?>
                                    <?php if ($_GET['status'] == 'success'): ?>
                                        Swal.fire({
                                            icon: 'success',
                                            title: '¡Éxito!',
                                            text: 'La receta se ha editado correctamente.',
                                            confirmButtonText: 'Aceptar'
                                        });
                                    <?php elseif ($_GET['status'] == 'error'): ?>
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Hubo un problema al editar la receta. Por favor, inténtalo de nuevo.',
                                            confirmButtonText: 'Aceptar'
                                        });
                                    <?php endif; ?>
                                <?php endif; ?>
                            </script>





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    </div>
    <!-- / Content -->
    </div>
    <!-- Content wrapper -->
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