<?php
session_start();
include("conexion.php");

// Lógica para el inicio de sesión
if (isset($_POST['enviar'])) {
    // Verificar reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $secret = '6LfLNUUqAAAAAJ7EG0hamBVmkOZT2zH_G3uKCCyb';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        $_SESSION['captcha_error'] = 'Por favor, complete el CAPTCHA.';
        $_SESSION['active_tab'] = 'login';
        header("Location: Login.php");
        exit();
    } else {
        // Validar si se ingresó el email
        if (empty($_POST['email'])) {
            $_SESSION['email_error'] = 'Por favor ingresa un email válido.';
            $_SESSION['active_tab'] = 'login';
            header("Location: Login.php");
            exit();
        }

        // Validar si se ingresó la contraseña
        if (empty($_POST['contrasena'])) {
            $_SESSION['contrasena_error'] = 'Por favor ingresa una contraseña.';
            $_SESSION['active_tab'] = 'login';
            header("Location: Login.php");
            exit();
        }

        // Si se ingresaron ambos campos, continuar con el proceso de inicio de sesión
        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];

        // Consulta preparada para verificar el inicio de sesión
        $stmt = $conexion->prepare("SELECT U.id_usuario, U.nombre, U.apellido FROM usuarios U 
                                    INNER JOIN emails_usuarios EU ON U.id_usuario = EU.id_usuario
                                    WHERE EU.email = ? AND U.contrasena = ?");
        $stmt->bind_param("ss", $email, $contrasena);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $reg = $resultado->fetch_assoc();
            $_SESSION['id_usuario'] = $reg['id_usuario'];
            $_SESSION['nombre'] = $reg['nombre'];
            $_SESSION['apellido'] = $reg['apellido'];
            header("Location: admin.php");
        } else {
            // Verificar si el correo electrónico es incorrecto
            $stmt = $conexion->prepare("SELECT * FROM emails_usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado_email = $stmt->get_result();

            if ($resultado_email->num_rows == 0) {
                $_SESSION['email_incorrecto'] = 'El correo electrónico ingresado es incorrecto.';
            }

            // Verificar si la contraseña es incorrecta
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE contrasena = ?");
            $stmt->bind_param("s", $contrasena);
            $stmt->execute();
            $resultado_contrasena = $stmt->get_result();

            if ($resultado_contrasena->num_rows == 0) {
                $_SESSION['contrasena_incorrecta'] = 'La contraseña ingresada es incorrecta.';
            }

            $_SESSION['active_tab'] = 'login';
            header("Location: Login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="../css/Login.css" />
    <title>Crear Cuenta</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/Login.css">
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up <?php if (isset($_SESSION['active_tab']) && $_SESSION['active_tab'] == 'register') {
                                                echo 'active';
                                            } ?>">
            <form action="crear_cuenta.php" method="POST" onsubmit="return validarForm()">
                <h1>Crear cuenta</h1>
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
                <?php if (isset($_SESSION['usuario_duplicado'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['usuario_duplicado']; ?>
                    </div>
                <?php unset($_SESSION['usuario_duplicado']);
                } ?>

                <input type="text" placeholder="Nombre" name="nombre" pattern="[A-Za-z\s]+" title="Solo letras y espacios" oninput="validarNombre(this)">
                <input type="text" placeholder="Apellido" name="apellido" pattern="[A-Za-z]+" title="Solo letras" oninput="validarApellido(this)">

                <select class="form-input" name="genero" id="genero" required>
                    <?php
                    require "conexion.php";
                    $g = mysqli_query($conexion, "SELECT * FROM generos");
                    while ($opciones = mysqli_fetch_row($g)) {
                    ?>
                        <option value="<?php echo $opciones[0] ?>"><?php echo $opciones[1] ?></option>
                    <?php } ?>
                </select>

                <input type="text" placeholder="Nombre de usuario" name="usuario" pattern="\S+" title="No se permiten espacios" oninput="validarSinEspacios(this)">
                <input type="email" placeholder="Email" name="email" pattern="\S+" title="No se permiten espacios" oninput="validarSinEspacios(this)">

                <div class="input-group">
                    <input type="password" class="form-control" placeholder="Contraseña" name="contrasena" id="contrasena_reg" pattern="\S+" title="No se permiten espacios" oninput="validarContrasena(); validarSinEspacios(this)">
                    <span class="input-group-text cursor-pointer" onclick="togglePassword('contrasena_reg')">
                        <i class="fa fa-eye" id="togglePasswordIcon_reg"></i>
                    </span>
                </div>

                <div class="input-group">
                    <input type="password" class="form-control" placeholder="Confirmar Contraseña" name="confirmar_contrasena" id="confirmar_contrasena" pattern="\S+" title="No se permiten espacios" oninput="validarSinEspacios(this)">
                    <span class="input-group-text cursor-pointer" onclick="togglePassword('confirmar_contrasena')">
                        <i class="fa fa-eye" id="toggleConfirmPasswordIcon"></i>
                    </span>
                </div>

                <div id="alert-container"></div> <!-- Contenedor para las alertas -->

                <button type="submit" name="registrar" id="registrar">Crear cuenta</button>
            </form>
        </div>





        <!-- Formulario de Inicio de Sesión -->
        <div class="form-container sign-in <?php if (isset($_SESSION['active_tab']) && $_SESSION['active_tab'] == 'login') {
                                                echo 'active';
                                            } ?>">
            <form action="login.php" method="POST">
                <h1>Iniciar Sesión</h1>
                <!-- Mensajes de inicio de sesión -->
                <?php if (isset($_SESSION['captcha_error'])) { ?>
                    <div class="alert alert-warning" role="alert">
                        <?php echo $_SESSION['captcha_error']; ?>
                    </div>
                <?php unset($_SESSION['captcha_error']);
                } ?>
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
                <?php if (isset($_SESSION['registro_exitoso'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['registro_exitoso']; ?>
                    </div>
                <?php unset($_SESSION['registro_exitoso']);
                } ?>
                <input type="text" placeholder="Email" name="email" id="email">
                <div class="input-group">
                    <input type="password" class="form-control" placeholder="Contraseña" name="contrasena" id="contrasena">
                    <span class="input-group-text cursor-pointer" onclick="togglePassword('contrasena')">
                        <i class="fa fa-eye" id="togglePasswordIcon"></i>
                    </span>
                </div>
                <div class="g-recaptcha" data-sitekey="6LfLNUUqAAAAAPHUK_ihDD6xCAxYR56T7QksqAAd"></div>
                <button type="submit" name="enviar" id="enviar">Iniciar sesión</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bienvenido! Su contraseña debe tener :</h1>
                    <br>
                    <ul id="password-criteria" class="text-muted small">
                        <li id="min-length">Mínimo 8 caracteres <i class="fa fa-times"></i></li>
                        <li id="upper-case">Al menos una mayúscula <i class="fa fa-times"></i></li>
                        <li id="lower-case">Al menos una minúscula <i class="fa fa-times"></i></li>
                        <li id="special-char">Al menos un carácter especial <i class="fa fa-times"></i></li>
                    </ul>
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
        const container = document.querySelector("#container");

        const registerBtn = document.querySelector("#register");
        const loginBtn = document.querySelector("#login");

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });

        // Para el ojito de la contraseña
        const togglePassword = (id) => {
            const passwordField = document.getElementById(id);
            const toggleIcon = document.querySelector(`#${id} ~ .input-group-text i`);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        };

        // Validar nombre para solo permitir letras y espacios
        function validarNombre(input) {
            const regex = /^[A-Za-z\s]+$/;
            if (!regex.test(input.value)) {
                input.value = input.value.replace(/[^A-Za-z\s]/g, '');
            }
        }

        // Validar apellido para solo permitir letras
        function validarApellido(input) {
            const regex = /^[A-Za-z]+$/;
            if (!regex.test(input.value)) {
                input.value = input.value.replace(/[^A-Za-z]/g, '');
            }
        }

        // Validar para no permitir espacios
        function validarSinEspacios(input) {
            input.value = input.value.replace(/\s+/g, '');
        }

        // Validar los requisitos de la contraseña
        function validarContrasena() {
            const password = document.getElementById('contrasena_reg').value;
            const minLength = document.getElementById('min-length');
            const upperCase = document.getElementById('upper-case');
            const lowerCase = document.getElementById('lower-case');
            const specialChar = document.getElementById('special-char');

            const lengthRegex = /.{8,}/;
            const upperCaseRegex = /[A-Z]/;
            const lowerCaseRegex = /[a-z]/;
            const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

            updateCriteria(minLength, lengthRegex.test(password));
            updateCriteria(upperCase, upperCaseRegex.test(password));
            updateCriteria(lowerCase, lowerCaseRegex.test(password));
            updateCriteria(specialChar, specialCharRegex.test(password));
        }

        // Actualizar los iconos de los criterios
        function updateCriteria(element, isValid) {
            const icon = element.querySelector('i');
            if (isValid) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-check');
            } else {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-times');
            }
        }

        // Mostrar alerta personalizada en el contenedor específico
        function mostrarAlerta(mensaje) {
            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = `
            <div class="alert alert-danger" role="alert">
                ${mensaje}
            </div>
        `;
        }

        // Validar que las contraseñas coincidan
        function validarForm() {
            const contrasena = document.getElementById('contrasena_reg').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;

            if (contrasena !== confirmarContrasena) {
                mostrarAlerta('Las contraseñas no coinciden.');
                return false;
            }

            // Validar los criterios de la contraseña
            const minLength = document.getElementById('min-length').querySelector('i').classList.contains('fa-check');
            const upperCase = document.getElementById('upper-case').querySelector('i').classList.contains('fa-check');
            const lowerCase = document.getElementById('lower-case').querySelector('i').classList.contains('fa-check');
            const specialChar = document.getElementById('special-char').querySelector('i').classList.contains('fa-check');

            if (!minLength || !upperCase || !lowerCase || !specialChar) {
                mostrarAlerta('Requisitos de contraseña incompletos');
                return false;
            }

            return true;
        }

        // Script para mostrar la pestaña correcta basado en la variable de sesión
        document.addEventListener('DOMContentLoaded', function() {
            if ('<?php echo $_SESSION['active_tab'] ?? ''; ?>' === 'register') {
                container.classList.add('active');
            } else {
                container.classList.remove('active');
            }
        });
    </script>





    

    <?php
    // Limpiar la variable de sesión después de usarla
    unset($_SESSION['active_tab']);
    ?>

</body>

</html>