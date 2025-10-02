<?php
include("conexion.php");

$id = $_REQUEST['id'];

// Verificar si se ha enviado la confirmación desde el frontend
if (isset($_GET['confirmacion']) && $_GET['confirmacion'] === 'si') {
    // Si se confirma, proceder con la eliminación
    $sql = "UPDATE  emails_usuarios SET Estado_Email='deshabilitado' WHERE ID_Email = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        header("location: email.php");
    } else {
        echo "No se pudo eliminar el usuario";
    }
} else {
    // Si no se ha confirmado, mostrar el mensaje de confirmación en el frontend
    echo '<script>
        var confirmacion = confirm("¿Realmente desea eliminar este Email ?");
        if (confirmacion) {
            // Redirigir al backend con confirmación
            window.location.href = "eliminar-email.php?id=' . $id . '&confirmacion=si";
        } else {
            // Si se cancela, redirigir a la página de lista
            window.location.href = "email.php";
        }
    </script>';
}
?>