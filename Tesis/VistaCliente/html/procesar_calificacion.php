<?php
session_start();
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receta_id = $_POST['receta_id'];
    $usuario_id = $_POST['usuario_id'];
    $calificacion = $_POST['calificacion'];

    // Verificar si el usuario ya ha calificado esta receta
    $sql_verificar = "SELECT id_calificacion FROM calificaciones WHERE receta_id = ? AND usuario_id = ?";
    $stmt_verificar = $conexion->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $receta_id, $usuario_id);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows > 0) {
        // Si ya existe una calificación, actualizarla
        $sql_actualizar = "UPDATE calificaciones SET calificacion = ?, fecha_calificacion = CURRENT_TIMESTAMP WHERE receta_id = ? AND usuario_id = ?";
        $stmt_actualizar = $conexion->prepare($sql_actualizar);
        $stmt_actualizar->bind_param("iii", $calificacion, $receta_id, $usuario_id);
        $stmt_actualizar->execute();
        $stmt_actualizar->close();
    } else {
        // Si no existe una calificación, insertar una nueva
        $sql_insertar = "INSERT INTO calificaciones (receta_id, usuario_id, calificacion) VALUES (?, ?, ?)";
        $stmt_insertar = $conexion->prepare($sql_insertar);
        $stmt_insertar->bind_param("iii", $receta_id, $usuario_id, $calificacion);
        $stmt_insertar->execute();
        $stmt_insertar->close();
    }

    $stmt_verificar->close();
    header("Location: vista-detalle-receta.php?id=$receta_id&calificado=true");
    exit();
}

$conexion->close();
