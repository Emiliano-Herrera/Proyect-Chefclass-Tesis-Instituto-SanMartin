<?php
session_start();
require_once '../../VistaAdmin/html/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header('Location: login.php');
    exit;
}

// --- Validación y limpieza de datos ---
$nombre = trim(preg_replace('/\s+/', ' ', $_POST['nombre']));
$apellido = trim(preg_replace('/\s+/', ' ', $_POST['apellido']));
$nombre_usuario = trim(preg_replace('/\s+/', ' ', $_POST['nombre_usuario']));
$genero = $_POST['genero'] ?? '';

$email_ids = $_POST['email_ids'] ?? [];
$emails = $_POST['emails'] ?? [];
$tipos_email = $_POST['tipo_email'] ?? [];

$telefono_ids = $_POST['telefono_ids'] ?? [];
$telefonos = $_POST['telefonos'] ?? [];
$tipos_telefono = $_POST['tipo_telefono'] ?? [];


// --- Validación de emails ---
$emails_limpios = [];
foreach ($emails as $email) {
    $email = trim($email);
    if (!empty($email)) {
        // Validar formato
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['perfil_error'] = "El email '$email' no tiene un formato válido.";
            header("Location: editar-perfil.php");
            exit;
        }
        // No permitir repetidos en el mismo formulario
        if (in_array($email, $emails_limpios)) {
            $_SESSION['perfil_error'] = "No puedes repetir emails.";
            header("Location: editar-perfil.php");
            exit;
        }
        // No permitir repetidos en otros usuarios
        $stmt = $conexion->prepare("SELECT id_usuario FROM emails_usuarios WHERE email = ? AND id_usuario != ?");
        $stmt->bind_param("si", $email, $id_usuario);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['perfil_error'] = "El email '$email' ya está registrado por otro usuario.";
            header("Location: editar-perfil.php");
            exit;
        }
        $stmt->close();
        $emails_limpios[] = $email;
    }
}
if (count($emails_limpios) > 2) {
    $_SESSION['perfil_error'] = "Solo puedes registrar hasta 2 emails.";
    header("Location: editar-perfil.php");
    exit;
}

// --- Validación de teléfonos ---
$telefonos_limpios = [];
foreach ($telefonos as $telefono) {
    $telefono = trim($telefono);
    if (!empty($telefono)) {
        // Solo números y longitud
        if (!preg_match('/^\d{10,13}$/', $telefono)) {
            $_SESSION['perfil_error'] = "El teléfono '$telefono' debe tener entre 10 y 13 dígitos numéricos.";
            header("Location: editar-perfil.php");
            exit;
        }
        // No permitir repetidos en el mismo formulario
        if (in_array($telefono, $telefonos_limpios)) {
            $_SESSION['perfil_error'] = "No puedes repetir teléfonos.";
            header("Location: editar-perfil.php");
            exit;
        }
        // No permitir repetidos en otros usuarios
        $stmt = $conexion->prepare("SELECT id_usuario FROM telefonos_usuarios WHERE telefono = ? AND id_usuario != ?");
        $stmt->bind_param("si", $telefono, $id_usuario);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['perfil_error'] = "El teléfono '$telefono' ya está registrado por otro usuario.";
            header("Location: editar-perfil.php");
            exit;
        }
        $stmt->close();
        $telefonos_limpios[] = $telefono;
    }
}
if (count($telefonos_limpios) > 2) {
    $_SESSION['perfil_error'] = "Solo puedes registrar hasta 2 teléfonos.";
    header("Location: editar-perfil.php");
    exit;
}

// --- Ubicación ---
$latitud = isset($_POST['latitud']) ? round(floatval($_POST['latitud']), 6) : null;
$longitud = isset($_POST['longitud']) ? round(floatval($_POST['longitud']), 6) : null;
$provincia = $_POST['provincia'] ?? null;
$departamento = $_POST['departamento'] ?? null;
$localidad = $_POST['localidad'] ?? null;
$barrio = $_POST['barrio'] ?? null;
$pais = $_POST['pais'] ?? null;

$id_localidad = $_POST['id_localidad'] ?? null;


// --- Imagen de perfil ---
$img_path = null;
$img_anterior = null;

// 1. Obtener la imagen anterior del usuario
$stmt = $conexion->prepare("SELECT img FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($img_anterior);
$stmt->fetch();
$stmt->close();

if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $img_tmp = $_FILES['img']['tmp_name'];
    $img_name = uniqid('perfil_') . '_' . basename($_FILES['img']['name']);
    $img_dest = '../../VistaAdmin/html/img_perfil/' . $img_name;
    if (move_uploaded_file($img_tmp, $img_dest)) {
        $img_path = 'img_perfil/' . $img_name;

        // 2. Eliminar la imagen anterior si existe y no es la por defecto
        if ($img_anterior && $img_anterior !== 'img_perfil/default.png') {
            $ruta_anterior = '../../VistaAdmin/html/' . $img_anterior;
            if (file_exists($ruta_anterior)) {
                unlink($ruta_anterior);
            }
        }
    }
}

// --- Localidad: buscar o crear si es necesario ---
if (empty($id_localidad) && $latitud && $longitud) {
    $sql = "SELECT id_localidad FROM localidades WHERE ROUND(latitud,6) = ? AND ROUND(longitud,6) = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("dd", $latitud, $longitud);
    $stmt->execute();
    $stmt->bind_result($id_localidad_encontrada);
    if ($stmt->fetch()) {
        $id_localidad = $id_localidad_encontrada;
    } else {
        $stmt->close();
        $sql = "INSERT INTO localidades (latitud, longitud, provincia, departamento, localidad, barrio, pais) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ddsssss", $latitud, $longitud, $provincia, $departamento, $localidad, $barrio, $pais);
        $stmt->execute();
        $id_localidad = $conexion->insert_id;
    }
    $stmt->close();
}

// --- Actualizar datos básicos del usuario ---
if ($img_path) {
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre=?, apellido=?, nombre_usuario=?, genero=?, img=?, id_localidad=? WHERE id_usuario=?");
    $stmt->bind_param("ssssssi", $nombre, $apellido, $nombre_usuario, $genero, $img_path, $id_localidad, $id_usuario);
} else {
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre=?, apellido=?, nombre_usuario=?, genero=?, id_localidad=? WHERE id_usuario=?");
    $stmt->bind_param("ssssii", $nombre, $apellido, $nombre_usuario, $genero, $id_localidad, $id_usuario);
}
$stmt->execute();

// --- Eliminar localidades huérfanas (no asociadas a ningún usuario) ---
$sql = "DELETE FROM localidades WHERE id_localidad NOT IN (SELECT id_localidad FROM usuarios WHERE id_localidad IS NOT NULL)";
$conexion->query($sql);

// --- Emails ---
$ids_actuales = [];
$res = $conexion->query("SELECT id_email FROM emails_usuarios WHERE id_usuario = $id_usuario");
while ($row = $res->fetch_assoc()) {
    $ids_actuales[] = $row['id_email'];
}
$ids_enviados = array_filter($email_ids);

// Eliminar los que no están en el formulario
foreach ($ids_actuales as $id) {
    if (!in_array($id, $ids_enviados)) {
        $conexion->query("DELETE FROM emails_usuarios WHERE id_email = $id");
    }
}

// Actualizar o insertar
for ($i = 0; $i < count($emails); $i++) {
    $id = $email_ids[$i];
    $email = $emails[$i];
    $tipo = $tipos_email[$i];
    if (!empty($email)) {
        if ($id) {
            // Actualizar
            $sql = "UPDATE emails_usuarios SET email=?, tipo=? WHERE id_email=? AND id_usuario=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssii", $email, $tipo, $id, $id_usuario);
            $stmt->execute();
        } else {
            // Insertar
            $sql = "INSERT INTO emails_usuarios (id_usuario, email, tipo) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iss", $id_usuario, $email, $tipo);
            $stmt->execute();
        }
    }
}

// --- Teléfonos ---
$ids_actuales_tel = [];
$res = $conexion->query("SELECT id_telefono FROM telefonos_usuarios WHERE id_usuario = $id_usuario");
while ($row = $res->fetch_assoc()) {
    $ids_actuales_tel[] = $row['id_telefono'];
}
$ids_enviados_tel = array_filter($telefono_ids);

// Eliminar los que no están en el formulario
foreach ($ids_actuales_tel as $id) {
    if (!in_array($id, $ids_enviados_tel)) {
        $conexion->query("DELETE FROM telefonos_usuarios WHERE id_telefono = $id");
    }
}

// Actualizar o insertar
for ($i = 0; $i < count($telefonos); $i++) {
    $id = $telefono_ids[$i];
    $telefono = $telefonos[$i];
    $tipo = $tipos_telefono[$i];
    if (!empty($telefono)) {
        if ($id) {
            // Actualizar
            $sql = "UPDATE telefonos_usuarios SET telefono=?, tipo=? WHERE id_telefono=? AND id_usuario=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssii", $telefono, $tipo, $id, $id_usuario);
            $stmt->execute();
        } else {
            // Insertar
            $sql = "INSERT INTO telefonos_usuarios (id_usuario, telefono, tipo) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iss", $id_usuario, $telefono, $tipo);
            $stmt->execute();
        }
    }
}

// Si todo OK
header("Location: vista-perfil.php?status=success");
exit;
