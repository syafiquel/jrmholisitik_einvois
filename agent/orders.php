<?php

include 'core/init.php';
$page_name = "Orders";
$url = "orders.php";

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
               <div class="table-responsive ">
                <div class="p-2">
                  <?php echo $paginator->itemsPerPage(); ?>
                </div>

                 <table class="table table-hover" id="pc-dt-simple">
                   <thead>
                     <tr>
                       <th class="text-end">#</th>
                       <th>Reference</th>
                       <th>Type</th>
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
                         <td class=""><?php echo $row['shipping_method'] ?></td>
                         <td class="text-end">RM<?php echo number_format($row['all_items_price'], 2) ?></td>
                         <td class="text-center">
                           <?php if ($row['paid_at']): ?>
                             <?php if ($row['credit_outstanding'] == 'outstanding'): ?>
                               <span class="badge bg-warning">Pending Payment</span>
                             <?php else: ?>
                               <span class="badge bg-success">Paid</span>
                             <?php endif; ?>
                           <?php else: ?>
                             <span class="badge bg-secondary">Unpaid</span>
                           <?php endif; ?>
                         </td>
                         <td>
                           <?php if ($row['payment_type'] == 'Online'): ?>
                             <span class="badge bg-light-info f-12">Billplz</span>
                           <?php elseif ($row['payment_type'] == 'Credit Terms'): ?>
                             <?php $outstandingStatus = $row['credit_outstanding'] == 'outstanding' ? 'danger':'success';  ?>
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
     const dataTable = new simpleDatatables.DataTable('#pcs-dt-simple', {
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
