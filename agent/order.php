<?php
 include 'core/init.php';
 $page_name = "Order";

 $order_id = $_GET['id'];
 $order = Order::purchaseOrder($order_id, 1);
 $products = $order['products'];
 $payment_via = '';
 if ($order['payment_status'] == 'Done') {
   $payment_via = $order['payment_type'] == 'Online' ? 'Billplz ('.$order['billplz_id'].')' : 'Credit Term';
 }
 $payment_via = $order['payment_type'] == 'Online' ? 'Billplz ('.$order['billplz_id'].')' : $order['payment_type'];

 $resit_img = '';

 if ($order['payment_type'] == 'Bank Transfer') {
   $resit_img = $order['resit_img'];
   $resit_img = "/resit/".$order['resit_img'];
 }
 ?>

<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <!-- [Meta] -->
  <?php include 'partial/header.php'; ?>


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
    <div class="pc-content ">
      <!-- [ Main Content ] start -->
      <div class="row">
        <div class="col-sm-12">
          <div class="d-print-none card mb-3">
            <div class="card-body p-3">
              <!-- <div class="" style="display: inline-block;position: absolute; left: 10;top: 20">
                <span class="">INV#<?php echo Helper::orderFormat($order['id']); ?></span>
              </div> -->
              <ul class="list-inline ms-auto mb-0 d-flex justify-content-end flex-wrap">
                <li class="list-inline-item align-bottom me-2">
                  <a href="#" class="avtar avtar-s btn-link-secondary btn-print-invoice">
                    <i class="ph-duotone ph-download-simple f-22"></i>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12">
                  <div class="row align-items-center g-3">
                    <div class="col-sm-6">
                      <div class="d-flex align-items-center mb-2">
                        <img src="../assets/images/logo-dark.svg" class="img-fluid" alt="images" />
                        <?php if ($order['paid_at']): ?>
                          <!-- <span class="badge bg-light-success rounded-pill ms-2 me-2">Paid</span> -->
                          <?php if (isset($order['credit_terms'])): ?>
                            <?php if ($order['credit_terms']['status'] == 'outstanding'): ?>
                              <a href="pay_outstanding.php">
                                <span class=" ms-2 badge bg-danger rounded-pill">Outstanding Credit Payment</span>
                              </a>
                            <?php else: ?>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="badge bg-light-secondary rounded-pill ms-2">Pending</span>
                        <?php endif; ?>
                      </div>
                      <p class="mb-0"></p>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                      <!-- <h6>Date <span class="text-muted f-w-400"><?php //echo $order['paid_at'] ?></span></h6> -->
                      <!-- <h6>Due Date <span class="text-muted f-w-400">10/8/2023</span></h6> -->
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="border rounded p-3">
                    <h6 class="mb-0">Order Reference:</h6>
                    <h5>INV#<?php echo Helper::orderFormat($order['id']); ?></h5>
                    <p class="mb-0"><b>Payment Via:</b> <?php echo $payment_via ?>
                    <?php if ($resit_img): ?>
                      <p class="mb-0"><b>Payment Status:</b>
                        <span class="badge bg-light-success rounded-pill">Paid</span>
                      </p>
                      <p class="mb-0"><b>Payment Receipt:</b> <a href="#" class="badge bg-light-primary rounded-pill" onclick="viewOrder('<?php echo $resit_img ?>')">View</a>
                    <?php endif; ?>
                    <?php if ($order['payment_type'] == 'Credit Terms'): ?>
                      <?php if (isset($order['credit_terms'])): ?>
                          <p class="mb-0"><b>Payment Status:</b>
                          <?php if ($order['credit_terms']['status'] == 'outstanding'): ?>
                            <span class="badge bg-danger rounded-pill">Outstanding</span>
                          </p>
                        <?php elseif ($order['credit_terms']['status'] == 'rejected'): ?>
                            <span class="badge bg-danger rounded-pill">Rejected</span>
                          </p>
                          <?php else: ?>
                            <span class="badge bg-light-success rounded-pill">Paid</span>
                          </p>
                          <p class="mb-0"><b>Settled Via:</b> <?php echo $order['credit_terms']['settled_via'] ?>
                          </p>
                            <?php if ($order['credit_terms']['settled_resit']): ?>

                              <p class="mb-0"><b>Settled Receipt:</b>
                                <?php if ($order['credit_terms']['settled_via'] == 'Billplz'): ?>
                                  <a href="<?php echo $order['credit_terms']['settled_resit'] ?>" target="_blank" class="badge bg-light-primary rounded-pill" >View</a>
                                <?php else: ?>
                                  <a href="#" class="badge bg-light-primary rounded-pill" onclick="viewOrder('<?php echo $order['credit_terms']['settled_resit'] ?>')">View</a>
                                <?php endif; ?>
                              </p>
                            <?php endif; ?>

                          <?php endif; ?>
                      <?php endif; ?>
                    <?php endif; ?>
                    <p class="mb-0"><b>Payment At:</b> <?php echo $order['payment_date'] ?></p>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="border rounded p-3">
                    <?php if ($order['shipping_to']): ?>
                      <h6 class="mb-0"><?php echo $order['shipping_method'] ?> To:</h6>
                      <h5><?php echo $order['shipping_to'] ?></h5>
                      <p class="mb-0"><?php echo $order['ship_address'] ?></p>
                      <p class="mb-0"><?php echo $order['shipping_contact'] ?></p>
                    <?php else: ?>
                      <h6 class="mb-0">Pick Up By:</h6>
                      <h5><?php echo $order['pickup_name'] ?></h5>
                      <p class="mb-0"><?php echo $order['pickup_phone'] ?></p>
                      <p class="mb-0"><?php echo $order['pickup_datetime'] ?></p>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="col-12">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th class="text-end">Qty</th>
                          <th class="text-end">Price</th>
                          <th class="text-end">Total Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i=0; ?>
                        <?php foreach ($products ?? [] as $key => $product): ?>
                          <?php $i++; ?>
                          <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $product['name'] ?></td>
                            <td class="text-end"><?php echo $product['quantity'] ?></td>
                            <td class="text-end">RM<?php echo $product['price_unit'];?></td>
                            <td class="text-end">RM<?php echo $product['total_price'];?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="text-start">
                    <hr class="mb-2 mt-1 border-secondary border-opacity-50" />
                  </div>
                </div>
                <div class="col-12">
                  <div class="invoice-total ms-auto">
                    <div class="row">
                      <div class="col-6">
                        <p class="f-w-600 mb-1 text-start">Grand Total :</p>
                      </div>
                      <div class="col-6">
                        <p class="f-w-600 mb-1 text-end">RM<?php echo $order['all_items_price'] ?></p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Note</label>
                  <p class="mb-0">It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance projects.
                    Thank You!</p>
                </div>
                <div class="col-12 text-end d-print-none">
                  <button class="btn btn-outline-secondary btn-print-invoice">Download</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->
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
          <h5 class="modal-title" >Payment Receipt </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">

          <div class="modal-body">
              <label class="form-label"> </label>
              <img id="img-resit" src="" alt="" width="100%">
              <div id="pdf-resit" style="width:100%"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'partial/footer.php'; ?>
  <?php include 'partial/scripts.php'; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js"></script>
  <script>
    $('.btn-print-invoice').on('click', function() {
      var link2 = document.createElement('link');
      link2.innerHTML =
        '<style>@media print{*,::after,::before{text-shadow:none!important;box-shadow:none!important}a:not(.btn){text-decoration:none}abbr[title]::after{content:" ("attr(title) ")"}pre{white-space:pre-wrap!important}blockquote,pre{border:1px solid #adb5bd;page-break-inside:avoid}thead{display:table-header-group}img,tr{page-break-inside:avoid}h2,h3,p{orphans:3;widows:3}h2,h3{page-break-after:avoid}@page{size:a3}body{min-width:992px!important}.container{min-width:992px!important}.page-header,.pc-sidebar,.pc-mob-header,.pc-header,.pct-customizer,.modal,.navbar{display:none}.pc-container{top:0;}.invoice-contact{padding-top:0;}@page,.card-body,.card-header,body,.pcoded-content{padding:0;margin:0}.badge{border:1px solid #000}.table{border-collapse:collapse!important}.table td,.table th{background-color:#fff!important}.table-bordered td,.table-bordered th{border:1px solid #dee2e6!important}.table-dark{color:inherit}.table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#dee2e6}.table .thead-dark th{color:inherit;border-color:#dee2e6}}</style>';
      document.getElementsByTagName('head')[0].appendChild(link2);
      window.print();
    });

    function viewOrder(resit_img) {
      $("#img-resit").attr('src', '');
      $("#pdf-resit").html('');
      $("#pdf-resit").css('height', '0px')


      if (resit_img) {
        var appSubdomain = "https://<?php echo $domain ?>";
        var path = appSubdomain+"/uploads"+resit_img;
        // if (!ImageExist(path)) {
        //   path = appPath;
        // }
        var ext = resit_img.substr(resit_img.lastIndexOf('.') + 1);
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
  </script>

</body>
<!-- [Body] end -->

</html>
