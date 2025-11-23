<?php
include_once 'includes/header.php';
include_once 'database/conf.php';
?>

<div class="container">
    <h1 class="text-center my-4">Ưu đãi</h1>

    <?php
    // Fetch coupons
    $coupon_sql = "SELECT * FROM coupon WHERE active = 1";
    $coupon_result = $conn->query($coupon_sql);

    if ($coupon_result->num_rows > 0) {
        echo '<div class="row">';
        while ($coupon_row = $coupon_result->fetch_assoc()) {
            ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mã giảm giá: <?php echo $coupon_row['code']; ?></h5>
                        <p class="card-text">Giảm <?php echo number_format($coupon_row['discountAmount'], 0, ',', '.'); ?> VNĐ cho đơn hàng từ <?php echo number_format($coupon_row['conditionAmount'], 0, ',', '.'); ?> VNĐ.</p>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<p>Hiện tại không có mã giảm giá nào.</p>';
    }
    ?>
</div>

<?php
include_once 'includes/footer.php';
?>
