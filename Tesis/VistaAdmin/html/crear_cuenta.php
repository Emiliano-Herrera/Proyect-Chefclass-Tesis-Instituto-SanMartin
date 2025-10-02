<?php
session_start();
include("conexion.php");

if (isset($_POST['registrar'])) {
    // Validar si se ingresaron todos los campos
    if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['usuario']) || empty($_POST['contrasena']) || empty($_POST['confirmar_contrasena']) || empty($_POST['email']) || empty($_POST['genero'])) {
        $_SESSION['registro_error'] = "Todos los campos son obligatorios.";
        $_SESSION['active_tab'] = 'register';
        header("Location: Login.php");
        exit();
    }

    // Validar que las contraseñas coincidan
    if ($_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
        $_SESSION['registro_error'] = "Las contraseñas no coinciden.";
        $_SESSION['active_tab'] = 'register';
        header("Location: Login.php");
        exit();
    }

    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $email = $_POST['email'];
    $genero = $_POST['genero'];
    $rol = 2; // Asignar el valor a una variable

    // Verificar si el email ya está registrado
    $stmt = $conexion->prepare("SELECT * FROM emails_usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['email_duplicado'] = "El email ya está registrado.";
        $_SESSION['active_tab'] = 'register';
        header("Location: Login.php");
        exit();
    }

    // Verificar si el nombre de usuario ya está registrado
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['usuario_duplicado'] = "El nombre de usuario ya está registrado.";
        $_SESSION['active_tab'] = 'register';
        header("Location: Login.php");
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, nombre_usuario, contrasena, genero, rol) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $nombre, $apellido, $usuario, $contrasena, $genero, $rol);

    if ($stmt->execute()) {
        $id_usuario = $conexion->insert_id;
        $stmt = $conexion->prepare("INSERT INTO emails_usuarios (id_usuario, email) VALUES (?, ?)");
        $stmt->bind_param("is", $id_usuario, $email);
        $stmt->execute();
        $_SESSION['registro_exitoso'] = "Cuenta creada exitosamente. Por favor, inicia sesión.";
        $_SESSION['active_tab'] = 'login';
        header("Location: Login.php");
    } else {
        $_SESSION['registro_error'] = "Error al crear la cuenta. Por favor, intenta de nuevo.";
        $_SESSION['active_tab'] = 'register';
        header("Location: Login.php");
    }
    exit();
}
