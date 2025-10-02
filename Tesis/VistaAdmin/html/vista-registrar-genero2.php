<!-- 
<?php



include("conexion.php");

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
-->
<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <!-- cnd Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Genero</title>

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

          <li class="menu-item active">
            <a href="generos.php" class="menu-link">

              <i class='bx bx-male-female'></i>
              <div data-i18n="Basic Inputs">Generos</div>
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
              <span class="text-muted fw-light">Admin / <a class="text-muted" href="./ListaEmpleados.php">Lista de Genero /</a></span> Registrar Genero
            </h4>
            <div class="row">
              <div class="col-md-12">

                <div class="card mb-4">
                  <h5 class="card-header">Agregar Genero</h5>
                  <!-- Account -->

                  <hr class="my-0">
                  <div class="card-body">

                    <form id="formAuthentication" class="mb-3" method="POST">
                      <div class="row">
                        <div class="mb-3 col-md-6">
                          <label for="nombre" class="form-label">Nombre del género</label>
                          <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresá el género" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo se permiten letras" autofocus required />
                        </div>
                        <div class="mt-2">
                          <button type="button" id="registrar-genero-btn" class="btn btn-primary me-2">Registrar Género</button>
                          <a type="button" href="generos.php" class="btn btn-label-secondary">Cancelar</a>
                        </div>
                      </div>
                    </form>



                  </div>
                  <!-- /Account -->
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $(document).ready(function() {
      $('#registrar-genero-btn').on('click', function() {
        const nombre = $('#nombre').val();
        const usuario = '<?php echo $_SESSION['nombre']; ?>';

        if (nombre === '') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor ingrese un nombre para el género.',
          });
        } else {
          // Verificar si el género ya existe
          $.ajax({
            url: 'verificar-genero.php',
            method: 'POST',
            data: {
              nombre: nombre
            },
            dataType: 'json',
            success: function(response) {
              if (response.exists) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Duplicado',
                  text: 'Ya existe un género con ese nombre.',
                  confirmButtonColor: '#3085d6',
                  allowOutsideClick: false
                });
              } else {
                Swal.fire({
                  title: `¿${usuario}, de verdad quieres agregar este género "${nombre}"?`,
                  text: 'Esta acción no se puede deshacer.',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Sí, agregar',
                  cancelButtonText: 'Cancelar',
                  backdrop: true,
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  allowEnterKey: false,
                  showCloseButton: true,
                  stopKeydownPropagation: true
                }).then((result) => {
                  if (result.isConfirmed) {
                    // Enviar el formulario mediante AJAX
                    $.ajax({
                      url: 'registrar-genero.php',
                      method: 'POST',
                      data: {
                        nombre: nombre
                      },
                      success: function(response) {
                        Swal.fire({
                          icon: 'success',
                          title: '¡Éxito!',
                          text: 'El género ha sido registrado correctamente.',
                          confirmButtonColor: '#3085d6'
                        }).then(() => {
                          window.location.href = 'generos.php';
                        });
                      },
                      error: function() {
                        Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Hubo un problema al registrar el género. Inténtelo de nuevo más tarde.',
                          confirmButtonColor: '#3085d6',
                          allowOutsideClick: false
                        });
                      }
                    });
                  }
                });
              }
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al verificar el género. Inténtelo de nuevo más tarde.',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false
              });
            }
          });
        }
      });
    });

    document.getElementById('nombre').addEventListener('keypress', function(event) {
      const char = String.fromCharCode(event.which);
      if (!/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/.test(char)) {
        event.preventDefault();
      }
    });

    document.getElementById('registrar-genero-btn').addEventListener('click', function() {
      const nombre = document.getElementById('nombre').value.trim();
      if (!/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/.test(nombre)) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Solo se permiten letras en el nombre del género.',
        });
        return;
      }

      // Enviar el formulario si la validación pasa
      document.getElementById('formAuthentication').submit();
    });
  </script>


</body>

</html>