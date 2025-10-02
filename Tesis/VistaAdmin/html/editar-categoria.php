<?php
include("conexion.php");
session_start();

date_default_timezone_set('America/Argentina/Catamarca'); // Ajustar la zona horaria

$response = array('status' => 'error', 'message' => 'No se recibieron los datos correctamente.');

if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];

    if (isset($_POST['nombre'])) {
        $nombre = $_POST['nombre'];
        $id = $_REQUEST['id'];

        // Iniciar transacción
        $conexion->begin_transaction();

        try {
            // Obtener el nombre de la categoría antes de editarla
            $stmtSelect = $conexion->prepare("SELECT nombre FROM categoria WHERE id_categoria = ?");
            $stmtSelect->bind_param("i", $id);
            $stmtSelect->execute();
            $stmtSelect->bind_result($nombre_categoria_anterior);
            $stmtSelect->fetch();
            $stmtSelect->close();

            // Actualizar datos de la categoría
            $stmt = $conexion->prepare("UPDATE categoria SET nombre = ? WHERE id_categoria = ?");
            $stmt->bind_param('si', $nombre, $id);
            $stmt->execute();

            // Verificar si la actualización fue exitosa
            if ($stmt->affected_rows > 0) {
                // Registrar en la tabla historial_usuarios
                $accion = "Modificación de categoría";
                $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
                $detalle = "La categoría $nombre_categoria_anterior ha sido modificada a $nombre por $Nombre $Apellido en la fecha " . date('d-m-Y H:i:s') . ".";

                $detalle = mysqli_real_escape_string($conexion, $detalle);
                $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES (?, ?, ?, ?)";
                $stmtHistorial = $conexion->prepare($sqlHistorial);
                $stmtHistorial->bind_param("isss", $ID_Usuario, $accion, $detalle, $fecha);

                if ($stmtHistorial->execute()) {
                    $conexion->commit();
                    $response['status'] = 'success';
                    $response['message'] = "Has modificado correctamente la categoría.";
                } else {
                    $conexion->rollback();
                    $response['message'] = 'Error al registrar en el historial.';
                }
            } else {
                $conexion->rollback();
                $response['message'] = 'No se pudo editar la categoría.';
            }

            $stmt->close();
        } catch (Exception $e) {
            $conexion->rollback();
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Sesión no válida.';
}

$conexion->close();

echo json_encode($response);
?>
