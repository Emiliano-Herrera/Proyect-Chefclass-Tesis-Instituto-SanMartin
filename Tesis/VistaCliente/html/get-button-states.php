<?php
session_start();
include('conexion.php');

$response = ['is_saved' => false, 'is_liked' => false, 'likes' => 0];

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode($response);
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$receta_id = $_GET['receta_id'];

// Verificar si la receta está guardada
$sql = "SELECT * FROM recetas_favoritas WHERE usuario_id = ? AND receta_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $receta_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $response['is_saved'] = true;
}

// Verificar si ya dio like
$sql = "SELECT * FROM likes WHERE usuario_id = ? AND receta_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $receta_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $response['is_liked'] = true;
}

// Obtener el conteo de likes
$sql = "SELECT COUNT(*) AS total_likes FROM likes WHERE receta_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $receta_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $response['likes'] = $row['total_likes'];
}

$stmt->close();
$conexion->close();

echo json_encode($response);
?>
