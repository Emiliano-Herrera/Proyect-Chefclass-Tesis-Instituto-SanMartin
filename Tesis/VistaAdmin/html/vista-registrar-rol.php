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

$nombre_usuario = $_SESSION['nombre'];
?>
-->
<!DOCTYPE html>


<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- cnd Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Registrar Rol</title>

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

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

          <li class="menu-item ">
            <a href="usuarios.php" class="menu-link">
              <i class='menu-icon bx bxs-user-detail'></i>
              <div data-i18n="Basic Inputs">Usuarios</div>
            </a>
          </li>

          <li class="menu-item active">
            <a href="roles.php" class="menu-link">
              <i class='menu-icon bx bxs-user-badge'></i>
              <div data-i18n="Input groups">Roles</div>
            </a>
          </li>

          <li class="menu-item ">
            <a href="vista-recetas.php" class="menu-link">
              <!-- <i class='menu-icon bx bx-donate-blood'></i> -->
              <i class='menu-icon bx bxs-food-menu'></i>
              <div data-i18n="Account Settings">Recetas</div>
            </a>
          </li>

          <li class="menu-item ">
            <a href="generos.php" class="menu-link">

              <i class='menu-icon bx bx-male-female'></i>
              <div data-i18n="Basic Inputs">Generos</div>
            </a>
          </li>

          <li class="menu-item ">
            <a href="categorias.php" class="menu-link">

              <i class='menu-icon bx bxs-category'></i>
              <div data-i18n="Basic Inputs">Categorias</div>
            </a>
          </li>

          <li class="menu-item ">
            <a href="auditoria.php" class="menu-link">

              <i class='menu-icon bx bxs-time'></i>
              <div data-i18n="Basic Inputs">Auditorias</div>
            </a>
          </li>

          <li class="menu-item ">
            <a href="localidades.php" class="menu-link">

              <i class='menu-icon bx bxs-map'></i>
              <div data-i18n="Localidades">Localidades</div>
            </a>
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
              <span class="text-muted fw-light">Administración / <a class="text-muted" href="roles.php">Roles /</a></span> Registrar Rol
            </h4>
            <div class="row">
              <div class="col-md-12">

                <div class="card mb-4">
                  <h5 class="card-header">Agregar Rol</h5>
                  <!-- Account -->

                  <hr class="my-0">
                  <div class="card-body">

                    <form id="formAuthentication" class="mb-3" method="POST">
                      <div class="row">
                        <div class="mb-3 col-md-6"> <label for="nombre" class="form-label">Nombre del Rol</label>
                          <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresá el rol" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo se permiten letras" autofocus required />
                        </div>
                        <div class="mt-2"> <button type="button" id="registrar-rol-btn" class="btn btn-primary me-2">Registrar Rol</button>
                          <a type="button" href="roles.php" class="btn btn-label-secondary">Cancelar</a>
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
      $('#registrar-rol-btn').on('click', function() {
        const nombre = $('#nombre').val();
        const usuario = '<?php echo $_SESSION['nombre']; ?>';
        if (nombre === '') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor ingrese un nombre para el rol.',
          });
        } else { // Verificar si el rol ya existe 
          $.ajax({
            url: 'verificar-rol.php',
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
                  text: 'Ya existe un rol con ese nombre.',
                  confirmButtonColor: '#3085d6',
                  allowOutsideClick: false
                });
              } else {
                Swal.fire({
                  title: `¿${usuario}, de verdad quieres agregar este rol "${nombre}"?`,
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
                  if (result.isConfirmed) { // Enviar el formulario mediante AJAX 
                    $.ajax({
                      url: 'registrar-rol.php',
                      method: 'POST',
                      data: {
                        nombre: nombre
                      },
                      success: function(response) {
                        Swal.fire({
                          icon: 'success',
                          title: '¡Éxito!',
                          text: 'El rol ha sido registrado correctamente.',
                          confirmButtonColor: '#3085d6'
                        }).then(() => {
                          window.location.href = 'roles.php';
                        });
                      },
                      error: function() {
                        Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Hubo un problema al registrar el rol. Inténtelo de nuevo más tarde.',
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
                text: 'Hubo un problema al verificar el rol. Inténtelo de nuevo más tarde.',
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
  </script>



</body>

</html>