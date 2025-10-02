<?php
include("conexion.php");

if (isset($_POST['enviar'])) {
    $id = $_POST['id'];
    $pass = $_POST['new_password'];

    // Validar si se ingresó la contraseña
    if (empty($pass)) {
        $_SESSION['contrasena_error'] = true;
        header("Location: cambiar_contraseña.php?id=$id&message=error_password");
        exit();
    }

    // Validar que la contraseña tenga al menos 8 caracteres
    if (strlen($pass) < 8) {
        header("Location: cambiar_contraseña.php?id=$id&message=contrasena_sin8");
        exit();
    }

    // Validar que la contraseña tenga al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $pass)) {
        header("Location: cambiar_contraseña.php?id=$id&message=mayus");
        exit();
    }

    // Si todas las validaciones son correctas, actualizar la contraseña
    $query = "UPDATE usuarios SET contrasena = '$pass' WHERE ID_Usuario = '$id'";
    if ($conexion->query($query) === TRUE) {
        header("Location: Login.php?message=success_password");
    } else {
        echo "Error al actualizar la contraseña: " . $conexion->error;
    }
} else {
    echo "ERROR";
}
