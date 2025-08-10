<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database

 $poskod = $_GET['poskod'];
 $list_bandar = [];
 $sql = "SELECT DISTINCT(bandar), negeri FROM postcode WHERE poskod = '$poskod'";

 $result = mysqli_query($db, $sql);
 while ($row = $result->fetch_assoc()) {
   $list_bandar[] = strtoupper($row['bandar']);
   $negeri = strtoupper($row['negeri']);
 }

 echo json_encode([
   'list_bandar' => array_unique($list_bandar),
   'negeri' => $negeri,
 ]);
 ?>
