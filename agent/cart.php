<?php
 include 'core/init.php';
 include "../assets/plugins/resize_image.php";
 $page_name = "Cart";
 $url = "cart.php";

 if (!$inside_cart) {
  $sql = "SELECT * FROM purchase_order WHERE user_id = $user_id AND payment_status IS NULL AND resit_img IS NULL AND date_time_transfer IS NULL ORDER BY created_at DESC LIMIT 1";
  $result = $db->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $order_id = $row['id'];
      $sql = "DELETE FROM purchase_order WHERE id = $order_id AND user_id = $user_id";
      if (mysqli_query($db,$sql)) {
        // echo "order deleted";
        // exit;

      }
    }
  }
  $_SESSION['success'] = "Cart is empty. Let us add products first.";
  header("Location: shop.php");
  exit;
}


 require '../billplz/configuration.php';
 require '../lib/API.php';
 require '../lib/Connect.php';

 use Billplz\Minisite\API;
 use Billplz\Minisite\Connect;

if (isset($_POST['making_payment'])) {
  $order_id = $_POST['order_id'];
  $order = Order::purchaseOrder($order_id, false, $_POST['address_id'], $_POST['shipping_method']);
  $user = $order['user'];

  // Validate stock availability before processing any payment
  $stock_validation = Order::validateStockBeforePayment($order_id);
  if ($stock_validation['status'] == 'error') {
    $_SESSION['error'] = $stock_validation['message'];
    header("Location: $url");
    exit;
  }

  if ($_POST['payment_method'] === 'billplz') {

    $collection_id = 'ccjauryl';
    $collection_id = 'uug5fzxx';


    $parameter = array(
      'collection_id' => $collection_id,
      'email'=> $user['email'],
      'mobile'=> $user['no_tel'],
      'name'=> $user['nama'],
      'successpath'=> "http://$domain/payment_processing/{$order['id']}",
      'amount'=> $order['all_items_price']*100 ,
      'callback_url'=> "https://$domain/callback.php",
      'description'=> 'Agent Purchase'
    );
    $optional = array(
      'redirect_url' => "https://$domain/redirect.php",
      'reference_1_label' => "Payment Reference",
      'reference_1' => $order['id'],
      'reference_2_label' => '',
      'reference_2' => '',
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

  } elseif ($_POST['payment_method'] === 'credit') {
    if ($credit_available) {

      if (Order::payWithCreditTerm($order_id)) {
        Credit::recordCreditTermsUse($order);
        $_SESSION['success'] = "Payment was successfully. Credit Terms is now locked until you settled the outstanding. Thank you.";
        header("Location: order.php?id=$order_id");
        exit;
      }else {
        $_SESSION['error'] = "Credit Term Payment is not available to use. Please checkout credit page to confirm.";
        header("Location: cart.php");
        exit;
      }
    }

    $_SESSION['error'] = "Credit Term Payment is not available to use. Please checkout credit page to confirm.";
    header("Location: cart.php");
    exit;

  } elseif ($_POST['payment_method'] === 'bank_transfer') {

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

        $sql =  "UPDATE purchase_order SET
        payment_type = 'Bank Transfer',
        payment_status = 'Processing',
        resit_img = '$img_url_uploaded',
        date_time_transfer = '$date_time_transfer',
        isApproved = null
        WHERE id = '$order_id'";
        // echo $sql;
        // exit;
        if(mysqli_query($db, $sql))
        {

          $sql = "SELECT * FROM order_details WHERE order_id = $order_id";
          $result = $db->query($sql);
          while ($row = $result->fetch_assoc() ) {
            $quantity = $row['quantity'];
            $sql = "SELECT * FROM product WHERE id = ".$row['product_id'];
            $result_asd = $db->query($sql);
            while ($row_c = $result_asd->fetch_assoc() ) {
              Product::updateStock($row['product_id'], $order_id, $quantity);
              // $sql_update = "UPDATE product SET reserved_stock = reserved_stock + $quantity WHERE id = ".$row['product_id'];
              // $db->query($sql_update);
            }
          }

          if ($file_size) {
            if ($file_ext == "pdf") {
              move_uploaded_file($file_tmp, "../uploads/resit/". $img_url_uploaded);

            } else {

              $saizgambar = getImageSizeKeepAspectRatio($file_tmp, 700, 700);
              resize($file_tmp, "../uploads/resit/". $img_url_uploaded, $saizgambar['width'], $saizgambar['height']);
            }
          }

          $_SESSION['success'] = "Payment file has been uploaded. Once payment is approved, we will notified you.";
          header("Location: order.php?id=$order_id");
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




}


 if (isset($_POST['delete_item_id'])) {
  $order_detail_id = mysqli_real_escape_string($db, $_POST['delete_item_id']);
  $order_id = mysqli_real_escape_string($db, $_POST['order_id']);
  $negeri = mysqli_real_escape_string($db, $_POST['negeri']);
  // echo "<hr>";
  $item_details = Order::itemDetails($order_detail_id);
  $order = Order::purchaseOrder($order_id);

  // var_dump($item_details);
  // echo "<hr>";
  // var_dump($order);
  // echo "<hr>";
  $total_qty_item = $item_details['unit_qty'] * $item_details['quantity'];
  $total_left = $order['total_weight'] - $item_details['total_weight'];
  $new_shipping_cost = 0;
  // if (!empty($negeri) && $total_left > 0) {
  //   $new_shipping_cost = getPostage($total_left, strtoupper($negeri));
  // }

  // echo "<hr>";
  $sql_delete = "DELETE FROM order_details WHERE id = $order_detail_id AND order_id = $order_id";
  // exit;
  $sql_update = "UPDATE purchase_order SET
                  total_qty = total_qty - {$item_details['quantity']},
                  total_weight = total_weight - {$item_details['total_weight']},
                  all_items_price = all_items_price - {$item_details['total_price']},
                  shipping_cost = $new_shipping_cost
                  WHERE id = $order_id";
  // echo $sql_update;
  // echo $sql_delete;
  // exit;

  if( mysqli_query($db, $sql_delete) && mysqli_query($db, $sql_update) )
  {
    $_SESSION['success'] = "Item has been deleted. Cart is updated";
    header("Location: $url");
    exit;
  }else{
    $_SESSION['error'] = "Failed to delete. Please try again.";
    header("Location: $url");
    exit;
  }

}



  if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
  }else {
    $sql = "SELECT * FROM purchase_order WHERE user_id = $user_id AND payment_status IS NULL AND resit_img IS NULL AND date_time_transfer IS NULL ORDER BY created_at DESC LIMIT 1";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
      // echo "asdasd";
      // exit;
      while($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        $negeri = $row['negeri'];
      }
    }else {
      $_SESSION['success'] = "Cart is empty. Lets add products first.";
      header("Location: shop.php");
      exit;
    }

    // $_SESSION['error'] = "Unknown Cart reference.";
    // header("Location: order_list.php");
    // exit;
  }



  $credit_terms = Credit::getSetting();

$order = Order::purchaseOrder($order_id, 1);

 ?>

<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <!-- [Meta] -->
  <?php include 'partial/header.php'; ?>
  <link rel="stylesheet" href="../assets/css/plugins/datepicker-bs5.min.css" />
  <link rel="stylesheet" href="../assets/css/plugins/flatpickr.min.css" />

  <style media="screen">
    .order-product-quantity::-webkit-outer-spin-button,
    .order-product-quantity::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Firefox */
    .order-product-quantity[type=number] {
      -moz-appearance: textfield;
    }
  </style>

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-3" data-pc-sidebar-caption="true" data-pc-layout="<?php echo $main_orientation ?>" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
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

<?php
// did this because want to bypass sidebar/topbar
  $moreThanLimit = 0;
  if ($credit_available) {
    // $moreThanLimit = $order['total_qty'] > $credit_terms['limit_quantity'] || $order['all_items_price'] > $credit_terms['limit_total_price'];
    $moreThanLimit = $order['all_items_price'] > $user['credit_balance'];
    $credit_available = !($moreThanLimit);

  }

 ?>

  <!-- [ Main Content ] start -->
  <div class="pc-container">
    <div class="pc-content">
      <!-- <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard/index.html">Home</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0)">E-commerce</a></li>
                <li class="breadcrumb-item" aria-current="page">Checkout</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Checkout</h2>
              </div>
            </div>
          </div>
        </div>
      </div> -->
      <!-- [ Main Content ] start -->
      <div class="row">
        <!-- [ sample-page ] start -->
          <style media="screen">
            @media only screen and (max-width: 600px) {
              .checkout-tabs .nav-item{
                width: 33%;
                font-size: 13px;
              }
              .avtar.avtar-s{
                width: 30px;
                height: 30px;
              }
              .checkout-tabs .nav-item.show .nav-link, .checkout-tabs .nav-link.active {
                color:  var(--bs-primary);
                font-weight: 700;
              }
            }
          </style>
          <div class="card">
            <div class="card-body p-0">
              <ul class="nav nav-tabs checkout-tabs mb-0" id="myTab" role="tablist">
                <li class="nav-item ">
                  <a class="nav-link active" id="ecomtab-tab-1" data-bs-toggle="tab" href="#ecomtab-1" role="tab" aria-controls="ecomtab-1" aria-selected="true">
                    <div class="d-flex align-items-center">
                      <div class="avtar avtar-s">
                        <i class="ti ti-shopping-cart"></i>
                      </div>
                      <div class="flex-grow-1 ms-2">
                        <span class="mb-0">Cart </span>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" id="ecomtab-tab-2" data-bs-toggle="tab" href="#ecomtab-2" role="tab" aria-controls="ecomtab-2" aria-selected="true">
                    <div class="d-flex align-items-center">
                      <div class="avtar avtar-s">
                        <i class="ti ti-building-skyscraper"></i>
                      </div>
                      <div class="flex-grow-1 ms-2">
                        <span class="mb-0">Shipping </span>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" id="ecomtab-tab-3" data-bs-toggle="tab" href="#ecomtab-3" role="tab" aria-controls="ecomtab-3" aria-selected="true">
                    <div class="d-flex align-items-center">
                      <div class="avtar avtar-s">
                        <i class="ti ti-credit-card"></i>
                      </div>
                      <div class="flex-grow-1 ms-2">
                        <span class="mb-0">Payment</span>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="tab-content">
            <div class="tab-pane show active" id="ecomtab-1" role="tabpanel" aria-labelledby="ecomtab-tab-1">
              <?php
                // Check for stock issues and display warning
                $stock_validation = Order::validateStockBeforePayment($order_id);
                if ($stock_validation['status'] == 'error') {
                  echo '<div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading"><i class="ti ti-alert-triangle me-2"></i>Stock Warning!</h6>
                    <p class="mb-0">' . nl2br(htmlspecialchars($stock_validation['message'])) . '</p>
                    <hr>
                    <p class="mb-0">Please update your cart quantities or remove items with insufficient stock before proceeding to payment.</p>
                  </div>';
                }
              ?>
              <div class="row">
                <div class="col-xl-8">
                  <div class="card">
                    <div class="card-header">
                      <div class="row align-items-center my-2">
                        <div class="col">
                          <div class="progress" style="height: 6px">
                            <div class="progress-bar bg-primary" style="width: 33%"></div>
                          </div>
                        </div>
                        <div class="col-auto">
                          <p class="mb-0 h6">Step 1</p>
                        </div>
                      </div>
                    </div>
                    <!-- <div class="card-body border-bottom">
                        <h5>Cart Item <span class="ms-2 f-14 px-2 badge bg-light-secondary rounded-pill">3</span></h5>
                      </div> -->
                    <div class="card-body p-0 table-body">
                      <div class="table-responsive">
                        <table class="table mb-0" id="pc-dt-simple">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th class="text-end">Price</th>
                              <th class="text-center">Quantity</th>
                              <th class="text-end">Total</th>
                              <th class="text-end"></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $cart_price = 0;
                              // $sql = "SELECT *, od.unit_price as unit_price,
                              //    od.id as id,
                              //    od.total_price as total_price,
                              //    p.product_img as img,
                              //    p.name as product
                              //    FROM order_details od
                              //    LEFT JOIN product p
                              //    ON p.id = od.product_id
                              //    WHERE od.order_id = $order_id";
                              //    // echo $sql;
                              //    // exit;
                              //    $result = $db->query($sql);
                              //    while ($row = $result->fetch_assoc()) {
                                 foreach ($order['products'] as $key => $row) {
                                    $cart_price += $row['total_price'];
                                    $product_img = $row['img'];
                                    $products[$row['id']]['name'] = $row['name'];
                                    $products[$row['id']]['image'] = $img = $product_img && file_exists("$product_img") ? "$product_img" : '';
                                    $products[$row['id']]['total_price'] = $row['total_price'];
                                    $products[$row['id']]['quantity'] = $row['quantity'];
                                    
                                    // Get current stock for this product
                                    $stock_sql = "SELECT stock_balance FROM product WHERE id = " . $row['product_id'];
                                    $stock_result = $db->query($stock_sql);
                                    $stock_row = $stock_result->fetch_assoc();
                                    $current_stock = $stock_row['stock_balance'];
                                    $stock_status = '';
                                    $stock_class = '';
                                    
                                    if ($current_stock < $row['quantity']) {
                                        $stock_status = 'Insufficient Stock';
                                        $stock_class = 'text-danger';
                                    } elseif ($current_stock <= 5) {
                                        $stock_status = 'Low Stock';
                                        $stock_class = 'text-warning';
                                    } else {
                                        $stock_status = 'In Stock';
                                        $stock_class = 'text-success';
                                    }
                               ?>
                            <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="<?php echo $product_img  ?>" alt="image" class="bg-light wid-50 rounded" />
                                  <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1"><?php echo $row['name'] ?></h5>
                                    <small class="<?php echo $stock_class; ?>"><?php echo $stock_status; ?></small>
                                  </div>
                                </div>
                              </td>
                              <td class="text-end">
                                <h5 class="mb-0"><?php echo Helper::MYR($row['rank_price']);?></h5>
                                <!-- <span class="text-sm text-muted text-decoration-line-through"><?php echo Helper::MYR($row['unit_price']);?></span> -->
                              </td>
                              <td class="text-center">
                                <div class="btn-group btn-group-sm mb-2 border" role="group">
                                  <button type="button" onclick="decreaseValue('number-<?php echo $row['id']?>',)" class="btn btn-link-secondary"><i class="ti ti-minus"></i></button>
                                  <input class="wid-40 text-center border-0 m-0 form-control rounded-0 shadow-none order-product-quantity" type="number" id="number-<?php echo $row['id']?>" value="<?php echo $row['quantity'] ?>"
                                    onblur="calculateOrderProduct('<?php echo $row['id']?>', this.value)" min=1 />
                                  <button type="button" onclick="increaseValue('number-<?php echo $row['id']?>',)" class="btn btn-link-secondary"><i class="ti ti-plus"></i></button>
                                </div>
                              </td>
                              <td class="text-end ">
                                <h5 class="mb-0">
                                  RM<span id="op-price-<?php echo $row['id'];?>"><?php echo $row['total_price'];?></span>
                                </h5>
                              </td>
                              <td class="text-end">
                                <form id="item_<?php echo $row['id'];?>" method="post">
                                  <input type="hidden" name="delete_item_id" value="<?php echo $row['id'] ?>">
                                  <input type="hidden" name="order_id" value="<?php echo $order_id ?>">
                                  <input type="hidden" name="negeri" value="<?php echo $negeri ?>">
                                  <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default" onclick="deleteThisItem('#item_<?php echo $row['id'];?>')">
                                    <i class="ti ti-trash f-18"></i>
                                  </a>
                                </form>

                              </td>
                            </tr>
                            <?php }
                            $cart_price = number_format($cart_price, 2);?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <!-- <div class="">
                        <form method="post">
                          <input type="hidden" name="clear_cart_id" value="<?php echo $order_id ?>">
                          <button name="clear_cart" type="submit" class="btn btn-link-danger d-inline-flex align-items-center"><i class="ti ti-trash me-2"></i> Cancel Order</button>
                        </form>
                      </div> -->

                    </div>
                    <div class="col-6">

                      <!-- <div class="text-end">
                        <a href="shop.php" class="btn btn-link-secondary d-inline-flex align-items-center"><i class="ti ti-chevron-left me-2"></i> Back to Shopping</a>
                      </div> -->
                    </div>
                  </div>
                </div>
                <div class="col-xl-4">
                  <div class="card">
                    <div class="card-body py-2">
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                          <h5 class="mb-0">Order Summary</h5>
                        </li>
                        <li class="list-group-item px-0">
                          <div class="float-end">
                            <h5 class="mb-0">RM<span class="cart_total_price"><?php echo $cart_price ?></span></h5>
                          </div><span class="text-muted">Sub Total</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body py-2">
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                          <div class="float-end">
                            <h5 class="mb-0">RM<span class="cart_total_price"><?php echo $cart_price ?></span></h5>
                          </div>
                          <h5 class="mb-0 d-inline-block">Total</h5>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <!-- <div class="d-flex align-items-center text-muted my-4">
                    <div class="avtar avtar-s bg-light-primary flex-shrink-0 me-2">
                      <i class="material-icons-two-tone text-primary f-20">security</i>
                    </div>
                    <span class="text-muted text-sm w-100">Safe & Secure Payment. Easy returns. 100% Athentic products.</span>
                  </div> -->
                  <div class="d-grid mb-3">
                    <button class="btn btn-primary" onClick="change_tab('#ecomtab-2')" <?php echo ($stock_validation['status'] == 'error') ? 'disabled' : ''; ?>>
                      <?php echo ($stock_validation['status'] == 'error') ? 'Fix Stock Issues First' : 'Proceed to Delivery'; ?>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="ecomtab-2" role="tabpanel" aria-labelledby="ecomtab-tab-2">
              <div class="row">
                <div class="col-xl-8">
                  <div class="card">
                    <div class="card-header">
                      <div class="row align-items-center mb-3">
                        <div class="col">
                          <div class="progress" style="height: 6px">
                            <div class="progress-bar bg-primary" style="width: 66%"></div>
                          </div>
                        </div>
                        <div class="col-auto">
                          <p class="mb-0 h6">Step 2</p>
                        </div>
                      </div>
                    </div>
                    <div class="collapse  multi-collapse show" id="mainCollapse">
                      <div class="card-body border-bottom">
                        <div class="row align-items-center">
                          <div class="col">
                            <h5 class="mb-0">Shipping Information</h5>
                          </div>
                          <div class="col-auto">

                          </div>
                        </div>
                      </div>
                      <div class="">

                        <div class="card-body pc-component">
                          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" style="border-bottom: 1px dotted #eee;padding-bottom: 10px;">
                            <li class="nav-item">
                              <a
                                class="nav-link active"
                                id="pills-delivery-tab"
                                data-bs-toggle="pill"
                                href="#pills-delivery"
                                role="tab"
                                aria-controls="pills-delivery"
                                aria-selected="true"
                                onclick="setShiipingMethod('Delivery')"
                                >Delivery</a>
                            </li>
                            <li class="nav-item">
                              <a
                                class="nav-link "
                                id="pills-home-tab"
                                data-bs-toggle="pill"
                                href="#pills-home"
                                role="tab"
                                aria-controls="pills-home"
                                aria-selected="true"
                                onclick="setShiipingMethod('Shipping')"
                                >Shipping</a>
                            </li>
                            <li class="nav-item">
                              <a
                                class="nav-link"
                                id="pills-contact-tab"
                                data-bs-toggle="pill"
                                href="#pills-contact"
                                role="tab"
                                aria-controls="pills-contact"
                                aria-selected="false"
                                onclick="setShiipingMethod('Pick Up')"
                                >Pick Up</a
                              >
                            </li>
                          </ul>
                          <div class="tab-content" id="myTabContent" >
                            <div class="tab-pane fade show active" id="pills-delivery" role="tabpanel" aria-labelledby="home-tab">
                              <div class="text-center b-1">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_address_form">
                                  Add new address
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_address_list" onclick="loadAddress()">
                                  Select an address
                                </button>

                              </div>


                            </div>
                            <div class="tab-pane fade show " id="pills-home" role="tabpanel" aria-labelledby="home-tab">
                              <div class="text-center b-1">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_address_form">
                                  Add new address
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_address_list" onclick="loadAddress()">
                                  Select an address
                                </button>

                              </div>


                            </div>
                            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="profile-tab">
                              <div class="text-center">
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_pickup_form" >
                                  Add New
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_pickup_list" onclick="loadPickup()">
                                  Select a person
                                </button>
                              </div>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="collapse multi-collapse" id="selecetedAddreessCollapseExample2">
                      <div class="card-body border-bottom">
                        <div class="row align-items-center">
                          <div class="col">
                            <h5 class="mb-0">Selected <span id='selected_shipping_option'>Shipping Address</span></h5>
                          </div>
                          <div class="col-auto"> </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <div id="selectedAddress">

                        </div>
                        <div class="text-end btn-page mb-0 mt-4">
                          <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false" aria-controls="multiCollapseExample1 selecetedAddreessCollapseExample2">Cancel</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-link-primary"><i class="ti ti-arrow-narrow-left align-text-bottom me-2"></i>Back to Cart</button>
                  </div> -->
                </div>
                <div class="col-xl-4">
                  <?php include 'partial/_cart_summary.php'; ?>
                  <!-- <div class="d-flex align-items-center text-muted my-4">
                    <div class="avtar avtar-s bg-light-primary flex-shrink-0 me-2">
                      <i class="material-icons-two-tone text-primary f-20">security</i>
                    </div>
                    <span class="text-muted text-sm w-100">Safe & Secure Payment. Easy returns. 100% Athentic products.</span>
                  </div> -->
                  <div class="d-grid mb-3">
                    <button class="btn btn-primary" onClick="change_tab('#ecomtab-3')">Process to Checkout</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="ecomtab-3" role="tabpanel" aria-labelledby="ecomtab-tab-3">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-8">
                    <div class="card">
                      <div class="card-header">
                        <div class="row align-items-center my-2">
                          <div class="col">
                            <div class="progress" style="height: 6px">
                              <div class="progress-bar bg-primary" style="width: 99%"></div>
                            </div>
                          </div>
                          <div class="col-auto">
                            <p class="mb-0 h6">Step 3</p>
                          </div>
                        </div>
                      </div>
                      <div class="card-body border-bottom">
                        <h5>Payment</h5>
                      </div>
                      <div class="card-body" style="">

                        <div class="row">
                          <!-- <div class="col-xl-12 col-xxl-4">
                              <div class="address-check border rounded">
                                <div class="form-check">
                                  <input
                                    type="radio"
                                    name="payoptradio1"
                                    class="form-check-input input-primary"
                                    id="payopn-check-1"
                                    checked=""
                                  />
                                  <label class="form-check-label d-block" for="payopn-check-1">
                                    <span class="card-body p-3 d-block">
                                      <span class="h5 mb-3 d-block">Credit Card</span>
                                      <span class="d-flex align-items-center">
                                        <span class="f-12 badge bg-success me-3">5% OFF</span>
                                        <img src="../assets/images/application/card.png" alt="img" class="img-fluid ms-1" />
                                      </span>
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div> -->
                            <!-- <div class="col-xl-6 col-xxl-6">
                              <div class="address-check border rounded">
                                <div class="form-check">
                                  <input type="radio" value="billplz" name="payment_method_radio" onchange="paymentMethodChange('billplz')" class="form-check-input
                                    input-primary" id="payopn-check-2"/>
                                  <label class="form-check-label d-block" for="payopn-check-2">
                                    <span class="card-body p-3 d-block">
                                      <span class="h5 mb-3 d-block">Bank Transfer</span>
                                      <span class="d-flex align-items-center">
                                        <img src="../assets/images/application/billplz.webp" alt="img" class="img-fluid ms-1" style="max-width: 80px;" />
                                      </span>
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div> -->

                          <div class="col-12">
                            <div class="address-check border rounded">
                              <div class="form-check">
                                <input type="radio" value="billplz" name="payment_method_radio" onchange="paymentMethodChange('billplz')" class="form-check-input payment-options
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
                                <input <?php echo $credit_available ? "" : 'disabled'  ?> type="radio" value="credit" name="payment_method_radio" onchange="paymentMethodChange('credit')" class="form-check-input payment-options input-primary" id="credit-payment-opt" />
                                <label class="form-check-label d-block" for="credit-payment-opt">
                                  <div class="row">
                                    <div class="col-7">
                                      <span class="card-body p-3 d-block align-middle">
                                        <span class="h5 mb-2 d-block">
                                          Buy Now Pay Later
                                          <br>
                                          <small>Credit Terms</small>
                                        </span>
                                      </span>

                                    </div>
                                    <div class="col-5 text-end">

                                      <span class="card-body p-3 d-block">
                                        <img src="../assets/images/logo-dark.svg" alt="img" class="img-fluid ms-1 " style="max-width: 80px;" />
                                      </span>
                                    </div>
                                  </div>
                                  <div class="f-12 badge bg-danger  mx-3 mb-3" id="credit-not-available-indicator" style="display: <?php echo $credit_available ? 'none' : 'block' ?>;">
                                    <?php if ($moreThanLimit): ?>
                                      Disabled!<br>Over credit balance (RM<?php echo $user['credit_balance'] ?>) limit.
                                    <?php else: ?>
                                      Disable due to Outstading
                                    <?php endif; ?>
                                  </div>
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="address-check border rounded">
                              <div class="form-check">
                                <input type="radio" value="bank_transfer" name="payment_method_radio" onchange="paymentMethodChange('bank_transfer')" class="form-check-input payment-options
                                  input-primary" id="payopn-check-bank" />
                                <label class="form-check-label d-block" for="payopn-check-bank">
                                  <div class="row">
                                    <div class="col-7">
                                      <span class="card-body p-3 d-block align-middle">
                                        <span class="h5 m-0 d-block">Bank Transfer</span>
                                      </span>

                                    </div>
                                    <div class="col-4 text-end">
                                      <span class="card-body p-3 d-block">
                                        <img src="../assets/images/public_bank.webp" alt="img" class="img-fluid ms-1 " style="max-width: 80px;" />
                                      </span>
                                    </div>
                                  </div>
                                </label>
                              </div>
                            </div>

                          </div>

                        </div>
                      </div>
                    </div>
                    <!-- <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-link-primary"
                          ><i class="ti ti-arrow-narrow-left align-text-bottom me-2"  onClick="change_tab('#ecomtab-2')"></i>Back to Shipping Information</button
                        >
                      </div> -->
                  </div>
                  <div class="col-xl-4">
                    <?php //include 'partial/_cart_summary.php'; ?>

                    <!-- <div class="d-flex align-items-center text-muted my-4">
                      <div class="avtar avtar-s bg-light-primary flex-shrink-0 me-2">
                        <i class="material-icons-two-tone text-primary f-20">security</i>
                      </div>
                      <span class="text-muted text-sm w-100">Safe & Secure Payment. Easy returns. 100% Athentic products.</span>
                    </div> -->
                    <div class="d-grid mb-3">
                      <form method="post" enctype="multipart/form-data" id="form-payment">
                        <div class="card" id="upload-box" style="display: none;" >
                          <div class="card-body">
                            <h5>Please fill in transaction information</h5>
                            <br>
                            Bank Name : <b>PUBLIC BANK BERHAD</b> <br>
                            Bank Holder : <b>KIINO ONE SDN BHD</b> <br>
                            Account Number <b>3206592214</b> <br>
                            <hr>
                            Payment Receipt (Image/PDF) <span class="text-danger">*</span>
                            <input type="file" class="form-control" id="upload-receipt-input" name="resit_file">
                            <br>
                            Date Time of the transaction <span class="text-danger">*</span>
                            <div class="input-group timepicker">
                              <input name="date_time_transfer" class="form-control datetime-picker-x"  placeholder="Pick date time of transaction" id="transactionDateTime" type="text" />
                              <span class="input-group-text">
                                <i class="feather icon-clock"></i>
                              </span>
                            </div>
                          </div>
                        </div>
                        <div class="card" id="credit-agreement-box" style="display: none;">
                          <div class="card-body">
                            <div class="form-check mb-2" id="credit-term-agree-box">
                              <input class="form-check-input" type="checkbox" value="" id="creditAgreement">
                              <label class="form-check-label" for="creditAgreement">Do you agree? This is credit terms agreement please check first. <a href="#" data-bs-toggle="modal" data-bs-target="#modal-agreement-credit_terms">Read Agreement.</a> </label>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" name="payment_method" id="payment_method_input" value="billplz">
                        <input type="hidden" name="shipping_method" id="shipping_method" value="Delivery">
                        <input type="hidden" name="address_id" id="address_id" value="">
                        <input type="hidden" name="order_id" value="<?php echo $order_id ?>">
                        <input type="hidden" name="making_payment" value="making_payment">
                        <button type="button" onclick="submitThis()" name="making_payment" class="btn btn-primary btn-lg" style="width: 100%;" >
                          Pay <span id="now_later">Now</span> RM<span class="cart_total_price"><?php echo $cart_price ?>
                          via
                          <span id="payment_method_display">
                            Billplz
                          </span>
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ sample-page ] end -->
        <!-- </div> -->

      </div>
    </div>
  </div>
  <div class="modal fade " id="modalPoskodList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="myModalLabel1">SELECT CITY</h3>
          <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
            <i class="bx bx-x"></i>
          </button>
        </div>
        <div class="modal-body" id="city_list_wrapper">
          <div class="border border-danger p-1 text-danger mb-1 text-center rounded">No City</div>
        </div>
      </div>
    </div>
  </div>

  <div
    id="modal_address_list"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby="moda_address_listTitle"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="moda_address_listTitle">Select Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="address_list">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div

    id="modal-agreement-credit_terms"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby="moda_address_listTitle"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Credit Terms Agreement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php echo $credit_terms['agreement_context'] ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div

    id="modal_pickup_list"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby="moda_address_listTitle"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Select a Person</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="pickup_list">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div
    id="modal_pickup_form"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby="moda_address_listTitle"
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="">Who will Pick Up</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="pickUpForm" method="post">
          <div class="modal-body" id="">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <!-- <div class="mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="" id="checkaddres" />
                      <label class="form-check-label" for="checkaddres"> Self Pick Up</label>
                    </div>
                  </div> -->
                  <div class="mb-3 row">
                    <h5>Note: Please come pick up one hours after order made
                      <br>
                      or
                      <br>
                      Chat admin to check order is ready or not</h5>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Full Name :</label>
                    <div class="col-lg-8">
                      <input name="name" type="text" class="form-control" placeholder="Enter Full Name" />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Phone No. :</label>
                    <div class="col-lg-8">
                      <input name="no_tel" type="text" class="form-control" placeholder="Enter Phone No." />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Date Time :<small class="text-muted d-block">(9:00AM-17:00PM)</small> </label>
                    <div class="col-lg-8">
                      <div class="input-group timepicker">
                        <input name="datetime_pickup" class="form-control datetime-picker"  placeholder="Select pick up time" type="text" />
                        <span class="input-group-text">
                          <i class="feather icon-clock"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="addPickup()">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div
    id="modal_set_pickup_form"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby=""
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="">Set Pick Up Date Time</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="setPickUpForm" method="post">
          <div class="modal-body" id="">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Full Name :</label>
                    <div class="col-lg-8">
                      <input name="name" id="pickup_name" type="text" class="form-control" placeholder="Enter Full Name" disabled />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Phone No. :</label>
                    <div class="col-lg-8">
                      <input name="no_tel" id="pickup_no_tel" type="text" class="form-control" placeholder="Enter Phone No." disabled />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Date Time :<small class="text-muted d-block">(9:00AM-17:00PM)</small> </label>
                    <div class="col-lg-8">
                      <div class="input-group timepicker">
                        <input type="hidden" name="pickup_id" id="pickup_id" value="">
                        <input name="datetime_pickup" class="form-control datetime-picker"  placeholder="Select pick up time" type="text" />
                        <span class="input-group-text">
                          <i class="feather icon-clock"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="setPickup()">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div
    id="modal_address_form"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby="moda_address_listTitle"
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="">Add new address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="">
          <form method="post" id="newAddressForm">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Full Name :</label>
                    <div class="col-lg-8">
                      <input name="name" type="text" class="form-control" placeholder="Enter Full Name" />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Phone No. :</label>
                    <div class="col-lg-8">
                      <input name="no_tel" type="text" class="form-control" placeholder="Enter Phone No." />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Email :</label>
                    <div class="col-lg-8">
                      <input name="email" type="email" class="form-control" placeholder="Enter Email" />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Address :</label>
                    <div class="col-lg-8">
                      <input name="address_1" type="text" class="form-control" placeholder="Enter Address" />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">Postcode :</label>
                    <div class="col-lg-8">
                      <input id="poskod" name="postcode" type="number" autocomplete="off" placeholder="Enter Postcode" class="form-control" value="" onblur="getCityByPoskod(this.value)" required />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">City :</label>
                    <div class="col-lg-8">
                      <input id="daerah" name="city" type="text" placeholder="Enter City" class="form-control" />
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-lg-4 col-form-label">State :</label>
                    <div class="col-lg-8">
                      <select id="negeri" name="state" class="form-control" required >
                        <option value="">Pilih Negeri</option>
                        <?php foreach ($all_negeri as $each_negeri): ?>
                        <option value="<?php echo $each_negeri ?>" <?php $negeri == $each_negeri ? 'selected' : '' ?>> <?php echo $each_negeri ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary" onclick="addAddress()" >Save & Deliver to this Address</button>
        </div>
      </form>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->


  <?php include 'partial/footer.php'; ?>
  <?php include 'partial/scripts.php'; ?>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="../assets/js/plugins/datepicker-full.min.js"></script>
  <script src="../assets/js/plugins/flatpickr.min.js"></script>


  <script type="text/javascript">
  var mp3_url = 'https://media.geeksforgeeks.org/wp-content/uploads/20190531135120/beep.mp3';

    var user_id = <?php echo $user_id ?> ;
    var products = JSON.parse('<?php echo json_encode($products) ?>');
    var credit_qty_limit = parseInt('<?php echo $credit_terms['limit_quantity'] ?>')
    var credit_qty_price = parseInt('<?php echo $user['credit_balance'] ?>')

    // console.log(products);
    renderProductList(products);

    function deleteThisItem(the_item_form) {
      Swal.fire({
        icon: "warning",
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this item!",
        showCancelButton: true,
        confirmButtonText: "Yes, Delete it.",
      }).then((result) => {
        console.log(result.value);
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $(the_item_form).submit();
          return;
        } else if (result.isDenied) {
          // window.location.href = 'cart.php';
          return;
        }
      });
      return;
      swal({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this item!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $(the_item_form).submit();
            // swal("Poof! Your imaginary file has been deleted!", {
            //   icon: "success",
            // });
          }
        });
    }

    function change_tab(tab_name) {
      if (tab_name == '#ecomtab-3') {
        var address_id = $("#address_id").val()

        if (!address_id) {
          // alert('Please set Shipping Method first')
          (new Audio(mp3_url)).play()
          $("#mainCollapse").effect('shake');
          return change_tab('#ecomtab-2');
        }
      }

      var someTabTriggerEl = document.querySelector('a[href="' + tab_name + '"]');
      var actTab = new bootstrap.Tab(someTabTriggerEl);
      actTab.show();
    }

    function increaseValue(counter) {
      var op_id = counter.replace("number-", "");
      var value = parseInt(document.getElementById(counter).value, 10);
      value = isNaN(value) ? 0 : value;
      value++;

      // if (calculateOrderProduct(op_id, value) ==  1) {
      //   document.getElementById(counter).value = value;
      // }
      calculateOrderProduct(op_id, value, function (success) {
        if (success) {
          document.getElementById(counter).value = value;
        }
      });
    }

    function decreaseValue(counter) {
      var op_id = counter.replace("number-", "");
      var value = parseInt(document.getElementById(counter).value, 10);
      value = isNaN(value) ? 0 : value;
      value < 1 ? (value = 1) : '';
      value--;
      if (value == 0) {
        value = 1;
      }
      calculateOrderProduct(op_id, value, function (success) {
       if (success) {
         document.getElementById(counter).value = value;
       }
     });
      // if (calculateOrderProduct(op_id, value)  ==  1) {
      //   document.getElementById(counter).value = value;
      // }
    }

    // quantity en
    function calculateOrderProduct(op_id, new_qty, callback) {
      // return false;

      if (isNaN(new_qty) || new_qty < 1) {
        $("#number-"+op_id).val(1)
        new_qty = 1
     }


      $("#credit-payment-opt").attr('disabled', false);
      $("#credit-not-available-indicator").hide()
      $("#credit-not-available-indicator").text('Disable due to Outstading')
      $(".payment-options").attr('checked', false);

      // console.log(op_id, new_qty);

      $.ajax({
        url: "../shared/shop_executer/update_order_product.php",
        data: {
          user_id,
          op_id,
          new_qty
        },
        type: "POST",
        dataType: 'json',
        success: function(data) {
          // alert('manatap');
          if (data["status"] == 'success') {
            $('.order-product-list').html('');
            // mainUpdateCart(user_id);
            $("#op-price-" + op_id).text(parseFloat(data.new_price).toFixed(2));
            $(".cart_total_price").text(parseFloat(data.update_cart.total_price).toFixed(2));
            // console.log({qty: data.update_cart.total_qty > credit_qty_limit, price:data.update_cart.total_price > credit_qty_price});
            // if ((data.update_cart.total_qty > credit_qty_limit || data.update_cart.total_price > credit_qty_price)) {
            if ( data.update_cart.total_price > credit_qty_price) {
              $("#credit-payment-opt").attr('disabled', true);
              $("#credit-not-available-indicator").show()
              // $("#credit-not-available-indicator").text(`Disabled due to order is over quantity (${credit_qty_limit} items) or price (RM${credit_qty_price}) limit.`)
              $("#credit-not-available-indicator").html(`Disabled!<br>Over credit balance (RM${credit_qty_price}) limit.`)
              paymentMethodChange('billplz')

            }
            // return;
            renderProductList(data.update_cart.product_list)

            callback(true)
          } else if (data['status'] == 'error') {



            Swal.fire({
              icon: "error",
              title: "Action Failed",
              text: data['title']
            }).then((swalData) => {
              var type = data['type'] ?? '';
              if (type == 'over_stock') {
                // Show confirmation dialog to remove item instead of just setting to available stock
                Swal.fire({
                  icon: "warning",
                  title: "Stock Issue Detected",
                  text: "This product has insufficient stock. Would you like to remove this item from your cart?",
                  showCancelButton: true,
                  confirmButtonText: "Yes, Remove Item",
                  cancelButtonText: "Keep Current Quantity",
                  confirmButtonColor: "#d33",
                  cancelButtonColor: "#3085d6"
                }).then((result) => {
                  if (result.isConfirmed) {
                    // Remove the item from cart
                    var itemForm = "#item_" + op_id;
                    deleteThisItem(itemForm);
                  } else {
                    // Keep current quantity (set to available stock)
                    calculateOrderProduct(op_id, data['available_stock'], function(success) {
                      if (success) {
                        $('#number-' + op_id).val(data['available_stock']);
                      }
                    });
                  }
                });
              }
            });
            callback(false)

          }
        },
        error: function() {
          callback(false)

        }
      });
    }

    function renderProductList(product_list) {
      Object.entries(product_list).forEach(([product_id, product]) => {
        const productHTML = `
      <li class="list-group-item">
        <div class="d-flex align-items-start">
          <img
            class="bg-light rounded img-fluid wid-60 flex-shrink-0"
            src="${products[product_id].image}"
            alt="User image"
          />
          <div class="flex-grow-1 mx-2">
            <h5 class="mb-1 mt-2">${products[product_id].name}</h5>
            <p class="text-muted text-sm mb-2">X ${product.quantity}pcs</p>
          </div>
          <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default flex-shrink-0">
            <h5 class="mb-1"><b>RM${product.total_price}</b></h5>
          </a>
        </div>
      </li>`;
        $('.order-product-list').append(productHTML);
      });
    }


    function getState(country) {
      if (country) {
        $.ajax({
          crossOrigin: true,
          url: '../shared/fetcher/getStates.php',
          data: {
            country
          },
          type: 'get',
          dataType: 'html',
          success: function(data) {
            $("#negeri").html(data)
          }
        });
      }
    }

    getState('malaysia');


    function getCityByPoskod(poskod) {
      if (poskod) {
        $.ajax({
          url: '../shared/fetcher/getCityByPoskod.php',
          data: {
            poskod
          },
          type: 'get',
          dataType: 'json',
          success: function(data) {
            console.log(data.list_bandar);
            if (data.list_bandar.length == 0) {
              return alert('Poskod Not Found')
            }

            if (data.list_bandar.length > 1) {

              var output = '';
              var outputClass = 'border border-danger p-1 text-danger mb-1 text-center rounded cursor-pointer';
              $("#city_list_wrapper").html('');
              $.each(data.list_bandar, function(index, bandar) {
                $("#city_list_wrapper").append('<div class="' + outputClass + '" onclick="selectCity(`' + bandar + '`)">' + bandar + '</div>')
              });
              $("#modalPoskodList").modal('show');
            }
            if (data.list_bandar.length == 1) {
              $("#daerah").val(data.list_bandar[0]);

            }
            $("#negeri").val(data.negeri).change();
          }
        });

      }
    }

    function selectCity(city) {
      $("#daerah").val(city);
      $("#modalPoskodList").modal('hide');

    }

    // const inputs = document.querySelectorAll('.datetime-picker');
    // for (let i = 0; i < inputs.length; i++) {
    //   const input = inputs[0];  // <- look at the index
    //   new Datepicker(input);
    // }

    function rmydays(date) {
        return (date.getDay() === 0 || date.getDay() === 6);
    }

    document.querySelectorAll('.datetime-picker').flatpickr({
      enableTime: true,
      noCalendar: false,
      minTime: '9:00',
      maxTime: '17:00',
      disable: [rmydays],
      minDate: "today",
      dateFormat: "Y-m-d H:i",

    });
    document.querySelectorAll('.datetime-picker-x').flatpickr({
      enableTime: true,
      noCalendar: false,
      dateFormat: "Y-m-d H:i",

    });

    function loadAddress(selected = null, pickup = null) {
      var wrapper = '#address_list';
      if (pickup) {
        wrapper = '#pickup_list';
      }
      if (!selected) {
        $(wrapper).html('');
      }
      $('#selectedAddress').html('');

      $.ajax({
        url: '../shared/fetcher/getUserAddress.php',
        data: { user_id, selected, pickup },
        type: 'get',
        dataType: 'json',
        success: function(data) {
          if (data.addresses.length == 0) {
            $('#address_list').append(`<div class="text-center">Address Book is empty. No worries, just add one.<br><br>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_address_form">
              Add new address
            </button></div>`);

          }

          if (data.addresses.length >= 1) {

            $.each(data.addresses, function(index, address) {
              var checked = address.checked ? 'checked' : '';
              if (!selected) {
                var addressHTML = `<div class="address-check-block">
                <div class="address-check border rounded p-3">
                <div class="form-check">
                <input onClick='change_address(${address.id})' type="radio" name="radio1" value="${address.id}" class="form-check-input input-primary address-block" id="address-${address.id}" ${checked} />
                <label class="form-check-label d-block" for="address-${address.id}">
                <span class="h6 mb-0 d-block">${address.name}</span>
                <span class="text-muted address-details">${address.full_address} </span>
                <span class="row align-items-center justify-content-between">
                <span class="col-sm-6">
                <span class="text-muted mb-0">${address.phone}</span>
                </span>
                <span class="col-sm-6 text-sm-end">
                <span class="address-btns d-flex align-items-center justify-content-end">
                <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default me-1">
                <i class="ti ti-trash f-20"></i>
                </a>
                <button class="btn btn-sm btn-outline-primary">Deliver here</button>
                </span>
                </span>
                </span>
                </label>
                </div>
                </div>
                </div>`;
                $('#address_list').append(addressHTML);

              }
              if (address.checked) {
                $('#selectedAddress').append(`<div class="address-check-block">
                <div class="address-check border rounded p-3">
                <div class="form-check">
                <input type="radio" name="" value="" class="form-check-input input-primary address-block" id="address-${address.id}a" ${checked} />
                <label class="form-check-label d-block" for="address-${address.id}">
                <span class="h6 mb-0 d-block">${address.name}</span>
                <span class="text-muted address-details">${address.full_address} </span>
                <span class="row align-items-center justify-content-between">
                <span class="col-sm-6">
                <span class="text-muted mb-0">${address.phone}</span>
                </span>
                <span class="col-sm-6 text-sm-end">
                <span class="address-btns d-flex align-items-center justify-content-end">
                <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default me-1">
                <i class="ti ti-trash f-20"></i>
                </a>
                <button class="btn btn-sm btn-outline-primary">Deliver here</button>
                </span>
                </span>
                </span>
                </label>
                </div>
                </div>
                </div>`);

              }
            });
          }

        }
      });
    }

    function loadPickup(selected = null) {
      var wrapper = '#pickup_list';
      if (!selected) {
        $(wrapper).html('');
      }
      $('#selectedAddress').html('');

      $.ajax({
        url: '../shared/fetcher/getUserAddress.php',
        data: { user_id, selected, pickup: 1 },
        type: 'get',
        dataType: 'json',
        success: function(data) {
          if (data.addresses.length == 0) {
            $('#pickup_list').append(`<div class="text-center">Pick up contacts is empty. No worries, just add one.<br><br>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_pickup_form" >
              Add New
            </button>`);
          }

          if (data.addresses.length >= 1) {

            $.each(data.addresses, function(index, address) {
              var checked = address.checked ? 'checked' : '';
              if (!selected) {

                var addressHTML = `<div class="address-check-block">
                <div class="address-check border rounded p-3">
                <div class="form-check">
                <input onClick='setPickUpDateTime(${address.id}, "${address.name}", "${address.phone}")' type="radio" name="radio1" value="${address.id}" class="form-check-input input-primary address-block" id="address-${address.id}" ${checked} />
                <label class="form-check-label d-block" for="address-${address.id}">
                <span class="h6 mb-0 d-block">${address.name}</span>
                <span class="row align-items-center justify-content-between">
                <span class="col-sm-6">
                <span class="text-muted mb-0">${address.phone}</span>
                </span>
                <span class="col-sm-6 text-sm-end">
                <span class="address-btns d-flex align-items-center justify-content-end">
                <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default me-1">
                <i class="ti ti-trash f-20"></i>
                </a>
                <button class="btn btn-sm btn-outline-primary">Deliver here</button>
                </span>
                </span>
                </span>
                </label>
                </div>
                </div>
                </div>`;
                $('#pickup_list').append(addressHTML);
              }
              if (address.checked) {
                $('#selectedAddress').append(`<div class="address-check-block">
                <div class="address-check border rounded p-3">
                <div class="form-check">
                <input type="radio" name="" value="" class="form-check-input input-primary address-block" id="address-${address.id}a" ${checked} />
                <label class="form-check-label d-block" for="address-${address.id}">
                <span class="h6 mb-0 d-block">${address.name}</span>
                <span class="text-muted address-details">Pick Up at ${address.datetime_pickup} </span>
                <span class="row align-items-center justify-content-between">
                <span class="col-sm-6">
                <span class="text-muted mb-0">${address.phone}</span>
                </span>
                <span class="col-sm-6 text-sm-end">
                <span class="address-btns d-flex align-items-center justify-content-end">
                <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default me-1">
                <i class="ti ti-trash f-20"></i>
                </a>
                <button class="btn btn-sm btn-outline-primary">Select this</button>
                </span>
                </span>
                </span>
                </label>
                </div>
                </div>
                </div>`);

              }

            });
          }

        }
      });
    }

    (function($){
      $.fn.getFormData = function(){
        var data = {};
        var dataArray = $(this).serializeArray();
        for(var i=0;i<dataArray.length;i++){
          data[dataArray[i].name] = dataArray[i].value;
        }
        return data;
      }
    })(jQuery);

    function addAddress() {
      event.preventDefault(); // Prevent default form submission

      var newAddressData = $("#newAddressForm").getFormData();
      $.ajax({
        url: '../shared/fetcher/add_new_address.php',
          type: 'POST',
          data: { ...newAddressData , user_id, type: 'shipping'}, // Data to send
          dataType: 'json',
          success: function(response) {
            $("#modal_address_form").modal('hide');
            $("#newAddressForm").trigger("reset");
            change_address(response.address_id)
            // loadAddress(response.address_id);
            // $("#address_id").val(response.address_id);

          },
          error: function(xhr, status, error) {
            // Handle error
            $('#response').html(`<p style="color: red;">Error: ${error}</p>`);
          }
        });

    }

    function addPickup() {
      event.preventDefault(); // Prevent default form submission

      var newPickUpData = $("#pickUpForm").getFormData();
      $.ajax({
        url: '../shared/fetcher/add_new_address.php',
          type: 'POST',
          data: { ...newPickUpData , user_id, type: 'pickup'}, // Data to send
          dataType: 'json',
          success: function(response) {
            $("#modal_pickup_form").modal('hide');
            $("#pickUpForm").trigger("reset");
            change_address(response.address_id, 1)
            // loadAddress(response.address_id);
            // $("#address_id").val(response.address_id);

          },
          error: function(xhr, status, error) {
            // Handle error
            $('#response').html(`<p style="color: red;">Error: ${error}</p>`);
          }
        });

    }

  function change_address(id, isPickUp) {
    $("#address_id").val(id);
    var selected_shipping_option = 'Shipping Address';

    if (isPickUp) {
      selected_shipping_option = 'Pick Up';
      loadPickup(id, 1);
    }else {
      loadAddress(id);
    }
    $("#selected_shipping_option").text(selected_shipping_option);
    $('#modal_address_list').modal('hide');
    $('#modal_pickup_list').modal('hide');
    $("#selecetedAddreessCollapseExample2").collapse("show");
    $(".collapse:not(#selecetedAddreessCollapseExample2)").collapse("hide");

  }

  function setPickUpDateTime(id, name, notel) {
    $("#pickup_name").val(name);
    $("#pickup_no_tel").val(notel);
    $("#pickup_id").val(id);

    $("#modal_set_pickup_form").modal('show')

  }

  function setPickup() {
    event.preventDefault(); // Prevent default form submission

    var setPickUpData = $("#setPickUpForm").getFormData();
    $.ajax({
      url: '../shared/fetcher/set_pickup.php',
        type: 'POST',
        data: setPickUpData, // Data to send
        dataType: 'json',
        success: function(response) {
          $("#modal_pickup_list").modal('hide');
          $("#setPickUpForm").trigger("reset");
          $("#modal_set_pickup_form").modal('hide');
          change_address(response.id, 1)
          // loadAddress(response.address_id);
          // $("#address_id").val(response.address_id);

        },
        error: function(xhr, status, error) {
          // Handle error
        }
      });
  }

  function paymentMethodChange(method) {
    $("#upload-box").hide();
    $("#credit-agreement-box").hide();
    $("#creditAgreement").attr('required', false);
    $("#upload-receipt-input").attr('required', false);
    $("#transactionDateTime").attr('required', false);
    $("#payment_method_input").val(method);
    var display = 'Billplz';
    if (method == 'billplz') {
      display = 'Billplz';
    }
    if (method == 'credit') {
      display = 'Credit Terms';
      $("#credit-agreement-box").show();
      $("#creditAgreement").attr('required', true);

    }
    if (method == 'bank_transfer') {
      display = 'Bank Transfer';
      $("#upload-box").show();
      $("#upload-receipt-input").attr('required', true);
      $("#transactionDateTime").attr('required', true);
      $("#transactionDateTime").attr('readonly', false);
    }
    $("#payment_method_display").text(display);
    $("#now_later").text(method == 'billplz' || method == 'bank_transfer'  ? 'Now' : 'Later');


  }


  function submitThis() {
    event.preventDefault(); // Prevent default form submission

    var payment_method_input = $("#payment_method_input").val()
    var address_id = $("#address_id").val()

    if (!address_id) {
      alert('Please set Shipping Method first')
      $("#mainCollapse").effect('shake');
      return change_tab('#ecomtab-2');
    }
    var resitUpload = $("#upload-receipt-input")[0].files.length
    var transactionDateTime = $("#transactionDateTime").val()
    if (payment_method_input == 'bank_transfer' && ( !resitUpload || !transactionDateTime ) ) {
      (new Audio(mp3_url)).play()
      $("#upload-box").effect('shake');
      return;
    }

    if ( payment_method_input == 'credit' && $("#creditAgreement").not(':checked').length) {
      return alert('Please check on credit terms agreement')
    }

    return $("#form-payment").submit();
  }

  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    switch (e.target.id){
        case "ecomtab-tab-3":{
            var address_id = $("#address_id").val()

            if (!address_id) {
              alert('Please set Shipping Method first')
              // $("#mainCollapse").effect('shake');
              return change_tab('#ecomtab-2');
            }
           break;
        }
    }
  })

  function setShiipingMethod(method) {
    $("#shipping_method").val(method);
  }

  </script>

</body>
<!-- [Body] end -->

</html>
