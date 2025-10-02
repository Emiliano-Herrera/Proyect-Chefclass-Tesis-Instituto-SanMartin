<?php

include("conexion.php");

$Email = $_POST['Email'];



$id = $_REQUEST['id'];

// Verificar si algún campo está vacío
if (
    empty($Email) 
) {
    // Mostrar alerta si algún campo está vacío
    echo "<script>alert('Campos incompletos, por favor revisar.');</script>";
    // Redireccionar a la página anterior o hacer algo más, según tus necesidades
    echo "<script>window.history.back();</script>";
    exit;
}

$sql = "UPDATE emails_usuarios SET Email = '$Email'   WHERE ID_Email = '$id'";

$resultado = mysqli_query($conexion, $sql);

if ($resultado) {
    header('location: email.php');
} else {
    echo "No se pudo editar el Email";
}
?>
