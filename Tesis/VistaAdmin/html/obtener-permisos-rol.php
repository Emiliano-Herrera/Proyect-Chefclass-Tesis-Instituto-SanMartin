<?php
include("conexion.php");
session_start();
header('Content-Type: application/json');

$response = ['status' => 'error', 'permisos' => []];

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Sesión no válida.';
    echo json_encode($response);
    exit;
}

$id_rol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;

if ($id_rol <= 0) {
    $response['message'] = 'ID de rol inválido.';
    echo json_encode($response);
    exit;
}

// Consulta los permisos del rol
$sql = "SELECT id_seccion, permisos FROM roles_permisos_secciones WHERE id_rol = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_rol);
$stmt->execute();
$stmt->bind_result($id_seccion, $permisos_str);

$permisos = [];
while ($stmt->fetch()) {
    $permisos[$id_seccion] = array_filter(explode(',', $permisos_str));
}
$stmt->close();

$response['status'] = 'success';
$response['permisos'] = $permisos;

echo json_encode($response);
?>