<?php
include '../database/conf.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Hành động không hợp lệ.'];

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

// 2. If user is not authenticated, block action
if (!$currentUser) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng giỏ hàng.']);
    exit;
}

// Get user's active cart ID
$userId = $currentUser['id'];
$cartId = null;
$cartSql = "SELECT id FROM cart WHERE user_id = ? AND cartStatus = 'active'";
$cartStmt = $conn->prepare($cartSql);
$cartStmt->bind_param("i", $userId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();
if ($cartResult->num_rows > 0) {
    $cartId = $cartResult->fetch_assoc()['id'];
}
$cartStmt->close();


$action = $_POST['action'] ?? '';
$productId = (int)($_POST['id'] ?? 0);

// Main logic switch
if ($cartId || $action == 'add') { // Allow 'add' to create a cart
    $conn->begin_transaction();
    try {
        switch ($action) {
            case 'add':
                $quantity = (int)($_POST['quantity'] ?? 1);
                if ($productId > 0 && $quantity > 0) {
                    if (!$cartId) { // Create cart if it doesn't exist
                        $insertCartSql = "INSERT INTO cart (user_id, cartStatus) VALUES (?, 'active')";
                        $insertCartStmt = $conn->prepare($insertCartSql);
                        $insertCartStmt->bind_param("i", $userId);
                        $insertCartStmt->execute();
                        $cartId = $conn->insert_id;
                    }
                    // Check if item exists
                    $itemSql = "SELECT quantity FROM cart_item WHERE cartId = ? AND product_id = ?";
                    $itemStmt = $conn->prepare($itemSql);
                    $itemStmt->bind_param("ii", $cartId, $productId);
                    $itemStmt->execute();
                    if ($itemStmt->get_result()->num_rows > 0) {
                        $updateSql = "UPDATE cart_item SET quantity = quantity + ? WHERE cartId = ? AND product_id = ?";
                        $updateStmt = $conn->prepare($updateSql);
                        $updateStmt->bind_param("iii", $quantity, $cartId, $productId);
                        $updateStmt->execute();
                    } else {
                        $insertSql = "INSERT INTO cart_item (cartId, product_id, quantity) VALUES (?, ?, ?)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->bind_param("iii", $cartId, $productId, $quantity);
                        $insertStmt->execute();
                    }
                } else {
                    throw new Exception('Dữ liệu sản phẩm không hợp lệ.');
                }
                break;

            case 'update':
                $quantity = (int)($_POST['quantity'] ?? 0);
                if ($productId > 0 && $quantity > 0) {
                    $updateSql = "UPDATE cart_item SET quantity = ? WHERE cartId = ? AND product_id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("iii", $quantity, $cartId, $productId);
                    $updateStmt->execute();
                } else if ($productId > 0 && $quantity <= 0) { // Treat quantity 0 as removal
                    $deleteSql = "DELETE FROM cart_item WHERE cartId = ? AND product_id = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("ii", $cartId, $productId);
                    $deleteStmt->execute();
                } 
                else {
                    throw new Exception('Dữ liệu sản phẩm không hợp lệ.');
                }
                break;
            
            case 'remove':
                if ($productId > 0) {
                    $deleteSql = "DELETE FROM cart_item WHERE cartId = ? AND product_id = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("ii", $cartId, $productId);
                    $deleteStmt->execute();
                } else {
                    throw new Exception('Dữ liệu sản phẩm không hợp lệ.');
                }
                break;

            default:
                throw new Exception('Hành động không hợp lệ.');
        }

        // Recalculate totals and send response
        $totals = ['count' => 0, 'total_price' => 0];
        $totalSql = "SELECT SUM(ci.quantity) as total_items, SUM(ci.quantity * p.price) as total_price
                     FROM cart_item ci
                     JOIN product p ON ci.product_id = p.id
                     WHERE ci.cartId = ?";
        $totalStmt = $conn->prepare($totalSql);
        $totalStmt->bind_param("i", $cartId);
        $totalStmt->execute();
        $totalResult = $totalStmt->get_result();
        if($row = $totalResult->fetch_assoc()){
            $totals['count'] = (int)($row['total_items'] ?? 0);
            $totals['total_price'] = (float)($row['total_price'] ?? 0);
        }

        $conn->commit();
        $response = [
            'success' => true,
            'cart_count' => $totals['count'],
            'total_price' => $totals['total_price'],
            'message' => 'Giỏ hàng đã được cập nhật.'
        ];

    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Đã có lỗi xảy ra: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Không tìm thấy giỏ hàng của bạn.';
}


echo json_encode($response);
?>
