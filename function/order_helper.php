<?php
function create_order_and_details($conn, $userId, $address, $note, $totalMoney, $paymentMethod, $status, $validCoupons = []) {
    $conn->begin_transaction();
    try {
        // 1. Fetch Cart Items and Lock the Rows
        $cartItemsSql = "SELECT c.id as cart_id, ci.product_id, ci.quantity, p.price
                         FROM cart c
                         JOIN cart_item ci ON c.id = ci.cartId
                         JOIN product p ON ci.product_id = p.id
                         WHERE c.user_id = ? AND c.cartStatus = 'active' FOR UPDATE";
        $cartItemsStmt = $conn->prepare($cartItemsSql);
        $cartItemsStmt->bind_param("i", $userId);
        $cartItemsStmt->execute();
        $cartResult = $cartItemsStmt->get_result();

        if ($cartResult->num_rows == 0) {
            throw new Exception("Cart is empty.");
        }

        $cartItems = [];
        $cartId = null;
        while ($row = $cartResult->fetch_assoc()) {
            $cartItems[] = $row;
            if ($cartId === null) {
                $cartId = $row['cart_id'];
            }
        }
        $cartItemsStmt->close();

        // 2. Create Order
        $orderSql = "INSERT INTO `order` (user_id, address, note, orderDate, status, totalMoney, paymentMethod, active)
                     VALUES (?, ?, ?, NOW(), ?, ?, ?, 1)";
        $orderStmt = $conn->prepare($orderSql);
        $orderStmt->bind_param("isssds", $userId, $address, $note, $status, $totalMoney, $paymentMethod);
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

        // 4. Insert Applied Coupons
        if (!empty($validCoupons)) {
            $couponSql = "INSERT INTO order_coupon (order_id, coupon_code, discount_amount) VALUES (?, ?, ?)";
            $couponStmt = $conn->prepare($couponSql);
            foreach ($validCoupons as $coupon) {
                $couponStmt->bind_param("isd", $orderId, $coupon['code'], $coupon['discount_amount']);
                $couponStmt->execute();
            }
            $couponStmt->close();
        }

        // 5. Clear Cart (only if the order is confirmed, not for pending)
        if ($status !== 'pending') {
            $deleteItemsSql = "DELETE FROM cart_item WHERE cartId = ?";
            $deleteItemsStmt = $conn->prepare($deleteItemsSql);
            $deleteItemsStmt->bind_param("i", $cartId);
            $deleteItemsStmt->execute();
            $deleteItemsStmt->close();
        }

        $conn->commit();
        return $orderId;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Order creation failed: " . $e->getMessage());
        return false;
    }
}

function clear_cart($conn, $userId) {
    $conn->begin_transaction();
    try {
        // Find the active cart ID for the user
        $cartSql = "SELECT id FROM cart WHERE user_id = ? AND cartStatus = 'active' LIMIT 1";
        $cartStmt = $conn->prepare($cartSql);
        $cartStmt->bind_param("i", $userId);
        $cartStmt->execute();
        $result = $cartStmt->get_result();
        if ($result->num_rows > 0) {
            $cart = $result->fetch_assoc();
            $cartId = $cart['id'];
            $cartStmt->close();

            // Delete items from that cart
            $deleteItemsSql = "DELETE FROM cart_item WHERE cartId = ?";
            $deleteItemsStmt = $conn->prepare($deleteItemsSql);
            $deleteItemsStmt->bind_param("i", $cartId);
            $deleteItemsStmt->execute();
            $deleteItemsStmt->close();
        }
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Cart clearing failed for user $userId: " . $e->getMessage());
        return false;
    }
}
?>