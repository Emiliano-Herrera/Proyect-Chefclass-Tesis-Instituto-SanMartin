<?php
session_start();
include('conexion.php');

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Debe iniciar sesión.';
    echo json_encode($response);
    exit();
}

$id_seguidor = $_SESSION['id_usuario'];
$id_usuario_a_seguir = $_POST['usuario_id'];
$action = $_POST['action'] ?? 'follow';

if ($action === 'unfollow') {
    // Dejar de seguir
    $sql_unfollow = "DELETE FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt_unfollow = $conexion->prepare($sql_unfollow);
    $stmt_unfollow->bind_param("ii", $id_seguidor, $id_usuario_a_seguir);
    if ($stmt_unfollow->execute()) {
        $response['success'] = true;
        $response['message'] = 'Has dejado de seguir al usuario.';
    } else {
        $response['message'] = 'Hubo un problema al intentar dejar de seguir al usuario.';
    }
    $stmt_unfollow->close();
} else {
    // Seguir usuario
    $sql_verificar = "SELECT 1 FROM seguimiento WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt_verificar = $conexion->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $id_seguidor, $id_usuario_a_seguir);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();

    if ($stmt_verificar->num_rows > 0) {
        $response['message'] = 'Ya sigues a este usuario.';
    } else {
        $sql_seguir = "INSERT INTO seguimiento (seguidor_id, seguido_id) VALUES (?, ?)";
        $stmt_seguir = $conexion->prepare($sql_seguir);
        $stmt_seguir->bind_param("ii", $id_seguidor, $id_usuario_a_seguir);

        if ($stmt_seguir->execute()) {
            $response['success'] = true;
            $response['message'] = 'Has seguido al usuario correctamente.';
        } else {
            $response['message'] = 'Hubo un problema al intentar seguir al usuario.';
        }
        $stmt_seguir->close();
    }
    $stmt_verificar->close();
}

$conexion->close();
echo json_encode($response);
exit();
