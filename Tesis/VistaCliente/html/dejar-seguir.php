<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    echo 'error';
    exit();
}

$seguidorId = $_SESSION['id_usuario'];
$seguidoId = (int)$_GET['id'];

$sql = "DELETE FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $seguidorId, $seguidoId);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
?>