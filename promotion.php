<?php
include_once 'includes/header.php';
include_once 'database/conf.php';

$isNewUser = false;
$hasUsedNewUserCoupon = false;
$newUserCoupon = null; // To store coupon data for ID 1

if ($currentUser) {
    // 1. Determine "New User" status
    $userCreatedAtSql = "SELECT createdAt FROM user WHERE id = ?";
    $userCreatedAtStmt = $conn->prepare($userCreatedAtSql);
    $userCreatedAtStmt->bind_param("i", $currentUser['id']);
    $userCreatedAtStmt->execute();
    $userCreatedAtResult = $userCreatedAtStmt->get_result();
    if ($userCreatedAtRow = $userCreatedAtResult->fetch_assoc()) {
        $createdAt = new DateTime($userCreatedAtRow['createdAt']);
        $now = new DateTime();
        $interval = $now->diff($createdAt);
        if ($interval->days <= 30) {
            $isNewUser = true;
        }
    }
    $userCreatedAtStmt->close();

    // 2. Fetch the coupon code for new user coupon (ID 1)
    $newUserCouponSql = "SELECT * FROM coupon WHERE id = 1 AND active = 1";
    $newUserCouponResult = $conn->query($newUserCouponSql);
    if ($newUserCouponResult->num_rows > 0) {
        $newUserCoupon = $newUserCouponResult->fetch_assoc();
        
        // 3. Check if the new user has already used this coupon
        if ($isNewUser) {
            $checkUsedCouponSql = "
                SELECT 1 FROM order_coupon oc
                JOIN `order` o ON oc.order_id = o.id
                WHERE o.user_id = ? AND oc.coupon_code = ?
                LIMIT 1";
            $checkUsedCouponStmt = $conn->prepare($checkUsedCouponSql);
            $checkUsedCouponStmt->bind_param("is", $currentUser['id'], $newUserCoupon['code']);
            $checkUsedCouponStmt->execute();
            $checkUsedCouponResult = $checkUsedCouponStmt->get_result();
            if ($checkUsedCouponResult->num_rows > 0) {
                $hasUsedNewUserCoupon = true;
            }
            $checkUsedCouponStmt->close();
        }
    }
}
?>

<div class="container">
    <h1 class="text-center my-4">Ưu đãi</h1>

    <?php if ($isNewUser && !$hasUsedNewUserCoupon && $newUserCoupon) : ?>
        <h2 class="text-center my-3">Ưu đãi dành cho người dùng mới</h2>
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">Mã giảm giá: <?php echo $newUserCoupon['code']; ?> <span class="badge bg-success">Dành cho bạn</span></h5>
                        <p class="card-text">Giảm <?php echo number_format($newUserCoupon['discountAmount'], 0, ',', '.'); ?> % cho đơn hàng từ <?php echo number_format($newUserCoupon['conditionAmount'], 0, ',', '.'); ?> VNĐ.</p>
                        <button class="btn btn-success btn-sm" onclick="copyToClipboard('<?php echo $newUserCoupon['code']; ?>')">Sao chép mã</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h2 class="text-center my-3">Tất cả các ưu đãi khác</h2>
    <?php
    // Fetch other coupons (excluding the new user coupon if it exists)
    $coupon_sql = "SELECT * FROM coupon WHERE active = 1";
    if ($newUserCoupon) {
        $coupon_sql .= " AND id != " . $newUserCoupon['id'];
    }
    $coupon_result = $conn->query($coupon_sql);

    if ($coupon_result->num_rows > 0) {
        echo '<div class="row">';
        while ($coupon_row = $coupon_result->fetch_assoc()) {
            ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mã giảm giá: <?php echo $coupon_row['code']; ?></h5>
                        <p class="card-text">Giảm <?php echo number_format($coupon_row['discountAmount'], 0, ',', '.'); ?> % cho đơn hàng từ <?php echo number_format($coupon_row['conditionAmount'], 0, ',', '.'); ?> VNĐ.</p>
                        <button class="btn btn-primary btn-sm" onclick="copyToClipboard('<?php echo $coupon_row['code']; ?>')">Sao chép mã</button>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<p>Hiện tại không có mã giảm giá nào khác.</p>';
    }
    ?>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Đã sao chép mã: ' + text);
        }, function(err) {
            alert('Không thể sao chép mã. Vui lòng thử lại.');
        });
    }
</script>

<?php
include_once 'includes/footer.php';
?>

