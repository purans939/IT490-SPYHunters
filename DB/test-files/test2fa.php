<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require '/home/ps1messaging/git/testDB/vendor/autoload.php';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->SMTPAuth = true;
$mail->Username = 'spyhunters490@gmail.com';
$mail->Password = 'gxjtdmttmxpypfkw';
$mail->setFrom('spyhunters490@gmail.com', 'SPY Hunters');
$mail->addAddress('puraanshievkumar@gmail.com', 'Test Test');
$mail->Subject = 'PHPMailer GMail SMTP test';
$mail->Body = 'This is a plain-text message body';

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
	}



