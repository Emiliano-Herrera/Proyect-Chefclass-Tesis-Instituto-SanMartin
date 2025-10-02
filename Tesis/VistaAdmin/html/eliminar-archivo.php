<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivo = $_POST['archivo'];

    // Eliminar archivo del sistema de archivos
    if (file_exists($archivo)) {
        if (unlink($archivo)) {
            // Conectar con la base de datos
            include('conexion.php');

            // Eliminar la entrada de la base de datos
            $sql = "DELETE FROM img_recetas WHERE url_imagen = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $archivo);
            $stmt->execute();

            // También eliminar la relación en imagenes_recetas
            $sql_relacion = "DELETE FROM imagenes_recetas WHERE img_id = (SELECT id_img FROM img_recetas WHERE url_imagen = ?)";
            $stmt_relacion = $conexion->prepare($sql_relacion);
            $stmt_relacion->bind_param("s", $archivo);
            $stmt_relacion->execute();

            if ($stmt->affected_rows > 0 && $stmt_relacion->affected_rows > 0) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => 'No se pudo eliminar la entrada en la base de datos.');
            }

            $stmt->close();
            $conexion->close();
        } else {
            $response = array('success' => false, 'message' => 'No se pudo eliminar el archivo en el sistema de archivos.');
        }
    } else {
        $response = array('success' => false, 'message' => 'El archivo no existe.');
    }

    echo json_encode($response);
}
?>
