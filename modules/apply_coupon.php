<?php
session_start();
include_once '../database/conf.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get current user
    $currentUser = null;
    $token = $_COOKIE['auth_token'] ?? '';
    if ($token) {
        $sql_user = "SELECT u.id FROM user_tokens t JOIN user u ON u.id = t.user_id WHERE t.token = ? AND t.expires_at > NOW()";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("s", $token);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($result_user->num_rows == 1) {
            $currentUser = $result_user->fetch_assoc();
        }
        $stmt_user->close();
    }

    if (!$currentUser) {
        echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để sử dụng mã giảm giá.']);
        exit;
    }

    $couponCode = $_POST['coupon'] ?? '';
    $total = floatval($_POST['total'] ?? 0);

    if (empty($couponCode)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
        exit;
    }

    // Check if new coupon is valid
    $sql = "SELECT * FROM coupon WHERE code = ? AND active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();

        if ($total < $coupon['conditionAmount']) {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng chưa đủ điều kiện để áp dụng mã giảm giá này.']);
            exit;
        }

        if (!isset($_SESSION['applied_coupons'])) {
            $_SESSION['applied_coupons'] = [];
        }

        if (in_array($couponCode, array_column($_SESSION['applied_coupons'], 'code'))) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá này đã được áp dụng.']);
            exit;
        }

        // Add the new valid coupon to the session
        $_SESSION['applied_coupons'][] = ['code' => $couponCode];
        $_SESSION['coupon_user_id'] = $currentUser['id'];

        // Recalculate all coupon discounts based on the current total
        $totalDiscount = 0;
        $recalculated_coupons = [];
        foreach ($_SESSION['applied_coupons'] as $c) {
            $coupon_sql = "SELECT * FROM coupon WHERE code = ? AND active = 1";
            $coupon_stmt = $conn->prepare($coupon_sql);
            $coupon_stmt->bind_param("s", $c['code']);
            $coupon_stmt->execute();
            $coupon_res = $coupon_stmt->get_result();
            if($db_coupon = $coupon_res->fetch_assoc()){
                if($total >= $db_coupon['conditionAmount']){
                    $discountAmount = ($total * floatval($db_coupon['discountAmount'])) / 100;
                    $recalculated_coupons[] = [
                        'code' => $db_coupon['code'],
                        'discount_amount' => $discountAmount
                    ];
                    $totalDiscount += $discountAmount;
                }
            }
            $coupon_stmt->close();
        }
        
        $_SESSION['applied_coupons'] = $recalculated_coupons;
        $newTotal = $total - $totalDiscount;

        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'new_total' => $newTotal,
            'total_discount' => $totalDiscount,
            'applied_coupons' => $_SESSION['applied_coupons']
        ]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
    }
    $stmt->close();
}
$conn->close();
