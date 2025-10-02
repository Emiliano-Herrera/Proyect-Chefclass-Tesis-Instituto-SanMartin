<?php
include('conexion.php');

$receta_id = $_GET['receta_id'];

$response = ['likes' => 0];

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
