<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database

 $user_id = $_GET['user_id'];
 $selected_id = $_GET['selected'];
 $selected_query = $_GET['selected'] ? "AND id = '{$_GET['selected']}' ":"";
 $pickup = isset($_GET['pickup']) && $_GET['pickup'] ? " AND type = 'pickup' " :"AND (type = 'shipping' OR type is null)" ;
 $addresses = [];
 $sql = "SELECT * FROM shipping_address WHERE user_id = '$user_id'  $selected_query $pickup ORDER BY id DESC LIMIT 5";

 $result = mysqli_query($db, $sql);
 while ($row = $result->fetch_assoc()) {
   $row['full_address'] = "{$row['address_1']}, {$row['postcode']} {$row['city']}, {$row['state']}";
   $row['checked'] = $selected_id == $row['id'];
   $addresses[] = $row;
 }

 echo json_encode([
   'addresses' => $addresses,
 ]);
 ?>
