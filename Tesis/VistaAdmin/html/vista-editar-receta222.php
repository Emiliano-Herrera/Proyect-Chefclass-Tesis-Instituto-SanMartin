<?php
session_start();
include("conexion.php");
// Obtener el ID de la receta desde la URL
$receta_id = $_GET['receta_id'];

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
$sql_instrucciones = "SELECT * FROM instrucciones WHERE receta_id = ?";
$statement_instrucciones = $conexion->prepare($sql_instrucciones);
$statement_instrucciones->bind_param("i", $receta_id);
$statement_instrucciones->execute();
$resultado_instrucciones = $statement_instrucciones->get_result();
$instrucciones = [];
while ($fila = $resultado_instrucciones->fetch_assoc()) {
    $instrucciones[] = $fila;
}

// Obtener los ingredientes de la receta
$sql_ingredientes = "SELECT I.nombre, I.cantidad FROM ingredientes I JOIN recetas_ingredientes RI ON I.id_ingrediente = RI.ingrediente_id WHERE RI.receta_id = ?";
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
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title></title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                    <li class="menu-item active">
                        <a href="perfil.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div class="text-truncate" data-i18n="Users">Mi perfil</div>
                        </a>
                    </li>

                    <!-- //!GESTION DE REGISTROS -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Gestión</span>
                    </li>

                    <li class="menu-item ">
                        <a href="usuarios.php" class="menu-link">
                            <i class='menu-icon bx bxs-user-detail'></i>
                            <div data-i18n="Basic Inputs">Usuarios</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="roles.php" class="menu-link">
                            <i class='menu-icon bx bxs-user-badge'></i>
                            <div data-i18n="Input groups">Roles</div>
                        </a>
                    </li>

                    <li class="menu-item ">
                        <a href="Servicios.php" class="menu-link">
                            <!-- <i class='menu-icon bx bx-donate-blood'></i> -->
                            <i class='menu-icon bx bxs-food-menu'></i>
                            <div data-i18n="Account Settings">Recetas</div>
                        </a>
                    </li>

                    <li class="menu-item ">
                        <a href="generos.php" class="menu-link">

                            <i class='bx bx-male-female'></i>
                            <div data-i18n="Basic Inputs">Generos</div>
                        </a>
                    </li>

                    <!-- //!======================================================================================================= -->



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
                                        <a class="dropdown-item" href="todasDirecciones.php">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Mapa</span>
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

                        <h4 class="py-3 mb-4"><span class="text-muted fw-light"><a href="perfil.php">Perfil</a> /</span> Subir receta
                        </h4>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <h5 class="card-header">Formulario para subir una receta</h5>
                                    <div class="card-body">


                                        <form id="formValidationExamples" class="row g-3" method="POST" action="editar-receta.php" enctype="multipart/form-data"> <!-- 1. Datos de la receta -->

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
                                            <div class="col-md-6">
                                                <label class="form-label" for="tiempo">Tiempo de preparación (HH:MM)</label>
                                                <input type="time" id="tiempo" class="form-control" placeholder="Tiempo de preparación" name="tiempo" value="<?php echo htmlspecialchars($receta['tiempo_preparacion']); ?>" required />
                                            </div>

                                            <div class="col-md-12">
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
                                            <div class="col-md-6">
                                                <label class="form-label" for="categoria">Categorías</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-select" id="categoria" name="categoria[]">
                                                        <?php
                                                        // Obtener las categorías de la base de datos
                                                        $sql_categoria = "SELECT id_categoria, nombre FROM categoria";
                                                        $result_categoria = $conexion->query($sql_categoria);

                                                        // Crear una lista de categorías
                                                        $categorias = [];
                                                        while ($row = $result_categoria->fetch_assoc()) {
                                                            $categorias[] = $row;
                                                        }

                                                        // Mostrar la categoría actual primero
                                                        foreach ($categorias_receta as $cat_id) {
                                                            foreach ($categorias as $key => $categoria) {
                                                                if ($categoria['id_categoria'] == $cat_id) {
                                                                    echo "<option value='{$categoria['id_categoria']}' selected>{$categoria['nombre']}</option>";
                                                                    unset($categorias[$key]);
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        // Mostrar las otras categorías
                                                        foreach ($categorias as $categoria) {
                                                            echo "<option value='{$categoria['id_categoria']}'>{$categoria['nombre']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div id="selected-categorias">
                                                    <?php
                                                    // Mostrar las categorías seleccionadas como etiquetas
                                                    foreach ($categorias_receta as $cat_id) {
                                                        $sql_cat_nombre = "SELECT nombre FROM categoria WHERE id_categoria = $cat_id";
                                                        $result_cat_nombre = $conexion->query($sql_cat_nombre);
                                                        $cat_nombre = $result_cat_nombre->fetch_assoc()['nombre'];
                                                    }
                                                    ?>
                                                </div>
                                            </div>











                                            <!-- 2. Instrucciones de la receta -->
                                            <!-- Parte 3: Instrucciones de la Receta -->
                                            <div class="col-12 mt-5">
                                                <h6>2. Instrucciones de la receta</h6>
                                                <hr class="mt-0" />
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label" for="instrucciones">Pasos</label>
                                                <div id="instrucciones-container">
                                                    <?php foreach ($instrucciones as $instruccion): ?>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso" value="<?php echo htmlspecialchars($instruccion['paso']); ?>" required />
                                                            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" value="<?php echo htmlspecialchars($instruccion['descripcion']); ?>" required />
                                                            <button class="btn btn-outline-success add-instruccion-btn" type="button">Agregar paso</button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>



                                            <!-- 3. Ingredientes de la receta -->
                                            <!-- Parte 4: Ingredientes de la receta -->
                                            <div class="col-12 mt-5">
                                                <h6 class="mt-2">3. Ingredientes de la receta</h6>
                                                <hr class="mt-0" />
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label" for="ingredientes">Ingredientes</label>
                                                <div id="ingredientes-container">
                                                    <?php foreach ($ingredientes as $ingrediente): ?>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" value="<?php echo htmlspecialchars($ingrediente['nombre']); ?>" required />
                                                            <input type="text" class="form-control" name="cantidades[]" placeholder="Cantidad" value="<?php echo htmlspecialchars($ingrediente['cantidad']); ?>" required />
                                                            <button class="btn btn-outline-success add-ingrediente-btn" type="button">Agregar ingrediente</button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>



                                            <!-- Subir fotos o videos de la receta -->
                                            <!-- Parte 5: Mostrar archivos multimedia subidos -->
                                            <!-- Parte 4: Archivos Subidos -->
                                            <!-- Parte 4: Archivos Subidos -->
                                            <!-- Parte 4: Archivos Subidos -->
                                            <!-- Mostrar imágenes y videos actuales -->
                                            <div class="col-12 mt-5">
                                                <h6 class="mt-2">Archivos Actuales</h6>
                                                <hr class="mt-0" />
                                            </div>
                                            <div class="col-12 text-center" id="archivos-actuales">
                                                <?php if (empty($imagenes)): ?>
                                                    <p>No hay imágenes o videos para esta receta.</p>
                                                <?php else: ?>
                                                    <?php foreach ($imagenes as $imagen): ?>
                                                        <div class="archivo mb-2 d-inline-block">
                                                            <?php
                                                            // Verificar si la ruta ya contiene 'uploads/'
                                                            $ruta_archivo = (strpos($imagen, 'uploads/') === 0) ? $imagen : 'uploads/' . $imagen;
                                                            ?>
                                                            <?php if (file_exists($ruta_archivo)): ?>
                                                                <?php if (preg_match('/\.(mp4|webm)$/i', $imagen)): ?>
                                                                    <!-- Si el archivo es un video -->
                                                                    <video src="<?php echo htmlspecialchars($ruta_archivo); ?>" class="img-thumbnail" width="200" height="200" style="object-fit: cover;" controls></video>
                                                                <?php elseif (preg_match('/\.(jpg|jpeg|png|gif)$/i', $imagen)): ?>
                                                                    <!-- Si el archivo es una imagen -->
                                                                    <img src="<?php echo htmlspecialchars($ruta_archivo); ?>" class="img-thumbnail" width="200" height="200" style="object-fit: cover;">
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <p>No se encontró el archivo: <?php echo htmlspecialchars($ruta_archivo); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>








                                            <!-- Subir nuevas fotos o videos -->
                                            <div class="col-12 mt-5">
                                                <h6 class="mt-2">5. Subir fotos o videos de la receta</h6>
                                                <hr class="mt-0" />
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="media_files">Subir fotos o videos</label>
                                                <div class="input-group mb-3">
                                                    <label class="input-group-text" for="inputGroupFile01">Subir</label>
                                                    <input type="file" class="form-control" id="inputGroupFile01" name="media_files[]" accept="image/*,video/*" multiple>
                                                </div>
                                            </div>
                                            <div class="col-12 justify-content-between">
                                                <button type="submit" class="btn btn-primary me-2">Guardar Cambios</button>
                                                <a href="perfil.php" class="btn btn-label-secondary">Cancelar</a>
                                            </div>



                                        </form>

                                        <script>
                                            $(document).ready(function() {
                                                $('#add-categoria-btn').click(function() {
                                                    const selectedCategoria = $('#categoria').val();
                                                    const selectedCategoriaText = $('#categoria option:selected').text();
                                                    if (selectedCategoria && !$(`#selected-categorias input[value="${selectedCategoria}"]`).length) {
                                                        $('#selected-categorias').append(`<div class="input-group mb-2"> <input type="hidden" name="categoria[]" value="${selectedCategoria}"> <input type="text" class="form-control" value="${selectedCategoriaText}" readonly> <button class="btn btn-outline-danger remove-categoria-btn" type="button">Eliminar</button> </div>`);
                                                    }
                                                });
                                                $(document).on('click', '.remove-categoria-btn', function() {
                                                    $(this).parent().remove();
                                                });
                                            });

                                            // Agregar paso de instrucción
                                            document.querySelector('.add-instruccion-btn').addEventListener('click', function() {
                                                let container = document.getElementById('instrucciones-container');
                                                let newInputGroup = document.createElement('div');
                                                newInputGroup.className = 'input-group mb-3';
                                                newInputGroup.innerHTML = `<input type="number" class="form-control" name="num_pasos[]" placeholder="Número de paso" required />
                                                                            <input type="text" class="form-control" name="pasos[]" placeholder="Descripción del paso" required />
                                                                            <button class="btn btn-outline-danger remove-instruccion-btn" type="button">Eliminar</button>`;
                                                container.appendChild(newInputGroup);

                                                // Agregar evento de eliminar paso
                                                newInputGroup.querySelector('.remove-instruccion-btn').addEventListener('click', function() {
                                                    container.removeChild(newInputGroup);
                                                });
                                            });

                                            // Agregar ingrediente
                                            document.querySelector('.add-ingrediente-btn').addEventListener('click', function() {
                                                let container = document.getElementById('ingredientes-container');
                                                let newInputGroup = document.createElement('div');
                                                newInputGroup.className = 'input-group mb-3';
                                                newInputGroup.innerHTML = `<input type="text" class="form-control" name="ingredientes[]" placeholder="Ingrediente" required />
                                                                            <input type="text" class="form-control" name="cantidades[]" placeholder="Cantidad" required />
                                                                            <button class="btn btn-outline-danger remove-ingrediente-btn" type="button">Eliminar</button>`;
                                                container.appendChild(newInputGroup);

                                                // Agregar evento de eliminar ingrediente
                                                newInputGroup.querySelector('.remove-ingrediente-btn').addEventListener('click', function() {
                                                    container.removeChild(newInputGroup);
                                                });
                                            });
                                        </script>



                                        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

                                        <script>
                                            //Definición de Palabras Prohibidas
                                            const forbiddenWords = ["gay", "gil", "topo", "topoide"]; // Añade aquí las palabras que quieres prohibir

                                            //Función para Verificar Palabras Prohibidas Esta función toma una cadena de texto (text) como argumento 
                                            // y verifica si contiene alguna de las palabras prohibidas. Si encuentra alguna palabra prohibida, 
                                            //devuelve true, de lo contrario, devuelve false.
                                            function containsForbiddenWords(text) {
                                                for (let word of forbiddenWords) {
                                                    if (text.includes(word)) {
                                                        return true;
                                                    }
                                                }
                                                return false;
                                            }

                                            // Función para Validar el Formulario
                                            /* Obtención de Valores: Se obtienen los valores de los campos del formulario 
                                            (título, descripción, instrucciones y ingredientes).

                                            Lista de Campos Inválidos: Se crea una lista para almacenar los nombres de los campos que 
                                            contienen palabras prohibidas.

                                            Verificación de Palabras Prohibidas: Se verifica si cada campo contiene alguna de las 
                                            palabras prohibidas usando la función containsForbiddenWords. 
                                            Si se encuentra una palabra prohibida en un campo, se añade el nombre del campo a la lista invalidFields.

                                            Mostrar Alerta y Prevenir Envío: Si hay campos inválidos 
                                            (es decir, invalidFields no está vacío), se previene el envío del formulario (event.preventDefault()) 
                                            y se muestra una alerta de error usando SweetAlert2, indicando los campos que contienen palabras no permitidas. */
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

                                            //Asignación del Evento de Validación al Formulario
                                            document.getElementById('formValidationExamples').addEventListener('submit', validateForm);
                                        </script>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Añadir categoría
                                                document.getElementById('add-categoria-btn').addEventListener('click', function() {
                                                    const select = document.getElementById('categoria');
                                                    const selectedOption = select.options[select.selectedIndex];
                                                    const selectedValue = selectedOption.value;
                                                    const selectedText = selectedOption.text;

                                                    if (selectedValue !== "Selecciona una categoría...") {
                                                        const categoriasContainer = document.getElementById('selected-categorias');
                                                        const newCategoria = document.createElement('span');
                                                        newCategoria.classList.add('badge', 'bg-primary', 'me-1');
                                                        newCategoria.setAttribute('data-id', selectedValue);
                                                        newCategoria.innerHTML = `${selectedText} <button type='button' class='btn btn-sm btn-outline-danger remove-categoria-btn'>&times;</button>`;

                                                        categoriasContainer.appendChild(newCategoria);

                                                        // Añadir event listener al nuevo botón de eliminar
                                                        newCategoria.querySelector('.remove-categoria-btn').addEventListener('click', function() {
                                                            newCategoria.remove();
                                                        });
                                                    }
                                                });

                                                // Eliminar categoría existente
                                                document.querySelectorAll('.remove-categoria-btn').forEach(button => {
                                                    button.addEventListener('click', function() {
                                                        this.closest('.badge').remove();
                                                    });
                                                });
                                            });
                                        </script>


                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Eliminar archivo visual (imagen o video)
                                                document.querySelectorAll('.remove-archivo-btn').forEach(button => {
                                                    button.addEventListener('click', function() {
                                                        this.closest('.archivo').remove();
                                                    });
                                                });

                                                // Añadir nueva lógica para eliminar archivo visual
                                                const archivosContainer = document.getElementById('archivos-subidos');

                                                // Lógica para añadir nuevos archivos puede ir aquí si se necesita

                                                // Eliminar archivo visual
                                                document.querySelectorAll('.remove-archivo-btn').forEach(button => {
                                                    button.addEventListener('click', function() {
                                                        this.closest('.archivo').remove();
                                                    });
                                                });
                                            });
                                        </script>


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