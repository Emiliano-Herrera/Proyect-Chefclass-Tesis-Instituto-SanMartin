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
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida.']);
    exit();
}

$id = isset($_POST['id_categoria']) ? $_POST['id_categoria'] : null;

if ($id) {
    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Obtener el nombre de la categoría antes de eliminarla
        $stmtSelect = $conexion->prepare("SELECT nombre FROM categoria WHERE id_categoria = ?");
        $stmtSelect->bind_param("i", $id);
        $stmtSelect->execute();
        $stmtSelect->bind_result($nombre_categoria_eliminada);
        $stmtSelect->fetch();
        $stmtSelect->close();

        // Eliminar la categoría en la base de datos
        $sql = "DELETE FROM categoria WHERE id_categoria = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Verificar si la eliminación fue exitosa
        if ($stmt->affected_rows > 0) {
            // Registrar en la tabla historial_usuarios
            $accion = "Eliminación de categoría";
            $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
            $detalle = "La categoría $nombre_categoria_eliminada ha sido eliminada por $Nombre $Apellido en la fecha " . date('d-m-Y H:i:s') . ".";

            $detalle = mysqli_real_escape_string($conexion, $detalle);
            $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES (?, ?, ?, ?)";
            $stmtHistorial = $conexion->prepare($sqlHistorial);
            $stmtHistorial->bind_param("isss", $ID_Usuario, $accion, $detalle, $fecha);

            if ($stmtHistorial->execute()) {
                $conexion->commit();
                echo json_encode(['status' => 'success', 'message' => 'Categoría eliminada con éxito.']);
            } else {
                $conexion->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Error al registrar en el historial.']);
            }
        } else {
            $conexion->rollback();
            echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar la categoría.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de categoría no válido.']);
}
?>
