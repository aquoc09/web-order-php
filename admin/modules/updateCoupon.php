<?php
require_once __DIR__ . '/../../database/conf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$id              = intval($_POST['id']);
$code            = trim($_POST['code']);
$discountAmount  = floatval($_POST['discountAmount']);
$conditionAmount = floatval($_POST['conditionAmount']);
$description     = $_POST['description'] ?? '';
$active          = isset($_POST['active']) ? 1 : 0;

// ===== Lấy ảnh cũ =====
$oldImage = null;
$getSql = "SELECT image FROM coupon WHERE id = ?";
$getStmt = $conn->prepare($getSql);
$getStmt->bind_param("i", $id);
$getStmt->execute();
$getResult = $getStmt->get_result();

if ($getResult->num_rows > 0) {
    $oldImage = $getResult->fetch_assoc()['image'];
}

// ===== Upload ảnh mới (nếu có) =====
$newImage = $oldImage;

if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/../images/coupons/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $newImage = 'coupon_' . time() . '.' . $ext;

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $uploadDir . $newImage
    );

    // Xóa ảnh cũ
    if ($oldImage && file_exists($uploadDir . $oldImage)) {
        unlink($uploadDir . $oldImage);
    }
}

// ===== Update DB =====
$sql = "UPDATE coupon 
        SET code = ?, 
            discountAmount = ?, 
            conditionAmount = ?, 
            description = ?, 
            active = ?, 
            image = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sddsisi",
    $code,
    $discountAmount,
    $conditionAmount,
    $description,
    $active,
    $newImage,
    $id
);

$stmt->execute();

// Redirect về danh sách coupon
header("Location: ../index.php?mod=general&ac=coupon");
exit;
?>