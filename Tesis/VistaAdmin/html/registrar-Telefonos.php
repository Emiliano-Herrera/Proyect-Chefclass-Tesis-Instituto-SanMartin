<?php
// Incluir el archivo de conexión
include("conexion.php");

if (isset($_POST['registrar'])) {
    // Verificar que los campos obligatorios no estén vacíos
    if (empty($_POST['idUsuario']) || empty($_POST['telefono'])) {
        echo "<script language='JavaScript'>
            alert('Los campos no han sido completados, por favor completa los campos para registrar.');
            location.assign('telefonos.php');
            </script>";
    } else {
        // Obtener el nuevo ID
        $id_max_query = "SELECT MAX(`ID_Telefono`) AS `ultimo_id` FROM `telefonos_usuarios`";
        $resultado_id_max = $conexion->query($id_max_query);

        if ($resultado_id_max) {
            $fila_id_max = $resultado_id_max->fetch_assoc();
            $ultimo_id = $fila_id_max['ultimo_id'];
            $id_nuevo = $ultimo_id + 1;
        } else {
            echo "Error al obtener el nuevo ID: " . $conexion->error;
            exit(); // Detener la ejecución del script en caso de error
        }

        $idUsuario = $_POST['idUsuario'];
        $telefono = $_POST['telefono'];

        // Insertar datos en la tabla telefonos_usuarios
        $sql = "INSERT INTO `telefonos_usuarios` (`ID_Telefono`, `ID_Usuario`, `Telefono`)
                VALUES ('$id_nuevo', '$idUsuario', '$telefono')";

        // Ejecutar la consulta SQL
        if ($conexion->query($sql) === TRUE) {
            echo "<script language='JavaScript'>
            alert('El teléfono ha sido registrado correctamente');
            location.assign('telefonos.php');
            </script>";
        } else {
            echo "Error al registrar: " . $conexion->error;
        }

        // Cerrar la conexión
        $conexion->close();
    }
}
?>
