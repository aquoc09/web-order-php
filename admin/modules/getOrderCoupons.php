<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../database/conf.php';

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid order_id']);
    exit;
}

$orderID = intval($_GET['order_id']);

// Fetch coupon details from order_coupon
$sql = "SELECT c.code, oc.discount_amount
        FROM order_coupon oc
        JOIN coupon c ON oc.coupon_id = c.id
        WHERE oc.order_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $orderID);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$coupons = [];
while ($row = $result->fetch_assoc()) {
    $coupons[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($coupons, JSON_UNESCAPED_UNICODE);
?>