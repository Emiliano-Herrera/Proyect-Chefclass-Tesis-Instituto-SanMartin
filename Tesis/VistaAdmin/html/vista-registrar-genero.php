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
?>

<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Registrar Género</title>

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

                    // Archivos que deben marcar "Usuarios" como activo
                    $recetas_activos = [
                        'vista-editar-genero.php',
                        'vista-registrar-genero.php'
                    ];

                    // Mostrar solo las secciones permitidas para el rol
                    foreach ($secciones_menu as $id => $info) {
                        if (in_array($id, $secciones_permitidas)) {
                            // Para la sección Usuarios, marcar activo si el archivo actual está en $usuarios_activos
                            if ($id == 4) {
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

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">

                            </div>
                        </div>
                        <!-- //?Search =============================================-->
                        <!-- //?NAV MENÚ =============================================-->
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

                <!-- //!Content wrapper MAIN MENÚ==================================================================================== -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- !AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Administración / <a class="text-muted" href="generos.php">Géneros /</a></span> Registrar Género
                        </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">Agregar Género</h5>
                                    <!-- Account -->
                                    <hr class="my-0">
                                    <div class="card-body">

                                        <form id="formAuthentication" class="mb-3" method="POST">
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="nombre" class="form-label">Nombre del género</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresá el género" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo se permiten letras" required />
                                                </div>
                                                <div class="d-flex justify-content-center pt-3">
                                                    <button type="button" id="registrar-genero-btn" class="btn btn-primary me-2">Registrar Género</button>
                                                    <a href="generos.php" class="btn btn-label-danger suspend-user">Cancelar y volver</a>
                                                </div>
                                            </div>
                                        </form>



                                    </div>
                                    <!-- /Account -->
                                </div>
                            </div>
                        </div>
                    </div>


                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        // Función para permitir solo letras y espacios en el nombre del género
                        document.getElementById("nombre").addEventListener("input", function() {
                            this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
                        });

                        // Validar formulario completo y enviar datos mediante AJAX
                        function validarFormularioGenero(event) {
                            event.preventDefault(); // Prevenir el envío automático del formulario

                            const nombre = document.getElementById('nombre').value.trim();
                            // Validar nombre
                            if (nombre === '') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'El nombre del género es obligatorio.',
                                });
                                return;
                            }

                            // Comprobar si el género ya existe en la base de datos
                            fetch('comprobar-genero.php', {
                                method: 'POST',
                                body: JSON.stringify({
                                    nombre: nombre
                                }),
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            }).then(response => response.json()).then(data => {
                                if (data.exists) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'El género ya existe en la base de datos.',
                                    });
                                } else {
                                    // Si no existe, proceder a registrar el género
                                    const formData = new FormData(document.getElementById('formAuthentication'));

                                    fetch('registrar-genero.php', {
                                        method: 'POST',
                                        body: formData
                                    }).then(response => response.json()).then(data => {
                                        if (data.status === 'success') {
                                            Swal.fire({
                                                title: 'Éxito',
                                                text: `${data.message}`,
                                                icon: 'success'
                                            }).then(() => {
                                                window.location.href = 'generos.php';
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: data.message
                                            });
                                        }
                                    }).catch(error => {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Hubo un problema al registrar el género. Intente nuevamente.'
                                        });
                                    });
                                }
                            }).catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Hubo un problema al comprobar el género. Intente nuevamente.'
                                });
                            });
                        }

                        // Asociar la función validarFormularioGenero al evento click del botón
                        document.getElementById('registrar-genero-btn').addEventListener('click', validarFormularioGenero);
                    </script>








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