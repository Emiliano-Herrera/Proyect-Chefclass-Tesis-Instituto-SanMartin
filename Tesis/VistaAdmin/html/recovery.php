<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

require("conexion.php");

// Iniciar buffer de salida
ob_start();

$email = $_POST['Email'];

$query = "SELECT U.* , E.* FROM usuarios U INNER JOIN emails_usuarios E ON U.id_usuario = E.id_usuario WHERE email = '$email'";
$result = $conexion->query($query);
$row = $result->fetch_assoc();

if ($result->num_rows > 0) {
    $mail = new PHPMailer(true);

    try {
        // Debug
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Habilitar depuración detallada

        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tesisdesarrollodesoftware@gmail.com'; // Reemplaza con tu nueva cuenta de Gmail
        $mail->Password = 'bltw drma rqvn niui'; // Reemplaza con tu nueva contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Información para el envío de correos
        $mail->setFrom('tesisdesarrollodesoftware@gmail.com', 'Chefclass');
        $mail->addAddress($email, 'Cambiar password'); // Añadir destinatario

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Recuperar password';
        $mail->Body = 'Hola, este es un correo generado para solicitar tu cambio de password, por favor, visita la pagina <a href="http://localhost/Tesis/VistaAdmin/html/cambiar_contrasena.php?id='.$row['id_usuario'].'">Cambiar password</a>';
        $mail->AltBody = 'Este es el cuerpo en texto plano para clientes de correo que no admiten HTML.';

        $mail->send();
        // Limpia el buffer de salida antes de redirigir
        ob_end_clean();
        header("Location: Login.php?message=ok");
    } catch (Exception $e) {
        ob_end_clean(); // Limpia el buffer en caso de excepción también
        echo "El mensaje no se pudo enviar. Error de PHPMailer: {$mail->ErrorInfo}";
    }
} else {
    ob_end_clean();
    header("Location: Login.php?message=not_found");
}
?>
