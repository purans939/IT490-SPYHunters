<?php
$to      = 'puraanshievkumar@gmail.com';
$subject = 'the subject';
$message = 'hello';
$headers = array(
    'From' => 'spyhunters490@gmail.com',
    'Reply-To' => 'spyhunters490@gmail.com',
    'X-Mailer' => 'PHP/' . phpversion()
);

mail($to, $subject, $message, $headers);
?>

