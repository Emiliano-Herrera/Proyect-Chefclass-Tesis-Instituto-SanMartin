<?php
session_start();
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['id'])) {
        $id_receta = intval($_GET['id']);

        // Actualizar el estado de la receta a 'habilitado'
        $sql = "UPDATE recetas SET estado = 'habilitado' WHERE id_receta = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_receta);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Receta aprobada exitosamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al aprobar la receta.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID de receta no proporcionado.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
}

$conexion->close();
