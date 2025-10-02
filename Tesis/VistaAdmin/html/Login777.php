<?php
session_start(); // Iniciar sesión para usar variables de sesión
include("conexion.php");

if (isset($_POST['enviar'])) {

  // Verificar reCAPTCHA
  $recaptcha_response = $_POST['g-recaptcha-response'];
  $secret = '6LfLNUUqAAAAAJ7EG0hamBVmkOZT2zH_G3uKCCyb';
  $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha_response");
  $response_keys = json_decode($response, true);

  if (intval($response_keys["success"]) !== 1) {
    echo 'Por favor, complete el CAPTCHA.';
  } else {


    // Validar si se ingresó el correo electrónico
    if (empty($_POST['email'])) {
      $_SESSION['email_error'] = true;
      header("Location: Login.php");
      exit(); // Salir del script si falta el correo electrónico
    }

    // Validar si se ingresó la contraseña
    if (empty($_POST['contrasena'])) {
      $_SESSION['contrasena_error'] = true;
      header("Location: Login.php");
      exit(); // Salir del script si falta la contraseña
    }

    // Si se ingresaron ambos campos, continuar con el proceso de inicio de sesión
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT U.id_usuario , U.nombre , U.apellido  FROM usuarios U 
    INNER JOIN emails_usuarios EU ON U.id_usuario = EU.id_usuario
    WHERE EU.email = '$email' AND U.contrasena = '$contrasena'";

    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) > 0) {
      $reg = mysqli_fetch_row($resultado);
      $_SESSION['id_usuario'] = $reg[0];
      $_SESSION['nombre'] = $reg[1];
      $_SESSION['apellido'] = $reg[2];
      header("Location: admin.php");
    } else {
      // Verificar si el correo electrónico es incorrecto
      $sql_email = "SELECT * FROM emails_usuarios WHERE email = '$email'";
      $resultado_email = mysqli_query($conexion, $sql_email);

      if (mysqli_num_rows($resultado_email) == 0) {
        $_SESSION['email_incorrecto'] = true;
      }

      // Verificar si la contraseña es incorrecta
      $sql_contrasena = "SELECT * FROM usuarios WHERE contrasena = '$contrasena'";
      $resultado_contrasena = mysqli_query($conexion, $sql_contrasena);

      if (mysqli_num_rows($resultado_contrasena) == 0) {
        $_SESSION['contrasena_incorrecta'] = true;
      }

      header("Location: Login.php");
      exit(); // Salir del script
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>

  <!-- Esto es para el reCaptcha -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <!-- Your code -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Login ihm</title>

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

  <!-- Page CSS -->
  <!-- Page -->
  <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />
  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>

  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="../assets/js/config.js"></script>
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
              <a href="admin.html" class="app-brand-link gap-2">
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
                <span class="app-brand-text demo text-body fw-bolder">Ihm</span>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-2">Bienvenido! 👋</h4>

            <!-- //!99999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999 -->
            <!-- //TODO00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->

            <form class="mb-3" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control <?= isset($_SESSION['email_error']) ? 'is-invalid' : '' ?>" name="email" placeholder="Ingresa tu email" autofocus />
                <?php if (isset($_SESSION['email_error'])) { ?>
                  <div class="invalid-feedback">
                    Por favor ingresa un Email válido.
                  </div>
                <?php unset($_SESSION['email_error']);
                } ?>
                <?php if (isset($_SESSION['email_incorrecto'])) { ?>
                  <br>
                  <div class="alert alert-danger" role="alert">
                    El correo electrónico ingresado es incorrecto.
                  </div>
                <?php unset($_SESSION['email_incorrecto']);
                } ?>
              </div>

              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Contraseña</label>
                  <a href="recuperar_contraseña.php">
                    <small>¿Has olvidado tu contraseña?</small>
                  </a>
                </div>

                <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Contraseña</label>
                  <a href="cerrar-sesion.php">
                    <small>¿cerrar sesion?</small>
                  </a>
                </div>






                <?php

                /*  Este div es para mostrar el mensaje si es que existe el mensaje enviado desde recovery.php */

                if (isset($_GET['message'])) {
                ?>

                  <div class="alert alert-primary" role="alert">

                    <?php
                    switch ($_GET['message']) {
                      case 'ok':
                        echo 'Por favor revisa tu correo electronico';
                        break;

                      case 'success_password':
                        echo 'Inicia session con tu nueva contraseña';
                        break;

                      default:
                        echo 'Algo salio mal, intenta de nuevo';
                        break;
                    }
                    ?>
                  </div>


                <?php
                }
                ?>

                <div class="input-group input-group-merge">
                  <input type="password" class="form-control <?= isset($_SESSION['contrasena_error']) ? 'is-invalid' : '' ?>" name="contrasena" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  <?php if (isset($_SESSION['contrasena_error'])) { ?>
                    <div class="invalid-feedback">
                      Por favor ingresa una contraseña.
                    </div>
                  <?php unset($_SESSION['contrasena_error']);
                  } ?>
                  <?php if (isset($_SESSION['contrasena_incorrecta'])) { ?>
                    <br>
                    <div class="alert alert-danger" role="alert">
                      La contraseña ingresada es incorrecta.
                    </div>
                  <?php unset($_SESSION['contrasena_incorrecta']);
                  } ?>
                </div>
              </div>
              <!--Este es el reCaptcha-->

              <br>
              <div class="g-recaptcha" data-sitekey="6LfLNUUqAAAAAPHUK_ihDD6xCAxYR56T7QksqAAd"></div>
              <br>
              <br>



              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit" name="enviar">Iniciar Sesion</button>
              </div>
            </form>

            <?php if (isset($_SESSION['credenciales_incorrectas'])) { ?>
              <div class="alert alert-danger" role="alert">
                El correo electrónico o la contraseña ingresados son incorrectos.
              </div>
            <?php unset($_SESSION['credenciales_incorrectas']);
            } ?>


            <!--
            <p class="text-center">
              <span>Nuevo en nuestra plataforma?</span>
              <a href="auth-register-basic.html">
                <span>Crear una cuenta</span>
              </a>
            </p>-->
          
          </div>
        </div>
        <!-- /Register -->
      </div>
    </div>
  </div>

  <!-- / Content -->



  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="../assets/vendor/js/menu.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>