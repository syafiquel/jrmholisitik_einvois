<?php

include 'core/init.php';
include "../assets/plugins/resize_image.php";
$page_name = "Product Stock Log";
$url = "product_stock_log.php";

function shortenText($text, $maxLength = 50, $suffix = '..') {
    if (strlen($text) <= $maxLength) {
        return $text;
    }
    return substr($text, 0, $maxLength - strlen($suffix)) . $suffix;
}

$where_clause = '';

$startDate = '';
$endDate = '';
$errors = [];
$selected_product_id = '';
$selected_agent_id = '';

if (isset($_GET['filtered'])) {
    $startDate = $_GET['date_start'] ?? '';
    $endDate = $_GET['date_end'] ?? '';

    $selected_product_id = $_GET['product_id'] ?? '';
    $selected_agent_id = $_GET['agent_id'] ?? '';

    // Convert to timestamps
    $start = strtotime($startDate);
    $end = strtotime($endDate);
    $today = strtotime(date('Y-m-d'));

    // 1. Start date must be earlier than end date
    if ($start > $end ) {
        $errors[] = "Start date must be earlier than end date.";
    }

    // 2. End date must not be later than today
    if ($end > $today) {
        $errors[] = "End date cannot be later than today.";
    }

    // 3. Date range must be less than or equal to 1 month
    $maxDuration = strtotime('+1 month', $start);
    if ($end > $maxDuration) {
        $errors[] = "Date range must be less than or equal to 1 month.";
    }

    if ($startDate && $endDate) {
      $where_clause .= " AND
      (
        ( plog.created_at >= '$startDate 00:00:00' AND plog.created_at <= '$endDate 23:59:59')
        OR
        (plog.purchased_at >= '$startDate 00:00:00' AND plog.purchased_at <= '$endDate 23:59:59' )
      )";
    }
    if ($selected_product_id) {
      $where_clause .= " AND plog.product_id = $selected_product_id";
    }
    if ($selected_agent_id) {
      $where_clause .= " AND po.user_id = $selected_agent_id";
    }
    // Show errors or success message
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } else {
        // echo "<p style='color:green;'>Date range is valid. Processing data...</p>";
    }
}



$sql = "SELECT * FROM product ";

$result_c = $db->query($sql);
if ($result_c->num_rows > 0) {
  while($row = $result_c->fetch_assoc()) {
    $products[$row['id']] = $row['name'];
  }
}

$sql = "SELECT * FROM user WHERE access_level <> 66 AND isDeleted IS NULL";

$result_c = $db->query($sql);
if ($result_c->num_rows > 0) {
  while($row = $result_c->fetch_assoc()) {
    $agents[$row['id']] = $row['nama'];
  }
}


$sql_raw = "SELECT *, plog.created_at as created_at FROM product_stock_log plog
LEFT JOIN product prd ON prd.id = plog.product_id
LEFT JOIN purchase_order po ON po.id = plog.order_id
WHERE 1 = 1  $where_clause";
// echo $sql_raw;
// exit;

$result = $db->query("$sql_raw");
$totalRecords = mysqli_num_rows($result);
// $totalRecords++;
include '../assets/plugins/paginator.php';
$paginator = new Paginator();
$paginator->total = $totalRecords;
$paginator->paginate();

$start = ($paginator->currentPage-1)*$paginator->itemsPerPage;

$sql = "$sql_raw  ORDER BY plog.id DESC LIMIT $start,  $paginator->itemsPerPage";
// echo $sql;
// exit;

$result_c = $db->query($sql);

 ?>

 <!doctype html>
 <html lang="en">
 <!-- [Head] start -->

 <head>
   <!-- [Meta] -->
   <?php include 'partial/header.php'; ?>
   <link rel="stylesheet" href="../assets/plugins/dropify/dist/css/dropify.min.css">
   <link rel="stylesheet" href="../assets/css/plugins/flatpickr.min.css" />

   <style media="screen">
     .dropify-wrapper {
       border: 1px solid #bec8d0;
       border-radius: 8px;
     }
   </style>

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
     <div class="pc-content">
       <!-- [ Main Content ] start -->
       <div class="page-header">
         <div class="page-block">
           <div class="row align-items-center">
             <!-- <div class="col-md-12">
               <ul class="breadcrumb">
                 <li class="breadcrumb-item"><a href="../dashboard/index.html">Home</a></li>
                 <li class="breadcrumb-item"><a href="javascript: void(0)">E-commerce</a></li>
                 <li class="breadcrumb-item" aria-current="page">Products list</li>
               </ul>
             </div> -->
             <div class="col-md-12">
               <div class="page-header-title">
                 <h2 class="mb-0">Products Stock Log</h2>
               </div>
             </div>
           </div>
         </div>
       </div>
       <div class="row">
         <!-- [ sample-page ] start -->
         <div class="col-sm-12">
           <div class="card table-card">
             <div class="card-body pb-0">
               <form method="get">
                 <div class=" p-4 ">
                   <div class="row">
                     <div class="col-md-4">
                       <div class="mb-3">
                         <input type="text" class="form-control datetime-picker-x" placeholder="Date Start" name="date_start" value="<?php echo $startDate ?>">
                       </div>
                     </div>
                     <div class="col-md-4">
                       <div class="mb-3">
                         <input type="text" class="form-control datetime-picker-x" placeholder="Date End" name="date_end" value="<?php echo $endDate ?>">
                       </div>
                     </div>
                     <!-- <div class="col-md-4">
                       <div class="mb-3">
                         <select class="form-control" name="agent_id">
                           <option value="">Select Agent</option>
                           <?php foreach ($agents as $agent_id => $nama): ?>
                             <option <?php echo $selected_agent_id == $agent_id ? 'selected':'' ?> value="<?php echo $agent_id ?>"><?php echo $nama ?></option>
                           <?php endforeach; ?>
                         </select>
                       </div>
                     </div> -->
                     <div class="col-md-4">
                       <div class="mb-3">
                         <select class="form-control" name="product_id">
                           <option value="">Select Product</option>
                           <?php foreach ($products as $product_id => $nama): ?>
                             <option <?php echo $selected_product_id == $product_id ? 'selected':'' ?> value="<?php echo $product_id ?>"><?php echo $nama ?></option>
                           <?php endforeach; ?>
                         </select>
                       </div>
                     </div>
                     <div class="col-md-3 text-end">
                       <button type="submit" class="btn btn-primary" style="width: 100%;" name="filtered">Sumbit</button>
                     </div>
                   </div>
                 </div>
               </form>
             </div>
           </div>
         </div>
         <div class="col-sm-12">
           <div class="card table-card">
             <div class="card-body">
               <div class=" p-4 pb-sm-2">
                 <!-- <a href="#" data-bs-toggle="offcanvas" data-bs-target="#add-product-form" aria-controls="add-product-form" class="btn btn-primary d-inline-flex align-items-center gap-2">
                   <i class="ti ti-plus f-18"></i> Add Product
                 </a> -->
                 <?php echo $paginator->itemsPerPage(); ?>
               </div>
               <div class="table-responsive">

                 <table class="table table-hover" id="pc-dt-simple">
                   <thead>
                     <tr>
                       <th class="text-center">#</th>
                       <th style="width: 60px;">DateTime</th>
                       <th class="text-center">Order ID</th>
                       <th style="width: 50px;">Product</th>
                       <th class="text-end">IN</th>
                       <th class="text-end">OUT</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php
                      // echo $row;
                      if ($result_c->num_rows > 0) {
                      // output data of each row
                      $i = 0+($paginator->currentPage-1)*$paginator->itemsPerPage;
                        // $i = 0;
                        while($row = $result_c->fetch_assoc()) {
                          $i++;
                      ?>
                       <tr>
                         <td class="text-center"><?php echo $i ?></td>
                         <td class=""><?php echo $row['purchased_at'] ?? $row['created_at'] ?></td>
                         <td class="text-center">Order#<?php echo $row['order_id'] ?? '-' ?></td>
                         <td>
                           <h6 class="mb-1"><?php echo shortenText($row['name'],18) ?></h6>
                         </td>
                         <td class="text-end"><?php echo $row['stock_in'] ?? '-' ?></td>
                         <td class="text-end"><?php echo $row['stock_out'] ?? '-' ?></td>
                       </tr>
                      <?php
                           }
                         } else {
                           ?>
                           <tr>
                             <td colspan="8">
                               <div class="text-center txt-oflo">
                                 Tiada Rekod.
                               </div>
                             </td>
                           </tr>
                           <?php
                         }
                         ?>
                   </tbody>
                 </table>
                 <?php echo $paginator->pageNumbers(); ?>

               </div>
             </div>
           </div>
         </div>
         <!-- [ sample-page ] end -->
       </div>
       <!-- [ Main Content ] end -->
     </div>
   </div>
   <!-- [ Main Content ] end -->
   <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="add-product-form" aria-labelledby="announcementLabel">
     <div class="offcanvas-header">
       <h5 class="offcanvas-title" id="announcementLabel">Add Prodduct</h5>
       <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
     </div>
     <div class="offcanvas-body">
       <form  method="post" enctype="multipart/form-data" >
         <div class="mb-3">
           <label class="form-label">Name</label>
           <input type="text" name="name" class="form-control" required>
         </div>
         <?php foreach ($ranks_ref as $rank_id => $rank): ?>
           <div class="mb-3 ">
             <label class="form-label bg-<?php echo $rank ?> p-1 rounded"><?php echo $rank ?> Price</label>
             <input type="number" name="new_rank_price[<?php echo $rank_id ?>]" value="" step="0.01" min="0.01" class="form-control" required>
           </div>
         <?php endforeach; ?>
         <div class="mb-3">
           <label class="form-label">Description</label>
           <textarea name="product_description" class="form-control"></textarea>
         </div>
         <div class="mb-3">
           <label class="form-label">Product Image</label>
           <input name="img" type="file" id="input-file-now" class="dropify mt-2" />
           <small>*Image will be resize to 500px </small>
         </div>

         <button  class="btn btn-sm btn-primary" type="submit" name="simpan_pengguna" >Submit</button>
         <input class="pull-right btn btn-sm btn-outline-primary" type="Reset" name="" value="Reset">
         <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="offcanvas">Cancel</button>
       </form>
     </div>
   </div>




   <?php include 'partial/footer.php'; ?>
   <?php include 'partial/scripts.php'; ?>

  <script src="../assets/js/plugins/sweetalert2.all.min.js"></script>
  <script src="../assets/plugins/dropify/dist/js/dropify.min.js"></script>
  <!-- <script src="../assets/js/plugins/datepicker-full.min.js"></script> -->
  <script src="../assets/js/plugins/flatpickr.min.js"></script>

   <script type="text/javascript">

    $(document).ready(function () {
       $('.dropify').dropify();
    });
   </script>

   <script src="../assets/js/plugins/simple-datatables.js"></script>
   <script>

   document.querySelectorAll('.datetime-picker-x').flatpickr({
     enableTime: false,
     noCalendar: false,
     dateFormat: "Y-m-d",

   });
     // const dataTable = new simpleDatatables.DataTable('#pc-dt-simple', {
     //   sortable: false,
     //   perPage: 5
     // });
     function deleteThisItem(the_item_form) {
       Swal.fire({
         title: 'Are you sure?',
         showDenyButton: true,
         showCancelButton: true,
         confirmButtonText: `Yes, delete it.`,
         denyButtonText: `No`
       }).then((result) => {
         if (result.isConfirmed) {
           $(the_item_form).submit();
           // Swal.fire('Saved!', '', 'success');
         } else if (result.isDenied) {
           // Swal.fire('Changes are not saved', '', 'info');
         }
       });
     }

   </script>

 </body>
 <!-- [Body] end -->

 </html>
