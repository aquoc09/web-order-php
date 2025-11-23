<?php
include '../includes/header.php';

// Check if user is logged in
if (!$currentUser) {
    header("Location: ../login-form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $currentUser['id'];
    $fullName = $_POST['fullName'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'] ?? '';
    $paymentMethod = $_POST['paymentMethod'] ?? 'cod';

    // Basic validation
    if (empty($fullName) || empty($phone) || empty($address)) {
        // Handle error - maybe redirect back with an error message
        header("Location: ../checkout.php?error=Vui lòng điền đầy đủ thông tin.");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Fetch Cart Items and Lock the Rows
        $cartSql = "SELECT c.id as cart_id, ci.product_id, ci.quantity, p.price 
                    FROM cart c
                    JOIN cart_item ci ON c.id = ci.cartId
                    JOIN product p ON ci.product_id = p.id
                    WHERE c.user_id = ? AND c.cartStatus = 'active' FOR UPDATE";
        $cartStmt = $conn->prepare($cartSql);
        $cartStmt->bind_param("i", $userId);
        $cartStmt->execute();
        $cartResult = $cartStmt->get_result();
        
        if ($cartResult->num_rows == 0) {
            throw new Exception("Giỏ hàng trống.");
        }
        
        $cartItems = [];
        $totalMoney = 0;
        $cartId = null;

        while($row = $cartResult->fetch_assoc()) {
            $cartItems[] = $row;
            $totalMoney += $row['price'] * $row['quantity'];
            if ($cartId === null) {
                $cartId = $row['cart_id'];
            }
        }
        $cartStmt->close();

        // 2. Create Order
        $orderSql = "INSERT INTO `order` (user_id, address, note, orderDate, status, totalMoney, paymentMethod, active) 
                     VALUES (?, ?, ?, NOW(), 'pending', ?, ?, 1)";
        $orderStmt = $conn->prepare($orderSql);
        $orderStmt->bind_param("issds", $userId, $address, $note, $totalMoney, $paymentMethod);
        $orderStmt->execute();
        $orderId = $conn->insert_id;
        $orderStmt->close();

        // 3. Create Order Details
        $orderDetailSql = "INSERT INTO order_detail (order_id, product_id, numOfProducts, totalMoney) VALUES (?, ?, ?, ?)";
        $detailStmt = $conn->prepare($orderDetailSql);
        foreach ($cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $detailStmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $itemTotal);
            $detailStmt->execute();
        }
        $detailStmt->close();

        // 4. Clear Cart
        $deleteItemsSql = "DELETE FROM cart_item WHERE cartId = ?";
        $deleteItemsStmt = $conn->prepare($deleteItemsSql);
        $deleteItemsStmt->bind_param("i", $cartId);
        $deleteItemsStmt->execute();
        $deleteItemsStmt->close();

        // Optionally, deactivate the cart instead of deleting items
        // $updateCartSql = "UPDATE cart SET cartStatus = 'completed' WHERE id = ?";

        // Commit transaction
        $conn->commit();

        // Redirect to a success page
        header("Location: ../order_success.php?order_id=" . $orderId);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        // Log error and redirect to an error page
        error_log("Order creation failed: " . $e->getMessage());
        header("Location: ../checkout.php?error=Đã có lỗi xảy ra. Vui lòng thử lại.");
        exit;
    }
} else {
    // If not a POST request, redirect to home
    header("Location: ../index.php");
    exit;
}
?>
