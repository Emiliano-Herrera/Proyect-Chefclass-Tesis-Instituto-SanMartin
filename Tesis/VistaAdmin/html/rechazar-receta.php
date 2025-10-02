<?php
session_start();
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['id'])) {
        $id_receta = intval($_GET['id']);

        // Iniciar transacción
        $conexion->begin_transaction();

        try {
            // Eliminar calificaciones relacionadas
            $sql = "DELETE FROM calificaciones WHERE receta_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar comentarios relacionados
            $sql = "DELETE FROM comentarios WHERE receta_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar likes relacionados
            $sql = "DELETE FROM likes WHERE receta_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar instrucciones relacionadas
            $sql = "DELETE FROM instrucciones WHERE receta_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar imagenes relacionadas
            $sql = "SELECT img_id FROM imagenes_recetas WHERE recetas_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $img_id = $row['img_id'];

                // Eliminar imagen de img_recetas
                $sql_delete_img = "DELETE FROM img_recetas WHERE id_img = ?";
                $stmt_delete_img = $conexion->prepare($sql_delete_img);
                $stmt_delete_img->bind_param("i", $img_id);
                $stmt_delete_img->execute();
            }

            // Eliminar relaciones de imagenes
            $sql = "DELETE FROM imagenes_recetas WHERE recetas_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar ingredientes relacionados
            $sql = "DELETE FROM recetas_ingredientes WHERE receta_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar categorías relacionadas
            $sql = "DELETE FROM recetas_categorias WHERE receta_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar recetas favoritas relacionadas
            $sql = "DELETE FROM recetas_favoritas WHERE receta_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Eliminar la receta de la base de datos
            $sql = "DELETE FROM recetas WHERE id_receta = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();

            // Confirmar transacción
            $conexion->commit();

            echo json_encode(['status' => 'success', 'message' => 'Receta rechazada y eliminada exitosamente.']);
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conexion->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error al rechazar la receta.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID de receta no proporcionado.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
}

$conexion->close();
