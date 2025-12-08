<?php
// Bắt đầu session và include header, kết nối DB
include_once 'includes/header.php';
require_once 'auth/vnpay_config.php';
require_once 'function/order_helper.php'; // Include the new helper

// Lấy dữ liệu VNPAY trả về
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '99';
$vnp_TxnRef = $_GET['vnp_TxnRef'] ?? ''; // Mã đơn hàng
$vnp_Amount = ($_GET['vnp_Amount'] ?? 0) / 100; // Số tiền
$vnp_OrderInfo = $_GET['vnp_OrderInfo'] ?? '';
$vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? ''; // Mã giao dịch tại VNPAY
$vnp_BankCode = $_GET['vnp_BankCode'] ?? '';
$vnp_PayDate = $_GET['vnp_PayDate'] ?? ''; // Thời gian thanh toán
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">

            <?php
            // 1. Xác thực chữ ký
            if ($secureHash == $vnp_SecureHash) {
                // Fetch order to check status
                $order_check_sql = "SELECT `status`, `user_id` FROM `order` WHERE `id` = ?";
                $stmt_check = $conn->prepare($order_check_sql);
                $stmt_check->bind_param("i", $vnp_TxnRef);
                $stmt_check->execute();
                $order_result = $stmt_check->get_result();
                $order_data = $order_result->fetch_assoc();
                $stmt_check->close();

                if ($order_data && $order_data['status'] == 'pending') {
                    // 2. Kiểm tra kết quả giao dịch
                    if ($vnp_ResponseCode == '00') {
                        // Giao dịch thành công
                        // 3. Cập nhật trạng thái đơn hàng và xóa giỏ hàng
                        $conn->begin_transaction();
                        try {
                            // Update order status
                            $update_sql = "UPDATE `order` SET `status` = 'paid' WHERE `id` = ?";
                            $stmt_update = $conn->prepare($update_sql);
                            $stmt_update->bind_param("i", $vnp_TxnRef);
                            $stmt_update->execute();
                            $stmt_update->close();

                            // Clear the user's cart
                            clear_cart($conn, $order_data['user_id']);

                            // Lấy chi tiết đơn hàng để hiển thị
                            $order_items = [];
                            $details_sql = "
                                SELECT p.name, od.numOfProducts as quantity, p.price 
                                FROM order_detail od
                                JOIN product p ON od.product_id = p.id
                                WHERE od.order_id = ?
                            ";
                            $stmt_details = $conn->prepare($details_sql);
                            $stmt_details->bind_param("i", $vnp_TxnRef);
                            $stmt_details->execute();
                            $details_result = $stmt_details->get_result();
                            $order_items = $details_result->fetch_all(MYSQLI_ASSOC);
                            $stmt_details->close();

                            $conn->commit();
            ?>
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                            <h1 class="mb-3 mt-3">Thanh toán thành công!</h1>
                            <p class="lead">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất.</p>
                            <div class="card mt-4">
                                <div class="card-header"><strong>Thông tin giao dịch</strong></div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Mã đơn hàng:</span>
                                        <strong>#<?php echo htmlspecialchars($vnp_TxnRef); ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Số tiền:</span>
                                        <strong><?php echo number_format($vnp_Amount, 0, ',', '.'); ?> VNĐ</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Mã giao dịch VNPAY:</span>
                                        <strong><?php echo htmlspecialchars($vnp_TransactionNo); ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Ngân hàng:</span>
                                        <strong><?php echo htmlspecialchars($vnp_BankCode); ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Thời gian thanh toán:</span>
                                        <strong><?php echo date("d/m/Y H:i:s", strtotime($vnp_PayDate)); ?></strong>
                                    </li>
                                </ul>
                            </div>

                            <div class="card mt-4 text-start">
                                <div class="card-header"><strong>Chi tiết đơn hàng</strong></div>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($order_items as $item) : ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)
                                            <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
            <?php
                        } catch (Exception $e) {
                            $conn->rollback();
                            // Show generic error to user, log the detailed error
                            error_log("VNPay Return Error: " . $e->getMessage());
            ?>
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                            <h1 class="mb-3 mt-3">Lỗi xử lý đơn hàng</h1>
                            <p class="lead">Giao dịch của bạn đã thành công nhưng có lỗi khi cập nhật đơn hàng. Vui lòng liên hệ hỗ trợ.</p>
            <?php
                        }
                    } else {
                        // Giao dịch không thành công
                        $update_fail_sql = "UPDATE `order` SET `status` = 'denied' WHERE `id` = ?";
                        $stmt_fail = $conn->prepare($update_fail_sql);
                        $stmt_fail->bind_param("i", $vnp_TxnRef);
                        $stmt_fail->execute();
                        $stmt_fail->close();
            ?>
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                        <h1 class="mb-3 mt-3">Giao dịch không thành công</h1>
                        <p class="lead">Đã có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.</p>
            <?php
                    }
                } else if ($order_data && $order_data['status'] == 'accepted') {
                     // If order is already processed
            ?>
                    <i class="bi bi-info-circle-fill text-info" style="font-size: 5rem;"></i>
                    <h1 class="mb-3 mt-3">Đơn hàng đã được xử lý</h1>
                    <p class="lead">Đơn hàng này đã được thanh toán và xử lý trước đó.</p>
            <?php
                } else {
                     // Order not found or status is not pending_payment
            ?>
                    <i class="bi bi-shield-exclamation text-danger" style="font-size: 5rem;"></i>
                    <h1 class="mb-3 mt-3">Lỗi giao dịch</h1>
                    <p class="lead">Không tìm thấy đơn hàng hoặc đơn hàng không hợp lệ.</p>
            <?php
                }
            } else {
                // Chữ ký không hợp lệ
            ?>
                <i class="bi bi-shield-exclamation text-danger" style="font-size: 5rem;"></i>
                <h1 class="mb-3 mt-3">Lỗi xác thực giao dịch</h1>
                <p class="lead">Chữ ký không hợp lệ. Giao dịch không thể được xác nhận.</p>
            <?php
            }
            ?>

            <hr class="my-4">
            <p>
                <a href="order.php" class="btn btn-secondary">Xem lịch sử đặt hàng</a>
                <a href="menu.php" class="btn btn-primary">Tiếp tục mua sắm</a>
            </p>

        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>