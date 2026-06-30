<?php
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';
use phpmailer\phpmailer\PHPMailer;

function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'mail.archmateestatesanh.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'support@archmateestatesanh.com';
    $mail->Password = 'Archmate_2024';
    $mail->SMTPSecure = '';
    $mail->Port = 587;
    
    // Set email content and recipient(s)
    $mail->setFrom('support@archmateestatesanh.com', 'Archmate Group');
    $mail->addAddress($recipient);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->Send();
}
?>