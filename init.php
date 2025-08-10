<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
// echo $_SERVER['DOCUMENT_ROOT'];
// exit;
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");
define('SITE_NAME', 'JRM HOLISTIK AMPANG');
$domain = $_SERVER['HTTP_HOST'];

header("Access-Control-Allow-Origin: *"); // Allow all domains (for testing only)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

//
// // Get the current domain
// $currentDomain = $_SERVER['HTTP_HOST'];
//appFolder
// // Check if the current domain is 'jombiz.vip'
// if ($currentDomain == 'jombiz.vip') {
//     // Get the current URI and query string
//     $currentUri = $_SERVER['REQUEST_URI'];
//
//     // Construct the new URL by replacing the domain
//     $newUrl = 'https://tonewow.my' . $currentUri;
//
//     // Redirect to the new URL
//     header("Location: $newUrl", true, 301);
//     exit();
// }
// if ($currentDomain == 'app.jombiz.vip') {
//     // Get the current URI and query string
//     $currentUri = $_SERVER['REQUEST_URI'];
//
//     // Construct the new URL by replacing the domain
//     $newUrl = 'https://app.tonewow.my' . $currentUri;
//
//     // Redirect to the new URL
//     header("Location: $newUrl", true, 301);
//     exit();
// }
//
//
// $mainUrl = "https://tonewow.my";
// // $mainUrl = $domain;
// // $mainUrl = "https://$domain";
// $idp = $_GET['id'] ?? '264527';
// $username_superadmin = '264527';
// // if (!isset($_GET['dev'])) {
// //   $redirectUrl = $domain;
// //   if(strpos($domain, 'app.') === false){
// //     $redirectUrl = str_replace("app.","",$domain);
// //     header("Location: http://$redirectUrl/coming-soon.php");
// //     exit;
// //   }
// // }
// $appFolder= $_SERVER['DOCUMENT_ROOT'];
// $webType = 'twe';
// define('SITE_NAME', 'TONEWOW.MY');
// define('TAGLINE', 'This is the best platform for you to achieve financial freedom');
// define('APP_SITE_URL', 'https://app.jombiz.vip/');
// define('SITE_URL', 'https://jombiz.vip/');
// define('WEB_TYPE_FULL', 'TONEWOW.MY');
// define('DOMAIN_NAME', '//TONEWOW.MY');
// define('NO_IMAGE_URL', '//app.jombiz.vip/assets/img/default/no-image.png');
// define('DEFAULT_IMAGE_URL', '//app.jombiz.vip/assets/img/default/');
// define('RANK_FILE_URL', '//app.jombiz.vip/uploads/rank_file/');
// define('KP_FILE_URL', '//app.jombiz.vip/uploads/user_kp/');
// define('PROFILE_PICTURE_URL', '//app.jombiz.vip/uploads/profile_pic/');
// define('PROFILE_PICTURE_THUMB_URL', '//app.jombiz.vip/uploads/profile_pic/thumbs/');
// // define('FAVICON_URL', '//'.$domain.'/favicon.ico');
// define('JOMBIZ_LOGO_URL', $mainUrl.'/jombiz_logo_circle_black.png');
// define('FAVICON_WHITE_URL', '//'.$domain.'/assets/logo/icon-only-white.png');
// define('LOGO_URL', 'https://'.$domain.'/assets/logo/icon-text-small.png');
// define('ICON_LOGO_URL', 'https://'.$domain.'/beta_assets/logo/logo-border.png');
// define('LEVEL_LOGO_URL', 'https://'.$domain.'/assets/img/level/');
// define('OG_IMAGE_URL', 'https://'.$domain.'/OG_IMAGE.jpg');
// define('FAVICON_URL', $mainUrl.'/logo-favicon.png');
//
// define('SIDEBAR_LOGO_URL', 'https://'.$domain.'/assets/logo/icon-text-only.png');
// // define('SIDEBAR_LOGO_WHITE_URL', 'https://'.$domain.'/assets/logo/icon-text-white.png');
// define('SIDEBAR_LOGO_WHITE_URL', 'https://'.$domain.'/logo-full.png');
//
// // define('ELASTIC_MAIL_USERNAME', 'app@jombiz.vip');
// // define('ELASTIC_MAIL_API_KEY', 'EA8E1A54EFA00E6A9C72C600A41DECBCFFF9');
// define('ELASTIC_MAIL_USERNAME', 'superadmin@jombiz.vip');
// define('ELASTIC_MAIL_API_KEY', 'BC13DA3545DF492822FBDD3BE020BF800A02');
//
//
// $idp_superadmin = '264527';
//
//
// if ( $domain == 'tonewow.my' || $domain == 'jombiz.vip' || $domain == 'www.jombiz.vip' ) {
//   $appFolder = 'app';
// }
//
// define('ROOT_DIRECTORY', $appFolder);
// $upCaseWebType = strtoupper($webType);
// $domains['twe'] = "https://tonewowexcel.my";
// $domains['twp'] = "https://tonewowplus.my";
include_once 'core/classes/Database.php';
include_once 'core/database/connect.php';
include_once 'core/classes/User.php';
include_once 'core/classes/Product.php';
include_once 'core/classes/Order.php';
include_once 'core/classes/Helper.php';
include_once 'core/classes/Credit.php';
include_once 'core/classes/Telegram.php';

$all_negeri = array('PERLIS','KEDAH','PULAU PINANG','PERAK','PAHANG','SELANGOR','KUALA LUMPUR','PUTRAJAYA','NEGERI SEMBILAN','MELAKA','LABUAN','JOHOR','KELANTAN','TERENGGANU','SABAH','SARAWAK');

$bank_list =  array('Maybank',
                    'Bank Islam',
                    'CIMB Bank',
                    'RHB Bank',
                    'Public Bank Berhad',
                    'Affin Bank',
                    'Hong Leong Bank',
                    'AmBank',
                    'Bank Rakyat',
                    'HSBC Bank Malaysia',
                    'Bank Simpanan Nasional (BSN)');


$standard_month = array(
    "01" => "January",
    "02" => "February",
    "03" => "March",
    "04" => "April",
    "05" => "May",
    "06" => "June",
    "07" => "July",
    "08" => "August",
    "09" => "September",
    "10" => "October",
    "11" => "November",
    "12" => "December"
);

$standard_month_my = array(
    "01" => "Januari",
    "02" => "Februari",
    "03" => "Mac",
    "04" => "April",
    "05" => "Mei",
    "06" => "Jun",
    "07" => "Julai",
    "08" => "Ogos",
    "09" => "September",
    "10" => "Oktober",
    "11" => "November",
    "12" => "Disember"
);

$standard_year = [];
$year_start = 2024;
$year_end = date('Y');

while ($year_start <= $year_end) {
  $standard_year[$year_start] = $year_start;
  $year_start++;
}




// lock if user type



// function lockIfUser($user_type, $value)
// {
//
//   if ($user_type == 'u') {
//     $value = '<i class="fa fa-lock" onclick="alert(`Please Upgrade to Dealer`)"> </i>';
//   }
//   return $value;
// }
//
// if (isset($_POST['changeBizTo'])) {
//   $business = Business::changeBusiness($_POST['changeBizTo']);
//   $_SESSION['success'] = "Successfully change business to ".$business['name'].".";
//   $curPageName = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
//
//   header("Location: $curPageName");
//   exit;
// }

 $ranks_ref = [
   1 => 'Gold',
   2 => 'Silver'
 ];

 ?>
