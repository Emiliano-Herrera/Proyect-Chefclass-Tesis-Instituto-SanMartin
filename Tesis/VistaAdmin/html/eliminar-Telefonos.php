<?php
include("conexion.php");

$id = $_REQUEST['id'];

// Verificar si se ha enviado la confirmación desde el frontend
if (isset($_GET['confirmacion']) && $_GET['confirmacion'] === 'si') {
    // Si se confirma, proceder con la eliminación
    $sql = "DELETE FROM  telefonos_usuarios  WHERE ID_Telefono = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        header("location: telefonos.php");
    } else {
        echo "No se pudo eliminar el telefono";
    }
} else {
    // Si no se ha confirmado, mostrar el mensaje de confirmación en el frontend
    echo '<script>
        var confirmacion = confirm("¿Realmente desea eliminar el telefono  ?");
        if (confirmacion) {
            // Redirigir al backend con confirmación
            window.location.href = "eliminar-Telefonos.php?id=' . $id . '&confirmacion=si";
        } else {
            // Si se cancela, redirigir a la página de lista
            window.location.href = "telefonos.php";
        }
    </script>';
}
?>