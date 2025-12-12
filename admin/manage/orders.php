<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

// Lấy danh sách order + join user
$sql = "SELECT o.id, o.user_id, u.fullName AS userName, o.status, o.totalMoney
        FROM `order` o
        LEFT JOIN `user` u ON o.user_id = u.id
        ORDER BY o.id DESC";

$result = $conn->query($sql);
?>

<div class="main-content">
    <div class="main-content-inner">
        <h3>Danh sách Order</h3>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['userName']) ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= number_format($row['totalMoney']) ?> đ</td>
                    <td>
                        <button class="btn btn-primary btn-view" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#viewOrderModal">Thông tin</button>
                        <button class="btn btn-warning btn-detail" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#detailModal">Chi tiết</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Chưa có order nào</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



<!-- Modal thông tin order -->
 <div class="modal fade" id="viewOrderModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="orderForm" method="POST" action="modules/updateOrder.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thông tin Order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <input type="hidden" name="id" id="order-id">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>User</label>
                    <input type="text" id="order-user" class="form-control" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select id="order-status" name="status" class="form-select">
                        <option value="pending">PENDING</option>
                        <option value="accepted">ACCEPTED</option>
                        <option value="denied">DENIED</option>
                        <option value="not_pay">NOT_PAY</option>
                        <option value="paid">PAID</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Address</label>
                    <input type="text" id="order-address" name="address" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Note</label>
                    <input type="text" id="order-note" name="note" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Order Date</label>
                    <input type="text" id="order-date" name="orderDate" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Phương thức thanh toán</label>
                    <select id="order-payment" name="payment" class="form-select">
                        <option value="cod">COD</option>
                        <option value="vnpay">VNPAY</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Total Money</label>
                    <input type="number" id="order-total" name="totalMoney" class="form-control">
                </div>
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.btn-view').forEach(btn => {
    btn.addEventListener('click', function () {
        let id = this.dataset.id;

        fetch("modules/getOrder.php?id=" + id)
        .then(res => res.json())
        .then(data => {

            document.getElementById("order-id").value = data.id;
            document.getElementById("order-user").value = data.userName;
            document.getElementById("order-status").value = data.status;
            document.getElementById("order-address").value = data.address;
            document.getElementById("order-note").value = data.note;
            document.getElementById("order-date").value = data.orderDate;
            document.getElementById("order-payment").value = data.paymentMethod;
            document.getElementById("order-total").value = data.totalMoney;

            // XỬ LÝ QUYỀN
            if (data.role === "manager") {
                document.querySelectorAll("#viewOrderModal input").forEach(i => i.readOnly = true);
                document.querySelectorAll("#viewOrderModal select").forEach(s => s.disabled = true);

                // Manager chỉ được sửa status
                //document.getElementById("order-status").disabled = false;
            }
        });
    });
});
</script>

<!-- Modal order detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content will be injected by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.dataset.id;
            let modalBody = document.getElementById('detailModalBody');
            modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            fetch("modules/getOrderDetail.php?order_id=" + id)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    const { products, coupons } = data;

                    let productHtml = '<h6>Các sản phẩm</h6>';
                    if (products && products.length > 0) {
                        productHtml += `
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Món</th>
                                        <th>SL</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                        let subtotal = 0;
                        products.forEach(p => {
                            let imgPath = `../images/${p.categoryCode}/${p.productImage}`;
                            subtotal += parseFloat(p.totalMoney);
                            productHtml += `
                                <tr>
                                    <td>${p.id}</td>
                                    <td>
                                        <img src="${imgPath}" style="width:50px; height:50px; object-fit:cover; border:1px solid #ccc;" onerror="this.src='../img/logo/logo40x40.png'">
                                        ${p.productName}
                                    </td>
                                    <td>${p.numOfProducts}</td>
                                    <td>${new Intl.NumberFormat('vi-VN').format(p.totalMoney)} đ</td>
                                </tr>`;
                        });
                        productHtml += '</tbody></table>';
                        
                        let couponHtml = '<h6>Các mã ưu đãi</h6>';
                        let totalDiscount = 0;
                        if (coupons && coupons.length > 0) {
                            couponHtml += `
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Mã ưu đãi</th>
                                            <th>Giá tiền đã giảm</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                            coupons.forEach(coupon => {
                                totalDiscount += parseFloat(coupon.discount_amount);
                                couponHtml += `
                                    <tr>
                                        <td>${coupon.coupon_code}</td>
                                        <td>-${new Intl.NumberFormat('vi-VN').format(coupon.discount_amount)} đ</td>
                                    </tr>`;
                            });
                            couponHtml += '</tbody></table>';
                        } else {
                            couponHtml += '<p>Không có mã ưu đãi nào được áp dụng.</p>';
                        }

                        modalBody.innerHTML = productHtml + couponHtml;

                    } else {
                         modalBody.innerHTML = "<p>Không có sản phẩm trong đơn hàng này.</p>";
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi lấy chi tiết đơn hàng:', error);
                    modalBody.innerHTML = "<div class='alert alert-danger'>Đã có lỗi xảy ra khi tải chi tiết đơn hàng. Vui lòng thử lại.</div>";
                });
        });
    });
</script>
