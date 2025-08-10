<?php

class Order {

  public static function itemDetails($id){
    global $database;

    $sql = "SELECT * FROM order_details oprd
    RIGHT JOIN product prd ON oprd.product_id = prd.id
    WHERE oprd.id = '$id'";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      return $row;
    }

  }

  public static function itemDetailsByOrder($order_id){
    global $database;

    $sql = "SELECT *, oprd.id as id FROM order_details oprd
    RIGHT JOIN product prd ON oprd.product_id = prd.id
    WHERE oprd.order_id = '$order_id'";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc() ) {
        $row['img'] = "../uploads/product/".$row['product_img'];
        $row['price_unit'] = $row['rank_price'] > 0 ? $row['rank_price'] : $row['unit_price'];
        $product[] = $row;
      }
      return $product ?? [];
    }

  }

  public static function purchaseOrder($id, $need_products = false, $address_id = null, $method = null){
    global $database;

    if ($address_id) {
      $sql = "SELECT * FROM shipping_address WHERE id = '$address_id'";
      $result = mysqli_query($database, $sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);

      if ($row['type'] == 'shipping' || $row['type'] == null) {
        $full_address = "{$row['address_1']}, {$row['postcode']} {$row['city']}, {$row['state']}";
        $sql_update = "UPDATE purchase_order SET
          pickup_name = null,
          pickup_phone = null,
          pickup_datetime = null,
          shipping_to = '{$row['name']}',
          shipping_contact = '{$row['phone']}',
          address_1 = '{$row['address_1']}',
          poskod = '{$row['postcode']}',
          daerah = '{$row['city']}',
          negeri = '{$row['state']}',
          shipping_method = '$method',
          shipping_address = '$full_address',
          shipping_address_id = '$address_id'
          WHERE id = $id";
      }

      if ($row['type'] == 'pickup') {
        $sql_update = "UPDATE purchase_order SET
          pickup_name = '{$row['name']}',
          pickup_phone = '{$row['phone']}',
          pickup_datetime = '{$row['datetime_pickup']}',
          shipping_to = null,
          shipping_contact = null,
          address_1 = null,
          poskod = null,
          daerah = null,
          negeri = null,
          shipping_method = '$method',
          shipping_address = null,
          shipping_address_id = null
          WHERE id = $id";
      }


      mysqli_query($database, $sql_update);
    }

    $sql = "SELECT * FROM purchase_order WHERE id = '$id'";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $order = $row;
      $order['payment_date'] = date("d/m/Y H:iA", strtotime($row['paid_at']));
      // if ($order['payment_type'] == 'Bank Transfer') {
      //   $order['payment_date'] = $row['date_time_transfer'];
      // }
      $order['ship_address'] = $order['address_1'].", ".$order['poskod'].", ".$order['daerah'].", ".$order['negeri'];
      $order['shipping_email'] = $order['shipping_email'] ?? '-';
      if ($need_products) {
        $order['products'] = self::itemDetailsByOrder($id);
      }
      $order['user'] = User::get($row['user_id']);
      $order['credit_terms'] = $row['payment_type'] == 'Credit Terms' ? Credit::getCreditInfo($row['user_id'], $id): null;
      return $order;
    }

  }

  public static function purchaseOrderByRef($order_reference){
    global $database;

    $sql = "SELECT * FROM purchase_order WHERE order_reference = '$order_reference'";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $order['id'] = $row['id'];
      $order['order_reference'] = $row['order_reference'];
      $order['total_qty'] = $row['total_qty'];
      $order['total_weight'] = $row['total_weight'];
      $order['all_items_price'] = $row['all_items_price'];
      $order['user'] = User::get($row['user_id']);
      return $order;
    }

  }

  public static function approveManualOrder($value='')
  {
    // code...
  }

  public static function approveOnlineOrder($data)
  {
    global $database;
    $db = $database;

    $order_id = $data['order_id'];
    $sql = "SELECT * FROM purchase_order WHERE id = '$order_id'";
    $result = mysqli_query($db,$sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    if ($row['payment_status'] != 'Done') {
    // if (1) {
      $bill_id = $data['bill_id'];
      $amount = $data['amount'];
      $order = self::purchaseOrder($order_id);
      $user = $order['user'];

      $sql =  "UPDATE purchase_order SET
      payment_type = 'Online',
      paid_at = now(),
      payment_received = '$amount',
      billplz_id = '$bill_id',
      payment_status = 'Done',
      isApproved = now()
      WHERE id = '$order_id'";
      // echo "$sql";
      // exit;
      if (mysqli_query($db, $sql)) {
        // $user_id = $user['id'];
        // $to = $user['email'];
        // $nama = $user['nama'];
        // $no_tel = $user['no_tel'];
        // $web_type = $user['web_type'];
        //
        // $type = 'receipt_template';
        // $sql = "SELECT * FROM email_design where type = '$type' AND web_type = '$web_type'";
        // $result = $database->query($sql);
        // $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        // $email_subject = $row['subject'] . " [ORDER#$order_id]";
        //
        // $message = Email::mailWithTemplate($type, $order_id);
        // Email::smtpmailer($to, $nama, '', '', $email_subject, $message);
        // Notification::send(Helper::sanitizePhone($no_tel), $order_id, $type);


        $sql = "SELECT * FROM order_details WHERE order_id = $order_id";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc() ) {
          $quantity = $row['quantity'];
          $sql = "SELECT * FROM product WHERE id = ".$row['product_id'];
          $result_asd = $db->query($sql);
          while ($row_c = $result_asd->fetch_assoc() ) {
            Product::updateStock($row['product_id'], $order_id, $quantity);
          }
        }

      }else {
        echo "failed";
      }
      return 1;
    }
    return 1;
  }

  public static function updateCart($order_id)
  {
    global $database;
    $db = $database;
    $total_qty = $total_price = 0;
    $postage = $total_weight = 0;

    $sql = "SELECT * FROM order_details WHERE order_id = $order_id ";
    $result_order_details = $db->query($sql);
    if ($result_order_details->num_rows > 0) {
      while($row_x = $result_order_details->fetch_assoc()) {
        $total_qty += $row_x['quantity'];
        $total_price += $row_x['total_price'];
        $exist_weight = $row_x['total_weight'];
        $product_list[$row_x['id']]['total_price'] = $row_x['total_price'];
        $product_list[$row_x['id']]['quantity'] = $row_x['quantity'];
      }

      $sql_update_order = "UPDATE purchase_order SET
      all_items_price = $total_price,
      total_qty = $total_qty,
      total_weight = $total_weight,
      shipping_cost = $postage
      WHERE id = $order_id";
      if (mysqli_query($db, $sql_update_order) ) {
        return [
          'total_price' => $total_price,
          'total_qty' => $total_qty,
          'product_list' => $product_list,
        ];
      }else {
        // code...
        // return "x mantap";
      }
    }else {
      // code...
      // return "x product";
    }
  }

  public static function payWithCreditTerm($order_id)
  {
    global $database;

    $sql =  "UPDATE purchase_order SET
    payment_type = 'Credit Terms',
    paid_at = now(),
    payment_status = 'Done',
    isApproved = now()
    WHERE id = '$order_id'";
    // echo "$sql";
    // exit;
    if (mysqli_query($database, $sql)) {
      $sql = "SELECT * FROM order_details WHERE order_id = $order_id";
      $result = $database->query($sql);
      while ($row = $result->fetch_assoc() ) {
        $quantity = $row['quantity'];
        $sql = "SELECT * FROM product WHERE id = ".$row['product_id'];
        $result_asd = $database->query($sql);
        while ($row_c = $result_asd->fetch_assoc() ) {
          Product::updateStock($row['product_id'], $order_id, $quantity);
        }
      }
      return 1;
    }
    return 0;
  }

  public static function validateStockBeforePayment($order_id)
  {
    global $database;

    $sql = "SELECT od.product_id, od.quantity, p.stock_balance, p.name 
            FROM order_details od 
            JOIN product p ON od.product_id = p.id 
            WHERE od.order_id = $order_id";
    $result = $database->query($sql);
    $insufficient_stock = [];
    
    while ($row = $result->fetch_assoc()) {
      if ($row['quantity'] > $row['stock_balance']) {
        $insufficient_stock[] = [
          'product_name' => $row['name'],
          'requested' => $row['quantity'],
          'available' => $row['stock_balance']
        ];
      }
    }
    
    if (!empty($insufficient_stock)) {
      $error_message = "Insufficient stock for the following products:\n";
      foreach ($insufficient_stock as $item) {
        $error_message .= "- {$item['product_name']}: Requested {$item['requested']}, Available {$item['available']}\n";
      }
      return ['status' => 'error', 'message' => $error_message];
    }
    
    return ['status' => 'success'];
  }

  public static function updateCompleted($order_id)
  {
    global $database;

    $sql =  "UPDATE purchase_order SET
    isCompleted = now()
    WHERE id = '$order_id'";
    // echo "$sql";
    // exit;
    if (mysqli_query($database, $sql)) {
      return 1;
    }
    return 0;
  }
  public static function bulkUpdateCompleted($ids)
  {
    global $database;

    $sql =  "UPDATE purchase_order SET
    isCompleted = now()
    WHERE id IN ($ids)";
    // echo "$sql";
    // exit;
    if (mysqli_query($database, $sql)) {
      return 1;
    }
    return 0;
  }

}

?>
