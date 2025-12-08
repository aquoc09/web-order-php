<?php
include_once 'includes/header.php';

// Lấy order_id từ URL
$order_id = $_GET['order_id'] ?? null;
$order_items = [];
$total_amount = 0;

// Nếu có order_id, truy vấn CSDL để lấy chi tiết đơn hàng
if ($order_id) {
    // Truy vấn chi tiết các món trong đơn hàng
    $details_sql = "
        SELECT p.name, od.numOfProducts as quantity, p.price
        FROM order_detail od
        JOIN product p ON od.product_id = p.id
        WHERE od.order_id = ?
    ";
    $stmt = $conn->prepare($details_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Tính tổng số tiền
    foreach ($order_items as $item) {
        $total_amount += $item['quantity'] * $item['price'];
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
                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                Tổng cộng:
                                <span><?php echo number_format($total_amount, 0, ',', '.'); ?> VNĐ</span>
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
