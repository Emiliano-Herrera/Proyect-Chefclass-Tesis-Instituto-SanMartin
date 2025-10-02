<?php
include("conexion.php");

if (isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $sql_check = "SELECT * FROM generos WHERE nombre_genero = '$nombre'";
    $result_check = $conexion->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
} else {
    echo json_encode(['exists' => false]);
}
?>
