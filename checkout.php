<?php
include_once 'includes/header.php';
include_once 'database/conf.php';

// Check if user is logged in
if (!$currentUser) {
    header("Location: login-form.php");
    exit;
}

// Fetch cart items from the database for the logged-in user
$cart_items = [];
$total_price = 0;

$sql = "SELECT
            p.id as product_id,
            p.name,
            p.price,
            ci.quantity,
            c.id as cart_id
        FROM cart c
        JOIN cart_item ci ON c.id = ci.cartId
        JOIN product p ON ci.product_id = p.id
        WHERE c.user_id = ? AND c.cartStatus = 'active'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUser['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['price'] * $row['quantity'];
    }
} else {
    // If cart is empty, redirect to the order page
    header("Location: order.php");
    exit;
}
$stmt->close();

?>

<div class="container py-5">
    <h1 class="mb-4">Thanh toán</h1>
    <div class="row">
        <div class="col-md-7">
            <h4>Thông tin giao hàng</h4>
            <form action="modules/create_order.php" method="POST">
                <div class="mb-3">
                    <label for="fullName" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" value="<?= htmlspecialchars($currentUser['fullName'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($currentUser['address'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                </div>
                
                <hr class="my-4">

                <h4>Phương thức thanh toán</h4>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="cod" checked>
                    <label class="form-check-label" for="cod">
                        Thanh toán khi nhận hàng (COD)
                    </label>
                </div>
                <!-- Add other payment methods here if available -->

                <hr class="my-4">

                <button type="submit" class="btn btn-primary w-100">Đặt hàng</button>
            </form>
        </div>
        <div class="col-md-5">
            <h4>Đơn hàng của bạn</h4>
            <ul class="list-group mb-3">
                <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                            <small class="text-muted">Số lượng: <?= $item['quantity'] ?></small>
                        </div>
                        <span class="text-muted"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫</span>
                    </li>
                <?php endforeach; ?>

                <li class="list-group-item d-flex justify-content-between">
                    <span>Tổng cộng</span>
                    <strong><?= number_format($total_price, 0, ',', '.') ?> ₫</strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
