<?php
session_start();
include('conexion.php');

date_default_timezone_set('America/Argentina/Catamarca'); // Ajustar la zona horaria

// Verificar si las variables de sesión están definidas antes de acceder a ellas
if (isset($_SESSION['id_usuario'])) {
    $ID_Usuario = $_SESSION['id_usuario'];
    $Nombre = $_SESSION['nombre'];
    $Apellido = $_SESSION['apellido'];
} else {
    header("Location: login.php");
    exit();
}

// Obtener los datos del formulario
$receta_id = $_REQUEST['id'];
$titulo = $_POST['titulo'];
$tiempo = $_POST['tiempo']; // Formato HH:MM
$descripcion = $_POST['descripcion'];
$dificultad = $_POST['dificultad'];
$categorias_nuevas = isset($_POST['categoria_nueva']) ? $_POST['categoria_nueva'] : [];
$categorias_existentes = isset($_POST['categoria_existente']) ? $_POST['categoria_existente'] : [];
$categorias_eliminar = isset($_POST['categorias_eliminar']) ? $_POST['categorias_eliminar'] : [];
$num_pasos = $_POST['num_pasos'];
$pasos = $_POST['pasos'];
$ingredientes = $_POST['ingredientes'];
$cantidades = $_POST['cantidades'];

// Asegurarse de que el tiempo está en el formato HH:MM:SS
$tiempo_preparacion = date("H:i:s", strtotime($tiempo));

// Obtener los campos para eliminar categorías
/* $categorias_eliminar = isset($_POST['categorias_eliminar']) ? $_POST['categorias_eliminar'] : []; */

// Obtener los campos para eliminar instrucciones
/* $instrucciones_eliminar = isset($_POST['instrucciones_eliminar']) ? $_POST['instrucciones_eliminar'] : [];
$instruccion_id = isset($_POST['instruccion_id']) ? $_POST['instruccion_id'] : []; */
$instrucciones_nuevas_eliminar = isset($_POST['instrucciones_nuevas_eliminar']) ? $_POST['instrucciones_nuevas_eliminar'] : [];
$instrucciones_eliminar = isset($_POST['instrucciones_eliminar']) ? $_POST['instrucciones_eliminar'] : [];
$instruccion_existente_id = isset($_POST['instruccion_existente_id']) ? $_POST['instruccion_existente_id'] : [];
$num_pasos_existentes = isset($_POST['num_pasos_existentes']) ? $_POST['num_pasos_existentes'] : [];
$descripcion_pasos_existentes = isset($_POST['descripcion_pasos_existentes']) ? $_POST['descripcion_pasos_existentes'] : [];
$num_pasos_nuevos = isset($_POST['num_pasos_nuevos']) ? $_POST['num_pasos_nuevos'] : [];
$descripcion_pasos_nuevos = isset($_POST['descripcion_pasos_nuevos']) ? $_POST['descripcion_pasos_nuevos'] : [];

// Obtener los campos para eliminar ingredientes
/* $ingredientes_eliminar = isset($_POST['ingredientes_eliminar']) ? $_POST['ingredientes_eliminar'] : [];
$ingrediente_id = isset($_POST['ingrediente_id']) ? $_POST['ingrediente_id'] : []; */
$ingredientes_nuevos_eliminar = isset($_POST['ingredientes_nuevos_eliminar']) ? $_POST['ingredientes_nuevos_eliminar'] : [];
$ingredientes_eliminar = isset($_POST['ingredientes_eliminar']) ? $_POST['ingredientes_eliminar'] : [];
$ingrediente_existente_id = isset($_POST['ingrediente_existente_id']) ? $_POST['ingrediente_existente_id'] : [];
$nombre_ingredientes_existentes = isset($_POST['nombre_ingredientes_existentes']) ? $_POST['nombre_ingredientes_existentes'] : [];
$cantidades_existentes = isset($_POST['cantidades_existentes']) ? $_POST['cantidades_existentes'] : [];
$nombre_ingredientes_nuevos = isset($_POST['nombre_ingredientes_nuevos']) ? $_POST['nombre_ingredientes_nuevos'] : [];
$cantidades_nuevas = isset($_POST['cantidades_nuevas']) ? $_POST['cantidades_nuevas'] : [];

// Obtener los campos para eliminar archivos
$archivos_eliminar = isset($_POST['archivos_eliminar']) ? $_POST['archivos_eliminar'] : [];
$archivo_url = isset($_POST['archivo_url']) ? $_POST['archivo_url'] : [];





try {
    // Iniciar transacción
    $conexion->begin_transaction();
    // Actualizar la receta en la base de datos 
    $sql = "UPDATE recetas SET titulo = ?, tiempo_preparacion = ?, descripcion = ?, dificultad = ? WHERE id_receta = ?";
    $statement = $conexion->prepare($sql);
    $statement->bind_param("ssssi", $titulo, $tiempo_preparacion, $descripcion, $dificultad, $receta_id);
    $statement->execute();


    if (isset($categorias_eliminar) && count($categorias_eliminar) > 0) {
        foreach ($categorias_eliminar as $index => $valor) {
            if ($valor === 'true') {
                $categoria_id_eliminar = (int)$categorias_existentes[$index];
                $sql_delete_categoria = "DELETE FROM recetas_categorias WHERE receta_id = ? AND categoria_id = ?";
                $stmt_delete_categoria = $conexion->prepare($sql_delete_categoria);
                $stmt_delete_categoria->bind_param("ii", $receta_id, $categoria_id_eliminar);
                if (!$stmt_delete_categoria->execute()) {
                    throw new Exception("Error al eliminar la categoría con ID $categoria_id_eliminar: " . $stmt_delete_categoria->error);
                }
                echo "Categoría con ID $categoria_id_eliminar eliminada.<br>";
            }
        }
    }
    // Insertar nuevas categorías 
    if (count($categorias_nuevas) > 0) {
        foreach ($categorias_nuevas as $categoria_id_nueva) {
            $sql_insert_categoria = "INSERT INTO recetas_categorias (receta_id, categoria_id) VALUES (?, ?)";
            $stmt_insert_categoria = $conexion->prepare($sql_insert_categoria);
            $stmt_insert_categoria->bind_param("ii", $receta_id, $categoria_id_nueva);
            if (!$stmt_insert_categoria->execute()) {
                throw new Exception("Error al insertar la categoría con ID $categoria_id_nueva: " . $stmt_insert_categoria->error);
            }
            echo "Nueva categoría con ID $categoria_id_nueva insertada.<br>";
        }
    }


    //TODO instrucciones ======================================================================================================================================


    if (isset($instrucciones_eliminar) && count($instrucciones_eliminar) > 0) {
        foreach ($instrucciones_eliminar as $index => $valor) {
            if ($valor === 'true') {
                $instruccion_id_eliminar = $instruccion_existente_id[$index];
                $sql_delete_instruccion = "DELETE FROM instrucciones WHERE id_instruccion = ?";
                $stmt_delete_instruccion = $conexion->prepare($sql_delete_instruccion);
                $stmt_delete_instruccion->bind_param("i", $instruccion_id_eliminar);
                if (!$stmt_delete_instruccion->execute()) {
                    throw new Exception("Error al eliminar la instrucción con ID $instruccion_id_eliminar: " . $stmt_delete_instruccion->error);
                }
            }
        }
    }
    // Insertar nuevas instrucciones 
    if (count($num_pasos_nuevos) > 0) {
        for ($i = 0; $i < count($num_pasos_nuevos); $i++) {
            $step = $num_pasos_nuevos[$i];
            $description = $descripcion_pasos_nuevos[$i];
            $sql_insert_paso = "INSERT INTO instrucciones (receta_id, paso, descripcion) VALUES (?, ?, ?)";
            $stmt_insert_paso = $conexion->prepare($sql_insert_paso);
            $stmt_insert_paso->bind_param("iis", $receta_id, $step, $description);
            if (!$stmt_insert_paso->execute()) {
                throw new Exception("Error al insertar la nueva instrucción con paso $step: " . $stmt_insert_paso->error);
            }
        }
    }
    // Actualizar instrucciones existentes 
    if (count($num_pasos_existentes) > 0) {
        for ($i = 0; $i < count($num_pasos_existentes); $i++) {
            $step = $num_pasos_existentes[$i];
            $description = $descripcion_pasos_existentes[$i];
            $sql_update_paso = "UPDATE instrucciones SET descripcion = ? WHERE receta_id = ? AND paso = ?";
            $stmt_update_paso = $conexion->prepare($sql_update_paso);
            $stmt_update_paso->bind_param("sii", $description, $receta_id, $step);
            if (!$stmt_update_paso->execute()) {
                throw new Exception("Error al actualizar la instrucción con paso $step: " . $stmt_update_paso->error);
            }
        }
    }




    //?INGREDIENTES==========================================================================================================================================================
    if (isset($ingredientes_eliminar) && count($ingredientes_eliminar) > 0) {
        foreach ($ingredientes_eliminar as $index => $valor) {
            if ($valor === 'true') {
                $ingrediente_id_eliminar = $ingrediente_existente_id[$index];
                $sql_delete_ingrediente = "DELETE FROM recetas_ingredientes WHERE receta_id = ? AND ingrediente_id = ?";
                $stmt_delete_ingrediente = $conexion->prepare($sql_delete_ingrediente);
                $stmt_delete_ingrediente->bind_param("ii", $receta_id, $ingrediente_id_eliminar);
                if (!$stmt_delete_ingrediente->execute()) {
                    throw new Exception("Error al eliminar el ingrediente con ID $ingrediente_id_eliminar: " . $stmt_delete_ingrediente->error);
                }
            }
        }
    }
    // Insertar nuevos ingredientes 
    if (count($nombre_ingredientes_nuevos) > 0) {
        for ($i = 0; $i < count($nombre_ingredientes_nuevos); $i++) {
            $nombre = $nombre_ingredientes_nuevos[$i];
            $cantidad = $cantidades_nuevas[$i];
            $sql_verificar_ingrediente = "SELECT id_ingrediente FROM ingredientes WHERE nombre = ?";
            $stmt_verificar = $conexion->prepare($sql_verificar_ingrediente);
            $stmt_verificar->bind_param("s", $nombre);
            $stmt_verificar->execute();
            $result_verificar = $stmt_verificar->get_result();
            if ($result_verificar->num_rows > 0) {
                $row = $result_verificar->fetch_assoc();
                $ingrediente_id = $row['id_ingrediente'];
            } else {
                $sql_insert_ingrediente = "INSERT INTO ingredientes (nombre) VALUES (?)";
                $stmt_insert_ingrediente = $conexion->prepare($sql_insert_ingrediente);
                $stmt_insert_ingrediente->bind_param("s", $nombre);
                $stmt_insert_ingrediente->execute();
                $ingrediente_id = $stmt_insert_ingrediente->insert_id;
            }
            $sql_insert_receta_ingrediente = "INSERT INTO recetas_ingredientes (receta_id, ingrediente_id, cantidad) VALUES (?, ?, ?)";
            $stmt_insert_receta_ingrediente = $conexion->prepare($sql_insert_receta_ingrediente);
            $stmt_insert_receta_ingrediente->bind_param("iis", $receta_id, $ingrediente_id, $cantidad);
            if (!$stmt_insert_receta_ingrediente->execute()) {
                throw new Exception("Error al insertar el nuevo ingrediente con ID $ingrediente_id: " . $stmt_insert_receta_ingrediente->error);
            }
        }
    }
    // Actualizar ingredientes existentes 
    if (count($nombre_ingredientes_existentes) > 0) {
        for ($i = 0; $i < count($nombre_ingredientes_existentes); $i++) {
            $nombre = $nombre_ingredientes_existentes[$i];
            $cantidad = $cantidades_existentes[$i];
            $ingrediente_id = $ingrediente_existente_id[$i];
            $sql_update_receta_ingrediente = "UPDATE recetas_ingredientes SET cantidad = ? WHERE receta_id = ? AND ingrediente_id = ?";
            $stmt_update_receta_ingrediente = $conexion->prepare($sql_update_receta_ingrediente);
            $stmt_update_receta_ingrediente->bind_param("sii", $cantidad, $receta_id, $ingrediente_id);
            if (!$stmt_update_receta_ingrediente->execute()) {
                throw new Exception("Error al actualizar el ingrediente con ID $ingrediente_id: " . $stmt_update_receta_ingrediente->error);
            }
        }
    }






    // Eliminar archivos marcados 
    if (isset($_POST['archivos_eliminar']) && count($_POST['archivos_eliminar']) > 0) {
        foreach ($_POST['archivos_eliminar'] as $index => $valor) {
            if ($valor === 'true') {
                $archivo_url = $_POST['archivo_url'][$index];
                if (file_exists($archivo_url)) {
                    unlink($archivo_url);
                }
                $sql_get_img_id = "SELECT id_img FROM img_recetas WHERE url_imagen = ?";
                $stmt_get_img_id = $conexion->prepare($sql_get_img_id);
                $stmt_get_img_id->bind_param("s", $archivo_url);
                $stmt_get_img_id->execute();
                $result_img_id = $stmt_get_img_id->get_result()->fetch_assoc();
                $img_id = $result_img_id['id_img'];
                // Eliminar relaciones en imagenes_recetas 
                $sql_delete_imagen_rel = "DELETE FROM imagenes_recetas WHERE img_id = ?";
                $stmt_delete_imagen_rel = $conexion->prepare($sql_delete_imagen_rel);
                $stmt_delete_imagen_rel->bind_param("i", $img_id);
                $stmt_delete_imagen_rel->execute();
                // Eliminar de img_recetas 
                $sql_delete_archivo = "DELETE FROM img_recetas WHERE id_img = ?";
                $stmt_delete_archivo = $conexion->prepare($sql_delete_archivo);
                $stmt_delete_archivo->bind_param("i", $img_id);
                if ($stmt_delete_archivo->execute()) {
                    echo "Archivo con ID $img_id eliminado.<br>";
                } else {
                    echo "Error al eliminar el archivo con ID $img_id.<br>";
                }
            }
        }
    }

    // Manejar cambio de archivos 
    if (!empty($_FILES['cambiar_archivo']['name'][0])) {
        $targetDir = "../../uploads/";
        foreach ($_FILES['cambiar_archivo']['name'] as $key => $val) {
            if (!empty($val)) {
                $fileName = basename($_FILES['cambiar_archivo']['name'][$key]);
                $targetFilePath = $targetDir . $fileName;
                $archivoUrl = $_POST['archivo_url'][$key];
                // Obtener la URL del archivo actual 
                // Subir archivo al servidor 
                if (move_uploaded_file($_FILES['cambiar_archivo']['tmp_name'][$key], $targetFilePath)) {
                    // Actualizar la ruta del archivo en 
                    $sql_update_img = "UPDATE img_recetas SET url_imagen = ? WHERE url_imagen = ?";
                    $stmt_update_img = $conexion->prepare($sql_update_img);
                    $filePathInDB = "../../uploads/" . $fileName;
                    // Guardar ruta completa con ../../ 
                    $stmt_update_img->bind_param("ss", $filePathInDB, $archivoUrl);
                    $stmt_update_img->execute();
                }
            }
        }
    }
    // Manejar subida de nuevos archivos 
    if (!empty($_FILES['archivos']['name'][0])) {
        $targetDir = "../../uploads/";
        foreach ($_FILES['archivos']['name'] as $key => $val) {
            $fileName = basename($_FILES['archivos']['name'][$key]);
            $targetFilePath = $targetDir . $fileName;
            // Subir archivo al servidor 
            if (move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $targetFilePath)) {
                // Insertar la ruta del archivo en img_recetas
                $sql_img = "INSERT INTO img_recetas (url_imagen) VALUES (?)";
                $stmt_img = $conexion->prepare($sql_img);
                $filePathInDB = "../../uploads/" . $fileName;
                // Guardar ruta completa con ../../ 
                $stmt_img->bind_param("s", $filePathInDB);
                if ($stmt_img->execute() === TRUE) {
                    $img_id = $stmt_img->insert_id;
                    // Insertar la relación en imagens_recetas
                    $sql_relacion = "INSERT INTO imagenes_recetas (recetas_id, img_id) VALUES (?, ?)";
                    $stmt_relacion = $conexion->prepare($sql_relacion);
                    $stmt_relacion->bind_param("ii", $receta_id, $img_id);
                    $stmt_relacion->execute();
                }
            }
        }
    }





    // Registrar en la tabla historial_usuarios
    $accion = "Modificación de receta";
    $fecha = date('Y-m-d H:i:s'); // Formato para la base de datos
    $detalle = htmlentities("La receta $titulo ha sido modificada por $Nombre $Apellido en la fecha " . date('d-m-Y H:i:s') . "'.");

    $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES (?, ?, ?, ?)";
    $stmtHistorial = $conexion->prepare($sqlHistorial);
    $stmtHistorial->bind_param("isss", $ID_Usuario, $accion, $detalle, $fecha);
    $stmtHistorial->execute();

    // Confirmar transacción
    $conexion->commit();

    // Redirigir con mensaje de éxito
    header("Location: vista-recetas.php?status=success");
    exit();
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    header("Location: vista-recetas.php?status=error");
    exit();
}

$conexion->close();
