<?php
// Iniciar o reanudar la sesión del usuario
// Esto permite acceder a las variables $_SESSION que contienen los datos del usuario logueado
session_start();

// Incluir el archivo de conexión a la base de datos
// Este archivo contiene la configuración para conectarse a MySQL
include("conexion.php");

// Verificar si el usuario ha iniciado sesión
// Comprueba si existe la variable de sesión 'id_usuario'
if (isset($_SESSION['id_usuario'])) {

    // OBTENER DATOS BÁSICOS DEL USUARIO DESDE LA SESIÓN

    // Guardar el ID del usuario actual desde la sesión
    $ID_Usuario = $_SESSION['id_usuario'];

    // Guardar el nombre del usuario desde la sesión  
    $Nombre = $_SESSION['nombre'];

    // Guardar el apellido del usuario desde la sesión
    $Apellido = $_SESSION['apellido'];

    // CONSULTAR EL ROL DEL USUARIO EN LA BASE DE DATOS

    // Consulta SQL para obtener el ID y nombre del rol del usuario
    // JOIN une la tabla 'usuarios' con 'roles' usando la columna 'rol'
    $sql = "SELECT r.id_rol, r.nombre_rol FROM usuarios u JOIN roles r ON u.rol = r.id_rol WHERE u.id_usuario = $ID_Usuario";

    // Ejecutar la consulta en la base de datos
    $result = $conexion->query($sql);

    // Obtener la primera fila del resultado como array asociativo
    $row = $result->fetch_assoc();

    // Guardar el nombre del rol (ej: 'Administrador', 'Usuario')
    $Rol = $row['nombre_rol'];

    // Guardar el ID numérico del rol (ej: 1, 2, 3)
    $RolId = $row['id_rol'];

    // CONSULTAR LAS SECCIONES PERMITIDAS PARA ESTE ROL

    // Consulta para obtener todas las secciones a las que tiene acceso este rol
    $sql_permisos = "SELECT id_seccion FROM roles_permisos_secciones WHERE id_rol = $RolId";

    // Ejecutar la consulta
    $result_permisos = $conexion->query($sql_permisos);

    // Crear un array vacío para almacenar los IDs de secciones permitidas
    $secciones_permitidas = [];

    // Recorrer TODOS los resultados de la consulta usando while
    // Mientras haya filas disponibles, el bucle sigue ejecutándose
    while ($row_permiso = $result_permisos->fetch_assoc()) {
        // Agregar cada ID de sección al array de secciones permitidas
        $secciones_permitidas[] = $row_permiso['id_seccion'];
    }

    // Definir el ID de la sección de Usuarios (siempre será 1 en este sistema)
    $id_seccion_usuarios = 1;

    // CONSULTAR LOS PERMISOS ESPECÍFICOS PARA LA SECCIÓN DE USUARIOS

    // Consulta para obtener los permisos específicos (crear, editar, eliminar, etc.)
    // Solo para la sección de Usuarios y el rol actual
    $sql_permiso_usuario = "SELECT permisos FROM roles_permisos_secciones WHERE id_rol = $RolId AND id_seccion = $id_seccion_usuarios";

    // Ejecutar la consulta
    $result_permiso_usuario = $conexion->query($sql_permiso_usuario);

    // Crear array vacío para los permisos del usuario
    $permisos_usuario = [];

    // Verificar si se obtuvieron resultados y procesarlos
    if ($row_permiso_usuario = $result_permiso_usuario->fetch_assoc()) {
        // PROCESAR LOS PERMISOS DE TIPO SET DE MYSQL:
        // 1. str_replace("'", "", ...) → Elimina las comillas simples del string
        // 2. explode(',', ...) → Convierte el string en array separando por comas

        // Ejemplo: Convierte "'crear,editar,eliminar'" en ['crear', 'editar', 'eliminar']
        $permisos_usuario = explode(',', str_replace("'", "", $row_permiso_usuario['permisos']));
    }
} else {
    // SI EL USUARIO NO HA INICIADO SESIÓN:

    // Redirigir al usuario a la página de Login
    header("Location: Login.php");

    // Terminar la ejecución del script inmediatamente
    // Esto evita que se ejecute código adicional
    exit();
}
?>




<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Usuarios</title>

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
                <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                <!-- //!Content wrapper MAIN MENÚ==================================================================================== -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <!-- //* USUARIO HABILITADOS ==================================================================================================== -->
                    <!-- Comenzamos con el HTML -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <?php
                        // Conecto con la base de datos
                        include "conexion.php";

                        // Si falla la conexión, muestro un error y corto la ejecución
                        if (!$conexion) {
                            die("Error de conexión: " . mysqli_connect_error());
                        }

                        //  mostramos 10 resultados por página
                        $resultadosPorPagina = 10;
                        // Obtenemos la página actual de la URL, si no viene nada mostramos la 1
                        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        // Calculamos desde qué registro empezar (para el LIMIT de SQL)
                        $inicio = ($paginaActual - 1) * $resultadosPorPagina;

                        // Recibimos los filtros del formulario de búsqueda
                        $nombreFiltro = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
                        $apellidoFiltro = isset($_GET['apellido']) ? trim($_GET['apellido']) : '';
                        $usuarioFiltro = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';
                        $fechaFiltro = isset($_GET['fecha']) ? $_GET['fecha'] : '';

                        // Condición base para solo traer usuarios habilitados
                        $whereCondition = "WHERE U.estado = 'habilitado'";

                        // Si hay filtros, los vamos agregando a la consulta
                        if (!empty($nombreFiltro)) {
                            // Escapamos el valor para evitar inyección SQL
                            $whereCondition .= " AND U.nombre LIKE '%" . $conexion->real_escape_string($nombreFiltro) . "%'";
                        }
                        if (!empty($apellidoFiltro)) {
                            $whereCondition .= " AND U.apellido LIKE '%" . $conexion->real_escape_string($apellidoFiltro) . "%'";
                        }
                        if (!empty($usuarioFiltro)) {
                            $whereCondition .= " AND U.nombre_usuario LIKE '%" . $conexion->real_escape_string($usuarioFiltro) . "%'";
                        }
                        if (!empty($fechaFiltro)) {
                            $whereCondition .= " AND DATE(U.fecha_creacion) = '" . $conexion->real_escape_string($fechaFiltro) . "'";
                        }

                        // Consulta principal que trae los usuarios con sus géneros
                        $sql = "SELECT U.*, G.* FROM usuarios U 
                            INNER JOIN generos G ON G.id_genero = U.genero 
                            $whereCondition
                            ORDER BY U.id_usuario ASC
                            LIMIT $inicio, $resultadosPorPagina";

                        $result = $conexion->query($sql);

                        // Consulta para saber el total de usuarios (para la paginación)
                        $totalQuery = "SELECT COUNT(*) as total FROM usuarios U $whereCondition";
                        $totalResult = $conexion->query($totalQuery);
                        $totalUsuarios = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;
                        $totalPaginas = ceil($totalUsuarios / $resultadosPorPagina);

                        // Si hay error en la consulta, mostramos y cortamos
                        if (!$result) {
                            die("Error en la consulta: " . $conexion->error);
                        }
                        ?>

                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Administración /</span> Usuarios Habilitados
                        </h4>

                        <div class="card">
                            <div class="card-header border-bottom">
                                <!-- Botón para agregar nuevo usuario (solo si tiene permiso) -->
                                <h6 class="card-title">Añadir un usuario</h6>
                                <?php if (in_array('crear', $permisos_usuario)): ?>
                                    <a href="./vista-registrar-usuario.php" class="btn btn-primary">+ Nuevo usuario</a>
                                <?php endif; ?>

                                <br>
                                <hr>

                                <!-- Formulario de búsqueda -->
                                <h6 class="card-title">Buscar usuario</h6>
                                <form action="" method="GET" class="row g-3">
                                    <!-- Reseteamos a página 1 al hacer una nueva búsqueda -->
                                    <input type="hidden" name="page" value="1">

                                    <!-- Campos de filtro -->
                                    <div class="col-md-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" name="nombre" id="nombre" class="form-control"
                                            value="<?= htmlspecialchars($nombreFiltro) ?>" placeholder="Filtrar por nombre">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" name="apellido" id="apellido" class="form-control"
                                            value="<?= htmlspecialchars($apellidoFiltro) ?>" placeholder="Filtrar por apellido">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="usuario" class="form-label">Usuario</label>
                                        <input type="text" name="usuario" id="usuario" class="form-control"
                                            value="<?= htmlspecialchars($usuarioFiltro) ?>" placeholder="Filtrar por nombre de usuario">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="fecha" class="form-label">Fecha creación</label>
                                        <input type="date" name="fecha" id="fecha" class="form-control"
                                            value="<?= htmlspecialchars($fechaFiltro) ?>">
                                    </div>

                                    <!-- Botones de acción -->
                                    <div class="col-md-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-2">Buscar</button>
                                        <!-- Mostramos botón para limpiar solo si hay filtros aplicados -->
                                        <?php if ($nombreFiltro || $apellidoFiltro || $usuarioFiltro || $fechaFiltro): ?>
                                            <a href="usuarios.php" class="btn btn-secondary">Limpiar filtros</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>

                            <!-- Tabla de resultados -->
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover">
                                    <!-- ENCABEZADO DE LA TABLA -->
                                    <thead>
                                        <tr>
                                            <th>Persona</th>
                                            <th>Nombre de Usuario</th>
                                            <th>Fecha de Creación</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>

                                    <!-- CUERPO DE LA TABLA -->
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <!-- 
                        Si hay resultados en la consulta, recorrer cada fila con while
                        fetch_assoc() obtiene una fila como array asociativo
                        El bucle se ejecuta mientras haya filas disponibles
                        -->
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <!-- COLUMNA: INFORMACIÓN PERSONAL CON FOTO -->
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php
                                                            // Obtener la ruta de la imagen del usuario
                                                            $imgPath = $row['img'];

                                                            // Verificar si la imagen existe y mostrar la foto
                                                            // Si no existe, mostrar un ícono por defecto
                                                            if (!empty($imgPath) && file_exists("../ruta/donde/guardas/las/imagenes/$imgPath")) {
                                                                // Mostrar imagen del usuario
                                                                echo '<img src="./img_usuario/' . $imgPath . '" alt="Avatar" class="rounded-circle me-3" width="40">';
                                                            } else {
                                                                // Mostrar ícono por defecto (silueta de persona)
                                                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-fill rounded-circle me-3" viewBox="0 0 16 16">
                                              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>';
                                                            }
                                                            ?>
                                                            <div>
                                                                <!-- Mostrar nombre y apellido del usuario -->
                                                                <strong><?= htmlspecialchars($row['nombre']) ?></strong>
                                                                <div class="text-muted small"><?= htmlspecialchars($row['apellido']) ?></div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- COLUMNA: NOMBRE DE USUARIO -->
                                                    <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>

                                                    <!-- COLUMNA: FECHA DE CREACIÓN FORMATEADA -->
                                                    <td>
                                                        <?php
                                                        // Convertir la fecha de la base de datos a objeto DateTime
                                                        $fecha = new DateTime($row['fecha_creacion']);

                                                        // Configurar localización para español (aunque no se usa directamente)
                                                        setlocale(LC_TIME, 'es_ES.UTF-8');

                                                        // Array para traducir meses de inglés a español
                                                        $meses = [
                                                            'January' => 'Enero',
                                                            'February' => 'Febrero',
                                                            'March' => 'Marzo',
                                                            'April' => 'Abril',
                                                            'May' => 'Mayo',
                                                            'June' => 'Junio',
                                                            'July' => 'Julio',
                                                            'August' => 'Agosto',
                                                            'September' => 'Septiembre',
                                                            'October' => 'Octubre',
                                                            'November' => 'Noviembre',
                                                            'December' => 'Diciembre'
                                                        ];

                                                        // Extraer día, mes y año de la fecha
                                                        $dia = $fecha->format('d');       // Día con 2 dígitos (01-31)
                                                        $mes = $meses[$fecha->format('F')]; // Mes en español
                                                        $anio = $fecha->format('Y');     // Año con 4 dígitos

                                                        // Mostrar fecha formateada: "15 de Enero del 2024"
                                                        echo "$dia de $mes del $anio";
                                                        ?>
                                                    </td>

                                                    <!-- COLUMNA: ESTADO DEL USUARIO -->
                                                    <td>
                                                        <!-- Badge verde que muestra el estado "habilitado" -->
                                                        <span class="badge bg-success"><?= htmlspecialchars($row['estado']) ?></span>
                                                    </td>

                                                    <!-- COLUMNA: ACCIONES DISPONIBLES -->
                                                    <td>
                                                        <!-- Menú desplegable con las acciones posibles -->
                                                        <div class="dropdown">
                                                            <!-- Botón que activa el menú desplegable -->
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>

                                                            <!-- Contenido del menú desplegable -->
                                                            <div class="dropdown-menu">
                                                                <!-- OPCIÓN: VER DETALLE -->
                                                                <?php if (in_array('detalle', $permisos_usuario)): ?>
                                                                    <a class="dropdown-item" href="PerfilUsuario.php?id_usuario=<?= $row['id_usuario'] ?>">
                                                                        <i class="bi bi-eye me-1"></i> Ver detalle
                                                                    </a>
                                                                <?php endif; ?>

                                                                <!-- 
                                            Verificar que el usuario no sea el mismo que está logueado
                                            Esto evita que un usuario se edite/elimine a sí mismo
                                            -->
                                                                <?php if ($ID_Usuario != $row['id_usuario']): ?>

                                                                    <!-- OPCIÓN: EDITAR USUARIO -->
                                                                    <?php if (in_array('editar', $permisos_usuario)): ?>
                                                                        <a class="dropdown-item" href="vista-editar-usuario.php?Id=<?= $row['id_usuario'] ?>">
                                                                            <i class="bi bi-pencil-square me-1"></i> Editar
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <!-- OPCIÓN: ELIMINAR USUARIO -->
                                                                    <?php if (in_array('eliminar', $permisos_usuario)): ?>
                                                                        <!-- 
                                                    onclick llama a función JavaScript con confirmación
                                                    addslashes() escapa comillas para evitar errores en JavaScript
                                                    -->
                                                                        <a class="dropdown-item" href="#" onclick="confirmarEliminacion(<?= $row['id_usuario'] ?>, '<?= addslashes($row['nombre']) ?>', '<?= addslashes($row['apellido']) ?>')">
                                                                            <i class="bx bx-trash me-1"></i> Eliminar
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <!-- OPCIÓN: DESHABILITAR USUARIO -->
                                                                    <?php if (in_array('estado', $permisos_usuario)): ?>
                                                                        <a class="dropdown-item" href="#" onclick="deshabilitarUsuario(<?= $row['id_usuario'] ?>, '<?= addslashes($row['nombre']) ?>', '<?= addslashes($row['apellido']) ?>')">
                                                                            <i class="bi bi-box-arrow-right me-1"></i> Deshabilitar
                                                                        </a>
                                                                    <?php endif; ?>

                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <!-- MENSAJE CUANDO NO HAY RESULTADOS -->
                                            <tr>
                                                <td colspan="5" class="text-center py-4">No se encontraron usuarios habilitados</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                <!-- PIE DE TABLA CON INFORMACIÓN DE PAGINACIÓN -->
                                <div class="d-flex justify-content-between align-items-center p-3">
                                    <!-- TEXTO INFORMATIVO SOBRE LOS RESULTADOS MOSTRADOS -->
                                    <div class="text-muted">
                                        <!-- 
                    Muestra el rango de resultados actual y el total
                    Ej: "Mostrando 1 a 10 de 25 usuarios"
                    min() evita que se muestre un número mayor al total
                    -->
                                        Mostrando <?= ($inicio + 1) ?> a <?= min($inicio + $resultadosPorPagina, $totalUsuarios) ?> de <?= $totalUsuarios ?> usuarios
                                    </div>

                                    <!-- PAGINACIÓN - SOLO SE MUESTRA SI HAY MÁS DE UNA PÁGINA -->
                                    <?php if ($totalPaginas > 1): ?>
                                        <nav aria-label="Paginación">
                                            <ul class="pagination mb-0">
                                                <?php
                                                // PREPARAR FILTROS PARA MANTENERLOS EN LA PAGINACIÓN

                                                // Array con todos los filtros actuales
                                                $params = [
                                                    'nombre' => $nombreFiltro,
                                                    'apellido' => $apellidoFiltro,
                                                    'usuario' => $usuarioFiltro,
                                                    'fecha' => $fechaFiltro
                                                ];

                                                // array_filter() elimina los filtros vacíos
                                                // http_build_query() convierte el array en string de URL
                                                $queryString = http_build_query(array_filter($params));

                                                // BOTÓN "ANTERIOR"
                                                // Si estamos en página 1, el botón está deshabilitado
                                                echo '<li class="page-item ' . ($paginaActual == 1 ? 'disabled' : '') . '">';
                                                echo '<a class="page-link" href="?page=' . ($paginaActual - 1) . '&' . $queryString . '" aria-label="Anterior">';
                                                echo '<span aria-hidden="true">&laquo;</span>';
                                                echo '</a></li>';

                                                // CÁLCULO DE QUÉ PÁGINAS MOSTRAR
                                                // Mostrar 5 páginas centradas en la actual
                                                $startPage = max(1, $paginaActual - 2);  // No menor que 1
                                                $endPage = min($totalPaginas, $startPage + 4); // No mayor que el total

                                                // GENERACIÓN DE LOS NÚMEROS DE PÁGINA
                                                // Bucle for que crea los enlaces a cada página
                                                for ($i = $startPage; $i <= $endPage; $i++) {
                                                    // Marcar como activa la página actual
                                                    echo '<li class="page-item ' . ($i == $paginaActual ? 'active' : '') . '">';
                                                    echo '<a class="page-link" href="?page=' . $i . '&' . $queryString . '">' . $i . '</a>';
                                                    echo '</li>';
                                                }

                                                // BOTÓN "SIGUIENTE"
                                                // Si estamos en la última página, el botón está deshabilitado
                                                echo '<li class="page-item ' . ($paginaActual == $totalPaginas ? 'disabled' : '') . '">';
                                                echo '<a class="page-link" href="?page=' . ($paginaActual + 1) . '&' . $queryString . '" aria-label="Siguiente">';
                                                echo '<span aria-hidden="true">&raquo;</span>';
                                                echo '</a></li>';
                                                ?>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- //? JavaScript para las funcionalidades DE USUARIOS HABILITADOS 111111111111111111111111111111111111111111111111111111111111 -->
                    <script>
                        // Buscar al presionar Enter en cualquier campo de filtro
                        document.addEventListener('DOMContentLoaded', function() {
                            document.querySelectorAll('#nombre, #apellido, #usuario, #fecha').forEach(input => {
                                input.addEventListener('keypress', function(e) {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();
                                        this.form.submit();
                                    }
                                });
                            });
                        });
                    </script>
                    <!-- / Content -->



                    <!-- Content -->
                    <!-- //! USUARIOS DESHABILITADOS=================================================================================================== -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="py-3 mb-4">
                                    <span class="text-muted fw-light">Administración /</span> Usuarios Deshabilitados
                                </h4>

                                <!-- Formulario de búsqueda para deshabilitados (con IDs diferentes) -->
                                <form action="" method="GET" class="row g-3">
                                    <input type="hidden" name="page" value="1">

                                    <div class="col-md-3">
                                        <label for="nombre_des" class="form-label">Nombre</label>
                                        <input type="text" name="nombre_des" id="nombre_des" class="form-control"
                                            value="<?= isset($_GET['nombre_des']) ? htmlspecialchars($_GET['nombre_des']) : '' ?>"
                                            placeholder="Filtrar por nombre">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="apellido_des" class="form-label">Apellido</label>
                                        <input type="text" name="apellido_des" id="apellido_des" class="form-control"
                                            value="<?= isset($_GET['apellido_des']) ? htmlspecialchars($_GET['apellido_des']) : '' ?>"
                                            placeholder="Filtrar por apellido">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="usuario_des" class="form-label">Usuario</label>
                                        <input type="text" name="usuario_des" id="usuario_des" class="form-control"
                                            value="<?= isset($_GET['usuario_des']) ? htmlspecialchars($_GET['usuario_des']) : '' ?>"
                                            placeholder="Filtrar por nombre de usuario">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="fecha_des" class="form-label">Fecha creación</label>
                                        <input type="date" name="fecha_des" id="fecha_des" class="form-control"
                                            value="<?= isset($_GET['fecha_des']) ? htmlspecialchars($_GET['fecha_des']) : '' ?>">
                                    </div>

                                    <div class="col-md-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-2">Buscar</button>
                                        <?php if (isset($_GET['nombre_des']) || isset($_GET['apellido_des']) || isset($_GET['usuario_des']) || isset($_GET['fecha_des'])): ?>
                                            <a href="usuarios.php" class="btn btn-secondary">Limpiar filtros</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>

                            <?php
                            // Configuración de la paginación
                            $resultadosPorPagina = 10;

                            // Obtenemos los filtros (con sufijo _des)
                            $nombreFiltro = isset($_GET['nombre_des']) ? trim($_GET['nombre_des']) : '';
                            $apellidoFiltro = isset($_GET['apellido_des']) ? trim($_GET['apellido_des']) : '';
                            $usuarioFiltro = isset($_GET['usuario_des']) ? trim($_GET['usuario_des']) : '';
                            $fechaFiltro = isset($_GET['fecha_des']) ? $_GET['fecha_des'] : '';

                            // Condición base
                            $whereCondition = "WHERE U.estado = 'deshabilitado'";

                            // Aplicamos filtros
                            if (!empty($nombreFiltro)) {
                                $whereCondition .= " AND U.nombre LIKE '%" . $conexion->real_escape_string($nombreFiltro) . "%'";
                            }
                            if (!empty($apellidoFiltro)) {
                                $whereCondition .= " AND U.apellido LIKE '%" . $conexion->real_escape_string($apellidoFiltro) . "%'";
                            }
                            if (!empty($usuarioFiltro)) {
                                $whereCondition .= " AND U.nombre_usuario LIKE '%" . $conexion->real_escape_string($usuarioFiltro) . "%'";
                            }
                            if (!empty($fechaFiltro)) {
                                $whereCondition .= " AND DATE(U.fecha_creacion) = '" . $conexion->real_escape_string($fechaFiltro) . "'";
                            }

                            // Paginación
                            $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $inicio = ($paginaActual - 1) * $resultadosPorPagina;

                            // Consulta principal con filtros
                            $sql = "SELECT U.*, G.* FROM usuarios U 
                            INNER JOIN generos G ON G.id_genero = U.genero 
                            $whereCondition
                            ORDER BY U.id_usuario ASC
                            LIMIT $inicio, $resultadosPorPagina";
                            $result = $conexion->query($sql);

                            // Total de resultados para paginación
                            $totalQuery = "SELECT COUNT(*) as total FROM usuarios U $whereCondition";
                            $totalResult = $conexion->query($totalQuery);
                            $totalUsuarios = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;
                            $totalPaginas = ceil($totalUsuarios / $resultadosPorPagina);
                            ?>

                            <!-- Tabla de resultados -->
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Persona</th>
                                            <th>Nombre de Usuario</th>
                                            <th>Fecha de Creación</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php
                                                            $imgPath = $row['img'];
                                                            if (!empty($imgPath) && file_exists("../ruta/donde/guardas/las/imagenes/$imgPath")) {
                                                                echo '<img src="./img_usuario/' . $imgPath . '" alt="Avatar" class="rounded-circle me-3" width="40">';
                                                            } else {
                                                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-fill rounded-circle me-3" viewBox="0 0 16 16">
                                              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>';
                                                            }
                                                            ?>
                                                            <div>
                                                                <strong><?= htmlspecialchars($row['nombre']) ?></strong>
                                                                <div class="text-muted small"><?= htmlspecialchars($row['apellido']) ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>
                                                    <td>
                                                        <?php
                                                        $fecha = new DateTime($row['fecha_creacion']);
                                                        setlocale(LC_TIME, 'es_ES.UTF-8');
                                                        $meses = [
                                                            'January' => 'Enero',
                                                            'February' => 'Febrero',
                                                            'March' => 'Marzo',
                                                            'April' => 'Abril',
                                                            'May' => 'Mayo',
                                                            'June' => 'Junio',
                                                            'July' => 'Julio',
                                                            'August' => 'Agosto',
                                                            'September' => 'Septiembre',
                                                            'October' => 'Octubre',
                                                            'November' => 'Noviembre',
                                                            'December' => 'Diciembre'
                                                        ];
                                                        $dia = $fecha->format('d');
                                                        $mes = $meses[$fecha->format('F')];
                                                        $anio = $fecha->format('Y');
                                                        echo "$dia de $mes del $anio";
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-danger"><?= htmlspecialchars($row['estado']) ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <?php if (in_array('detalle', $permisos_usuario)): ?>
                                                                    <a class="dropdown-item" href="PerfilUsuario.php?id_usuario=<?= $row['id_usuario'] ?>">
                                                                        <i class="bi bi-eye me-1"></i> Ver detalle
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($ID_Usuario != $row['id_usuario']): ?>
                                                                    <?php if (in_array('editar', $permisos_usuario)): ?>
                                                                        <a class="dropdown-item" href="vista-editar-usuario.php?Id=<?= $row['id_usuario'] ?>">
                                                                            <i class="bi bi-pencil-square me-1"></i> Editar
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <?php if (in_array('estado', $permisos_usuario)): ?>
                                                                        <a class="dropdown-item" href="#" onclick="habilitarUsuario(<?= $row['id_usuario'] ?>, '<?= addslashes($row['nombre']) ?>', '<?= addslashes($row['apellido']) ?>')">
                                                                            <i class="bi bi-box-arrow-in-right me-1"></i> Habilitar
                                                                        </a>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">No se encontraron usuarios deshabilitados</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                <!-- Pie de tabla con paginación -->
                                <div class="d-flex justify-content-between align-items-center p-3">
                                    <div class="text-muted">
                                        Mostrando <?= ($inicio + 1) ?> a <?= min($inicio + $resultadosPorPagina, $totalUsuarios) ?> de <?= $totalUsuarios ?> usuarios
                                    </div>

                                    <?php if ($totalPaginas > 1): ?>
                                        <nav aria-label="Paginación">
                                            <ul class="pagination mb-0">
                                                <?php
                                                // Juntamos los filtros para mantenerlos en la paginación
                                                $params = [
                                                    'nombre_des' => $nombreFiltro,
                                                    'apellido_des' => $apellidoFiltro,
                                                    'usuario_des' => $usuarioFiltro,
                                                    'fecha_des' => $fechaFiltro
                                                ];
                                                $queryString = http_build_query(array_filter($params));

                                                // Botón Anterior
                                                echo '<li class="page-item ' . ($paginaActual == 1 ? 'disabled' : '') . '">';
                                                echo '<a class="page-link" href="?page=' . ($paginaActual - 1) . '&' . $queryString . '" aria-label="Anterior">';
                                                echo '<span aria-hidden="true">&laquo;</span>';
                                                echo '</a></li>';

                                                // Mostramos las páginas
                                                $startPage = max(1, $paginaActual - 2);
                                                $endPage = min($totalPaginas, $startPage + 4);

                                                for ($i = $startPage; $i <= $endPage; $i++) {
                                                    echo '<li class="page-item ' . ($i == $paginaActual ? 'active' : '') . '">';
                                                    echo '<a class="page-link" href="?page=' . $i . '&' . $queryString . '">' . $i . '</a>';
                                                    echo '</li>';
                                                }

                                                // Botón Siguiente
                                                echo '<li class="page-item ' . ($paginaActual == $totalPaginas ? 'disabled' : '') . '">';
                                                echo '<a class="page-link" href="?page=' . ($paginaActual + 1) . '&' . $queryString . '" aria-label="Siguiente">';
                                                echo '<span aria-hidden="true">&raquo;</span>';
                                                echo '</a></li>';
                                                ?>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- //? JavaScript para las funcionalidades DE USUARIOS DESHABILITADOS 22222222222222222222222222222222222222222222222222222222222222 -->
                    <script>
                        // Buscar al presionar Enter en cualquier campo de filtro
                        document.addEventListener('DOMContentLoaded', function() {
                            document.querySelectorAll('#nombre_des, #apellido_des, #usuario_des, #fecha_des').forEach(input => {
                                input.addEventListener('keypress', function(e) {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();
                                        this.form.submit();
                                    }
                                });
                            });
                        });
                    </script>



                    <!-- //TODO USUARIOS PENDIENTES=================================================================================================== -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="py-3 mb-4">
                                    <span class="text-muted fw-light">Administración /</span> Usuarios Pendientes
                                </h4>

                                <!-- Formulario de búsqueda para pendientes (con IDs diferentes) -->
                                <form action="" method="GET" class="row g-3">
                                    <input type="hidden" name="page3" value="1">

                                    <div class="col-md-3">
                                        <label for="nombre_pen" class="form-label">Nombre</label>
                                        <input type="text" name="nombre_pen" id="nombre_pen" class="form-control"
                                            value="<?= isset($_GET['nombre_pen']) ? htmlspecialchars($_GET['nombre_pen']) : '' ?>"
                                            placeholder="Filtrar por nombre">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="apellido_pen" class="form-label">Apellido</label>
                                        <input type="text" name="apellido_pen" id="apellido_pen" class="form-control"
                                            value="<?= isset($_GET['apellido_pen']) ? htmlspecialchars($_GET['apellido_pen']) : '' ?>"
                                            placeholder="Filtrar por apellido">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="usuario_pen" class="form-label">Usuario</label>
                                        <input type="text" name="usuario_pen" id="usuario_pen" class="form-control"
                                            value="<?= isset($_GET['usuario_pen']) ? htmlspecialchars($_GET['usuario_pen']) : '' ?>"
                                            placeholder="Filtrar por nombre de usuario">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="fecha_pen" class="form-label">Fecha creación</label>
                                        <input type="date" name="fecha_pen" id="fecha_pen" class="form-control"
                                            value="<?= isset($_GET['fecha_pen']) ? htmlspecialchars($_GET['fecha_pen']) : '' ?>">
                                    </div>

                                    <div class="col-md-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-2">Buscar</button>
                                        <?php if (isset($_GET['nombre_pen']) || isset($_GET['apellido_pen']) || isset($_GET['usuario_pen']) || isset($_GET['fecha_pen'])): ?>
                                            <a href="usuarios.php" class="btn btn-secondary">Limpiar filtros</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>

                            <?php
                            // Configuración de la paginación
                            $resultadosPorPagina = 10;

                            // Obtenemos los filtros (con sufijo _pen)
                            $nombreFiltro = isset($_GET['nombre_pen']) ? trim($_GET['nombre_pen']) : '';
                            $apellidoFiltro = isset($_GET['apellido_pen']) ? trim($_GET['apellido_pen']) : '';
                            $usuarioFiltro = isset($_GET['usuario_pen']) ? trim($_GET['usuario_pen']) : '';
                            $fechaFiltro = isset($_GET['fecha_pen']) ? $_GET['fecha_pen'] : '';

                            // Condición base
                            $whereCondition = "WHERE U.estado = 'pendiente'";

                            // Aplicamos filtros
                            if (!empty($nombreFiltro)) {
                                $whereCondition .= " AND U.nombre LIKE '%" . $conexion->real_escape_string($nombreFiltro) . "%'";
                            }
                            if (!empty($apellidoFiltro)) {
                                $whereCondition .= " AND U.apellido LIKE '%" . $conexion->real_escape_string($apellidoFiltro) . "%'";
                            }
                            if (!empty($usuarioFiltro)) {
                                $whereCondition .= " AND U.nombre_usuario LIKE '%" . $conexion->real_escape_string($usuarioFiltro) . "%'";
                            }
                            if (!empty($fechaFiltro)) {
                                $whereCondition .= " AND DATE(U.fecha_creacion) = '" . $conexion->real_escape_string($fechaFiltro) . "'";
                            }

                            // Paginación
                            $paginaActual = isset($_GET['page3']) ? (int)$_GET['page3'] : 1;
                            $inicio = ($paginaActual - 1) * $resultadosPorPagina;

                            // Consulta principal con filtros
                            $sql = "SELECT U.*, G.* FROM usuarios U 
                            INNER JOIN generos G ON G.id_genero = U.genero 
                            $whereCondition
                            ORDER BY U.id_usuario ASC
                            LIMIT $inicio, $resultadosPorPagina";
                            $result = $conexion->query($sql);

                            // Total de resultados para paginación
                            $totalQuery = "SELECT COUNT(*) as total FROM usuarios U $whereCondition";
                            $totalResult = $conexion->query($totalQuery);
                            $totalUsuarios = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;
                            $totalPaginas = ceil($totalUsuarios / $resultadosPorPagina);
                            ?>

                            <!-- Tabla de resultados -->
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Persona</th>
                                            <th>Nombre de Usuario</th>
                                            <th>Fecha de Creación</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php
                                                            $imgPath = $row['img'];
                                                            if (!empty($imgPath) && file_exists("../ruta/donde/guardas/las/imagenes/$imgPath")) {
                                                                echo '<img src="./img_usuario/' . $imgPath . '" alt="Avatar" class="rounded-circle me-3" width="40">';
                                                            } else {
                                                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-fill rounded-circle me-3" viewBox="0 0 16 16">
                                              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>';
                                                            }
                                                            ?>
                                                            <div>
                                                                <strong><?= htmlspecialchars($row['nombre']) ?></strong>
                                                                <div class="text-muted small"><?= htmlspecialchars($row['apellido']) ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>
                                                    <td>
                                                        <?php
                                                        $fecha = new DateTime($row['fecha_creacion']);
                                                        setlocale(LC_TIME, 'es_ES.UTF-8');
                                                        echo strftime("%d de %B de %Y %H:%M:%S", $fecha->getTimestamp());
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning"><?= htmlspecialchars($row['estado']) ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <?php if (in_array('autorizacion', $permisos_usuario)): ?>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#" onclick="ConfirmarCreacionUsuario(<?= $row['id_usuario'] ?>, '<?= addslashes($row['nombre']) ?>', '<?= addslashes($row['apellido']) ?>','<?= $ID_Usuario ?>')">
                                                                        <i class="bi bi-check-lg me-1"></i> Confirmar Creación
                                                                    </a>
                                                                    <a class="dropdown-item" href="#" onclick="DenegarCreacionUsuario(<?= $row['id_usuario'] ?>, '<?= addslashes($row['nombre']) ?>', '<?= addslashes($row['apellido']) ?>')">
                                                                        <i class="bi bi-x-lg me-1"></i> Denegar Creación
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">No se encontraron usuarios pendientes</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                <!-- Pie de tabla con paginación -->
                                <div class="d-flex justify-content-between align-items-center p-3">
                                    <div class="text-muted">
                                        Mostrando <?= ($inicio + 1) ?> a <?= min($inicio + $resultadosPorPagina, $totalUsuarios) ?> de <?= $totalUsuarios ?> usuarios pendientes
                                    </div>

                                    <?php if ($totalPaginas > 1): ?>
                                        <nav aria-label="Paginación">
                                            <ul class="pagination mb-0">
                                                <?php
                                                // Juntamos los filtros para mantenerlos en la paginación
                                                $params = [
                                                    'nombre_pen' => $nombreFiltro,
                                                    'apellido_pen' => $apellidoFiltro,
                                                    'usuario_pen' => $usuarioFiltro,
                                                    'fecha_pen' => $fechaFiltro
                                                ];
                                                $queryString = http_build_query(array_filter($params));

                                                // Botón Anterior
                                                echo '<li class="page-item ' . ($paginaActual == 1 ? 'disabled' : '') . '">';
                                                echo '<a class="page-link" href="?page3=' . ($paginaActual - 1) . '&' . $queryString . '" aria-label="Anterior">';
                                                echo '<span aria-hidden="true">&laquo;</span>';
                                                echo '</a></li>';

                                                // Mostramos las páginas
                                                $startPage = max(1, $paginaActual - 2);
                                                $endPage = min($totalPaginas, $startPage + 4);

                                                for ($i = $startPage; $i <= $endPage; $i++) {
                                                    echo '<li class="page-item ' . ($i == $paginaActual ? 'active' : '') . '">';
                                                    echo '<a class="page-link" href="?page3=' . $i . '&' . $queryString . '">' . $i . '</a>';
                                                    echo '</li>';
                                                }

                                                // Botón Siguiente
                                                echo '<li class="page-item ' . ($paginaActual == $totalPaginas ? 'disabled' : '') . '">';
                                                echo '<a class="page-link" href="?page3=' . ($paginaActual + 1) . '&' . $queryString . '" aria-label="Siguiente">';
                                                echo '<span aria-hidden="true">&raquo;</span>';
                                                echo '</a></li>';
                                                ?>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- //? JavaScript para las funcionalidades DE USUARIOS PENDIENTES 33333333333333333333333333333333333333333333333333333333333333333333 -->
                    <script>
                        // Buscar al presionar Enter en cualquier campo de filtro
                        document.addEventListener('DOMContentLoaded', function() {
                            document.querySelectorAll('#nombre_pen, #apellido_pen, #usuario_pen, #fecha_pen').forEach(input => {
                                input.addEventListener('keypress', function(e) {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();
                                        this.form.submit();
                                    }
                                });
                            });
                        });
                    </script>

                </div>
                <!--/ Hoverable Table rows -->
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <!-- //!======================================================================================================== -->
        <!-- Content wrapper -->
    </div>
    <!-- / Layout wrapper -->
    <?php
    // Si el botón de búsqueda se presionó sin introducir un término o no se encontraron coincidencias
    if (isset($_GET['search'])) {
        if (empty($term)) {
            // Si el campo de búsqueda está vacío

            echo "<script> window.onload = function() { Swal.fire({ icon: 'warning', title: 'Campo vacío', text: 'Por favor ingresa un nombre de usuario para buscar.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'usuarios.php'; }); } </script>";
            exit;
        } elseif ($result->num_rows == 0) {
            // Si no se encontraron resultados
            echo "<script> window.onload = function() { Swal.fire({ icon: 'info', title: 'Sin resultados', text: 'No se encontró ningún usuario con ese nombre.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'usuarios.php'; }); } </script>";
            exit;
        }
    } ?>


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


    <!--Este scrip es para mostrar la alerta  despues de editar un usuario-->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- //TODO AQUI VAN LAS PREGUNTAS POR SWEET ALERT PARA EJECUTAR ACCIONES -->
    <script>
        function habilitarUsuario(id, nombre, apellido) {
            Swal.fire({
                title: `¿Realmente desea habilitar al usuario ${nombre} ${apellido}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, habilitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "habilitar-usuario.php?id=" + id + "&confirmacion=si";
                }
            });
        }

        function deshabilitarUsuario(id, nombre, apellido) {
            Swal.fire({
                title: `¿Realmente desea deshabilitar al usuario ${nombre} ${apellido}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, deshabilitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "deshabilitar-usuario.php?id=" + id + "&confirmacion=si";
                }
            });
        }

        function confirmarEliminacion(id_usuario, nombre, apellido) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Realmente quiere eliminar a "${nombre} ${apellido}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { // Realizar la petición AJAX para eliminar el usuario
                    fetch('eliminar-usuario.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_usuario=${id_usuario}`
                    }).then(response => response.json()).then(data => {
                        if (data.status === "success") {
                            Swal.fire({
                                title: 'Éxito',
                                text: data.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    }).catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al eliminar el usuario. Intente nuevamente.',
                        });
                    });
                }
            });
        }

        function ConfirmarCreacionUsuario(id, nombre, apellido, ID_Usuario) {
            Swal.fire({
                title: `¿Realmente desea confirmar la creación del usuario ${nombre} ${apellido}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, habilitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar indicador de carga
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Por favor, espera un momento.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Hacer una solicitud AJAX para confirmar la creación del usuario
                    fetch(`confirmar-creacion-usuario.php?id=${id}&confirmacion=si&ID_Usuario=${ID_Usuario}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = "usuarios.php";
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.message
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



        function DenegarCreacionUsuario(id_usuario, nombre, apellido) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Realmente quiere denegar a "${nombre} ${apellido}"? , este usuario seria eliminado `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { // Realizar la petición AJAX para eliminar el usuario
                    fetch('eliminar-usuario.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_usuario=${id_usuario}`
                    }).then(response => response.json()).then(data => {
                        if (data.status === "success") {
                            Swal.fire({
                                title: 'Éxito',
                                text: data.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    }).catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al denegar al usuario. Intente nuevamente.',
                        });
                    });
                }
            });
        }
    </script>

    <?php if (isset($_GET['mensaje'])) {
        if ($_GET['mensaje'] == 'exito') {
            echo "<script> Swal.fire({ title: 'Usuario editado correctamente', icon: 'success', confirmButtonText: 'OK' }); </script>";
        } elseif ($_GET['mensaje'] == 'error' && isset($_GET['detalle'])) {
            echo "<script> Swal.fire({ title: 'Error al actualizar el usuario', text: '" . $_GET['detalle'] . "', icon: 'error', confirmButtonText: 'OK' }); </script>";
        }
    } ?>
</body>

</html>