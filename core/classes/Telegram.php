<?php

 class Telegram
 {

   public static function send($method, $data)
   {
     global $telegram_token;

      $url = "https://api.telegram.org/bot$telegram_token". "/" . $method;

      if (!$curld = curl_init()) {
          exit;
      }

      error_log($url);
      curl_setopt($curld, CURLOPT_POST, true);
      curl_setopt($curld, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curld, CURLOPT_URL, $url);
      curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
      $output = curl_exec($curld);
      curl_close($curld);
      return $output;
   }

   public static function sendMessage($chatID, $messaggio) {
    // echo "sending message to " . $chatID . "\n";
    // global $telegram_token;

    $telegram_token = '1893885063:AAF8Xaf1bXuGTzEFSYw7OXZWTRy4od4n70I';

    $url = "https://api.telegram.org/bot" . $telegram_token . "/sendMessage?chat_id=" . $chatID;

    $url = $url . "&text=" . urlencode($messaggio);
    $url = $url . "&parse_mode=html";
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }


 }

 ?>
