<?php
session_start(); // Iniciar sesión para usar variables de sesión
include("conexion.php");

if (isset($_POST['enviar'])) {

    // Validar si se ingresó el correo electrónico
    if (empty($_POST['Email'])) {
        $_SESSION['email_error'] = 'Por favor, ingrese un email válido.';
        $_SESSION['active_tab'] = 'recuperar_contraseña';
        header("Location: recuperar_contrasena.php");
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
        header("Location: recuperar_contrasena.php");
        exit();
    }

    // Aquí puedes agregar el código para enviar el correo de recuperación de contraseña

    header("Location: recuperar_contraseña.php");
    exit(); // Salir del script
}
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Recuperar contraseña - Chefclass</title>
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
                                <span class="app-brand-text demo text-body fw-bold">Chefclass</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Recuperar contraseña!</h4>
                        <p class="mb-4">Por favor ingresá tú email para recuperar tu contraseña. Te enviaremos un enlace para restablecerla.</p>

                        <form class="mb-3" action="recovery.php" method="POST">

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

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="Email" placeholder="Ingresá tú email" required autofocus>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit" name="enviar">Enviar email</button>
                            </div>

                        </form>

                        <p class="text-center">
                            <span>Recordaste tú contraseña?</span>
                            <a href="Login.php">
                                <span>Volver atrás.</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

    <script>
        const container = document.querySelector("#container");
    </script>

    <!-- / Content -->
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