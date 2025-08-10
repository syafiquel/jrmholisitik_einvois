<?php
require_once "../../db_con/connection.php"; //Establishing connection with our database
include '../../init.php';

// var_dump($_POST);
// TODO: log the redeem rewards
// TODO: update new points balance
$user = AuthUser::getCurrentUser();

$reward_id = $_POST['reward_id'];
$reward = Catalogue::getReward($reward_id);
$points = $reward['price'];
$user_id = $_POST['user_id'];
if( !empty($_POST["user_id"]) ){
  if ($user['point_wallet'] < $points) {
    $data['title'] = 'Insufficient Point';
    $data['status'] = 'error';
  }else {
    Catalogue::redeemReward($reward_id, $user_id, $points);
    User::updatePoints($user_id, $points, 'deduct');
    $data['title'] = $reward['title'].' is yours. Check out Reward List';
    $data['status'] = 'success';
  }
  echo json_encode($data);

  exit;

}



 ?>
