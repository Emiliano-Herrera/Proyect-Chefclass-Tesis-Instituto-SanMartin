<?php
session_start();
include("conexion.php");

// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function contieneCodigoPHP($input)
{
    // Patrones comunes de código PHP
    $patrones = [
        '/<\?php/i',
        '/<\?=/i',
        '/<\?/i',
        '/eval\(/i',
        '/system\(/i',
        '/exec\(/i',
        '/shell_exec\(/i',
        '/passthru\(/i',
        '/`.*`/i',
        '/include\(/i',
        '/require\(/i',
        '/file_get_contents\(/i',
        '/fopen\(/i',
        '/fwrite\(/i'
    ];

    foreach ($patrones as $patron) {
        if (preg_match($patron, $input)) {
            return true;
        }
    }
    return false;
}

// Verificar si se ha enviado el formulario
if (isset($_POST['registrar'])) {

    // Validar todos los inputs contra código PHP
    $inputs = [
        'email' => $_POST['email'],
        'usuario' => $_POST['usuario'],
        'contrasena' => $_POST['contrasena']
    ];

    // Validar también cada teléfono
    foreach ($_POST['telefonos'] as $index => $telefono) {
        $inputs["telefono_$index"] = $telefono;
    }

    foreach ($inputs as $key => $value) {
        if (contieneCodigoPHP($value)) {
            $_SESSION['registro_error'] = "El texto ingresado contiene código no permitido.";
            header("Location: login_cargar_usuario.php");
            exit();
        }
    }

    // Verificar campos vacíos
    if (empty($_POST['email']) || empty($_POST['telefonos']) || empty($_POST['contrasena']) || empty($_POST['confirmar_contrasena']) || empty($_POST['usuario'])) {
        $_SESSION['registro_error'] = 'Por favor, completa todos los campos.';
        header("Location: login_cargar_usuario.php");
        exit();
    }

    $nombre = $_SESSION['registro_nombre'];
    $apellido = $_SESSION['registro_apellido'];
    $genero = $_SESSION['registro_genero'];
    $email = $_POST['email'];
    $telefonos = $_POST['telefonos'];
    $tipos = $_POST['tipos'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    $usuario = $_POST['usuario'];

    // Validar que las contraseñas coincidan
    if ($contrasena != $confirmar_contrasena) {
        $_SESSION['registro_error'] = 'Las contraseñas no coinciden.';
        header("Location: login_cargar_usuario.php");
        exit();
    }

    // Verificar si el género existe en la tabla generos
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM generos WHERE id_genero = ?");
    $stmt->bind_param("i", $genero);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists == 0) {
        $_SESSION['registro_error'] = 'El género seleccionado no existe.';
        header("Location: login_cargar_usuario.php");
        exit();
    }

    // Guardar los datos en la sesión para pasarlos a cargar_ubicacion.php
    $_SESSION['registro_email'] = $email;
    $_SESSION['registro_telefonos'] = $telefonos;
    $_SESSION['registro_tipos'] = $tipos;
    $_SESSION['registro_contrasena'] = $contrasena;
    $_SESSION['registro_usuario'] = $usuario;

    // Redirigir a cargar_ubicacion.php
    header("Location: cargar_ubicacion.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../css/Login.css">
    <title>Crear Cuenta</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #fff;
        }

        .telefono-group {
            margin-bottom: 10px;
            position: relative;
        }

        .telefono-group input {
            width: calc(100% - 30px);
            display: inline-block;
            background-color: #fff;
        }

        .telefono-group .remove-telefono {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ff3333;
            font-size: 20px;
            background-color: transparent;
            border: none;
        }

        .telefono-group .remove-telefono:hover {
            color: #ff6666;
        }

        .valid {
            color: green;
        }

        .invalid {
            color: red;
        }

        .criteria {
            background-color: #fff;
            padding: 5px;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .password {
            color: black !important;
        }
    </style>
</head>

<body>
    <div class="container active" id="container">
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Importante!</h1>
                    <p>Características de la contraseña:</p>
                    <div>
                        <ul>
                            <li id="min-length" class="criteria invalid"><i class="fa fa-times"></i> Al menos 8 caracteres</li>
                            <li id="upper-case" class="criteria invalid"><i class="fa fa-times"></i> Una letra mayúscula</li>
                            <li id="lower-case" class="criteria invalid"><i class="fa fa-times"></i> Una letra minúscula</li>
                            <li id="special-char" class="criteria invalid"><i class="fa fa-times"></i> Un carácter especial</li>
                        </ul>
                    </div>
                    <button class="hidden" id="login" onclick="redirigirLogin()">Iniciar sesión</button>
                </div>
            </div>
        </div>

        <script>
            function redirigirLogin() {
                window.location.href = 'login.php#sign-up-form';
            }
        </script>

        <div class="form-container sign-up active" id="sign-up-form">
            <form id="registerForm" action="login_cargar_usuario.php" method="POST" onsubmit="return validarRegistro()">
                <h2>Crear cuenta</h2>
                <!-- Mensajes de registro -->
                <?php if (isset($_SESSION['registro_error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['registro_error']; ?>
                    </div>
                <?php unset($_SESSION['registro_error']);
                } ?>
                <input type="text" placeholder="Email" name="email" id="email" oninput="validarSinEspacios(this)">
                <!-- //?============================================================================================================================================= -->
                <div id="telefonos-container">
                    <div class="telefono-group">
                        <input type="text" placeholder="Teléfono" name="telefonos[]" oninput="validarSinEspacios(this)" maxlength="17" minlength="10">
                        <span class="remove-telefono">&times;</span>
                        <select name="tipos[]" class="form-input">
                            <option value="Personal">Personal</option>
                            <option value="Laboral">Laboral</option>
                        </select>
                    </div>
                </div>
                <button type="button" id="agregar-telefono">Agregar Teléfono</button>
                <!-- //?============================================================================================================================================= -->
                <input type="text" placeholder="Nombre de usuario" name="usuario" id="usuario" oninput="validarSinEspacios(this)">
                <!-- //?============================================================================================================================================= -->
                <input type="password" class="form-control password" placeholder="Contraseña" name="contrasena" id="contrasena_reg" oninput="validarContrasena()">
                <!-- //?============================================================================================================================================= -->
                <input type="password" class="form-control password" placeholder="Confirmar Contraseña" name="confirmar_contrasena" id="confirmar_contrasena" oninput="validarContrasena()">
                
                <button type="submit" name="registrar" id="registrar">Registrar</button>
                <button type="button" id="volver" class="btn-volver">Volver</button>
            </form>
        </div>
    </div>

    <script>
        function validarRegistro() {
            const email = document.getElementById('email').value.trim();
            const telefonos = document.querySelectorAll('input[name="telefonos[]"]');
            const usuario = document.getElementById('usuario').value.trim();
            const contrasena = document.getElementById('contrasena_reg').value.trim();
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value.trim();
            const recaptchaResponse = grecaptcha.getResponse();

            if (email === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Email es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            for (let telefono of telefonos) {
                if (telefono.value.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Teléfono es obligatorio.',
                        confirmButtonText: 'Aceptar'
                    });
                    return false;
                }
            }

            if (usuario === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Nombre de usuario es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (contrasena === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Contraseña es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (confirmarContrasena === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Confirmar Contraseña es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (contrasena !== confirmarContrasena) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            // Validar que la contraseña cumpla con los requisitos
            const criteria = [{
                    regex: /.{8,}/,
                    message: 'La contraseña debe tener al menos 8 caracteres.'
                },
                {
                    regex: /[A-Z]/,
                    message: 'La contraseña debe tener al menos una letra mayúscula.'
                },
                {
                    regex: /[a-z]/,
                    message: 'La contraseña debe tener al menos una letra minúscula.'
                },
                {
                    regex: /[!@#$%^&*(),.?":{}|<>]/,
                    message: 'La contraseña debe tener al menos un carácter especial.'
                }
            ];

            for (let criterion of criteria) {
                if (!criterion.regex.test(contrasena)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: criterion.message,
                        confirmButtonText: 'Aceptar'
                    });
                    return false;
                }
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
    </script>

    <script>
        function validarSinEspacios(input) {
            input.value = input.value.replace(/\s/g, '');
        }



        function validarRegistro() {
            const email = document.getElementById('email').value.trim();
            const telefonos = document.querySelectorAll('input[name="telefonos[]"]');
            const usuario = document.getElementById('usuario').value.trim();
            const contrasena = document.getElementById('contrasena_reg').value.trim();
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value.trim();
            const recaptchaResponse = grecaptcha.getResponse();

            if (email === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Email es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            for (let telefono of telefonos) {
                if (telefono.value.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El campo Teléfono es obligatorio.',
                        confirmButtonText: 'Aceptar'
                    });
                    return false;
                }
            }

            if (usuario === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Nombre de usuario es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (contrasena === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Contraseña es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (confirmarContrasena === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El campo Confirmar Contraseña es obligatorio.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            if (contrasena !== confirmarContrasena) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden.',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            }

            // Validar que la contraseña cumpla con los requisitos
            const criteria = [{
                    regex: /.{8,}/,
                    message: 'La contraseña debe tener al menos 8 caracteres.'
                },
                {
                    regex: /[A-Z]/,
                    message: 'La contraseña debe tener al menos una letra mayúscula.'
                },
                {
                    regex: /[a-z]/,
                    message: 'La contraseña debe tener al menos una letra minúscula.'
                },
                {
                    regex: /[!@#$%^&*(),.?":{}|<>]/,
                    message: 'La contraseña debe tener al menos un carácter especial.'
                }
            ];

            for (let criterion of criteria) {
                if (!criterion.regex.test(contrasena)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: criterion.message,
                        confirmButtonText: 'Aceptar'
                    });
                    return false;
                }
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

        function validarSinEspacios(input) {
            input.value = input.value.replace(/\s/g, '');
        }

        function validarContrasena() {
            const password = document.getElementById('contrasena_reg').value;
            const confirmPassword = document.getElementById('confirmar_contrasena').value;

            const criteria = [{
                    element: document.getElementById('min-length'),
                    isValid: /.{8,}/.test(password)
                },
                {
                    element: document.getElementById('upper-case'),
                    isValid: /[A-Z]/.test(password)
                },
                {
                    element: document.getElementById('lower-case'),
                    isValid: /[a-z]/.test(password)
                },
                {
                    element: document.getElementById('special-char'),
                    isValid: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                }
            ];

            criteria.forEach(({
                element,
                isValid
            }) => {
                const icon = element.querySelector('i');
                if (isValid) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-check');
                    element.classList.remove('invalid');
                    element.classList.add('valid');
                } else {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-times');
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                }
            });

            updateInputClass('contrasena_reg', criteria.every(c => c.isValid));
            updateInputClass('confirmar_contrasena', password === confirmPassword);
        }

        function updateInputClass(inputId, isValid) {
            const inputElement = document.getElementById(inputId);
            if (isValid) {
                inputElement.classList.add('valid');
                inputElement.classList.remove('invalid');
            } else {
                inputElement.classList.add('invalid');
                inputElement.classList.remove('valid');
            }
        }

        function togglePassword(id) {
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
        }

        document.getElementById('registrar').addEventListener('click', () => {
            document.getElementById('container').classList.add('right-panel-active');
        });

        document.getElementById('volver').addEventListener('click', () => {
            // Redirigir a Login.php con el parámetro from=create_account
            window.location.href = 'Login.php?from=create_account';
        });


        // Función para permitir solo números en los inputs de teléfono
        function soloNumerosTelefono(input) {
            input.setAttribute('inputmode', 'numeric');
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Aplica la función a los inputs existentes al cargar la página
        document.querySelectorAll('input[name="telefonos[]"]').forEach(soloNumerosTelefono);

        // Cuando agregues un nuevo teléfono, aplica la función también
        document.getElementById('agregar-telefono').addEventListener('click', () => {
            const container = document.getElementById('telefonos-container');
            const telefonoGroups = container.getElementsByClassName('telefono-group');
            if (telefonoGroups.length < 2) {
                const newGroup = telefonoGroups[0].cloneNode(true);
                const newInput = newGroup.querySelector('input');
                newInput.value = '';
                soloNumerosTelefono(newInput); // <-- Aplica aquí
                newGroup.querySelector('.remove-telefono').addEventListener('click', function() {
                    const telefonoGroups = document.getElementsByClassName('telefono-group');
                    if (telefonoGroups.length > 1) {
                        this.parentElement.remove();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Acción no permitida',
                            text: 'Debe haber al menos un teléfono.'
                        });
                    }
                });
                container.appendChild(newGroup);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Límite alcanzado',
                    text: 'No puedes agregar más de 2 teléfonos.'
                });
            }
        });


        document.querySelectorAll('.remove-telefono').forEach(button => {
            button.addEventListener('click', function() {
                const telefonoGroups = document.getElementsByClassName('telefono-group');
                if (telefonoGroups.length > 1) {
                    this.parentElement.remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Acción no permitida',
                        text: 'Debe haber al menos un teléfono.'
                    });
                }
            });
        });
    </script>

    <script src="../js/login.js"></script>

    <script>
        // Función para validar inputs contra código PHP
        function validarContraPHP(input) {
            const patronesPHP = [
                /<\?php/i,
                /<\?=/i,
                /<\?/i,
                /eval\(/i,
                /system\(/i,
                /exec\(/i,
                /shell_exec\(/i,
                /passthru\(/i,
                /`.*`/i,
                /include\(/i,
                /require\(/i
            ];

            for (const patron of patronesPHP) {
                if (patron.test(input.value)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de seguridad',
                        text: 'El texto ingresado contiene código no permitido.',
                        confirmButtonText: 'Aceptar'
                    });
                    input.value = input.value.replace(patron, '');
                    return false;
                }
            }
            return true;
        }

        // Agregar event listeners a los campos
        document.addEventListener('DOMContentLoaded', function() {
            // Campos principales
            document.getElementById('email')?.addEventListener('input', function(e) {
                validarContraPHP(e.target);
            });

            document.getElementById('usuario')?.addEventListener('input', function(e) {
                validarContraPHP(e.target);
            });

            document.getElementById('contrasena_reg')?.addEventListener('input', function(e) {
                validarContraPHP(e.target);
            });

            // Campos de teléfono (los existentes y los nuevos que se agreguen)
            document.querySelectorAll('input[name="telefonos[]"]').forEach(input => {
                input.addEventListener('input', function(e) {
                    validarContraPHP(e.target);
                });
            });
        });
    </script>

    <?php
    // Limpiar la variable de sesión después de usarla
    unset($_SESSION['active_tab']);
    ?>
</body>

</html>