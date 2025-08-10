<?php
include 'core/init.php';
// include "../assets/plugins/resize_image.php";
$page_name = "Credit Terms History";
$url = "products.php";

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

if (isset($_POST['settle'])) {
  $id = $_POST['credit_id'];
  // $sql = "DELETE FROM product WHERE id = $id";
  $sql = "UPDATE credit_terms SET status = 'completed' WHERE id = $id";
  if (mysqli_query($db,$sql)) {
    $_SESSION['success'] = "Outstanding was successfully settled.";
    header("Location: $url");
    exit;
  }else {
    $_SESSION['error'] = "Action failed.";
    header("Location: $url");
    exit;
  }
}

// $result = $db->query("SELECT * FROM credit_terms WHERE status = 'outstanding' ORDER BY id DESC ");
// $totalRecords = mysqli_num_rows($result);
//
// include '../assets/plugins/paginator.php';
// $paginator = new Paginator();
// $paginator->total = $totalRecords;
// $paginator->paginate();
//
// $start = ($paginator->currentPage-1)*$paginator->itemsPerPage;
// $sql = "SELECT * FROM user WHERE status = 'Selesai Bayaran' AND track_no <> '' AND no_idp <> '88888888' $where_clause ORDER BY id DESC LIMIT $start,  $paginator->itemsPerPage";


// $sql = "SELECT * FROM credit_terms WHERE status = 'outstanding' ORDER BY id DESC LIMIT $start,  $paginator->itemsPerPage";
$sql = "SELECT *, ct.status as status, ct.created_at as created_at, ct.id as id FROM credit_terms ct
 JOIN user usr
 ON usr.id = ct.user_id
 WHERE ct.user_id = $user_id
 ORDER BY ct.id DESC ";

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
                 <h2 class="mb-0"><?php echo $page_name ?></h2>
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
               <!-- <div class="text-end p-4 pb-sm-2">
                 <a href="#" data-bs-toggle="offcanvas" data-bs-target="#add-product-form" aria-controls="add-product-form" class="btn btn-primary d-inline-flex align-items-center gap-2">
                   <i class="ti ti-plus f-18"></i> Add Product
                 </a>
               </div> -->
               <div class="table-responsive">
                 <table class="table table-hover" id="pc-dt-simple">
                   <thead>
                     <tr>
                       <th class="text-end">#</th>
                       <th>Status</th>
                       <th>Agent Detail</th>
                       <th class="text-center">Order Ref</th>
                       <th class="text-end">Outstanding</th>
                       <th class="text-center">Actions</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php
                      // echo $row;
                      if ($result_c->num_rows > 0) {
                      // output data of each row
                      // $i = 0+($paginator->currentPage-1)*$paginator->itemsPerPage;
                        $i = 0;
                        while($row = $result_c->fetch_assoc()) {
                          $i++;
                      ?>
                       <tr>
                         <td class="text-end"><?php echo $i ?></td>
                         <td>
                           <?php if ($row['status'] != 'outstanding'): ?>
                             <span class="badge bg-success">Paid</span>
                           <?php else: ?>
                             <span class="badge bg-danger">Outstanding</span>
                           <?php endif; ?>
                         </td>
                         <td>
                           <h6 class="mb-1"><?php echo $row['nama'] ?></h6>
                           <small><?php echo $row['no_tel'] ?></small>
                         </td>
                         <td class="text-center">
                           <a class="text-primary" href="order.php?id=<?php echo $row['order_id'] ?>">
                             INV#<?php echo $row['order_id'] ?>
                             <div class="">
                               <?php echo $row['created_at'] ?>
                             </div>

                           </a>
                         </td>
                         <td class="text-end"><?php echo Helper::MYR($row['outstanding_amount']) ?></td>
                         <td class="text-center">
                           <ul class="list-inline me-auto mb-0">
                             <!-- <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Whatsapp">
                               <a href="https://wa.me/<?php echo Helper::sanitizePhone($row['no_tel']) ?>" class="avtar avtar-xs btn-link-success btn-pc-default">
                                 <i class="ti ti-phone-call f-18"></i>
                               </a>
                             </li> -->
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="<?php echo strtoupper($row['status']) ?>#<?php echo $row['id'] ?>">
                               <?php if ($row['status'] == 'outstanding'): ?>
                                 <a href="pay_outstanding.php"  class="avtar avtar-xs btn-link-success btn-pc-default">
                                   <i class="ti ti-edit-circle f-18"></i>
                                 </a>
                               <?php else: ?>
                                 <i class="ti ti-circle-check text-success f-20"></i>

                               <?php endif; ?>
                             </li>

                           </ul>
                         </td>
                         <div class="modal fade " id="credit-action-<?php echo $row['id'];?>" tabindex="-1" role="dialog"
                           aria-labelledby="exampleModalRight" aria-hidden="true" style=" overflow-y: scroll;">
                           <div class="modal-dialog modal-dialog-centered " role="document">
                             <div class="modal-content">
                               <div class="modal-header">
                                 <h5 class="modal-title">Settlement</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                               </div>
                               <form id="admin_pengguna_form" name="admin_pengguna_form" role="form" method="post" onsubmit=""  enctype="multipart/form-data" >
                                 <div class="modal-body">

                                   <div class="form-group">
                                     Product: <b><?php echo $row['nama'] ?></b>
                                     <br>
                                     Balance: <b><?php echo $row['outstanding_amount'] ?></b>
                                     <br>
                                     <hr>
                                     <!-- <label class="control-label">Settle Outstanding</label> -->
                                     <button type="submit" name="settle" class="btn btn-block btn-primary mt-2" style="width: 100%;">Settle Outstanding</button>
                                   </div>

                                 </div>
                                 <div class="modal-footer">
                                   <input type="hidden" name="credit_id" value="<?php echo $row['id'] ?>">
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
