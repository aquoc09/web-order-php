<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PQ - Dashboard</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/dashboard.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
  <!-- Header -->
  <header>
    <div class="header-dashboard">
      <div class="logo">
        <div class="menu-bar">
          <i class="bi bi-list"></i>
        </div>
        <a href="index.html">
          <img src="../img/logo/logo40x40.png" alt="logo">
        </a>
      </div>
      <div class="admin-user">
        <span>Xin chào, <strong>admin</strong></span> <img src="../img/logo/logo40x40.png" alt="avatar-admin">
      </div>
    </div>
  </header>
  <!-- Content -->
  <main>
    <div class="content-wrapper">
      <!-- Navigation -->
      <div class="admin-navigation">
        <div class="admin-nav-container">
          <div class="admin-nav">
            <ul class="nav-list list-unstyled">
              <li class="root-nav-item">
                <div class="nav-item-title">CHUNG</div>
                <ul class="item-group list-unstyled">
                  <li class="nav-item active">
                    <a href="dashboard.html">
                      <i class="bi bi-speedometer2"></i>
                      Dashboard
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="dashboard-add-food.html">
                      <i class="bi bi-plus-square"></i>
                      Thêm món ăn
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="dashboard-add-category.html">
                      <i class="bi bi-plus-square"></i>
                      Thêm danh mục
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./index.html?id=admin">
                      <i class="bi bi-house"></i>
                      Về trang chủ
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
            <ul class="nav-list list-unstyled">
              <li class="root-nav-item">
                <div class="nav-item-title">QUẢN LÍ</div>
                <ul class="item-group list-unstyled">
                  <li class="nav-item">
                    <a href="dashboard-foods.html">
                      <i class="bi bi-bag-plus-fill"></i>
                      Món ăn
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="dashboard-categories.html">
                      <i class="bi bi-link-45deg"></i>
                      Danh mục
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="dashboard-orders.html">
                      <i class="bi bi-box"></i>
                      Đặt bàn
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Main content -->
      <div class="main-content">
        <!-- Main content inner -->
        <div class="main-content-inner">
          <div class="report-sales">
            <div class="sales-product">
              <div class="sales-product-title">
                <h2>Số lượng truy cập</h2>
              </div>
              <canvas id="charAccessSite" style="max-height: 500px; width: 100%; max-width: 100%"></canvas>
            </div>
            <div class="orders-total">
              <div class="orders-total-title">
                <h2>Thống kê trong tuần</h2>
              </div>
              <canvas id="chartOrdersWeekly"
                style="color: rgb(22, 22, 22) ;max-height: 500px; width: 100%; max-width: 100%"></canvas>
            </div>
          </div>
          <div class="report-dashboard">
            <div class="report-sales-daily">
              <span class="report-title">Khung giờ đặt bàn trên ngày</span>
              <canvas id="chartOrdersDaily" style="max-height: 500px; width: 100%; max-width: 100%"></canvas>
            </div>
            <div class="report-order-month">
              <span class="report-title">54 đơn đặt bàn</span>
              <p>Đặt bàn tháng này</p>
              <canvas id="chartOrdersMonthly" style="max-height: 500px; width: 100%; max-width: 100%"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <!-- Bootstrap -->
  <script src="../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../js/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- Chartjs -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../js/chart.js"></script>
  <script src="../js/dashboard.js"></script>


</body>

</html>