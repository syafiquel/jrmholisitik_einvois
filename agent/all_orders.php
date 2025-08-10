<?php

include 'core/init.php';
$page_name = "Orders";
$url = "orders.php";

if (isset($_POST['simpan_pengguna'])){
  $name = $_POST['name'];
  $unit_price = $_POST['unit_price'];
  $product_description = $_POST['product_description'];
  $img = $_FILES['img'];

  if ( !empty($name)
    && !empty($unit_price)
    && !empty($img) ){

    if(isset($_FILES['img']) && $_FILES['img']['size'] > 0){
      //  echo 'dah masyuk upload pls<br>';
      $file_name = $_FILES['img']['name'];
      $file_size =$_FILES['img']['size'];
      $file_tmp =$_FILES['img']['tmp_name'];
      $file_type=$_FILES['img']['type'];
      $tmp = explode('.', $file_name);
      $file_extension = end($tmp);
      $file_name_rand = round(microtime(true));
      $newfilename = $file_name_rand . '.' . $file_extension;
      // $newfilename_thumb = $file_name_rand . '_thumb.' . $file_extension;
      $file_ext=strtolower($file_extension);

      $expensions= array("jpeg","jpg","png", "webp");

      if(in_array($file_ext,$expensions)=== false){
        $errors_uploading[]="extension not allowed, please choose a JPEG or PNG file.";
      }

      // if($file_size > 2097152){
      //    $errors_uploading[]='File size must be less than 2 MB';
      // }

      $img_url_uploaded = $newfilename;

      if(empty($errors_uploading)==true){
        //   echo "VALUES('$username','', ' ', '$no_kp', '$email ', '$no_tel ', '$created_at', '88888888', 'Tiada Bayaran', '$img_url', '', '$no_idp'";

        $sql =  "INSERT INTO product
        ( name,
          unit_price,
          product_description,
          product_img,
          created_at,
          updated_at
        )"

        . " VALUES(
          '$name',
          '$unit_price',
          '$product_description',
          '$newfilename',
          now(),
          now()
          )";

          // echo $sql;
          // exit;
          if(mysqli_query($db, $sql))
          {
            $big_img = getImageSizeKeepAspectRatio($file_tmp, 600, 600);
            $thumbs_img = getImageSizeKeepAspectRatio($file_tmp, 30, 30);
            resize($file_tmp, "../uploads/product/". $newfilename, $big_img['width'], $big_img['height']);
            resize($file_tmp, "../uploads/product/thumbs/". $newfilename, $thumbs_img['width'], $thumbs_img['height']);
            // move_uploaded_file($file_tmp,"../images/uploads/".$newfilename);
            $_SESSION['success'] = "Product $name was successfully added.";
            header("Location: $url");
            exit;
          }else{
            $_SESSION['error'] = "Failed to add product. Please fill all details of the product. Please try again.";
            header("Location: $url");
            exit;
          }

        }else{
          $errors = "<li>Terdapat isu dengan Gambar.</li>";
          $_SESSION['error'] = "Invalid image. Use format: JPG, JPEG & PNG. Please try again.";
          header("Location: $url");
          exit;
        }
      }
      else
      {
        $errors = "<li>Product Image is mandatory.</li>";
        $_SESSION['error'] = "Product Image is mandatory. Please try again";
        header("Location: $url");
        exit;
      }
   }
   $_SESSION['error'] = $errors;
   header("Location: $url");
   exit;

}

if (isset($_POST['update_product'])){
  $product_id = $_POST['product_id'];
  $name = $_POST['name'];
  $unit_price = $_POST['unit_price'];
  $product_description = $_POST['product_description'];
  $img = $_FILES['img'];

  if ( !empty($name)
    && !empty($unit_price) ){

    if(isset($_FILES['img']) && $_FILES['img']['size'] > 0){
      //  echo 'dah masyuk upload pls<br>';
      $file_name = $_FILES['img']['name'];
      $file_size =$_FILES['img']['size'];
      $file_tmp =$_FILES['img']['tmp_name'];
      $file_type=$_FILES['img']['type'];
      $tmp = explode('.', $file_name);
      $file_extension = end($tmp);
      $file_name_rand = round(microtime(true));
      $newfilename = $file_name_rand . '.' . $file_extension;
      // $newfilename_thumb = $file_name_rand . '_thumb.' . $file_extension;
      $file_ext=strtolower($file_extension);

      $expensions= array("jpeg","jpg","png");

      if(in_array($file_ext,$expensions)=== false){
        $errors_uploading[]="extension not allowed, please choose a JPEG or PNG file.";
      }

      // if($file_size > 2097152){
      //    $errors_uploading[]='File size must be less than 2 MB';
      // }

      $img_url_uploaded = $newfilename;

      if(empty($errors_uploading)==true){
        //   echo "VALUES('$username','', ' ', '$no_kp', '$email ', '$no_tel ', '$created_at', '88888888', 'Tiada Bayaran', '$img_url', '', '$no_idp'";
        $uploaded_img = "product_img = '$newfilename', ";

      }else{
        $errors = "<li>Terdapat isu dengan Gambar.</li>";
        $_SESSION['error'] = "Invalid image. Use format: JPG, JPEG & PNG. Please try again.";
        header("Location: $url");
        exit;
      }
    }

    $sql =  "UPDATE product SET
      name = '$name',
      unit_price = '$unit_price',
      $uploaded_img
      product_description = '$product_description',
      updated_at = now()
      WHERE id = $product_id";

      // echo $sql;
      // exit;
      if(mysqli_query($db, $sql))
      {
          if(isset($_FILES['img']) && $_FILES['img']['size'] > 0){
          $big_img = getImageSizeKeepAspectRatio($file_tmp, 600, 600);
          $thumbs_img = getImageSizeKeepAspectRatio($file_tmp, 30, 30);
          resize($file_tmp, "../uploads/product/". $newfilename, $big_img['width'], $big_img['height']);
          resize($file_tmp, "../uploads/product/thumbs/". $newfilename, $thumbs_img['width'], $thumbs_img['height']);
        }
        $_SESSION['success'] = "Product was successfully restock.";
        header("Location: $url");
        exit;
      }else{
        $_SESSION['error'] = "Failed to update product. Please fill all details of the product. Please try again.";
        header("Location: $url");
        exit;
      }

   }
   $_SESSION['error'] = "Product Details is not complete.";
   header("Location: $url");
   exit;

}

if (isset($_POST['update_stock'])) {
  $product_id = $_POST['product_id'];
  $quantity = $_POST['stock_in'];

  if ($quantity) {
    $sql_insert_log = "INSERT INTO product_stock_log ( product_id, order_id, stock_in, created_at )
    VALUES ('$product_id', '$order_id', '$quantity', now())";

    $sql = "UPDATE product SET stock_balance = stock_balance + $quantity WHERE id = $product_id";

    if(mysqli_query($db, $sql_insert_log)&&mysqli_query($db, $sql))
    {
      $_SESSION['success'] = "Product was successfully restock.";
      header("Location: $url");
      exit;
    }else {
      $_SESSION['error'] = "Restock failed.";
      header("Location: $url");
      exit;
    }
  }else {
    $_SESSION['error'] = "Stokc Details is not complete.";
    header("Location: $url");
    exit;
  }
}

if (isset($_POST['id_members_to_delete'])) {
  $id = $_POST['id_members_to_delete'];
  // $sql = "UPDATE user SET isDeleted = now() WHERE id = $id";
  $sql = "DELETE FROM product WHERE id = $id";

  if (mysqli_query($db,$sql)) {
    $_SESSION['success'] = "The product was successfully deleted.";
    header("Location: $url");
    exit;
  }else {
    $_SESSION['error'] = "Action failed.";
    header("Location: $url");
    exit;
  }
}

$sqlBase = "SELECT po.*, ct.status as credit_outstanding
FROM purchase_order po
LEFT JOIN credit_terms ct
ON ct.order_id = po.id
WHERE po.user_id = '$user_id' ORDER BY po.id DESC ";
$result = $db->query($sqlBase);
$totalRecords = mysqli_num_rows($result);

include '../assets/plugins/paginator.php';
$paginator = new Paginator();
$paginator->total = $totalRecords;
$paginator->paginate();

$start = ($paginator->currentPage-1)*$paginator->itemsPerPage;
// $sql = "SELECT * FROM user WHERE status = 'Selesai Bayaran' AND track_no <> '' AND no_idp <> '88888888' $where_clause ORDER BY id DESC LIMIT $start,  $paginator->itemsPerPage";
$sql = "$sqlBase LIMIT $start,  $paginator->itemsPerPage";

$result_c = $db->query($sql);
 ?>

 <!doctype html>
 <html lang="en">
 <!-- [Head] start -->

 <head>
   <!-- [Meta] -->
   <?php include 'partial/header.php'; ?>
   <link rel="stylesheet" href="../assets/plugins/dropify/dist/css/dropify.min.css">
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
           <div class="page-header-title">
             <h2 class="mb-0">My Orders</h2>
           </div>
         </div>
       </div>
       <div class="row">
         <!-- [ sample-page ] start -->
         <div class="col-sm-12">
           <div class="card table-card">
             <div class="card-body">
               <div class="table-responsive">
                 <table class="table table-hover" id="pc-dt-simple">
                   <thead>
                     <tr>
                       <th class="text-end">#</th>
                       <th>Reference</th>
                       <th>Shipping Option</th>
                       <th class="text-center">Delivery Status</th>
                       <th class="text-end">Price</th>
                       <th class="text-center">Status</th>
                       <th>Via</th>
                       <th class="text-center">Actions</th>
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
                         <td class="text-end"><?php echo $i ?></td>
                         <td>
                           <a href="order.php?id=<?php echo $row['id'] ?>">
                             <h6 class="mb-1 text-primary">
                               INV#<?php echo $row['id'] ?>
                               <br>
                               <?php echo $row['paid_at'] ?? '-' ?>
                             </h6>
                           </a>
                         </td>
                         <td class=""><?php echo $row['pickup_datetime'] ? 'Pick Up': 'Delivery'?></td>
                         <td class="text-end">RM<?php echo number_format($row['all_items_price'], 2) ?></td>
                         <td class="text-center">
                           <?php if ($row['paid_at']): ?>
                             <span class="badge bg-success">Paid</span>
                           <?php else: ?>
                             <span class="badge bg-secondary">Unpaid</span>
                           <?php endif; ?>
                         </td>
                         <td>
                           <?php if ($row['payment_type'] == 'Online'): ?>
                             <span class="badge bg-light-info f-12">Billplz</span>
                           <?php elseif ($row['payment_type'] == 'Credit Terms'): ?>
                             <?php $outstandingStatus = $row['credit_outstanding'] == 'outstanding' ? 'success':'danger';  ?>
                             <span class="badge bg-light-<?php echo "$outstandingStatus";?> f-12">Credit Terms</span>
                             <?php else: ?>
                               -
                           <?php endif; ?>
                         </td>
                         <td class="text-center">
                           <ul class="list-inline me-auto mb-0">
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="View INV#<?php echo $row['id'] ?>">
                               <a
                                 href="order.php?id=<?php echo $row['id'] ?>"
                                 class="avtar avtar-xs btn-link-secondary btn-pc-default"
                               >
                                 <i class="ti ti-eye f-18"></i>
                               </a>
                             </li>
                           </ul>
                         </td>
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
   <?php include 'partial/footer.php'; ?>
   <?php include 'partial/scripts.php'; ?>

  <script src="../assets/js/plugins/sweetalert2.all.min.js"></script>

   <script src="../assets/plugins/dropify/dist/js/dropify.min.js"></script>
   <script type="text/javascript">

    $(document).ready(function () {
       $('.dropify').dropify();
    });
   </script>

   <script src="../assets/js/plugins/simple-datatables.js"></script>
   <script>
     const dataTable = new simpleDatatables.DataTable('#pc-dt-simple', {
       sortable: false,
       perPage: 5
     });
     function deleteThisItem(the_item_form) {
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
       // Swal.fire('The Internet?', 'That thing is still around?', 'question');
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
