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
                    <h5 class="card-header">Formulario de registro de usuario</h5>
                    <div class="card-body">
                      <form id="formValidationExamples" method="post" enctype="multipart/form-data">
                        <!-- 1. Datos personales -->
                        <div class="row">
                          <div class="col-12">
                            <h6>1. Datos personales</h6>
                            <hr class="mt-0">
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" class="form-control" placeholder="Nombre" name="nombre" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+">
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" id="apellido" class="form-control" placeholder="Apellido" name="apellido" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+">
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="genero" class="form-label">Género</label>
                            <select class="form-select" name="genero" id="genero" required>
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
                            <select class="form-select" name="rol" id="rol" required>
                              <?php
                              require "conexion.php";
                              $g = mysqli_query($conexion, "SELECT * FROM roles");
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
                            <h6>2. Detalles de cuenta</h6>
                            <hr class="mt-0">
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="archivo" class="form-label">Foto de perfil</label>
                            <input class="form-control" type="file" id="archivo" name="archivo" required>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nombre de usuario</label>
                            <input type="text" id="username" class="form-control" placeholder="Nombre de usuario" name="username" required>
                          </div>
                          <div class="col-md-12 mb-3">
                            <label for="emails" class="form-label">Emails</label>
                            <div id="email-container">
                              <div class="input-group mb-3">
                                <input type="email" class="form-control" name="emails[]" placeholder="Email" id="email" required>
                                <button class="btn btn-outline-secondary add-email-btn" type="button">Agregar otro</button>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12 mb-3">
                            <label for="telefonos" class="form-label">Teléfonos</label>
                            <div id="phone-container">
                              <div class="input-group mb-3">
                                <input type="text" class="form-control phone-input" name="telefonos[]" placeholder="Teléfono" pattern="[0-9]{10,13}" title="Debe contener entre 10 y 13 números" minlength="10" maxlength="13" required>
                                <button class="btn btn-outline-secondary add-phone-btn" type="button">Agregar otro</button>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 mb-3">
                            <div class="form-password-toggle">
                              <label for="contraseña" class="form-label">Contraseña</label>
                              <div class="input-group input-group-merge">
                                <input class="form-control" type="password" id="contraseña" name="contraseña" placeholder="••••••••" required>
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
                                <input class="form-control" type="password" id="contraseña2" name="contraseña2" placeholder="••••••••" required>
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                              </div>
                            </div>
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
                //funcion para que en los emails solo se permitan letras (a-z), números (0-9) y puntos (.).
                document.getElementById("email").addEventListener("input", function() { // Eliminar espacios mientras se escribe 
                  this.value = this.value.replace(/\s/g, ''); // para validar las restricciones del email 
                  const emailRegex = /^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/;
                  if (!emailRegex.test(this.value)) {
                    this.setCustomValidity("El email no cumple con las restricciones especificadas.");
                  } else {
                    this.setCustomValidity("");
                  }
                });

                //Funcion para que solamente se permitan letras en nombre y apellido
                document.getElementById("nombre").addEventListener("input", function() {
                  this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
                });
                document.getElementById("apellido").addEventListener("input", function() {
                  this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
                });


                // Validar los requisitos de la contraseña
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

                function validarFormulario(event) {
                  event.preventDefault(); // Prevenir el envío automático del formulario

                  const errorMessage = document.getElementById('error-message');
                  const successMessage = document.getElementById('success-message');
                  errorMessage.innerText = ''; // Limpiar el mensaje de error
                  successMessage.innerText = ''; // Limpiar el mensaje de éxito

                  // Validar todos los campos del formulario
                  let isValid = true;
                  let mensajeError = '';

                  // Validar nombre
                  const nombre = document.getElementById('nombre').value.trim();
                  if (nombre === '') {
                    isValid = false;
                    mensajeError += 'El nombre es obligatorio.\n';
                  }

                  // Validar apellido
                  const apellido = document.getElementById('apellido').value.trim();
                  if (apellido === '') {
                    isValid = false;
                    mensajeError += 'El apellido es obligatorio.\n';
                  }

                  // Validar género
                  const genero = document.getElementById('genero').value;
                  if (genero === '') {
                    isValid = false;
                    mensajeError += 'El género es obligatorio.\n';
                  }

                  // Validar rol
                  const rol = document.getElementById('rol').value;
                  if (rol === '') {
                    isValid = false;
                    mensajeError += 'El rol es obligatorio.\n';
                  }

                  // Validar nombre de usuario
                  const username = document.getElementById('username').value.trim();
                  if (username === '') {
                    isValid = false;
                    mensajeError += 'El nombre de usuario es obligatorio.\n';
                  }

                  // Validar emails
                  const emailInputs = document.querySelectorAll('input[name="emails[]"]');
                  if (emailInputs.length > 3) {
                    isValid = false;
                    mensajeError += 'No puedes agregar más de 3 emails.\n';
                  }
                  emailInputs.forEach(emailInput => {
                    const email = emailInput.value.trim();
                    if (email === '' || !validateEmail(email)) {
                      isValid = false;
                      mensajeError += 'Uno de los emails no es válido.\n';
                    }
                  });

                  // Validar teléfonos
                  const telefonoInputs = document.querySelectorAll('input[name="telefonos[]"]');
                  if (telefonoInputs.length > 3) {
                    isValid = false;
                    mensajeError += 'No puedes agregar más de 3 teléfonos.\n';
                  }
                  telefonoInputs.forEach(telefonoInput => {
                    const telefono = telefonoInput.value.trim();
                    if (telefono === '' || telefono.length < 10 || telefono.length > 13 || !/^[0-9]+$/.test(telefono)) {
                      isValid = false;
                      mensajeError += 'Uno de los teléfonos no es válido. Debe contener entre 10 y 13 números y solo dígitos.\n';
                    }
                  });

                  // Validar que las contraseñas cumplan con los requisitos
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

                  // Si no es válido, mostrar errores con SweetAlert y detener el envío
                  if (!isValid) {
                    Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: mensajeError,
                    });
                    return false;
                  }

                  // Si es válido, enviar el formulario mediante AJAX
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

                // Función para validar email
                function validateEmail(email) {
                  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                  return re.test(email);
                }

                // Asociar la función validarFormulario al evento submit del formulario
                document.getElementById('formValidationExamples').addEventListener('submit', validarFormulario);

                // Verificación de la contraseña mientras se escribe
                document.getElementById('contraseña').addEventListener('input', validarContrasena);

                // Funcionalidad para agregar más emails
                document.querySelector('.add-email-btn').addEventListener('click', function() {
                  const emailContainer = document.getElementById('email-container');
                  if (emailContainer.children.length <
                    3) {
                    const newEmailInput = document.createElement('div');
                    newEmailInput.classList.add('input-group', 'mb-3');
                    newEmailInput.innerHTML = `
                      <input type="email" class="form-control" name="emails[]" placeholder="Email" />
                    <button class="btn btn-outline-secondary remove-email-btn" type="button">Eliminar</button>`;
                    emailContainer.appendChild(newEmailInput);

                    // Funcionalidad para eliminar emails
                    newEmailInput.querySelector('.remove-email-btn').addEventListener('click', function() {
                      newEmailInput.remove();
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Límite alcanzado',
                      text: 'No puedes agregar más de 3 emails.',
                    });
                  }
                });

                document.querySelector('.add-phone-btn').addEventListener('click', function() {
                  const phoneContainer = document.getElementById('phone-container');
                  if (phoneContainer.children.length <
                    3) {
                    const newPhoneInput = document.createElement('div');
                    newPhoneInput.classList.add('input-group', 'mb-3');
                    newPhoneInput.innerHTML = `
                      <input type="text" class="form-control phone-input" name="telefonos[]" placeholder="Teléfono" pattern="[0-9]{10,13}" title="Debe contener entre 10 y 13 números" minlength="10" maxlength="13" />
                    <button class="btn btn-outline-secondary remove-phone-btn" type="button">Eliminar</button>
                    `;
                    phoneContainer.appendChild(newPhoneInput);

                    // Funcionalidad para eliminar teléfonos
                    newPhoneInput.querySelector('.remove-phone-btn').addEventListener('click', function() {
                      newPhoneInput.remove();
                    });

                    // Validar que solo se ingresen números en el campo de teléfono
                    newPhoneInput.querySelector('.phone-input').addEventListener('keypress', function(event) {
                      if (!/[0-9]/.test(event.key)) {
                        event.preventDefault();
                      }
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Límite alcanzado',
                      text: 'No puedes agregar más de 3 teléfonos.',
                    });
                  }
                });

                // Validar que solo se ingresen números en los campos existentes de teléfono
                document.querySelectorAll('.phone-input').forEach(function(input) {
                  input.addEventListener('keypress', function(event) {
                    if (!/[0-9]/.test(event.key)) {
                      event.preventDefault();
                    }
                  });
                });

                // Funcionalidad para agregar más teléfonos
                $('.add-phone-btn').on('click', function() {
                  const phoneContainer = $('#phone-container');
                  if (phoneContainer.children().length < 3) {
                    const newPhoneInput = `
                      <div class="input-group mb-3">
                      <input type="text" class="form-control" name="telefonos[]" placeholder="Teléfono" pattern="[0-9]{10,13}" title="Debe contener entre 10 y 13 números" minlength="10" maxlength="13" />
                      <button class="btn btn-outline-secondary remove-phone-btn" type="button">Eliminar</button>
                </div>`;
                    phoneContainer.append(newPhoneInput);

                    // Funcionalidad para eliminar teléfonos
                    $('.remove-phone-btn').last().on('click', function() {
                      $(this).parent().remove();
                    });

                    // Validar que solo se ingresen números en el campo de teléfono
                    $('.phone-input').last().on('keypress', function(event) {
                      if (!/[0-9]/.test(event.key)) {
                        event.preventDefault();
                      }
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Límite alcanzado',
                      text: 'No puedes agregar más de 3 teléfonos.',
                    });
                  }
                });

                // Validar que solo se ingresen números en los campos existentes de teléfono
                $('.phone-input').on('keypress', function(event) {
                  if (!/[0-9]/.test(event.key)) {
                    event.preventDefault();
                  }
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