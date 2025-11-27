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
// Usar prepared statements para insertar datos de la receta (evitar SQL injection)
$sql_receta = "INSERT INTO recetas (titulo, tiempo_preparacion, descripcion, dificultad, usuario_id, estado) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql_receta);
$stmt->bind_param("ssssis", $titulo, $tiempo_preparacion, $descripcion, $dificultad, $user_id, $estado);

// Si la receta se inserta correctamente
if ($stmt->execute() === TRUE) {
    // Obtener el ID de la receta recién insertada
    $receta_id = $stmt->insert_id;

    // ========== MANEJAR CATEGORÍAS - RELACIÓN MUCHOS A MUCHOS ==========
    // Verificar si el usuario seleccionó al menos una categoría para la receta
    if (!empty($categorias)) {
        // Preparar la consulta SQL para insertar en la tabla intermedia recetas_categorias
        // Esta tabla relaciona recetas con categorías (relación muchos a muchos)
        $sql_categoria = "INSERT INTO recetas_categorias (receta_id, categoria_id) VALUES (?, ?)";
        $stmt_categoria = $conexion->prepare($sql_categoria);

        // Recorrer cada categoría seleccionada por el usuario
        foreach ($categorias as $categoria_id) {
            // Convertir el ID de categoría a entero para prevenir inyección SQL
            $categoria_id = intval($categoria_id);

            // Vincular parámetros: receta_id (entero) y categoria_id (entero)
            $stmt_categoria->bind_param("ii", $receta_id, $categoria_id);

            // Ejecutar la inserción de la relación receta-categoría
            if (!$stmt_categoria->execute()) {
                // Si falla una categoría, registrar el error pero continuar con las demás
                // Esto evita que un error en una categoría detenga todo el proceso
                error_log("Error al insertar categoría: " . $stmt_categoria->error);
            }
        }
        // Cerrar el statement de categorías para liberar recursos
        $stmt_categoria->close();
    } else {
        // Si no hay categorías seleccionadas, es un error crítico
        // Redirigir al formulario con mensaje de error específico
        header("Location: vista-subir-receta.php?status=error&message=No se seleccionaron categorías");
        exit(); // Terminar la ejecución del script
    }

    // ========== INSERTAR PASOS DE PREPARACIÓN - INSTRUCCIONES PASO A PASO ==========
    // Preparar consulta para insertar los pasos de preparación de la receta
    $sql_paso = "INSERT INTO instrucciones (receta_id, paso, descripcion) VALUES (?, ?, ?)";
    $stmt_paso = $conexion->prepare($sql_paso);

    // Recorrer todos los pasos enviados desde el formulario
    // Se asume que $pasos y $num_pasos son arrays del mismo tamaño
    for ($i = 0; $i < count($pasos); $i++) {
        // Obtener el número del paso (ej: 1, 2, 3...) para mantener el orden
        $paso = $num_pasos[$i];

        // Obtener la descripción detallada de este paso específico
        $descripcion_paso = $pasos[$i];

        // Vincular parámetros: receta_id (entero), paso (entero), descripcion (string)
        $stmt_paso->bind_param("iis", $receta_id, $paso, $descripcion_paso);

        // Ejecutar la inserción de este paso específico
        $stmt_paso->execute();
    }

    // ========== MANEJAR INGREDIENTES - SISTEMA DE REUTILIZACIÓN ==========
    // Consulta para insertar nuevos ingredientes en la tabla maestra
    $sql_ingrediente = "INSERT INTO ingredientes (nombre) VALUES (?)";
    $stmt_ingrediente = $conexion->prepare($sql_ingrediente);

    // Consulta para relacionar ingredientes con la receta actual, incluyendo cantidad y unidad
    $sql_receta_ingrediente = "INSERT INTO recetas_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (?, ?, ?, ?)";
    $stmt_receta_ingrediente = $conexion->prepare($sql_receta_ingrediente);

    // Recorrer todos los ingredientes enviados desde el formulario
    for ($i = 0; $i < count($ingredientes); $i++) {
        // Obtener datos del ingrediente actual
        $nombre = $ingredientes[$i];        // Nombre del ingrediente (ej: "Harina")
        $cantidad = $cantidades[$i];        // Cantidad necesaria (ej: "2")
        $unidad = $unidades[$i];            // Unidad de medida (ej: "tazas")

        // ========== VERIFICAR SI EL INGREDIENTE YA EXISTE ==========
        // Consulta para buscar si el ingrediente ya está en la base de datos
        $sql_verificar_ingrediente = "SELECT id_ingrediente FROM ingredientes WHERE nombre = ?";
        $stmt_verificar = $conexion->prepare($sql_verificar_ingrediente);
        $stmt_verificar->bind_param("s", $nombre); // "s" = string
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        // Si el ingrediente ya existe en la base de datos
        if ($result_verificar->num_rows > 0) {
            // Obtener el ID del ingrediente existente
            $row = $result_verificar->fetch_assoc();
            $ingrediente_id = $row['id_ingrediente'];
        } else {
            // Si el ingrediente no existe, insertarlo como nuevo
            $stmt_ingrediente->bind_param("s", $nombre);
            $stmt_ingrediente->execute();
            // Obtener el ID del nuevo ingrediente insertado
            $ingrediente_id = $stmt_ingrediente->insert_id;
        }

        // ========== RELACIONAR INGREDIENTE CON LA RECETA ACTUAL ==========
        // Vincular parámetros: receta_id (entero), ingrediente_id (entero), cantidad (string), unidad (string)
        $stmt_receta_ingrediente->bind_param("iiss", $receta_id, $ingrediente_id, $cantidad, $unidad);

        // Ejecutar la inserción de la relación receta-ingrediente
        $stmt_receta_ingrediente->execute();
    }

    // ========== MANEJAR ARCHIVOS MULTIMEDIA - IMÁGENES Y VIDEOS ==========
    // Directorio donde se guardarán los archivos subidos
    $targetDir = "../../uploads/";

    // Verificar si el directorio de uploads existe, si no, crearlo
    if (!is_dir($targetDir)) {
        // Crear directorio con permisos de lectura, escritura y ejecución
        mkdir($targetDir, 0777, true);
    }

    // Verificar si se subió al menos un archivo multimedia
    if (!empty($_FILES['media_files']['name'][0])) {
        // Recorrer cada archivo subido en el array $_FILES
        foreach ($_FILES['media_files']['name'] as $key => $val) {
            // Obtener el nombre del archivo actual
            $fileName = basename($_FILES['media_files']['name'][$key]);

            // Ruta completa donde se guardará el archivo
            $targetFilePath = $targetDir . $fileName;

            // Mover el archivo desde la ubicación temporal al directorio destino
            if (move_uploaded_file($_FILES['media_files']['tmp_name'][$key], $targetFilePath)) {
                // ========== GUARDAR INFORMACIÓN DE LA IMAGEN EN LA BASE DE DATOS ==========
                // Preparar consulta para insertar la ruta de la imagen
                $sql_img = "INSERT INTO img_recetas (url_imagen) VALUES (?)";
                $stmt_img = $conexion->prepare($sql_img);

                // Ruta que se guardará en la base de datos (relativa al sistema de archivos)
                $filePathInDB = "../../uploads/" . $fileName;
                $stmt_img->bind_param("s", $filePathInDB);

                // Si la imagen se inserta correctamente en img_recetas
                if ($stmt_img->execute() === TRUE) {
                    // Obtener el ID de la imagen recién insertada
                    $img_id = $stmt_img->insert_id;

                    // ========== RELACIONAR IMAGEN CON LA RECETA ==========
                    // Preparar consulta para la tabla intermedia imagenes_recetas
                    $sql_relacion = "INSERT INTO imagenes_recetas (recetas_id, img_id) VALUES (?, ?)";
                    $stmt_relacion = $conexion->prepare($sql_relacion);

                    // Vincular receta_id e img_id
                    $stmt_relacion->bind_param("ii", $receta_id, $img_id);

                    // Ejecutar la inserción de la relación receta-imagen
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
