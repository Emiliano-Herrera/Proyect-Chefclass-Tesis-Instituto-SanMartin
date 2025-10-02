<?php
/* Session_name();
Session_start(); */

$dbhost = "localhost: 3307";
$dbusuario = "root";
$dbpassword = "";
$db = "auxiliar";
$conexion = mysqli_connect($dbhost, $dbusuario, $dbpassword, $db);
if (!$conexion) {
    $error = mysqli_connect_error(); 
    header('Location: Error.html');
    exit;
} else {
    
?>
    
<?php
}



?>