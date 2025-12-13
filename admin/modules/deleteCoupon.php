<?php
require_once __DIR__ . '/../../database/conf.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;
$id = intval($id);

if ($id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID không hợp lệ'
    ]);
    exit;
}

// Lấy ảnh trước khi xóa
$sql = "SELECT image FROM coupon WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$image = null;
if ($result->num_rows > 0) {
    $image = $result->fetch_assoc()['image'];
}

// Xóa DB
$delSql = "DELETE FROM coupon WHERE id = ?";
$delStmt = $conn->prepare($delSql);
$delStmt->bind_param("i", $id);

if ($delStmt->execute()) {

    // Xóa ảnh
    if ($image) {
        $path = __DIR__ . '/../images/coupons/' . $image;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Không thể xóa coupon'
    ]);
}
?>