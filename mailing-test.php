<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo 'relax bos<br>';

include 'mail_smtp.php';

smtpmailer('syazwanasman@gmail.com','Syazwan','Suasana Hening', '<h1>Subuh itu indah</h1>');
 exit;
require_once "phpmailer/PHPMailerAutoload.php";

//Create a new PHPMailer instance
$mail = new PHPMailer();

$mail->IsSMTP();
$mail->SMTPDebug = 4;
$mail->Debugoutput = 'html';

$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Host = "smtp-relay.brevo.com";

$mail->Port = 587;
$mail->IsHTML(true);
//Username to use for SMTP authentication
$mail->Username = "7b5d22001@smtp-brevo.com";
$mail->Password = "wPrqLkTaFI0CpVDd";
//Set who the message is to be sent from
$mail->setFrom('devsyazwan@gmail.com', 'devsyz');
//Set an alternative reply-to address
$mail->addReplyTo('replyto@gmail.com', 'Secure Developer');
//Set who the message is to be sent to
$mail->addAddress('syazwanasman@gmail.com', 'Syazwan Asman');
//Set the subject line
$mail->Subject = 'PHPMailer SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML("convert HTML into a basic plain-text alternative body");
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//send the message, check for errors
if (!$mail->send()) {
    echo "<br>Mailer Error: " . $mail->ErrorInfo;
} else {

  echo "Message sent!";
     }

 ?>
