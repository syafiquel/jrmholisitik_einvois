<?php

 /**
  *
  */
 class Helper
 {

   public static function MYR($amount)
   {
     return 'RM' . number_format($amount, 2);
   }

   public static function orderFormat($order_id)
   {
     return str_pad($order_id, 4 , '0', STR_PAD_LEFT);

   }

   public static function viewImage($model, $file_url)
   {
     return '';
   }

   public static function sanitizePhone($phone)
   {
     if ($phone) {
       $phone = str_replace(' ', '', $phone);
       $phone = preg_replace('/[^\p{L}\p{N}\s]/u', '', $phone);
       $firstCharacter = $phone[0];
       $firstCharacter_1 = $phone[1];
       $firstCharacter_2 = $phone[2];
       if (
          $firstCharacter == '6'
          && $firstCharacter_1 == '0'
          && $firstCharacter_2 == '0'
       ) {
         // echo "asd";
         // exit;
         $phone = preg_replace('/^600/', '60', $phone);

       }

       elseif (($firstCharacter != "6" || $firstCharacter != 6 && $firstCharacter == 0) ) {
        $phone = "6".$phone;
       }
       elseif (($firstCharacter != "6" || $firstCharacter != 6 && $firstCharacter == 1) ) {
        $phone = "60".$phone;
       }
       elseif ( $firstCharacter != '6' && $firstCharacter_1 != '0' ) {
         $phone = "60".$phone;

       }
     }
     return trim($phone);
   }

   public static function registrationSet($web_type = 'twe', $registration_for = 'daftar')
   {
     $allSimcard = [
       'twe' => [
         'daftar'=> [
            [
              "id" => 5
            ]
           ]
         ,
         'kekal_nombor_lama'=> [
            [
              "id" => 5
            ],
            [
              "id" => 16
            ]
         ]
       ],
       'twp' => [
         'daftar'=> [
            [
              "id" => 33
            ]
          ],
         'kekal_nombor_lama'=> [
            [
              "id" => 33
            ],
            [
              "id" => 35
            ]
          ],
       ],
     ];
     return $allSimcard[$web_type][$registration_for];

   }

   public static function getUserVerificationCode($length = 5)
   {
     global $database;

     $token = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, $length);
     $result = $database->query("SELECT * FROM user where verification_token = '$token'");
     $num_rows = mysqli_num_rows($result);
     // echo "<br>";
     // exit;
     if ($num_rows) {
       // echo "existed <br>";
       // exit;
       self::getUserVerificationCode();
     }else {
       // echo "new <br>";
       return $token;
     }
   }

   public static function lostPasswordSecet($length = 10)
   {
     global $database;

     $token = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, $length);
     $result = $database->query("SELECT * FROM user where lost_password_code = '$token'");
     $num_rows = mysqli_num_rows($result);
     // echo "<br>";
     // exit;
     if ($num_rows) {
       // echo "existed <br>";
       // exit;
       self::lostPasswordSecet();
     }else {
       // echo "new <br>";
       return $token;
     }
   }

   public static function getLastActiveDate($theDate)
   {
     if (!$theDate) {
       return date('Y-m-d');
     }

     if (date('Y-m-d') >= $theDate) {
       return date('Y-m-d');
     }else {
       return $theDate;
     }
   }

   public static function shortName($name)
   {
     return strtok($name, " ");
   }


 }


 ?>
