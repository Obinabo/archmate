<?php
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';
use phpmailer\phpmailer\PHPMailer;

function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = '';
    $mail->SMTPAuth = true;
    $mail->Username = '';
    $mail->Password = '';
    $mail->SMTPSecure = '';
    $mail->Port = 587;
    
    // Set email content and recipient(s)
    $mail->setFrom('', '');
    $mail->addAddress($recipient);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->Send();
}
?>