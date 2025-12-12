<?php
function create_order_and_details($conn, $userId, $address, $note, $totalMoney, $paymentMethod, $status, $appliedCoupons = [], $clearCartItems = false) {
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
            if (!$cartItemsStmt->execute()) {
                throw new Exception("Failed to fetch cart items: " . $cartItemsStmt->error);
            }
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
            if (!$orderStmt->execute()) {
                throw new Exception("Failed to create order: " . $orderStmt->error);
            }
            $orderId = $conn->insert_id;
            $orderStmt->close();
    
            // 3. Create Order Details
            $orderDetailSql = "INSERT INTO order_detail (order_id, product_id, numOfProducts, totalMoney) VALUES (?, ?, ?, ?)";
            $detailStmt = $conn->prepare($orderDetailSql);
            foreach ($cartItems as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $detailStmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $itemTotal);
                if (!$detailStmt->execute()) {
                    throw new Exception("Failed to create order details: " . $detailStmt->error);
                }
            }
            $detailStmt->close();
    
                                // 4. Save Applied Coupons
                                if (!empty($appliedCoupons)) {
                                    $couponSql = "INSERT INTO order_coupon (order_id, coupon_code, discount_amount) VALUES (?, ?, ?)";
                                    $couponStmt = $conn->prepare($couponSql);
                                    foreach ($appliedCoupons as $coupon) {
                                        $couponStmt->bind_param("isd", $orderId, $coupon['code'], $coupon['discount_amount']);
                                        if (!$couponStmt->execute()) {
                                            throw new Exception("Failed to save applied coupons: " . $couponStmt->error);
                                        }
                                    }
                                    $couponStmt->close();
                                }            // 5. Clear Cart (if requested)
            if ($clearCartItems && $cartId) {
                $deleteItemsSql = "DELETE FROM cart_item WHERE cartId = ?";
                $deleteItemsStmt = $conn->prepare($deleteItemsSql);
                $deleteItemsStmt->bind_param("i", $cartId);
                if (!$deleteItemsStmt->execute()) {
                    throw new Exception("Failed to clear cart items: " . $deleteItemsStmt->error);
                }
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