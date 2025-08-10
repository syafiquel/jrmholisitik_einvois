<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database
include '../../init.php';

if( !empty($_POST["user_id"]) ){
  // 1. get product details
  // 2. get order if dont have create order.
  // 3. insert or update the order details

  $user_id = $_POST["user_id"];
  $inside_cart = 0;
  $sql = "SELECT * FROM offline_buyer WHERE no_idp = $user_id LIMIT 1";
  $result = $db->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      $user['status'] = 1;
      $user['id'] = $row['id'];
      $user['nama'] = $row['nama'];
      $user['email'] = $row['email'];
      $user['no_tel'] = $row['no_tel'];
      $user['idp'] = $row['no_idp'];
    }
  }else {
    $user['status'] = 0;

  }
  echo json_encode($user);
}
?>
