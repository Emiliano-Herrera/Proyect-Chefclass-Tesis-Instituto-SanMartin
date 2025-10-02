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

// Recibe datos por POST
$id_rol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;
$nombre_rol = isset($_POST['nombre_rol']) ? trim($_POST['nombre_rol']) : '';
$permisos = isset($_POST['permisos']) ? $_POST['permisos'] : [];

$nombre_rol = mb_convert_case($nombre_rol, MB_CASE_TITLE, "UTF-8");

$permisos = isset($_POST['permisos']) ? $_POST['permisos'] : [];
if (is_string($permisos)) {
    $permisos = json_decode($permisos, true);
}

if ($id_rol <= 0 || $nombre_rol === '') {
    $response['message'] = 'Datos incompletos.';
    echo json_encode($response);
    exit;
}
if (!is_array($permisos) || count($permisos) === 0) {
    $response['message'] = 'Debe seleccionar al menos un permiso.';
    echo json_encode($response);
    exit;
}

// Validar nombre (solo letras y espacios)
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+( [A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/', $nombre_rol)) {
    $response['message'] = 'El nombre del rol solo puede contener letras y espacios.';
    echo json_encode($response);
    exit;
}

// Verificar si el nombre ya existe en otro rol
$stmtCheck = $conexion->prepare("SELECT COUNT(*) FROM roles WHERE nombre_rol = ? AND id_rol != ?");
$stmtCheck->bind_param("si", $nombre_rol, $id_rol);
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
    // Obtener el nombre anterior del rol
    $stmtSelect = $conexion->prepare("SELECT nombre_rol FROM roles WHERE id_rol = ?");
    $stmtSelect->bind_param("i", $id_rol);
    $stmtSelect->execute();
    $stmtSelect->bind_result($nombre_rol_anterior);
    $stmtSelect->fetch();
    $stmtSelect->close();

    // Actualizar nombre del rol
    $stmt = $conexion->prepare("UPDATE roles SET nombre_rol = ? WHERE id_rol = ?");
    $stmt->bind_param('si', $nombre_rol, $id_rol);
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar el rol: ' . $stmt->error);
    }
    $stmt->close();

    // Eliminar permisos anteriores
    $conexion->query("DELETE FROM roles_permisos_secciones WHERE id_rol = $id_rol");

    // Insertar nuevos permisos
    $stmtPerm = $conexion->prepare("INSERT INTO roles_permisos_secciones (id_rol, id_seccion, permisos) VALUES (?, ?, ?)");
    foreach ($permisos as $id_seccion => $permisos_seccion) {
        if (!is_array($permisos_seccion) || count($permisos_seccion) === 0) continue;
        $permisos_str = implode(',', $permisos_seccion);
        $stmtPerm->bind_param("iis", $id_rol, $id_seccion, $permisos_str);
        if (!$stmtPerm->execute()) {
            throw new Exception("Error al guardar permisos: " . $stmtPerm->error);
        }
    }
    $stmtPerm->close();

    // Registrar en historial
    $accion = "Modificación de rol";
    $fecha = date('Y-m-d H:i:s');
    $detalle = "El rol '$nombre_rol_anterior' ha sido modificado a '$nombre_rol' por '$Nombre $Apellido' con nuevos permisos en la fecha '" . date('d-m-Y H:i:s') . "'.";
    $detalle = mysqli_real_escape_string($conexion, $detalle);
    $sqlHistorial = "INSERT INTO historial_usuarios (id_usuario, Accion, Detalles, Fecha) VALUES ('$ID_Usuario', '$accion', '$detalle', '$fecha')";
    $resultHistorial = mysqli_query($conexion, $sqlHistorial);

    if ($resultHistorial) {
        $conexion->commit();
        $response['status'] = 'success';
        $response['message'] = "El rol ha sido modificado correctamente.";
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