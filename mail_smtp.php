<?php

require_once "phpmailer/PHPMailerAutoload.php";

function smtpmailer($to, $to_name, $subject, $body) {
    $options["ssl"] = array(
      "verify_peer" => false,
      "verify_peer_name" => false,
      "allow_self_signed" => true,
    );

    $from_email = "ampangjrm@gmail.com";
    $from_pass = "jrmholistikampang#1234";

    $mail = new PHPMailer();

    $mail->SMTPDebug = 3;
    //Set PHPMailer to use SMTP.
    $mail->isSMTP();
    //Set SMTP host name
    // $mail->Host = "smtp.gmail.com";
    $mail->Host = 'smtp.gmail.com';
    //Set this to true if SMTP host requires authentication to send email
    $mail->SMTPAuth = true;
    //Provide username and password
    $mail->Username = $from_email;
    $mail->Password = $from_pass;
    //If SMTP requires TLS encryption then set it
    // $mail->SMTPSecure = "tls";
    // //Set TCP port to connect to
    // $mail->Port = 587;
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;
    $mail->smtpConnect($options);
    $mail->From = $from_email;
    $mail->FromName = "JRM Admin";
    $mail->addAddress($to, $to_name);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    // $mail->AltBody = "This is the plain text version of the email content";
    if(!$mail->send())
    {
    echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
    echo "Message has been sent successfully";
    }
}

 ?>
