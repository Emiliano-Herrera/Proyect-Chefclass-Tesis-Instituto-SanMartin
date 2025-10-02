<?php
session_start(); // Inicia la sesión

// Destruye todas las variables de sesión
$_SESSION = array();

// Si deseas destruir completamente la sesión, descomenta la siguiente línea
// session_destroy();

// Redirecciona al usuario a la página de inicio de sesión
header("Location: Login.php");
exit();
?>
