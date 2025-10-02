<?php
session_start();

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['ID_Usuario'])) {
    $usuario_Id = $_SESSION['ID_Usuario'];
} else {
    // Las variables de sesión no están definidas, manejar el caso en consecuencia
    echo "Las variables de sesión no están definidas.";
}

date_default_timezone_set('America/Argentina/Catamarca');
$fecha=date("Y-m-d H:i:s");

include("conexion.php");

if(isset($_POST['subirArchivo'])) {
    // Verifica si se ha subido un archivo
    if(file_exists($_FILES['archivo']['tmp_name'])) {
        if(move_uploaded_file($_FILES['archivo']['tmp_name'], 'archivos/'.$_FILES['archivo']['name'])){
            $nombre = $_POST['nombreArchivo'];
            $url = 'archivos/'.$_FILES['archivo']['name'];

            $sql = "INSERT INTO archivos (usuario_ID, nombre_documento, fecha_subida ,url) VALUES ('$usuario_Id', '$nombre', '$fecha', '$url')";
            $resultado = $conexion->query($sql);

            if ($resultado) {
                echo "<script language='JavaScript'>
                    alert('Archivo subido correctamente');
                    location.assign('vista-archivos.php');
                    </script>";
            } else {
                echo "Error al subir el archivo: " . $conexion->error;
            }
        } else {
            echo "Error al mover el archivo.";
        }
    } else {
        echo "<script language='JavaScript'>
            alert('No se ha seleccionado ningún archivo');
            location.assign('vista-archivos.php');
            </script>";
    }
}
?>
