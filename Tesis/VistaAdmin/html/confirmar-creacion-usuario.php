<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

include("conexion.php");

$id = $_REQUEST['id'];
$ID_Usuario = isset($_GET['ID_Usuario']) ? $_GET['ID_Usuario'] : null;

$response = [
    'status' => '',
    'message' => ''
];

if (isset($_GET['confirmacion']) && $_GET['confirmacion'] === 'si' && $ID_Usuario) {
    $sql = "UPDATE usuarios SET estado='habilitado', usuario_creacion=$ID_Usuario WHERE id_usuario = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        // Obtener el email del usuario habilitado
        $query = "SELECT E.email FROM usuarios U INNER JOIN emails_usuarios E ON U.id_usuario = E.id_usuario WHERE U.id_usuario = $id";
        $result = $conexion->query($query);
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // Enviar correo de confirmación
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tesisdesarrollodesoftware@gmail.com';
            $mail->Password = 'bltw drma rqvn niui';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Información para el envío de correos
            $mail->setFrom('tesisdesarrollodesoftware@gmail.com', 'Chefclass');
            $mail->addAddress($email);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Cuenta Habilitada';
            $mail->Body = 'Hola, tu cuenta ha sido habilitada. Ahora puedes iniciar sesión <a href="http://localhost/Tesis/VistaAdmin/html/Login.php">aquí</a>.';
            $mail->AltBody = 'Hola, tu cuenta ha sido habilitada. Ahora puedes iniciar sesión en http://localhost/Tesis/VistaAdmin/html/Login.php.';

            $mail->send();
            $response['status'] = 'success';
            $response['message'] = 'Usuario habilitado exitosamente y correo enviado.';
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = "Usuario habilitado pero no se pudo enviar el correo. Error de PHPMailer: {$mail->ErrorInfo}";
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'No se pudo habilitar el usuario.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'ID de usuario de sesión no válido.';
}

// Convertir el array $response a JSON y enviarlo como respuesta
echo json_encode($response);
