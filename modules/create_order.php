<?php
//include '../includes/header.php';
include '../function/order_helper.php'; // Include the new helper

// 1. Authenticate user via token
$token = $_COOKIE['auth_token'] ?? '';
$currentUser = null;
if ($token) {
    $sql = "SELECT u.id, u.username FROM user_tokens t 
            JOIN user u ON u.id = t.user_id
            WHERE t.token = ? AND t.expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $currentUser = $result->fetch_assoc();
    }
}

// Check if user is logged in
if (!$currentUser) {
    header("Location: ../login-form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Collect and sanitize all POST data first ---
    $userId = $currentUser['id'];
    $fullName = $_POST['fullName'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $note = $_POST['note'] ?? '';
    $paymentMethod = $_POST['paymentMethod'] ?? 'cod';

    // Basic validation
    if (empty($fullName) || empty($phone) || empty($address)) {
        header("Location: ../checkout.php?error=Vui lòng điền đầy đủ thông tin.");
        exit;
    }

    // --- Calculate total from cart to prevent price manipulation ---
    $cartSql = "SELECT SUM(p.price * ci.quantity) as total
                FROM cart c
                JOIN cart_item ci ON c.id = ci.cartId
                JOIN product p ON ci.product_id = p.id
                WHERE c.user_id = ? AND c.cartStatus = 'active'";
    $cartStmt = $conn->prepare($cartSql);
    $cartStmt->bind_param("i", $userId);
    $cartStmt->execute();
    $result = $cartStmt->get_result();
    $row = $result->fetch_assoc();
    $totalMoney = $row['total'] ?? 0;
    $cartStmt->close();

    if ($totalMoney <= 0) {
        header("Location: ../checkout.php?error=Giỏ hàng của bạn đang trống.");
        exit;
    }
    
    // --- Securely re-validate coupons and calculate final price ---
    $totalDiscount = 0;
    $valid_coupons = [];
    $finalTotal = $totalMoney;

    if (isset($_SESSION['applied_coupons']) && is_array($_SESSION['applied_coupons'])) {
        // Check if user has changed
        if (isset($_SESSION['coupon_user_id']) && $_SESSION['coupon_user_id'] !== $userId) {
            unset($_SESSION['applied_coupons']);
            unset($_SESSION['coupon_user_id']);
        } else {
            // Re-validate each coupon
            foreach ($_SESSION['applied_coupons'] as $coupon_in_session) {
                $sql_coupon = "SELECT * FROM coupon WHERE code = ? AND active = 1";
                $stmt_coupon = $conn->prepare($sql_coupon);
                $stmt_coupon->bind_param("s", $coupon_in_session['code']);
                $stmt_coupon->execute();
                $result_coupon = $stmt_coupon->get_result();

                if ($coupon_db = $result_coupon->fetch_assoc()) {
                    if ($totalMoney >= $coupon_db['conditionAmount']) {
                        $discountAmount = floatval($coupon_db['discountAmount']);
                        $valid_coupons[] = [
                            'code' => $coupon_db['code'],
                            'discount_amount' => $discountAmount
                        ];
                        $totalDiscount += $discountAmount;
                    }
                }
                $stmt_coupon->close();
            }
        }
    }
    
    $finalTotal = $totalMoney - $totalDiscount;

    // --- Handle payment method ---
    if ($paymentMethod === 'cod') {
        // For COD, create order, details, and clear cart immediately.
        $orderId = create_order_and_details($conn, $userId, $address, $note, $finalTotal, 'cod', 'pending', $valid_coupons);
        
        if ($orderId) {
            // Clear the cart and coupon session data after successful order creation
            clear_cart($conn, $userId);
            unset($_SESSION['applied_coupons']);
            unset($_SESSION['coupon_user_id']);
            header("Location: ../order_success.php?order_id=" . $orderId);
            exit;
        } else {
            header("Location: ../checkout.php?error=Đã có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.");
            exit;
        }

    } elseif ($paymentMethod === 'vnpay') {
        // For VNPAY, create a 'pending_payment' order first.
        $orderId = create_order_and_details($conn, $userId, $address, $note, $finalTotal, 'vnpay', 'pending', $valid_coupons);

        if ($orderId) {
            $_SESSION['order_id_for_vnpay'] = $orderId;
            header("Location: ../vnpay_create_payment.php");
            exit;
        } else {
            header("Location: ../checkout.php?error=Đã có lỗi xảy ra khi chuẩn bị thanh toán. Vui lòng thử lại.");
            exit;
        }
    } else {
        // Invalid payment method
        header("Location: ../checkout.php?error=Phương thức thanh toán không hợp lệ.");
        exit;
    }
} else {
    // If not a POST request, redirect to home
    header("Location: ../index.php");
    exit;
}
?>
