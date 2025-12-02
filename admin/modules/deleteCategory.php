<?php 
require_once __DIR__ . '/../../database/conf.php';

header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
    exit;
}

$id = intval($_GET['id']);

/* 1. Kiểm tra xem Category có sản phẩm hay không */
$checkSql = "SELECT COUNT(*) AS total FROM product WHERE category_id = $id";
$checkResult = $conn->query($checkSql);
$row = $checkResult->fetch_assoc();

if ($row['total'] > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Không thể xóa! Danh mục đang có {$row['total']} sản phẩm liên kết."
    ]);
    exit;
}

/* 2. Xóa danh mục nếu không có sản phẩm liên kết */
$sql = "DELETE FROM category WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>
