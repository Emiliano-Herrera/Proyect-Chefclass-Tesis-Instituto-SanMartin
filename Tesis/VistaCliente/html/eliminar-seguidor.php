<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    echo 'error';
    exit();
}

$seguidoId = $_SESSION['id_usuario'];
$seguidorId = (int)$_GET['id'];

$sql = "DELETE FROM seguimiento WHERE seguido_id = ? AND seguidor_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $seguidoId, $seguidorId);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
?>