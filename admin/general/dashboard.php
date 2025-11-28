<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../../index.php'>Trở về trang chủ</a>";
    exit;
}
?>
<div class="content-wrapper">
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