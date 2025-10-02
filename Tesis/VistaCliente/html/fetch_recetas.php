<?php
include("conexion.php");

$categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$recipes_per_page = 3; // Número de recetas por página
$offset = ($page - 1) * $recipes_per_page;

$response = [];

// Obtener recetas para la categoría con paginación
$sql_recetas = "
    SELECT R.id_receta, R.titulo, R.descripcion, R.dificultad, RI.url_imagen, U.nombre_usuario, R.fecha_creacion, AVG(C.calificacion) as promedio_calificacion
    FROM recetas R 
    JOIN recetas_categorias RC ON R.id_receta = RC.receta_id 
    JOIN imagenes_recetas IR ON R.id_receta = IR.recetas_id 
    JOIN img_recetas RI ON IR.img_id = RI.id_img 
    JOIN usuarios U ON R.usuario_id = U.id_usuario
    LEFT JOIN calificaciones C ON R.id_receta = C.receta_id
    WHERE RC.categoria_id = ? 
    AND R.estado = 'habilitado'
    GROUP BY R.id_receta
    LIMIT ? OFFSET ?
";
$stmt = $conexion->prepare($sql_recetas);
$stmt->bind_param("iii", $categoria_id, $recipes_per_page, $offset);
$stmt->execute();
$result_recetas = $stmt->get_result();

$recetas = [];
if ($result_recetas->num_rows > 0) {
    while ($row = $result_recetas->fetch_assoc()) {
        if (!isset($recetas[$row['id_receta']])) {
            $recetas[$row['id_receta']] = $row;
            $recetas[$row['id_receta']]['imagenes'] = [];
        }
        $recetas[$row['id_receta']]['imagenes'][] = $row['url_imagen'];
    }
}

// Obtener el número total de recetas para la categoría
$sql_total_recetas = "
    SELECT COUNT(*) as total
    FROM recetas R
    JOIN recetas_categorias RC ON R.id_receta = RC.receta_id
    WHERE RC.categoria_id = ? AND R.estado = 'habilitado'
";
$stmt_total = $conexion->prepare($sql_total_recetas);
$stmt_total->bind_param("i", $categoria_id);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_recetas = $result_total->fetch_assoc()['total'];

$total_pages = ceil($total_recetas / $recipes_per_page);

$response['recetas'] = array_values($recetas); // Se asegura de que sea un arreglo indexado
$response['total_pages'] = $total_pages;

header('Content-Type: application/json');
echo json_encode($response);
