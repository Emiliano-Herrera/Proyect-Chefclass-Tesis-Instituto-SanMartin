<?php
session_start();
include("conexion.php");

// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =============================================================================
// PROCESAMIENTO DEL FORMULARIO DE LOGIN
// =============================================================================
if (isset($_POST['enviar'])) {
    // 1. VALIDACIÓN DE CAMPOS INDIVIDUALES
    // -------------------------------------------------------------------------
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Validar email vacío
    if (empty($email)) {
        $_SESSION['login_email_error'] = "El campo email es obligatorio";
        $_SESSION['active_tab'] = 'login';
        header("Location: Login.php");
        exit();
    }

    // Validar contraseña vacía
    if (empty($password)) {
        $_SESSION['login_contrasena_error'] = "El campo contraseña es obligatorio";
        $_SESSION['login_email'] = $email;
        $_SESSION['active_tab'] = 'login';
        header("Location: Login.php");
        exit();
    }

    // 2. VALIDACIÓN DE reCAPTCHA (SIEMPRE REQUERIDO)
    // -------------------------------------------------------------------------
    if (empty($recaptcha_response)) {
        $_SESSION['login_captcha_error'] = "Por favor, complete el reCAPTCHA";
        $_SESSION['login_email'] = $email;
        $_SESSION['active_tab'] = 'login';
        header("Location: Login.php");
        exit();
    }

    // Validar reCAPTCHA con Google
    $secret = '6LeVg9kqAAAAANzb63CBr3IuAXJNk4_90wo-LoRG';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        $_SESSION['login_captcha_error'] = "Error en la verificación reCAPTCHA. Intente nuevamente.";
        $_SESSION['login_email'] = $email;
        $_SESSION['active_tab'] = 'login';
        header("Location: Login.php");
        exit();
    }

    // 3. VERIFICACIÓN EN BASE DE DATOS
    // -------------------------------------------------------------------------
    try {
        // Consulta unificada que incluye el estado 'habilitado'
        $stmt = $conexion->prepare("SELECT U.id_usuario, U.nombre, U.apellido, U.contrasena, 
                                           U.estado, R.nombre_rol, EU.email
                                    FROM usuarios U 
                                    INNER JOIN emails_usuarios EU ON U.id_usuario = EU.id_usuario 
                                    INNER JOIN roles R ON U.rol = R.id_rol 
                                    WHERE EU.email = ? AND U.contrasena = ? AND U.estado = 'habilitado'");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // 3.1. USUARIO NO ENCONTRADO O CREDENCIALES INCORRECTAS (si NO se encontró ningún usuario)
        if ($resultado->num_rows === 0) {
            // Verificar si el email existe pero la contraseña es incorrecta
            $stmt_email = $conexion->prepare("SELECT U.estado FROM usuarios U 
                                            INNER JOIN emails_usuarios EU ON U.id_usuario = EU.id_usuario 
                                            WHERE EU.email = ?");
            $stmt_email->bind_param("s", $email);
            $stmt_email->execute();
            $resultado_email = $stmt_email->get_result();

            if ($resultado_email->num_rows > 0) {
                $usuario_estado = $resultado_email->fetch_assoc();

                // Verificar estado de la cuenta
                if ($usuario_estado['estado'] === 'pendiente') {
                    $_SESSION['login_cuenta_pendiente'] = "Su cuenta está pendiente de aprobación. Contacte al administrador.";
                } elseif ($usuario_estado['estado'] === 'deshabilitado') {
                    $_SESSION['login_cuenta_deshabilitada'] = "Su cuenta está deshabilitada. Contacte al administrador.";
                } else {
                    $_SESSION['login_contrasena_incorrecta'] = "Contraseña incorrecta";
                }
            } else {
                $_SESSION['login_email_incorrecto'] = "El email ingresado no está registrado";
            }

            $_SESSION['login_email'] = $email;
            $_SESSION['active_tab'] = 'login';
            header("Location: Login.php");
            exit();
        }

        $usuario = $resultado->fetch_assoc();

        // 4. LOGIN EXITOSO - CREAR SESIÓN
        // ---------------------------------------------------------------------
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['rol'] = $usuario['nombre_rol'];

        // Verificar si el usuario fue redirigido desde un intento de ver una receta
        if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
            $redirectTo = $_GET['redirect_to'];
            $idReceta = $_GET['id'] ?? '';
            header("Location: $redirectTo?id=$idReceta");
            exit();
        }

        // Redirección según el rol
        if (in_array($usuario['nombre_rol'], ['Administrador', 'Supervisor De Cuentas', 'Supervisor De Recetas'])) {
            header("Location: perfil2.php");
        } else {
            header("Location: ../../VistaCliente/html/index.php");
        }
        exit();
    } catch (Exception $e) {
        // Error en la consulta
        $_SESSION['login_error'] = "Error del sistema. Intente nuevamente más tarde.";
        $_SESSION['login_email'] = $email;
        $_SESSION['active_tab'] = 'login';
        header("Location: Login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login - Chefclass</title>
    <meta name="description" content="Sistema de autenticación" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../VistaCliente/img/chefclassFinal.png" />

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
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <div class="card">
                    <div class="card-body">

                        <div class="app-brand justify-content-center">
                            <a href="#" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bold">Iniciar sesión</span>
                            </a>
                        </div>

                        <!-- MENSAJE DE QUE SE ENVIO EL OKEY PARA EL CAMBIO DE CONTRASEÑA -->
                        <?php
                        /*  Este div es para mostrar el mensaje si es que existe el mensaje enviado desde recovery.php */
                        if (isset($_GET['message'])) {
                        ?>
                            <div class="alert alert-primary" role="alert">
                                <?php
                                switch ($_GET['message']) {
                                    case 'ok':
                                        echo 'Por favor revisá tú email para el cambio de contraseña.';
                                        break;
                                    case 'success_password':
                                        echo 'Ya puedes iniciar sesión con tú nueva contraseña.';
                                        break;
                                    default:
                                        echo 'Algo salio mal, intenta de nuevo.';
                                        break;
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>

                        <!-- MENSAJES DE ERROR ESPECÍFICOS -->
                        <?php if (isset($_SESSION['login_email_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?php echo $_SESSION['login_email_error']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_email_error']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['login_contrasena_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?php echo $_SESSION['login_contrasena_error']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_contrasena_error']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['login_captcha_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?php echo $_SESSION['login_captcha_error']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_captcha_error']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['login_email_incorrecto'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?php echo $_SESSION['login_email_incorrecto']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_email_incorrecto']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['login_contrasena_incorrecta'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?php echo $_SESSION['login_contrasena_incorrecta']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_contrasena_incorrecta']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['login_cuenta_pendiente'])): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Atención:</strong> <?php echo $_SESSION['login_cuenta_pendiente']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_cuenta_pendiente']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['login_cuenta_deshabilitada'])): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Atención:</strong> <?php echo $_SESSION['login_cuenta_deshabilitada']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_cuenta_deshabilitada']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['login_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> <?php echo $_SESSION['login_error']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['login_error']); ?>
                        <?php endif; ?>

                        <!-- FORMULARIO DE LOGIN -->
                        <form id="formAuthentication" class="mb-3" action="Login.php" method="POST">

                            <!-- CAMPO EMAIL -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Ingrese su Email" autofocus
                                    value="<?php echo isset($_SESSION['login_email']) ? htmlspecialchars($_SESSION['login_email']) : ''; ?>">
                                <?php unset($_SESSION['login_email']); ?>
                            </div>

                            <!-- CAMPO CONTRASEÑA -->
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Contraseña</label>
                                    <a href="recuperar_contrasena.php">
                                        <small>¿Olvidó su contraseña?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="Ingrese su contraseña" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility()">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- reCAPTCHA (SIEMPRE PRESENTE) -->
                            <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="6LeVg9kqAAAAAIMcjLWNuyRhU1tdZUO55BFcIN0W"></div>
                            </div>

                            <!-- BOTÓN DE ENVÍO -->
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit" name="enviar">
                                    Iniciar sesión
                                </button>
                            </div>

                        </form>

                        <p class="text-center">
                            <span>¿Es nuevo en la plataforma?</span>
                            <a href="./vista-crear-usuario.php">
                                <span>Crear una cuenta</span>
                            </a>
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
        // Función para mostrar/ocultar contraseña
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('#password + .input-group-text i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            }
        }

        // Auto-cerrar alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Resetear reCAPTCHA cada vez que se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof grecaptcha !== 'undefined') {
                setTimeout(function() {
                    grecaptcha.reset();
                }, 100);
            }
        });

        // Resetear reCAPTCHA cuando se muestra un error
        <?php if (
            isset($_SESSION['login_captcha_error']) ||
            isset($_SESSION['login_email_error']) ||
            isset($_SESSION['login_contrasena_error']) ||
            isset($_SESSION['login_email_incorrecto']) ||
            isset($_SESSION['login_contrasena_incorrecta'])
        ): ?>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof grecaptcha !== 'undefined') {
                    setTimeout(function() {
                        grecaptcha.reset();
                    }, 500);
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>