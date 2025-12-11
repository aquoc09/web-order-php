<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

// Lấy danh sách revenue theo ngày
$sql = "SELECT id, date, totalMoney, totalOrder 
        FROM revenue
        ORDER BY date DESC";

$result = $conn->query($sql);
?>

<div class="main-content">
    <div class="main-content-inner">
        <h3>Danh sách Doanh Thu</h3>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ngày</th>
                    <th>Tổng Order</th>
                    <th>Doanh Thu</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['totalOrder'] ?></td>
                    <td><?= number_format($row['totalMoney']) ?> đ</td>
                    <td>
                        <button class="btn btn-primary btn-view-revenue" 
                                data-date="<?= $row['date'] ?>" 
                                data-bs-toggle="modal" 
                                data-bs-target="#revenueDetailModal">
                            Xem chi tiết
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Chưa có doanh thu nào</td></tr>
            <?php endif; ?>

            </tbody>
        </table>

    </div>
</div>

<!-- Modal chi tiết revenue -->
<div class="modal fade" id="revenueDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title">Chi tiết doanh thu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <h6>Danh sách Order trong ngày</h6>

            <table class="table table-bordered" id="revenue-detail-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Order Date</th>
                        <th>Total Money</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
  </div>
</div>

<script>
document.querySelectorAll('.btn-view-revenue').forEach(btn => {
    btn.addEventListener('click', function () {
        
        let date = this.dataset.date;

        fetch("modules/getRevenueDetail.php?date=" + date)
        .then(res => res.json())
        .then(rows => {

            let tbody = document.querySelector("#revenue-detail-table tbody");
            tbody.innerHTML = "";

            rows.forEach(r => {
                tbody.innerHTML += `
                    <tr>
                        <td>${r.id}</td>
                        <td>${r.userName}</td>
                        <td>${r.orderDate}</td>
                        <td>${Number(r.totalMoney).toLocaleString()} đ</td>
                        <td>${r.status}</td>
                    </tr>
                `;
            });
        });
    });
});
</script>
