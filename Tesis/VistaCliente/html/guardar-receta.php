<?php
session_start();
include('conexion.php');

// Obtener el id_usuario desde la sesión
$user_id = $_SESSION['id_usuario'];

$titulo = $_POST['titulo'];
$tiempo = $_POST['tiempo']; // Formato HH:MM
$descripcion = $_POST['descripcion'];
$dificultad = $_POST['dificultad'];
$categorias = isset($_POST['categoria']) ? $_POST['categoria'] : []; // Asegurarse de que esté definida
$pasos = $_POST['pasos'];
$num_pasos = $_POST['num_pasos'];
$ingredientes = $_POST['ingredientes'];
$cantidades = $_POST['cantidades'];

// Asegurarse de que el tiempo está en el formato HH:MM:SS
$tiempo_preparacion = date("H:i:s", strtotime($tiempo));

// Usar prepared statements para insertar datos de la receta
$sql_receta = "INSERT INTO recetas (titulo, tiempo_preparacion, descripcion, dificultad, usuario_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql_receta);
$stmt->bind_param("ssssi", $titulo, $tiempo_preparacion, $descripcion, $dificultad, $user_id);

if ($stmt->execute() === TRUE) {
    $receta_id = $stmt->insert_id;

    // Insertar categorías de la receta
    $sql_categoria = "INSERT INTO recetas_categorias (receta_id, categoria_id) VALUES (?, ?)";
    $stmt_categoria = $conexion->prepare($sql_categoria);
    foreach ($categorias as $categoria_id) {
        $stmt_categoria->bind_param("ii", $receta_id, $categoria_id);
        $stmt_categoria->execute();
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

    // Insertar ingredientes de la receta
    $sql_ingrediente = "INSERT INTO ingredientes (nombre, cantidad) VALUES (?, ?)";
    $stmt_ingrediente = $conexion->prepare($sql_ingrediente);
    $sql_receta_ingrediente = "INSERT INTO recetas_ingredientes (receta_id, ingrediente_id) VALUES (?, ?)";
    $stmt_receta_ingrediente = $conexion->prepare($sql_receta_ingrediente);
    for ($i = 0; $i < count($ingredientes); $i++) {
        $nombre = $ingredientes[$i];
        $cantidad = $cantidades[$i];
        $stmt_ingrediente->bind_param("ss", $nombre, $cantidad);
        $stmt_ingrediente->execute();
        $ingrediente_id = $stmt_ingrediente->insert_id;
        $stmt_receta_ingrediente->bind_param("ii", $receta_id, $ingrediente_id);
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
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Subir archivo al servidor
            if (move_uploaded_file($_FILES['media_files']['tmp_name'][$key], $targetFilePath)) {
                // Insertar la ruta del archivo en img_recetas
                $sql_img = "INSERT INTO img_recetas (url_imagen) VALUES (?)";
                $stmt_img = $conexion->prepare($sql_img);
                $stmt_img->bind_param("s", $targetFilePath);
                if ($stmt_img->execute() === TRUE) {
                    $img_id = $stmt_img->insert_id;
                    // Insertar la relación en imagenes_recetas
                    $sql_relacion = "INSERT INTO imagenes_recetas (recetas_id, img_id) VALUES (?, ?)";
                    $stmt_relacion = $conexion->prepare($sql_relacion);
                    $stmt_relacion->bind_param("ii", $receta_id, $img_id);
                    $stmt_relacion->execute();
                }
            }
        }
    }

    // Redirigir con mensaje de éxito
    header("Location: ../vista-subir-receta.php?status=success");
    exit();
} else {
    // Redirigir con mensaje de error
    header("Location: ../vista-subir-receta.php?status=error");
    exit();
}

$conexion->close();
?>
