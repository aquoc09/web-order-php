<?php
header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Missing order ID']);
    exit;
}

require_once __DIR__ . '/../../database/conf.php';

$id = intval($_GET['id']);

// Lấy order + user name
$sql = "SELECT o.*, u.fullName AS userName 
        FROM `order` o
        LEFT JOIN user u ON o.user_id = u.id
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$order = $result->fetch_assoc();

echo json_encode($order, JSON_UNESCAPED_UNICODE);
exit;
?>
