<?php
include 'includes/header.php';

// Check if user is logged in
if (!$currentUser) {
    // If not logged in, show a message and a login button
?>
<div class="container text-center py-5">
    <h1 class="mb-4">Bạn cần đăng nhập</h1>
    <p class="lead mb-4">Vui lòng đăng nhập để xem giỏ hàng của bạn.</p>
    <a href="login-form.php" class="btn btn-primary">Đăng nhập</a>
</div>
<?php
    include 'includes/footer.php';
    exit; // Stop further execution
}

// Fetch cart items from the database for the logged-in user
$cart_items = [];
$total_price = 0;

$sql = "SELECT
            p.id as product_id,
            p.name,
            p.productImage,
            p.price,
            ci.quantity,
            c.id as cart_id,
            cat.categoryCode
        FROM cart c
        JOIN cart_item ci ON c.id = ci.cartId
        JOIN product p ON ci.product_id = p.id
        JOIN category cat ON p.category_id = cat.id
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
}
$stmt->close();
?>

<div class="container py-5">
    <h1 class="mb-4">Giỏ hàng của bạn</h1>

    <?php if (empty($cart_items)): ?>
        <div class="text-center">
            <p class="lead">Giỏ hàng của bạn đang trống.</p>
            <a href="menu.php" class="btn btn-primary">Bắt đầu mua sắm</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col" style="width: 15%;">Sản phẩm</th>
                        <th scope="col" style="width: 30%;"></th>
                        <th scope="col" class="text-center">Giá</th>
                        <th scope="col" class="text-center">Số lượng</th>
                        <th scope="col" class="text-end">Tạm tính</th>
                        <th scope="col" class="text-center">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr data-product-id="<?= $item['product_id'] ?>">
                            <td>
                                <img src="img/food/menu/<?= htmlspecialchars($item['categoryCode']) ?>/<?= htmlspecialchars($item['productImage']) ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            </td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td class="text-center item-price" data-price="<?= $item['price'] ?>"><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center quantity-input" 
                                       data-id="<?= $item['product_id'] ?>" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="0" style="width: 70px; margin: auto;">
                            </td>
                            <td class="text-end subtotal"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm remove-item" data-id="<?= $item['product_id'] ?>">&times;</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tổng cộng</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Tổng tiền: <span class="float-end" id="cart-total-price"><?= number_format($total_price, 0, ',', '.') ?> ₫</span></h6>
                        <hr>
                        <div class="d-grid">
                            <a href="checkout.php" class="btn btn-primary">Tiến hành thanh toán</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="js/cart.js"></script>
<?php include 'includes/footer.php'; ?>
