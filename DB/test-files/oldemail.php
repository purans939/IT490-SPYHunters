<?php

//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;
require '/home/ps1messaging/git/testDB/vendor/autoload.php';
require '/home/ps1messaging/git/testDB/vendor/src/SMTP.php';
require '/home/ps1messaging/git/testDB/vendor/src/PHPMailer.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // secure transfer enabled REQUIRED for Gmail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 465 TLS, 587 TLS
//$mail->IsHTML(true);
$mail->Username = "spyhunters490@gmail.com";
$mail->Password = "Terryb.24";
$mail->SetFrom("spyhunters490@gmail.com");
$mail->Subject = "Testing";
$mail->Body = 'testing';
//$mail->Body = $message;
$mail->AddAddress("spyhunters490@gmail.com", "SPYHunters");
//$mail->AddAttachment( $path , 'filename' );


$headers = "From: Sender\n";
$headers .= 'Content-Type:text/calendar; Content-Disposition: inline; charset=utf-8;\r\n';
$headers .= "Content-Type: text/plain;charset=\"utf-8\"\r\n"; #EDIT: TYPO

if (!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message has been sent";
}
