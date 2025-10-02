<?php
session_start();
include('conexion.php');

if (isset($_POST['guardar'])) {
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $provincia = $_POST['provincia'];
    $departamento = $_POST['departamento'];
    $localidad = $_POST['localidad'];
    $barrio = $_POST['barrio'];
    $pais = $_POST['pais'];

    // Obtener los datos del formulario de registro
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $telefonos = json_decode($_POST['telefonos'], true);
    $tipos = json_decode($_POST['tipos'], true);
    $contrasena = $_POST['contrasena'];
    $rol = 2; // Asignar el rol de usuario
    $estado = 'pendiente';

    // Verificar si el género existe en la tabla generos
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM generos WHERE id_genero = ?");
    $stmt->bind_param("i", $genero);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists == 0) {
        die("Error: El género seleccionado no existe.");
    }

    // Insertar la localidad en la base de datos
    $stmt = $conexion->prepare("INSERT INTO localidades (provincia, departamento, localidad, barrio, latitud, longitud, pais) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdds", $provincia, $departamento, $localidad, $barrio, $latitud, $longitud, $pais);
    $stmt->execute();
    $id_localidad = $stmt->insert_id;
    $stmt->close();

    // Insertar el usuario en la base de datos con la referencia a la localidad
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, genero, nombre_usuario, contrasena, rol, estado, id_localidad) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $nombre, $apellido, $genero, $usuario, $contrasena, $rol, $estado, $id_localidad);
    $stmt->execute();
    $id_usuario = $stmt->insert_id;
    $stmt->close();

    // Insertar el email del usuario en la tabla emails_usuarios
    $stmt = $conexion->prepare("INSERT INTO emails_usuarios (id_usuario, email, tipo) VALUES (?, ?, 'Personal')");
    $stmt->bind_param("is", $id_usuario, $email);
    $stmt->execute();
    $stmt->close();

    // Insertar los teléfonos del usuario en la tabla telefonos_usuarios
    foreach ($telefonos as $index => $telefono) {
        $tipo = $tipos[$index];
        $stmt = $conexion->prepare("INSERT INTO telefonos_usuarios (id_usuario, telefono, tipo) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_usuario, $telefono, $tipo);
        $stmt->execute();
        $stmt->close();
    }

    // Mensaje de éxito y redirección
    $_SESSION['registro_exitoso'] = 'Espere a que su cuenta sea validada, le llegará un correo de verificación';
    $_SESSION['active_tab'] = 'login';
    header("Location: Login.php");
    exit();
}
