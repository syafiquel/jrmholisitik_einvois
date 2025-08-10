<?php
 include 'core/init.php';
 $page_name = "Home";


 ?>

<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title><?php echo $page_name ?> | <?php echo SITE_NAME ?></title>
  <!-- [Meta] -->
  <?php include 'partial/header.php'; ?>

  <style media="screen">
    .bg-gray{
      background-color: #eee;
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
      <div class="ecom-wrapper">
        <div class="ecom-content">
          <div class="card bg-light-primary">
            <div class="card-body p-3">
              <div class="d-sm-flex align-items-center">
                <ul class="list-inline me-auto my-1">
                  <li class="list-inline-item">
                    <div class="form-search">
                      <i class="ti ti-search"></i>
                      <input type="text" id="search" class="form-control" placeholder="Search Products" />
                    </div>
                  </li>
                </ul>
                <!-- <ul class="list-inline ms-auto my-1">
                  <li class="list-inline-item">
                    <select class="form-select">
                      <option>Price: High To Low</option>
                      <option>Price: Low To High</option>
                      <option>Popularity</option>
                      <option>Discount</option>
                      <option>Fresh Arrivals</option>
                    </select>
                  </li>
                  <li class="list-inline-item align-bottom">
                    <a href="#" class="d-inline-flex d-xxl-none btn btn-link-secondary align-items-center" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filter">
                      <i class="ti ti-filter f-16"></i> Filter
                    </a>
                    <a href="#" class="d-none d-xxl-inline-flex btn btn-link-secondary align-items-center" data-bs-toggle="collapse" data-bs-target="#ecom-filter">
                      <i class="ti ti-filter f-16"></i> Filter
                    </a>
                  </li>
                </ul> -->
              </div>
            </div>
          </div>
          <div class="row">
            <?php
            $products = Product::all();

            ?>
            <?php foreach ($products as $key => $product): ?>

              <div class="col-sm-6 col-xl-4">
                <?php if ($product['stock_balance'] > 0): ?>
                  <div class="card product-card bg-light-primary" data-name="<?php echo $product['name'] ?>">
                    <div class="card-img-top overlay-black">
                      <a href="#" onclick="addToCart(<?php echo $product['id'] ?>,'<?php echo $product['name'] ?>','<?php echo $product['img'] ?>','<?php echo trim($product['product_description']) ?>')">
                        <img src="<?php echo $product['img'] ?>" alt="image" class="img-prod img-fluid" />
                      </a>
                      <div class="btn-prod-cart card-body position-absolute end-0 bottom-0">
                        <div class="btn btn-warning" onclick="addToCart(<?php echo $product['id'] ?>,'<?php echo $product['name'] ?>','<?php echo $product['img'] ?>','<?php echo trim($product['product_description']) ?>')">
                          <svg class="pc-icon">
                            <use xlink:href="#custom-bag"></use>
                          </svg>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <a href="#" onclick="addToCart(<?php echo $product['id'] ?>,'<?php echo $product['name'] ?>','<?php echo $product['img'] ?>','<?php echo trim($product['product_description']) ?>')">
                        <p class="prod-content mb-0 text-muted"><?php echo $product['name'] ?></p>
                      </a>
                      <div class="d-flex align-items-center justify-content-between mt-2">
                        <h4 class="mb-0 text-truncate">
                          <?php if (isset($rank_price[$product['id']])): ?>
                            <b><?php echo Helper::MYR($rank_price[$product['id']]) ?></b>
                            <!-- <?php if ($rank_price[$product['id']] < $product['unit_price']): ?>
                              <span class="text-sm text-muted f-w-400 text-decoration-line-through"><?php echo Helper::MYR($product['unit_price']) ?></span>
                            <?php endif; ?> -->

                          <?php endif; ?>
                        </h4>
                        <div class="prod-color">
                          <small>
                            <!-- <?php echo $product['stock_balance'] ?> left -->
                          </small>
                          <!-- <span class="badge text-bg-primary">
                        </span> -->
                      </div>
                    </div>
                  </div>
                </div>
                <?php else: ?>
                  <div class="card product-card bg-gray" data-name="<?php echo $product['name'] ?>">
                    <div class="card-img-top">
                      <a href="#" onclick="noStock()">
                        <img src="<?php echo $product['img'] ?>" alt="image" class="img-prod img-fluid" />
                      </a>
                      <div class="btn-prod-cart card-body position-absolute end-0 bottom-0">
                        <div class="btn btn-warning" onclick="noStock()">
                          <svg class="pc-icon">
                            <use xlink:href="#custom-story"></use>
                          </svg>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <a href="#" onclick="noStock()">
                        <p class="prod-content mb-0 text-muted"><?php echo $product['name'] ?></p>
                      </a>
                      <div class="d-flex align-items-center justify-content-between mt-2">
                        <h4 class="mb-0 text-truncate">
                          <?php if (isset($rank_price[$product['id']])): ?>
                            <b><?php echo Helper::MYR($rank_price[$product['id']]) ?></b>
                            <!-- <?php if ($rank_price[$product['id']] < $product['unit_price']): ?>
                              <span class="text-sm text-muted f-w-400 text-decoration-line-through"><?php echo Helper::MYR($product['unit_price']) ?></span>
                            <?php endif; ?> -->

                          <?php endif; ?>
                        </h4>
                        <div class="prod-color text-danger">
                          <small>
                            <!-- <?php echo $product['stock_balance'] ?> left -->
                            Out of Stock
                          </small>
                          <!-- <span class="badge text-bg-primary">
                        </span> -->
                      </div>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>


          </div>
        </div>
      </div>
      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->


  <?php include 'partial/footer.php'; ?>
  <?php include 'partial/scripts.php'; ?>
  <script type="text/javascript">
    $(document).ready(function () {
      // updateCart('<?php echo $user_id ?>')
    });
    </script>

    <script>

    function noStock() {
      Swal.fire({
        icon: "error",
        title: "Oops... ",
        text: "This item is sold out!"
      });

    }
    function addToCart(product_id, name, file_name, description) {
      // var qty = $("#qty").val();
      // Swal.fire({
      //   icon: "error",
      //   title: "Oops...",
      //   text: "Something went wrong!"
      // });
      // returssn;
      var user_id = <?php echo $user_id ?>;
      var rank_id = '<?php echo $user['rank_id'] ?>';
      // var id = $("#product_id").val();
      // var name = $("#product_name").val();
      // var file_name = $("#file_name").val();

      // if (qty > 0 ) {

      Swal.fire({
        title: "How many\n[ "+name+" ]\ndo you want?",
        imageUrl: file_name,
        imageHeight: 400,
        showCancelButton: true,
        denyButtonText: `cancel`,
        confirmButtonText: "Add to cart",
        showLoaderOnConfirm: true,
        input: "number",
        inputAttributes: {
          autocapitalize: "off",
          placeholder: 'Enter item quantity',
          min: 1,
          required: true
        },
        preConfirm: async (qty) => {
          // Swal.fire("Saved!", "", "success");
          $.ajax({
            url: "../shared/shop_executer/add_to_cart.php",
            data:{user_id, product_id, qty, rank_id},
            type: "POST",
            dataType: 'json',
            success:function(data){
              if (data["status"] == 'success' ) {
                mainUpdateCart(user_id);
                Swal.fire({
                  title: data['title'],
                  showDenyButton: true,
                  showCancelButton: true,
                  confirmButtonText: "Continue Shopping",
                  denyButtonText: `Go to Cart`
                }).then((result) => {
                  console.log(result.value);
                  /* Read more about isConfirmed, isDenied below */
                  if (result.isConfirmed) {
                    // Swal.fire("Saved!", "", "success");
                    return;
                  } else if (result.isDenied) {
                    // Swal.fire("Changes are not saved", "", "info");
                    window.location.href = 'cart.php';
                    return;
                  }
                });
                return;
              }else if (data['status'] == 'error') {
                Swal.fire({
                  icon: "error",
                  title: "Action Failed",
                  text: data['title']
                });
              }
            },
            error:function (){}
          });
        },
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
        } else if (result.isDenied) {
          // Swal.fire("Changes are not saved", "", "info");
        }
      });
      return;
      // if (qty > 0 ) {
      //
      // }else {
      //   swal("Quantity is required", "Please fill quantity ", "warning")
      // }

    }
    function updateCart(user_id) {
      $.ajax({
        url: "../shared/shop_executer/update_cart.php",
        data:{user_id: user_id},
        type: "POST",
        dataType: 'text',
        success:function(data){
          // console.log(data)
          $("#cartButton").text(data);
          if (data > 0) {
            $("#cart-floating").show();
          }

        },
        error:function (){}
      });

    }

    $(document).ready(function () {
        $("#search").on("keyup", function () {
            let filter = $(this).val().toLowerCase();

            $(".product-card").each(function () {
                let productName = $(this).data("name").toLowerCase();

                if (productName.includes(filter)) {
                    $(this).parent().show().css("display", "flex");;
                } else {
                    $(this).parent().hide();
                }
            });
        });
    });

    </script>

</body>
<!-- [Body] end -->

</html>
