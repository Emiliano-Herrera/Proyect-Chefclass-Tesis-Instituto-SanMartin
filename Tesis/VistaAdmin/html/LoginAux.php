<?php
session_start();
include("conexion.php");

// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si se ha enviado el formulario de inicio de sesión
if (isset($_POST['enviar'])) {
    // Verificar reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $secret = '6LeVg9kqAAAAANzb63CBr3IuAXJNk4_90wo-LoRG';  //  clave secreta
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        $_SESSION['login_captcha_error'] = 'Por favor, complete el CAPTCHA.';
        $_SESSION['active_tab'] = 'login';
        header("Location: login.php");
        exit();
    } else {

        if (empty($_POST['email'])) {
            $_SESSION['login_email_error'] = 'El email es obligatorio';
            $_SESSION['active_tab'] = 'login';
            header("Location: login.php");
        } else {
            if (empty($_POST['contrasena'])) {
                $_SESSION['login_contrasena_error'] = 'La contraseña es obligatoria';
                $_SESION['active_tab'] = 'login;';
                header('Location:login.php');
            }
        }


        // Lógica de verificación del login
        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];

        // Consulta preparada para verificar el inicio de sesión 
        $stmt = $conexion->prepare("SELECT U.id_usuario, U.nombre, U.apellido, R.nombre_rol, R.id_rol 
                                    FROM usuarios U 
                                    INNER JOIN emails_usuarios EU ON U.id_usuario = EU.id_usuario 
                                    INNER JOIN roles R ON U.rol = R.id_rol 
                                    WHERE EU.email = ? AND U.contrasena = ? AND U.estado='habilitado'");
        $stmt->bind_param("ss", $email, $contrasena);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $reg = $resultado->fetch_assoc();
            $_SESSION['id_usuario'] = $reg['id_usuario'];
            $_SESSION['nombre'] = $reg['nombre'];
            $_SESSION['apellido'] = $reg['apellido'];

            if ($resultado->num_rows > 0) {
                $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
                header("Location: login.php");
            }

            // Verificar si el usuario fue redirigido desde un intento de ver una receta
            if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
                $redirectTo = $_GET['redirect_to'];
                $idReceta = $_GET['id'] ?? '';
                header("Location: $redirectTo?id=$idReceta");
                exit();
            }


            // Redirigir según el rol
            if (
                $reg['nombre_rol'] == 'Administrador' || // Administrador
                $reg['nombre_rol'] == 'Supervisor De Cuentas' ||
                $reg['nombre_rol'] == 'Supervisor De Recetas'
            ) {
                header("Location: perfil2.php");
                exit();
            }

            // Redirigir al index si no hay redirección específica
            header("Location: ../../VistaCliente/html/index.php");
            exit();
        } else {
            // Manejar errores de inicio de sesión
            $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
            header("Location: login.php");
            exit();
        }
    }
}

// Verificar si el usuario está pendiente o deshabilitado
if (isset($_POST['enviar'])) {
    $stmt = $conexion->prepare("SELECT * FROM usuarios U 
                                INNER JOIN emails_usuarios EU ON U.id_usuario = EU.id_usuario 
                                WHERE EU.email = ? AND U.estado = 'pendiente'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado_pendiente = $stmt->get_result();

    if ($resultado_pendiente->num_rows > 0) {
        $_SESSION['login_cuenta_pendiente'] = 'Tu cuenta aún no ha sido habilitada. Por favor, espera al correo de confirmación.';
    } else {
        $stmt = $conexion->prepare("SELECT * FROM usuarios U 
                                    INNER JOIN emails_usuarios EU ON U.id_usuario = EU.id_usuario 
                                    WHERE EU.email = ? AND U.estado != 'habilitado'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado_estado = $stmt->get_result();

        if ($resultado_estado->num_rows > 0) {
            $_SESSION['login_cuenta_deshabilitada'] = 'Tu cuenta está deshabilitada. Prueba con otra.';
        } else {
            $stmt = $conexion->prepare("SELECT * FROM emails_usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado_email = $stmt->get_result();

            if ($resultado_email->num_rows == 0) {
                $_SESSION['login_email_incorrecto'] = 'El correo electrónico ingresado es incorrecto.';
            }

            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE contrasena = ?");
            $stmt->bind_param("s", $contrasena);
            $stmt->execute();
            $resultado_contrasena = $stmt->get_result();

            if ($resultado_contrasena->num_rows == 0) {
                $_SESSION['login_contrasena_incorrecta'] = 'La contraseña ingresada es incorrecta.';
            }
        }
    }

    $_SESSION['active_tab'] = 'login';
    header("Location: login.php");
    exit();
}

// Lógica para el registro de usuario (modificada para redirigir to login_cargar_usuario.php)
if (isset($_POST['action']) && $_POST['action'] == 'register') {
    // Verificar reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $secret = '6LeVg9kqAAAAANzb63CBr3IuAXJNk4_90wo-LoRG';  // Tu nueva clave secreta
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        $_SESSION['registro_captcha_error'] = 'Por favor, complete el CAPTCHA.';
        $_SESSION['active_tab'] = 'register';
        header("Location: login.php");
        exit();
    } else {
        // Validar otros campos aquí
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $genero = $_POST['genero'];

        // Guardar los datos en la sesión para pasarlos a login_cargar_usuario.php
        $_SESSION['registro_nombre'] = $nombre;
        $_SESSION['registro_apellido'] = $apellido;
        $_SESSION['registro_genero'] = $genero;

        // Redirigir a login_cargar_usuario.php
        header("Location: login_cargar_usuario.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../css/Login.css">
    <title>Crear Cuenta</title>
    <link rel="icon" href="../../VistaCliente/img/chefclassFinal.png">

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up <?php if (isset($_SESSION['active_tab']) && $_SESSION['active_tab'] == 'register') echo 'active'; ?>" id="sign-up-form">
            <form id="registerForm" action="login.php" method="POST" onsubmit="return validarRegistro()">
                <h2>Crear cuenta</h2>
                <!-- Mensajes de registro -->
                <?php if (isset($_SESSION['registro_error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['registro_error']; ?>
                    </div>
                <?php unset($_SESSION['registro_error']);
                } ?>
                <?php if (isset($_SESSION['email_duplicado'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['email_duplicado']; ?>
                    </div>
                <?php unset($_SESSION['email_duplicado']);
                } ?>
                <?php if (isset($_SESSION['registro_captcha_error'])) { ?>
                    <div class="alert alert-warning" role="alert">
                        <?php echo $_SESSION['registro_captcha_error']; ?>
                    </div>
                <?php unset($_SESSION['registro_captcha_error']);
                } ?>

                <?php if (isset($_SESSION['registro_captcha_success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['registro_captcha_success']; ?>
                    </div>
                <?php unset($_SESSION['registro_captcha_success']);
                } ?>



                <div id="alert-container"></div> <!-- Contenedor para las alertas -->

                <input type="text" placeholder="Nombre" name="nombre" id="nombre" oninput="validarNombre(this)"
                    value="<?php echo isset($_SESSION['registro_nombre']) ? htmlspecialchars($_SESSION['registro_nombre']) : ''; ?>">

                <input type="text" placeholder="Apellido" name="apellido" id="apellido" oninput="validarApellido(this)"
                    value="<?php echo isset($_SESSION['registro_apellido']) ? htmlspecialchars($_SESSION['registro_apellido']) : ''; ?>">

                <select class="form-input" name="genero" id="genero" required>
                    <option value="">Selecciona un género</option>
                    <?php
                    $g = mysqli_query($conexion, "SELECT * FROM generos");
                    $genero_sesion = isset($_SESSION['registro_genero']) ? $_SESSION['registro_genero'] : '';
                    while ($opciones = mysqli_fetch_row($g)) { ?>
                        <option value="<?php echo $opciones[0] ?>" <?php if ($genero_sesion == $opciones[0]) echo 'selected'; ?>>
                            <?php echo $opciones[1] ?>
                        </option>
                    <?php } ?>
                </select>

                <div class="g-recaptcha" data-sitekey="6LeVg9kqAAAAAIMcjLWNuyRhU1tdZUO55BFcIN0W"></div>
                <button type="submit" name="action" value="register" id="registrar">Siguiente</button>

                <button type="button" class="small-screen-sign-in" onclick="showSignInForm();">Iniciar sesión</button>
            </form>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                function toggleSignInButton() {
                    let screenWidth = window.innerWidth;
                    let signInButton = document.querySelector('.small-screen-sign-in');

                    if (screenWidth < 768) {
                        signInButton.style.display = "block";
                    } else {
                        signInButton.style.display = "none";
                    }
                }

                // Ejecutar la función al cargar la página y al cambiar el tamaño de la ventana
                toggleSignInButton();
                window.addEventListener("resize", toggleSignInButton);
            });
        </script>

        <!--Script para las contraseñas y cambiar de color en la pantalla para celular -->

        <div class="form-container sign-in <?php if (isset($_SESSION['active_tab']) && $_SESSION['active_tab'] == 'login') echo 'active'; ?>" id="sign-in-form">
            <form action="login.php" method="POST" id="loginForm">
                <h1>Iniciar Sesión </h1>
                <img src="../../VistaCliente/img/chefclassFinal.png" alt="Logo" style="width: 150px; height: auto;">
                <?php if (isset($_GET['message'])) { ?>
                    <div class="alert alert-primary" role="alert" id="alert-message">
                        <?php
                        switch ($_GET['message']) {
                            case 'ok':
                                echo 'Por favor revisa tu correo electronico';
                                break;
                            case 'success_password':
                                echo 'Inicia sesión con tu nueva contraseña';
                                break;
                            default:
                                echo 'Algo salió mal, intenta de nuevo';
                                break;
                        }
                        ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            var alertMessage = document.getElementById('alert-message');
                            if (alertMessage) {
                                alertMessage.style.display = 'none';
                            }
                        }, 8000);
                    </script>
                <?php } ?>

                <!-- Mensajes de inicio de sesión -->
                <?php if (isset($_SESSION['login_captcha_error'])) { ?>
                    <div class="alert alert-warning" role="alert">
                        <?php echo $_SESSION['login_captcha_error']; ?>
                    </div>
                <?php unset($_SESSION['login_captcha_error']);
                } ?>
                <?php if (isset($_SESSION['login_email_error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['login_email_error']; ?>
                    </div>
                <?php unset($_SESSION['login_email_error']);
                } ?>
                <?php if (isset($_SESSION['login_contrasena_error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['login_contrasena_error']; ?>
                    </div>
                <?php unset($_SESSION['login_contrasena_error']);
                } ?>
                <?php if (isset($_SESSION['login_email_incorrecto'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['login_email_incorrecto']; ?>
                    </div>
                <?php unset($_SESSION['login_email_incorrecto']);
                } ?>
                <?php if (isset($_SESSION['login_contrasena_incorrecta'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['login_contrasena_incorrecta']; ?>
                    </div>
                <?php unset($_SESSION['login_contrasena_incorrecta']);
                } ?>
                <?php if (isset($_SESSION['login_cuenta_pendiente'])) { ?>
                    <div class="alert alert-warning" role="alert">
                        <?php echo $_SESSION['login_cuenta_pendiente']; ?>
                    </div>
                <?php unset($_SESSION['login_cuenta_pendiente']);
                } ?>
                <?php if (isset($_SESSION['login_cuenta_deshabilitada'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['login_cuenta_deshabilitada']; ?>
                    </div>
                <?php unset($_SESSION['login_cuenta_deshabilitada']);
                } ?>
                <?php if (isset($_SESSION['registro_exitoso'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['registro_exitoso']; ?>
                    </div>
                <?php unset($_SESSION['registro_exitoso']);
                } ?>

                <?php if (isset($_SESSION['login_error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['login_error']; ?>
                    </div>
                <?php unset($_SESSION['login_error']);
                } ?>
                <input type="text" placeholder="Email" name="email" id="email">
                <div class="input-group">
                    <input type="password" class="form-control" placeholder="Contraseña" name="contrasena" id="contrasena">
                    <span class="input-group-append cursor-pointer" onclick="togglePassword('contrasena')">
                        <i class="fa fa-eye-slash" id="togglePasswordIcon"></i>
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="recuperar_contrasena.php">
                        <small>¿Has olvidado tu contraseña?</small>
                    </a>
                </div>
                <div class="g-recaptcha" data-sitekey="6LeVg9kqAAAAAIMcjLWNuyRhU1tdZUO55BFcIN0W"></div>
                <button type="submit" name="enviar" id="enviar">Iniciar sesión</button>
                <!-- Botón para mostrar el formulario de registro en pantallas pequeñas -->
                <button type="button" class="register-button" onclick="showSignUpForm();">Registrar</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bienvenido!</h1>
                    <p>Ingrese sus datos personales para utilizar todas las funciones del sitio</p>
                    <button class="hidden" id="login">Iniciar sesión</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hola!</h1>
                    <p>Registra tus datos personales para utilizar todas las funciones del sitio</p>
                    <button class="hidden" id="register">Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ===================================================================
        // FUNCIONES PARA DETECTAR CÓDIGO PHP Y CONTENIDO MALICIOSO
        // ===================================================================
        
        // Función para detectar código PHP o contenido malicioso
        function contieneCodigoPHP(texto) {
            if (!texto || texto.trim() === '') return false;
            
            // Patrones comunes de código PHP y scripts maliciosos
            const patrones = [
                /<\?php/i,        // Apertura de código PHP
                /<\?=/i,          // Short open tags
                /<\?/i,           // Otras formas de abrir PHP
                /\?>/i,           // Cierre de código PHP
                /echo\s/i,        // Comando echo
                /print\s/i,       // Comando print
                /die\(/i,         // Función die
                /exit\(/i,        // Función exit
                /system\(/i,      // Función system
                /exec\(/i,        // Función exec
                /shell_exec\(/i,  // Función shell_exec
                /eval\(/i,        // Función eval
                /base64_decode/i, // Decodificación base64
                /script\s*>/i,    // Etiquetas de script
                /onload\s*=/i,    // Eventos DOM
                /onerror\s*=/i,
                /onclick\s*=/i,
                /javascript:/i,   // Protocolo javascript
                /document\.cookie/i, // Acceso a cookies
            ];

            return patrones.some(patron => patron.test(texto));
        }

        // Validar formulario de login antes de enviar
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const contrasena = document.getElementById('contrasena').value;
            
            if (contieneCodigoPHP(email) || contieneCodigoPHP(contrasena)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Contenido no permitido',
                    text: 'El texto ingresado contiene código o caracteres no permitidos.',
                    confirmButtonText: 'Entendido'
                });
            }
        });

        // ===================================================================
        // FUNCIONES EXISTENTES DEL FORMULARIO
        // ===================================================================
        
        function showSignUpForm() {
            document.getElementById('container').classList.add('active');
        }

        function showSignInForm() {
            document.getElementById('container').classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if ('<?php echo $_SESSION['active_tab'] ?? ''; ?>' === 'register') {
                document.getElementById('container').classList.add('active');
            } else {
                document.getElementById('container').classList.remove('active');
            }
        });

        const container = document.querySelector("#container");

        const registerBtn = document.querySelector("#register");
        const loginBtn = document.querySelector("#login");

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });

        function validarNombre(input) {
            // Elimina espacios al principio
            input.value = input.value.replace(/^\s+/, '');
            // Reemplaza múltiples espacios por uno solo
            input.value = input.value.replace(/\s{2,}/g, ' ');
            // Solo letras y espacios
            input.value = input.value.replace(/[^A-Za-z\s]/g, '');
        }

        function validarApellido(input) {
            // Elimina espacios al principio
            input.value = input.value.replace(/^\s+/, '');
            // Reemplaza múltiples espacios por uno solo
            input.value = input.value.replace(/\s{2,}/g, ' ');
            // Solo letras y espacios
            input.value = input.value.replace(/[^A-Za-z\s]/g, '');
        }

        function validarSinEspacios(input) {
            input.value = input.value.replace(/\s+/g, '');
        }

        function togglePassword(id) {
            const passwordInput = document.getElementById(id);
            const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        // ===================================================================
        // VALIDACIÓN DE REGISTRO (MODIFICADA PARA INCLUIR DETECCIÓN DE CÓDIGO PHP)
        // ===================================================================
        
        function validarRegistro() {
            const nombre = document.getElementById('nombre').value;
            const apellido = document.getElementById('apellido').value;
            const genero = document.getElementById('genero').value;
            const recaptchaResponse = grecaptcha.getResponse();

            // Validar contenido malicioso
            if (contieneCodigoPHP(nombre) || contieneCodigoPHP(apellido)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Contenido no permitido',
                    text: 'El texto ingresado contiene código o caracteres no permitidos.',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }

            // Validaciones existentes
            if (nombre.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Nombre es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (apellido.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Apellido es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (genero === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe seleccionar un género.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (recaptchaResponse === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe completar el reCAPTCHA.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            return true;
        }

        // Reiniciar el reCAPTCHA si la sesión indica que es necesario
        window.onload = function() {
            if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.reset === 'function') {
                <?php if (isset($_SESSION['reset_recaptcha']) && $_SESSION['reset_recaptcha'] === true) { ?>
                    grecaptcha.reset();
                    <?php unset($_SESSION['reset_recaptcha']); // Limpiar la variable de sesión 
                    ?>
                <?php } ?>
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = '<?php echo $_SESSION['active_tab'] ?? ''; ?>';
            const container = document.getElementById('container');

            if (activeTab === 'register') {
                container.classList.add('right-panel-active'); // Mover el toggle a "Crear cuenta"
            } else {
                container.classList.remove('right-panel-active'); // Mostrar "Iniciar sesión" por defecto
            }
        });
    </script>

    <script src="../js/login.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('from') === 'create_account') {
                document.getElementById('container').classList.add('active');
            }
        });
    </script>

    <?php
    // Limpiar la variable de sesión después de usarla
    unset($_SESSION['active_tab']);
    ?>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>