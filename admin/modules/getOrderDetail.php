<?php
header('Content-Type: application/json');

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo json_encode(['error' => 'Missing order_id']);
    exit;
}

require_once __DIR__ . '/../../database/conf.php';

$orderID = intval($_GET['order_id']);

$sql = "SELECT od.id, od.product_id, od.numOfProducts, od.totalMoney,
               p.name AS productName, p.productImage, c.categoryCode
        FROM order_detail od
        LEFT JOIN product p ON od.product_id = p.id
        LEFT JOIN category c ON p.category_id = c.id
        WHERE od.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

$details = [];
while ($row = $result->fetch_assoc()) {
    $details[] = $row;
}

echo json_encode($details, JSON_UNESCAPED_UNICODE);
exit;
?>
