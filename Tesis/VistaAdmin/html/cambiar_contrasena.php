<?php
session_start(); // Iniciar sesión para usar variables de sesión
include("conexion.php");

if (isset($_POST['enviar'])) {
  $new_password = $_POST['new_password'] ?? '';
  $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

  // Verificar si los campos están vacíos
  if (empty($new_password) || empty($confirmar_contrasena)) {
    $_SESSION['error'] = 'Los campos de contraseña no pueden estar vacíos.';
    header("Location: cambiar_contrasena.php");
    exit();
  }

  // Verificar si las contraseñas coinciden
  if ($new_password !== $confirmar_contrasena) {
    $_SESSION['error'] = 'Las contraseñas no coinciden.';
    header("Location: cambiar_contrasena.php");
    exit();
  }

  // Verificar si la contraseña cumple con los requisitos de seguridad
  if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\W).{8,}$/', $new_password)) {
    $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un carácter especial.';
    header("Location: cambiar_contrasena.php");
    exit();
  }

  // Si pasa todas las validaciones, continuar con el cambio de contraseña
  $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
  $id_usuario = $_POST['id'];

  $sql = "UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?";
  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("si", $hashed_password, $id_usuario);

  if ($stmt->execute()) {
    $_SESSION['success'] = 'Contraseña actualizada correctamente.';
    header("Location: Login.php");
  } else {
    $_SESSION['error'] = 'Error al actualizar la contraseña.';
    header("Location: cambiar_contrasena.php");
  }
  exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Cambiar contraseña - Chefclass</title>
  <!-- Canonical SEO -->
  <link rel="canonical" href="https://themeselection.com/item/sneat-bootstrap-html-admin-template/">
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../../VistaCliente/img/chefclassFinal.png" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <!-- Icons -->
  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- Page CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>

  <!-- Config -->
  <script src="../assets/js/config.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    
    .alert {
      color: red;
      border: 1px solid red;
      padding: 10px;
      margin-top: 10px;
      background-color: #fdd;
    }
  </style>

</head>

<body>


  <!-- Content -->

  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register -->
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                </span>
                <span class="app-brand-text demo text-body fw-bold">Chefclass</span>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-2">Cambiar contraseña.</h4>
            <p class="mb-4">Por favor ingresá tú nueva contraseña.</p>

            <form id="changePasswordForm" action="change_password.php" method="POST">

              <!-- Mensajes de error -->
              <?php if (isset($_SESSION['email_error'])) { ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo $_SESSION['email_error']; ?>
                </div>
              <?php unset($_SESSION['email_error']);
              } ?>

              <?php if (isset($_SESSION['contrasena_error'])) { ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo $_SESSION['contrasena_error']; ?>
                </div>
              <?php unset($_SESSION['contrasena_error']);
              } ?>

              <?php if (isset($_SESSION['email_incorrecto'])) { ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo $_SESSION['email_incorrecto']; ?>
                </div>
              <?php unset($_SESSION['email_incorrecto']);
              } ?>

              <?php if (isset($_SESSION['contrasena_incorrecta'])) { ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo $_SESSION['contrasena_incorrecta']; ?>
                </div>
              <?php unset($_SESSION['contrasena_incorrecta']);
              } ?>

              <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">


              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Nueva contraseña</label>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="new_password" class="form-control" name="new_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>

              <ul class="ps-3 mb-3">
                <li class="mb-1" id="length">Mínimo 8 caracteres o más.</li>
                <li class="mb-1" id="uppercase">Al menos una letra mayúscula.</li>
                <li class="mb-1" id="lowercase">Al menos una letra minúscula.</li>
                <li id="special">Al menos un símbolo o carácter especial.</li>
              </ul>

              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Repetir contraseña</label>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="confirmar_contrasena" class="form-control" name="confirmar_contrasena" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>

              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit" name="enviar">Cambiar contraseña</button>
              </div>
            </form>

            <p class="text-center">
              <span>Quieres volver al inicio de sesión?</span>
              <a href="Login.php">
                <span>Iniciar sesión</span>
              </a>
            </p>
          </div>
        </div>
        <!-- /Register -->
      </div>
    </div>
  </div>

  <!-- / Content -->

  <script>
    const password = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirmar_contrasena');
    const length = document.getElementById('length');
    const uppercase = document.getElementById('uppercase');
    const lowercase = document.getElementById('lowercase');
    const special = document.getElementById('special');

    // Añadir escuchadores de eventos para validar la contraseña al escribir
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);

    // Función para validar los requisitos de la contraseña
    function validatePassword() {
      const pwdValue = password.value;

      // Validar longitud
      const validLength = pwdValue.length >= 8;
      length.innerHTML = validLength ? '✔ Mínimo 8 caracteres o más.' : '✖ Mínimo 8 caracteres o más.';

      // Validar mayúsculas
      const validUppercase = /[A-Z]/.test(pwdValue);
      uppercase.innerHTML = validUppercase ? '✔ Al menos una letra mayúscula.' : '✖ Al menos una letra mayúscula.';

      // Validar minúsculas
      const validLowercase = /[a-z]/.test(pwdValue);
      lowercase.innerHTML = validLowercase ? '✔ Al menos una letra minúscula.' : '✖ Al menos una letra minúscula.';

      // Validar caracteres especiales
      const validSpecial = /[\W]/.test(pwdValue);
      special.innerHTML = validSpecial ? '✔ Al menos un símbolo o carácter especial.' : '✖ Al menos un símbolo o carácter especial.';

      return validLength && validUppercase && validLowercase && validSpecial;
    }

    // Validar confirmación de contraseña antes de enviar el formulario
    document.querySelector("form").addEventListener("submit", function(e) {
      // Limpiar contenedor de mensajes
      const alertContainer = document.getElementById('alert-container');
      alertContainer.innerHTML = '';
      alertContainer.style.display = 'none';

      const passwordsMatch = password.value === confirmPassword.value;
      const passwordValid = validatePassword();

      if (!passwordsMatch) {
        e.preventDefault();
        showMessage('Las contraseñas no coinciden.');
      } else if (!passwordValid) {
        e.preventDefault();
        showMessage('La contraseña no cumple los requisitos.');
      }
    });

    // Función para mostrar/ocultar contraseña
    function togglePasswordVisibility(id, icon) {
      const input = document.getElementById(id);
      const eyeIcon = icon.querySelector('i');

      if (input.type === "password") {
        input.type = "text";
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      } else {
        input.type = "password";
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    }

    // Función para mostrar mensajes de error
    function showMessage(message) {
      const alertContainer = document.getElementById('alert-container');
      alertContainer.innerHTML = message;
      alertContainer.style.display = 'block';
    }
  </script>

  <script>
    document.querySelector("form").addEventListener("submit", function(e) {
      const alertContainer = document.getElementById('alert-container');
      alertContainer.innerHTML = '';
      alertContainer.style.display = 'none';

      const passwordsMatch = password.value === confirmPassword.value;
      const passwordValid = validatePassword();

      if (!password.value.trim() || !confirmPassword.value.trim()) {
        e.preventDefault();
        showMessage('Los campos de contraseña no pueden estar vacíos.');
      } else if (!passwordsMatch) {
        e.preventDefault();
        showMessage('Las contraseñas no coinciden.');
      } else if (!passwordValid) {
        e.preventDefault();
        showMessage('La contraseña no cumple los requisitos.');
      }
    });
  </script>

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->

  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/libs/hammer/hammer.js"></script>
  <script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>


  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>


</body>

</html>

<!-- beautify ignore:end -->