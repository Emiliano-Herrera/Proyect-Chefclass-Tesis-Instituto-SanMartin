<?php
// Iniciar sesión para acceder a los datos del usuario
session_start();
// Incluir archivo de conexión a la base de datos
include('conexion.php');

// Obtener el id_usuario desde la sesión (usuario que está subiendo la receta)
$user_id = $_SESSION['id_usuario'];

// Recoger todos los datos del formulario POST
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
$estado = 'pendiente'; // Estado inicial de la receta

// Convertir el tiempo al formato HH:MM:SS para la base de datos
$tiempo_preparacion = date("H:i:s", strtotime($tiempo));

// Validar formatos de archivos multimedia permitidos
$formatosPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];
// Verificar si se subieron archivos
if (!empty($_FILES['media_files']['name'][0])) {
    // Revisar cada archivo subido
    foreach ($_FILES['media_files']['name'] as $key => $val) {
        $fileName = basename($_FILES['media_files']['name'][$key]);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Si la extensión no está permitida, redirigir con error
        if (!in_array($extension, $formatosPermitidos)) {
            header("Location: vista-subir-receta.php?status=error_formato");
            exit();
        }
    }
}

// ========== INSERTAR RECETA PRINCIPAL ==========
// Usar prepared statements para insertar datos de la receta (prevención de SQL injection)
$sql_receta = "INSERT INTO recetas (titulo, tiempo_preparacion, descripcion, dificultad, usuario_id, estado) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql_receta);
$stmt->bind_param("ssssis", $titulo, $tiempo_preparacion, $descripcion, $dificultad, $user_id, $estado);

// Si la receta se inserta correctamente
if ($stmt->execute() === TRUE) {
    // Obtener el ID de la receta recién insertada
    $receta_id = $stmt->insert_id;

    // ========== MANEJAR CATEGORÍAS ==========
    // Verificar si se enviaron categorías desde el formulario
    $categorias = isset($_POST['categoria']) ? $_POST['categoria'] : [];
    // Eliminar categorías duplicadas
    $categorias = array_unique($categorias);

    // Si hay categorías seleccionadas
    if (!empty($categorias)) {
        $sql_categoria = "INSERT INTO recetas_categorias (receta_id, categoria_id) VALUES (?, ?)";
        $stmt_categoria = $conexion->prepare($sql_categoria);

        // Insertar cada categoría en la tabla de relación
        foreach ($categorias as $categoria_id) {
            $categoria_id = intval($categoria_id); // Convertir a entero por seguridad
            $stmt_categoria->bind_param("ii", $receta_id, $categoria_id);
            if (!$stmt_categoria->execute()) {
                // Log de error si falla la inserción (no detiene el proceso)
                error_log("Error al insertar categoría: " . $stmt_categoria->error);
            }
        }
        $stmt_categoria->close();
    } else {
        // Si no hay categorías, redirigir con error
        header("Location: vista-subir-receta.php?status=error&message=No se seleccionaron categorías");
        exit();
    }

    // ========== INSERTAR PASOS DE PREPARACIÓN ==========
    $sql_paso = "INSERT INTO instrucciones (receta_id, paso, descripcion) VALUES (?, ?, ?)";
    $stmt_paso = $conexion->prepare($sql_paso);
    // Recorrer todos los pasos enviados
    for ($i = 0; $i < count($pasos); $i++) {
        $paso = $num_pasos[$i]; // Número del paso
        $descripcion_paso = $pasos[$i]; // Descripción del paso
        $stmt_paso->bind_param("iis", $receta_id, $paso, $descripcion_paso);
        $stmt_paso->execute();
    }

    // ========== MANEJAR INGREDIENTES ==========
    $sql_ingrediente = "INSERT INTO ingredientes (nombre) VALUES (?)";
    $stmt_ingrediente = $conexion->prepare($sql_ingrediente);
    $sql_receta_ingrediente = "INSERT INTO recetas_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (?, ?, ?, ?)";
    $stmt_receta_ingrediente = $conexion->prepare($sql_receta_ingrediente);

    // Recorrer todos los ingredientes enviados
    for ($i = 0; $i < count($ingredientes); $i++) {
        $nombre = $ingredientes[$i];
        $cantidad = $cantidades[$i];
        $unidad = $unidades[$i];

        // Verificar si el ingrediente ya existe en la base de datos
        $sql_verificar_ingrediente = "SELECT id_ingrediente FROM ingredientes WHERE nombre = ?";
        $stmt_verificar = $conexion->prepare($sql_verificar_ingrediente);
        $stmt_verificar->bind_param("s", $nombre);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        // Si el ingrediente ya existe, usar su ID existente
        if ($result_verificar->num_rows > 0) {
            $row = $result_verificar->fetch_assoc();
            $ingrediente_id = $row['id_ingrediente'];
        } else {
            // Si no existe, insertar nuevo ingrediente
            $stmt_ingrediente->bind_param("s", $nombre);
            $stmt_ingrediente->execute();
            $ingrediente_id = $stmt_ingrediente->insert_id;
        }

        // Insertar la relación entre receta e ingrediente con cantidad y unidad
        $stmt_receta_ingrediente->bind_param("iiss", $receta_id, $ingrediente_id, $cantidad, $unidad);
        $stmt_receta_ingrediente->execute();
    }

    // ========== MANEJAR ARCHIVOS MULTIMEDIA ==========
    $targetDir = "../../uploads/";
    // Crear directorio si no existe
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Si se subieron archivos multimedia
    if (!empty($_FILES['media_files']['name'][0])) {
        foreach ($_FILES['media_files']['name'] as $key => $val) {
            $fileName = basename($_FILES['media_files']['name'][$key]);
            $targetFilePath = $targetDir . $fileName;

            // Mover archivo del temporal al directorio destino
            if (move_uploaded_file($_FILES['media_files']['tmp_name'][$key], $targetFilePath)) {
                // Insertar información de la imagen en la base de datos
                $sql_img = "INSERT INTO img_recetas (url_imagen) VALUES (?)";
                $stmt_img = $conexion->prepare($sql_img);
                $filePathInDB = "../../uploads/" . $fileName;
                $stmt_img->bind_param("s", $filePathInDB);
                if ($stmt_img->execute() === TRUE) {
                    $img_id = $stmt_img->insert_id;
                    // Crear relación entre receta e imagen
                    $sql_relacion = "INSERT INTO imagenes_recetas (recetas_id, img_id) VALUES (?, ?)";
                    $stmt_relacion = $conexion->prepare($sql_relacion);
                    $stmt_relacion->bind_param("ii", $receta_id, $img_id);
                    $stmt_relacion->execute();
                }
            }
        }
    }

    // Redirigir a la página de subir receta con estado de éxito
    header("Location: vista-subir-receta.php?status=success");
    exit();
} else {
    // Si falla la inserción principal, redirigir con error
    header("Location: vista-subir-receta.php?status=error");
    exit();
}

// Cerrar conexión a la base de datos
$conexion->close();