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

    // --- New User Coupon (ID 1) Specific Validation ---
    $newUserCouponCode = null;
    $newUserCouponSql = "SELECT code FROM coupon WHERE id = 1 AND active = 1";
    $newUserCouponResult = $conn->query($newUserCouponSql);
    if ($newUserCouponResult->num_rows > 0) {
        $newUserCouponData = $newUserCouponResult->fetch_assoc();
        $newUserCouponCode = $newUserCouponData['code'];
    }

    if ($newUserCouponCode && $couponCode === $newUserCouponCode) {
        // Fetch user's createdAt for new user check
        $userCreatedAtSql = "SELECT createdAt FROM user WHERE id = ?";
        $userCreatedAtStmt = $conn->prepare($userCreatedAtSql);
        $userCreatedAtStmt->bind_param("i", $currentUser['id']);
        $userCreatedAtStmt->execute();
        $userCreatedAtResult = $userCreatedAtStmt->get_result();
        $userCreatedAtRow = $userCreatedAtResult->fetch_assoc();
        $userCreatedAtStmt->close();

        if ($userCreatedAtRow) {
            $createdAt = new DateTime($userCreatedAtRow['createdAt']);
            $now = new DateTime();
            $interval = $now->diff($createdAt);

            // Case 1: Old user trying to use new user coupon
            if ($interval->days > 30) {
                echo json_encode(['success' => false, 'message' => 'Đây là mã cho người mới, bạn không thể dùng.']);
                exit;
            }

            // Case 2: New user trying to reuse new user coupon
            $checkUsedCouponSql = "
                SELECT 1 FROM order_coupon oc
                JOIN `order` o ON oc.order_id = o.id
                WHERE o.user_id = ? AND oc.coupon_code = ?
                LIMIT 1";
            $checkUsedCouponStmt = $conn->prepare($checkUsedCouponSql);
            $checkUsedCouponStmt->bind_param("is", $currentUser['id'], $newUserCouponCode);
            $checkUsedCouponStmt->execute();
            $checkUsedCouponResult = $checkUsedCouponStmt->get_result();
            if ($checkUsedCouponResult->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Bạn đã sử dụng mã người mới rồi.']);
                exit;
            }
            $checkUsedCouponStmt->close();
        }
    }
    // --- End New User Coupon Specific Validation ---

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
                    $discountAmount = floatval($db_coupon['discountAmount']);
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
