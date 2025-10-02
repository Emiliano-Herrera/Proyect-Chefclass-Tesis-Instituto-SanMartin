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

        // Verificar si el nombre de la categoría está vacío
        if (empty($nombre)) {
            $response['message'] = 'El campo de nombre está vacío, por favor completa el campo.';
        } else {
            // Iniciar transacción
            $conexion->begin_transaction();

            try {
                // Preparar la sentencia para prevenir inyecciones SQL
                $stmt = $conexion->prepare("INSERT INTO categoria (nombre) VALUES (?)");
                $stmt->bind_param("s", $nombre);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Registrar en la tabla historial_usuarios
                    $accion = "Registro de categoría";
                    $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
                    $detalle = "La categoría $nombre ha sido registrada por $Nombre $Apellido en la fecha " . date('d-m-Y H:i:s') . ".";

                    $detalle = mysqli_real_escape_string($conexion, $detalle);
                    $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES (?, ?, ?, ?)";
                    $stmtHistorial = $conexion->prepare($sqlHistorial);
                    $stmtHistorial->bind_param("isss", $ID_Usuario, $accion, $detalle, $fecha);

                    if ($stmtHistorial->execute()) {
                        $conexion->commit();
                        $response['status'] = 'success';
                        $response['message'] = 'La categoría ha sido registrada correctamente.';
                    } else {
                        $conexion->rollback();
                        $response['message'] = 'Error al registrar en el historial.';
                    }
                } else {
                    $conexion->rollback();
                    $response['message'] = 'Error al registrar la categoría.';
                }

                $stmt->close();
            } catch (Exception $e) {
                $conexion->rollback();
                $response['message'] = 'Error: ' . $e->getMessage();
            }
        }
    }
} else {
    $response['message'] = 'Sesión no válida.';
}

$conexion->close();

echo json_encode($response);
?>
