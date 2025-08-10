<?php

include 'core/init.php';
include "../assets/plugins/resize_image.php";
$page_name = "Product";
$url = "products.php";

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
            $product_id  = mysqli_insert_id($db);

            Product::insertRankPrice($product_id, $_POST['new_rank_price']);

            $big_img = getImageSizeKeepAspectRatio($file_tmp, 600, 600);
            $thumbs_img = getImageSizeKeepAspectRatio($file_tmp, 100, 100);
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
          Product::updateRankPrice($_POST['update_rank_price']);
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
    $sql_insert_log = "INSERT INTO product_stock_log ( product_id, stock_in, created_at )
    VALUES ('$product_id', '$quantity', now())";
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
    $_SESSION['error'] = "Stock Details is not complete.";
    header("Location: $url");
    exit;
  }
}

if (isset($_POST['deduct_stock'])) {
  $product_id = $_POST['product_id'];
  $quantity = $_POST['stock_out'];

  if ($quantity) {
    $sql_insert_log = "INSERT INTO product_stock_log ( product_id, stock_out, created_at )
    VALUES ('$product_id', '$quantity', now())";

    $sql = "UPDATE product SET stock_balance = stock_balance - $quantity WHERE id = $product_id";

    if(mysqli_query($db, $sql_insert_log)&&mysqli_query($db, $sql))
    {
      $_SESSION['success'] = "Product was successfully deducted.";
      header("Location: $url");
      exit;
    }else {
      $_SESSION['error'] = "Stock deduction failed.";
      header("Location: $url");
      exit;
    }
  }else {
    $_SESSION['error'] = "Stock Details is not complete.";
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


$result = $db->query("SELECT * FROM product ORDER BY id DESC ");
$totalRecords = mysqli_num_rows($result);

include '../assets/plugins/paginator.php';
$paginator = new Paginator();
$paginator->total = $totalRecords;
$paginator->paginate();

$start = ($paginator->currentPage-1)*$paginator->itemsPerPage;
// $sql = "SELECT * FROM user WHERE status = 'Selesai Bayaran' AND track_no <> '' AND no_idp <> '88888888' $where_clause ORDER BY id DESC LIMIT $start,  $paginator->itemsPerPage";

$sqlPrice = "SELECT * FROM product_rank_price ";
$result_price = $db->query($sqlPrice);
$rank_price = [];
while($rowx = $result_price->fetch_assoc()) {
  $rank_price[$rowx['product_id']][$rowx['rank_id']] = $rowx['price'];

}


$sql = "SELECT * FROM product ORDER BY id DESC LIMIT $start,  $paginator->itemsPerPage";

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
                 <h2 class="mb-0">Products list</h2>
               </div>
             </div>
           </div>
         </div>
       </div>
       <div class="row">
         <!-- [ sample-page ] start -->
         <div class="col-sm-12">
           <div class="card table-card">
             <div class="card-body">
               <div class="text-end p-4 pb-sm-2">
                 <a href="#" data-bs-toggle="offcanvas" data-bs-target="#add-product-form" aria-controls="add-product-form" class="btn btn-primary d-inline-flex align-items-center gap-2">
                   <i class="ti ti-plus f-18"></i> Add Product
                 </a>
               </div>
               <div class="table-responsive">
                 <table class="table table-hover" id="pc-dt-simple">
                   <thead>
                     <tr>
                       <th class="text-end">#</th>
                       <th>Product Detail</th>
                       <th class="text-end">Retail Price</th>
                       <?php foreach ($ranks_ref as $rank_id => $rank): ?>
                         <th class="text-end"><?php echo $rank ?> Price</th>
                       <?php endforeach; ?>
                       <th class="text-end">Balance</th>
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
                           <div class="row">
                             <div class="col-auto pe-0">
                               <?php if ($row['product_img']): ?>
                                 <img src="../uploads/product/thumbs/<?php echo $row['product_img'] ?>" alt="user-image" class="wid-40 rounded" />
                                 <?php else: ?>
                                 <img src="../assets/images/application/img-prod-2.jpg" alt="user-image" class="wid-40 rounded" />

                               <?php endif; ?>
                             </div>
                             <div class="col">
                               <h6 class="mb-1"><?php echo $row['name'] ?></h6>
                               <!-- <p class="text-muted f-12 mb-0">Mic(Bluetooth 4.2, Rockerz 450R</p> -->
                             </div>
                           </div>
                         </td>
                         <td class="text-end">RM<?php echo number_format($row['unit_price'], 2) ?></td>
                         <?php foreach ($ranks_ref as $rank_id => $rank): ?>
                           <td class="text-end"><?php echo isset($rank_price[$row['id']][$rank_id]) ? "RM".number_format($rank_price[$row['id']][$rank_id], 2) : 'Not Set'?></td>
                         <?php endforeach; ?>
                         <td class="text-end"><?php echo $row['stock_balance'] ?></td>
                         <td class="text-center">
                           <ul class="list-inline me-auto mb-0">
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="View">
                               <a
                                 href="#"
                                 class="avtar avtar-xs btn-link-secondary btn-pc-default"
                                 data-bs-toggle="modal"
                                 data-bs-target="#add_stock<?php echo $row['id'];?>"
                               >
                                 <i class="ti ti-square-plus f-18"></i>
                               </a>
                             </li>
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="View">
                               <a
                                 href="#"
                                 class="avtar avtar-xs btn-link-secondary btn-pc-default"
                                 data-bs-toggle="modal"
                                 data-bs-target="#deduct_stock<?php echo $row['id'];?>"
                               >
                                 <i class="ti ti-square-minus f-18"></i>
                               </a>
                             </li>
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                               <a href="#" data-bs-toggle="offcanvas" data-bs-target="#edit-product-form-<?php echo $row['id'];?>" class="avtar avtar-xs btn-link-success btn-pc-default">
                                 <i class="ti ti-edit-circle f-18"></i>
                               </a>
                             </li>
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                               <form id="form_product<?php echo $row['id'];?>" method="post">

                                 <input type="hidden" name="id_members_to_delete" value="<?php echo $row['id']; ?>">
                                 <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default"  onclick="deleteThisItem('#form_product<?php echo $row['id'];?>')">

                               </form>

                                 <i class="ti ti-trash f-18"></i>
                               </a>
                             </li>
                           </ul>
                         </td>
                         <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="edit-product-form-<?php echo $row['id'];?>" aria-labelledby="announcementLabel">
                           <div class="offcanvas-header">
                             <h5 class="offcanvas-title" id="announcementLabel">Edit Prodduct</h5>
                             <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                           </div>
                           <div class="offcanvas-body">
                             <form  method="post" enctype="multipart/form-data" >
                               <div class="mb-3">
                                 <label class="form-label">Name</label>
                                 <input type="text" name="name" value="<?php echo $row['name'] ?>" class="form-control" required>
                               </div>
                               <div class="mb-3 ">
                                 <label class="form-label">Unit Price</label>
                                 <input type="number" name="unit_price" value="<?php echo $row['unit_price'] ?>" step="0.01" min="0.01" class="form-control" required>
                               </div>
                               <?php foreach ($ranks_ref as $rank_id => $rank): ?>
                                 <div class="mb-3 ">
                                   <label class="form-label"><?php echo $rank ?> Price</label>
                                   <input type="number" name="update_rank_price[<?php echo $row['id'] ?>][<?php echo $rank_id ?>]" value="<?php echo isset($rank_price[$row['id']][$rank_id]) ? $rank_price[$row['id']][$rank_id] : '0'?>" step="0.01" min="0.01" class="form-control" required>
                                 </div>
                               <?php endforeach; ?>
                               <div class="mb-3">
                                 <label class="form-label">Description</label>
                                 <textarea name="product_description" class="form-control"><?php echo $row['product_description'] ?></textarea>
                               </div>
                               <div class="mb-3">
                                 <label class="form-label">Product Image</label>
                                 <input name="img" type="file" id="input-file-now" class="dropify mt-2" />
                                 <small>*Image will be resize to 500px </small>
                               </div>
                               <input type="hidden" name="product_id" value="<?php echo $row['id'] ?>">
                               <button  class="btn btn-sm btn-primary" type="submit" name="update_product" >Submit</button>
                               <input class="pull-right btn btn-sm btn-outline-primary" type="Reset" name="" value="Reset">
                               <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="offcanvas">Cancel</button>
                             </form>
                           </div>
                         </div>
                         <div class="modal fade " id="add_stock<?php echo $row['id'];?>" tabindex="-1" role="dialog"
                           aria-labelledby="exampleModalRight" aria-hidden="true" style=" overflow-y: scroll;">
                           <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                             <div class="modal-content">
                               <div class="modal-header">
                                 <h5 class="modal-title" id="">Add Stock</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                               </div>
                               <form id="admin_pengguna_form" name="admin_pengguna_form" role="form" method="post" onsubmit=""  enctype="multipart/form-data" >
                                 <div class="modal-body">

                                   <div class="form-group">
                                     Product: <b><?php echo $row['name'] ?></b>
                                     <br>
                                     Balance: <b><?php echo $row['stock_balance'] ?></b>
                                     <br>
                                     <hr>
                                     <label class="control-label">Add Stock</label>
                                     <input type="number" name="stock_in" class="form-control" required>
                                   </div>

                                 </div>
                                 <div class="modal-footer">
                                   <input type="hidden" name="product_id" value="<?php echo $row['id'] ?>">
                                   <button  class="btn btn-sm btn-primary" type="submit" name="update_stock" >Submit</button>
                                   <input class="pull-right btn btn-sm btn-outline-primary" type="Reset" name="" value="Reset">
                                   <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                                 </div>
                               </form>
                             </div>
                           </div>
                         </div>
                         <div class="modal fade " id="deduct_stock<?php echo $row['id'];?>" tabindex="-1" role="dialog"
                           aria-labelledby="exampleModalRight" aria-hidden="true" style=" overflow-y: scroll;">
                           <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                             <div class="modal-content">
                               <div class="modal-header">
                                 <h5 class="modal-title">Deduct Stock</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                               </div>
                               <form id="admin_pengguna_form" name="admin_pengguna_form" role="form" method="post" onsubmit=""  enctype="multipart/form-data" >
                                 <div class="modal-body">

                                   <div class="form-group">
                                     Product: <b><?php echo $row['name'] ?></b>
                                     <br>
                                     Balance: <b><?php echo $row['stock_balance'] ?></b>
                                     <br>
                                     <hr>
                                     <label class="control-label">Deduct Stock</label>
                                     <input type="number" name="stock_out" class="form-control" required>
                                   </div>

                                 </div>
                                 <div class="modal-footer">
                                   <input type="hidden" name="product_id" value="<?php echo $row['id'] ?>">
                                   <button  class="btn btn-sm btn-primary" type="submit" name="deduct_stock" >Submit</button>
                                   <input class="pull-right btn btn-sm btn-outline-primary" type="Reset" name="" value="Reset">
                                   <button type="button" class="btn btn-sm btn-outline-primary" data-b-dismiss="modal">Cancel</button>
                                 </div>
                               </form>
                             </div>
                           </div>
                         </div>
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
         <div class="mb-3 ">
           <label class="form-label">Unit Price</label>
           <input type="number" name="unit_price" step="0.01" min="0.01" class="form-control" required>
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
