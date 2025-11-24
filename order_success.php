<?php
include_once 'includes/header.php';

$order_id = $_GET['order_id'] ?? null;
?>

<div class="container text-center py-5">
    <h1 class="mb-4">Đặt hàng thành công!</h1>
    <p class="lead">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.</p>
    <?php if ($order_id): ?>
        <p>Mã đơn hàng của bạn là: <strong>#<?php echo htmlspecialchars($order_id); ?></strong></p>
    <?php endif; ?>
    <hr>
    <p>
        <a href="order.php" class="btn btn-secondary">Xem lịch sử đặt hàng</a>
        <a href="menu.php" class="btn btn-primary">Tiếp tục mua sắm</a>
    </p>
</div>

<?php
include_once 'includes/footer.php';
?>
