<?php
require "db_con/connection.php";
include 'init.php';

require 'lib/API.php';
require 'lib/Connect.php';
require 'billplz/configuration.php';

require_once 'lib/php-myinvois/autoload.php';
require_once 'lib/php-myinvois/src/helper.php';

use Billplz\Minisite\API;
use Billplz\Minisite\Connect;

$bill_from = $from ?? 'bill_callback';

$data = Connect::getXSignature($x_signature, $bill_from);
$connect = new Connect($api_key);
$connect->setStaging($is_sandbox);
$billplz = new API($connect);
list($rheader, $rbody) = $billplz->toArray($billplz->getBill($data['id']));

// print_r($rbody);
if ($rbody['paid']) {
 $bill_id = $rbody['id'];
 $description = $rbody['description'];
 $amount = $rbody['amount']/100;
 $payment_key = $rbody['reference_1'];


 if ($rbody['description'] == 'Credit Terms Settlement') {
   // echo "settle outstanding payment<hr>";
   Credit::settleOutstanding([
     'settlement_ref' => $rbody['reference_1'],
     'settlement_type' => $rbody['reference_2'],
     'bill_id' => $rbody['id'],
     'amount' => $amount
   ],
   $bill_from
   );

   $sql = "SELECT order_id FROM credit_terms WHERE settlement_ref = '{$rbody['reference_1']}'";
   $result = $db->query($sql);
   while($row = $result->fetch_assoc()){
        $order_id = $row['order_id'];
        $invoiceData = getInvoiceData($order_id, $db);

        $config = require 'lib/php-myinvois/config.php';
        $client = new MyInvois\Core\MyInvoisClient($config);
        $ublBuilder = new MyInvois\Ubl\UblBuilder();

        $facade = new MyInvois\MyInvoisFacade($client, $ublBuilder);

        $submissionResult = $facade->submitInvoice($invoiceData);

        if ($submissionResult['success']) {
            $submissionId = $submissionResult['submissionId'];
            $sql = "UPDATE purchase_order SET myinvois_submission_id = '$submissionId' WHERE id = '$order_id'";
            mysqli_query($db, $sql);
        }
   }

   // exit;
   $successpath = "http://$domain/agent/pay_outstanding.php";
 }

 if ($rbody['description'] == 'Agent Purchase') {
   $order_id = $rbody['reference_1'];
   Order::approveOnlineOrder(
     [
       'order_id' => $rbody['reference_1'],
       'description' => $rbody['description'],
       'bill_id' => $rbody['id'],
       'amount' => $amount
     ]
   );

   $invoiceData = getInvoiceData($order_id, $db);

    $config = require 'lib/php-myinvois/config.php';
    $client = new MyInvois\Core\MyInvoisClient($config);
    $ublBuilder = new MyInvois\Ubl\UblBuilder();

    $facade = new MyInvois\MyInvoisFacade($client, $ublBuilder);

    $submissionResult = $facade->submitInvoice($invoiceData);

    if ($submissionResult['success']) {
        $submissionId = $submissionResult['submissionId'];
        $sql = "UPDATE purchase_order SET myinvois_submission_id = '$submissionId' WHERE id = '$order_id'";
        mysqli_query($db, $sql);
    }

   $successpath = "http://$domain/agent/order.php?id=$order_id";
 }

  if (!empty($successpath)) {
      header('Location: ' . $successpath);
  } else {
      header('Location: ' . $rbody['url']);
  }
  exit;

} else {
   /*Do something here if payment has not been made*/
   header('Location: ' . $rbody['url']);
}

 ?>
