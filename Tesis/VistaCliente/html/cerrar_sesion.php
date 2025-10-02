<?php
session_start(); // Inicia la sesión

// Destruye todas las variables de sesión
$_SESSION = array();

// Si deseas destruir completamente la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Destruye la sesión

// Redirecciona al usuario a la página de inicio de sesión
header("Location: ../../VistaAdmin/html/Login.php");
exit();
?>
