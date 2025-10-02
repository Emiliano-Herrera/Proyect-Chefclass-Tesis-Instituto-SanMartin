<?php
session_start();
include("conexion.php");

date_default_timezone_set('America/Argentina/Catamarca'); // Ajustar la zona horaria

header('Content-Type: application/json');

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'No hay una sesión activa.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capturar los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $rol = $_POST['rol'];
    $genero = $_POST['genero'];
    $username = $_POST['username'];
    $password = password_hash($_POST['contraseña'], PASSWORD_BCRYPT); // Encriptar la contraseña
    $estado = 'habilitado'; // Inicializar el estado como habilitado
    $fechaCreacion = date('Y-m-d H:i:s');

    // Capturar emails y teléfonos (son arrays)
    $emails = $_POST['emails'];
    $telefonos = $_POST['telefonos'];

    // Verificar si el nombre de usuario ya existe
    $stmtCheck = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?");
    $stmtCheck->bind_param("s", $username);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    if ($stmtCheck->num_rows > 0) {
        $stmtCheck->close();
        echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario ya existe. Por favor, elige otro.']);
        exit();
    }
    $stmtCheck->close();

    // Verificar si los correos electrónicos ya existen
    $stmtCheckEmail = $conexion->prepare("SELECT id_usuario FROM emails_usuarios WHERE email = ?");
    foreach ($emails as $email) {
        $stmtCheckEmail->bind_param("s", $email);
        $stmtCheckEmail->execute();
        $stmtCheckEmail->store_result();
        if ($stmtCheckEmail->num_rows > 0) {
            $stmtCheckEmail->close();
            echo json_encode(['status' => 'error', 'message' => 'El email ya existe: ' . $email]);
            exit();
        }
        $stmtCheckEmail->reset();
    }
    $stmtCheckEmail->close();

    // Función para generar nombres únicos para imágenes
    function F_gen_password($Paswd_Length)
    {
        $lower_ascii_bound = 50; // "2"
        $upper_ascii_bound = 122; // "z"
        $notuse = array(58, 59, 60, 61, 62, 63, 64, 73, 79, 91, 92, 93, 94, 95, 96, 108, 111);
        $i = 0;
        $password = '';
        while ($i < $Paswd_Length) {
            mt_srand((float) microtime() * 1000000);
            $randnum = mt_rand($lower_ascii_bound, $upper_ascii_bound);
            if (!in_array($randnum, $notuse)) {
                $password .= chr($randnum);
                $i++;
            }
        }
        return $password;
    }

    // Verificar si se envió el formulario y si se incluyó un archivo
    if (isset($_FILES['archivo'])) {
        $name_archivo = $_FILES['archivo']['name'];
        if (!empty($name_archivo)) {
            $path = "img_perfil";
            $nomdig = F_gen_password(13);
            if ($_FILES['archivo']['type'] == "image/pjpeg" || $_FILES['archivo']['type'] == "image/jpeg") {
                $extension = '.jpg';
            } else if ($_FILES['archivo']['type'] == "image/png") {
                $extension = '.png';
            } else if ($_FILES['archivo']['type'] == "image/gif") {
                $extension = '.gif';
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tipo de imagen no soportado.']);
                exit();
            }

            $nuevo_nombre = $path . '/' . $nomdig . $extension;

            if (move_uploaded_file($_FILES['archivo']['tmp_name'], $nuevo_nombre)) {
                $stmtUsuario = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, nombre_usuario, img, contrasena, genero, rol, usuario_creacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtUsuario->bind_param("sssssssss", $nombre, $apellido, $username, $nuevo_nombre, $password, $genero, $rol, $ID_Usuario, $estado);
                $stmtUsuario->execute();

                if ($stmtUsuario->affected_rows > 0) {
                    $idUsuarioUltimo = $conexion->insert_id;

                    // Guardar los emails en la tabla 'emails_usuarios'
                    $stmtEmails = $conexion->prepare("INSERT INTO emails_usuarios (id_usuario, email) VALUES (?, ?)");
                    foreach ($emails as $email) {
                        $stmtEmails->bind_param("is", $idUsuarioUltimo, $email);
                        $stmtEmails->execute();
                    }

                    // Guardar los teléfonos en la tabla 'telefonos_usuarios'
                    $stmtTelefonos = $conexion->prepare("INSERT INTO telefonos_usuarios (id_usuario, telefono) VALUES (?, ?)");
                    foreach ($telefonos as $telefono) {
                        $stmtTelefonos->bind_param("is", $idUsuarioUltimo, $telefono);
                        $stmtTelefonos->execute();
                    }

                    // Registrar en la tabla historial_usuarios
                    $accion = "Creación de usuario";
                    $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
                    $detalle = "El usuario '$username' ha sido creado por '$Nombre $Apellido' en la fecha '" . date('d-m-Y H:i:s') . "'.";

                    $detalle = mysqli_real_escape_string($conexion, $detalle);
                    $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES ('$idUsuarioUltimo', '$accion', '$detalle', '$fecha')";
                    $resultHistorial = mysqli_query($conexion, $sqlHistorial);

                    echo json_encode(['status' => 'success', 'message' => 'Usuario creado correctamente.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al crear el usuario.']);
                }

                $stmtUsuario->close();
                $stmtEmails->close();
                $stmtTelefonos->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al cargar el archivo de imagen.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Debe seleccionar un archivo de imagen.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se ha enviado ningún formulario.']);
    }
}
?>
