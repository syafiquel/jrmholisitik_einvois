<?php
session_start();
require 'connection.php';
include('check_role.php');
date_default_timezone_set("Asia/Kuala_Lumpur");

function return401()
{
  http_response_code(401); // Unauthorized
  echo json_encode(["error" => "Unauthenticated"]);
  exit;
}

if(!isset($_SESSION['role']))
{
  return401();
}





?>
