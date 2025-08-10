<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database

 if (
   isset($_POST['name'])
   && isset($_POST['type'])
   && $_POST['type'] == 'shipping'
   && isset($_POST['no_tel'])
   && isset($_POST['address_1'])
   && isset($_POST['postcode'])
   && isset($_POST['city'])
   && isset($_POST['state'])
   && isset($_POST['user_id'])
  ) {
    $name = $_POST['name'];
    $phone = $_POST['no_tel'];
    $email = $_POST['email'];
    $address_1 = $_POST['address_1'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $user_id = $_POST['user_id'];

    $sql = "INSERT INTO shipping_address (type, name, phone, email, address_1, postcode, city, state, user_id) VALUES ('shipping', '$name', '$phone', '$email', '$address_1', '$postcode', '$city', '$state', '$user_id' )";
    if(mysqli_query($db, $sql)){
      $address_id = mysqli_insert_id($db);
      echo json_encode([ 'address_id' => $address_id ]);
    }
 }
// var_dump($_POST);
//  exit;
 if (
   isset($_POST['name'])
   && isset($_POST['type'])
   && $_POST['type'] == 'pickup'
   && isset($_POST['no_tel'])
   && isset($_POST['datetime_pickup'])
  ) {
    $name = $_POST['name'];
    $phone = $_POST['no_tel'];
    $user_id = $_POST['user_id'];
    $datetime_pickup = $_POST['datetime_pickup'];

    $sql = "INSERT INTO shipping_address (type, name, phone, datetime_pickup, user_id) VALUES ('pickup', '$name', '$phone', '$datetime_pickup', '$user_id' )";
    if(mysqli_query($db, $sql)){
      $address_id = mysqli_insert_id($db);
      echo json_encode([ 'address_id' => $address_id ]);
    }
 }
 ?>
