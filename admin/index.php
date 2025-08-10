<?php
 include 'core/init.php';
 $page_name = "Home";

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

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="<?php echo $main_orientation ?>" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="dark">
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
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h4 class="mb-2"><?php echo date('d M Y, D') ?></h4>
                <h6 class="mb-0">Hi <?php echo $my_name ?>!</h6>

              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ Main Content ] start -->
      <div class="row">
        <div class="col-md-6 col-xl-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">Today</h6>
              <h4 class="mb-3">
                <span id="today_sales"></span>
                <span id="daily_indicator_box" class="">
                  <i id="daily_indicator"class=""></i>
                  <span id="daily_percentage"></span>

                </span>
              </h4>
              <p class="mb-0 text-muted text-sm">You made
                <span class="text-warning" id="yesterday_sales"> 1,943</span>
                Yesterday
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">This Week</h6>
              <h4 class="mb-3">
                <span id="this_week_sales"></span>
                <span id="weekly_indicator_box" class="">
                  <i id="weekly_indicator"class=""></i>
                  <span id="weekly_percentage"></span>

                </span>
              </h4>
              <p class="mb-0 text-muted text-sm">You made
                <span class="text-warning" id="last_week_sales"> 1,943</span>
                Last Week
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-2 f-w-400 text-muted">This Month</h6>
              <h4 class="mb-3">
                <span id="this_month_sales"></span>
                <span id="monthly_indicator_box" class="">
                  <i id="monthly_indicator"class=""></i>
                  <span id="monthly_percentage"></span>

                </span>
              </h4>
              <p class="mb-0 text-muted text-sm">You made
                <span class="text-warning" id="last_month_sales"> 1,943</span>
                Last Month
              </p>
            </div>
          </div>
        </div>

        <!-- top agent sales -->
        <div class="col-xl-6 col-md-12">
          <div class="card">
            <div class="card-body pb-1">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">
                  <span id="range_display">This Month</span> Top Agent Sales
                </h5>
                <div class="dropdown">
                  <a
                    class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none"
                    href="#"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="ti ti-dots-vertical f-18"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#" onclick="getTopSales()">This Month, <?php echo date('M Y') ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopSales('last_month')">Last Month, <?php echo date('M Y', strtotime('first day of last month')) ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopSales('this_year')">This Year, <?php echo date('Y') ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopSales('last_year')">Last Year, <?php echo date('Y', strtotime('last year')) ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopSales('all_time')">All Times</a>
                  </div>
                </div>
              </div>
              <!-- <h5 class="mb-1">Record: $746.5k
                <small class="text-success f-w-400">+20.6 <i class="ti ti-arrow-up"></i></small>
              </h5> -->
            </div>
            <div class="table-body  pt-0">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Agent</th>
                      <th class="text-center">Rank</th>
                      <th class="text-end">Sales</th>
                    </tr>
                  </thead>
                  <tbody id="top_sales_table">
                    <tr>
                      <td colspan="10">Loading...</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-body border-bottom pb-0">
              <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0"> <span id=product_range_display>This Month</span> Top Products</h5>
                <div class="dropdown">
                  <a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti ti-dots-vertical f-18"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#" onclick="getTopProduct()">This Month, <?php echo date('M Y') ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopProduct('last_month')">Last Month, <?php echo date('M Y', strtotime('first day of last month')) ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopProduct('this_year')">This Year, <?php echo date('Y') ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopProduct('last_year')">Last Year, <?php echo date('Y', strtotime('last year')) ?></a>
                    <a class="dropdown-item" href="#" onclick="getTopProduct('all_time')">All Times</a>
                  </div>
                </div>
              </div>
              <ul class="list-group list-group-flush" id="list_top_products">
                <li class="list-group-item text-center">Loading...</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="card">
            <div class="card-body pb-0">
              <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Outstanding Credit Terms</h5>
                <!-- <div class="dropdown"><a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ti ti-dots-vertical f-18"></i></a>
                  <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="#">Today</a> <a class="dropdown-item" href="#">Weekly</a> <a class="dropdown-item" href="#">Monthly</a></div>
                </div> -->
              </div>
            </div>
            <ul class="list-group list-group-flush border-top-0" id="outstanding_list">
            </ul>
          </div>
        </div>
      </div>
      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->


  <?php include 'partial/footer.php'; ?>
  <?php include 'partial/scripts.php'; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"
  ></script>
  <script type="text/javascript">


  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  fetch('../shared/dashboard/get_sales_summary.php')
    .then(response => response.json())
    .then(data => {
        // console.log(data); // Render the data in tables or graphs
        $("#today_sales").text("RM"+data.today.total_price);
        $("#daily_percentage").text(data.today.percentage_difference+"%");
        var daily_indicator = 'up';
        var daily_indicator_box = 'info';
        if (data.today.percentage_difference < 0) {
          daily_indicator = 'down';
          daily_indicator_box = 'warning';
        }
        $("#daily_indicator").addClass('ti ti-trending-'+daily_indicator);
        $("#daily_indicator_box").addClass(`badge bg-light-${daily_indicator_box} border border-${daily_indicator_box}`);
        $("#yesterday_sales").text("RM"+data.yesterday.total_price);

        // weekly
        $("#this_week_sales").text("RM"+data.this_week.total_price);
        $("#weekly_percentage").text(data.this_week.percentage_difference+"%");
        var weekly_indicator = 'up';
        var weekly_indicator_box = 'info';
        if (data.this_week.percentage_difference < 0) {
          weekly_indicator = 'down';
          weekly_indicator_box = 'warning';
        }
        $("#weekly_indicator").addClass('ti ti-trending-'+weekly_indicator);
        $("#weekly_indicator_box").addClass(`badge bg-light-${weekly_indicator_box} border border-${weekly_indicator_box}`);
        $("#last_week_sales").text("RM"+data.last_week.total_price);

        //monthly
        $("#this_month_sales").text("RM"+data.this_month.total_price);
        $("#monthly_percentage").text(data.this_month.percentage_difference+"%");
        var monthly_indicator = 'up';
        var monthly_indicator_box = 'info';
        if (data.this_month.percentage_difference < 0) {
          monthly_indicator = 'down';
          monthly_indicator_box = 'warning';
        }
        $("#monthly_indicator").addClass('ti ti-trending-'+monthly_indicator);
        $("#monthly_indicator_box").addClass(`badge bg-light-${monthly_indicator_box} border border-${monthly_indicator_box}`);
        $("#last_month_sales").text("RM"+data.last_month.total_price);



    });


    function getTopSales(range = 'this_month') {

      $("#range_display").text(formatText(range))

      $("#top_sales_table").html(`<tr>
        <td colspan="10">Loading...</td>
      </tr>`);



      fetch('../shared/dashboard/get_top_sales.php?' + new URLSearchParams({
        range
      }).toString())
      .then(response => response.json())
      .then(data => {
        // console.log(data);
        $("#top_sales_table").html(``);
        if (data.top_sales.length) {
          data.top_sales.sort((a, b) => parseFloat(b.total_sales) - parseFloat(a.total_sales));
          data.top_sales.forEach((usr,i) => {
            $("#top_sales_table").append(`
              <tr>
              <td>
              <span class="text-truncate w-100">${GFG(usr.nama)}</span>
              </td>
              <td class="text-center"><span class="badge bg-${usr.rank_title}">${usr.rank_title}</span></td>
              <td class="text-end f-w-600">
              RM${formatter.format(usr.total_sales)}
              </td>
              </tr>
              `)
            })
          }else {
            $("#top_sales_table").html(`<tr>
              <td colspan="10" class="text-center" >No sales data recorded. </td>
            </tr>`);
          }

        });
    }
    getTopSales();

    function getTopProduct(range = 'this_month') {
      $("#list_top_products").html('<li class="list-group-item text-center">Loading...</li>');
      $("#product_range_display").text(formatText(range))



      fetch('../shared/dashboard/get_top_products.php?' + new URLSearchParams({
        range
      }).toString())
      .then(response => response.json())
      .then(data => {
        $("#list_top_products").html('');
        // return console.log(data);
        if (data.top_products.length) {
          data.top_products.sort((a, b) => parseFloat(b.total_qty) - parseFloat(a.total_qty));
          data.top_products.forEach((product,i) => {
            $("#list_top_products").append(`
              <li class="list-group-item">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0">
                      <img src="../uploads/product/${product.image}" alt="image" class="bg-light wid-50 rounded" />
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <div class="row g-1">
                      <div class="col-6">
                        <h6 class="mb-0">${GFG(product.name)}</h6>
                        <small>RM${formatter.format(product.min_price)} - RM${formatter.format(product.max_price)}</small>
                      </div>
                      <div class="col-6 text-end">
                        <h6 class="mb-1">RM${formatter.format(product.total_sales)}</h6>
                        <p class="text-primary mb-0"> ${product.total_qty} pcs</p>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              `)
            })
          }else {
            $("#list_top_products").html('<li class="list-group-item text-center">No order data recorded.</li>');

          }

        });
    }
    getTopProduct();

    function getOutstanding(range = 'this_month') {
      $("#outstanding_list").html('');



      fetch('../shared/dashboard/get_outstanding.php')
      .then(response => response.json())
      .then(data => {
        // return console.log(data);
        if (data.length) {
          data.forEach((user,i) => {
            $("#outstanding_list").append(`
              <li class="list-group-item">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1 mx-2">
                    <h6 class="mb-1">${GFG(user.nama)}</h6>
                    <p class="mb-0">${user.rank_title}</p>
                  </div>
                  <div class="flex-shrink-0 text-end">
                    <h6 class="mb-1">RM${formatter.format(user.credit_used)}</h6>
                    <p class="text-muted mb-0">${moment(user.last_credit_used_at).startOf('day').fromNow()}</p>
                  </div>
                </div>
              </li>
              `)
            })
          }else {
            $("#outstanding_list").append(`<li class="list-group-item text-center">All Settles</li>`);
          }

        });
    }
    getOutstanding();

    function GFG(str, maxLength = 20) {
        if (str.length > maxLength) {
            return str.substring(0, maxLength) + '...';
        }
        return str;
    }

    function formatText(input) {
      return input
        .replace(/_/g, ' ') // Replace underscores with spaces
        .replace(/\b\w/g, char => char.toUpperCase()); // Capitalize each word
    }

  </script>



</body>
<!-- [Body] end -->

</html>
