<?php
session_start();
include('conexion.php');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Debe iniciar sesión.';
    echo json_encode($response);
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$data = json_decode(file_get_contents("php://input"), true);
$receta_id = $data['receta_id'];

// Verificar si la receta ya está guardada
$sql = "SELECT * FROM recetas_favoritas WHERE usuario_id = ? AND receta_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $receta_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Eliminar de la tabla recetas_favoritas
    $sql = "DELETE FROM recetas_favoritas WHERE usuario_id = ? AND receta_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $receta_id);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Receta eliminada de guardados.';
    } else {
        $response['message'] = 'Error al eliminar la receta de guardados.';
    }
} else {
    // Insertar en la tabla recetas_favoritas
    $sql = "INSERT INTO recetas_favoritas (usuario_id, receta_id) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $receta_id);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Receta guardada exitosamente.';
    } else {
        $response['message'] = 'Error al guardar la receta.';
    }
}

echo json_encode($response);
$conexion->close();
?>