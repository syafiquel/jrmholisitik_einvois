<?php
include("db_con/connection.php"); //Establishing connection with our database
include('db_con/check_role.php');
// session_start();


$error = ""; //Variable for storing our errors.
$goto = "index.php";
//
// if(isset($_GET["page"]) && $_GET["page"])
// {
//   $goto = $_GET["page"];
// }

if(isset($_SESSION['login_user']) && isset($_SESSION['role']) ){
  $role = $_SESSION['role'];
  header("location: $role/$goto");
  exit;
}

if(isset($_POST["login"]))
{
  if(empty($_POST["username"]) || empty($_POST["password"]))
  {
    $error = "Both fields are required.";
  }else
  {
    // Define $username and $password
    $username=$_POST['username'];
    $password=$_POST['password'];

    // To protect from MySQL injection
    $username = stripslashes($username);
    $password = stripslashes($password);
    $username = mysqli_real_escape_string($db, $username);
    $password = mysqli_real_escape_string($db, $password);
    $secret_salt = mysqli_real_escape_string($db, '??..jrmholisampang||2024..??');
    // echo $password.$secret_salt;
    $password = md5($password.$secret_salt);
    // echo "|".$password;
    // exit;
    // echo "$webType";
    // exit;
    //Check username and password from database
    // $sql="SELECT * FROM user WHERE username='$username' and password='$password' AND isNonJ2S IS NULL AND payment_status = 'Done' AND web_type = '$webType'  AND role <> 'user'";
    $sql="SELECT * FROM user WHERE email ='$username' and password='$password' AND isDeleted IS NULL";
    // $sql="SELECT * FROM user WHERE email ='$username' and password='$password' AND access_level <> 1 AND isDeleted IS NULL";
    // echo "$sql";
    // exit;
    $result=mysqli_query($db,$sql);
    $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
    // var_dump($row);
    // exit;
    //If username and password exist in our database then create a session.
    //Otherwise echo error.
    if(mysqli_num_rows($result) == 1)
      {
        // Initializing Session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['user_type'] = $row['user_type'];
        $_SESSION['user_role'] = $row['role'];
        // $getBiz = Business::getUserBiz($row['id']);
        // $_SESSION['business'] = $getBiz['main_business'];
        // $_SESSION['businesses'] = $getBiz['businesses'];

        $role = check_role($row['access_level']);
        $_SESSION['role'] = $role;
        header("location: $role"); // Redirecting To Other Page
        exit;
      }else
      {
        $error = "*Incorrect Email or Password.<br>Please try again.";
      }
    }
  }
?>
