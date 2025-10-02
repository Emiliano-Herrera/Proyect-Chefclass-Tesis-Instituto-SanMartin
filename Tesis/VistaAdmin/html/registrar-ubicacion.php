<?php
/* session_start();
$ID_usuario = $_SESSION['ID_usuario'];
$nombre = $_SESSION['Nombre'];
$apellido = $_SESSION['Apellido'];
$Id_Rol = $_SESSION['IdRol']; */

?>


<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <!-- cnd Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Talleres</title>

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

    <link href='https://api.mapbox.com/mapbox-gl-js/v2.5.1/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.5.1/mapbox-gl.js'></script>
    <style>
        #map {
            width: 100%;
            height: 400px;
        }
    </style>
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
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">MS ADMIN</span>
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
                            <div>Incio</div>
                        </a>
                    </li>

                    <!-- //!MI PERFIL================================================================================================== -->

                    <li class="menu-item">
                        <a href="perfil.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div>Mi perfil</div>
                        </a>
                    </li>

                    <!-- //!GESTION DE REGISTROS -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Gestión</span>
                    </li>
                    <li class="menu-item">
                        <a href="Cursos.php" class="menu-link">
                            <i class='menu-icon bx bxs-graduation'></i>
                            <div>Cursos</div>
                        </a>
                    </li>
                    <li class="menu-item active">
                        <a href="Talleres.php" class="menu-link">
                            <i class='menu-icon bx bx-clipboard'></i>
                            <div>Talleres</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="Servicios.php" class="menu-link">
                            <i class='menu-icon bx bx-donate-blood'></i>
                            <div>Servicios</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="Beneficios.php" class="menu-link">
                            <i class='menu-icon bx bx-dollar-circle'></i>
                            <div>Beneficios</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="viajes.php" class="menu-link">
                            <i class='menu-icon bx bxs-plane-alt'></i>
                            <div>Viajes</div>
                        </a>
                    </li>

                    <!-- //!======================================================================================================= -->



                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Administración</span>
                    </li>

                    <!-- //! ADMIN -->
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon bx bx-wrench'></i>
                            <div>Admin</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="Lista-Empleados.php" class="menu-link">
                                    <i class='menu-icon bx bx-group'></i>
                                    <div>Lista de empleados</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="Lista-socios.php" class="menu-link">
                                    <i class='bx bxs-user-rectangle'></i>
                                    <div>Lista de socios</div>
                                </a>
                            </li>



                        </ul>
                    </li>


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
                                                    <span class="fw-semibold d-block"><?php echo $nombre ?></span>
                                                    <small class="text-muted"><?php echo $apellido ?></small>
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
                    <!-- Content wrapper -->
                    <div class="content-wrapper">

                        <!-- Content -->

                        <div class="container-xxl flex-grow-1 container-p-y">



                            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Registrar /</span> Registrar ubicación
                            </h4>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <h5 class="card-header">Ubicación</h5>
                                        <form id="ubicacionForm" action="guardar-ubicacion.php" method="post">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <label for="pais" class="form-label">País</label>
                                                    <input type="text" class="form-control" id="pais" name="pais" placeholder="País" readonly />
                                                </div>
                                                <div class="mb-4">
                                                    <label for="provincia" class="form-label">Provincia</label>
                                                    <input type="text" class="form-control" id="provincia" name="provincia" placeholder="Provincia" readonly />
                                                </div>
                                                <div class="mb-4">
                                                    <label for="departamento" class="form-label">Departamento</label>
                                                    <input type="text" class="form-control" id="departamento" name="departamento" placeholder="Departamento" readonly />
                                                </div>
                                                <div class="mb-4">
                                                    <label for="calle" class="form-label">Calle</label>
                                                    <input type="text" class="form-control" id="calle" name="calle" placeholder="Calle" readonly />
                                                </div>
                                                <div class="mb-4">
                                                    <label for="barrio" class="form-label">Barrio</label>
                                                    <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Barrio" />
                                                </div>
                                                <div class="mb-4 d-flex justify-content-center">
                                                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                                                </div>
                                                <div id="error-message" class="text-danger text-center"></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="card mb-4">
                                        <h5 class="card-header">Seleccione en el mapa su residencia</h5>
                                        <div class="card-body">
                                            <div id="map"></div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    // Configura tu token de acceso a Mapbox
                                    mapboxgl.accessToken = 'pk.eyJ1IjoiZW1pbGlhbm9tYXBib3gwMDciLCJhIjoiY2x6NHE1OTQ1MDBzMTJxb2h3bGYydXp5MCJ9.A5eHCMoGGOhlW_8p7_Ccqw';

                                    // Crea el mapa
                                    var map = new mapboxgl.Map({
                                        container: 'map',
                                        style: 'mapbox://styles/mapbox/streets-v11',
                                        center: [-64.183, -31.417], // Coordenadas iniciales [longitud, latitud]
                                        zoom: 9
                                    });

                                    // Añade un marcador al hacer clic en el mapa
                                    map.on('click', function(e) {
                                        var coordinates = e.lngLat;
                                        console.log('Ubicación seleccionada:', coordinates);

                                        // Elimina el marcador anterior si existe
                                        if (window.marker) {
                                            window.marker.remove();
                                        }

                                        // Añade el nuevo marcador
                                        window.marker = new mapboxgl.Marker()
                                            .setLngLat(coordinates)
                                            .addTo(map);

                                        // Usa un servicio de geocodificación para obtener la información de ubicación
                                        fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${coordinates.lng},${coordinates.lat}.json?access_token=${mapboxgl.accessToken}`)
                                            .then(response => response.json())
                                            .then(data => {
                                                var place = data.features[0];
                                                console.log(place);

                                                // Extrae y muestra la información de país, provincia, departamento, barrio y calle
                                                var country = place.context.find(c => c.id.includes('country'))?.text;
                                                var region = place.context.find(r => r.id.includes('region'))?.text;
                                                var department = place.context.find(d => d.id.includes('place'))?.text;
                                                var neighborhood = place.context.find(n => n.id.includes('neighborhood'))?.text;
                                                var address = place.text;

                                                document.getElementById('pais').value = country || '';
                                                document.getElementById('provincia').value = region || '';
                                                document.getElementById('departamento').value = department || '';
                                                document.getElementById('barrio').value = neighborhood || '';
                                                document.getElementById('calle').value = address || '';
                                            })
                                            .catch(error => console.error('Error al obtener la información de geocodificación:', error));
                                    });

                                    // Validación del formulario
                                    document.getElementById('ubicacionForm').addEventListener('submit', function(event) {
                                        var pais = document.getElementById('pais').value;
                                        var provincia = document.getElementById('provincia').value;
                                        var departamento = document.getElementById('departamento').value;
                                        var calle = document.getElementById('calle').value;
                                        var barrio = document.getElementById('barrio').value;
                                        var errorMessage = '';

                                        if (!pais || !provincia || !departamento || !calle || !barrio) {
                                            errorMessage = 'Por favor, complete todos los campos antes de guardar.';
                                            document.getElementById('error-message').innerText = errorMessage;
                                            event.preventDefault();
                                        } else {
                                            document.getElementById('error-message').innerText = '';
                                        }
                                    });
                                </script>
                            </div>



                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->


    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/form-basic-inputs.js"></script>






</body>

</html>