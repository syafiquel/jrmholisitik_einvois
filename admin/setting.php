<?php
 include 'core/init.php';
 $page_name = "Setting";
 $url = "setting.php";
 if (isset($_POST['save_setting'])) {
   $agreement_context = $_POST['agreement_context'];
   $limit_quantity = $_POST['limit_quantity'];
   $limit_total_price = $_POST['limit_total_price'];
   $cut_off_order_time = $_POST['cut_off_order_time'];
   $cut_off_ship_out_time = $_POST['cut_off_ship_out_time'];

   $sql =  "UPDATE setting SET
   agreement_context = '$agreement_context',
   limit_quantity = '$limit_quantity',
   cut_off_ship_out_time = '$cut_off_ship_out_time',
   cut_off_order_time = '$cut_off_order_time',
   limit_total_price = '$limit_total_price'
   WHERE id = '1'";
   // echo $sql;
   // exit;
   if(mysqli_query($db, $sql))
   {
     $_SESSION['success'] = "Setting updated.";
     header("Location: $url");
     exit;
   }else {
     $_SESSION['error'] = "There error when processing data. Please try again.";
     header("Location: $url");
     exit;
   }

 }
 $sql = "SELECT * FROM setting WHERE id = '1'";
 $result = mysqli_query($database, $sql);
 $row = mysqli_fetch_array($result,MYSQLI_ASSOC);

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

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
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
      <div class="row">
        <form method="post">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0"><?php echo $page_name ?></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <br>
                  <h5>Credit Terms Setting </h5>
                  <br>
                </div>
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Agreement Context</label>
                    <textarea name="agreement_context" id="classic-editor" rows="8"><?php echo $row['agreement_context'] ?></textarea>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Default Limit Total Price</label>
                    <input name="limit_total_price" type="number" step="0.01" class="form-control" placeholder="Limit Quantity" value="<?php echo $row['limit_total_price'] ?>" />
                  </div>
                </div>
                <div class="col-md-12">
                  <hr>
                  <br>
                  <br>
                  <h5>Order Setting </h5>
                  <br>
                </div>
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Order Cut Off Time </label>
                    <input name="cut_off_order_time" type="number" step="0.01" class="form-control" placeholder="Order Cut Off Time" value="<?php echo $row['cut_off_order_time'] ?>" />
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Ship Out Cut Off Time </label>
                    <input name="cut_off_ship_out_time" type="number" step="0.01" class="form-control" placeholder="Ship Out Cut Off Time" value="<?php echo $row['cut_off_ship_out_time'] ?>" />
                  </div>
                </div>

                <div class="col-md-12 text-end">
                  <button type="submit" name="save_setting" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </div>

          </div>
        </form>

      </div>
      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->


  <?php include 'partial/footer.php'; ?>
  <?php include 'partial/scripts.php'; ?>
  <!-- Ckeditor js -->
  <script src="../assets/js/plugins/ckeditor/classic/ckeditor.js"></script>
  <script>
    (function () {
      ClassicEditor.create(document.querySelector('#classic-editor')).catch((error) => {
        console.error(error);
      });
    })();
  </script>
  <!-- [Page Specific JS] end -->


</body>
<!-- [Body] end -->

</html>
