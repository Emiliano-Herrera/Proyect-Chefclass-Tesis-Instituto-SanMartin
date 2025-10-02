<?php
include("conexion.php");

if (isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $sql_check = "SELECT * FROM roles WHERE nombre_rol = '$nombre'";
    $result_check = $conexion->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
} else {
    echo json_encode(['exists' => false]);
}
