<?php
include("conexion.php");

$response = array('exists' => false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $nombre = $data['nombre'];
    $id = isset($data['id']) ? $data['id'] : 0;

    // Consulta SQL que excluye el género actual si estamos editando
    $sql = "SELECT COUNT(*) AS count FROM generos WHERE nombre_Genero = '$nombre' AND id_genero != '$id'";
    $result = $conexion->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            $response['exists'] = true;
        }
    }
}

echo json_encode($response);
?>

