<?php
require_once __DIR__ . '/../../database/conf.php'; // kết nối DB

$code            = $_POST['code'];
$discountAmount  = $_POST['discountAmount'];
$conditionAmount = $_POST['conditionAmount'];
$description     = $_POST['description'] ?? '';

$active = isset($_POST['active']) ? 1 : 0;

/* ===== Folder coupons trong images ===== */
$root   = realpath(__DIR__ . "/../../"); // root project
$folder = $root . "/images/coupons";

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

/* ===== Upload ảnh ===== */
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    // đặt tên file giống product
    $imgName = strtolower($code) . "_" . time() . "." . $ext;

    $tmp    = $_FILES['image']['tmp_name'];
    $target = $folder . "/" . $imgName;

    if (move_uploaded_file($tmp, $target)) {

        // ===== Lưu DB =====
        $sql = "INSERT INTO coupon(code, discountAmount, conditionAmount, image, description, active)
                VALUES ('$code', '$discountAmount', '$conditionAmount', '$imgName', '$description', '$active')";

        if ($conn->query($sql)) {
            header("Location: ../index.php?mod=general&ac=coupon&msg=success");
            exit();
        } else {
            echo "Lỗi lưu DB: " . $conn->error;
        }

    } else {
        echo "Lỗi upload file ảnh coupon.";
    }

} else {
    echo "Chưa chọn file ảnh hoặc file bị lỗi.";
}
