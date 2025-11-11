<?php
session_start();
include('conexion.php');

// ========== VALIDACIÓN reCAPTCHA ==========
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptcha_response)) {
    $_SESSION['error_registro'] = "Por favor, complete el reCAPTCHA";
    header("Location: vista-crear-usuario.php");
    exit();
}

// Validar reCAPTCHA con Google
$secret = '6LeVg9kqAAAAANzb63CBr3IuAXJNk4_90wo-LoRG'; // Tu secret key del login
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha_response");
$response_keys = json_decode($response, true);

if (intval($response_keys["success"]) !== 1) {
    $_SESSION['error_registro'] = "Error en la verificación reCAPTCHA. Intente nuevamente.";
    header("Location: vista-crear-usuario.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar campos requeridos
    $required_fields = ['nombre', 'apellido', 'genero', 'usuario', 'email', 'contrasena', 'provincia', 'departamento', 'localidad', 'pais'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error_registro'] = "Error: El campo $field es requerido.";
            header("Location: vista-crear-usuario.php");
            exit();
        }
    }

    // Asignar variables
    $latitud = $_POST['latitud'] ?? 0;
    $longitud = $_POST['longitud'] ?? 0;
    $provincia = $_POST['provincia'] ?? 'Provincia no detectada';
    $departamento = $_POST['departamento'] ?? 'Departamento no detectado';
    $localidad = $_POST['localidad'] ?? 'Localidad no detectada';
    $barrio = $_POST['barrio'] ?? '';
    $pais = $_POST['pais'] ?? 'Argentina';

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $genero = $_POST['genero'];
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = 2; // Rol de cliente
    $estado = 'pendiente';

    // Manejar teléfonos (adaptado para un solo teléfono del formulario)
    $telefonos = [];
    $tipos = [];

    if (isset($_POST['telefono']) && !empty($_POST['telefono'])) {
        $telefonos = [$_POST['telefono']];
        $tipos = ['Personal'];
    } else if (isset($_POST['telefonos'])) {
        $telefonos = json_decode($_POST['telefonos'], true);
        $tipos = json_decode($_POST['tipos'], true);
    }

    if (empty($telefonos)) {
        $_SESSION['error_registro'] = 'Error: Debe proporcionar al menos un número de teléfono.';
        header("Location: vista-crear-usuario.php");
        exit();
    }

    // Verificar si el usuario ya existe
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($user_exists);
    $stmt->fetch();
    $stmt->close();

    if ($user_exists > 0) {
        $_SESSION['error_registro'] = 'El nombre de usuario ya existe';
        header("Location: vista-crear-usuario.php");
        exit();
    }

    // Verificar si el email ya existe
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM emails_usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($email_exists);
    $stmt->fetch();
    $stmt->close();

    if ($email_exists > 0) {
        $_SESSION['error_registro'] = 'El email ya está registrado';
        header("Location: vista-crear-usuario.php");
        exit();
    }

    // Verificar si el género existe
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM generos WHERE id_genero = ?");
    $stmt->bind_param("i", $genero);
    $stmt->execute();
    $stmt->bind_result($genero_exists);
    $stmt->fetch();
    $stmt->close();

    if ($genero_exists == 0) {
        $_SESSION['error_registro'] = 'El género seleccionado no es válido';
        header("Location: vista-crear-usuario.php");
        exit();
    }

    // Insertar localidad
    $stmt = $conexion->prepare("INSERT INTO localidades (provincia, departamento, localidad, barrio, latitud, longitud, pais) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdds", $provincia, $departamento, $localidad, $barrio, $latitud, $longitud, $pais);

    if (!$stmt->execute()) {
        $_SESSION['error_registro'] = "Error al insertar localidad: " . $stmt->error;
        header("Location: vista-crear-usuario.php");
        exit();
    }

    $id_localidad = $stmt->insert_id;
    $stmt->close();

    // Insertar usuario
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, genero, nombre_usuario, contrasena, rol, estado, id_localidad) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssi", $nombre, $apellido, $genero, $usuario, $contrasena, $rol, $estado, $id_localidad);

    if (!$stmt->execute()) {
        // Si falla, eliminar la localidad insertada
        $conexion->query("DELETE FROM localidades WHERE id_localidad = $id_localidad");
        $_SESSION['error_registro'] = "Error al insertar usuario: " . $stmt->error;
        header("Location: vista-crear-usuario.php");
        exit();
    }

    $id_usuario = $stmt->insert_id;
    $stmt->close();

    // Insertar email
    $stmt = $conexion->prepare("INSERT INTO emails_usuarios (id_usuario, email, tipo) VALUES (?, ?, 'Personal')");
    $stmt->bind_param("is", $id_usuario, $email);

    if (!$stmt->execute()) {
        // Si falla, eliminar usuario y localidad
        $conexion->query("DELETE FROM usuarios WHERE id_usuario = $id_usuario");
        $conexion->query("DELETE FROM localidades WHERE id_localidad = $id_localidad");
        $_SESSION['error_registro'] = "Error al insertar email: " . $stmt->error;
        header("Location: vista-crear-usuario.php");
        exit();
    }

    $stmt->close();

    // Insertar teléfonos
    foreach ($telefonos as $index => $telefono) {
        $tipo = $tipos[$index] ?? 'Personal';
        $stmt = $conexion->prepare("INSERT INTO telefonos_usuarios (id_usuario, telefono, tipo) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_usuario, $telefono, $tipo);

        if (!$stmt->execute()) {
            // Si falla, eliminar todo lo insertado
            $conexion->query("DELETE FROM telefonos_usuarios WHERE id_usuario = $id_usuario");
            $conexion->query("DELETE FROM emails_usuarios WHERE id_usuario = $id_usuario");
            $conexion->query("DELETE FROM usuarios WHERE id_usuario = $id_usuario");
            $conexion->query("DELETE FROM localidades WHERE id_localidad = $id_localidad");
            $_SESSION['error_registro'] = "Error al insertar teléfono: " . $stmt->error;
            header("Location: vista-crear-usuario.php");
            exit();
        }

        $stmt->close();
    }

    // Éxito - redirigir con mensaje
    $_SESSION['registro_exitoso'] = 'Su cuenta ha sido creada exitosamente. Espere a que sea validada por un administrador.';
    $_SESSION['active_tab'] = 'login';
    header("Location: Login.php");
    exit();
} else {
    // Si no viene del formulario, redirigir
    header("Location: vista-crear-usuario.php");
    exit();
}
