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




//Sql para obtener las mejores recetas segun las calificaciones 
$sql_mejores_recetas_admin = "SELECT r.id_receta, r.titulo, COALESCE(COUNT(DISTINCT l.id_like), 0) as total_likes, AVG(c.calificacion) as promedio_calificacion, COUNT(DISTINCT c.id_calificacion) as total_calificaciones
                             FROM recetas r
                             LEFT JOIN calificaciones c ON r.id_receta = c.receta_id
                             LEFT JOIN likes l ON r.id_receta = l.receta_id
                             GROUP BY r.id_receta
                             ORDER BY total_likes DESC, promedio_calificacion DESC, total_calificaciones DESC
                             LIMIT 6;";
$result_mejores_recetas_admin = $conexion->query($sql_mejores_recetas_admin);
$mejores_recetas_admin = [];
if ($result_mejores_recetas_admin->num_rows > 0) {
  while ($row = $result_mejores_recetas_admin->fetch_assoc()) {
    $mejores_recetas_admin[] = $row;
  }
}


//Sql para obtener las recetas mas guardadas
$sql_mas_guardadas = "
    SELECT r.id_receta, r.titulo, guardados.total_guardados, calificaciones.promedio_calificacion, calificaciones.total_calificaciones
    FROM recetas r
    LEFT JOIN (
        SELECT receta_id, COUNT(*) AS total_guardados
        FROM recetas_favoritas
        GROUP BY receta_id
    ) guardados ON r.id_receta = guardados.receta_id
    LEFT JOIN (
        SELECT receta_id, AVG(calificacion) AS promedio_calificacion, COUNT(*) AS total_calificaciones
        FROM calificaciones
        GROUP BY receta_id
    ) calificaciones ON r.id_receta = calificaciones.receta_id
    ORDER BY guardados.total_guardados DESC, calificaciones.promedio_calificacion DESC, calificaciones.total_calificaciones DESC
    LIMIT 6;
";
$result_mas_guardadas = $conexion->query($sql_mas_guardadas);
$mas_guardadas = [];
if ($result_mas_guardadas->num_rows > 0) {
  while ($row = $result_mas_guardadas->fetch_assoc()) {
    $mas_guardadas[] = $row;
  }
}


//Sql para saber los usuarios que mas subieron recetas
$sql_usuarios_mas_recetas = "
    SELECT u.id_usuario, u.nombre_usuario, COUNT(r.id_receta) as total_recetas
    FROM usuarios u
    JOIN recetas r ON u.id_usuario = r.usuario_id
    GROUP BY u.id_usuario
    ORDER BY total_recetas DESC
    LIMIT 6;
";
$result_usuarios_mas_recetas = $conexion->query($sql_usuarios_mas_recetas);
$usuarios_mas_recetas = [];
if ($result_usuarios_mas_recetas->num_rows > 0) {
  while ($row = $result_usuarios_mas_recetas->fetch_assoc()) {
    $usuarios_mas_recetas[] = $row;
  }
}


//Para calcular la popularidad de cada categoria
//colores para la categoria

$colores = [
  'bg-primary',
  'bg-success',
  'bg-info',
  'bg-warning',
  'bg-danger',
  'bg-secondary',
  'bg-dark',
];

$sql_popularidad = "
    SELECT cat.nombre, AVG(cal.calificacion) AS popularidad
    FROM categoria cat
    JOIN recetas_categorias rc ON cat.id_categoria = rc.categoria_id
    JOIN recetas r ON rc.receta_id = r.id_receta
    LEFT JOIN calificaciones cal ON r.id_receta = cal.receta_id
    GROUP BY cat.id_categoria
";
$result_popularidad = $conexion->query($sql_popularidad);

$categorias = [];
if ($result_popularidad->num_rows > 0) {
  while ($row = $result_popularidad->fetch_assoc()) {
    $categorias[] = $row;
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

  <title>Inicio</title>

  <meta name="description" content="" />

  <!-- Favicon -->

  <link rel="icon" type="image/x-icon" href="../../VistaCliente/img/chefclassFinal.png" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />

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
</head>

<style>
  .btn-primary {
    color: #fff;
    background-color: #ff6426;
    border-color: #ff6426;
    box-shadow: 0 .125rem .25rem 0 rgba(105, 108, 255, .4)
  }

  .btn-primary:hover {
    color: #fff;
    background-color: rgb(255, 72, 0);
    border-color: rgb(255, 72, 0);
    transform: translateY(-1px);
  }
</style>

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
          <li class="menu-item active">
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

          // Mostrar solo las secciones permitidas para el rol
          foreach ($secciones_menu as $id => $info) {
            if (in_array($id, $secciones_permitidas)) {
          ?>
              <li class="menu-item">
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

        <!-- //!Content wrapper MAIN MENÚ -->
        <div class="content-wrapper">
          <!-- Content -->
          <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
          <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
          <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">

              <div class="row">
                <div class="col-lg-7 mb-4 order-0">
                  <div class="card" style="height: 180px;">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h5 class="card-title text-primary">Bienvenido <?php echo $Nombre, ' ', $Apellido ?>! 🎉</h5>
                          <p class="mb-3">
                            Este es tú espacio de trabajo, en el que podrás
                            realizar tus diferentes tareas.
                          </p>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                          <img src="../assets/img/illustrations/man-with-laptop-light.png" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-5 col-md-12 col-6 mb-4 d-flex align-items-stretch">
                  <a href="../../VistaCliente/html/index.php" class="card-link w-100">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                          <h4 class="fw-semibold d-block mb-1">Ir Al Sitio Web</h4>
                          <div class="alert alert-primary d-flex align-items-center mb-0 py-1 px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                              fill="currentColor" viewBox="0 0 24 24">
                              <path d="m10 17 6-5-6-5v4H3v2h7z"></path>
                              <path d="M19 3h-7v2h7v14h-7v2h7c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2"></path>
                            </svg>
                          </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Este es el un link que te permitirá acceder al sitio web Chefclass. </span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>

              <!-- Agrupar los 4 bloques en una sola fila -->
              <div class="row">
                <!-- Popularidad por categoría -->
                <div class="col-12 col-md-6 col-xl-3 mb-4 d-flex align-items-stretch">
                  <div class="card h-100 w-100">
                    <div class="card-header">
                      <div class="card-title mb-0">
                        <h5 class="m-0">Popularidad por categoría</h5>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="d-none d-lg-flex vehicles-progress-labels mb-3">
                        <?php foreach ($categorias as $index => $categoria): ?>
                          <div class="vehicles-progress-label" style="width: <?= round($categoria['popularidad'], 1) ?>%;"></div>
                        <?php endforeach; ?>
                      </div>
                      <div class="vehicles-overview-progress progress rounded-2 mb-3" style="height: 46px;">
                        <?php foreach ($categorias as $index => $categoria): ?>
                          <div class="progress-bar fs-big fw-medium text-start <?= $colores[$index % count($colores)] ?> px-1 px-lg-3 <?= $index == 0 ? 'rounded-start' : ($index == count($categorias) - 1 ? 'rounded-end' : '') ?>" role="progressbar" style="width: <?= round($categoria['popularidad'], 1) ?>%;" aria-valuenow="<?= round($categoria['popularidad'], 1) ?>" aria-valuemin="0" aria-valuemax="100"><?= round($categoria['popularidad'], 1) ?>%</div>
                        <?php endforeach; ?>
                      </div>
                      <div class="table-responsive">
                        <table class="table card-table">
                          <tbody class="table-border-bottom-0">
                            <?php foreach ($categorias as $index => $categoria): ?>
                              <tr>
                                <td class="w-50 ps-0">
                                  <div class="d-flex justify-content-start align-items-center">
                                    <div class="me-2">
                                      <div class="<?= $colores[$index % count($colores)] ?>" style="width: 16px; height: 16px; background-color: currentColor; border-radius: 3px;"></div>
                                    </div>
                                    <h6 class="mb-0 fw-normal"><?= htmlspecialchars($categoria['nombre']) ?></h6>
                                  </div>
                                </td>
                                <td class="text-end pe-0 text-nowrap">
                                  <h6 class="mb-0"><?= round($categoria['popularidad'], 1) ?>%</h6>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Mejores recetas -->
                <div class="col-12 col-md-6 col-xl-3 mb-4 d-flex align-items-stretch">
                  <div class="card h-100 w-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Mejores recetas</h5>
                      </div>
                    </div>
                    <div class="card-body">
                      <ul class="p-0 m-0">
                        <?php
                        $colores = ['bg-label-primary', 'bg-label-info', 'bg-label-success', 'bg-label-warning', 'bg-label-danger', 'bg-label-secondary'];
                        foreach ($mejores_recetas_admin as $index => $receta): ?>
                          <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                              <span class="avatar-initial rounded <?= $colores[$index % count($colores)] ?>"><i class='bx bx-heart'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-1 fw-normal"><?= htmlspecialchars($receta['titulo']) ?></h6>
                              </div>
                              <div class="user-progress">
                                <h6 class="mb-0"><?= htmlspecialchars($receta['total_likes']) ?> </h6>
                              </div>
                            </div>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Recetas más guardadas -->
                <div class="col-12 col-md-6 col-xl-3 mb-4 d-flex align-items-stretch">
                  <div class="card h-100 w-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Recetas más guardadas</h5>
                      </div>
                    </div>
                    <div class="card-body">
                      <ul class="p-0 m-0">
                        <?php
                        $colores = ['bg-label-primary', 'bg-label-info', 'bg-label-success', 'bg-label-warning', 'bg-label-danger', 'bg-label-secondary'];
                        foreach ($mas_guardadas as $index => $receta): ?>
                          <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                              <span class="avatar-initial rounded <?= $colores[$index % count($colores)] ?>"><i class='bx  bx-bookmark'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-1 fw-normal"><?= htmlspecialchars($receta['titulo']) ?></h6>
                                <small class="text-muted">Guardados: <?= htmlspecialchars($receta['total_guardados']) ?></small>
                              </div>
                            </div>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Usuarios más activos -->
                <div class="col-12 col-md-6 col-xl-3 mb-4 d-flex align-items-stretch">
                  <div class="card h-100 w-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Usuarios más activos</h5>
                      </div>
                    </div>
                    <div class="card-body">
                      <ul class="p-0 m-0">
                        <?php
                        $colores = ['bg-label-primary', 'bg-label-info', 'bg-label-success', 'bg-label-warning', 'bg-label-danger', 'bg-label-secondary'];
                        foreach ($usuarios_mas_recetas as $index => $usuario): ?>
                          <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                              <span class="avatar-initial rounded <?= $colores[$index % count($colores)] ?>"><i class='bx bx-user'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-1 fw-normal"><?= htmlspecialchars($usuario['nombre_usuario']) ?></h6>
                              </div>
                              <div class="user-progress">
                                <h6 class="mb-0"><i class='bx bx-restaurant'></i> <?= htmlspecialchars($usuario['total_recetas']) ?></h6>
                              </div>
                            </div>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ! -->
            </div>
          </div>
          <!-- / Content -->





          <div class="content-backdrop fade"></div>
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