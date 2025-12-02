<?php
require_once __DIR__ . '/../../database/conf.php';

header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
    exit;
}

$id = intval($_GET['id']);

// Xóa sản phẩm
$sql = "DELETE FROM product WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>