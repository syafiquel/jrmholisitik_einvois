<header class="pc-header">
  <div class="header-wrapper">
    <!-- [Mobile Media Block] start -->
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <!-- <li class="pc-h-item d-none d-md-inline-flex">
          <form class="form-search">
            <i class="search-icon">
              <svg class="pc-icon">
                <use xlink:href="#custom-search-normal-1"></use>
              </svg>
            </i>
            <input type="search" class="form-control" placeholder="Ctrl + K" />
          </form>
        </li> -->
      </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <style media="screen">
    .pulsate-icon {
/* width: 50px; /* Icon width */
height: 40px; /* Icon height */ */
/* background-color: #3498db; /* Icon color */ */
border-radius: 50%; /* Makes it a circle */
animation: pulsate 1.3s infinite; /* Applies the pulsating effect */
}

@keyframes pulsate {
0% {
  transform: scale(1); /* Normal size */
  opacity: 1;
}
50% {
  transform: scale(1.1); /* Enlarged size */
  opacity: 0.6;
}
100% {
  transform: scale(1); /* Back to normal size */
  opacity: 1;
}
}
    </style>
    <div class="ms-auto">
      <ul class="list-unstyled">
        <?php if ($user['credit_used'] > 0): ?>
          <li class="pc-h-item">
            <a href="pay_outstanding.php" class="pc-head-link me-0 bg-danger pulsate-icon text-white me-1"  aria-controls="" style="width: auto;font-size: 12px;height: 30px;padding: 5px;">
              outstanding
            </a>
          </li>
        <?php endif; ?>
        <li class="pc-h-item">
          <div class="bg-white p-1" style="height: 30px;border-radius: 10px;border: #eee solid 1px;">
            <b>Credit</b> : <?php echo Helper::MYR($user['credit_balance']) ?>
          </div>
        </li>
        <li class="pc-h-item">
          <a href="cart.php" class="pc-head-link me-0" >
            <svg class="pc-icon">
              <use xlink:href="#custom-bag"></use>
            </svg>
            <span class="badge bg-warning pc-h-badge" id="#cartButton"><?php echo $inside_cart ?></span>
          </a>
        </li>
        <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
            <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar" />
          </a>
          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Profile</h5>
            </div>
            <div class="dropdown-body">
              <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                <div class="d-flex mb-1">
                  <!-- <div class="flex-shrink-0">
                    <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar wid-35" />
                  </div> -->
                  <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">Nama: <?php echo $my_name ?></h6>
                    <span>
                      Rank: <?php echo $user['my_rank'] ?> Agent

                    </span>
                  </div>
                </div>
                <hr class="border-secondary border-opacity-50" />
                <p class="text-span">Manage</p>
                <a href="setting.php" class="dropdown-item">
                  <span>
                    <svg class="pc-icon text-muted me-2">
                      <use xlink:href="#custom-setting-outline"></use>
                    </svg>
                    <span>My Profile</span>
                  </span>
                </a>
                <a href="change_password.php" class="dropdown-item">
                  <span>
                    <svg class="pc-icon text-muted me-2">
                      <use xlink:href="#custom-lock-outline"></use>
                    </svg>
                    <span>Change Password</span>
                  </span>
                </a>

                <hr class="border-secondary border-opacity-50" />
                <div class="d-grid mb-3">
                  <a href="../login/logout.php" class="btn btn-primary">
                    <svg class="pc-icon me-2">
                      <use xlink:href="#custom-logout-1-outline"></use>
                    </svg>Logout
                  </a>
                </div>
                <!-- <div class="card border-0 shadow-none drp-upgrade-card mb-0" style="background-image: url(../assets/images/layout/img-profile-card.jpg)">
                  <div class="card-body">
                    <div class="user-group">
                      <img src="../assets/images/user/avatar-1.jpg" alt="user-image" class="avtar" />
                      <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="avtar" />
                      <img src="../assets/images/user/avatar-3.jpg" alt="user-image" class="avtar" />
                      <img src="../assets/images/user/avatar-4.jpg" alt="user-image" class="avtar" />
                      <img src="../assets/images/user/avatar-5.jpg" alt="user-image" class="avtar" />
                      <span class="avtar bg-light-primary text-primary">+20</span>
                    </div>
                    <h3 class="my-3 text-dark">245.3k <small class="text-muted">Followers</small></h3>
                    <a href="#" class="btn btn btn-warning buynowlinks">
                      <svg class="pc-icon me-2">
                        <use xlink:href="#custom-logout-1-outline"></use>
                      </svg>
                      Upgrade to Platinum
                    </a>
                  </div>
                </div> -->
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>
