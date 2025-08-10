<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database
include '../../init.php';

$currentBusiness = Business::currentBusiness();
$currentBizFull = Business::getBusiness($currentBusiness['id']);

function getPostage($total_weight, $negeri)
{
  if ($total_weight > 0) {
    // code...
    // echo "$negeri";
    if ($negeri == 'SABAH' || $negeri == 'SARAWAK' || $negeri == 'LABUAN') {
      if ($total_weight >= 0.001 && $total_weight <= 0.500 ) {
        $postage = 18;
      }elseif ($total_weight >= 0.501 && $total_weight <= 0.750 ) {
        $postage = 23;
      }elseif ($total_weight >= 0.751 && $total_weight <= 1.000 ) {
        $postage = 23;
      }elseif ($total_weight >= 1.001 && $total_weight <= 1.500 ) {
        $postage = 33;
      }elseif ($total_weight >= 1.501 && $total_weight <= 2.000 ) {
        $postage = 33;
      }elseif ($total_weight >= 2.001 && $total_weight <= 3.000 ) {
        $postage = 55;
      }elseif ($total_weight >= 3.001 && $total_weight <= 4.500 ) {
        $postage = 73;
      }elseif ($total_weight >= 4.501 && $total_weight <= 6.000 ) {
        $postage = 93;
      }elseif ($total_weight >= 6.001 && $total_weight <= 7.500 ) {
        $postage = 113;
      }elseif ($total_weight >= 7.501 && $total_weight <= 9.000 ) {
        $postage = 133;
      }else {
        $postage = 0;
      }

    }else {
      if ($total_weight >= 0.001 && $total_weight <= 0.500 ) {
        $postage = 10;
      }elseif ($total_weight >= 0.501 && $total_weight <= 1.500 ) {
        $postage = 15;
      }elseif ($total_weight >= 1.501 && $total_weight <= 3.000 ) {
        $postage = 20;
      }elseif ($total_weight >= 3.001 && $total_weight <= 4.500 ) {
        $postage = 25;
      }elseif ($total_weight >= 4.501 && $total_weight <= 6.000 ) {
        $postage = 30;
      }elseif ($total_weight >= 6.001 && $total_weight <= 7.500 ) {
        $postage = 35;
      }elseif ($total_weight >= 7.501 && $total_weight <= 9.000 ) {
        $postage = 40;
      }else {
        $postage = 0;

      }
    }
  }else {
    $postage = 0;
  }
  return $postage;
}

function getPostageOld($total_weight, $negeri)
{
  if ($total_weight > 0) {
    // code...
    if ($negeri == 'SABAH' || $negeri == 'SARAWAK' || $negeri == 'LABUAN') {
      if ($total_weight >= 0.01 && $total_weight <= 0.55 ) {
        $postage = 15;
      }elseif ($total_weight >= 0.551 && $total_weight <= 0.750 ) {
        $postage = 20;
      }elseif ($total_weight >= 0.751 && $total_weight <= 1.250 ) {
        $postage = 25;
      }elseif ($total_weight >= 1.251 && $total_weight <= 1.750 ) {
        $postage = 30;
      }elseif ($total_weight >= 1.751 && $total_weight <= 2.000 ) {
        $postage = 35;
      }elseif ($total_weight >= 2.001 && $total_weight <= 2.500 ) {
        $postage = 50;
      }elseif ($total_weight >= 2.501 && $total_weight <= 3.000 ) {
        $postage = 55;
      }elseif ($total_weight >= 3.001 && $total_weight <= 4.750 ) {
        $postage = 75;
      }elseif ($total_weight >= 4.751 && $total_weight <= 6.000 ) {
        $postage = 90;
      }elseif ($total_weight >= 6.001 && $total_weight <= 7.750 ) {
        $postage = 105;
      }elseif ($total_weight >= 7.751 && $total_weight <= 9.000 ) {
        $postage = 120;
      }elseif( $total_weight >= 9.001 && $total_weight <= 10.750){
        $postage = 135;
      }elseif( $total_weight >= 10.751 && $total_weight <=  12.000){
        $postage = 150;
      }elseif( $total_weight >= 12.001 && $total_weight <=  13.750){
        $postage = 165;
      }elseif( $total_weight >= 13.751 && $total_weight <=  15.000){
        $postage = 180;
      }elseif( $total_weight >= 15.001 && $total_weight <=  16.750){
        $postage = 195;
      }elseif( $total_weight >= 16.751 && $total_weight <=  18.000){
        $postage = 210;
      }elseif( $total_weight >= 18.001 && $total_weight <=  19.750){
        $postage = 225;
      }elseif( $total_weight >= 19.751 && $total_weight <=  21.000){
        $postage = 240;
      }

    }else {
      if ($total_weight >= 0.01 && $total_weight <= 0.55 ) {
        $postage = 10;
      }elseif ($total_weight >= 0.551 && $total_weight <= 1.750 ) {
        $postage = 15;
      }elseif ($total_weight >= 1.751 && $total_weight <= 3.000 ) {
        $postage = 20;
      }elseif ($total_weight >= 3.000 && $total_weight <= 4.750 ) {
        $postage = 25;
      }elseif ($total_weight >= 4.751 && $total_weight <= 6.000 ) {
        $postage = 30;
      }elseif ($total_weight >= 6.001 && $total_weight <= 7.750 ) {
        $postage = 35;
      }elseif ($total_weight >= 7.751 && $total_weight <= 9.000 ) {
        $postage = 40;
      }elseif ($total_weight >= 9.001 && $total_weight < 10.750) {
        $postage = 45;
      }elseif ($total_weight >= 10.751 && $total_weight <= 12.000) {
        $postage = 50;
      }elseif ($total_weight >= 12.001 && $total_weight <= 13.750) {
        $postage = 55;
      }elseif ($total_weight >= 13.751 && $total_weight <= 15.000) {
        $postage = 60;
      }elseif ($total_weight >= 15.001 && $total_weight <= 16.750) {
        $postage = 65;
      }elseif ($total_weight >= 16.751 && $total_weight <= 18.000) {
        $postage = 70;
      }elseif ($total_weight >= 18.001 && $total_weight <= 19.750) {
        $postage = 75;
      }elseif ($total_weight >= 19.751 && $total_weight <= 21.000) {
        $postage = 80;
      }
    }
  }else {
    $postage = 0;
  }
  return $postage;
}
$weightLimit = 9.001;
// var_dump($_POST);
if(!empty($_POST["user_id"]) && !empty($_POST["product_id"]) && !empty($_POST["qty"]) ){
  // 1. get product details
  // 2. get order if dont have create order.
  // 3. insert or update the order details
  $current_user_id = $currentBusiness['user_id'];
  $current_biz_id = $currentBusiness['business_id'];
  $current_user_rank = $currentBusiness['rank_id'];
  $user_id = $_POST["user_id"];
  $product_id = $_POST["product_id"];
  $qty = $_POST["qty"];

  $sql = mysqli_query($db,"SELECT * FROM product_set WHERE id = $product_id");
  $row = mysqli_fetch_array($sql,MYSQLI_ASSOC);
  $product_name = $row["name"];
  $product_id = $row["id"];
  $price = $row["price"];
  $weight = $row["weight_unit"];
  if ($currentBizFull['enable_rank'] && $currentBusiness['status'] == 'approved') {
    $sql = "SELECT * FROM business_product_rank_price WHERE business_id = '$current_biz_id' AND rank_id = '$current_user_rank' AND product_set_id = '$product_id' ";
    $result = $database->query($sql);
    while ($rowp = $result->fetch_assoc()) {
      $price = $rowp['price'];
    }

  }

  $sql = "SELECT isWalkIn FROM purchase_order WHERE user_id = $user_id AND ( payment_status IS NULL OR payment_status <> 'Done' ) AND date_time_transfer IS NULL ORDER BY created_at DESC LIMIT 1";
  $result = mysqli_query($db,$sql);
  $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  $isWalkIn = $row['isWalkIn'];
  if ($isWalkIn) {
    $weightLimit = 9999999;
  }

  // $sub_total_qty =  $product_unit_qty * $qty;
  $sub_total_qty = $qty;
  $product_price = $price * $qty;
  $sub_total_weight = $weight * $qty;
  if ($sub_total_weight >= $weightLimit ) {
    $data['status'] = 'error';
    $data['title'] = 'Ops! You have exceeded weight limit. Please make a new order. Weight limit per order is 9 Kg.';

  }else {
    $sql = "SELECT * FROM purchase_order WHERE user_id = $user_id AND ( payment_status IS NULL OR payment_status <> 'Done' ) AND date_time_transfer IS NULL ORDER BY created_at DESC LIMIT 1";
    // echo $sql;
    // exit;
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
      // if purchase order is exist.
      // echo "found a open cart";
      while($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        $total_existed_qty = $row['total_qty'];
        $total_existed_weight = $row['total_weight'];
        $negeri = $row['negeri'];

        $sql = "SELECT * FROM order_details WHERE order_id = $order_id AND product_id = $product_id LIMIT 1";
        // exit;
        $result_order_details = $db->query($sql);
        if ($result_order_details->num_rows > 0) {
          // if product is exist.

          while($row_x = $result_order_details->fetch_assoc()) {
            $order_details_id = $row_x['id'];
            $exist_qty = $row_x['quantity'];
            $exist_price = $row_x['total_price'];
            $exist_weight = $row_x['total_weight'];

            // recalculate order details
            $new_qty = $exist_qty + $qty;
            $new_weight = $exist_weight + $sub_total_weight;
            $new_price = $exist_price + $product_price;

            // recalculate po qty and shiping cost and total_price
            $new_all_qty = $total_existed_qty + $qty;
            $new_all_weight = $total_existed_weight + $sub_total_weight;
            if ($new_all_weight >= $weightLimit) {
              $data['status'] = 'error';
              $data['title'] = 'Ops! You have exceeded weight limit. Please make a new order. Weight limit per order is 9 Kg.';

            }else {
              // code...
              $postage = 0;
              if (!$isWalkIn && $negeri) {
                // code...
                $postage = getPostage($new_all_weight, strtoupper($negeri));
                // $postage = getPostage($new_all_weight, $negeri);
              }

              $sql_update_order_details = "UPDATE order_details SET quantity = $new_qty, total_price = $new_price WHERE id = $order_details_id";
              $sql_update_order = "UPDATE purchase_order SET
              all_items_price = all_items_price + $product_price,
              total_qty = total_qty + $qty,
              total_weight = total_weight + $sub_total_weight,
              shipping_cost = $postage
              WHERE id = $order_id";
              // echo $sql_update_order_details;
              // echo "<hr>";
              // echo $sql_update_order;
              // exit;
              if ( mysqli_query($db, $sql_update_order_details) && mysqli_query($db, $sql_update_order) ) {
                $data['status'] = 'success';
                $data['title'] = 'Nice! Cart Updated.';
              }else{
                $data['status'] = 'error';
                $data['title'] = 'Ops! Something went wrong. Please try again.';
              }
            }
            // $postage = getPostage($new_weight, $negeri);

            // $data['status'] = 'success';
            // $data['title'] = 'Nice! Cart Updated.';
          }
        }else {
          // if product is not exist yet.
          $new_all_qty = $total_existed_qty + $qty;
          $new_all_weight = $total_existed_weight + $sub_total_weight;
          // $postage = getPostage($new_all_qty);
          if ($new_all_weight >= $weightLimit) {
            $data['status'] = 'error';
            $data['title'] = 'Ops! You have exceeded weight limit. Please make a new order. Weight limit per order is 9 Kg.';
          }else {
            $postage = 0;

            if (!$isWalkIn && $negeri) {
              $postage = getPostage($new_all_weight, strtoupper($negeri));
              // $postage = getPostage($new_all_weight, $negeri);
            }

            $sql_order_details =  "INSERT INTO order_details (order_id, product_id, quantity, unit_price, total_price, total_weight, created_at)
            VALUES ('$order_id', '$product_id', '$qty', '$price', '$product_price', '$sub_total_weight', now())";

            $sql_update_order = "UPDATE purchase_order SET
            all_items_price = all_items_price + $product_price,
            total_qty = total_qty + $qty,
            total_weight = total_weight + $sub_total_weight,
            shipping_cost = $postage
            WHERE id = $order_id";

            // echo $sql_order_details;
            // echo "<hr>";
            // echo $sql_update_order;
            // exit;
            if (mysqli_query($db, $sql_order_details) && mysqli_query($db, $sql_update_order) ){
              $data['status'] = 'success';
              $data['title'] = 'Nice! Added to Cart.';
            }else {
              echo("Error description: " . $db -> error);
              $data['status'] = 'error';
              $data['title'] = 'Ops! Something went wrong. Please try again.';

            }
          }
        }
      }
    }else {
      // if purchase order is not exist yet.

      // echo "need to create new cart";
      // exit;
      $total_qty = $sub_total_qty;
      $total_weight = $sub_total_weight;
      // $postage = getPostage($total_weight, $negeri);
      $postage = 0;
      $total_price = $product_price + $postage;

      $sql = "INSERT INTO purchase_order (user_id, created_at, all_items_price, total_qty, shipping_cost, total_weight, payment_status ) VALUES ('$user_id', now(), '$product_price', '$total_qty', '$postage' , '$total_weight', NULL )";
      // echo $sql;
      // exit;
      if( mysqli_query($db, $sql)){
        $order_id  = mysqli_insert_id($db);
        $sql_details =  "INSERT INTO order_details (order_id, product_id, quantity, unit_price, total_price, created_at, total_weight)
        VALUES ('$order_id', '$product_id', '$qty', '$price', '$product_price', now(), '$total_weight' )";
        // echo $sql_details;
        // exit;
        if (mysqli_query($db, $sql_details)) {
          $data['status'] = 'success';
          $data['title'] = 'Nice! Added to Cart.';
        }else {
          $data['status'] = 'error';
          $data['title'] = 'Ops! Something went wrong. Please try again.';
        }
      }else {
        echo("Error description: " . $db -> error);
        $data['status'] = 'error';
        $data['title'] = 'Ops! Something went wrong. Please try again.';
      }
    }
  }

  echo json_encode($data);


//   $result = mysqli_query($db, "SELECT * FROM user WHERE email = '$email'");
//   $num_rows = mysqli_num_rows($result);
//    if( $num_rows == 0 ) {
//      echo 0;
//    }else{
//      echo 1;
//    }
// }else{
//   echo "<i class='fa fa-question' style='color:yellow'></i>";
}
?>
