<?php
// Conexión a la base de datos
require('conexion.php');
session_start();

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
    // Capturar el ID del usuario a eliminar
    $id_usuario = $_POST['id_usuario'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Obtener el nombre de usuario antes de eliminarlo
        $stmtSelect = $conexion->prepare("SELECT nombre_usuario FROM usuarios WHERE id_usuario = ?");
        $stmtSelect->bind_param("i", $id_usuario);
        $stmtSelect->execute();
        $stmtSelect->bind_result($nombre_usuario_eliminado);
        $stmtSelect->fetch();
        $stmtSelect->close();

        // Registrar en la tabla historial_usuarios antes de eliminar el usuario
        $accion = "Eliminación de usuario";
        $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
        $detalle = "El usuario '$nombre_usuario_eliminado' ha sido eliminado por '$Nombre $Apellido' en la fecha '" . date('d-m-Y H:i:s') . "'.";

        $detalle = mysqli_real_escape_string($conexion, $detalle);
        $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES ('$ID_Usuario', '$accion', '$detalle', '$fecha')";
        $resultHistorial = mysqli_query($conexion, $sqlHistorial);

        if ($resultHistorial) {
            // Eliminar el usuario de la base de datos
            $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();

            // Verificar si la eliminación fue exitosa
            if ($stmt->affected_rows > 0) {
                $conexion->commit();
                echo json_encode(["status" => "success", "message" => "Usuario eliminado correctamente."]);
            } else {
                $conexion->rollback();
                echo json_encode(["status" => "error", "message" => "No se pudo eliminar el usuario. Intente nuevamente."]);
            }

            $stmt->close();
        } else {
            $conexion->rollback();
            echo json_encode(["status" => "error", "message" => "Error al registrar en el historial."]);
        }
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>
