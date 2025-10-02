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
  <!-- Incluir jQuery desde un CDN -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark/dark.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- cnd Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Registrar Usuario</title>

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

  <!-- MAPAAAAAAA -->

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- MAPAAAAAAA -->
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
              <div data-i18n="Inicio">Incio</div>
            </a>
          </li>

          <!-- //!MI PERFIL================================================================================================== -->

          <li class="menu-item">
            <a href="perfil2.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-user"></i>
              <div class="text-truncate" data-i18n="Mi perfil">Mi perfil</div>
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
          $usuarios_activos = [
            'usuarios.php',
            'vista-registrar-usuario.php',
            'vista-editar-usuario.php',
            'PerfilUsuario.php'
          ];

          // Mostrar solo las secciones permitidas para el rol
          foreach ($secciones_menu as $id => $info) {
            if (in_array($id, $secciones_permitidas)) {
              // Para la sección Usuarios, marcar activo si el archivo actual está en $usuarios_activos
              if ($id == 1) {
                $active = in_array($archivo_actual, $usuarios_activos) ? 'active' : '';
              } else {
                $active = ($archivo_actual == $info['archivo']) ? 'active' : '';
              }
          ?>
              <li class="menu-item <?= $active ?>">
                <a href="<?= $info['archivo'] ?>" class="menu-link">
                  <i class='menu-icon bx <?= $info['icono'] ?>'></i>
                  <div data-i18n="<?= $info['nombre'] ?>"><?= $info['nombre'] ?></div>
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
                            <img src="img_perfil/ryvrAb4K6b6mH.png" alt="" width="50" height="50">
                            <!--<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg>-->
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
                    <a class="dropdown-item" href="perfil2.php">
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



              <h4 class="py-3 mb-4"><span class="text-muted fw-light">Administración / Usuarios</span> / Registrar Usuario
              </h4>

              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <h3 class="card-header">Registro de usuario</h3>
                    <div class="card-body">
                      <form id="formValidationExamples" method="post" enctype="multipart/form-data">
                        <!-- 1. Datos personales -->
                        <div class="row">
                          <div class="col-12">
                            <h4>1. Datos personales</h4>
                            <hr class="mt-0">
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" class="form-control" placeholder="Nombre" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+">
                            <div class="invalid-feedback" id="nombre-error" style="display:none;">
                              El nombre es obligatorio.
                            </div>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" id="apellido" class="form-control" placeholder="Apellido" name="apellido" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+">
                            <div class="invalid-feedback" id="apellido-error" style="display:none;">
                              El Apellido es obligatorio.
                            </div>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="genero" class="form-label">Género</label>
                            <select class="form-select" name="genero" id="genero">
                              <?php
                              require "conexion.php";
                              $g = mysqli_query($conexion, "SELECT * FROM generos");
                              while ($opciones = mysqli_fetch_row($g)) {
                              ?>
                                <option value="<?php echo $opciones[0] ?>"><?php echo $opciones[1] ?></option>
                              <?php
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" name="rol" id="rol">
                              <?php
                              require "conexion.php";
                              $g = mysqli_query($conexion, "SELECT * FROM roles WHERE estado = 'habilitado'");
                              while ($opciones = mysqli_fetch_row($g)) {
                              ?>
                                <option value="<?php echo $opciones[0] ?>"><?php echo $opciones[1] ?></option>
                              <?php
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <!-- Detalles de cuenta -->
                        <div class="row">
                          <div class="col-12 mt-5">
                            <h4>2. Detalles de cuenta</h4>
                            <hr class="mt-0">
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="archivo" class="form-label">Foto de perfil</label>
                            <input class="form-control" type="file" id="archivo" name="archivo">
                            <div class="invalid-feedback" id="foto-perfil-error" style="display:none;">
                              La foto de perfil es obligatoria.
                            </div>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nombre de usuario</label>
                            <input type="text" id="username" class="form-control" placeholder="Nombre de usuario" name="username" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras, sin números ni caracteres especiales">
                            <div class="invalid-feedback" id="username-error" style="display:none;">
                              El nombre de usuario es obligatorio.
                            </div>
                          </div>
                          <div class="col-md-12 mb-3">
                            <label for="emails" class="form-label">Emails</label>
                            <div id="email-container">
                              <div class="input-group mb-3">
                                <input type="email" class="form-control" name="emails[]" placeholder="Email" id="email">

                                <button class="btn btn-outline-secondary add-email-btn" type="button">Agregar otro</button>
                                <div class="invalid-feedback" id="email-error" style="display:none;">
                                  El email es obligatorio.
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12 mb-3">
                            <label for="telefonos" class="form-label">Teléfonos</label>
                            <div id="phone-container">
                              <!-- En el bloque inicial de teléfonos -->
                              <div class="input-group mb-3">
                                <input type="text" class="form-control phone-input" name="telefonos[]" placeholder="Teléfono" pattern="[0-9]{10,13}" title="Debe contener entre 10 y 13 números" minlength="10" maxlength="13">

                                <select class="form-select ms-2" name="tipos_telefono[]">
                                  <option value="Personal">Personal</option>
                                  <option value="Laboral">Laboral</option>
                                </select>
                                <button class="btn btn-outline-secondary add-phone-btn" type="button">Agregar otro</button>
                                <div class="invalid-feedback" id="telefono-error" style="display:none;">
                                  El telefono es obligatorio.
                                </div>
                              </div>
                            </div>
                          </div>


                          <div class="col-md-6 mb-3">
                            <div class="form-password-toggle">
                              <label for="contraseña" class="form-label">Contraseña</label>
                              <div class="input-group input-group-merge">
                                <input class="form-control" type="password" id="contraseña" name="contraseña" placeholder="••••••••">
                                <div class="invalid-feedback" id="password-error" style="display:none;">
                                  La contraseña es obligatoria.
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                              </div>
                              <ul id="password-criteria" class="text-muted small">
                                <li id="min-length">Mínimo 8 caracteres</li>
                                <li id="upper-case">Al menos una mayúscula</li>
                                <li id="lower-case">Al menos una minúscula</li>
                                <li id="special-char">Al menos un carácter especial</li>
                              </ul>
                            </div>
                          </div>
                          <div class="col-md-6 mb-3">
                            <div class="form-password-toggle">
                              <label for="contraseña2" class="form-label">Confirmar contraseña</label>
                              <div class="input-group input-group-merge">
                                <input class="form-control" type="password" id="contraseña2" name="contraseña2" placeholder="••••••••">
                                <div class="invalid-feedback" id="password-confirm-error" style="display:none;">
                                  La confirmacion de contraseña es obligatoria.
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                              </div>
                            </div>
                          </div>
                        </div>


                        <!--Ubicacion -->

                        <!--Ubicacion -->

                        <div class="row">
                          <div class="col-12 mt-5">
                            <h4>3. Ubicación</h4>
                            <hr class="mt-0">
                          </div>
                          <!-- Ubicación del usuario -->
                          <div class="row mb-3">
                            <div class="col-md-4">
                              <label for="provincia" class="form-label">Provincia</label>
                              <input type="text" id="provincia" name="provincia" class="form-control" readonly>
                              <div class="invalid-feedback" id="provincia-error" style="display:none;">
                                La provincia es obligatoria.
                              </div>
                            </div>
                            <div class="col-md-4">
                              <label for="departamento" class="form-label">Departamento</label>
                              <input type="text" id="departamento" name="departamento" class="form-control" readonly>

                            </div>
                            <div class="col-md-4">
                              <label for="localidad" class="form-label">Localidad</label>
                              <input type="text" id="localidad" name="localidad" class="form-control" readonly>
                              <div class="invalid-feedback" id="localidad-error" style="display:none;">
                                La localidad es obligatoria.
                              </div>
                            </div>
                            <div class="col-md-6 mt-3">
                              <label for="barrio" class="form-label">Barrio</label>
                              <input type="text" id="barrio" name="barrio" class="form-control" readonly>
                              <div class="invalid-feedback" id="barrio-error" style="display:none;">
                                El barrio es obligatorio.
                              </div>
                            </div>
                            <div class="col-md-6 mt-3">
                              <label for="pais" class="form-label">País</label>
                              <input type="text" id="pais" name="pais" class="form-control" readonly>
                              <div class="invalid-feedback" id="pais-error" style="display:none;">
                                El país es obligatorio.
                              </div>
                            </div>
                          </div>

                          <!-- Inputs ocultos para latitud y longitud -->
                          <input type="hidden" id="latitud" name="latitud">
                          <input type="hidden" id="longitud" name="longitud">

                          <!-- Mapa para seleccionar ubicación -->
                          <div class="mb-3">
                            <label class="form-label">Ubicación en el mapa</label>
                            <div id="map" style="height: 350px; border-radius: 10px;"></div>
                            <div class="form-text">Haz clic en el mapa para seleccionar la ubicación del usuario.</div>
                          </div>
                        </div>
                        <div class="col-12">
                          <button type="submit" class="btn btn-primary">Registrar usuario</button>
                          <div id="error-message" class="text-danger text-center mt-2"></div>
                          <div id="success-message" class="text-success text-center mt-2"></div>
                          <a type="button" href="usuarios.php" class="btn btn-label-secondary">Cancelar</a>
                        </div>
                      </form>





                    </div>
                  </div>
                </div>
              </div>

              <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


              <script>
                // Solo permitir letras, números y puntos en emails (sin espacios)
                document.getElementById("email").addEventListener("input", function() {
                  this.value = this.value.replace(/\s/g, '');
                  const emailRegex = /^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/;
                  if (!emailRegex.test(this.value)) {
                    this.setCustomValidity("El email no cumple con las restricciones especificadas.");
                  } else {
                    this.setCustomValidity("");
                  }
                });

                // Solo letras en nombre y apellido
                document.getElementById("nombre").addEventListener("input", function() {
                  this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
                });
                document.getElementById("apellido").addEventListener("input", function() {
                  this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
                });

                // Validar requisitos de contraseña
                function validarContrasena() {
                  const password = document.getElementById('contraseña').value;
                  const minLength = document.getElementById('min-length');
                  const upperCase = document.getElementById('upper-case');
                  const lowerCase = document.getElementById('lower-case');
                  const specialChar = document.getElementById('special-char');

                  const lengthRegex = /.{8,}/;
                  const upperCaseRegex = /[A-Z]/;
                  const lowerCaseRegex = /[a-z]/;
                  const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

                  minLength.style.color = lengthRegex.test(password) ? 'green' : 'red';
                  upperCase.style.color = upperCaseRegex.test(password) ? 'green' : 'red';
                  lowerCase.style.color = lowerCaseRegex.test(password) ? 'green' : 'red';
                  specialChar.style.color = specialCharRegex.test(password) ? 'green' : 'red';
                }

                // Validar formulario
                function validarFormulario(event) {
                  event.preventDefault();

                  const errorMessage = document.getElementById('error-message');
                  const successMessage = document.getElementById('success-message');
                  errorMessage.innerText = '';
                  successMessage.innerText = '';

                  let isValid = true;
                  let mensajeError = '';

                  //Mensaje de error si es que algun campo del formulario esta vacio 

                  // Validar campos de ubicación
                  const ubicacionCampos = [{
                      id: 'provincia',
                      mensaje: 'La provincia es obligatoria.'
                    },
                    {
                      id: 'departamento',
                      mensaje: 'El departamento es obligatorio.'
                    },
                    {
                      id: 'localidad',
                      mensaje: 'La localidad es obligatoria.'
                    },
                    {
                      id: 'barrio',
                      mensaje: 'El barrio es obligatorio.'
                    },
                    {
                      id: 'pais',
                      mensaje: 'El país es obligatorio.'
                    }
                  ];

                  ubicacionCampos.forEach(function(campo) {
                    const input = document.getElementById(campo.id);
                    const errorDiv = document.getElementById(campo.id + '-error');
                    if (input && input.value.trim() === '') {
                      input.classList.add('is-invalid');
                      if (errorDiv) {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = campo.mensaje;
                      }
                      isValid = false;
                    } else {
                      input.classList.remove('is-invalid');
                      if (errorDiv) errorDiv.style.display = 'none';
                    }
                    // Limpiar error al escribir
                    if (input) {
                      input.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                          this.classList.remove('is-invalid');
                          if (errorDiv) errorDiv.style.display = 'none';
                        }
                      });
                    }
                  });

                  // Validar nombre
                  const nombreInput = document.getElementById('nombre');
                  const nombreError = document.getElementById('nombre-error');
                  if (nombreInput.value.trim() === '') {
                    isValid = false;
                    nombreInput.classList.add('is-invalid');
                    nombreError.style.display = 'block';
                    nombreError.textContent = 'El nombre es obligatorio.';
                  } else {
                    nombreInput.classList.remove('is-invalid');
                    nombreError.style.display = 'none';
                  }

                  // Validar apellido
                  const apellidoInput = document.getElementById('apellido');
                  const apellidoError = document.getElementById('apellido-error');
                  if (apellidoInput.value.trim() === '') {
                    isValid = false;
                    apellidoInput.classList.add('is-invalid');
                    apellidoError.style.display = 'block';
                    apellidoError.textContent = 'El apellido es obligatorio.';
                  } else {
                    apellidoInput.classList.remove('is-invalid');
                    apellidoError.style.display = 'none';
                  }

                  // Validar género
                  const generoInput = document.getElementById('genero');
                  const generoError = document.getElementById('genero-error');
                  if (generoInput.value === '') {
                    isValid = false;
                    generoInput.classList.add('is-invalid');
                    if (generoError) {
                      generoError.style.display = 'block';
                      generoError.textContent = 'El género es obligatorio.';
                    }
                  } else {
                    generoInput.classList.remove('is-invalid');
                    if (generoError) generoError.style.display = 'none';
                  }

                  // Validar rol
                  const rolInput = document.getElementById('rol');
                  const rolError = document.getElementById('rol-error');
                  if (rolInput.value === '') {
                    isValid = false;
                    rolInput.classList.add('is-invalid');
                    if (rolError) {
                      rolError.style.display = 'block';
                      rolError.textContent = 'El rol es obligatorio.';
                    }
                  } else {
                    rolInput.classList.remove('is-invalid');
                    if (rolError) rolError.style.display = 'none';
                  }

                  // Validar username
                  const usernameInput = document.getElementById('username');
                  const usernameError = document.getElementById('username-error');
                  if (usernameInput.value.trim() === '') {
                    isValid = false;
                    usernameInput.classList.add('is-invalid');
                    usernameError.style.display = 'block';
                    usernameError.textContent = 'El nombre de usuario es obligatorio.';
                  } else {
                    usernameInput.classList.remove('is-invalid');
                    usernameError.style.display = 'none';
                  }

                  // Validar solo letras en nombre de usuario
                  if (!/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/.test(usernameInput.value.trim())) {
                    isValid = false;
                    usernameInput.classList.add('is-invalid');
                    usernameError.style.display = 'block';

                  }


                  // Validar foto de perfil
                  const archivoInput = document.getElementById('archivo');
                  const archivoError = document.getElementById('foto-perfil-error');
                  if (!archivoInput.value) {
                    isValid = false;
                    archivoInput.classList.add('is-invalid');
                    archivoError.style.display = 'block';
                    archivoError.textContent = 'Debe seleccionar un archivo de imagen.';
                  } else {
                    archivoInput.classList.remove('is-invalid');
                    archivoError.style.display = 'none';
                  }

                  // Validar contraseñas
                  const password = document.getElementById('contraseña').value;
                  const confirmPassword = document.getElementById('contraseña2').value;
                  const lengthRegex = /.{8,}/;
                  const upperCaseRegex = /[A-Z]/;
                  const lowerCaseRegex = /[a-z]/;
                  const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

                  if (!lengthRegex.test(password) || !upperCaseRegex.test(password) || !lowerCaseRegex.test(password) || !specialCharRegex.test(password)) {
                    isValid = false;
                    mensajeError += 'La contraseña no cumple con los requisitos.\n';
                  }
                  if (password === '' || confirmPassword === '' || password !== confirmPassword) {
                    isValid = false;
                    mensajeError += 'Las contraseñas no coinciden o están vacías.\n';
                  }

                  // Validar emails (visual)
                  const emailInputs = document.querySelectorAll('input[name="emails[]"]');
                  let emailVacio = true;
                  emailInputs.forEach(emailInput => {
                    const errorDiv = emailInput.parentElement.querySelector('.invalid-feedback') || document.getElementById('email-error');
                    if (emailInput.value.trim() === '' || !validateEmail(emailInput.value.trim())) {
                      isValid = false;
                      emailInput.classList.add('is-invalid');
                      if (errorDiv) {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'El email es obligatorio y debe ser válido.';
                      }
                    } else {
                      emailInput.classList.remove('is-invalid');
                      if (errorDiv) errorDiv.style.display = 'none';
                      emailVacio = false;
                    }
                    emailInput.addEventListener('input', function() {
                      if (this.value.trim() !== '' && validateEmail(this.value.trim())) {
                        this.classList.remove('is-invalid');
                        if (errorDiv) errorDiv.style.display = 'none';
                      }
                    });
                  });
                  if (emailVacio) isValid = false;

                  // Validar teléfonos (visual)
                  const telefonoInputs = document.querySelectorAll('input[name="telefonos[]"]');
                  let telefonoVacio = true;
                  telefonoInputs.forEach(telefonoInput => {
                    const errorDiv = telefonoInput.parentElement.querySelector('.invalid-feedback') || document.getElementById('telefono-error');
                    if (
                      telefonoInput.value.trim() === '' ||
                      telefonoInput.value.trim().length < 10 ||
                      telefonoInput.value.trim().length > 13 ||
                      !/^[0-9]+$/.test(telefonoInput.value.trim())
                    ) {
                      isValid = false;
                      telefonoInput.classList.add('is-invalid');
                      if (errorDiv) {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'El teléfono es obligatorio y debe contener entre 10 y 13 dígitos.';
                      }
                    } else {
                      telefonoInput.classList.remove('is-invalid');
                      if (errorDiv) errorDiv.style.display = 'none';
                      telefonoVacio = false;
                    }
                    telefonoInput.addEventListener('input', function() {
                      if (
                        this.value.trim() !== '' &&
                        this.value.trim().length >= 10 &&
                        this.value.trim().length <= 13 &&
                        /^[0-9]+$/.test(this.value.trim())
                      ) {
                        this.classList.remove('is-invalid');
                        if (errorDiv) errorDiv.style.display = 'none';
                      }
                    });
                  });
                  if (telefonoVacio) isValid = false;


                  // Enviar formulario por AJAX
                  const formData = new FormData(document.getElementById('formValidationExamples'));
                  fetch('guardar-usuario.php', {
                      method: 'POST',
                      body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                      if (data.status === 'success') {
                        Swal.fire({
                          title: 'Éxito',
                          text: data.message,
                          icon: 'success'
                        }).then(() => {
                          window.location.href = 'usuarios.php';
                        });
                      } else {
                        Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: data.message
                        });
                      }
                    })
                    .catch(error => {
                      Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al crear el usuario. Intente nuevamente.'
                      });
                    });
                }

                // Validar email
                function validateEmail(email) {
                  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                  return re.test(email);
                }

                // Asociar eventos
                document.getElementById('formValidationExamples').addEventListener('submit', validarFormulario);
                document.getElementById('contraseña').addEventListener('input', validarContrasena);

                // Agregar emails (máximo 2)
                document.querySelector('.add-email-btn').addEventListener('click', function() {
                  const emailContainer = document.getElementById('email-container');
                  if (emailContainer.children.length < 2) {
                    const newEmailInput = document.createElement('div');
                    newEmailInput.classList.add('input-group', 'mb-3');
                    newEmailInput.innerHTML = `
        <input type="email" class="form-control" name="emails[]" placeholder="Email" required />
        <button class="btn btn-outline-secondary remove-email-btn" type="button">Eliminar</button>
      `;
                    emailContainer.appendChild(newEmailInput);

                    newEmailInput.querySelector('.remove-email-btn').addEventListener('click', function() {
                      newEmailInput.remove();
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Límite alcanzado',
                      text: 'No puedes agregar más de 2 emails.',
                    });
                  }
                });

                // Agregar teléfonos (máximo 2) con tipo
                document.querySelector('.add-phone-btn').addEventListener('click', function() {
                  const phoneContainer = document.getElementById('phone-container');
                  if (phoneContainer.children.length < 2) {
                    const newPhoneInput = document.createElement('div');
                    newPhoneInput.classList.add('input-group', 'mb-3');
                    newPhoneInput.innerHTML = `
        <input type="text" class="form-control phone-input" name="telefonos[]" placeholder="Teléfono" pattern="[0-9]{10,13}" title="Debe contener entre 10 y 13 números" minlength="10" maxlength="13" required />
        <select class="form-select ms-2" name="tipos_telefono[]" required>
          <option value="Personal">Personal</option>
          <option value="Laboral">Laboral</option>
        </select>
        <button class="btn btn-outline-secondary remove-phone-btn" type="button">Eliminar</button>
      `;
                    phoneContainer.appendChild(newPhoneInput);

                    newPhoneInput.querySelector('.remove-phone-btn').addEventListener('click', function() {
                      newPhoneInput.remove();
                    });

                    // Solo números en el nuevo campo
                    newPhoneInput.querySelector('.phone-input').addEventListener('keypress', function(event) {
                      if (!/[0-9]/.test(event.key)) {
                        event.preventDefault();
                      }
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Límite alcanzado',
                      text: 'No puedes agregar más de 2 teléfonos.',
                    });
                  }
                });

                // Solo números en los campos existentes de teléfono
                document.querySelectorAll('.phone-input').forEach(function(input) {
                  input.addEventListener('keypress', function(event) {
                    if (!/[0-9]/.test(event.key)) {
                      event.preventDefault();
                    }
                  });
                });



                // Inicializar el mapa con Leaflet
                var map = L.map('map').setView([-28.4696, -65.7852], 13); // Catamarca por defecto

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  maxZoom: 19,
                  attribution: '© OpenStreetMap'
                }).addTo(map);

                var marker;

                map.on('click', function(e) {
                  var lat = e.latlng.lat.toFixed(6);
                  var lng = e.latlng.lng.toFixed(6);

                  if (marker) {
                    marker.setLatLng([lat, lng]);
                  } else {
                    marker = L.marker([lat, lng]).addTo(map);
                  }

                  // Guardar en los inputs ocultos para enviar al backend
                  document.getElementById('latitud').value = lat;
                  document.getElementById('longitud').value = lng;

                  // Geocodificación inversa con Nominatim
                  fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                      // Puedes ver la estructura de data.address en la consola
                      // console.log(data);

                      // Autocompletar los campos si existen en la respuesta
                      document.getElementById('provincia').value = data.address.state || '';
                      document.getElementById('departamento').value = data.address.county || '';
                      document.getElementById('localidad').value = data.address.city || data.address.town || data.address.village || '';
                      document.getElementById('barrio').value = data.address.suburb || '';
                      document.getElementById('pais').value = data.address.country || '';
                    })
                    .catch(error => {
                      // Si hay error, limpia los campos
                      document.getElementById('provincia').value = '';
                      document.getElementById('departamento').value = '';
                      document.getElementById('localidad').value = '';
                      document.getElementById('barrio').value = '';
                      document.getElementById('pais').value = '';
                    });
                });


                //Evita mas de un espacio seguido para los input text y input email
                document.querySelectorAll('input[type="text"], input[type="email"]').forEach(function(input) {
                  input.addEventListener('input', function() {
                    this.value = this.value.replace(/ {2,}/g, ' ');
                    this.value = this.value.replace(/^\s+/, '');

                  });


                });

                // Evita espacios en los inputs de Password
                document.querySelectorAll('input[type="password"]').forEach(function(input) {
                  input.addEventListener('input', function() {
                    this.value = this.value.replace(/\s/g, '');
                  });

                });

                // Solo letras en el username
                document.getElementById("username").addEventListener("input", function() {
                  this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
                });
              </script>








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