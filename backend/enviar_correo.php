<?php

require_once __DIR__ . '/config/config.php';

require_once __DIR__ . '/../assets/vendors/phpmailer/src/Exception.php';
require_once __DIR__ . '/../assets/vendors/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../assets/vendors/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Configuración base de PHPMailer
 */
function crearMailer()
{
    global $hostMail;
    global $usernameMail;
    global $passMail;
    global $SMTPSecure;
    global $PortMail;
    global $mailAlias;
    global $nameMail;

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $hostMail;
    $mail->SMTPAuth   = true;
    $mail->Username   = $usernameMail;
    $mail->Password   = $passMail;
    $mail->SMTPSecure = $SMTPSecure;
    $mail->Port       = $PortMail;

    $mail->CharSet = "UTF-8";
    $mail->Encoding = "base64";
    $mail->isHTML(true);

    $mail->setFrom($mailAlias, $nameMail);

    return $mail;
}

/**
 * Correo al cliente
 */
function enviarCorreoCliente($nombre, $correo)
{
    try {

        $mail = crearMailer();

        $mail->addAddress($correo, $nombre);

        $mail->Subject = "Hemos recibido tu información - INTEXA";

        $mail->Body = '

            <div style="
                margin:0;
                padding:40px 20px;
                background:#0F172A;
                font-family:Arial,Helvetica,sans-serif;
            ">

            <table 
                width="100%" 
                cellspacing="0" 
                cellpadding="0"
                style="
                    max-width:650px;
                    margin:auto;
                    background:#121217;
                    border-collapse:collapse;
                ">

            <tr>

            <td 
                style="
                    padding:40px 40px 25px;
                    text-align:left;
                "
            >

            <img 
                src="https://intexacr.com/assets/img/logo-white.png"
                alt="INTEXA"
                style="
                    max-width:160px;
                    height:auto;
                "
            >

            </td>

            </tr>


            <tr>

            <td
            style="
                padding:0 40px;
            ">

            <div
            style="
                height:3px;
                background:#8B5CF6;
                width:60px;
                margin-bottom:35px;
            ">
            </div>


            <h1
            style="
                color:#ffffff;
                font-size:28px;
                font-weight:600;
                margin:0 0 20px;
                line-height:1.3;
            ">

            Hola ' . htmlspecialchars($nombre) . ',

            </h1>


            <p
            style="
                color:#A0AEC0;
                font-size:16px;
                line-height:1.8;
                margin-bottom:20px;
            ">

            Hemos recibido correctamente la información que nos enviaste mediante nuestro formulario de contacto.

            </p>


            <p
            style="
                color:#A0AEC0;
                font-size:16px;
                line-height:1.8;
                margin-bottom:35px;
            ">

            Nuestro equipo revisará tu solicitud y se pondrá en contacto contigo lo antes posible.

            </p>



            <table
            width="100%"
            cellspacing="0"
            cellpadding="0"
            style="
                background:#1E293B;
            ">

            <tr>

            <td
            style="
                padding:25px;
            ">

            <p
            style="
                margin:0;
                color:#ffffff;
                font-size:16px;
                line-height:1.6;
            ">

            Gracias por confiar en 
            <strong style="color:#8B5CF6;">
            INTEXA
            </strong>.

            </p>

            </td>

            </tr>

            </table>


            <p
            style="
                color:#64748B;
                font-size:13px;
                line-height:1.6;
                margin-top:35px;
            ">

            Este correo fue generado automáticamente, por favor no responder.

            </p>


            </td>

            </tr>



            <tr>

            <td
            style="
                padding:35px 40px;
                text-align:center;
                border-top:1px solid rgba(255,255,255,0.08);
            ">

            <p
            style="
                margin:0;
                color:#64748B;
                font-size:13px;
            ">

            © INTEXA - Tecnología que entiende tu día a día.

            </p>

            </td>

            </tr>


            </table>

            </div>';

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log($mail->ErrorInfo);
        return false;
    }
}

/**
 * Correo para administradores
 */
function enviarCorreoAdmin($nombre, $correo, $asunto, $mensaje)
{
    try {

        date_default_timezone_set('America/Costa_Rica');

        $mail = crearMailer();

        $mail->addAddress("info@intexacr.com");
        $mail->addAddress("quesadajeremy7@gmail.com");

        $mail->Subject = "Nuevo formulario de contacto recibido";

        $fecha = date("d/m/Y h:i:s A");

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'No disponible';

        $navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'No disponible';

        $mail->Body = '

        <div style="font-family:Arial,Helvetica,sans-serif;background:#f5f5f5;padding:30px;">

        <table style="max-width:750px;background:white;margin:auto;border-radius:8px;border-collapse:collapse;overflow:hidden;">

            <tr>

                <td colspan="2"
                style="
                background:#0F172A;
                color:white;
                padding:20px;
                text-align:center;
                font-size:24px;
                ">

                Nuevo formulario de contacto

                </td>

            </tr>

            <tr>
                <td style="padding:15px;font-weight:bold;width:220px;">Nombre</td>
                <td style="padding:15px;">' . htmlspecialchars($nombre) . '</td>
            </tr>

            <tr style="background:#F8FAFC;">
                <td style="padding:15px;font-weight:bold;">Correo</td>
                <td style="padding:15px;">' . htmlspecialchars($correo) . '</td>
            </tr>

            <tr>
                <td style="padding:15px;font-weight:bold;">Asunto</td>
                <td style="padding:15px;">' . htmlspecialchars($asunto) . '</td>
            </tr>

            <tr style="background:#F8FAFC;">
                <td style="padding:15px;font-weight:bold;">Mensaje</td>
                <td style="padding:15px;white-space:pre-line;">' . nl2br(htmlspecialchars($mensaje)) . '</td>
            </tr>

            <tr>
                <td style="padding:15px;font-weight:bold;">Fecha</td>
                <td style="padding:15px;">' . $fecha . '</td>
            </tr>

            <tr style="background:#F8FAFC;">
                <td style="padding:15px;font-weight:bold;">IP</td>
                <td style="padding:15px;">' . $ip . '</td>
            </tr>

            <tr>
                <td style="padding:15px;font-weight:bold;">Navegador</td>
                <td style="padding:15px;">' . htmlspecialchars($navegador) . '</td>
            </tr>

        </table>

        </div>';

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log($mail->ErrorInfo);
        return false;

    }
}