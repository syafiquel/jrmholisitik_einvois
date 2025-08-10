<?php

include 'core/init.php';
$page_name = "Completed Orders";
$url = "completed_orders.php";


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
                   <div class="">
                     <!-- <span onclick="loadOrders('Pending')">To Deliver</span>
                     |
                     <span onclick="loadOrders('All')">All</span>
                   </div> -->
                   <table class="table table-hover " id="table-orders">
                     <thead>
                       <tr id="bulkActionWrapper" style="display:none;" >
                        <td colspan="7" class="no-sort">
                            <div style="">

                              <button type="button" name="button" class="btn btn-warning btn-sm">
                                <span id="selectBerasCount">0</span> Order(s) Selected
                              </button>
                              <input type="hidden" name="checkedIds" id="checkedIds" value="">
                              <button type="button" class="btn btn-primary btn-sm" name="button" onclick="printDO()">Print DO</button>
                            </div>
                          </td>
                        </tr>

                       <tr>
                         <th class=" no-sort text-center" > <input id="checkedAll" type="checkbox" name="all" value="" > </th>
                         <th class="text-center">#</th>
                         <th class="text-center">Agent</th>
                         <th class="text-center">Status</th>
                         <th class="text-center">Delivery</th>
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
         <div class="modal fade " id="tracking_form_modal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalRight" aria-hidden="true" style=" overflow-y: scroll;">
           <div class="modal-dialog modal-dialog-centered " role="document">
             <div class="modal-content">
               <div class="modal-header">
                 <h5 class="modal-title">Update Tracking Number</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

               </div>
               <form method="post" >
                 <div class="modal-body">

                   <div class="form-group">
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
                       <div class="col-12">
                         <div class="mb-3">
                           <label class="form-label">Approval Note</label>
                           <textarea name="cancel_note" class="form-control" rows="4" cols="80"></textarea>
                         </div>
                       </div>
                     </div>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"
    ></script>
   <!-- <script src="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.js"></script> -->
   <script src="../assets/js/plugins/dataTables.min.js"></script>
   <script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
   <!-- <script src="../assets/plugins/datatables/multi-checkbox/js/dataTables.checkboxes.min.js"></script> -->

   <script>
    $(document).ready(function() {
     $('.dropify').dropify();
    });

    var tableOrder = $('#table-orders').on('init.dt', function () {
        $(".dt-empty").text('No data available');
        console.log('Table initialisation complete: ' + new Date().getTime());
    }).DataTable({
      serverSide: true,
      ajax: {
        url: '../shared/orders/get_orders.php?status=paid',
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
                var label = `Paid via ${row.payment_type}`;
                var bgcolor = 'success';

                if (row.credit_status == 'outstanding') {
                  bgcolor = 'danger';
                  label += '<br>Outstanding'
                }
                return `<span class="badge bg-${bgcolor}">${label}</span>`;
              } else if (data === '0') {
                return '<span class="badge bg-secondary">Unpaid</span>';
              }
            }
            return data;
          }
        }, {
          data: 'order_type'
        }, {
          data: 'all_items_price',
          render: function(data, type, row, meta) {
            // Custom rendering for the 'price' column
            if (type === 'display') {
              return 'RM' + parseFloat(data).toFixed(2); // Format as currency
              //return 'RM' + parseFloat(data).toFixed(2) + `(${row['total_qty']})`; // Format as currency
            }
            return data; // Return raw data for other operations (e.g., sorting)
          }
        },  {
          data: 'id',
          render: function(data, type, row, meta) {
            // Display badges for statuses
            if (type === 'display') {
              return `<ul class="list-inline me-auto mb-0">
                         <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="View">
                           <a
                             href="view_order.php?id=${data}"
                             class="avtar avtar-xs btn-link-secondary btn-pc-default"
                           >
                             <i class="ti ti-eye f-18"></i>
                           </a>
                         </li>

                       </ul>`;
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
          className: 'dt-center'
        }, {
          targets: [5],
          className: 'dt-right'
        }, {
          targets: [6],
          className: 'dt-center'
        }, {
          targets: 'no-sort',
          orderable: false
        }
      ]
    });
    tableOrder.on('draw', function () {
       // If "Check All" is checked, ensure all visible checkboxes are checked
       if ($('#checkedAll').is(':checked')) {
           $('.checkSingle').prop('checked', true);
       }
   });

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
    $(document).on("change", "#checkedAll", function () {
        // Toggle all checkboxes based on the state of #checkedAll
        $(".checkSingle").prop("checked", this.checked);
        checkBoxChange();
    });

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
    $(document).on("click", ".checkSingle", function () {
        console.log('Checkbox clicked');
        var isAllChecked = true; // Assume all checkboxes are checked

        // Check the state of each checkbox
        $(".checkSingle").each(function () {
            if (!$(this).is(":checked")) {
                isAllChecked = false; // Found an unchecked box
            }
        });

        // Set the master checkbox state based on `isAllChecked`
        $("#checkedAll").prop("checked", isAllChecked);
        checkBoxChange();
    });

    function checkBoxChange() {
      console.log('checkBoxChange');
      var checkedIds = [];
      var checkedBeras = [];
      var allCheckedBeras = $('.checkSingle:checkbox:checked');

      allCheckedBeras.length && allCheckedBeras.each(function(a) {
        checkedIds.push($(allCheckedBeras[a]).val());
        checkedBeras.push($(allCheckedBeras[a]).data('barcode'));
      })

      $('#checkedIds').val(checkedIds);
      $('#checkedBeras').val(checkedBeras);

      var numberOfChecked = allCheckedBeras.length;
      $('#selectBerasCount').text(numberOfChecked);

      if (numberOfChecked > 0) {
        $("#bulkActionWrapper").css('display', 'table-row');
      } else {
        $("#bulkActionWrapper").hide()
      }

    }
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

    function printDO() {
      var checkedIds = $('#checkedIds').val();
      console.log(checkedIds);
      window.open('http://<?php echo $domain ?>/admin/print_do.php?ids='+checkedIds,'window','toolbar=no, menubar=no, resizable=yes, height=900, width=1000');
      return;
    }

    </script>

 </body>
 <!-- [Body] end -->

 </html>
