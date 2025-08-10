<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database
include '../../init.php';

if(!empty($_POST["user_id"]) && !empty($_POST["op_id"]) && !empty($_POST["new_qty"]) ){
  $user_id = $_POST["user_id"];
  $order_product_id = $_POST["op_id"];
  $new_qty = $_POST["new_qty"];

  $sql = "SELECT * FROM order_details WHERE id = $order_product_id ";
  $result_order_details = $db->query($sql);
  if ($result_order_details->num_rows > 0) {
    // if product is exist.

    while($row_x = $result_order_details->fetch_assoc()) {
      $order_id = $row_x['order_id'];
      $order_details_id = $row_x['id'];
      $exist_qty = $row_x['quantity'];
      $exist_price = $row_x['total_price'];
      $exist_weight = $row_x['total_weight'];
      $product_id = $row_x['product_id'];

      // recalculate order details
      // $new_qty = $exist_qty + $qty;
      // $new_price = $exist_price + $product_price;

      $new_price = $new_qty * $row_x['rank_price'];

      // Check stock
     $sql_stock = "SELECT stock_balance FROM product WHERE id = $product_id";
     $result_stock = $db->query($sql_stock);
     $row_stock = $result_stock->fetch_assoc();
     $available_stock = $row_stock['stock_balance'];

     if ($new_qty > $available_stock) {
       $data['type'] = 'over_stock';
       $data['status'] = 'error';
       $data['title'] = 'Product stock balance is not sufficient! Product set to maximum quantity';
       $data['available_stock'] = $available_stock;
       echo json_encode($data);
       exit;
     }

      $sql_update_order_details = "UPDATE order_details SET quantity = $new_qty, total_price = $new_price WHERE id = $order_details_id";

      // echo $sql_update_order_details;
      // echo "<hr>";
      // echo $sql_update_order;
      // exit;
      if ( mysqli_query($db, $sql_update_order_details)  ) {
        $data['update_cart'] = Order::updateCart($order_id);
        $data['new_price'] = $new_price;
        $data['status'] = 'success';
        $data['title'] = 'Nice! Cart Updated.';
      }else{
        $data['status'] = 'error';
        $data['title'] = 'Ops! Something went wrong. Please try again.';
      }

      echo json_encode($data);
      // $postage = getPostage($new_weight, $negeri);

      // $data['status'] = 'success';
      // $data['title'] = 'Nice! Cart Updated.';
    }
  }
}
// exit;

 ?>
