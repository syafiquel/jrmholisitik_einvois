<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="/" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="../assets/logo/logo-jrm-ampang.jpg" class="img-fluid logo-lg" alt="logo" />
        <span class="badge bg-light-primary rounded-pill ms-2 theme-version"><?php echo $user['my_rank'] ?> Agent</span>
      </a>
    </div>
    <div class="navbar-content ">
      <div class="card pc-user-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <img src="../ablepro/assets/images/user/avatar-1.jpg" alt="user-image" class="user-avtar wid-45 rounded-circle" />
            </div>
            <div class="flex-grow-1 ms-3 me-2">
              <h6 class="mb-0"><?php echo $my_name ?></h6>
              <small><?php echo $user['my_rank'] ?> Agent</small>
            </div>
            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
              <svg class="pc-icon">
                <use xlink:href="#custom-sort-outline"></use>
              </svg>
            </a>
          </div>
          <div class="collapse pc-user-links" id="pc_sidebar_userlink">
            <div class="pt-3">
              <a href="setting.php">
                <i class="ti ti-settings"></i>
                <span>Settings</span>
              </a>
              <a href="../login/logout.php">
                <i class="ti ti-power"></i>
                <span>Logout</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <ul class="pc-navbar">
        <li class="pc-item ">
          <a class="pc-link" href="shop.php">Shop</a>
        </li>

        <!-- Orders Menu -->
        <li class="pc-item pc-caption">
          <label>Order</label>
          <svg class="pc-icon"><use xlink:href="#custom-layer"></use></svg>
        </li>
        <!-- <li class="pc-item ">
          <a href="shop.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-element-plus"></use>
              </svg>
            </span>
            <span class="pc-mtext">Shop</span>
          </a>
        </li> -->
        <li class="pc-item ">
          <a href="cart.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-bag"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill text-bg-primary"><?php echo $inside_cart ?></span>
              Cart
            </span>
          </a>
        </li>
        <li class="pc-item ">
          <a href="paid_orders.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-bag"></use>
              </svg>
            </span>
            <span class="pc-mtext">Paid Orders</span>
          </a>
        </li>
        <li class="pc-item ">
          <a href="orders.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-bag"></use>
              </svg>
            </span>
            <span class="pc-mtext">All Orders</span>
          </a>
        </li>

        <!-- Credit -->
        <li class="pc-item pc-caption">
          <label>Credit Terms
            <?php if ($user['credit_used'] > 0): ?>
              <span class="pulsate-icon position-absolute start-100 translate-middle badge rounded-pill text-bg-danger" style="top: 10px;height: 20px;" >!</span>
            <?php endif; ?>
          </label>
          <svg class="pc-icon"><use xlink:href="#custom-dollar-square"></use></svg>
        </li>
        <li class="pc-item ">
          <a href="pay_outstanding.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-dollar-square"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              Pay Outstanding
              <?php if ($user['credit_used'] > 0): ?>
                <span class="pulsate-icon position-absolute start-100 translate-middle badge rounded-pill text-bg-danger" style="top: 10px;height: 20px;" >!</span>
              <?php endif; ?>
            </span>
          </a>
        </li>
        <li class="pc-item ">
          <a href="credit_terms_activity.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-text-align-justify-center"></use>
              </svg>
            </span>
            <span class="pc-mtext">Activity</span>
          </a>
        </li>


        <li class="pc-item pc-caption">
          <label>Setting</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-setting-outline"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="setting.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-user-bold"></use>
              </svg>
            </span>
            <span class="pc-mtext">My Profile</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="change_password.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-security-safe"></use>
              </svg>
            </span>
            <span class="pc-mtext">Change Password</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
