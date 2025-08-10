<?php

class Product {

  public static function all(){
    global $database;

    $result = $database->query("SELECT * FROM product");
    while ($row = $result->fetch_assoc() ) {
      $row['img'] = "../uploads/product/".$row['product_img'];
      $product[] = $row;

    }
    return $product ?? [];
  }

  public static function getName($id){
    global $database;

    $result = $database->query("SELECT name FROM product WHERE id = $id");
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $name = $row["name"];

    return !empty($name) ? $name : "Not assigned" ;
  }

  public static function updateInventory($order_id, $set_id, $qty)
  {
    global $database;

    $sql = "SELECT * FROM product_set_details WHERE set_id = $set_id";
    $result = $database->query($sql);
    while ($row = $result->fetch_assoc() ) {
      $product_id = $row['product_id'];
      self::deductStock($order_id, $set_id, $product_id, $qty);
    }
    return true;

  }

  public static function deductStock($order_id, $set_id, $product_id, $qty, $user_id = 0)
  {
    global $database;

    // insert log
    // update latest stock balance with deduct the old

    $sql_insert_log = "INSERT INTO product_stock_log ( product_id, set_id, order_id, user_id, stock_out, purchased_at )
                        VALUES ('$product_id', '$set_id', '$order_id', '$user_id', '$qty', now())";
    // echo $sql_insert_log;
    // exit;
    mysqli_query($database, $sql_insert_log);

    $sql = "UPDATE product SET stock_balance = stock_balance - $qty WHERE product_id = $product_id";
    mysqli_query($database, $sql);
    // return true;
  }

  public static function updateStock($product_id, $order_id, $quantity)
  {
    global $database;

    $sql_insert_log = "INSERT INTO product_stock_log ( product_id, order_id, stock_out, purchased_at )
    VALUES ('$product_id', '$order_id', '$quantity', now())";
    mysqli_query($database, $sql_insert_log);

    $sql = "UPDATE product SET stock_balance = stock_balance - $quantity WHERE id = $product_id";
    mysqli_query($database, $sql);
  }

  public static function deductRegistrationSet($user_id, $web_type, $registration_for)
  {
    $getProduct = Helper::registrationSet($web_type, $registration_for);

    foreach ($getProduct as $key => $product) {
      self::deductStock(0, 0, $product['id'], 1, $user_id);
    }

  }

  public static function updateRankPrice($rank_prices)
  {
    global $database;

    if (sizeof($rank_prices)) {

      foreach ($rank_prices as $product_id => $rank_price) {
        foreach ($rank_price as $rank_id => $price) {
          $sql = "DELETE FROM product_rank_price WHERE product_id = '$product_id' AND rank_id = '$rank_id'";
          mysqli_query($database, $sql);

          $sql = "INSERT INTO product_rank_price ( product_id, rank_id, price, created_at )
          VALUES ('$product_id', '$rank_id', '$price', now())";
          mysqli_query($database, $sql);
        }
      }
    }
  }

  public static function insertRankPrice($id, $rank_prices)
  {
    global $database;

    if (sizeof($rank_prices)) {

      foreach ($rank_prices as $rank_id => $price) {
        $sql = "INSERT INTO product_rank_price ( product_id, rank_id, price, created_at )
        VALUES ('$id', '$rank_id', '$price', now())";
        mysqli_query($database, $sql);
      }
    }
  }

  public static function productRankPrice($rank_id, $product_id = null)
  {
    global $database;
    $product_clause = $product_id ? "AND product_id = '$product_id'" : '';
    $sqlPrice = "SELECT * FROM product_rank_price WHERE rank_id = {$rank_id} $product_clause";
    $result_price = $database->query($sqlPrice);
    $rank_price = [];
    while($rowx = $result_price->fetch_assoc()) {
      $rank_price[$rowx['product_id']] = $rowx['price'];
    }

    return $rank_price;

  }

}

?>
