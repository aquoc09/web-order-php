<?
session_start();
//include '../includes/header.php';
include '../function/order_helper.php'; // Include the new helper

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

    // --- Handle payment method ---
    if ($paymentMethod === 'cod') {
        // For COD, create order, details, and clear cart immediately.
        $orderId = create_order_and_details($conn, $userId, $address, $note, $totalMoney, 'cod', 'pending');
        
        if ($orderId) {
            // Clear the cart after successful order creation
            clear_cart($conn, $userId);
            header("Location: ../order_success.php?order_id=" . $orderId);
            exit;
        } else {
            header("Location: ../checkout.php?error=Đã có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.");
            exit;
        }

    } elseif ($paymentMethod === 'vnpay') {
        // For VNPAY, create a 'pending_payment' order first.
        // The cart will be cleared in vnpay_return.php after successful payment.
        $orderId = create_order_and_details($conn, $userId, $address, $note, $totalMoney, 'vnpay', 'pending');

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
