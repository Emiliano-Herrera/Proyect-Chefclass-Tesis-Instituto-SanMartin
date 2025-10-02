<?php
include("conexion.php");
session_start();
date_default_timezone_set('America/Argentina/Catamarca');
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'No se recibieron los datos correctamente.'];

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Sesión no válida.';
    echo json_encode($response);
    exit;
}

$ID_Usuario = $_SESSION['id_usuario'];
$Nombre = $_SESSION['nombre'];
$Apellido = $_SESSION['apellido'];

// Recibe datos por POST (nombre del rol y permisos)
$nombre_rol = isset($_POST['nombre_rol']) ? trim($_POST['nombre_rol']) : '';
// Formatear: primera letra de cada palabra en mayúscula, el resto en minúscula
$nombre_rol = mb_convert_case($nombre_rol, MB_CASE_TITLE, "UTF-8");
$permisos = isset($_POST['permisos']) ? $_POST['permisos'] : [];

if ($nombre_rol === '') {
    $response['message'] = 'El nombre del rol es obligatorio.';
    echo json_encode($response);
    exit;
}
if (!is_array($permisos) || count($permisos) === 0) {
    $response['message'] = 'Debe seleccionar al menos un permiso.';
    echo json_encode($response);
    exit;
}

// Validar que el nombre solo tenga letras y espacios (igual que en el frontend)
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+( [A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/', $nombre_rol)) {
    $response['message'] = 'El nombre del rol solo puede contener letras y espacios.';
    echo json_encode($response);
    exit;
}

// Verificar si el rol ya existe (nombre único)
$stmtCheck = $conexion->prepare("SELECT COUNT(*) FROM roles WHERE nombre_rol = ?");
$stmtCheck->bind_param("s", $nombre_rol);
$stmtCheck->execute();
$stmtCheck->bind_result($count);
$stmtCheck->fetch();
$stmtCheck->close();

if ($count > 0) {
    echo json_encode(['status' => 'exists']);
    exit;
}

$conexion->begin_transaction();
try {
    // Insertar el rol (por defecto habilitado)
    $stmt = $conexion->prepare("INSERT INTO roles (nombre_rol, estado) VALUES (?, 'habilitado')");
    $stmt->bind_param("s", $nombre_rol);

    if (!$stmt->execute()) {
        throw new Exception('Error al registrar el rol: ' . $stmt->error);
    }
    $id_rol = $conexion->insert_id;
    $stmt->close();

    // Insertar permisos por sección
    $stmtPerm = $conexion->prepare("INSERT INTO roles_permisos_secciones (id_rol, id_seccion, permisos) VALUES (?, ?, ?)");
    foreach ($permisos as $id_seccion => $permisos_seccion) {
        // Validar que $permisos_seccion sea array y no esté vacío
        if (!is_array($permisos_seccion) || count($permisos_seccion) === 0) continue;
        // Unir los permisos en formato SET (ej: 'detalle,editar,crear')
        $permisos_str = implode(',', $permisos_seccion);
        $stmtPerm->bind_param("iis", $id_rol, $id_seccion, $permisos_str);
        if (!$stmtPerm->execute()) {
            throw new Exception("Error al guardar permisos: " . $stmtPerm->error);
        }
    }
    $stmtPerm->close();

    // Registrar en historial
    $accion = "Creación de rol";
    $fecha = date('Y-m-d H:i:s');
    $detalle = "El rol '$nombre_rol' ha sido creado por '$Nombre $Apellido' con permisos asignados en la fecha '" . date('d-m-Y H:i:s') . "'.";
    $detalle = mysqli_real_escape_string($conexion, $detalle);
    $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES ('$ID_Usuario', '$accion', '$detalle', '$fecha')";
    $resultHistorial = mysqli_query($conexion, $sqlHistorial);

    if ($resultHistorial) {
        $conexion->commit();
        $response['status'] = 'success';
        $response['message'] = 'El rol y sus permisos han sido registrados correctamente.';
    } else {
        $conexion->rollback();
        $response['message'] = 'Error al registrar en el historial.';
    }
} catch (Exception $e) {
    $conexion->rollback();
    $response['message'] = 'Error: ' . $e->getMessage();
}

$conexion->close();
echo json_encode($response);
?>