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
      </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <div class="ms-auto">
      <ul class="list-unstyled">
        <li class="dropdown pc-h-item"><a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><svg class="pc-icon">
              <use xlink:href="#custom-notification"></use>
            </svg> <span class="badge bg-success pc-h-badge"><?php echo $totalTodo ?></span></a>
          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Notifications</h5>
            </div>
            <div class="dropdown-body text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 215px)">
              <p class="text-span">Payment Approval</p>
              <div class="card mb-2">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="flex-shrink-0"><svg class="pc-icon text-primary">
                        <use xlink:href="#custom-layer"></use>
                      </svg></div>
                    <div class="flex-grow-1 ms-3">
                      <a href="approval_payment.php">
                        <h5 class="text-body mb-2">Order</h5>
                        <p class="mb-0"><?php echo $sidebar['order_approval'] ?></p>
                      </a>
                    </div>
                  </div>
                  <hr>
                  <div class="d-flex">
                    <div class="flex-shrink-0"><svg class="pc-icon text-primary">
                        <use xlink:href="#custom-layer"></use>
                      </svg></div>
                    <div class="flex-grow-1 ms-3">
                      <a href="credit_settlement_approval.php">
                        <h5 class="text-body mb-2">Credit Settlement</h5>
                        <p class="mb-0"><?php echo $sidebar['outstanding_approval'] ?></p>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="dropdown-body text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 215px)">
              <p class="text-span">Order To Process</p>
              <div class="card mb-2">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="flex-shrink-0"><svg class="pc-icon text-primary">
                        <use xlink:href="#custom-layer"></use>
                      </svg></div>
                    <div class="flex-grow-1 ms-3">
                      <a href="to_pickup.php">
                        <h5 class="text-body mb-2">To Deliver</h5>
                        <p class="mb-0"><?php echo $sidebar['to_pick_up'] ?></p>
                      </a>
                    </div>
                  </div>
                  <hr>
                  <div class="d-flex">
                    <div class="flex-shrink-0"><svg class="pc-icon text-primary">
                        <use xlink:href="#custom-layer"></use>
                      </svg></div>
                    <div class="flex-grow-1 ms-3">
                      <a href="to_deliver.php">
                        <h5 class="text-body mb-2">To Pickup</h5>
                        <p class="mb-0"><?php echo $sidebar['to_deliver'] ?></p>
                      </a>
                    </div>
                  </div>
                  <hr>
                  <div class="d-flex">
                    <div class="flex-shrink-0"><svg class="pc-icon text-primary">
                        <use xlink:href="#custom-layer"></use>
                      </svg></div>
                    <div class="flex-grow-1 ms-3">
                      <a href="to_ship_out.php">
                        <h5 class="text-body mb-2">To Ship Out</h5>
                        <p class="mb-0"><?php echo $sidebar['to_ship_out'] ?></p>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
                  <div class="flex-shrink-0">
                    <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar wid-35" />
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1"><?php echo $my_name ?></h6>
                    <span>
                      Admin

                    </span>
                  </div>
                </div>
                <!-- <hr class="border-secondary border-opacity-50" />
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
                </a> -->

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
