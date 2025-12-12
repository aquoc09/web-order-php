<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../database/conf.php';

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid order_id']);
    exit;
}

$orderID = intval($_GET['order_id']);

$response = [];

// Get Product Details
$productSql = "SELECT od.id, od.product_id, od.numOfProducts, od.totalMoney,
               p.name AS productName, p.productImage, c.categoryCode
        FROM order_detail od
        LEFT JOIN product p ON od.product_id = p.id
        LEFT JOIN category c ON p.category_id = c.id
        WHERE od.order_id = ?";

$stmt = $conn->prepare($productSql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Product query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $orderID);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Product query execution failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

$response['products'] = $products;

// Get Coupon Details
$couponSql = "SELECT coupon_code, discount_amount FROM order_coupon WHERE order_id = ?";
$couponStmt = $conn->prepare($couponSql);
if (!$couponStmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Coupon query preparation failed: ' . $conn->error]);
    exit;
}

$couponStmt->bind_param("i", $orderID);

if (!$couponStmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Coupon query execution failed: ' . $couponStmt->error]);
    exit;
}

$couponResult = $couponStmt->get_result();
$coupons = [];
while ($row = $couponResult->fetch_assoc()) {
    $coupons[] = $row;
}
$couponStmt->close();

$response['coupons'] = $coupons;

$conn->close();

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
