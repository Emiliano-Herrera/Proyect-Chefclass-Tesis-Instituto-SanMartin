<?php
session_start();
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

if (isset($_GET['confirmacion']) && $_GET['confirmacion'] === 'si') {
    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Obtener el nombre de usuario antes de deshabilitarlo
        $stmtSelect = $conexion->prepare("SELECT nombre_usuario FROM usuarios WHERE id_usuario = ?");
        $stmtSelect->bind_param("i", $id);
        $stmtSelect->execute();
        $stmtSelect->bind_result($nombre_usuario_deshabilitado);
        $stmtSelect->fetch();
        $stmtSelect->close();

        // Deshabilitar el usuario en la base de datos
        $sql = "UPDATE usuarios SET estado='deshabilitado' WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Verificar si la deshabilitación fue exitosa
        if ($stmt->affected_rows > 0) {
            // Registrar en la tabla historial_usuarios
            $accion = "Deshabilitación de usuario";
            $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
            $detalle = "El usuario '$nombre_usuario_deshabilitado' ha sido deshabilitado por '$Nombre $Apellido' en la fecha '" . date('d-m-Y H:i:s') . "'.";

            $detalle = mysqli_real_escape_string($conexion, $detalle);
            $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES ('$ID_Usuario', '$accion', '$detalle', '$fecha')";
            $resultHistorial = mysqli_query($conexion, $sqlHistorial);

            if ($resultHistorial) {
                $conexion->commit();
                header("Location: usuarios.php?mensaje=exito");
            } else {
                $conexion->rollback();
                echo "Error al registrar en el historial.";
            }
        } else {
            $conexion->rollback();
            echo "No se pudo deshabilitar el usuario.";
        }

        $stmt->close();
    } catch (Exception $e) {
        $conexion->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
