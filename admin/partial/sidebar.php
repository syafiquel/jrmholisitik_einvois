<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="index.php" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="../assets/logo/logo-jrm-ampang.jpg" class="img-fluid logo-lg" alt="logo" />
        <span class="badge bg-light-success rounded-pill ms-2 theme-version">Admin</span>
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
              <h6 class="mb-0">JRM AMPANG</h6>
              <small>Administrator</small>
            </div>
            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
              <svg class="pc-icon">
                <use xlink:href="#custom-sort-outline"></use>
              </svg>
            </a>
          </div>
          <div class="collapse pc-user-links" id="pc_sidebar_userlink">
            <div class="pt-3">
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
          <a class="pc-link" href="index.php">Dashboard</a>
        </li>
         <!-- Orders -->
        <li class="pc-item pc-caption">
          <label>Payment
            <?php if (($sidebar['order_approval']+$sidebar['outstanding_approval']) > 0): ?>
              <span class="position-absolute start-100 translate-middle badge rounded-pill text-bg-danger" style="top: 10px;" ><?php echo $sidebar['order_approval']+$sidebar['outstanding_approval'] ?></span>
            <?php endif; ?>
          </label>
          <svg class="pc-icon">
            <use xlink:href="#custom-presentation-chart"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="approval_payment.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-dollar-square"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              Order Payment Approval
              <?php if (isset($sidebar['order_approval']) && $sidebar['order_approval']): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger"><?php echo $sidebar['order_approval'] ?></span>
              <?php endif; ?>
            </span>
          </a>
        </li>
        <li class="pc-item">
          <a href="credit_settlement_approval.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-dollar-square"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              Credit Settlement Approval

              <?php if (isset($sidebar['outstanding_approval']) && $sidebar['outstanding_approval']): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger"><?php echo $sidebar['outstanding_approval'] ?></span>
              <?php endif; ?>
            </span>
          </a>
        </li>
        <li class="pc-item">
          <a href="rejected_payment.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-dollar-square"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              Orders Payment Rejected
            </span>
          </a>
        </li>
        <li class="pc-item">
          <a href="settlement_rejected.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-dollar-square"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              Settlement Rejected
            </span>
          </a>
        </li>
        <li class="pc-item pc-caption">
          <label>Orders
            <?php if (($todoTotalCount) > 0): ?>
              <span class="position-absolute start-100 translate-middle badge rounded-pill text-bg-danger" style="top: 10px;" ><?php echo $todoTotalCount ?></span>
            <?php endif; ?>
          </label>
          <svg class="pc-icon">
            <use xlink:href="#custom-presentation-chart"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="to_pickup.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-box-1"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              To Pickup
              <?php if (isset($sidebar['to_pick_up']) && $sidebar['to_pick_up']): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger"><?php echo $sidebar['to_pick_up'] ?></span>
              <?php endif; ?>
            </span>
          </a>
        </li>
        <li class="pc-item">
          <a href="to_ship_out.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-airplane"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              To Ship Out
              <?php if (isset($sidebar['to_ship_out']) && $sidebar['to_ship_out']): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger"><?php echo $sidebar['to_ship_out'] ?></span>
              <?php endif; ?>
            </span>
          </a>
        </li>
        <li class="pc-item">
          <a href="to_deliver.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-airplane"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              To Deliver Today
              <?php if (isset($sidebar['to_deliver']) && $sidebar['to_deliver']): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger"><?php echo $sidebar['to_deliver'] ?></span>
              <?php endif; ?>
            </span>
          </a>
        </li>
        <li class="pc-item">
          <a href="completed_orders.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-star-bold"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              Completed Orders
            </span>
          </a>
        </li>
        <!-- <li class="pc-item">
          <a href="pickup_orders.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-user"></use>
              </svg>
            </span>
            <span class="pc-mtext">
              Pick Up Orders
          </span>
          </a>
        </li> -->
        <li class="pc-item">
          <a href="orders.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-layer"></use>
              </svg>
            </span>
            <span class="pc-mtext">All Orders</span>
          </a>
        </li>

        <!-- Products -->
        <li class="pc-item pc-caption">
          <label>Products</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-shapes"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="products.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-add-item"></use>
              </svg>
            </span>
            <span class="pc-mtext">Products</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="product_stock_log.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-add-item"></use>
              </svg>
            </span>
            <span class="pc-mtext">Product Stock Log</span>
          </a>
        </li>

        <!-- Users -->
        <li class="pc-item pc-caption">
          <label>Users</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-user"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="agents.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-user-add"></use>
              </svg>
            </span>
            <span class="pc-mtext">Agents</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="agent_report.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
              </svg>
            </span>
            <span class="pc-mtext">Report</span>
          </a>
        </li>

        <!-- Credit -->
        <li class="pc-item pc-caption">
          <label>Credit Checker</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-element-plus"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="credit_user.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-mouse-circle"></use>
              </svg>
            </span>
            <span class="pc-mtext">Credit User</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="outstanding.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-mouse-circle"></use>
              </svg>
            </span>
            <span class="pc-mtext">Outstanding Orders</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="credit_history.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-row-vertical"></use>
              </svg>
            </span>
            <span class="pc-mtext">History</span>
          </a>
        </li>
        <!-- end Credit -->

        <!-- Configuration -->
        <li class="pc-item pc-caption">
          <label>Configuration</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-setting-2"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="setting.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-share-bold"></use>
              </svg>
            </span>
            <span class="pc-mtext">Setting</span>
          </a>
        </li>
        <!-- <li class="pc-item">
          <a href="billplz_setting.php" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-dollar-square"></use>
              </svg>
            </span>
            <span class="pc-mtext">Billplz Setting</span>
          </a>
        </li> -->
        <!-- <li class="pc-item pc-hasmenu"><a href="#!" class="pc-link"><span class="pc-micon"><svg class="pc-icon">
                <use xlink:href="#custom-document"></use>
              </svg> </span><span class="pc-mtext">Credit Terms</span> <span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="../demo/layout-vertical.html">Agreement Text</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-horizontal.html">Setting</a></li>
          </ul>
        </li> -->


      </ul>
    </div>
  </div>
</nav>
