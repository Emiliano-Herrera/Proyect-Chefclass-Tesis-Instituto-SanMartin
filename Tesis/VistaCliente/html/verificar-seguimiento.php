<?php
session_start();
include('conexion.php');

$response = ['is_following' => false];

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode($response);
    exit();
}

$id_seguidor = $_SESSION['id_usuario'];
$id_usuario_a_seguir = $_GET['usuario_id'];

$sql_verificar = "SELECT * FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
$stmt_verificar = $conexion->prepare($sql_verificar);
$stmt_verificar->bind_param("ii", $id_seguidor, $id_usuario_a_seguir);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

if ($result_verificar->num_rows > 0) {
    $response['is_following'] = true;
}

$stmt_verificar->close();
$conexion->close();

echo json_encode($response);
?>