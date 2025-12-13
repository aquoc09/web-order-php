<?php
require_once __DIR__ . '/../../database/conf.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;
$id = intval($id);

if ($id <= 0) {
    echo json_encode(['error' => 'ID không hợp lệ']);
    exit;
}

$sql = "SELECT * FROM coupon WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Không tìm thấy coupon']);
    exit;
}

echo json_encode($result->fetch_assoc());
?>