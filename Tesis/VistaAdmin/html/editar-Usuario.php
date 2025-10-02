<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("conexion.php");

date_default_timezone_set('America/Argentina/Catamarca'); // Ajustar la zona horaria

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
} else {
    header("Location: login.php");
    exit();
}

$id = $_REQUEST['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_usuario'];
    $apellido = $_POST['apellido'];
    $username = $_POST['username'];
    $rol = $_POST['rol'];
    $genero = $_POST['genero'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Actualizar datos de usuario
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, nombre_usuario = ?, rol = ?, genero = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('sssssi', $nombre, $apellido, $username, $rol, $genero, $id);
        $stmt->execute();

        // Manejar la actualización de la imagen de perfil si se cargó un archivo
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = $_FILES['archivo']['name'];
            $rutaTemporal = $_FILES['archivo']['tmp_name'];
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $nuevoNombre = "img_perfil/" . uniqid() . "." . $extension;

            if (move_uploaded_file($rutaTemporal, $nuevoNombre)) {
                $sqlImagen = "UPDATE usuarios SET img = ? WHERE id_usuario = ?";
                $stmtImagen = $conexion->prepare($sqlImagen);
                $stmtImagen->bind_param('si', $nuevoNombre, $id);
                $stmtImagen->execute();
            }
        }

        // Eliminar los emails y teléfonos existentes
        $conexion->query("DELETE FROM emails_usuarios WHERE id_usuario = $id");
        $conexion->query("DELETE FROM telefonos_usuarios WHERE id_usuario = $id");

        // Insertar nuevos emails
        $emails = $_POST['emails'];
        foreach ($emails as $email) {
            $sqlEmail = "INSERT INTO emails_usuarios (id_usuario, email) VALUES (?, ?)";
            $stmtEmail = $conexion->prepare($sqlEmail);
            $stmtEmail->bind_param('is', $id, $email);
            $stmtEmail->execute();
        }

        // Insertar nuevos teléfonos
        $telefonos = $_POST['telefonos'];
        foreach ($telefonos as $telefono) {
            $sqlTelefono = "INSERT INTO telefonos_usuarios (id_usuario, telefono) VALUES (?, ?)";
            $stmtTelefono = $conexion->prepare($sqlTelefono);
            $stmtTelefono->bind_param('is', $id, $telefono);
            $stmtTelefono->execute();
        }

        // Registrar en la tabla historial_usuarios
        $accion = "Modificación de usuario";
        $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
        $detalle = "El usuario '$username' ha sido modificado por '$Nombre $Apellido' en la fecha '" . date('d-m-Y H:i:s') . "'.";

        $detalle = mysqli_real_escape_string($conexion, $detalle);
        $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES ('$id', '$accion', '$detalle', '$fecha')";
        $resultHistorial = mysqli_query($conexion, $sqlHistorial);

        // Confirmar transacción
        $conexion->commit();

        // Redirigir a usuarios.php con mensaje de éxito
        header("Location: usuarios.php?mensaje=exito");
        exit();
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        header("Location: usuarios.php?mensaje=error&detalle=" . urlencode($conexion->error));
        exit();
    }
}
?>
