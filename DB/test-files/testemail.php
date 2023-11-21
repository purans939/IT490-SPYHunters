<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
//Alias the League Google OAuth2 provider class
use League\OAuth2\Client\Provider\Google;

//date_default_timezone_set('Etc/EST');

require '/home/ps1messaging/git/testDB/vendor/autoload.php';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Host = 'smtp.gmail.com';

//Set the SMTP port number:
// - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
// - 587 for SMTP+STARTTLS
$mail->Port = 465;

//Set the encryption mechanism to use:
// - SMTPS (implicit TLS on port 465) or
// - STARTTLS (explicit TLS on port 587)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

$mail->SMTPAuth = true;
$mail->AuthType = 'XOAUTH2';

//Start Option 1: Use league/oauth2-client as OAuth2 token provider
//Fill in authentication details here
//Either the gmail account owner, or the user that gave consent
$email = 'spyhunters490@gmail.com';
$clientId = '321362664275-6vncankc4pu4r13ukak4pv7hknmcrh1b.apps.googleusercontent.com';
$clientSecret = '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';

//Obtained by configuring and running get_oauth_token.php
//after setting up an app in Google Developer Console.
$refreshToken = 'AIzaSyApqypdD_9DKXwSU-otQ6Lz2wIDW70Ww-g';

//Create a new OAuth2 provider instance
$provider = new Google(
    [
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
    ]
);

//Pass the OAuth provider instance to PHPMailer
$mail->setOAuth(
    new OAuth(
        [
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $email,
        ]
    )
);
//End Option 1

//Option 2: Another OAuth library as OAuth2 token provider
//Set up the other oauth library as per its documentation
//Then create the wrapper class that implements OAuthTokenProvider
//$oauthTokenProvider = new MyOAuthTokenProvider(/* Email, ClientId, ClientSecret, etc. */);

//Pass the implementation of OAuthTokenProvider to PHPMailer
//$mail->setOAuth($oauthTokenProvider);
//End Option 2

//Set who the message is to be sent from
//For gmail, this generally needs to be the same as the user you logged in as
$mail->setFrom($email, 'First Last');

//Set who the message is to be sent to
$mail->addAddress('spyhunters490@gmail.com', 'John Doe');

//Set the subject line
$mail->Subject = 'PHPMailer GMail XOAUTH2 SMTP test';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->CharSet = PHPMailer::CHARSET_UTF8;
$mail->msgHTML(file_get_contents('contentsutf8.html'), __DIR__);

//Replace the plain text body with one created manually
//$mail->AltBody = 'This is a plain-text message body';

//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
}

