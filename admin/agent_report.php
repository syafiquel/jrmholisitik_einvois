<?php
include 'core/init.php';
$page_name = "Agent Report";
$url = "agent_report.php";

$first_day_of_month = date('Y-m-1');
$today_of_month = date('Y-m-d');
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
   <link rel="stylesheet" href="../assets/css/plugins/buttons.bootstrap5.min.css" />

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
                   <div style="" class="row m-0 mb-2">
                     <div class="col-sm-3 ps-0">
                       <input type="text" class="form-control  date-time-picker" name="date_start" id="date_start" value="<?php echo $first_day_of_month ?>" placeholder="Date Start">
                     </div>
                     <div class="col-sm-3 ps-0">
                       <input type="text" class="form-control  date-time-picker" name="date_end" id="date_end" value="<?php echo $today_of_month ?>" placeholder="Date End">
                     </div>
                     <div class="col-sm-3 p-0">
                       <select class="form-control " name="agent_id" id="agent_id">
                         <option value="">Select Agent</option>
                       </select>
                     </div>
                     <div class="col-sm-3 pe-0">
                       <button type="button" class="btn btn-primary" style="width: 100%;border-radius: 6px;" onclick="filterData()">
                         <i class="ti ti-filter me-1"></i>
                         Filter
                       </button>
                     </div>

                   </div>

                   <table class="table table-hover table-bordered my-1" id="table-orders">
                     <thead>
                       <tr>
                         <td colspan="6"  class="no-sort">
                           Order Total: RM<span id="orderTotalDisplay"></span>
                         </td>
                       </tr>
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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"
    ></script>
   <script src="../assets/js/plugins/dataTables.min.js"></script>
   <script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
   <script src="../assets/js/plugins/choices.min.js"></script>
   <!-- <script src="../assets/js/plugins/datepicker-full.min.js"></script> -->
   <script src="../assets/js/plugins/flatpickr.min.js"></script>

   <script src="../assets/js/plugins/buttons.colVis.min.js"></script>
   <script src="../assets/js/plugins/buttons.print.min.js"></script>
   <script src="../assets/js/plugins/pdfmake.min.js"></script>
   <script src="../assets/js/plugins/jszip.min.js"></script>
   <script src="../assets/js/plugins/dataTables.buttons.min.js"></script>
   <script src="../assets/js/plugins/vfs_fonts.js"></script>
   <script src="../assets/js/plugins/buttons.html5.min.js"></script>
   <script src="../assets/js/plugins/buttons.bootstrap5.min.js"></script>

   <script>
   $('.date-time-picker').flatpickr({
     enableTime: false,
     noCalendar: false,
     minuteIncrement: 1,
     dateFormat: "Y-m-d",
   });

    var tableOrder = $('#table-orders').DataTable({
      buttons: ['excel', 'print'],
      dom: 'Bfrtlfip',

      searching: false,
      serverSide: true,
      ajax: {
        url: '../shared/orders/agent_report.php',
        type: 'POST',
        dataSrc: function (json) {
            // Display order_total in a separate HTML element
            $('#orderTotalDisplay').text(json.order_total);
            return json.data; // Return table data
        }
      },
      pageLength: 10,
      lengthMenu: [
        5, 10, 20, 50
      ],
      columns: [
        {
          data: 'id',
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
      ]
    });

   function filterData() {
     var dateStart = `date_start=${$('#date_start').val()}&`;
     var dateEnd = `date_end=${$('#date_end').val()}`;
     var agentId = `agent_id=${$('#agent_id').val()}`;
     var filter = dateStart + '&' + dateEnd + '&' + agentId;
     tableOrder.ajax.url('../shared/orders/agent_report.php?' + filter).load();
   }

    $('#table-orders thead td').off('click')


    var singleFetch = new Choices('#agent_id', {
      searchPlaceholderValue: 'Search Agent'
    })
      .setChoices(function () {
        return fetch('../shared/agents/get_all_agents.php')
          .then(function (response) {
            return response.json();
          })
          .then(function (data) {
            return data.releases.map(function (release) {
              return {
                label: release.nama,
                value: release.id
              };
            });
          });
      })
      .then(function (instance) {
        instance.setChoiceByValue('Fake Tales Of San Francisco');
      });


    </script>

 </body>
 <!-- [Body] end -->

 </html>
