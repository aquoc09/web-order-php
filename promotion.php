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
                <div class="card coupon-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-9">
                                <h5 class="card-title">Giảm <?php echo number_format($coupon_row['discountAmount'], 0, ',', '.'); ?> %</h5>
                                <p class="card-text">Cho đơn hàng từ <?php echo number_format($coupon_row['conditionAmount'], 0, ',', '.'); ?> VNĐ</p>
                                <p class="card-text"><strong>Mã: <?php echo $coupon_row['code']; ?></strong></p>
                                <button class="btn btn-primary btn-sm" onclick="copyToClipboard('<?php echo $coupon_row['code']; ?>')">Sao chép mã</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    <?php
        }
        echo '</div>';
    } else {
        echo '<p class="text-center">Hiện tại không có mã giảm giá nào.</p>';
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
