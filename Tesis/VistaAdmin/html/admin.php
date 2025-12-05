<?php
session_start();
include("conexion.php");
// Verificar si las variables de sesión están definidas antes de acceder a ellas
// Iniciar sesión para obtener datos del usuario logueado
if (isset($_SESSION['id_usuario'])) {
  $ID_Usuario = $_SESSION['id_usuario'];
  $Nombre = $_SESSION['nombre'];
  $Apellido = $_SESSION['apellido'];

  // Traer el id_rol y nombre_rol del usuario (usa la columna correcta: "rol")
  $sql = "SELECT r.id_rol, r.nombre_rol FROM usuarios u 
  JOIN roles r ON u.rol = r.id_rol /* Une con tabla roles donde coincida el ID del rol */
  WHERE u.id_usuario = $ID_Usuario"; /* filtramos por el usuario logeado */

  // ejecutamos la consulta sql
  $result = $conexion->query($sql);
  $row = $result->fetch_assoc();
  $Rol = $row['nombre_rol'];
  $RolId = $row['id_rol'];

  // Traer las secciones permitidas para ese rol
  $sql_permisos = "SELECT id_seccion FROM roles_permisos_secciones WHERE id_rol = $RolId"; //Busca todas las secciones a las que tiene acceso este rol
  $result_permisos = $conexion->query($sql_permisos);
  $secciones_permitidas = [];
  while ($row_permiso = $result_permisos->fetch_assoc()) {
    // almacena las secciones permitidas
    $secciones_permitidas[] = $row_permiso['id_seccion'];
  }
} else {
  header("Location: Login.php");
  exit();
}




//Sql para obtener las mejores 6 recetas segun las calificaciones 
$sql_mejores_recetas_admin = "SELECT 
    r.id_receta, 
    r.titulo, 
    AVG(c.calificacion) AS promedio_calificacion, 
    COUNT(c.id_calificacion) AS total_calificaciones
FROM recetas r
LEFT JOIN calificaciones c ON r.id_receta = c.receta_id
WHERE r.estado = 'habilitado'
GROUP BY r.id_receta
HAVING total_calificaciones > 0 /* Excluye las recetas si votos */
ORDER BY promedio_calificacion DESC, total_calificaciones DESC /* cantidad de votos para desempate */
LIMIT 6;";
$result_mejores_recetas_admin = $conexion->query($sql_mejores_recetas_admin);
$mejores_recetas_admin = [];
if ($result_mejores_recetas_admin->num_rows > 0) {
  while ($row = $result_mejores_recetas_admin->fetch_assoc()) {
    $mejores_recetas_admin[] = $row;
  }
}


//Sql para obtener las recetas mas guardadas
/* 
guardados.total_guardados - Cuántas veces se guardó como favorita

calificaciones.promedio_calificacion - Puntuación promedio

calificaciones.total_calificaciones - Total de votos

condición de unión entre la tabla principal y la subconsulta
*/
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
     WHERE r.estado = 'habilitado'
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
    SELECT u.id_usuario, u.nombre_usuario, r.estado, COUNT(r.id_receta) as total_recetas
    FROM usuarios u
    JOIN recetas r ON u.id_usuario = r.usuario_id
    WHERE r.estado = 'habilitado'
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


/* 
Descubrir qué categorías son más populares

Filtra categorías que si tienen calificaciones

AVG(cal.calificacion) - Promedio de todas las calificaciones de esa categoría

COUNT(cal.id_calificacion) - Total de votos en esa categoría
*/
$sql_popularidad = "
    SELECT 
        cat.nombre, 
        AVG(cal.calificacion) AS popularidad,
        COUNT(cal.id_calificacion) AS total_calificaciones
    FROM categoria cat
    JOIN recetas_categorias rc ON cat.id_categoria = rc.categoria_id
    JOIN recetas r ON rc.receta_id = r.id_receta
    LEFT JOIN calificaciones cal ON r.id_receta = cal.receta_id
    GROUP BY cat.id_categoria
    HAVING popularidad IS NOT NULL
    ORDER BY popularidad DESC
";
$result_popularidad = $conexion->query($sql_popularidad);

$categorias = [];
if ($result_popularidad->num_rows > 0) {
  while ($row = $result_popularidad->fetch_assoc()) {
    $categorias[] = $row;
  }
}

// ?==============================================
// ?DISTRIBUCIÓN DE RECETAS POR CATEGORÍA
// ?==============================================
/**
 * CONSULTA SQL PARA OBTENER DISTRIBUCIÓN DE RECETAS POR CATEGORÍA
 * 
 * Esta consulta calcula:
 * - Cuántas recetas hay en cada categoría
 * - El porcentaje que representa cada categoría del total
 * - Ordena por las categorías con más recetas
 * - Limita a las 6 categorías principales
 */
// Sql para obtener la distribución de recetas por categoría
$sql_distribucion_categorias = "
    SELECT 
        cat.nombre,
        COUNT(rc.receta_id) AS total_recetas,
        ROUND((COUNT(rc.receta_id) * 100.0 / (SELECT COUNT(*) FROM recetas WHERE estado = 'habilitado')), 1) AS porcentaje
    FROM categoria cat
    JOIN recetas_categorias rc ON cat.id_categoria = rc.categoria_id
    JOIN recetas r ON rc.receta_id = r.id_receta
    WHERE r.estado = 'habilitado'
    AND cat.estado = 'habilitado'
    GROUP BY cat.id_categoria
    ORDER BY total_recetas DESC
    LIMIT 6
";

$result_distribucion = $conexion->query($sql_distribucion_categorias);
// Este array contendrá todas las categorías con sus estadísticas
$distribucion_categorias = [];
if ($result_distribucion->num_rows > 0) {
  //BUCLE PARA RECORRER TODAS LAS FILAS DEL RESULTADO
  while ($row = $result_distribucion->fetch_assoc()) {
    $distribucion_categorias[] = $row;
  }
}

// Colores para las barras y puntitos
$colores_barras = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-secondary'];
$colores_puntos = ['text-primary', 'text-success', 'text-warning', 'text-danger', 'text-info', 'text-secondary'];

// Dividir categorías para las dos columnas de puntitos

$mitad = ceil(count($distribucion_categorias) / 2);  // Calcula la mitad redondeando hacia arriba
$columna1 = array_slice($distribucion_categorias, 0, $mitad);    // Primera mitad de categorías
$columna2 = array_slice($distribucion_categorias, $mitad);       // Segunda mitad de categorías

// *==============================================
// *DISTRIBUCIÓN DE RECETAS POR DIFICULTAD
// *==============================================
/**
 * CONSULTA SQL PARA OBTENER ESTADÍSTICAS DE DIFICULTAD DE RECETAS
 * 
 * Esta consulta calcula:
 * - Cuántas recetas hay por cada nivel de dificultad
 * - El porcentaje que representa cada dificultad del total
 * - Ordena los resultados en un orden específico
 */
// Consulta para obtener estadísticas de dificultad de recetas
$sql_dificultad = "SELECT 
                    dificultad, 
                    COUNT(*) as total,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM recetas WHERE estado = 'habilitado')), 2) as porcentaje
                   FROM recetas 
                   WHERE estado = 'habilitado' 
                   GROUP BY dificultad 
                   ORDER BY FIELD(dificultad, 'Fácil', 'Intermedio', 'Difícil')";

$result_dificultad = $conexion->query($sql_dificultad);

$dificultades = [];   // Almacenará los nombres de las dificultades: ['Fácil', 'Intermedio', 'Difícil']
$totales = [];        // Almacenará los conteos: [6, 3, 0] (ejemplo: 6 fáciles, 3 intermedias)
$porcentajes = [];    // Almacenará los porcentajes: [66.67, 33.33, 0] (ejemplo)
$colores = ['#4CAF50', '#FF9800', '#F44336']; // Verde, Naranja, Rojo - Colores para el gráfico

/**
 * PROCESAMIENTO DE LOS RESULTADOS
 * 
 * while() recorre cada fila del resultado de la consulta
 * $row representa una fila con los datos de cada dificultad
 */
while ($row = $result_dificultad->fetch_assoc()) {
  // Añade el nombre de la dificultad al array $dificultades
  $dificultades[] = $row['dificultad'];
  
  // Añade el total de recetas de esta dificultad al array $totales
  $totales[] = $row['total'];
  
  // Añade el porcentaje calculado al array $porcentajes
  $porcentajes[] = $row['porcentaje'];
}


// !==============================================
// !MAPA DE CALOR - UBICACIONES DE USUARIOS
// !==============================================

/**
 * CONSULTA PRINCIPAL PARA OBTENER DATOS GEOGRÁFICOS DE USUARIOS
 * 
 * Esta consulta realiza:
 * 1. JOIN entre localidades y usuarios para obtener ubicaciones
 * 2. Cuenta usuarios por localidad usando COUNT()
 * 3. Filtra solo usuarios habilitados
 * 4. Agrupa por localidad para evitar duplicados
 * 5. HAVING excluye localidades sin usuarios
 */
$sql_mapa_usuarios = "SELECT 
    l.latitud, 
    l.longitud, 
    l.provincia,
    l.localidad,
    COUNT(u.id_usuario) as total_usuarios
FROM localidades l
JOIN usuarios u ON l.id_localidad = u.id_localidad
WHERE u.estado = 'habilitado'
GROUP BY l.id_localidad
HAVING total_usuarios > 0";

// Ejecutar consulta y almacenar resultado
$result_mapa_usuarios = $conexion->query($sql_mapa_usuarios);

// Array para almacenar todas las ubicaciones procesadas
$ubicaciones_usuarios = [];

/**
 * PROCESAMIENTO DE RESULTADOS
 * 
 * Recorre cada fila del resultado y estructura los datos para el mapa
 * Cada ubicación contiene:
 * - Coordenadas (lat, lng) para posicionamiento en mapa
 * - Información geográfica (provincia, localidad)
 * - Cantidad de usuarios para calcular intensidad del calor
 */
if ($result_mapa_usuarios && $result_mapa_usuarios->num_rows > 0) {
  while ($row = $result_mapa_usuarios->fetch_assoc()) {
    $ubicaciones_usuarios[] = [
      'lat' => (float)$row['latitud'],           // Convertir a float para precisión
      'lng' => (float)$row['longitud'],          // Convertir a float para precisión
      'provincia' => $row['provincia'],          // Nombre de provincia
      'localidad' => $row['localidad'],          // Nombre de localidad
      'total_usuarios' => (int)$row['total_usuarios'] // Cantidad de usuarios (para intensidad)
    ];
  }
}
// Si no hay resultados, el array permanece vacío y el mapa se inicializa sin datos
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
  <!-- <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" /> -->

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
          //Para cada elemento del array $secciones_menu, toma la key y guárdala en $id, y toma el valor y guárdalo en $info
          foreach ($secciones_menu as $id => $info) {
            /* ¿El ID de esta sección del menú está dentro del array de secciones permitidas para el usuario? */
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
                        <div class="shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg>
                          </div>
                        </div>
                        <div class="grow">
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
          <div class="container-xxl grow container-p-y">
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

                <!-- Promedio de calificación por categoría (tipo mejores recetas) -->
                <div class="col-12 col-md-6 col-xl-3 mb-4 d-flex align-items-stretch">
                  <div class="card h-100 w-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Categorías mejor valoradas</h5>
                      </div>
                    </div>
                    <div class="card-body">
                      <ul class="p-0 m-0">
                        <?php
                        $colores = ['bg-label-primary', 'bg-label-info', 'bg-label-success', 'bg-label-warning', 'bg-label-danger', 'bg-label-secondary'];
                        foreach ($categorias as $index => $categoria): ?>
                          <li class="d-flex mb-4 pb-1">
                            <div class="avatar shrink-0 me-3">
                              <span class="avatar-initial rounded <?= $colores[$index % count($colores)] ?>"><i class='bx bx-category'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-1 fw-normal"><?= htmlspecialchars($categoria['nombre']) ?></h6>

                                <div class="text-muted small mt-1">
                                  ⭐ <?= number_format($categoria['popularidad'], 2) ?> (<?= $categoria['total_calificaciones'] ?> calificaciones)
                                </div>
                              </div>
                            </div>
                          </li>
                        <?php endforeach; ?>
                      </ul>
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
                            <div class="avatar shrink-0 me-3">
                              <span class="avatar-initial rounded <?= $colores[$index % count($colores)] ?>"><i class='bx bx-star'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-1 fw-normal"><?= htmlspecialchars($receta['titulo']) ?></h6>
                                <div class="text-muted small mt-1">
                                  ⭐ <?= number_format($receta['promedio_calificacion'], 2) ?> (<?= $receta['total_calificaciones'] ?> calificaciones)
                                </div>
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
                            <div class="avatar shrink-0 me-3">
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
                            <div class="avatar shrink-0 me-3">
                              <span class="avatar-initial rounded <?= $colores[$index % count($colores)] ?>"><i class='bx bx-user'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-1 fw-normal"><?= htmlspecialchars($usuario['nombre_usuario']) ?></h6>
                                <div class="text-muted small mt-1">
                                  <i class='bx bx-restaurant'></i> <?= htmlspecialchars($usuario['total_recetas']) ?>
                                </div>
                              </div>
                            </div>
                          </li>
                        <?php endforeach; ?>

                      </ul>
                    </div>
                  </div>
                </div>

                <!-- ============================================== -->
                <!-- //TODO -- DISTRIBUCIÓN POR CATEGORÍA -->
                <style>
                  .distribution-bars .progress {
                    background-color: #f5f5f5;
                    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
                  }

                  .distribution-bars .progress-bar {
                    transition: width 0.6s ease;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                  }
                </style>
                
                <div class="col-12 col-xl-6 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h4 class="card-title m-0 me-2">Distribución por Categoría</h4>
                    </div>
                    <div class="card-body row g-3">
                      <!-- Gráfico de barras con PHP -->
                      <div class="col-md-6">
                        <?php if (!empty($distribucion_categorias)): ?>
                          <div class="distribution-bars">
                            <?php foreach ($distribucion_categorias as $index => $categoria):
                              $color_class = $colores_barras[$index % count($colores_barras)];
                            ?>
                              <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                  <span class="fw-semibold"><?php echo htmlspecialchars($categoria['nombre']); ?></span>
                                  <span class="text-muted"><?php echo $categoria['porcentaje']; ?>%</span>
                                </div>
                                <div class="progress" style="height: 20px; border-radius: 10px;">
                                  <div class="progress-bar <?php echo $color_class; ?>"
                                    role="progressbar"
                                    style="width: <?php echo $categoria['porcentaje']; ?>%; border-radius: 10px;"
                                    aria-valuenow="<?php echo $categoria['porcentaje']; ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                  </div>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          </div>
                        <?php else: ?>
                          <div class="text-center py-4">
                            <p class="text-muted">No hay datos de categorías disponibles.</p>
                          </div>
                        <?php endif; ?>
                      </div>

                      <!-- Leyenda con puntitos -->
                      <div class="col-md-6 d-flex justify-content-around align-items-center">
                        <!-- Columna 1 -->
                        <div>
                          <?php foreach ($columna1 as $index => $categoria):
                            $color_class = $colores_puntos[$index % count($colores_puntos)];
                          ?>
                            <div class="d-flex align-items-baseline mb-3">
                              <span class="<?php echo $color_class; ?> me-2"><i class='bx bxs-circle'></i></span>
                              <div>
                                <p class="mb-1"><?php echo htmlspecialchars($categoria['nombre']); ?></p>
                                <h6 class="mb-0"><?php echo $categoria['porcentaje']; ?>%</h6>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>

                        <!-- Columna 2 -->
                        <div>
                          <?php foreach ($columna2 as $index => $categoria):
                            $color_index = $index + count($columna1);
                            $color_class = $colores_puntos[$color_index % count($colores_puntos)];
                          ?>
                            <div class="d-flex align-items-baseline mb-3">
                              <span class="<?php echo $color_class; ?> me-2"><i class='bx bxs-circle'></i></span>
                              <div>
                                <p class="mb-1"><?php echo htmlspecialchars($categoria['nombre']); ?></p>
                                <h6 class="mb-0"><?php echo $categoria['porcentaje']; ?>%</h6>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <script>
                  // Animación suave
                  document.addEventListener('DOMContentLoaded', function() {
                    const progressBars = document.querySelectorAll('.progress-bar');
                    progressBars.forEach(bar => {
                      const originalWidth = bar.style.width;
                      bar.style.width = '0%';
                      setTimeout(() => {
                        bar.style.width = originalWidth;
                      }, 100);
                    });
                  });
                </script>
                <!-- ============================================== -->
                <!-- //* -- DISTRIBUCIÓN POR DIFICULTAD - GRÁFICO CIRCULAR -->
                <style>
                  .chart-container {
                    position: relative;
                    margin: auto;
                  }

                  .card {
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    border: none;
                    border-radius: 10px;
                  }



                  .card-title {
                    margin-bottom: 0;
                    font-weight: 600;
                  }
                </style>
                
                <div class="col-12 col-xl-6 mb-4">
                  <div class="card h-100">
                    <div class="card-header">
                      <h5 class="card-title">Distribución de Recetas por Dificultad</h5>
                    </div>
                    <div class="card-body mt-5">
                      <div class="chart-container" style="position: relative; height:300px; width:100%">
                        <canvas id="dificultadChart"></canvas>
                      </div>
                      <div class="mt-3 card-footer" id="leyendaDificultad"></div>
                    </div>
                  </div>
                </div>

                <!-- ============================================== -->
                <!-- //! -- MAPA DE CALOR - UBICACIONES DE USUARIOS -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h4 class="card-title m-0">Mapa de Calor - Distribución de Usuarios</h4>
                      
                    </div>
                    <div class="card-body">
                      <div id="mapaCalorUsuarios" style="height: 500px; width: 100%; border-radius: 8px;"></div>
                      <div class="mt-3">
                        <small class="text-muted">
                          <i class='bx bx-info-circle'></i>
                          El mapa muestra la concentración de usuarios registrados por ubicación geográfica.
                        </small>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Incluir Leaflet CSS y JS -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <!-- Plugin de heatmap para Leaflet -->
                <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

                <style>
                  #mapaCalorUsuarios {
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e0e0e0;
                  }

                  .info {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                  }

                  .legend h6 {
                    color: #2c3e50;
                    font-weight: 600;
                  }
                </style>


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

  <script>
    /**
     * MAPA DE CALOR - IMPLEMENTACIÓN CON LEAFLET
     * 
     * Este script crea un mapa interactivo que muestra la densidad de usuarios
     * mediante un efecto de calor (heatmap) donde los colores indican concentración
     */

    // Mapa de Calor
    document.addEventListener('DOMContentLoaded', function() {
      /**
       * DATOS DESDE PHP
       * Convertimos el array PHP a JavaScript
       */
      const ubicaciones = <?php echo json_encode($ubicaciones_usuarios); ?>;

      /**
       * INICIALIZACIÓN DEL MAPA
       * Creamos el mapa y lo centramos en Argentina por defecto
       * Coordenadas: [-34.6037, -58.3816] = Buenos Aires
       * Zoom inicial: 4 (vista de todo el país)
       */
      const mapa = L.map('mapaCalorUsuarios').setView([-34.6037, -58.3816], 4);

      /**
       * CAPA BASE DEL MAPA (OpenStreetMap)
       * Provee el mapa base con calles, ciudades, etc.
       */
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
      }).addTo(mapa);

      /**
       * PREPARACIÓN DE DATOS PARA HEATMAP
       * Convertimos las ubicaciones en puntos de calor
       */
      const puntosCalor = [];
      let maxUsuarios = 1; // Mínimo 1 para evitar división por cero

      /**
       * ENCONTRAR MÁXIMO DE USUARIOS
       * Necesario para normalizar la intensidad del calor (0-1)
       */
      ubicaciones.forEach(ubicacion => {
        if (ubicacion.total_usuarios > maxUsuarios) {
          maxUsuarios = ubicacion.total_usuarios;
        }
      });

      /**
       * CREACIÓN DE PUNTOS DE CALOR
       * Por cada ubicación, generamos múltiples puntos para mejor visualización
       */
      ubicaciones.forEach(ubicacion => {
        /**
         * CÁLCULO DE INTENSIDAD
         * Normalizamos entre 0 y 1 basado en cantidad de usuarios
         * Escala especial para pocos usuarios (más sensible)
         */
        let intensidad;
        if (maxUsuarios <= 3) {
          // Escala muy sensible: 1 usuario = 0.33, 2 = 0.66, 3 = 1.0
          intensidad = ubicacion.total_usuarios / 3;
        } else {
          // Escala normalizada según máximo encontrado
          intensidad = ubicacion.total_usuarios / maxUsuarios;
        }

        /**
         * GENERACIÓN DE PUNTOS MÚLTIPLES
         * Creamos varios puntos por ubicación para:
         * - Mejor visualización del área de calor
         * - Efecto más suave y natural
         * - Mayor sensibilidad con pocos usuarios
         */
        const puntosExtra = Math.max(3, ubicacion.total_usuarios * 5);
        for (let i = 0; i < puntosExtra; i++) {
          // Pequeña variación aleatoria para dispersar el calor naturalmente
          const variacionLat = (Math.random() - 0.5) * 0.02;
          const variacionLng = (Math.random() - 0.5) * 0.02;

          // Añadir punto: [latitud, longitud, intensidad(0-1)]
          puntosCalor.push([
            ubicacion.lat + variacionLat,
            ubicacion.lng + variacionLng,
            intensidad
          ]);
        }
      });

      /**
       * CONFIGURACIÓN Y CREACIÓN DEL HEATMAP
       * Parámetros clave:
       * - radius: Tamaño del área de influencia de cada punto
       * - blur: Suavizado del efecto (mayor = más difuso)
       * - gradient: Escala de colores según intensidad
       */
      const capaCalor = L.heatLayer(puntosCalor, {
        radius: 35, // Radio grande para buena visibilidad
        blur: 25, // Alto blur para efecto suave
        maxZoom: 15, // Zoom máximo donde se muestra el calor
        minOpacity: 0.4, // Opacidad mínima para puntos débiles
        max: 1.0, // Intensidad máxima (normalizada)
        gradient: {
          0.1: 'blue', // Baja densidad: 1 usuario
          0.3: 'cyan', // Densidad media-baja: 1-2 usuarios
          0.5: 'lime', // Densidad media: 2 usuarios
          0.7: 'yellow', // Densidad media-alta: 2-3 usuarios
          0.9: 'orange', // Densidad alta: 3 usuarios
          1.0: 'red' // Máxima densidad: 3+ usuarios
        }
      }).addTo(mapa);

      /**
       * AJUSTE AUTOMÁTICO DEL ZOOM
       * Calcula el área que contiene todas las ubicaciones y ajusta la vista
       */
      if (ubicaciones.length > 0) {
        const bounds = L.latLngBounds();
        ubicaciones.forEach(ubicacion => {
          bounds.extend([ubicacion.lat, ubicacion.lng]);
        });
        mapa.fitBounds(bounds.pad(0.3)); // 30% de padding para mejor visualización
      }

      /**
       * CONTROLES DEL MAPA
       * Añade escala en metros/kilómetros para referencia
       */
      L.control.scale().addTo(mapa);

      /**
       * LEYENDA PERSONALIZADA
       * Explica el significado de los colores del heatmap
       * Se posiciona en la esquina inferior derecha
       */
      const leyenda = L.control({
        position: 'bottomright'
      });
      leyenda.onAdd = function() {
        const div = L.DomUtil.create('div', 'info legend');
        div.style.backgroundColor = 'white';
        div.style.padding = '12px';
        div.style.borderRadius = '8px';
        div.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        div.style.fontSize = '12px';
        div.innerHTML = `
            <h6 style="margin: 0 0 10px 0; font-weight: bold;">Densidad de Usuarios</h6>
            <div style="display: flex; align-items: center; margin-bottom: 6px;">
                <div style="width: 16px; height: 16px; background: blue; margin-right: 8px; border-radius: 3px;"></div>
                <span>1 usuario</span>
            </div>
            <div style="display: flex; align-items: center; margin-bottom: 6px;">
                <div style="width: 16px; height: 16px; background: lime; margin-right: 8px; border-radius: 3px;"></div>
                <span>2 usuarios</span>
            </div>
            <div style="display: flex; align-items: center; margin-bottom: 6px;">
                <div style="width: 16px; height: 16px; background: yellow; margin-right: 8px; border-radius: 3px;"></div>
                <span>2-3 usuarios</span>
            </div>
            <div style="display: flex; align-items: center;">
                <div style="width: 16px; height: 16px; background: red; margin-right: 8px; border-radius: 3px;"></div>
                <span>3+ usuarios</span>
            </div>
        `;
        return div;
      };
      leyenda.addTo(mapa);

      /**
       * EVENTO: TOOLTIP AL HOVER
       * Podría implementarse para mostrar información al pasar sobre áreas calientes
       */
      mapa.on('mousemove', function(e) {
        // Implementación futura: mostrar tooltip con info de ubicación
      });
    });
  </script>

  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>
  <script src="../assets/js/dashboards-analytics.js"></script>

  <!-- GitHub buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Datos desde PHP
    const dificultades = <?php echo json_encode($dificultades); ?>;
    const totales = <?php echo json_encode($totales); ?>;
    const porcentajes = <?php echo json_encode($porcentajes); ?>;
    const colores = ['#4CAF50', '#FF9800', '#F44336'];

    // Crear el gráfico circular
    const ctx = document.getElementById('dificultadChart').getContext('2d');
    const dificultadChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: dificultades.map((dificultad, index) =>
          `${dificultad} (${porcentajes[index]}%)`
        ),
        datasets: [{
          data: totales,
          backgroundColor: colores,
          borderColor: '#fff',
          borderWidth: 2,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true,
              pointStyle: 'circle',
              font: {
                size: 12
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.raw || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = Math.round((value / total) * 100);
                return `${label.split(' (')[0]}: ${value} recetas (${percentage}%)`;
              }
            }
          }
        },
        animation: {
          animateScale: true,
          animateRotate: true
        }
      }
    });

    // Crear una leyenda personalizada
    function actualizarLeyenda() {
      const leyendaContainer = document.getElementById('leyendaDificultad');
      let leyendaHTML = '<div class="row text-center">';

      dificultades.forEach((dificultad, index) => {
        leyendaHTML += `
            <div class="col-md-4 mb-2">
                <div class="d-flex align-items-center justify-content-center">
                    <span class="badge me-2" style="background-color: ${colores[index]}; width: 15px; height: 15px; border-radius: 50%;"></span>
                    <small>
                        <strong>${dificultad}:</strong> ${totales[index]} recetas (${porcentajes[index]}%)
                    </small>
                </div>
            </div>
        `;
      });

      leyendaHTML += '</div>';
      leyendaContainer.innerHTML = leyendaHTML;
    }

    // Llamar a la función para crear la leyenda
    actualizarLeyenda();

    // Función para actualizar el gráfico si es dinámico
    function actualizarGraficoDificultad() {
      
      dificultadChart.update();
    }
  </script>

</body>

</html>