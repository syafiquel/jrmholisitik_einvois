<?php
require_once "../../db_con/connection.php";

$country_name = isset($_GET['country']) ? $_GET['country'] : 'malaysia';
$state = $_GET['state'];

$sqlStates = "SELECT * FROM states WHERE country_id = '132'";
$result = $db->query($sqlStates);
$output = '<option value="">PILIH NEGERI</option>';
while ($rows = $result->fetch_assoc()) {
  $selected = $state == $rows['name'] ? "selected" : "";
  $output .= '<option '.$selected.' value="'.strtoupper($rows['name']).'">'.strtoupper($rows['name']).'</option>';
}

echo "$output";

 ?>
