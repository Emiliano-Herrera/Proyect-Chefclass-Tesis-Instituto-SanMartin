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

// Obtener el ID de la receta desde la URL
$receta_id = $_GET['id_receta'];

// Obtener los datos de la receta
$sql = "SELECT * FROM recetas WHERE id_receta = ?";
$statement = $conexion->prepare($sql);
$statement->bind_param("i", $receta_id);
$statement->execute();
$resultado = $statement->get_result();
$receta = $resultado->fetch_assoc();

// Obtener las categorías de la receta
$sql_categorias = "SELECT categoria_id FROM recetas_categorias WHERE receta_id = ?";
$statement_categorias = $conexion->prepare($sql_categorias);
$statement_categorias->bind_param("i", $receta_id);
$statement_categorias->execute();
$resultado_categorias = $statement_categorias->get_result();
$categorias_receta = [];
while ($fila = $resultado_categorias->fetch_assoc()) {
    $categorias_receta[] = $fila['categoria_id'];
}

// Obtener las instrucciones de la receta
$sql_instrucciones = "SELECT id_instruccion, paso, descripcion FROM instrucciones WHERE receta_id = ?";
$statement_instrucciones = $conexion->prepare($sql_instrucciones);
$statement_instrucciones->bind_param("i", $receta_id);
$statement_instrucciones->execute();
$resultado_instrucciones = $statement_instrucciones->get_result();
$instrucciones = [];
while ($fila = $resultado_instrucciones->fetch_assoc()) {
    $instrucciones[] = $fila;
}



// Obtener los ingredientes de la receta
$sql_ingredientes = "SELECT I.id_ingrediente AS id, I.nombre, RI.cantidad FROM ingredientes I JOIN recetas_ingredientes RI ON I.id_ingrediente = RI.ingrediente_id WHERE RI.receta_id = ?";
$statement_ingredientes = $conexion->prepare($sql_ingredientes);
$statement_ingredientes->bind_param("i", $receta_id);
$statement_ingredientes->execute();
$resultado_ingredientes = $statement_ingredientes->get_result();
$ingredientes = [];
while ($fila = $resultado_ingredientes->fetch_assoc()) {
    $ingredientes[] = $fila;
}




// Obtener las imágenes de la receta
$sql_imagenes = "SELECT IR.url_imagen FROM img_recetas IR JOIN imagenes_recetas I ON IR.id_img = I.img_id WHERE I.recetas_id = ?";
$statement_imagenes = $conexion->prepare($sql_imagenes);
$statement_imagenes->bind_param("i", $receta_id);
$statement_imagenes->execute();
$resultado_imagenes = $statement_imagenes->get_result();
$imagenes = [];
while ($fila = $resultado_imagenes->fetch_assoc()) {
    $imagenes[] = $fila['url_imagen'];
}
?>




<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Editar Receta</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                <div class="content-wrapper">

                    <?php if (isset($_GET['status'])) {
                        if ($_GET['status'] == 'success') {
                            echo "<script> Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'La receta se ha subido correctamente.', confirmButtonText: 'Aceptar' }); </script>";
                        } elseif ($_GET['status'] == 'error') {
                            echo "<script> Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al subir la receta. Por favor, inténtalo de nuevo.', confirmButtonText: 'Aceptar' }); </script>";
                        }
                    } ?>

                    <!-- Content -->
                    <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Administración / <a class="text-muted" href="vista-recetas.php">Recetas</a></span> / Editar Receta
                        </h4>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <h5 class="card-header">Formulario para subir una receta</h5>
                                    <div class="card-body">


                                        <form class="mb-3" id="editar-receta-form" action="editar-receta.php" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?php echo $receta_id; ?>">
                                            <div class="row">
                                                <!--//! 1. Datos de la receta -->
                                                <!-- Parte 1: Datos de la Receta -->
                                                <div class="col-12">
                                                    <h6>1. Datos de la receta</h6>
                                                    <hr class="mt-0" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="titulo">Título</label>
                                                    <input type="text" id="titulo" class="form-control" placeholder="Título" name="titulo" value="<?php echo htmlspecialchars($receta['titulo']); ?>" required />
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label" for="tiempo">Tiempo de preparación (HH:MM)</label>
                                                    <input type="time" id="tiempo" class="form-control" placeholder="Tiempo de preparación" name="tiempo" value="<?php echo htmlspecialchars($receta['tiempo_preparacion']); ?>" required />
                                                </div>
                                                <div class="col-md-12 mb-4">
                                                    <label class="form-label" for="descripcion">Descripción</label>
                                                    <textarea id="descripcion" class="form-control" placeholder="Describe brevemente la receta" name="descripcion" rows="3" required><?php echo htmlspecialchars($receta['descripcion']); ?></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Dificultad</label>
                                                    <div class="form-check custom mb-2">
                                                        <input type="radio" id="dificultad-facil" name="dificultad" class="form-check-input" value="Fácil" <?php echo ($receta['dificultad'] == 'Fácil') ? 'checked' : ''; ?> />
                                                        <label class="form-check-label" for="dificultad-facil">Fácil</label>
                                                    </div>
                                                    <div class="form-check custom mb-2">
                                                        <input type="radio" id="dificultad-intermedio" name="dificultad" class="form-check-input" value="Intermedio" <?php echo ($receta['dificultad'] == 'Intermedio') ? 'checked' : ''; ?> />
                                                        <label class="form-check-label" for="dificultad-intermedio">Intermedio</label>
                                                    </div>
                                                    <div class="form-check custom mb-2">
                                                        <input type="radio" id="dificultad-dificil" name="dificultad" class="form-check-input" value="Difícil" <?php echo ($receta['dificultad'] == 'Difícil') ? 'checked' : ''; ?> />
                                                        <label class="form-check-label" for="dificultad-dificil">Difícil</label>
                                                    </div>
                                                </div>

                                                <!--//! Parte 2: Selector de Categorías -->
                                                <!-- Parte del formulario de edición: Selector de Categorías -->
                                                <!-- Selector de Categorías -->
                                                <div class="col-md-6">
                                                    <label class="form-label" for="categoria">Categorías</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-select" id="categoria">
                                                            <option selected disabled>Selecciona una categoría...</option>
                                                            <?php
                                                            $receta_id = $_GET['id_receta'];

                                                            // Subconsulta para obtener las categorías ya asociadas con la receta
                                                            $sql_categoria_asociada = "SELECT categoria_id FROM recetas_categorias WHERE receta_id = ?";
                                                            $stmt_categoria_asociada = $conexion->prepare($sql_categoria_asociada);
                                                            $stmt_categoria_asociada->bind_param("i", $receta_id);
                                                            $stmt_categoria_asociada->execute();
                                                            $result_categoria_asociada = $stmt_categoria_asociada->get_result();
                                                            $categorias_asociadas = [];
                                                            while ($row_asociada = $result_categoria_asociada->fetch_assoc()) {
                                                                $categorias_asociadas[] = $row_asociada['categoria_id'];
                                                            }

                                                            // Consulta para obtener todas las categorías que no están asociadas con la receta
                                                            if (count($categorias_asociadas) > 0) {
                                                                $categorias_asociadas_list = implode(",", $categorias_asociadas);
                                                                $sql_categoria = "SELECT id_categoria, nombre FROM categoria WHERE id_categoria NOT IN ($categorias_asociadas_list)";
                                                            } else {
                                                                $sql_categoria = "SELECT id_categoria, nombre FROM categoria";
                                                            }

                                                            $result_categoria = $conexion->query($sql_categoria);
                                                            if ($result_categoria->num_rows > 0) {
                                                                while ($row = $result_categoria->fetch_assoc()) {
                                                                    echo "<option value='{$row['id_categoria']}'>{$row['nombre']}</option>";
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <button type="button" class="btn btn-outline-success" id="add-categoria-btn">Agregar Categoría</button>
                                                    </div>

                                                    <div id="selected-categorias">
                                                        <?php foreach ($categorias_receta as $cat_id): ?>
                                                            <?php
                                                            $sql_cat_nombre = "SELECT nombre FROM categoria WHERE id_categoria = ?";
                                                            $stmt_cat_nombre = $conexion->prepare($sql_cat_nombre);
                                                            $stmt_cat_nombre->bind_param("i", $cat_id);
                                                            $stmt_cat_nombre->execute();
                                                            $cat_nombre = $stmt_cat_nombre->get_result()->fetch_assoc()['nombre'];
                                                            ?>
                                                            <div class="input-group mb-2">
                                                                <input type="hidden" name="categoria_existente[]" value="<?php echo $cat_id; ?>">
                                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($cat_nombre); ?>" readonly>
                                                                <button class="btn btn-outline-danger remove-categoria-btn" type="button">Eliminar</button>
                                                                <input type="hidden" name="categorias_eliminar[]" value="false">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <input type="hidden" name="categorias_eliminar[]">
                                                </div>


                                                <!-- 2. Instrucciones de la receta -->
                                                <div class="col-12 mt-5">
                                                    <h6>2. Instrucciones de la receta</h6>
                                                    <hr class="mt-0" />
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label" for="instrucciones">Pasos</label>
                                                    <div id="instrucciones-container">
                                                        <?php foreach ($instrucciones as $instruccion): ?>
                                                            <div class="input-group mb-3">
                                                                <input type="hidden" name="instruccion_existente_id[]" value="<?php echo htmlspecialchars($instruccion['id_instruccion']); ?>">
                                                                <input type="number" class="form-control" name="num_pasos_existentes[]" placeholder="Número de paso" value="<?php echo htmlspecialchars($instruccion['paso']); ?>" required />
                                                                <input type="text" class="form-control" name="descripcion_pasos_existentes[]" placeholder="Descripción del paso" value="<?php echo htmlspecialchars($instruccion['descripcion']); ?>" required />
                                                                <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>
                                                                <input type="hidden" name="instrucciones_eliminar[]" value="false">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-success add-instruccion-btn">Añadir Paso</button>
                                                    <input type="hidden" name="instrucciones_eliminar[]">
                                                </div>


                                                <!-- 3. Ingredientes de la receta -->
                                                <div class="col-12 mt-5">
                                                    <h6 class="mt-2">3. Ingredientes de la receta</h6>
                                                    <hr class="mt-0" />
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label" for="ingredientes">Ingredientes</label>
                                                    <div id="ingredientes-container">
                                                        <?php foreach ($ingredientes as $ingrediente): ?>
                                                            <div class="input-group mb-3">
                                                                <input type="hidden" name="ingrediente_existente_id[]" value="<?php echo htmlspecialchars($ingrediente['id']); ?>">
                                                                <input type="text" class="form-control" name="nombre_ingredientes_existentes[]" placeholder="Ingrediente" value="<?php echo htmlspecialchars($ingrediente['nombre']); ?>" required />
                                                                <input type="text" class="form-control" name="cantidades_existentes[]" placeholder="Cantidad" value="<?php echo htmlspecialchars($ingrediente['cantidad']); ?>" required />
                                                                <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>
                                                                <input type="hidden" name="ingredientes_eliminar[]" value="false">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-success add-ingrediente-btn">Añadir Ingrediente</button>
                                                    <input type="hidden" name="ingredientes_eliminar[]">
                                                </div>




                                                <!-- 4. Sube imágenes o videos de tu receta -->
                                                <div class="col-12 mt-5">
                                                    <h6 class="mt-2">4. Subir fotos o videos de la receta</h6>
                                                    <hr class="mt-0" />
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" for="media_files">Subir fotos o videos</label>
                                                    <div id="archivos-subidos">
                                                        <?php foreach ($imagenes as $imagen): ?>
                                                            <div class="archivo mb-2">
                                                                <?php $file_path = "../../uploads" . htmlspecialchars($imagen); ?>
                                                                <?php if (file_exists($file_path)): ?>
                                                                    <?php if (preg_match('/\.(mp4|webm)$/i', $imagen)): ?>
                                                                        <video src="<?php echo $file_path; ?>" class="img-thumbnail" width="200" controls></video>
                                                                    <?php elseif (preg_match('/\.(jpg|jpeg|png|gif)$/i', $imagen)): ?>
                                                                        <img src="<?php echo $file_path; ?>" class="img-thumbnail" width="200">
                                                                    <?php endif; ?>
                                                                    <input type="file" name="cambiar_archivo[]" data-url="<?php echo $file_path; ?>" class="form-control mt-2">
                                                                    <button type="button" class="btn btn-outline-danger remove-archivo-btn">Eliminar</button>
                                                                    <input type="hidden" name="archivos_eliminar[]" value="false">
                                                                    <input type="hidden" name="archivo_url[]" value="<?php echo $file_path; ?>">
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-success add-archivo-btn">Añadir Archivo</button>
                                                    <input type="hidden" name="archivos_eliminar[]">
                                                </div>

                                                <div class="col-12 justify-content-between">
                                                    <button type="submit" class="btn btn-primary me-2">Guardar Cambios</button>
                                                    <a href="vista-recetas.php" class="btn btn-label-secondary">Cancelar</a>
                                                </div>
                                            </div>
                                        </form>





                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- / Content -->

                    <script>
                        $(document).ready(function() {
                            const recetaId = $('#receta-id').val(); // Asumiendo que tienes un campo oculto con el ID de la receta

                            $('#add-categoria-btn').click(function() {
                                const selectedCategoria = $('#categoria').val();
                                const selectedCategoriaText = $('#categoria option:selected').text();
                                if (selectedCategoria && !$(`#selected-categorias input[value="${selectedCategoria}"]`).length) {
                                    $('#selected-categorias').append(
                                        `<div class="input-group mb-2">
                                        <input type="hidden" name="categoria_nueva[]" value="${selectedCategoria}">
                                        <input type="hidden" name="receta_id[]" value="${recetaId}">
                                        <input type="text" class="form-control" value="${selectedCategoriaText}" readonly>
                                        <button class="btn btn-outline-danger remove-categoria-btn" type="button">Eliminar</button>
                                        <input type="hidden" name="categorias_eliminar[]" value="false">
                                    </div>`
                                    );
                                }
                            });

                            $(document).on('click', '.remove-categoria-btn', function() {
                                const hiddenInput = $(this).siblings('input[name="categorias_eliminar[]"]');
                                hiddenInput.val('true');
                                const categoriaId = $(this).siblings('input[name="categoria_existente[]"]').val();
                                console.log(`Eliminando categoría con ID: ${categoriaId}`);
                                $(this).closest('.input-group').hide();
                            });
                        });


                        /* //?INSTRUCCIONES============================================================================================================================ */
                        $(document).ready(function() {
                            $('.add-instruccion-btn').click(function() {
                                let container = $('#instrucciones-container');
                                let newInputGroup = $('<div class="input-group mb-3">');
                                newInputGroup.html(`
                                    <input type="number" class="form-control" name="num_pasos_nuevos[]" placeholder="Número de paso" required />
                                    <input type="text" class="form-control" name="descripcion_pasos_nuevos[]" placeholder="Descripción del paso" required />
                                    <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>
                                    <input type="hidden" name="instrucciones_nuevas_eliminar[]" value="false">
                                `);
                                container.append(newInputGroup);

                                newInputGroup.find('.remove-instruccion-btn').click(function() {
                                    console.log(`Eliminando instrucción nueva`);
                                    newInputGroup.find('input[name="instrucciones_nuevas_eliminar[]"]').val('true');
                                    newInputGroup.hide();
                                });
                            });

                            $(document).on('click', '.remove-instruccion-btn', function() {
                                const hiddenInput = $(this).siblings('input[name="instrucciones_eliminar[]"], input[name="instrucciones_nuevas_eliminar[]"]');
                                hiddenInput.val('true');
                                const instruccionId = $(this).siblings('input[name="instruccion_existente_id[]"]').val();
                                console.log(`Eliminando instrucción con ID: ${instruccionId}`);
                                $(this).closest('.input-group').hide();
                            });
                        });


                        /* //?INGREDIENTES============================================================================================================================ */
                        $(document).ready(function() {
                            $('.add-ingrediente-btn').click(function() {
                                let container = $('#ingredientes-container');
                                let newInputGroup = $('<div class="input-group mb-3">');
                                newInputGroup.html(`
                                        <input type="text" class="form-control" name="nombre_ingredientes_nuevos[]" placeholder="Ingrediente" required />
                                        <input type="text" class="form-control" name="cantidades_nuevas[]" placeholder="Cantidad" required />
                                        <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>
                                        <input type="hidden" name="ingredientes_nuevos_eliminar[]" value="false">
                                    `);
                                container.append(newInputGroup);

                                newInputGroup.find('.remove-ingrediente-btn').click(function() {
                                    console.log(`Eliminando ingrediente nuevo`);
                                    newInputGroup.find('input[name="ingredientes_nuevos_eliminar[]"]').val('true');
                                    newInputGroup.hide();
                                });
                            });

                            $(document).on('click', '.remove-ingrediente-btn', function() {
                                const hiddenInput = $(this).siblings('input[name="ingredientes_eliminar[]"], input[name="ingredientes_nuevos_eliminar[]"]');
                                hiddenInput.val('true');
                                const ingredienteId = $(this).siblings('input[name="ingrediente_existente_id[]"]').val();
                                console.log(`Eliminando ingrediente con ID: ${ingredienteId}`);
                                $(this).closest('.input-group').hide();
                            });
                        });





                        $(document).ready(function() {
                            $('.add-archivo-btn').click(function() {
                                let container = $('#archivos-subidos');
                                let newInputGroup = $('<div class="archivo mb-2">');
                                newInputGroup.html(`
                                    <input type="file" class="form-control mt-2" name="archivos[]" required />
                                    <button class="btn btn-outline-danger remove-archivo-btn" type="button">Eliminar</button>
                                    <input type="hidden" name="archivos_eliminar[]" value="false">
                                    <input type="hidden" name="archivo_url[]" value="">
                                `);
                                container.append(newInputGroup);

                                newInputGroup.find('.remove-archivo-btn').click(function() {
                                    console.log(`Eliminando archivo`);
                                    newInputGroup.find('input[name="archivos_eliminar[]"]').val('true');
                                    newInputGroup.hide();
                                });
                            });

                            $(document).on('click', '.remove-archivo-btn', function() {
                                const hiddenInput = $(this).siblings('input[name="archivos_eliminar[]"]');
                                hiddenInput.val('true');
                                const archivoUrl = $(this).siblings('input[name="archivo_url[]"]').val();
                                console.log(`Eliminando archivo con URL: ${archivoUrl}`);
                                $(this).closest('.archivo').hide();
                            });
                        });
                    </script>

                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                    <script>
                        // Definición de Palabras Prohibidas
                        const forbiddenWords = ["gay", "gil", "topo", "topoide"]; // Añade aquí las palabras que quieres prohibir

                        // Función para Verificar Palabras Prohibidas
                        function containsForbiddenWords(text) {
                            for (let word of forbiddenWords) {
                                if (text.includes(word)) {
                                    return true;
                                }
                            }
                            return false;
                        }

                        // Función para Validar el Formulario
                        function validateForm(event) {
                            const titulo = document.getElementById('titulo').value;
                            const descripcion = document.getElementById('descripcion').value;
                            const instrucciones = Array.from(document.querySelectorAll('input[name="pasos[]"]')).map(input => input.value);
                            const ingredientes = Array.from(document.querySelectorAll('input[name="ingredientes[]"]')).map(input => input.value);

                            let invalidFields = [];

                            if (containsForbiddenWords(titulo)) invalidFields.push('Título');
                            if (containsForbiddenWords(descripcion)) invalidFields.push('Descripción');
                            if (instrucciones.some(containsForbiddenWords)) invalidFields.push('Instrucciones');
                            if (ingredientes.some(containsForbiddenWords)) invalidFields.push('Ingredientes');

                            if (invalidFields.length > 0) {
                                event.preventDefault();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: `Los siguientes campos contienen palabras no permitidas: ${invalidFields.join(', ')}.`,
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        }

                        // Asignación del Evento de Validación al Formulario
                        /* document.getElementById('formValidationExamples').addEventListener('submit', validateForm); */
                    </script>







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
        <!-- <script src="../assets/vendor/libs/hammer/hammer.js"></script>
        <script src="../assets/vendor/libs/i18n/i18n.js"></script>
        <script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script> -->
        <!-- endbuild -->

        <!-- Vendors JS -->
        <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

        <!-- <script src="../assets/vendor/libs/dropzone/dropzone.js"></script> -->

        <!-- Page JS -->
        <!-- <script src="../assets/js/forms-file-upload.js"></script> -->

        <!-- Main JS -->
        <script src="../assets/js/main.js"></script>

        <!-- Page JS -->
        <script src="../assets/js/dashboards-analytics.js"></script>

        <!-- Place this tag in your head or just before your close body tag. -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>