<?php
// session_start();
require 'connection.php';
include('check_role.php');
date_default_timezone_set("Asia/Kuala_Lumpur");

$page = basename($_SERVER['PHP_SELF']);

if(!isset($_SESSION['role']))
{
  header("Location: ../index.php");
  exit;
}
if(!in_array($_SESSION['role'], $role))
// if($_SESSION['role'] != $role)
{
  // echo 'maintainance';
  // exit;
  header("Location:  ../".$_SESSION['role']."/index.php" );
  exit;
}


$logged_id=$_SESSION['user_id'];
// echo "<script>console.log($user_check)</script>";

$sql = mysqli_query($db,"SELECT access_level, username, id FROM user WHERE id = '$logged_id'");

$row=mysqli_fetch_array($sql,MYSQLI_ASSOC);

$user_id = $row['id'];

$user_role = check_role($row['access_level']);
// echo $user_role."|".$_SESSION['role'];
// var_dump($role);
// exit;


?>
