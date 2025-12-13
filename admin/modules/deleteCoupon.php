<?php
require_once __DIR__ . '/../../database/conf.php';

header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing ID"
    ]);
    exit;
}

$id = intval($_GET['id']);

/* --- Lấy ảnh coupon trước khi xóa --- */
$sqlImg = "SELECT image FROM coupon WHERE id = $id";
$rsImg = $conn->query($sqlImg);

$image = null;
if ($rsImg && $rsImg->num_rows > 0) {
    $image = $rsImg->fetch_assoc()['image'];
}

/* --- Xóa coupon --- */
$sql = "DELETE FROM coupon WHERE id = $id";

if ($conn->query($sql) === TRUE) {

    // Xóa ảnh trong images/coupons
    if (!empty($image)) {
        $root = realpath(__DIR__ . "/../../");
        $path = $root . "/images/coupons/" . $image;

        if (file_exists($path)) {
            unlink($path);
        }
    }

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
}
?>
