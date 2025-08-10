<?php
 include 'core/init.php';
 include "../assets/plugins/resize_image.php";
 $page_name = "Pay Outstanding";
 $url = "pay_outstanding.php";

 require '../billplz/configuration.php';
 require '../lib/API.php';
 require '../lib/Connect.php';


  use Billplz\Minisite\API;
  use Billplz\Minisite\Connect;

 $credit_available = $user['credit_used'] <= 0;
 $creditToPay = $user['credit_used'];

 $sql = "SELECT *, ct.status as status, ct.created_at as created_at, ct.id as id FROM credit_terms ct
  JOIN user usr
  ON usr.id = ct.user_id
  WHERE ct.user_id = $user_id
  ORDER BY ct.id DESC ";

 $result = $db->query($sql);

 if ($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
     $amounts[$row['order_id']] = $row['outstanding_amount'];
   }
 }

 // if (!$credit_available) {
 //   $creditToPay = Credit::getOutstanding($user_id);
 // }

 // var_dump($creditToPay);
 // exit;

if (isset($_POST['pay_outstanding'])) {

    $settlement_type = $_POST['settlement_type'] ?? 'Full';
    $totalPaid = 0;

    if (isset($_POST['order_id'])) {
      $selectedOrders = $_POST['order_id'];       // Array of selected order IDs
      foreach ($selectedOrders as $orderId) {
        if (isset($amounts[$orderId])) {
          $amountPaid = floatval($amounts[$orderId]);
          $totalPaid += $amountPaid;

          // Simpan detail untuk paparan atau simpanan
          $paymentDetails[] = [
            'order_id' => $orderId,
            'amount' => $amountPaid
          ];
        }
      }
      $selectedIds = implode(',', $selectedOrders);
    }


    if (isset($paymentDetails)) {
      $created_at = date("h:i:s d/m/Y");
      $payment_ref = md5($user_id.$created_at);

      $settlement_type = 'Partial';
      $payment_desc = 'Credit Terms Settlement';
      Credit::updateSettlementRef($selectedIds, $payment_ref);
    }else {
      $formatedID = Helper::orderFormat($user_id);
      $payment_ref = "AGENT#$formatedID";
      $timestamp = time();
      $payment_ref = $payment_ref .'-'. $timestamp;
      $settlement_type = 'Full';
      $payment_desc = 'Credit Terms Settlement';
      $totalPaid = $creditToPay;
    }
    // echo $totalPaid;
    // // var_dump($paymentDetails);
    // exit;


    if ($_POST['payment_method'] === 'billplz') {

      $collection_id = 'ccjauryl';
      $collection_id = 'uug5fzxx';

      $parameter = array(
        'collection_id' => $collection_id,
        'email'=> $user['email'],
        'mobile'=> $user['no_tel'],
        'name'=> $user['nama'],
        'successpath'=> "http://$domain/agent/pay_outstanding.php",
        'amount'=> $totalPaid*100 ,
        'callback_url'=> "https://$domain/callback.php",
        'description'=> $payment_desc
      );
      $optional = array(
        'redirect_url' => "https://$domain/redirect.php",
        'reference_1_label' => "Payment Reference",
        'reference_1' => $payment_ref,
        'reference_2_label' => 'Settlement Type',
        'reference_2' => $settlement_type,
        'deliver' => 'false'
      );

      if (empty($parameter['mobile']) && empty($parameter['email'])) {
        $parameter['email'] = 'noreply@billplz.com';
      }

      if (!filter_var($parameter['email'], FILTER_VALIDATE_EMAIL)) {
        $parameter['email'] = 'noreply@billplz.com';
      }


      $connect = new Connect($api_key);
      $connect->setStaging($is_sandbox);
      $billplz = new API($connect);
      list ($rheader, $rbody) = $billplz->toArray($billplz->createBill($parameter, $optional));

      // print_r($rheader);
      // var_dump($rbody);
      // exit;

      /***********************************************/
      // Include tracking code here
      /***********************************************/
      if ($rheader !== 200) {
        if (defined('DEBUG')) {
          echo '<pre>'.print_r($rbody, true).'</pre>';
        }
        if (!empty($fallbackurl)) {
          header('Location: ' . $fallbackurl);
        }
      }
      header('Location: ' . $rbody['url']);
      exit;

    } else if ( $_POST['payment_method'] === 'bank_transfer' ) {

      $resit_file = $_FILES['resit_file'];
      $date_time_transfer = $_POST['date_time_transfer'];

      if(isset($_FILES['resit_file']) && $_FILES['resit_file']['size'] > 0){

        //  echo 'dah masyuk upload pls<br>';
        $file_name = $_FILES['resit_file']['name'];
        $file_size = $_FILES['resit_file']['size'];
        $file_tmp = $_FILES['resit_file']['tmp_name'];
        $file_type = $_FILES['resit_file']['type'];
        $tmp = explode('.', $file_name);
        $file_extension = end($tmp);
        $file_name_rand = round(microtime(true));
        $newfilename = $file_name_rand . '.' . $file_extension;
        $file_ext=strtolower($file_extension);

        $expensions= array("jpeg","jpg","png","webp","pdf","jfif");

        if ($file_size) {
            if(in_array($file_ext,$expensions)=== false){
              $_SESSION['error'] = "Format fail tidak diterima , Sila gunakan format WEBP, JPG, PNG atau PDF sahaja.";
              header("Location: $url");
              exit;
            }
          }


        $img_url_uploaded = $newfilename;

        if(empty($errors_uploading)==true){

          // $sql =  "UPDATE user SET
          // settlement_approval_status = 'Pending',
          // settlement_resit_file = '$img_url_uploaded',
          // settlement_datetime_transfer = '$date_time_transfer'
          // WHERE id = '{$user_id}'";



          $sql =  "INSERT INTO credit_settlement (user_id, amount, approval_status, resit_file, datetime_transfer, settlement_ref, type)
          VALUES ('$user_id', '$totalPaid', 'pending', '$img_url_uploaded', '$date_time_transfer', '$payment_ref', '$settlement_type')";
          // echo $sql;
          // exit;
          if(mysqli_query($db, $sql))
          {
            if ($file_size) {
              if ($file_ext == "pdf") {
                move_uploaded_file($file_tmp, "../uploads/credit_settlement/". $img_url_uploaded);

              } else {

                $saizgambar = getImageSizeKeepAspectRatio($file_tmp, 700, 700);
                resize($file_tmp, "../uploads/credit_settlement/". $img_url_uploaded, $saizgambar['width'], $saizgambar['height']);
              }
            }

            if ($settlement_type == 'Partial') {
              $sql = "UPDATE credit_terms SET
              payment_approval_status = 'pending'
              WHERE settlement_ref = '$payment_ref'";
            }else {
              $sql = "UPDATE credit_terms SET
              payment_approval_status = 'pending',
              settlement_ref = '$payment_ref'
              WHERE user_id = '$user_id'
              AND status = 'outstanding'";
            }
            mysqli_query($db, $sql);

            $_SESSION['success'] = "Payment file has been uploaded. Once payment is approved, we will notified you.";
            header("Location: $url");
            exit;

          } else {

            $_SESSION['error'] = "There error when processing data. Please try again.";
            header("Location: $url");
            exit;
          }

        } else {

          $_SESSION['error'] = "Invalid image. Use format: PDF, JPG, JPEG & PNG. Please try again.";
          header("Location: $url");
          exit;
        }

      } else{
        // $errors = "";
        $_SESSION['error'] = "Receipt file is compulsory for bank transfer payment method.";
        header("Location: $url");
        exit;
      }
      exit;

    }


  // $sql = "UPDATE credit_terms SET status = 'completed' WHERE user_id = '$user_id'";
  // if (mysqli_query($db,$sql)) {
  //   $_SESSION['success'] = "Credit Outstanding has been settle. Nice! ";
  //   header("Location: $url");
  //   exit;
  // }else {
  //   $_SESSION['error'] = "Action failed.";
  //   header("Location: $url");
  //   exit;
  // }
}

$credit_settlement = Credit::getPendingSettlement($user_id);

$total_unpaid = 0;
$countPendingApproval = 0;
$approval = [];
$outstanding = [];

$sql = "SELECT *, ct.status as status, ct.created_at as created_at, ct.id as id FROM credit_terms ct
 JOIN user usr
 ON usr.id = ct.user_id
 WHERE ct.user_id = $user_id
 AND ct.status = 'outstanding'
 ORDER BY ct.id DESC ";

$result_c = $db->query($sql);
if ($result_c->num_rows > 0) {
  while($row = $result_c->fetch_assoc()) {
    if ($row['payment_approval_status'] == 'pending') {
      $approval[] = $row;
      $countPendingApproval++;
    }else {
      $outstanding[] = $row;
      $total_unpaid += $row['outstanding_amount'];
    }
  }
}

 ?>

<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <!-- [Meta] -->
  <?php include 'partial/header.php'; ?>

  <link rel="stylesheet" href="../assets/css/plugins/datepicker-bs5.min.css" />
  <link rel="stylesheet" href="../assets/css/plugins/flatpickr.min.css" />

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="<?php echo $main_orientation ?>" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ Sidebar Menu ] start -->
  <?php include 'partial/sidebar.php'; ?>
  <!-- [ Sidebar Menu ] end -->
  <!-- [ Header Topbar ] start -->
  <?php include 'partial/topnav.php'; ?>
  <!-- announcement modal -->
  <?php include 'partial/announcement_modal.php'; ?>
  <!-- [ Header ] end -->



  <!-- [ Main Content ] start -->
  <div class="pc-container">
    <div class="pc-content" style="max-width: 600px;">
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h3 class="mb-0"><?php echo $page_name ?></h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ Main Content ] start -->
      <div class="card">
        <div class="card-body ">
          <div class="mt-4">
            <form method="post" enctype="multipart/form-data">
              <?php if ($credit_available): ?>
                <h4 class="text-center">You have No Outstanding</h4>
              <?php elseif ($user['credit_used']): ?>
                <h4 class="text-center">🔔 Outstanding 🔔
                  <br>
                  You have unpaid bills below
                  <br>
                </h4>
                <!-- <h3 class="text-center text-danger">
                  Total Outstanding: <?php echo Helper::MYR($user['credit_used']) ?>
                </h3> -->
                <div class="">
                  <div class="row">
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-10 "  >
                      <div class="mx-auto class" style="max-width: 500px;">
                        <hr>
                        <div class="" style="display: ;">
                          <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation"><a class="nav-link text-uppercase active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false" tabindex="-1">Full</a></li>
                            <li class="nav-item" role="presentation"><a class="nav-link text-uppercase" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false" tabindex="-1">Partial (<?php echo count($outstanding) ?>)</a></li>
                            <li class="nav-item" role="presentation"><a class="nav-link text-uppercase " id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="true">In Process (<?php echo count($approval) ?>)</a></li>
                          </ul>
                          <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                              <div class="form-check mb-2">
                                <!-- <input class="form-check-input input-light-primary full-settlement" data-amount="<?php echo $user['credit_used'] ?>" type="checkbox" id="fullSettlement" checked="checked" data-gtm-form-interact-field-id="0"> -->
                                <input class="form-check-input input-light-primary full-settlement" data-amount="<?php echo $total_unpaid ?>" type="checkbox" id="fullSettlement" checked="checked" data-gtm-form-interact-field-id="0">
                                <label class="form-check-label" for="fullSettlement">
                                 <b>FULL SETTLEMENT</b> : <?php echo Helper::MYR($total_unpaid) ?>
                                </label>
                              </div>
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                              <?php foreach ($outstanding as $row): ?>
                                <div class="form-check mb-2">
                                  <input class="form-check-input input-light-primary partialSettlement " name="order_id[]" value="<?php echo $row['order_id']?>" data-amount="<?php echo $row['outstanding_amount'] ?>" type="checkbox" id="partial-<?php echo $row['order_id'];?>"  data-gtm-form-interact-field-id="0">

                                  <label class="form-check-label" for="partial-<?php echo $row['order_id'];?>">
                                    <b>ORDER#<?php echo Helper::orderFormat($row['order_id']);?></b> <?php echo date('d/m/Y', strtotime($row['created_at']))?>: <b><?php echo Helper::MYR($row['outstanding_amount']) ?></b>
                                  </label>
                                </div>
                              <?php endforeach; ?>
                            </div>
                            <div class="tab-pane fade " id="contact" role="tabpanel" aria-labelledby="contact-tab">
                              <?php foreach ($approval as $row): ?>
                                <li>
                                  🕒<b>ORDER#<?php echo Helper::orderFormat($row['order_id']);?></b> <?php echo date('d/m/Y', strtotime($row['created_at']))?>: <b><?php echo Helper::MYR($row['outstanding_amount']) ?></b>
                                </li>

                              <?php endforeach; ?>
                              <?php if (!count($approval)): ?>
                                <div class="text-center">
                                  All Clear.
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                        <div class="" style="display: none">

                          <div class="form-check mb-2">
                            <!-- <input class="form-check-input input-light-primary full-settlement" data-amount="<?php echo $user['credit_used'] ?>" type="checkbox" id="fullSettlement" checked="checked" data-gtm-form-interact-field-id="0"> -->
                            <input class="form-check-input input-light-primary full-settlement" data-amount="<?php echo $total_unpaid ?>" type="checkbox" id="fullSettlement" checked="checked" data-gtm-form-interact-field-id="0">
                            <label class="form-check-label" for="fullSettlement">
                              <b>FULL SETTLEMENT</b> : <?php echo Helper::MYR($total_unpaid) ?>
                            </label>
                          </div>
                          <hr>
                          <b>PARTIAL SETTLEMENT</b>
                          <br>
                          <br>
                          <?php
                          // hidden
                          if (false) {
                            // code...
                            $sql = "SELECT *, ct.status as status, ct.created_at as created_at, ct.id as id FROM credit_terms ct
                            JOIN user usr
                            ON usr.id = ct.user_id
                            WHERE ct.user_id = $user_id
                            AND ct.status = 'outstanding'
                            AND ct.payment_approval_status <> 'pending'
                            ORDER BY ct.id DESC ";

                            $result_c = $db->query($sql);
                            if ($result_c->num_rows > 0) {
                              while($row = $result_c->fetch_assoc()) {
                                ?>
                                <div class="form-check mb-2">
                                  <input class="form-check-input input-light-primary partialSettlement " name="order_id[]" value="<?php echo $row['order_id']?>" data-amount="<?php echo $row['outstanding_amount'] ?>" type="checkbox" id="partial-<?php echo $row['order_id'];?>"  data-gtm-form-interact-field-id="0">

                                  <label class="form-check-label" for="partial-<?php echo $row['order_id'];?>">
                                    <b>ORDER#<?php echo Helper::orderFormat($row['order_id']);?></b> <?php echo date('d/m/Y', strtotime($row['created_at']))?>: <b><?php echo Helper::MYR($row['outstanding_amount']) ?></b>
                                  </label>
                                </div>
                                <?php
                              }
                            }else {
                              ?>
                              All Clear!
                              <?php
                            }
                          }
                          ?>

                          <?php foreach ($outstanding as $row): ?>
                            <div class="form-check mb-2">
                              <input class="form-check-input input-light-primary partialSettlement " name="order_id[]" value="<?php echo $row['order_id']?>" data-amount="<?php echo $row['outstanding_amount'] ?>" type="checkbox" id="partial-<?php echo $row['order_id'];?>"  data-gtm-form-interact-field-id="0">

                              <label class="form-check-label" for="partial-<?php echo $row['order_id'];?>">
                                <b>ORDER#<?php echo Helper::orderFormat($row['order_id']);?></b> <?php echo date('d/m/Y', strtotime($row['created_at']))?>: <b><?php echo Helper::MYR($row['outstanding_amount']) ?></b>
                              </label>
                            </div>
                          <?php endforeach; ?>
                          <?php if ($countPendingApproval): ?>
                            <hr>
                            <b>PENDING APPROVAL</b>
                            <br>
                            <div class="text-center pt-3">

                              <button type="button" class="btn btn-outline-primary mr-2 mb-2 p-2" data-bs-toggle="modal" data-bs-target=".bd-example-modal-sm" >
                                🕒 <?php echo $countPendingApproval ?> Orders is pending approval
                              </button>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-12">
                    <div class="address-check border rounded">
                      <div class="form-check">
                        <input type="radio" value="billplz" name="payment_method" onchange="paymentMethodChange('billplz')" class="form-check-input payment-options
                        input-primary" id="payopn-check-2" checked="" />
                        <label class="form-check-label d-block" for="payopn-check-2">
                          <div class="row">
                            <div class="col-7">
                              <span class="card-body p-3 d-block align-middle">
                                <span class="h5 d-block">Pay with Billplz</span>
                              </span>

                            </div>
                            <div class="col-5 text-end">

                              <span class="card-body p-3 d-block">
                                <img src="../assets/images/application/billplz.webp" alt="img" class="img-fluid ms-1 " style="max-width: 80px;" />
                              </span>
                            </div>
                          </div>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="address-check border rounded">
                      <div class="form-check">
                        <input type="radio" value="bank_transfer" name="payment_method" onchange="paymentMethodChange('bank_transfer')" class="form-check-input payment-options
                        input-primary" id="payopn-check-bank" />
                        <label class="form-check-label d-block" for="payopn-check-bank">
                          <div class="row">
                            <div class="col-7">
                              <span class="card-body p-3 d-block align-middle">
                                <span class="h5 m-0 d-block">Bank Transfer</span>
                              </span>

                            </div>
                            <div class="col-5 text-end">
                              <span class="card-body p-3 d-block">
                                <img src="../assets/images/public_bank.webp" alt="img" class="img-fluid ms-1 " style="max-width: 80px;" />
                              </span>
                            </div>
                          </div>
                        </label>
                      </div>
                    </div>
                    <div class="col-12">

                      <div class="card" id="upload-box" style="display: none;" >
                        <div class="card-body">
                          <h5>Please fill in transaction information</h5>
                          <br>
                          Bank Name : <b>PUBLIC BANK BERHAD</b> <br>
                          Bank Holder : <b>KIINO ONE SDN BHD</b> <br>
                          Account Number <b>3206592214</b> <br>
                          <hr>

                          Payment Receipt (Image/PDF) <span class="text-danger">*</span>
                          <input type="file" class="form-control" id="upload-receipt-input" name="resit_file" accept="application/pdf,image/jpeg,image/jpeg,image/x-png">
                          <br>
                          Date Time of the transactiosn <span class="text-danger">*</span>
                          <div class="input-group timepicker">
                            <input name="date_time_transfer" class="form-control" autocomplete="off" placeholder="Pick date time of transaction" id="transactionDateTime" type="text" />
                            <span class="input-group-text">
                              <i class="feather icon-clock"></i>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary mt-2"style="width: 100%;" name="pay_outstanding">
                  RM<span id="totalAmount"><?php echo $user['credit_used'] ?></span>
                  <br>
                  Pay Now
                </button>
              <?php elseif ($user['credit_used'] && $credit_settlement['approval_status'] = 'pending'): ?>
                <h4 class="text-center">Payment approval in process</h4>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>


      <div
        class="modal fade bd-example-modal-sm"
        tabindex="-1"
        role="dialog"
        aria-labelledby="mySmallModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog ">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title h4" id="mySmallModalLabel">Pending Approval</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php foreach ($approval as $row): ?>
                <li>
                  🕒<b>ORDER#<?php echo Helper::orderFormat($row['order_id']);?></b> <?php echo date('d/m/Y', strtotime($row['created_at']))?>: <b><?php echo Helper::MYR($row['outstanding_amount']) ?></b>
                </li>

              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->


  <?php include 'partial/footer.php'; ?>
  <?php include 'partial/scripts.php'; ?>
  <script src="../assets/js/plugins/datepicker-full.min.js"></script>
  <script src="../assets/js/plugins/flatpickr.min.js"></script>

 <script type="text/javascript">
 $('#transactionDateTime').flatpickr({
   enableTime: true,
   noCalendar: false,
   minuteIncrement: 1,
   dateFormat: "Y-m-d H:i",
 });

 function paymentMethodChange(method) {
   $("#upload-box").hide();
   $("#upload-receipt-input").attr('required', false);
   $("#transactionDateTime").attr('required', false);

   if (method == 'bank_transfer') {
     display = 'Bank Transfer';
     $("#upload-box").show();
     $("#upload-receipt-input").attr('required', true);
     $("#transactionDateTime").attr('required', true);
     $("#transactionDateTime").attr('readonly', false);
   }



 }


 $(document).ready(function () {
    // When full settlement is checked
    function updateTotal() {
        let total = 0;

        // If full settlement is checked, only get its amount
        if ($('#fullSettlement').is(':checked')) {
            total = parseFloat($('#fullSettlement').data('amount'));
        } else {
            // Otherwise, sum up partials
            $('.partialSettlement:checked').each(function () {
                total += parseFloat($(this).data('amount'));
            });
        }

        $('#totalAmount').text(total.toFixed(2));
    }

    // Handle full settlement click
    $('#fullSettlement').on('change', function () {
        if ($(this).is(':checked')) {
            $('.partialSettlement').prop('checked', false);
        }
        updateTotal();
    });

    // Handle partial settlement click
    $('.partialSettlement').on('change', function () {
        if ($(this).is(':checked')) {
            $('#fullSettlement').prop('checked', false);
        }
        updateTotal();
    });

    // Optional: Call once on page load
    updateTotal();
});


 </script>
</body>
<!-- [Body] end -->

</html>
