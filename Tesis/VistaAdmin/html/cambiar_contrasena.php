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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../css/Login.css">
  <title>Cambiar Contraseña</title>
  <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .input-group {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-group .form-control {
      flex-grow: 1;
    }

    .input-group .input-group-text {
      cursor: pointer;
      border: none;
      background: none;
      color: #555;
    }

    .input-group .input-group-text i {
      font-size: 1.2rem;
    }

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
  <div class="container" id="container">
    <div class="form-container sign-in active">
      <form id="changePasswordForm" action="change_password.php" method="POST">
        <h1>Cambiar Contraseña</h1>

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

        <div class="input-group">
          <input type="password" placeholder="Nueva Contraseña" name="new_password" class="form-control" id="new_password">
          <span class="input-group-text" onclick="togglePasswordVisibility('new_password', this)"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
        </div>
        <div class="input-group">
          <input type="password" placeholder="Confirmar Nueva Contraseña" name="confirmar_contrasena" class="form-control" id="confirmar_contrasena">
          <span class="input-group-text" onclick="togglePasswordVisibility('confirmar_contrasena', this)"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
        </div>

        <div id="alert-container" class="alert" style="display: none;"></div> <!-- Contenedor para los mensajes -->

        <button type="submit" name="enviar" class="btn btn-primary d-grid w-100">Cambiar Contraseña</button>
        <button class="btn btn-secondary d-grid w-100" type="button" onclick="window.location.href='recuperar_contrasena.php';">Volver</button>
      </form>

    </div>

    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-right">
          <h1>¡Hola!</h1>
          <p>Cambia tu contraseña ingresando una nueva. Asegúrate de seguir las políticas de seguridad:</p>
          <ul class="ps-3 mb-0">
            <li class="mb-1" id="length">Mínimo 8 caracteres o más.</li>
            <li class="mb-1" id="uppercase">Al menos una letra mayúscula.</li>
            <li class="mb-1" id="lowercase">Al menos una letra minúscula.</li>
            <li id="special">Al menos un símbolo o carácter especial.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
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
</body>

</html>