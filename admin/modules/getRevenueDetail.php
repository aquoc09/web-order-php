<?php
require_once "../database/conf.php";

if (!isset($_GET['date'])) {
    echo json_encode([]);
    exit;
}

$date = $_GET['date'];

// Lấy order theo ngày
$sql = "SELECT o.id, o.orderDate, o.totalMoney, o.status, u.fullName AS userName
        FROM `order` o
        LEFT JOIN user u ON o.user_id = u.id
        WHERE DATE(o.orderDate) = ?
        ORDER BY o.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>