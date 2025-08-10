<?php

include 'core/init.php';
$page_name = "Orders";
$url = "orders.php";


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
                 <h2 class="mb-0">Orders</h2>
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

      processing: true,
      serverSide: true,
      ajax: {
        url: '../shared/orders/all_orders.php',
        type: 'POST'
      },
      pageLength: 10,
      lengthMenu: [
        5, 10, 20, 50
      ],
      columns: [
        {
          data: 'id',
          name: 'po.id',
          render: function(data, type, row, meta) {
            // Display badges for statuses
            if (type === 'display') {
              return `<div>INV#${data}</div>
              <div>${row.paid_at}</div>
              `;
              // <div>${row.paid_at ? moment(row.paid_at).startOf('day').fromNow() : '-'}</div>

            }
            return data;
          }
        }, {
          name: 'usr.nama',
          data: 'nama'
        }, {
          name: 'po.paid_at',
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
          name: 'po.shipping_method',
          data: 'shipping_method'
        }, {
          name: 'po.all_items_price',
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
              var showDelete = '';
              if (row.order_status == 0) {
                showDelete = `<li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                  <form id="form_product${data}" method="post">

                    <input type="hidden" name="id_members_to_delete" value="${data}">
                    <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default"  onclick="deleteThisItem('#form_product${data}')">

                  </form>

                    <i class="ti ti-trash f-18"></i>
                  </a>
                </li>`;
              }
              return `<ul class="list-inline me-auto mb-0">
                         <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="View">
                           <a
                             href="view_order.php?id=${data}"
                             class="avtar avtar-xs btn-link-secondary btn-pc-default"
                           >
                             <i class="ti ti-eye f-18"></i>
                           </a>
                         </li>
                         ${showDelete}
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
      ],
       order: [[0, 'desc']]
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
    //
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
