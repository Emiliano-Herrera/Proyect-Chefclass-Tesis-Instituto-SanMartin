<?php

include("conexion.php");

$telefono = $_POST['telefono'];



$id = $_REQUEST['id'];

// Verificar si algún campo está vacío
if (
    empty($telefono) 
) {
    // Mostrar alerta si algún campo está vacío
    echo "<script>alert('Campos incompletos, por favor revisar.');</script>";
    // Redireccionar a la página anterior o hacer algo más, según tus necesidades
    echo "<script>window.history.back();</script>";
    exit;
}

$sql = "UPDATE telefonos_usuarios SET telefono = '$telefono'   WHERE ID_Telefono = '$id'";

$resultado = mysqli_query($conexion, $sql);

if ($resultado) {
    header('location: telefonos.php');
} else {
    echo "No se pudo editar el telefono";
}
?>
