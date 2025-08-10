<?php
include 'core/init.php';
// include "../assets/plugins/resize_image.php";
$page_name = "Credit Terms Settlement";
$url = "credit_settlement_approval.php";

// if (isset($_POST['id_members_to_delete'])) {
//   $id = $_POST['id_members_to_delete'];
//   // $sql = "UPDATE user SET isDeleted = now() WHERE id = $id";
//   $sql = "DELETE FROM product WHERE id = $id";
//
//   if (mysqli_query($db,$sql)) {
//     $_SESSION['success'] = "The product was successfully deleted.";
//     header("Location: $url");
//     exit;
//   }else {
//     $_SESSION['error'] = "Action failed.";
//     header("Location: $url");
//     exit;
//   }
// }

if (isset($_POST['approve_payment'])) {


  $id = $_POST['credit_id'];
  $settlement_ref = $_POST['settlement_ref'];
  $user_id = $_POST['user_id'];

  $credit_info = Credit::getSettlementByRef($settlement_ref);

  if ($_POST['status'] == 'approve') {
    $approval_status = "now()";
    $outstanding_status = 'completed';
  }
  if ($_POST['status'] == 'rejected') {
    $approval_status = 'null';
    $outstanding_status = 'rejected';
  }

  if ($_POST['status'] == 'approve') {
    $user = User::getUser($user_id);
    if ($user['credit_used'] < $credit_info['amount']) {
      Telegram::sendMessage('87754515', "MANUAL - {$user_id} - {$settlement_ref} ");
      // $_SESSION['error'] = "Action failed. Please report to System Developer of this issue.";
      // header("Location: $url");
      // exit;
    }

  }

  $sql = "UPDATE credit_settlement SET approval_status = '$outstanding_status' , note = '{$_POST['note']}', paid_at = $approval_status WHERE settlement_ref = '$settlement_ref'";

  // echo $sql;

  if (mysqli_query($db,$sql)) {
    if ($_POST['status'] == 'approve') {

      $where_clause = "settlement_ref = '$settlement_ref'";


      $sql = "UPDATE credit_terms SET
      status = 'completed',
      payment_approval_status = '',
      settled_at = now()
      WHERE $where_clause";


      if (mysqli_query($db, $sql)) {
        $credit_used = Credit::calculateCreditUsed($user_id);
        $sql = "UPDATE user SET
        credit_used = $credit_used
        WHERE id = '$user_id'";
        mysqli_query($db, $sql);

      }


    }



    if ($_POST['status'] == 'rejected') {
      $where_clause = "settlement_ref = '$settlement_ref'";
      $sql = "UPDATE credit_terms SET
      status = 'outstanding',
      payment_approval_status = '',
      settled_at = null
      WHERE $where_clause";
      mysqli_query($db, $sql);
    }

    $_SESSION['success'] = "Payment Outstanding Approval was successfully updated.";
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
$sql = "SELECT *, cs.settlement_ref as settlement_ref FROM credit_terms ct
LEFT JOIN credit_settlement cs ON ct.settlement_ref = cs.settlement_ref
WHERE cs.approval_status = 'pending'";
$result_c = $db->query($sql);

if ($result_c->num_rows > 0) {
  while($row = $result_c->fetch_assoc()) {
    $pending[$row['settlement_ref']][] = "ORDER#{$row['order_id']} : <b>RM{$row['outstanding_amount']}</b>";
  }
}

// var_dump($pending);
// exit;


$sql = "SELECT
usr.id as user_id,
usr.nama as nama,
usr.no_tel as no_tel,
usr.credit_used as credit_used,
ct.datetime_transfer as datetime_transfer,
ct.resit_file as resit_file,
ct.amount as amount,
ct.type as type,
ct.settlement_ref as settlement_ref,
ct.id as id
FROM credit_settlement ct
 RIGHT JOIN user usr
 ON usr.id = ct.user_id
 WHERE ct.approval_status = 'pending'
 ORDER BY ct.id DESC ";
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
                       <th>Agent Detail</th>
                       <th class="text-center">Type</th>
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
                          $amount = $row['amount'] ?? $row['credit_used'];
                          $terms_list = null;
                          $termsCount = 0;
                          if (isset($pending[$row['settlement_ref']])) {
                            $termsCount = count($pending[$row['settlement_ref']]);
                            $terms_list = "<li>".implode ('</li><li>', $pending[$row['settlement_ref']]) . "</li>";
                          }
                      ?>
                       <tr>
                         <td class="text-end"><?php echo $i ?></td>
                         <td>
                           <h6 class="mb-1"><?php echo $row['nama'] ?><span class="text-white">#<?php echo $row['id'] ?></span> </h6>
                           <small><?php echo $row['no_tel'] ?></small>
                         </td>
                         <td class="text-center"><?php echo $row['type'] ?><?php echo $termsCount ? "<br>( $termsCount Orders ) " : '' ?></td>
                         <td class="text-end"><?php echo Helper::MYR($amount) ?></td>
                         <td class="text-center">
                           <ul class="list-inline me-auto mb-0">
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Whatsapp">
                               <a href="https://wa.me/<?php echo Helper::sanitizePhone($row['no_tel']) ?>" class="avtar avtar-xs btn-link-success btn-pc-default">
                                 <i class="ti ti-phone-call f-18"></i>
                               </a>
                             </li>
                             <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Outstanding #<?php echo $row['id'] ?>">
                               <a href="#"
                               onclick="viewSettlement('<?php echo $row['nama'] ?>',
                                                         '<?php echo $row['id'] ?>',
                                                         '<?php echo $row['resit_file'] ?>',
                                                         '<?php echo $row['datetime_transfer'] ?>',
                                                         '<?php echo $amount ?>',
                                                         '<?php echo $row['user_id'] ?>',
                                                         '<?php echo $row['settlement_ref'] ?>',
                                                         '<?php echo $terms_list ?>')"
                                class="avtar avtar-xs btn-link-success btn-pc-default">
                                 <i class="ti ti-edit-circle f-18"></i>
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
   <div class="modal fade " id="credit-action" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalRight" aria-hidden="true" style=" overflow-y: scroll;">
     <div class="modal-dialog modal-dialog-centered " role="document">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title">Settlement Approval</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

         </div>
         <form id="admin_pengguna_form" name="admin_pengguna_form" role="form" method="post" onsubmit=""  enctype="multipart/form-data" >
           <div class="modal-body">
             <div class="row">
               <div class="col-md-12">
                 <div class="mb-3">
                   <label class="form-label">Agent Name</label>
                   <input readonly id="nama" type="text" class="form-control" placeholder="Enter full name" />
                 </div>
               </div>
               <div class="col-md-6">
                 <div class="mb-3">
                   <label class="form-label">DateTime Transfer </label>
                   <input readonly id="date_time_transfer" type="text" class="form-control date_time_transfer_input" placeholder="Enter Phone No"/>
                 </div>
               </div>
               <div class="col-md-6">
                 <div class="mb-3">
                   <label class="form-label">Amount </label>
                   <input readonly id="outstanding_amount" type="text" class="form-control" placeholder="Enter Phone No"/>
                 </div>
               </div>
               <div class="col-md-12 mb-3" id="terms_list_wrapper" >
                 <label class="form-label">Order List </label>
                 <div style="" id="disply_list_term">

                 </div>
               </div>
               <div class="col-md-12">
                 <div class="mb-3">
                   <label class="form-label">Payment  </label>
                   <div class=" " style="border: 1px solid #eee;padding: 5px;">
                     <img id="img-resit" src="" alt="" width="100%">
                     <div id="pdf-resit" style="width:100%"></div>
                   </div>
                 </div>
               </div>
               <div class="col-12">
                 <div class="mb-3">
                   <label class="form-label">Approval</label>
                   <select class="form-control" name="status" required>
                     <option value="">Select Action</option>
                     <option value="approve">Approve</option>
                     <option value="rejected">Reject</option>
                   </select>
                 </div>
               </div>
               <div class="col-12">
                 <div class="mb-3">
                   <label class="form-label">Approval Note</label>
                   <textarea name="note" class="form-control" rows="4" cols="80"></textarea>
                   <small>Kindly put rejection note for future reference</small>
                 </div>
               </div>
             </div>
           </div>
           <div class="modal-footer">
             <input type="hidden" id="settlement_ref" name="settlement_ref" value="">
             <input type="hidden" id="credit_id" name="credit_id" value="">
             <input type="hidden" id="user_id" name="user_id" value="">
             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
             <button type="submit" name="approve_payment" class="btn btn-primary">Save changes</button>
           </div>
         </form>
         </form>
       </div>
     </div>
   </div>

   <!-- [ Main Content ] end -->

   <?php include 'partial/footer.php'; ?>
   <?php include 'partial/scripts.php'; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js"></script>

   <script type="text/javascript">

    // $(document).ready(function () {
    //    $('.dropify').dropify();
    // });
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

     function viewSettlement(nama, id, resit_file, date_time_transfer, amount, user_id, ref, list) {

       $("#img-resit").attr('src', '');
       $("#pdf-resit").html('');
       $("#terms_list_wrapper").hide()
       $("#disply_list_term").html('');

       $("#pdf-resit").css('height', '0px')


       $("#nama, #order_id, #order_ref, .date_time_transfer_input, #total_qty, #all_items_price").val('');
       $("#nama").val(nama);
       $("#credit_id").val(id);
       $("#settlement_ref").val(ref);
       $("#user_id").val(user_id);
       if (list) {
         $("#terms_list_wrapper").show()
         $("#disply_list_term").html(list);

       }
       $(".date_time_transfer_input").val(date_time_transfer);
       $("#outstanding_amount").val(amount);

       if (resit_file) {
         var appSubdomain = "https://<?php echo $domain ?>";
         var path = appSubdomain+"/uploads/credit_settlement/"+resit_file;
         // if (!ImageExist(path)) {
         //   path = appPath;
         // }
         var ext = resit_file.substr(resit_file.lastIndexOf('.') + 1);
         // var ext = 'asd';


         if (ext == 'pdf') {
           $("#pdf-resit").css('height', '500px')
           PDFObject.embed(path, "#pdf-resit");
         }else {
           $("#img-resit").prop('src', path)
         }
       }

       $("#credit-action").modal('show');
     }
   </script>

 </body>
 <!-- [Body] end -->

 </html>
