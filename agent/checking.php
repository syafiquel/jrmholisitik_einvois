<?php
include '../init.php';
$role[] = "admin";
$role[] = "agent";
// $role[] = "superadmin";
// $role[] = "user";

$main_orientation = 'horizontal';

require "../db_con/check.php";
require "../db_con/connection.php";

$user_id = $_SESSION['user_id'];
$my_name = $_SESSION['nama'];

$user = User::getUser($user_id);
var_dump($user);
exit;
$credit_main_setting =  Credit::getSetting();
$credit_available =  Credit::available($user_id);
$user['credit_limit'] = $user['credit_limit'] > 0 ? $user['credit_limit'] : $credit_main_setting['limit_total_price'];
$credit_available = $user['credit_balance'] = $user['credit_limit'] - $user['credit_used'];
// echo $user['credit_balance'];
// exit;
// var_dump($user);
// exit;

//------------------------------------------------------------------------------//
// include '../shared/notificationCounter.php';
//------------------------------------------------------------------------------//

$got_order = $got_address = $got_payment = null;
$inside_cart = 0;
$sql = "SELECT * FROM purchase_order WHERE user_id = $user_id AND payment_status IS NULL AND resit_img IS NULL AND date_time_transfer IS NULL ORDER BY created_at DESC LIMIT 1";
$result = $db->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $got_order = $row['id'];
    $got_address = $row["shipping_address"] ? : $row['isWalkIn'];
    $got_payment = $row["payment_status"];
    $sql = mysqli_query($db, "SELECT COUNT(*) as inside_cart FROM order_details WHERE order_id = $got_order");
    $row_ic = mysqli_fetch_array($sql,MYSQLI_ASSOC);
    $inside_cart = $row_ic['inside_cart'];
    // if (!$inside_cart) {
    //   header("Location: shop.php");
    //   exit;
    // }
  }
}


$rank_price = Product::productRankPrice($user['rank_id']);

 $products = [];

 ?>
