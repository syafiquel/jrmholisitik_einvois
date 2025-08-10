<?php
// define('ELASTIC_MAIL_USERNAME', 'app@jombiz.vip');
// define('ELASTIC_MAIL_API_KEY', 'EA8E1A54EFA00E6A9C72C600A41DECBCFFF9');

require_once "phpmailer/PHPMailerAutoload.php";

function smtpmailer($to, $to_name, $subject, $body) {
    $mail = new PHPMailer(); // create a new object
    // $mail->IsSMTP(true); // enable SMTP
    // $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    // $mail->SMTPAuth = true; // authentication enabled
    // $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
    // $mail->Host = "smtp-relay.brevo.com";
    // $mail->Port = 587; // or 587
    // $mail->Username = '7b5d22001@smtp-brevo.com';
    // $mail->Password = 'wPrqLkTaFI0CpVDd';
    // $mail->Subject = $subject;
    // $mail->IsHTML(true);
    // $mail->Body = $body;
    // $mail->AltBody = strip_tags($body);
    // $mail->SetFrom('help@jrmholistikampang.com', 'JRM Holistik Ampang');
    // $mail->addAddress($to, $to_name);
    //
    // //  if(!$mail->Send()) {
    // //     echo "Mailer Error: " . $mail->ErrorInfo;
    // //  } else {
    // //     echo "Message has been sent";
    // //  }
    //
    // //Attach an image file
    // // $mail->addAttachment('images/phpmailer_mini.png');
    // //send the message, check for errors
    // if (!$mail->Send()) {
    //   echo "Mailer Error: " . $mail->ErrorInfo;
    //     return false;
    // } else {
    //   return true;
    //     echo "Message sent!";
    //     //Section 2: IMAP
    //     //Uncomment these to save your message in the 'Sent Mail' folder.
    //     // if (save_mail($mail)) {
    //     //     echo "Message saved!";
    //     // }
    // }

    try {
      // Server settings
      $mail->SMTPDebug = 2; // Set to 2 for detailed debug output
      $mail->isSMTP();
      $mail->Host = 'smtp-relay.brevo.com'; // Brevo SMTP server
      $mail->SMTPAuth = true;
      $mail->Username = '7b5d22001@smtp-brevo.com'; // Your Brevo email
      $mail->Password = 'wPrqLkTaFI0CpVDd'; // Brevo SMTP API key
      $mail->SMTPSecure = 'tls'; // Use 'ssl' if port is 465
      $mail->Port = 587; // Port 587 for TLS, or 465 for SSL

      // Sender and recipient settings
      $mail->SetFrom('help@jrmholistikampang.com', 'JRM Holistik Ampang');
      $mail->addAddress($to, $to_name);

      // Email content
      $mail->isHTML(true); // Set email format to HTML
      $mail->Subject = $subject;
      $mail->Body    = $body;
      $mail->AltBody = strip_tags($body);


      // Send the email
      // $mail->send();
      // echo 'Email sent successfully!';
      if (!$mail->Send()) {
       echo "Mailer Error: " . $mail->ErrorInfo;
         return false;
     }
    } catch (Exception $e) {
        echo "Email could not be sent. PHPMailer Error: {$mail->ErrorInfo}";
    }

}

 ?>
