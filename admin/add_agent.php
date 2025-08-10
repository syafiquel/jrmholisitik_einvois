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
              <h5 class="mb-0">Add Agent</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="nama" placeholder="Enter full name" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Enter email" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Phone No </label>
                    <input type="text" class="form-control" placeholder="Enter Phone No"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Rank</label>
                    <select class="form-select" required>
                      <option>Select Rank</option>
                      <?php foreach ($ranks_ref as $rank_id => $rank_name): ?>
                        <option value="<?php echo $rank_id ?>"><?php echo $rank_name ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-12 text-end">
                  <button type="submit" class="btn btn-primary">Submit</button>
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


</body>
<!-- [Body] end -->

</html>
