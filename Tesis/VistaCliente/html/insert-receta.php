<?php
session_start();
include('conexion.php');

// Obtener el id_usuario desde la sesión
$user_id = $_SESSION['id_usuario'];

$titulo = $_POST['titulo'];
$tiempo = $_POST['tiempo']; // Formato HH:MM
$descripcion = $_POST['descripcion'];
$dificultad = $_POST['dificultad'];
$categorias = isset($_POST['categoria']) ? $_POST['categoria'] : [];
$pasos = $_POST['pasos'];
$num_pasos = $_POST['num_pasos'];
$ingredientes = $_POST['ingredientes'];
$cantidades = $_POST['cantidades'];
$unidades = $_POST['unidades'];
$estado = 'pendiente';

//  tiempo está en el formato HH:MM:SS
$tiempo_preparacion = date("H:i:s", strtotime($tiempo));



// Validar si algún archivo tiene extensión .webp

$formatosPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];
if (!empty($_FILES['media_files']['name'][0])) {
    foreach ($_FILES['media_files']['name'] as $key => $val) {
        $fileName = basename($_FILES['media_files']['name'][$key]);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $formatosPermitidos)) {
            header("Location: vista-subir-receta.php?status=error_formato");
            exit();
        }
    }
}



// Usar prepared statements para insertar datos de la receta
$sql_receta = "INSERT INTO recetas (titulo, tiempo_preparacion, descripcion, dificultad, usuario_id, estado) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql_receta);
$stmt->bind_param("ssssis", $titulo, $tiempo_preparacion, $descripcion, $dificultad, $user_id, $estado);

if ($stmt->execute() === TRUE) {
    $receta_id = $stmt->insert_id;

    // Verificar si se enviaron categorías
    $categorias = isset($_POST['categoria']) ? $_POST['categoria'] : [];
    $categorias = array_unique($categorias);

    if (!empty($categorias)) {
        $sql_categoria = "INSERT INTO recetas_categorias (receta_id, categoria_id) VALUES (?, ?)";
        $stmt_categoria = $conexion->prepare($sql_categoria);

        foreach ($categorias as $categoria_id) {
            $categoria_id = intval($categoria_id);
            $stmt_categoria->bind_param("ii", $receta_id, $categoria_id);
            if (!$stmt_categoria->execute()) {
                error_log("Error al insertar categoría: " . $stmt_categoria->error);
            }
        }
        $stmt_categoria->close();
    } else {
        header("Location: vista-subir-receta.php?status=error&message=No se seleccionaron categorías");
        exit();
    }

    // Insertar pasos de la receta
    $sql_paso = "INSERT INTO instrucciones (receta_id, paso, descripcion) VALUES (?, ?, ?)";
    $stmt_paso = $conexion->prepare($sql_paso);
    for ($i = 0; $i < count($pasos); $i++) {
        $paso = $num_pasos[$i];
        $descripcion_paso = $pasos[$i];
        $stmt_paso->bind_param("iis", $receta_id, $paso, $descripcion_paso);
        $stmt_paso->execute();
    }

    // Insertar ingredientes de la receta (con unidad)
    $sql_ingrediente = "INSERT INTO ingredientes (nombre) VALUES (?)";
    $stmt_ingrediente = $conexion->prepare($sql_ingrediente);
    $sql_receta_ingrediente = "INSERT INTO recetas_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (?, ?, ?, ?)";
    $stmt_receta_ingrediente = $conexion->prepare($sql_receta_ingrediente);

    for ($i = 0; $i < count($ingredientes); $i++) {
        $nombre = $ingredientes[$i];
        $cantidad = $cantidades[$i];
        $unidad = $unidades[$i];

        // Verificar si el ingrediente ya existe solo por nombre
        $sql_verificar_ingrediente = "SELECT id_ingrediente FROM ingredientes WHERE nombre = ?";
        $stmt_verificar = $conexion->prepare($sql_verificar_ingrediente);
        $stmt_verificar->bind_param("s", $nombre);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {
            $row = $result_verificar->fetch_assoc();
            $ingrediente_id = $row['id_ingrediente'];
        } else {
            $stmt_ingrediente->bind_param("s", $nombre);
            $stmt_ingrediente->execute();
            $ingrediente_id = $stmt_ingrediente->insert_id;
        }

        // Insertar en recetas_ingredientes con unidad
        $stmt_receta_ingrediente->bind_param("iiss", $receta_id, $ingrediente_id, $cantidad, $unidad);
        $stmt_receta_ingrediente->execute();
    }

    // Manejar subida de archivos
    $targetDir = "../../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (!empty($_FILES['media_files']['name'][0])) {
        foreach ($_FILES['media_files']['name'] as $key => $val) {
            $fileName = basename($_FILES['media_files']['name'][$key]);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['media_files']['tmp_name'][$key], $targetFilePath)) {
                $sql_img = "INSERT INTO img_recetas (url_imagen) VALUES (?)";
                $stmt_img = $conexion->prepare($sql_img);
                $filePathInDB = "../../uploads/" . $fileName;
                $stmt_img->bind_param("s", $filePathInDB);
                if ($stmt_img->execute() === TRUE) {
                    $img_id = $stmt_img->insert_id;
                    $sql_relacion = "INSERT INTO imagenes_recetas (recetas_id, img_id) VALUES (?, ?)";
                    $stmt_relacion = $conexion->prepare($sql_relacion);
                    $stmt_relacion->bind_param("ii", $receta_id, $img_id);
                    $stmt_relacion->execute();
                }
            }
        }
    }

    header("Location: vista-subir-receta.php?status=success");
    exit();
} else {
    header("Location: vista-subir-receta.php?status=error");
    exit();
}

$conexion->close();
