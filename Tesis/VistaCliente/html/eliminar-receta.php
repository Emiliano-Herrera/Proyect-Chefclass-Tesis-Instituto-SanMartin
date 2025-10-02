<?php
session_start();
include('conexion.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    // Redirigir a la página de inicio de sesión si no hay una sesión activa
    header("Location: Login.php");
    exit();
}

// Verificar si el ID de la receta está presente en la solicitud
if (isset($_GET['id'])) {
    $id_receta = intval($_GET['id']);
    $id_usuario = $_SESSION['id_usuario'];

    // Verificar si la receta pertenece al usuario
    $sql_verificar = "SELECT * FROM recetas WHERE id_receta = ? AND usuario_id = ?";
    $stmt_verificar = $conexion->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $id_receta, $id_usuario);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows === 0) {
        // Si la receta no pertenece al usuario, redirigir al perfil con un mensaje de error
        $_SESSION['mensaje_error'] = "No tienes permiso para eliminar esta receta.";
        header("Location: vista-perfil.php");
        exit();
    }

    // Eliminar las imágenes asociadas con la receta
    $sql_imagenes = "SELECT i.url_imagen FROM img_recetas i JOIN imagenes_recetas ir ON i.id_img = ir.img_id WHERE ir.recetas_id = ?";
    $stmt_imagenes = $conexion->prepare($sql_imagenes);
    $stmt_imagenes->bind_param("i", $id_receta);
    $stmt_imagenes->execute();
    $result_imagenes = $stmt_imagenes->get_result();

    while ($imagen = $result_imagenes->fetch_assoc()) {
        $ruta_imagen = '../../uploads/' . basename($imagen['url_imagen']);
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen); // Eliminar la imagen de la carpeta uploads
        }
    }

    // Eliminar los registros de las imágenes de la base de datos
    $sql_eliminar_imagenes = "DELETE i FROM img_recetas i JOIN imagenes_recetas ir ON i.id_img = ir.img_id WHERE ir.recetas_id = ?";
    $stmt_eliminar_imagenes = $conexion->prepare($sql_eliminar_imagenes);
    $stmt_eliminar_imagenes->bind_param("i", $id_receta);
    $stmt_eliminar_imagenes->execute();

    // Eliminar registros dependientes de otras tablas
    $sql_eliminar_calificaciones = "DELETE FROM calificaciones WHERE receta_id = ?";
    $stmt_eliminar_calificaciones = $conexion->prepare($sql_eliminar_calificaciones);
    $stmt_eliminar_calificaciones->bind_param("i", $id_receta);
    $stmt_eliminar_calificaciones->execute();

    $sql_eliminar_instrucciones = "DELETE FROM instrucciones WHERE receta_id = ?";
    $stmt_eliminar_instrucciones = $conexion->prepare($sql_eliminar_instrucciones);
    $stmt_eliminar_instrucciones->bind_param("i", $id_receta);
    $stmt_eliminar_instrucciones->execute();

    $sql_eliminar_likes = "DELETE FROM likes WHERE receta_id = ?";
    $stmt_eliminar_likes = $conexion->prepare($sql_eliminar_likes);
    $stmt_eliminar_likes->bind_param("i", $id_receta);
    $stmt_eliminar_likes->execute();

    $sql_eliminar_categorias = "DELETE FROM recetas_categorias WHERE receta_id = ?";
    $stmt_eliminar_categorias = $conexion->prepare($sql_eliminar_categorias);
    $stmt_eliminar_categorias->bind_param("i", $id_receta);
    $stmt_eliminar_categorias->execute();

    $sql_eliminar_favoritas = "DELETE FROM recetas_favoritas WHERE receta_id = ?";
    $stmt_eliminar_favoritas = $conexion->prepare($sql_eliminar_favoritas);
    $stmt_eliminar_favoritas->bind_param("i", $id_receta);
    $stmt_eliminar_favoritas->execute();

    $sql_eliminar_ingredientes = "DELETE FROM recetas_ingredientes WHERE receta_id = ?";
    $stmt_eliminar_ingredientes = $conexion->prepare($sql_eliminar_ingredientes);
    $stmt_eliminar_ingredientes->bind_param("i", $id_receta);
    $stmt_eliminar_ingredientes->execute();

    $sql_eliminar_comentarios = "DELETE FROM comentarios WHERE receta_id = ?";
    $stmt_eliminar_comentarios = $conexion->prepare($sql_eliminar_comentarios);
    $stmt_eliminar_comentarios->bind_param("i", $id_receta);
    $stmt_eliminar_comentarios->execute();

    // Eliminar la receta de la base de datos
    $sql_eliminar_receta = "DELETE FROM recetas WHERE id_receta = ?";
    $stmt_eliminar_receta = $conexion->prepare($sql_eliminar_receta);
    $stmt_eliminar_receta->bind_param("i", $id_receta);
    $stmt_eliminar_receta->execute();

    // Redirigir de vuelta a la página de perfil con un mensaje de éxito
    $_SESSION['mensaje_exito'] = "Receta eliminada exitosamente.";
    header("Location: vista-perfil.php");
    exit();
} else {
    // Si no se proporciona el ID, redirigir de vuelta a la página de perfil
    $_SESSION['mensaje_error'] = "No se pudo procesar la solicitud. ID de receta no encontrado.";
    header("Location: vista-perfil.php");
    exit();
}
?>
