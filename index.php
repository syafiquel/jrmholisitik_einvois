<?php
include 'init.php';
include 'login/login.php';

// $input_array = array("Selamat Datang", "Welcome", "Assalamualaikum", "Ahlaan bik", "Nǐ Hǎo", "Salut", "Vanakam",);
// $welcomeText = $input_array[rand(0,sizeof($input_array)-1)];
$welcomeText = '';
?>

<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Login | <?php echo SITE_NAME ?></title>
  <!-- [Meta] -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="<?php echo SITE_NAME ?> memberi yang terbaik untuk para agen yang berdaftar." />
  <meta name="keywords" content="<?php echo SITE_NAME ?>, Malaysia Agent System, Credit System for agent, Buy now Pay later, Buynow Paylater, Happy Agent" />
  <meta name="author" content="SYZ Resources" />

  <!-- [Favicon] icon -->
  <link rel="icon" href="assets/images/favicon.svg" type="image/x-icon" />
  <!-- [Font] Family -->
  <link rel="stylesheet" href="assets/fonts/inter/inter.css" id="main-font-link" />
  <!-- [phosphor Icons] https://phosphoricons.com/ -->
  <link rel="stylesheet" href="assets/fonts/phosphor/duotone/style.css" />
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="assets/fonts/tabler-icons.min.css" />
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="assets/fonts/feather.css" />
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="assets/fonts/fontawesome.css" />
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="assets/fonts/material.css" />
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="assets/css/style.css" id="main-style-link" />
  <script src="assets/js/tech-stack.js"></script>
  <link rel="stylesheet" href="assets/css/style-preset.css" />

  <style media="screen">

    .shimmer {
      text-align: center;
      color: rgba(255, 255, 255, 0.1);
      background: -webkit-gradient(linear, left top, right top, from(#222), to(#222), color-stop(0.5, #fff));
      background: -moz-gradient(linear, left top, right top, from(#222), to(#222), color-stop(0.5, #fff));
      background: gradient(linear, left top, right top, from(#222), to(#222), color-stop(0.5, #fff));
      -webkit-background-size: 125px 100%;
      -moz-background-size: 125px 100%;
      background-size: 125px 100%;
      -webkit-background-clip: text;
      -moz-background-clip: text;
      background-clip: text;
      -webkit-animation-name: shimmer;
      -moz-animation-name: shimmer;
      animation-name: shimmer;
      -webkit-animation-duration: 2s;
      -moz-animation-duration: 2s;
      animation-duration: 2s;
      -webkit-animation-iteration-count: infinite;
      -moz-animation-iteration-count: infinite;
      animation-iteration-count: infinite;
      background-repeat: no-repeat;
      background-position: 0 0;
      background-color: #222;
    }

    @-moz-keyframes shimmer {
      0% {
          background-position: top left;
      }
      100% {
          background-position: top right;
      }
    }

    @-webkit-keyframes shimmer {
      0% {
          background-position: top left;
      }
      100% {
          background-position: top right;
      }
    }

    @-o-keyframes shimmer {
      0% {
          background-position: top left;
      }
      100% {
          background-position: top right;
      }
    }

    @keyframes shimmer {
      0% {
          background-position: top left;
      }
      100% {
          background-position: top right;
      }
    }
  </style>

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="horizontal" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <div class="auth-main">
    <div class="auth-wrapper v1">
      <div class="auth-form">
        <div class="card my-5">
          <div class="card-body">
            <div class="text-center">
              <a href="#"><img style="max-width: 300px;" src="assets/logo/logo-offiial.png" alt="img" /></a>
              <br>
              <br>
              <hr>
            <form method="post">
              <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                  Email or Password is incorrect.
                </div>
              <?php endif; ?>
              <!-- <h3 class="text-center f-w-600 mb-3 shimmer text-danger">SYSTEM UPGRADE IN PROGRESS!</h3>
              <h5>ESTIMATED FINISH AT 16/4/2024 8AM</h5> -->
              <hr>
              <h4 class="text-center f-w-500 mb-3">Login with your email</h4>
              <div class="mb-3">
                <input type="email" name="username" class="form-control" id="floatingInput" placeholder="Email Address" />
              </div>
              <div class="mb-3">
                <input type="password" name="password" class="form-control" id="floatingInput1" placeholder="Password" />
              </div>
              <!-- <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check">
                  <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="" />
                  <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
                </div>
                <h6 class="text-secondary f-w-400 mb-0">
                  <a href="forgot-password-v2.html"> Forgot Password? </a>
                </h6>
              </div> -->
              <div class="d-grid mt-4">
                <button type="submit" name="login" class="btn btn-primary">Login</button>
              </div>
              <!-- <div class="d-flex justify-content-between align-items-end mt-4">
                <h6 class="f-w-500 mb-0">Don't have an Account?</h6>
                <a href="register-v2.html" class="link-primary">Create Account</a>
              </div> -->
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->
  <!-- Required Js -->
  <script src="assets/js/plugins/popper.min.js"></script>
  <script src="assets/js/plugins/simplebar.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/fonts/custom-font.js"></script>
  <script src="assets/js/pcoded.js"></script>
  <script src="assets/js/plugins/feather.min.js"></script>

  <!-- <script>
    layout_change('light');
  </script>

  <script>
    change_box_container('false');
  </script>

  <script>
    layout_caption_change('true');
  </script>

  <script>
    layout_rtl_change('false');
  </script>

  <script>
    preset_change('preset-1');
  </script>

  <script>
    main_layout_change('vertical');
  </script> -->

</body>
<!-- [Body] end -->

</html>
