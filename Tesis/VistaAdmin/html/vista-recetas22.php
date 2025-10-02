<?php
session_start();

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
} else {
    // Redirigir a la página de inicio de sesión si no hay una sesión activa
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

    <title>Incio</title>

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
                    <a href="index.php" class="app-brand-link">
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
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">CheffClas</span>
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
                        <a href="index.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Incio</div>
                        </a>
                    </li>

                    <!-- //!MI PERFIL================================================================================================== -->

                    <li class="menu-item">
                        <a href="perfil.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div class="text-truncate" data-i18n="Users">Mi perfil</div>
                        </a>
                    </li>

                    <!-- //!GESTION DE REGISTROS -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Gestión</span>
                    </li>
                    <li class="menu-item">
                        <a href="Cursos.php" class="menu-link">
                            <i class='menu-icon bx bxs-user-detail'></i>
                            <div data-i18n="Account Settings">Usuarios</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="Talleres.php" class="menu-link">
                            <!-- <i class='menu-icon bx bx-clipboard'></i> -->
                            <i class='menu-icon bx bxs-user-badge'></i>
                            <div data-i18n="Account Settings">Roles</div>
                        </a>
                    </li>
                    <li class="menu-item active">
                        <a href="Servicios.php" class="menu-link">
                            <!-- <i class='menu-icon bx bx-donate-blood'></i> -->
                            <i class='menu-icon bx bxs-food-menu'></i>
                            <div data-i18n="Account Settings">Recetas</div>
                        </a>
                    </li>

                    <!-- //!======================================================================================================= -->






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

                        <div class="row">
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card card-border-shadow-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2 pb-1">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bxs-truck"></i></span>
                                            </div>
                                            <h4 class="ms-1 mb-0">42</h4>
                                        </div>
                                        <p class="mb-1">Tabla de recetas</p>
                                        <p class="mb-0">
                                            <span class="fw-medium me-1">+18.2%</span>
                                            <small class="text-muted">than last week</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card card-border-shadow-warning h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2 pb-1">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-error'></i></span>
                                            </div>
                                            <h4 class="ms-1 mb-0">8</h4>
                                        </div>
                                        <p class="mb-1">Vehicles with errors</p>
                                        <p class="mb-0">
                                            <span class="fw-medium me-1">-8.7%</span>
                                            <small class="text-muted">than last week</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card card-border-shadow-danger h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2 pb-1">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-git-repo-forked'></i></span>
                                            </div>
                                            <h4 class="ms-1 mb-0">27</h4>
                                        </div>
                                        <p class="mb-1">Deviated from route</p>
                                        <p class="mb-0">
                                            <span class="fw-medium me-1">+4.3%</span>
                                            <small class="text-muted">than last week</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card card-border-shadow-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2 pb-1">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded bg-label-info"><i class='bx bx-time-five'></i></span>
                                            </div>
                                            <h4 class="ms-1 mb-0">13</h4>
                                        </div>
                                        <p class="mb-1">Late vehicles</p>
                                        <p class="mb-0">
                                            <span class="fw-medium me-1">-2.5%</span>
                                            <small class="text-muted">than last week</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ! -->

                        <div class="row">
                            <!-- Popularidad por categoría -->
                            <div class="col-xxl-6 mb-4 order-5 order-xxl-0">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0">Popularidad por categoría</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-none d-lg-flex vehicles-progress-labels mb-3">
                                            <div class="vehicles-progress-label on-the-way-text" style="width: 39.7%;"></div>
                                            <div class="vehicles-progress-label unloading-text" style="width: 28.3%;"></div>
                                            <div class="vehicles-progress-label loading-text" style="width: 17.4%;"></div>
                                            <div class="vehicles-progress-label waiting-text" style="width: 14.6%;"></div>
                                        </div>
                                        <!-- //! AQUI VA EL PORCENTAJE DE POPULARIDAD DE CADA CATEGORÍA -->
                                        <div class="vehicles-overview-progress progress rounded-2 mb-3" style="height: 46px;">
                                            <div class="progress-bar fs-big fw-medium text-start bg-lighter text-body px-1 px-lg-3 rounded-start shadow-none" role="progressbar" style="width: 39.7%" aria-valuenow="39.7" aria-valuemin="0" aria-valuemax="100">39.7%</div>
                                            <div class="progress-bar fs-big fw-medium text-start bg-primary px-1 px-lg-3 shadow-none" role="progressbar" style="width: 28.3%" aria-valuenow="28.3" aria-valuemin="0" aria-valuemax="100">28.3%</div>
                                            <div class="progress-bar fs-big fw-medium text-start text-bg-info px-1 px-lg-3 shadow-none" role="progressbar" style="width: 17.4%" aria-valuenow="17.4" aria-valuemin="0" aria-valuemax="100">17.4%</div>
                                            <div class="progress-bar fs-big fw-medium text-start bg-gray-900 px-1 px-lg-3 rounded-end shadow-none" role="progressbar" style="width: 14.6%" aria-valuenow="14.6" aria-valuemin="0" aria-valuemax="100">14.6%</div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table card-table">
                                                <tbody class="table-border-bottom-0">
                                                    <tr>
                                                        <td class="w-50 ps-0">
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <div class="me-2">
                                                                    <i class="bi bi-list"></i>
                                                                </div>
                                                                <h6 class="mb-0 fw-normal">Categoría</h6>
                                                            </div>
                                                        </td>
                                                        <td class="text-end pe-0 text-nowrap">
                                                            <h6 class="mb-0 fw-normal">Popularidad de búsqueda <i class='bx bx-search'></i></h6>
                                                        </td>
                                                        <!-- <td class="text-end pe-0">
                                                            <span class="fw-medium">39.7%</span>
                                                        </td> -->
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 ps-0">
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <div class="me-2">
                                                                    <i class="bi bi-cloud-fog-fill"></i>
                                                                </div>
                                                                <h6 class="mb-0 fw-normal">Postres</h6>
                                                            </div>
                                                        </td>
                                                        <td class="text-end pe-0 text-nowrap">
                                                            <h6 class="mb-0">28.3%</h6>
                                                        </td>
                                                        <!-- <td class="text-end pe-0">
                                                            <span class="fw-medium">28.3%</span>
                                                        </td> -->
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 ps-0">
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <div class="me-2">
                                                                    <i class="bi bi-cloud-fog-fill"></i>
                                                                </div>
                                                                <h6 class="mb-0 fw-normal">Panadería</h6>
                                                            </div>
                                                        </td>
                                                        <td class="text-end pe-0 text-nowrap">
                                                            <h6 class="mb-0">17.4%</h6>
                                                        </td>
                                                        <!-- <td class="text-end pe-0">
                                                            <span class="fw-medium">17.4%</span>
                                                        </td> -->
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 ps-0">
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <div class="me-2">
                                                                    <i class="bi bi-cloud-fog-fill"></i>
                                                                </div>
                                                                <h6 class="mb-0 fw-normal">Platos principales</h6>
                                                            </div>
                                                        </td>
                                                        <td class="text-end pe-0 text-nowrap">
                                                            <h6 class="mb-0">20%</h6>
                                                        </td>
                                                        <!-- <td class="text-end pe-0">
                                                            <span class="fw-medium">14.6%</span>
                                                        </td> -->
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/ Vehicles overview -->
                            <!-- TENDENCIAS MENSUALES-->
                            <div class="col-lg-6 col-xxl-6 mb-4 order-3 order-xxl-1">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0 me-2">Tendencias mensuales</h5>
                                            <small class="text-muted">Tendencias mensuales según la categoría de la receta.</small>
                                        </div>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-label-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">January</button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);">January</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">February</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">March</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">April</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">May</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">June</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">July</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">August</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">September</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">October</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">November</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">December</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="shipmentStatisticsChart"></div>
                                    </div>
                                </div>
                            </div>
                            <!--/ Shipment statistics -->
                            <!-- //* TOP 6 MEJORES RECETAS ACTUALMENTE -->
                            <div class="col-lg-6 col-xxl-4 mb-4 order-2 order-xxl-2">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0 me-2">Mejores recetas</h5>
                                            <small class="text-muted">top 6 mejores recetas actualmente en base a sus likes</small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="deliveryPerformance" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryPerformance">
                                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="p-0 m-0">
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Packages in transit</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            25.8%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">10k <i class='bx bx-heart'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-info"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Packages out for delivery</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            4.3%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">5k <i class='bx bx-heart'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-success"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Packages delivered</h6>
                                                        <small class="text-danger fw-normal d-block">
                                                            <i class="bx bx-chevron-down"></i>
                                                            12.5
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">152 <i class='bx bx-heart'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Delivery success rate</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            35.6%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">95 <i class='bx bx-heart'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Average delivery time</h6>
                                                        <small class="text-danger fw-normal d-block">
                                                            <i class="bx bx-chevron-down"></i>
                                                            2.15
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">12 <i class='bx bx-heart'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Customer satisfaction</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            5.7%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">10 <i class='bx bx-heart'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--/ Delivery Performance -->
                            <!-- //todo Recetas guardadas por usuarios -->
                            <div class="col-lg-6 col-xxl-4 mb-4 order-2 order-xxl-2">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0 me-2">Recetas más guardadas</h5>
                                            <small class="text-muted">top 6 mejores recetas más guardadas por usuarios</small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="deliveryPerformance" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryPerformance">
                                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="p-0 m-0">
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Packages in transit</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            25.8%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">10k <i class='bx bx-bookmark'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-info"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Packages out for delivery</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            4.3%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">5k <i class='bx bx-bookmark'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-success"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Packages delivered</h6>
                                                        <small class="text-danger fw-normal d-block">
                                                            <i class="bx bx-chevron-down"></i>
                                                            12.5
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">152 <i class='bx bx-bookmark'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Delivery success rate</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            35.6%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">95 <i class='bx bx-bookmark'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Average delivery time</h6>
                                                        <small class="text-danger fw-normal d-block">
                                                            <i class="bx bx-chevron-down"></i>
                                                            2.15
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">12 <i class='bx bx-bookmark'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-food-menu'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">Customer satisfaction</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            5.7%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">10 <i class='bx bx-bookmark'></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--/ Reasons for delivery exceptions -->
                            <!-- //?? Usuarios más activos -->
                            <div class="col-lg-6 col-xxl-4 mb-4 order-2 order-xxl-2">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0 me-2">Usuarios más activos</h5>
                                            <small class="text-muted">top 6 mejores usuarios más activos en la plataforma</small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="deliveryPerformance" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryPerformance">
                                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="p-0 m-0">
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-user'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">User1</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            25.8%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">10k <i class="bi bi-plus-square"></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-info"><i class='bx bx-user'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">User2</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            4.3%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">5k <i class="bi bi-plus-square"></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-success"><i class='bx bx-user'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">User3</h6>
                                                        <small class="text-danger fw-normal d-block">
                                                            <i class="bx bx-chevron-down"></i>
                                                            12.5
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">152 <i class="bi bi-plus-square"></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-user'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">User4</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            35.6%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">95 <i class="bi bi-plus-square"></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-user'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">User5</h6>
                                                        <small class="text-danger fw-normal d-block">
                                                            <i class="bx bx-chevron-down"></i>
                                                            2.15
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">12 <i class="bi bi-plus-square"></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-user'></i></span>
                                                </div>
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-1 fw-normal">User6</h6>
                                                        <small class="text-success fw-normal d-block">
                                                            <i class="bx bx-chevron-up"></i>
                                                            5.7%
                                                        </small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <h6 class="mb-0">10 <i class="bi bi-plus-square"></i></h6>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--/ Orders by Countries -->
                            <!--  //!=== TABLA DE RECETAS =====================================================================================================-->


                            <?php
                            include('conexion.php'); // Conexión a la base de datos

                            // Parámetros de búsqueda y filtros
                            $search = isset($_GET['search']) ? $conexion->real_escape_string($_GET['search']) : '';
                            $filter_difficulty = isset($_GET['filter_difficulty']) ? $conexion->real_escape_string($_GET['filter_difficulty']) : '';
                            $filter_category = isset($_GET['filter_category']) ? $conexion->real_escape_string($_GET['filter_category']) : '';

                            // Parámetros de paginación
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $limit = 10; // Número de recetas por página
                            $offset = ($page - 1) * $limit;

                            // Construir condiciones dinámicas para filtros
                            $where_conditions = "1=1"; // Condición base
                            if (!empty($search)) {
                                $where_conditions .= " AND (r.titulo LIKE '%$search%' OR u.nombre LIKE '%$search%')";
                            }
                            if (!empty($filter_difficulty)) {
                                $where_conditions .= " AND r.dificultad = '$filter_difficulty'";
                            }
                            if (!empty($filter_category)) {
                                $where_conditions .= " AND c.nombre = '$filter_category'";
                            }

                            // Consulta para obtener recetas con filtros
                            $sql = "SELECT r.titulo, u.nombre AS usuario, c.nombre AS categoria, r.dificultad, r.tiempo_preparacion, r.id_receta
        FROM recetas r
        JOIN usuarios u ON r.usuario_id = u.id_usuario
        JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
        JOIN categoria c ON rc.categoria_id = c.id_categoria
        WHERE $where_conditions
        LIMIT $limit OFFSET $offset";

                            $result = $conexion->query($sql);

                            // Construir datos para JSON
                            $data = [];
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $data[] = [
                                        'titulo' => $row['titulo'],
                                        'usuario' => $row['usuario'],
                                        'categoria' => $row['categoria'],
                                        'dificultad' => $row['dificultad'],
                                        'tiempo_preparacion' => $row['tiempo_preparacion'],
                                        'id' => $row['id_receta']
                                    ];
                                }
                            }

                            // Total de recetas
                            $sql_total = "SELECT COUNT(*) AS total FROM recetas r
                                        JOIN usuarios u ON r.usuario_id = u.id_usuario
                                        JOIN recetas_categorias rc ON r.id_receta = rc.receta_id
                                        JOIN categoria c ON rc.categoria_id = c.id_categoria
                                        WHERE $where_conditions";
                            $result_total = $conexion->query($sql_total);
                            $total = $result_total->fetch_assoc()['total'];
                            $total_pages = ceil($total / $limit);

                            // Respuesta JSON
                            echo json_encode([
                                'data' => $data,
                                'total' => $total,
                                'total_pages' => $total_pages
                            ]);

                            $conexion->close();
                            ?>



                            <div class="col-12 order-5">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0 me-2">Tabla de recetas</h5>
                                        </div>
                                        <div class="d-flex">
                                            <input type="text" id="search" class="form-control me-2" placeholder="Buscar...">
                                            <select id="filter-difficulty" class="form-control me-2">
                                                <option value="">Todas las Dificultades</option>
                                                <option value="Fácil">Fácil</option>
                                                <option value="Intermedio">Intermedio</option>
                                                <option value="Difícil">Difícil</option>
                                            </select>
                                            <select id="filter-category" class="form-control me-2">
                                                <option value="">Todas las Categorías</option>
                                                <!-- Las categorías dinámicas deben ser cargadas aquí -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-datatable table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Título</th>
                                                    <th>Usuario</th>
                                                    <th>Categoría</th>
                                                    <th>Dificultad</th>
                                                    <th>Tiempo de preparación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recipes-table">
                                                <!-- Las filas de recetas se renderizarán aquí -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <p class="text-center">Total de recetas: <span id="total-recipes"></span></p>
                                        <ul class="pagination justify-content-center" id="pagination"></ul>
                                    </div>
                                </div>
                            </div>




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

                                        fetch(`get_recipes.php?search=${search}&filter_difficulty=${difficulty}&filter_category=${category}&page=${page}`)
                                            .then(response => response.json())
                                            .then(data => {
                                                // Renderizar filas de la tabla
                                                recipesTable.innerHTML = '';
                                                if (data.data.length > 0) {
                                                    data.data.forEach(recipe => {
                                                        const row = `
                            <tr>
                                <td>${recipe.titulo}</td>
                                <td>${recipe.usuario}</td>
                                <td>${recipe.categoria}</td>
                                <td>${recipe.dificultad}</td>
                                <td>${recipe.tiempo_preparacion}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDetail(${recipe.id})">Ver detalle</button>
                                </td>
                            </tr>
                        `;
                                                        recipesTable.innerHTML += row;
                                                    });
                                                } else {
                                                    recipesTable.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron recetas</td></tr>';
                                                }

                                                // Actualizar el total de recetas
                                                totalRecipes.textContent = data.total;

                                                // Generar paginación
                                                pagination.innerHTML = '';
                                                for (let i = 1; i <= data.total_pages; i++) {
                                                    const activeClass = i === page ? 'active' : '';
                                                    pagination.innerHTML += `
                        <li class="page-item ${activeClass}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                                                }
                                            })
                                            .catch(error => {
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






                        </div>
                    </div>
                </div>
            </div>
        </div>
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