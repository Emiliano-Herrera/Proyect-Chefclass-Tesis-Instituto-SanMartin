<?php
session_start(); // Iniciar sesión para usar variables de sesión
include("conexion.php");

if (isset($_POST['enviar'])) {

  // Validar si se ingresó el correo electrónico
  if (empty($_POST['Email'])) {
    $_SESSION['email_error'] = 'Por favor, ingrese un email válido.';
    $_SESSION['active_tab'] = 'recuperar_contraseña';
    header("Location: recuperar_contraseña.php");
    exit(); // Salir del script si falta el correo electrónico
  }

  // Si se ingresaron bien el correo, continuar con el proceso de recuperación
  $email = $_POST['Email'];

  // Verificar si el correo electrónico es incorrecto
  $stmt = $conexion->prepare("SELECT * FROM Emails_Usuarios WHERE Email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $resultado_email = $stmt->get_result();

  if ($resultado_email->num_rows == 0) {
    $_SESSION['email_incorrecto'] = 'El correo electrónico ingresado es incorrecto.';
    $_SESSION['active_tab'] = 'recuperar_contraseña';
    header("Location: recuperar_contraseña.php");
    exit();
  }

  // Aquí puedes agregar el código para enviar el correo de recuperación de contraseña

  header("Location: recuperar_contraseña.php");
  exit(); // Salir del script
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../css/Login.css">
  <title>Recuperar Contraseña</title>
  <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="container" id="container">
    <div class="form-container sign-in active">
      <form action="recovery.php" method="POST">
        <h1>Recuperar Contraseña</h1>

        <!-- Mensajes de error -->
        <?php if (isset($_SESSION['email_error'])) { ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['email_error']; ?>
          </div>
        <?php unset($_SESSION['email_error']);
        } ?>

        <?php if (isset($_SESSION['email_incorrecto'])) { ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['email_incorrecto']; ?>
          </div>
        <?php unset($_SESSION['email_incorrecto']);
        } ?>

        <input type="email" placeholder="Email" name="Email" class="form-control" required autofocus />

        <button type="submit" name="enviar" class="btn btn-primary d-grid w-100">Enviar E-mail</button>
        <button class="btn btn-secondary d-grid w-100" type="button" onclick="window.location.href='Login.php';">Volver</button>
      </form>

    </div>

    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-right">
          <h1>¡Hola!</h1>
          <p>Ingresa tu correo electrónico para recuperar tu contraseña. Te enviaremos un enlace para restablecerla.</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    const container = document.querySelector("#container");
  </script>
</body>

</html>