<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database
include '../../init.php';

if( !empty($_POST["user_id"]) ){
  // 1. get product details
  // 2. get order if dont have create order.
  // 3. insert or update the order details

  $user_id = $_POST["user_id"];
  $inside_cart = 0;
  $sql = "SELECT * FROM purchase_order WHERE user_id = $user_id AND payment_status IS NULL AND resit_img IS NULL AND date_time_transfer IS NULL ORDER BY created_at DESC LIMIT 1";
  $result = $db->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $got_order = $row['id'];
      $got_address = $row["shipping_address"];
      $got_payment = $row["payment_status"];
      $sql = mysqli_query($db, "SELECT COUNT(*) as inside_cart FROM order_details WHERE order_id = $got_order");
      $row_ic = mysqli_fetch_array($sql,MYSQLI_ASSOC);
      $inside_cart = $row_ic['inside_cart'];
      $total_qty = $row['total_qty'];
      $total_price = $row['all_items_price'];
    }
  }
  echo $inside_cart;
  exit;
  echo json_encode([
'inside_cart' => $inside_cart,
'total_qty' => $total_qty,
'total_price' => $total_price,
  ]);
}
?>
