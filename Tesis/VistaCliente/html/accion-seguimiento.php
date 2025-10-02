<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_GET['accion']) || !isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Parámetros inválidos']);
    exit();
}

$usuarioId = $_SESSION['id_usuario'];
$accion = $_GET['accion'];
$otroUsuarioId = (int)$_GET['id'];

if ($accion === 'eliminar') {
    // Eliminar seguidor
    $sql = "DELETE FROM seguimiento WHERE seguido_id = ? AND seguidor_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuarioId, $otroUsuarioId);
} elseif ($accion === 'dejar-seguir') {
    // Dejar de seguir
    $sql = "DELETE FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuarioId, $otroUsuarioId);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    exit();
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>