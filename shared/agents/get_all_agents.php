<?php
require_once "../../db_con/connection.php";
require '../../db_con/check_authentication.php';

$sql = "SELECT id, nama FROM user
    WHERE access_level <> 66
    AND isDeleted IS NULL";
$result = $db->query($sql);
// $data=$result->fetch_assoc();
while($data[] = mysqli_fetch_assoc($result));
array_pop($data);

// header('Content-Type: application/json');
echo json_encode(['releases' =>$data]);

 ?>
