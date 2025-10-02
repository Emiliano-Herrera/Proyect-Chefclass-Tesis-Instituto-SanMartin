<?php
// Incluir el archivo de conexión
include("conexion.php");


if (isset($_POST['registrar'])) {
    // ... (código existente)
    if (
        empty($_POST['Email']) ||empty($_POST['nombreUsu']) 
        
    ) {

        echo "<script language='JavaScript'>
            alert('Los campos no han sido completados, por favor completa los campos para registrar.');
            location.assign('vista-registrar-email.php');
            </script>";
    } else {


        // Obtener el nuevo ID

        $id_max_query = "SELECT MAX(`ID_Email`) AS `ultimo_id` FROM `emails_usuarios`";
        $resultado_id_max = $conexion->query($id_max_query);

        if ($resultado_id_max) {

            $fila_id_max = $resultado_id_max->fetch_assoc();
            $ultimo_id = $fila_id_max['ultimo_id'];
            $id_nuevo = $ultimo_id + 1;
        } else {
            echo "Error al obtener el nuevo ID: " . $conexion->error;
            exit(); // Detener la ejecución del script en caso de error
        }

        $Email = $_POST['Email'];
       
        $nombreUsu = $_POST['nombreUsu'];
       
        


        // Insertar datos en la tabla usuario
        $sql = "INSERT INTO `emails_usuarios`(`ID_Email`, `ID_Usuario`, `Email`)
        VALUES ('$id_nuevo', '$nombreUsu', '$Email')";

        // Ejecutar la consulta SQL
        if ($conexion->query($sql) === TRUE) {


            echo "<script language='JavaScript'>
            alert('El Email  ha sido registrado correctamente');
            location.assign('email.php');
            </script>";
        } else {
            echo "Error al registrar: " . $conexion->error;
        }

        // Cerrar la conexión
        $conexion->close();
    }
}
