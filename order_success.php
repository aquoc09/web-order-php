<?php
include_once 'includes/header.php';

// Get order_id from URL
$order_id = $_GET['order_id'] ?? null;
$order = null;
$order_items = [];
$applied_coupons = [];
$original_amount = 0;
$final_amount = 0;

// If there is an order_id, query the database to get the order details
if ($order_id) {
    // 1. Fetch the main order record to get the final total
    $order_sql = "SELECT totalMoney FROM `order` WHERE id = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
        $final_amount = $order['totalMoney'];
    }
    $order_stmt->close();

    // 2. Query the details of the items in the order
    $details_sql = "
        SELECT p.name, od.numOfProducts as quantity, p.price
        FROM order_detail od
        JOIN product p ON od.product_id = p.id
        WHERE od.order_id = ?
    ";
    $details_stmt = $conn->prepare($details_sql);
    $details_stmt->bind_param("i", $order_id);
    $details_stmt->execute();
    $details_result = $details_stmt->get_result();
    $order_items = $details_result->fetch_all(MYSQLI_ASSOC);
    $details_stmt->close();

    // 3. Fetch applied coupons
    $coupons_sql = "SELECT coupon_code, discount_amount FROM order_coupon WHERE order_id = ?";
    $coupons_stmt = $conn->prepare($coupons_sql);
    $coupons_stmt->bind_param("i", $order_id);
    $coupons_stmt->execute();
    $coupons_result = $coupons_stmt->get_result();
    $applied_coupons = $coupons_result->fetch_all(MYSQLI_ASSOC);
    $coupons_stmt->close();

    // 4. Calculate the original total amount (subtotal)
    foreach ($order_items as $item) {
        $original_amount += $item['quantity'] * $item['price'];
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            <h1 class="mb-3 mt-3">Đặt hàng thành công!</h1>
            <p class="lead">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất.</p>

            <?php if ($order_id && !empty($order_items)) : ?>
                <div class="card mt-4 text-start">
                    <div class="card-header">
                        <strong>Chi tiết đơn hàng #<?php echo htmlspecialchars($order_id); ?></strong>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($order_items as $item) : ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)
                                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</span>
                                </li>
                            <?php endforeach; ?>
                            
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tạm tính
                                <span><?php echo number_format($original_amount, 0, ',', '.'); ?> VNĐ</span>
                            </li>
                            
                            <?php if (!empty($applied_coupons)) : ?>
                                <?php foreach ($applied_coupons as $coupon) : ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                                    <span>Giảm giá (<strong><?php echo htmlspecialchars($coupon['coupon_code']); ?></strong>)</span>
                                    <span>-<?php echo number_format($coupon['discount_amount'], 0, ',', '.'); ?> VNĐ</span>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                Tổng cộng
                                <span><?php echo number_format($final_amount, 0, ',', '.'); ?> VNĐ</span>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <hr class="my-4">
            <p>
                <a href="order.php" class="btn btn-secondary">Xem lịch sử đặt hàng</a>
                <a href="menu.php" class="btn btn-primary">Tiếp tục mua sắm</a>
            </p>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
