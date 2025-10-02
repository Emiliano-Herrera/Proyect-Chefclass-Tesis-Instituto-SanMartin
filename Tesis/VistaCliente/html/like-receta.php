<?php
session_start();
include('conexion.php');

$response = ['success' => false, 'message' => '', 'likes' => 0];

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Debe iniciar sesión.';
    echo json_encode($response);
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$data = json_decode(file_get_contents("php://input"), true);
$receta_id = $data['receta_id'];

// Verificar si ya dio like
$sql = "SELECT * FROM likes WHERE usuario_id = ? AND receta_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $receta_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Eliminar de la tabla likes
    $sql = "DELETE FROM likes WHERE usuario_id = ? AND receta_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $receta_id);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Like eliminado.';
    } else {
        $response['message'] = 'Error al eliminar el like.';
    }
} else {
    // Insertar en la tabla likes
    $fecha_like = date('Y-m-d H:i:s');
    $sql = "INSERT INTO likes (usuario_id, receta_id, fecha_like) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iis", $usuario_id, $receta_id, $fecha_like);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Receta marcada con "Me gusta" exitosamente.';
    } else {
        $response['message'] = 'Error al marcar "Me gusta".';
    }
}

// Actualizar contador de likes
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
