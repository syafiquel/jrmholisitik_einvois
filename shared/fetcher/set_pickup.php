<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database

 $id = $_POST['pickup_id'];
 $datetime_pickup = $_POST['datetime_pickup'];

 $sql = "UPDATE shipping_address SET datetime_pickup = '$datetime_pickup' WHERE id = '$id'";
 if (mysqli_query($db, $sql)) {
   echo json_encode([
     'status' => 1,
     'id' => $id,
   ]);
 }

 ?>
