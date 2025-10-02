<?php
include("conexion.php");

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $sql_check_username = "SELECT * FROM usuarios WHERE username = '$username'";
    $result_check_username = $conexion->query($sql_check_username);

    $response = [];
    if ($result_check_username->num_rows > 0) {
        $response = ['exists' => true, 'field' => 'username'];
    } else {
        $response = ['exists' => false];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
