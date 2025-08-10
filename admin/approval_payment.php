<?php
include 'core/init.php';
$page_name = "Payment Approval Orders";
$url = "approval_payment.php";

if (isset($_POST['approve_payment'])) {
  $order_id = $_POST['order_id'];
  $status = $_POST['status'];
  $cancel_note = $_POST['cancel_note'];
  $date_time_transfer = $_POST['date_time_transfer'];

  if ($status == 'approve') {
    $approval = "isApproved =  now(), paid_at = '$date_time_transfer'";
  }else {
    $approval = "isCanceled =  now(), cancel_reason = '$cancel_note'";
  }

  $sql =  "UPDATE purchase_order SET
  $approval
  WHERE id = '$order_id'";
  // echo $sql;
  // exit;
  if(mysqli_query($db, $sql))
  {
    require_once '../lib/php-myinvois/autoload.php';
    require_once '../lib/php-myinvois/src/helper.php';
    // Assuming you have a function to get invoice data
    $invoiceData = getInvoiceData($order_id, $db);

    // All the required classes will be loaded by the autoloader
    $config = require '../lib/php-myinvois/config.php';
    // You will need to implement the client and builder classes
    // For now, we are using dummy classes
    $client = new MyInvois\Core\MyInvoisClient($config);
    $ublBuilder = new MyInvois\Ubl\UblBuilder();

    $facade = new MyInvois\MyInvoisFacade($client, $ublBuilder);

    $submissionResult = $facade->submitInvoice($invoiceData);

    if ($submissionResult['success']) {
        $submissionId = $submissionResult['submissionId'];
        $sql = "UPDATE purchase_order SET myinvois_submission_id = '$submissionId' WHERE id = '$order_id'";
        mysqli_query($db, $sql);
    }

    $_SESSION['success'] = "Payment file has been uploaded. Once payment is approved, we will notified you.";
    header("Location: $url");
    exit;
  }else {
    // order rejected
    // Restore stock and remove from reserved
    $sql = "SELECT * FROM order_details WHERE order_id = $order_id";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $quantity = $row['quantity'];
        $product_id = $row['product_id'];
        $sql_update = "UPDATE product SET stock_balance = stock_balance + $quantity WHERE id = $product_id";
        $db->query($sql_update);
    }
    $_SESSION['error'] = "There error when processing data. Please try again.";
    header("Location: $url");
    exit;
  }

}



 ?>

 <!doctype html>
 <html lang="en">
 <!-- [Head] start -->

 <head>
   <!-- [Meta] -->
   <?php include 'partial/header.php'; ?>
   <link rel="stylesheet" href="../assets/plugins/dropify/dist/css/dropify.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.css" />
   <link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css" />
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
             <div class="card-body p-2">
               <form method="post">

                 <div class=" dt-responsive table-responsive">

                   <table class="table table-hover table-bordered" id="table-orders">
                     <thead>
                       <!-- <tr id="bulkActionWrapper" style="display:none;" >
                        <td colspan="6" class="no-sort">
                            <div style="">

                              <button type="button" name="button" class="btn btn-warning btn-sm">
                                <span id="selectBerasCount">0</span> Order(s) Selected
                              </button>
                              <input type="hidden" name="checkedIds" id="checkedIds" value="">
                              <input type="hidden" name="checkedBeras" id="checkedBeras" value="">
                              <button type="button" class="btn btn-primary btn-sm" name="button" onclick="updateMultipleEdaran()">Update Status</button>
                              <button type="button" class="btn btn-primary btn-sm" name="button" onclick="updatePenerima('multi')">Print Penerima</button>
                            </div>
                          </td>
                        </tr> -->

                       <tr>
                         <th class=" no-sort text-center" > <input id="checkedAll" type="checkbox" name="all" value="" > </th>
                         <th class="text-center">#</th>
                         <th class="text-center">Agent</th>
                         <th class="text-center">Status</th>
                         <th class="text-center">Total</th>
                         <th class="text-center">Actions</th>
                       </tr>
                     </thead>
                     <tbody>

                     </tbody>
                   </table>

                 </div>
                </form>


             </div>
           </div>
         </div>
         <!-- [ sample-page ] end -->
       </div>
       <!-- [ Main Content ] end -->

       <!-- approvel form modal -->
       <div
         id="modalApproval"
         class="modal fade"
         tabindex="-1"
         role="dialog"
         aria-labelledby=""
         aria-hidden="true"
       >
         <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
           <div class="modal-content">
             <div class="modal-header">
               <h5 class="modal-title" id="">Approval Payemnt </h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <form method="post">

               <div class="modal-body">
                 <div class="row">
                   <div class="col-md-12">
                     <div class="mb-3">
                       <label class="form-label">Agent Name</label>
                       <input id="nama" type="text" class="form-control" placeholder="Enter full name" />
                     </div>
                   </div>
                   <div class="col-md-6">
                     <div class="mb-3">
                       <label class="form-label">Order Ref</label>
                       <input id="order_ref" type="text" class="form-control" placeholder="Enter email" />
                     </div>
                   </div>
                   <div class="col-md-6">
                     <div class="mb-3">
                       <label class="form-label">DateTime Transfer </label>
                       <input id="date_time_transfer" type="text" class="form-control date_time_transfer_input" placeholder="Enter Phone No"/>
                     </div>
                   </div>
                   <div class="col-md-6">
                     <div class="mb-3">
                       <label class="form-label">Total Qty </label>
                       <input id="total_qty" type="text" class="form-control" placeholder="Enter Phone No"/>
                     </div>
                   </div>
                   <div class="col-md-6">
                     <div class="mb-3">
                       <label class="form-label">Amount </label>
                       <input id="all_items_price" type="text" class="form-control" placeholder="Enter Phone No"/>
                     </div>
                   </div>
                   <div class="col-md-12">
                     <div class="mb-3">
                       <label class="form-label">Payment  </label>
                       <img id="img-resit" src="" alt="" width="100%">
                       <div id="pdf-resit" style="width:100%"></div>
                     </div>
                   </div>
                   <div class="col-12">
                     <div class="mb-3">
                       <label class="form-label">Approval</label>
                       <select class="form-control" name="status">
                         <option value="">Select Action</option>
                         <option value="approve">Approve</option>
                         <option value="rejected">Reject</option>
                       </select>
                     </div>
                   </div>
                   <div class="col-12">
                     <div class="mb-3">
                       <label class="form-label">Approval Note</label>
                       <textarea name="cancel_note" class="form-control" rows="4" cols="80"></textarea>
                     </div>
                   </div>
                 </div>
               </div>
               <div class="modal-footer">
                 <input type="hidden" class="date_time_transfer_input"  name="date_time_transfer" value="">
                 <input type="hidden" id="order_id" name="order_id" value="">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button type="submit" name="approve_payment" class="btn btn-primary">Save changes</button>
               </div>
             </form>
           </div>
         </div>
       </div>
       <!-- end approvel form modal -->

     </div>
   </div>
   <!-- [ Main Content ] end -->

   <?php include 'partial/footer.php'; ?>
   <?php include 'partial/scripts.php'; ?>

  <script src="../assets/plugins/dropify/dist/js/dropify.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"
    ></script>
   <!-- <script src="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.js"></script> -->
   <script src="../assets/js/plugins/dataTables.min.js"></script>
   <script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
   <!-- <script src="../assets/plugins/datatables/multi-checkbox/js/dataTables.checkboxes.min.js"></script> -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js"></script>

   <script>
    $(document).ready(function() {
     $('.dropify').dropify();
    });

    var tableOrder = $('#table-orders').on('init.dt', function () {
        $(".dt-empty").text('No data available');
        console.log('Table initialisation complete: ' + new Date().getTime());
    }).DataTable({
      "language": {
            "processing"  : "<span class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></span>",
            "emptyTable"  : "No data available in table",
            "infoEmpty"   : "No records available",
            "zeroRecords" :    "No matching records found",
      }, // you can put text or html here in the language.processing setting.
      "processing": true, // you have to set this to true as well
      serverSide: true,
      ajax: {
        url: '../shared/orders/get_orders_for_approval.php',
        type: 'POST'
      },
      pageLength: 10,
      lengthMenu: [
        5, 10, 20, 50
      ],
      columns: [
        {
          data: 'id',
          render: function(data, type, row, meta) {
            if (type === 'display') {
              return `<input class="checkSingle" type="checkbox" name="beras_ids[]"  value="${data}">`;

            }
            return data;
          }
        }, {
          data: 'id',
          render: function(data, type, row, meta) {
            // Display badges for statuses
            if (type === 'display') {
              return `<div>INV#${data}</div>
              <div>${row.paid_at ? moment(row.paid_at).startOf('day').fromNow() : '-'}</div>
              `;

            }
            return data;
          }
        }, {
          data: 'nama'
        }, {
          data: 'order_status',
          render: function(data, type, row, meta) {
            // Display badges for statuses
            if (type === 'display') {
              if (data === '1') {

                return '<span class="badge bg-success">Paid</span>';
              } else if (data === '0') {
                return '<span class="badge bg-secondary">Unpaid</span>';
              }
            }
            return data;
          }
        }, {
          data: 'all_items_price',
          render: function(data, type, row, meta) {
            // Custom rendering for the 'price' column
            if (type === 'display') {
              return 'RM' + parseFloat(data).toFixed(2); // Format as currency
            }
            return data; // Return raw data for other operations (e.g., sorting)
          }
        },  {
          data: 'id',
          render: function(data, type, row, meta) {
            // Display badges for statuses
            if (type === 'display') {
              var orderParams = {
                id: row.id,
                nama: row.nama,
                date_time_transfer: row.date_time_transfer,
                total_qty: row.total_qty,
                all_items_price: row.all_items_price,
                resit_img: row.resit_img
              };
              return `<ul class="list-inline me-auto mb-0">
                         <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="View">
                           <a
                             href="view_order.php?id=${data}"
                             class="avtar avtar-xs btn-link-secondary btn-pc-default"
                           >
                             <i class="ti ti-eye f-18"></i>
                           </a>
                         </li>
                         <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                           <a href="#" onclick="viewOrder(this)" data-row='${JSON.stringify(orderParams)}' class="avtar avtar-xs btn-link-success btn-pc-default">
                             <i class="ti ti-edit-circle f-18"></i>
                           </a>
                         </li>
                       </ul>
                       `;
            }
            return data;
          }
        }
      ],
      columnDefs: [
        {
          targets: 0,
          className: 'dt-center'
        }, {
          targets: [1],
          className: 'dt-center'
        }, {
          targets: [2],
          className: 'dt-center'
        }, {
          targets: [3],
          className: 'dt-center'
        }, {
          targets: [4],
          className: 'dt-right'
        }, {
          targets: [5],
          className: 'dt-center'
        }, {
          targets: 'no-sort',
          orderable: false
        }
      ]
    });
   //  tableOrder.on('draw', function () {
   //     // If "Check All" is checked, ensure all visible checkboxes are checked
   //     if ($('#checkedAll').is(':checked')) {
   //         $('.checkSingle').prop('checked', true);
   //     }
   // });

    function deleteThisItem(the_item_form) {
      Swal.fire({title: 'Are you sure?', showCancelButton: true, confirmButtonText: `Yes, delete it.`, denyButtonText: `No`}).then((result) => {
        if (result.isConfirmed) {
          $(the_item_form).submit();
          // Swal.fire('Saved!', '', 'success');
        } else if (result.isDenied) {
          // Swal.fire('Changes are not saved', '', 'info');
        }
      });
    }

    function ImageExist(url)
    {
       var img = new Image();
       img.src = url;
       return img.height != 0;
    }


    function viewOrder(button) {
      const order = JSON.parse(button.getAttribute('data-row'));

      // return console.log(order);



      $("#img-resit").attr('src', '');
      $("#pdf-resit").html('');
      $("#pdf-resit").css('height', '0px')


      $("#nama, #order_id, #order_ref, .date_time_transfer_input, #total_qty, #all_items_price").val('');
      $("#nama").val(order.nama);
      $("#order_ref").val(order.id);
      $(".date_time_transfer_input").val(order.date_time_transfer);
      $("#total_qty").val(order.total_qty);
      $("#all_items_price").val(order.all_items_price);
      $("#order_id").val(order.id);

      if (order.resit_img) {
        var appSubdomain = "https://<?php echo $domain ?>";
        var path = appSubdomain+"/uploads/resit/"+order.resit_img;
        // if (!ImageExist(path)) {
        //   path = appPath;
        // }
        var ext = order.resit_img.substr(order.resit_img.lastIndexOf('.') + 1);
        // var ext = 'asd';


        if (ext == 'pdf') {
          $("#pdf-resit").css('height', '500px')
          PDFObject.embed(path, "#pdf-resit");
        }else {
          $("#img-resit").prop('src', path)
        }
      }

      $("#modalApproval").modal('show');
    }

    // $("#checkedAll").change(function() {
    //   if (this.checked) {
    //     $(".checkSingle").each(function() {
    //       this.checked = true;
    //     });
    //   } else {
    //     $(".checkSingle").each(function() {
    //       this.checked = false;
    //     });
    //   }
    // });
    // $(document).on("change", "#checkedAll", function () {
    //     // Toggle all checkboxes based on the state of #checkedAll
    //     $(".checkSingle").prop("checked", this.checked);
    //     checkBoxChange();
    // });

    // $(".checkSingle").click(function() {
    //   console.log('asdasd');
    //   if ($(this).is(":checked")) {
    //     var isAllChecked = 0;
    //
    //     $(".checkSingle").each(function() {
    //       if (!this.checked)
    //         isAllChecked = 1;
    //       }
    //     );
    //
    //     if (isAllChecked == 0) {
    //       $("#checkedAll").prop("checked", true);
    //     }
    //   } else {
    //     $("#checkedAll").prop("checked", false);
    //   }
    // });
    // $(document).on("click", ".checkSingle", function () {
    //     console.log('Checkbox clicked');
    //     var isAllChecked = true; // Assume all checkboxes are checked
    //
    //     // Check the state of each checkbox
    //     $(".checkSingle").each(function () {
    //         if (!$(this).is(":checked")) {
    //             isAllChecked = false; // Found an unchecked box
    //         }
    //     });
    //
    //     // Set the master checkbox state based on `isAllChecked`
    //     $("#checkedAll").prop("checked", isAllChecked);
    //     checkBoxChange();
    // });

    // function checkBoxChange() {
    //   console.log('checkBoxChange');
    //   var checkedIds = [];
    //   var checkedBeras = [];
    //   var allCheckedBeras = $('.checkSingle:checkbox:checked');
    //
    //   allCheckedBeras.length && allCheckedBeras.each(function(a) {
    //     checkedIds.push($(allCheckedBeras[a]).val());
    //     checkedBeras.push($(allCheckedBeras[a]).data('barcode'));
    //   })
    //
    //   $('#checkedIds').val(checkedIds);
    //   $('#checkedBeras').val(checkedBeras);
    //
    //   var numberOfChecked = allCheckedBeras.length;
    //   $('#selectBerasCount').text(numberOfChecked);
    //
    //   if (numberOfChecked > 0) {
    //     $("#bulkActionWrapper").css('display', 'table-row');
    //   } else {
    //     $("#bulkActionWrapper").hide()
    //   }
    //
    // }
    // $("#checkedAll, .checkSingle").change(function() {
    //   console.log('chec');
    //   var checkedIds = [];
    //   var checkedBeras = [];
    //   var allCheckedBeras = $('.checkSingle:checkbox:checked');
    //
    //   allCheckedBeras.length && allCheckedBeras.each(function(a) {
    //     checkedIds.push($(allCheckedBeras[a]).val());
    //     checkedBeras.push($(allCheckedBeras[a]).data('barcode'));
    //   })
    //
    //   $('#checkedIds').val(checkedIds);
    //   $('#checkedBeras').val(checkedBeras);
    //
    //   var numberOfChecked = allCheckedBeras.length;
    //   $('#selectBerasCount').text(numberOfChecked);
    //
    //   if (numberOfChecked > 0) {
    //     $("#bulkActionWrapper").css('display', 'table-row');
    //   } else {
    //     $("#bulkActionWrapper").hide()
    //   }
    //
    // });

    </script>

 </body>
 <!-- [Body] end -->

 </html>
