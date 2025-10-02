<?php
include('conexion.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $comentario = $_POST['comentario'];
    $receta_id = $_POST['receta_id'];
    $ID_Usuario = $_SESSION['id_usuario']; // Asumiendo que el ID del usuario se guarda en $_SESSION

    // Insertar el comentario en la base de datos
    $sql = "INSERT INTO comentarios (comentario, usuario_id, receta_id) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sii",  $comentario, $ID_Usuario, $receta_id);

    if ($stmt->execute()) {
        // Redirigir al usuario de vuelta a la página de la receta
        header("Location: vista-detalle-receta.php?id=" . $receta_id);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conexion->error;
    }

    // Cerrar la conexión
    $stmt->close();
    $conexion->close();
} else {
    echo "Método no permitido.";
}
