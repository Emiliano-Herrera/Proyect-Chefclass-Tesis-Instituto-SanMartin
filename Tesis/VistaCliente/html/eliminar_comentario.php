<?php

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_GET['id'])) {
    $idComentario = intval($_GET['id']);
    include("conexion.php");

    // Solo permite eliminar si el comentario es del usuario logueado
    $sql = "DELETE FROM comentarios WHERE id_comentario = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $idComentario, $_SESSION['id_usuario']);
    $stmt->execute();
}

header("Location: vista-actividad.php?comentarioEliminado=1");
exit();