<?php
require_once __DIR__ . '/../../database/conf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$code            = trim($_POST['code']);
$discountAmount  = floatval($_POST['discountAmount']);
$conditionAmount = floatval($_POST['conditionAmount']);
$description     = $_POST['description'] ?? '';
$active          = isset($_POST['active']) ? 1 : 0;

// ===== Xử lý upload ảnh =====
$imageName = null;

if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/coupons/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageName = 'coupon_' . time() . '.' . $ext;
    $uploadPath = $uploadDir . $imageName;

    move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
}

// ===== Insert DB =====
$sql = "INSERT INTO coupon 
        (code, active, discountAmount, conditionAmount, image, description)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "siddds",
    $code,
    $active,
    $discountAmount,
    $conditionAmount,
    $imageName,
    $description
);

if ($stmt->execute()) {
    header("Location: ../index.php?page=coupon&success=1");
} else {
    echo "Lỗi khi thêm coupon!";
}
?>