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

    $id_seccion_generos = 4; // ID de la sección Géneros

    $sql_permiso_generos = "SELECT permisos FROM roles_permisos_secciones WHERE id_rol = $RolId AND id_seccion = $id_seccion_generos";
    $result_permiso_generos = $conexion->query($sql_permiso_generos);
    $permisos_generos = [];
    if ($row_permiso_generos = $result_permiso_generos->fetch_assoc()) {
        $permisos_generos = explode(',', str_replace("'", "", $row_permiso_generos['permisos']));
    }
} else {
    header("Location: Login.php");
    exit();
}
$nombre_usuario = $_SESSION['nombre'];
?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Géneros</title>

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
    <link rel="stylesheet" href="../assets/vendor/css//rtl/theme-default.css" class="template-customizer-theme-css" />
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
                    <li class="menu-item ">
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
                $resultadosPorPagina = 15; // Establece la cantidad de resultados por página
                $totalResultados = $conexion->query("SELECT COUNT(*) as total FROM generos")->fetch_assoc()['total'];
                $totalPaginas = ceil($totalResultados / $resultadosPorPagina);

                // Obtiene la página actual, si no se establece, se asume la página 1
                $paginaActual = isset($_GET['page']) ? $_GET['page'] : 1;

                // Calcula el índice de inicio para la consulta
                $inicio = ($paginaActual - 1) * $resultadosPorPagina;

                /* //?----------------------------------------------------------------- */
                // Obtén el término de búsqueda si está presente en la URL
                $term = isset($_GET['search']) ? $_GET['search'] : '';

                // Añade la condición WHERE para filtrar por Nombre de genero
                $whereCondition = !empty($term) ? "WHERE nombre_genero LIKE '%$term%' " : '';

                $sql = "SELECT * FROM generos
                $whereCondition
                LIMIT $inicio, $resultadosPorPagina";


                $result = $conexion->query($sql);

                $totalGeneros = $conexion->query("SELECT COUNT(*) as total FROM generos ")->fetch_assoc()['total'];




                if (!$result) {
                    die("Error en la consulta: " . $conexion->error);
                }


                // Procesar los resultados y mostrar en la tabla
                ?>
                <!-- //!Content wrapper MAIN MENÚ==================================================================================== -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <!-- //!AQUÍ COMIENZA EL CONTENIDO DEL MAIN====================================================================================== -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">Administración /</span> Géneros
                        </h4>
                        <!-- Hoverable Table rows -->
                        <div class="card">



                            <div class="card-header border-bottom">


                                <?php if (in_array('crear', $permisos_generos)): ?>
                                    <h6 class="card-title">Añadir un género</h6>
                                    <a href="./vista-registrar-genero.php" class="btn btn-primary">+ nuevo género</a>
                                <?php endif; ?>
                                <br>
                                <hr>
                                <form action="" method="GET" class="row g-3">
                                    <div class="col-auto">
                                        <input type="text" name="search" id="search" class="form-control mx-2" placeholder="Nombre del género">

                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">Buscar<i class="bi bi-search m-2"></i></button>
                                    </div>
                                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                        <div class="col-auto">
                                            <a href="generos.php" class="btn btn-info">Ver todos</a>
                                        </div>
                                    <?php endif; ?>
                                </form>

                            </div>

                            <!-- //!funcion para  la confirmacion de eliminacion================================================= -->
                            <script>
                                function confirmacion() {
                                    var respuesta = confirm(" ¿ Desea realmente eliminar ?")
                                    if (respuesta == true) {
                                        return true
                                    } else {
                                        return false
                                    }
                                }
                            </script>
                            <!-- fin de funcion -->


                            <hr>
                            <!-- //!TABLA================================================= -->
                            <!-- Ahora va un nav bard -->
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover" id="tabla-empleados">
                                    <thead>
                                        <tr>
                                            <th>Géneros</th>




                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class='table-border-bottom-0'>
                                        <?php while ($row = $result->fetch_assoc()) : ?>
                                            <tr>

                                                <td><?= $row['nombre_genero'] ?></td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <?php if (in_array('editar', $permisos_generos)): ?>
                                                                <a class="dropdown-item" href="vista-editar-genero.php?Id=<?php echo $row['id_genero'] ?>">
                                                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (in_array('eliminar', $permisos_generos)): ?>
                                                                <a class="dropdown-item eliminar-genero" href="javascript:void(0);" data-id="<?php echo $row['id_genero']; ?>" data-nombre="<?php echo $row['nombre_genero']; ?>">
                                                                    <i class="bx bx-trash me-1"></i> Eliminar
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>


                                <!-- Mostrar el total de generos -->
                                <div class="d-flex justify-content-end">
                                    <p>Total de géneros: <strong><?= $totalGeneros ?></strong></p>
                                </div>

                                <?php

                                $paginationUrl = !empty($term) ? "&search=$term" : '';

                                // Muestra la paginación dentro del div
                                echo "<div class='d-flex justify-content-center'>";
                                echo "<ul class='pagination'>";

                                // Botón "Anterior"
                                echo "<li class='page-item " . ($paginaActual == 1 ? 'disabled' : '') . "'>";
                                /* echo "<a class='page-link' href='?page=" . ($paginaActual - 1) . "' aria-label='Previous'>"; */
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
                                /* echo "<a class='page-link' href='?page=" . ($paginaActual + 1) . "' aria-label='Next'>"; */
                                echo "<a class='page-link' href='?page=" . ($paginaActual + 1) . "$paginationUrl' aria-label='Next'>";
                                echo "<span aria-hidden='true'>&raquo;</span>";
                                echo "</a>";
                                echo "</li>";

                                echo "</ul>";
                                echo "</div>";

                                $conexion->close();
                                ?>

                            </div>
                        </div>
                        <!--/ Hoverable Table rows -->

                    </div>
                    <!-- / Content -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- //!======================================================================================================== -->







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

            echo "<script> window.onload = function() { Swal.fire({ icon: 'warning', title: 'Campo vacío', text: 'Por favor ingresa un nombre de genero para buscar.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'generos.php'; }); } </script>";
            exit;
        } elseif ($result->num_rows == 0) {
            // Si no se encontraron resultados
            echo "<script> window.onload = function() { Swal.fire({ icon: 'info', title: 'Sin resultados', text: 'No se encontró ningún genero con ese nombre.', 
                        confirmButtonColor: '#3085d6', allowOutsideClick: false }).then(() => { window.location.href = 'generos.php'; }); } </script>";
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.eliminar-genero').on('click', function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const usuario = '<?php echo $nombre_usuario; ?>';
                Swal.fire({
                    title: `${usuario} ¿Realmente desea eliminar el género "${nombre}"?`,
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    backdrop: true, // Asegura que la alerta sea modal
                    allowOutsideClick: false, // Desactiva clicks fuera de la alerta
                    allowEscapeKey: false, // Desactiva la tecla ESC
                    allowEnterKey: false, // Desactiva la tecla Enter
                    // Añadido para bloquear interacción
                    stopKeydownPropagation: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('eliminar-genero.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_genero=${id}`
                        }).then(response => response.json()).then(data => {
                            if (data.status === "success") {
                                console.log('Género eliminado con éxito'); // Mensaje de depuración
                                Swal.fire({
                                    title: 'Éxito',
                                    text: data.message,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                console.log('Error al eliminar el género:', data.message); // Mensaje de depuración
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                });
                            }
                        }).catch(error => {
                            console.error('Hubo un problema al eliminar el género:', error); // Mensaje de depuración
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un problema al eliminar el género. Intente nuevamente.',
                            });
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>