<?php
include_once './includes/header.php';



include_once './database/conf.php';

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
            <form action="modules/create_order.php" method="POST" id="checkout-form">
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
                    <textarea class="form-control" id="note" name="note" form="checkout-form" rows="2"></textarea>
                </div>
                
                <hr class="my-4">

                <h4>Phương thức thanh toán</h4>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="cod" checked>
                    <label class="form-check-label" for="cod">
                        Thanh toán khi nhận hàng (COD)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="vnpay" value="vnpay">
                    <label class="form-check-label" for="vnpay">
                        Thanh toán qua VNPAY
                    </label>
                </div>

                <hr class="my-4">
                <input type="hidden" name="total" value="<?php echo $total_price; ?>">
                <button type="submit" class="btn btn-primary w-100" name="redirect">Đặt hàng</button>
            </form>
        </div>
        <div class="col-md-5">
            <h4 class="mt-4">Đơn hàng của bạn</h4>
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

                <?php
                $total_discount = 0;
                $final_price = $total_price;
                $valid_coupons = [];

                // 1. Check if user has changed.
                if (isset($_SESSION['coupon_user_id']) && $_SESSION['coupon_user_id'] !== $currentUser['id']) {
                    unset($_SESSION['applied_coupons']);
                    unset($_SESSION['coupon_user_id']);
                }

                // 2. Re-validate coupons in session.
                if (isset($_SESSION['applied_coupons']) && is_array($_SESSION['applied_coupons'])) {
                    foreach ($_SESSION['applied_coupons'] as $coupon_in_session) {
                        $sql_coupon = "SELECT * FROM coupon WHERE code = ? AND active = 1";
                        $stmt_coupon = $conn->prepare($sql_coupon);
                        $stmt_coupon->bind_param("s", $coupon_in_session['code']);
                        $stmt_coupon->execute();
                        $result_coupon = $stmt_coupon->get_result();

                        if ($coupon_db = $result_coupon->fetch_assoc()) {
                            // Check if cart total meets the coupon's condition.
                            if ($total_price >= $coupon_db['conditionAmount']) {
                                // The coupon is valid for this cart. Recalculate its discount value.
                                $discountAmount = floatval($coupon_db['discountAmount']);
                                $valid_coupons[] = [
                                    'code' => $coupon_db['code'],
                                    'discount_amount' => $discountAmount
                                ];
                                $total_discount += $discountAmount;
                            }
                        }
                        $stmt_coupon->close();
                    }
                }

                $_SESSION['applied_coupons'] = $valid_coupons;
                if (!empty($valid_coupons)) {
                    $_SESSION['coupon_user_id'] = $currentUser['id'];
                } else {
                    unset($_SESSION['coupon_user_id']);
                }

                $final_price = $total_price - $total_discount;
                ?>

                <div id="applied-coupons-list">
                    <?php if (!empty($valid_coupons)): ?>
                        <?php foreach ($valid_coupons as $coupon): ?>
                            <li class="list-group-item d-flex justify-content-between text-success">
                                <div>
                                    <h6 class="my-0">Mã giảm giá: <?= htmlspecialchars($coupon['code']) ?></h6>
                                </div>
                                <span class="text-success">-<?= number_format($coupon['discount_amount'], 0, ',', '.') ?> ₫</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <li class="list-group-item d-flex justify-content-between">
                    <span>Tổng cộng</span>
                    <strong id="total-amount"><?= number_format($final_price, 0, ',', '.') ?> ₫</strong>
                </li>
            </ul>
            <form id="coupon-form" onsubmit="return false;">
                <div class="input-group">
                    <input type="text" class="form-control" name="coupon" placeholder="Mã giảm giá">
                    <button type="submit" class="btn btn-secondary">Áp dụng</button>
                </div>
             </form>
        </div>
    </div>
</div>
<script src="js/checkout.js"></script>
<?php include 'includes/footer.php'; ?>
