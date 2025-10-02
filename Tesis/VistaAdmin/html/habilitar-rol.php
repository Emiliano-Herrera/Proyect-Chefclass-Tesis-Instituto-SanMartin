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
        // Obtener el nombre del rol antes de habilitarlo
        $stmtSelect = $conexion->prepare("SELECT nombre_rol FROM roles WHERE id_rol = ?");
        $stmtSelect->bind_param("i", $id);
        $stmtSelect->execute();
        $stmtSelect->bind_result($nombre_rol_habilitado);
        $stmtSelect->fetch();
        $stmtSelect->close();

        // Habilitar el rol en la base de datos
        $sql = "UPDATE roles SET estado='habilitado' WHERE id_rol = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Verificar si la habilitación fue exitosa
        if ($stmt->affected_rows > 0) {
            // Registrar en la tabla historial_usuarios
            $accion = "Habilitación de rol";
            $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
            $detalle = "El rol " . $nombre_rol_habilitado . " ha sido habilitado por " . $Nombre . " " . $Apellido . " en la fecha " . date('d-m-Y H:i:s') . ".";

            $detalle = mysqli_real_escape_string($conexion, $detalle);
            $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES (?, ?, ?, ?)";
            $stmtHistorial = $conexion->prepare($sqlHistorial);
            $stmtHistorial->bind_param("isss", $ID_Usuario, $accion, $detalle, $fecha);

            if ($stmtHistorial->execute()) {
                $conexion->commit();
                header("Location: roles.php?mensaje=exito");
            } else {
                $conexion->rollback();
                echo "Error al registrar en el historial.";
                // Mensaje de depuración
                echo "Error: " . $stmtHistorial->error;
            }
        } else {
            $conexion->rollback();
            echo "No se pudo habilitar el rol.";
        }

        $stmt->close();
    } catch (Exception $e) {
        $conexion->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
