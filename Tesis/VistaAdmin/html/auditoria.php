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
} else {
    header("Location: Login.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>


    <!-- ... Para las alertas (SwettAlert) ... -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Auditoría</title>

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
                include("conexion.php");

                // Número de registros por página
                $registrosPorPagina = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $offset = ($pagina - 1) * $registrosPorPagina;

                // Filtros
                $usuarioFiltro = isset($_GET['usuario']) ? $_GET['usuario'] : '';
                $accionFiltro = isset($_GET['accion']) ? $_GET['accion'] : '';
                $ordenFiltro = isset($_GET['orden']) ? $_GET['orden'] : 'desc';
                $fechaFiltro = isset($_GET['fecha']) ? $_GET['fecha'] : '';
                $detalleFiltro = isset($_GET['detalle']) ? $_GET['detalle'] : '';

                // Consulta total de registros
                $totalQuery = "SELECT COUNT(*) as total FROM historial_usuarios WHERE 1=1";
                if ($usuarioFiltro != '') {
                    $totalQuery .= " AND ID_Usuario = '" . mysqli_real_escape_string($conexion, $usuarioFiltro) . "'";
                }
                if ($accionFiltro != '') {
                    $totalQuery .= " AND Accion = '" . mysqli_real_escape_string($conexion, $accionFiltro) . "'";
                }
                if ($fechaFiltro != '') {
                    $totalQuery .= " AND DATE(Fecha) = '" . mysqli_real_escape_string($conexion, $fechaFiltro) . "'";
                }
                if ($detalleFiltro != '') {
                    $totalQuery .= " AND Detalles LIKE '%" . mysqli_real_escape_string($conexion, $detalleFiltro) . "%'";
                }
                $totalResult = mysqli_query($conexion, $totalQuery);
                $totalRow = mysqli_fetch_assoc($totalResult);
                $totalRegistros = $totalRow['total'];

                // Consulta para obtener los registros
                $query = "SELECT historial_usuarios.*, CONCAT(usuarios.nombre, ' ', usuarios.apellido) AS nombre_completo 
                    FROM historial_usuarios 
                    JOIN usuarios ON historial_usuarios.ID_Usuario = usuarios.ID_Usuario
                    WHERE 1=1";
                if ($usuarioFiltro != '') {
                    $query .= " AND historial_usuarios.ID_Usuario = '" . mysqli_real_escape_string($conexion, $usuarioFiltro) . "'";
                }
                if ($accionFiltro != '') {
                    $query .= " AND historial_usuarios.Accion = '" . mysqli_real_escape_string($conexion, $accionFiltro) . "'";
                }
                if ($fechaFiltro != '') {
                    $query .= " AND DATE(historial_usuarios.Fecha) = '" . mysqli_real_escape_string($conexion, $fechaFiltro) . "'";
                }
                if ($detalleFiltro != '') {
                    $query .= " AND historial_usuarios.Detalles LIKE '%" . mysqli_real_escape_string($conexion, $detalleFiltro) . "%'";
                }
                $query .= " ORDER BY historial_usuarios.Fecha " . ($ordenFiltro == 'asc' ? 'ASC' : 'DESC');
                $query .= " LIMIT $offset, $registrosPorPagina";
                $result = mysqli_query($conexion, $query);

                // Obtener todos los usuarios para el filtro
                $usuariosQuery = "SELECT DISTINCT usuarios.ID_Usuario, CONCAT(usuarios.nombre, ' ', usuarios.apellido) AS nombre_completo 
                  FROM historial_usuarios 
                  JOIN usuarios ON historial_usuarios.ID_Usuario = usuarios.ID_Usuario";
                $usuariosResult = mysqli_query($conexion, $usuariosQuery);

                // Obtener todas las acciones para el filtro
                $accionesQuery = "SELECT DISTINCT Accion FROM historial_usuarios";
                $accionesResult = mysqli_query($conexion, $accionesQuery);
                ?>

                <!-- //!Content wrapper MAIN MENÚ -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Administración /</span> Auditoría
                        </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <!-- Filtros -->
                                    <div class="card-body row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label for="usuarioFiltro" class="form-label">Usuario</label>
                                            <select id="usuarioFiltro" class="form-select" name="usuarioFiltro">
                                                <option value="">Todos los usuarios</option>
                                                <?php while ($usuario = mysqli_fetch_assoc($usuariosResult)) { ?>
                                                    <option value="<?php echo $usuario['ID_Usuario']; ?>" <?php if ($usuarioFiltro == $usuario['ID_Usuario']) echo 'selected'; ?>>
                                                        <?php echo $usuario['nombre_completo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="accionFiltro" class="form-label">Acción</label>
                                            <select id="accionFiltro" class="form-select" name="accionFiltro">
                                                <option value="">Todas</option>
                                                <?php while ($accion = mysqli_fetch_assoc($accionesResult)) { ?>
                                                    <option value="<?php echo $accion['Accion']; ?>" <?php if ($accionFiltro == $accion['Accion']) echo 'selected'; ?>>
                                                        <?php echo ucfirst($accion['Accion']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="ordenFiltro" class="form-label">Orden</label>
                                            <select id="ordenFiltro" class="form-select" name="ordenFiltro">
                                                <option value="desc" <?php if ($ordenFiltro == 'desc') echo 'selected'; ?>>Más reciente</option>
                                                <option value="asc" <?php if ($ordenFiltro == 'asc') echo 'selected'; ?>>Más antiguo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="fechaFiltro" class="form-label">Fecha</label>
                                            <input type="date" id="fechaFiltro" class="form-control" value="<?php echo htmlspecialchars($fechaFiltro); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="detalleBuscar" class="form-label">Detalle</label>
                                            <div class="input-group">
                                                <input type="text" id="detalleBuscar" class="form-control" placeholder="Buscar por detalle" value="<?php echo htmlspecialchars($detalleFiltro); ?>">
                                                <button class="btn btn-primary" type="button" onclick="filtrarAuditorias()">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($usuarioFiltro != '' || $accionFiltro != '' || $ordenFiltro != 'desc' || $fechaFiltro != '' || $detalleFiltro != ''): ?>
                                                    <button class="btn btn-secondary" type="button" onclick="resetFiltros()">Restablecer</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <!-- Tabla de Auditorías -->
                                    <div class="table-responsive">
                                        <table class="table table-striped table-borderless border-bottom">
                                            <thead>
                                                <tr>
                                                    <th style="font-size: 1.15rem;">Usuario</th>
                                                    <th style="font-size: 1.15rem;">Acción</th>
                                                    <th style="font-size: 1.15rem;">Detalle</th>
                                                    <th style="font-size: 1.15rem;">Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($auditoria = mysqli_fetch_assoc($result)) { ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($auditoria['nombre_completo']); ?></td>
                                                        <td><?php echo htmlspecialchars($auditoria['Accion']); ?></td>
                                                        <td><?php echo htmlspecialchars($auditoria['Detalles']); ?></td>
                                                        <td><?php echo htmlspecialchars($auditoria['Fecha']); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Paginación estilo imagen -->
                                    <div class="card-body">
                                        <nav aria-label="Paginación">
                                            <ul class="pagination justify-content-center">
                                                <?php
                                                $totalPaginas = max(1, ceil($totalRegistros / $registrosPorPagina));
                                                $mostrarPaginas = 5;
                                                $inicio = max(1, $pagina - 2);
                                                $fin = min($totalPaginas, $inicio + $mostrarPaginas - 1);
                                                if ($inicio > 1) {
                                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                }
                                                // Botón anterior
                                                echo '<li class="page-item' . ($pagina <= 1 ? ' disabled' : '') . '">
                                                    <a class="page-link" href="' . ($pagina > 1 ? '?pagina=' . ($pagina - 1) . '&usuario=' . urlencode($usuarioFiltro) . '&accion=' . urlencode($accionFiltro) . '&orden=' . urlencode($ordenFiltro) . '&fecha=' . urlencode($fechaFiltro) . '&detalle=' . urlencode($detalleFiltro) : '#') . '">&lt;</a>
                                                </li>';
                                                for ($i = $inicio; $i <= $fin; $i++) {
                                                    echo '<li class="page-item' . ($i == $pagina ? ' active' : '') . '">
                                                        <a class="page-link" href="?pagina=' . $i . '&usuario=' . urlencode($usuarioFiltro) . '&accion=' . urlencode($accionFiltro) . '&orden=' . urlencode($ordenFiltro) . '&fecha=' . urlencode($fechaFiltro) . '&detalle=' . urlencode($detalleFiltro) . '">' . $i . '</a>
                                                    </li>';
                                                }
                                                if ($fin < $totalPaginas) {
                                                    if ($fin < $totalPaginas - 1) {
                                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                    }
                                                    echo '<li class="page-item">
                                                        <a class="page-link" href="?pagina=' . $totalPaginas . '&usuario=' . urlencode($usuarioFiltro) . '&accion=' . urlencode($accionFiltro) . '&orden=' . urlencode($ordenFiltro) . '&fecha=' . urlencode($fechaFiltro) . '&detalle=' . urlencode($detalleFiltro) . '">' . $totalPaginas . '</a>
                                                    </li>';
                                                }
                                                // Botón siguiente
                                                echo '<li class="page-item' . ($pagina >= $totalPaginas ? ' disabled' : '') . '">
                                                    <a class="page-link" href="' . ($pagina < $totalPaginas ? '?pagina=' . ($pagina + 1) . '&usuario=' . urlencode($usuarioFiltro) . '&accion=' . urlencode($accionFiltro) . '&orden=' . urlencode($ordenFiltro) . '&fecha=' . urlencode($fechaFiltro) . '&detalle=' . urlencode($detalleFiltro) : '#') . '">&gt;</a>
                                                </li>';
                                                ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <script>
                                    function filtrarAuditorias() {
                                        const usuarioFiltro = document.getElementById('usuarioFiltro').value;
                                        const accionFiltro = document.getElementById('accionFiltro').value;
                                        const ordenFiltro = document.getElementById('ordenFiltro').value;
                                        const fechaFiltro = document.getElementById('fechaFiltro').value;
                                        const detalleFiltro = document.getElementById('detalleBuscar').value;
                                        let params = [];
                                        if (usuarioFiltro) params.push('usuario=' + encodeURIComponent(usuarioFiltro));
                                        if (accionFiltro) params.push('accion=' + encodeURIComponent(accionFiltro));
                                        if (ordenFiltro) params.push('orden=' + encodeURIComponent(ordenFiltro));
                                        if (fechaFiltro) params.push('fecha=' + encodeURIComponent(fechaFiltro));
                                        if (detalleFiltro) params.push('detalle=' + encodeURIComponent(detalleFiltro));
                                        window.location.href = '?' + params.join('&');
                                    }
                                    function resetFiltros() {
                                        window.location.href = 'auditoria.php';
                                    }
                                    // Permitir buscar con Enter en los campos de filtro
                                    document.getElementById('detalleBuscar').addEventListener('keydown', function(e) {
                                        if (e.key === 'Enter') filtrarAuditorias();
                                    });
                                    document.getElementById('fechaFiltro').addEventListener('keydown', function(e) {
                                        if (e.key === 'Enter') filtrarAuditorias();
                                    });
                                    document.getElementById('usuarioFiltro').addEventListener('change', filtrarAuditorias);
                                    document.getElementById('accionFiltro').addEventListener('change', filtrarAuditorias);
                                    document.getElementById('ordenFiltro').addEventListener('change', filtrarAuditorias);
                                </script>
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

    <script>
        function filtrarAuditorias() {
            const usuarioFiltro = document.getElementById('usuarioFiltro').value;
            const detalleFiltro = document.getElementById('detalleBuscar').value;

            if (usuarioFiltro === '' && detalleFiltro === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Debe ingresar un detalle o seleccionar un filtro de usuario.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                window.location.href = `?usuario=${usuarioFiltro}&detalle=${detalleFiltro}`;
            }
        }

        function resetFiltros() {
            window.location.href = 'auditoria.php';
        }
    </script>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados',
                text: 'No se encontraron auditorías que coincidan con los criterios de búsqueda.',
                confirmButtonText: 'Aceptar'
            });
        </script>
    <?php endif; ?>

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