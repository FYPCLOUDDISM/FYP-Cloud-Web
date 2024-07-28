<?php
date_default_timezone_set('Singapore');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; // Add this line to include the Exception class

require 'phpmailer/Exception.php'; // Require the PHPMailer Exception class
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0;                       // Disable verbose debug output
        $mail->isSMTP();                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';       // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                   // Enable SMTP authentication
        $mail->Username   = 'fypclouddism24@gmail.com'; // SMTP username
        $mail->Password   = 'ulipfflipzjqubaj';  // SMTP password or App Password
        $mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587;                    // TCP port to connect to

        // Recipients
        $mail->setFrom('fypclouddism24@gmail.com', 'FYP Antivirus Review');
        $mail->addAddress($to);                     // Add a recipient

        // Content
        $mail->isHTML(true);                        // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
