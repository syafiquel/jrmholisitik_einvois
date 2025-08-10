<?php
 include 'core/init.php';
 $page_name = "My Profile";
$url = 'change_password.php';
$user_id = $user['id'];

 if (isset($_POST['change_password'])){
   $old_password = $_POST['old_password'];
   $new_password_2 = $_POST['new_password_2'];
   $new_password_1 = $_POST['new_password_1'];

   if (
     (
       !$old_password || !$new_password_1
     )
     &&
     (
       $new_password_2 == $new_password_1
     )
   ) {
     $_SESSION['error'] = "Failed to edit agent. Please fill all details. Please try again.";
     header("Location: $url");
     exit;
   }


     $password = mysqli_real_escape_string($db, $new_password_1);
     $secret_salt = mysqli_real_escape_string($db, '??..jrmholisampang||2024..??');
     $password = md5($password.$secret_salt);


   $sql =  "UPDATE user SET
     password = '$password'
     WHERE id = $user_id";

   // echo $sql;
   // exit;
   if(mysqli_query($db, $sql))
   {
     $_SESSION['success'] = "Password was successfully updated.";
     header("Location: $url");
     exit;
   }else{
     $_SESSION['error'] = "Failed to change password. Please fill all details. Please try again.";
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
  <style media="screen">
  .error {
    color: red;
  }
  .success {
    color: green;
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
  <div class="pc-container" >
    <div class="pc-content" style="max-width: 600px;;">
      <!-- [ Main Content ] start -->
      <div class="row">
        <form method="post">
          <div class="card" >
            <div class="card-header">
              <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password"  class="form-control noSpaceInput" name="old_password" value="" required />
                  </div>
                </div>

                <hr>
                <br>
                <b>Please Enter New Password & Confirm Password</b>
                <small id="syarat" class="error">
                  <span class="">*Password must be more than 4 characters & no space</span>
                </small>
                <small>
                  <span id="message"></span>

                </small>

                <br>
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password"  class="form-control noSpaceInput" name="new_password_1" id="new_password_1" value="" required />
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password"  class="form-control noSpaceInput" name="new_password_2" id="new_password_2" value="" required />
                  </div>
                </div>


                <div class="col-md-12 text-end">
                  <button type="submit" name="change_password" id="btn-submit" class="btn btn-primary">Submit</button>
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
  <script type="text/javascript">
  $(document).ready(function () {
    $('#new_password_1, #new_password_2').on('keyup', function () {
      $("#btn-submit").attr('disabled', true);
      $("#btn-submit").text('Enter a matching Password');
      const newPassword = $('#new_password_1').val();
      const confirmPassword = $('#new_password_2').val();
      const message = $('#message');
      const syarat = $('#syarat');
      syarat.removeClass('success').addClass('error');
      var lepasSyarat = false;
      if (  newPassword.length >= 4) {
        syarat.removeClass('error').addClass('success');
        lepasSyarat = true;
      }

      if (newPassword === confirmPassword && lepasSyarat) {
        message.text('*Passwords match!').removeClass('error').addClass('success');
        $("#btn-submit").attr('disabled', false);
        $("#btn-submit").text('Submit');

      } else {
        message.text('*Passwords do not match!').removeClass('success').addClass('error');
      }
    });

    // $('#passwordForm').on('submit', function (e) {
    //   const newPassword = $('#new_password_1').val();
    //   const confirmPassword = $('#new_password_2').val();
    //
    //   if (newPassword !== confirmPassword) {
    //     e.preventDefault(); // Prevent form submission
    //     alert('Passwords do not match!');
    //   }
    // });

    $('.noSpaceInput').on('keydown', function (e) {
        if (e.key === ' ' || e.keyCode === 32) {
          e.preventDefault(); // Prevent the default action of spacebar
        }
      });

  });
  </script>
</body>
<!-- [Body] end -->

</html>
