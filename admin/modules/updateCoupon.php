<?php
require_once __DIR__ . '/../../database/conf.php';

// ===== Lấy dữ liệu từ form =====
$id              = $_POST['id'];
$code            = trim($_POST['code']);
$discountAmount  = $_POST['discountAmount'];
$conditionAmount = $_POST['conditionAmount'];
$description     = $_POST['description'] ?? '';
$active          = isset($_POST['active']) ? 1 : 0;

/* --- Lấy coupon cũ để biết ảnh cũ --- */
$sqlOld = "SELECT image FROM coupon WHERE id=$id";
$old = $conn->query($sqlOld)->fetch_assoc();
$oldImage = $old['image'];

/* --- Thư mục ảnh coupons --- */
$root   = realpath(__DIR__ . "/../../"); // root project
$folder = $root . "/images/coupons";

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$newImageName = $oldImage; // mặc định giữ ảnh cũ

/* --- Nếu upload ảnh mới --- */
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $imgName = strtolower($code) . "_" . time() . "." . $ext;

    $tmp    = $_FILES['image']['tmp_name'];
    $target = $folder . "/" . $imgName;

    if (move_uploaded_file($tmp, $target)) {

        $newImageName = $imgName;

        // Xóa ảnh cũ nếu tồn tại
        if (!empty($oldImage) && file_exists($folder . "/" . $oldImage)) {
            unlink($folder . "/" . $oldImage);
        }

    } else {
        echo "Lỗi upload ảnh coupon.";
        exit();
    }
}

/* --- Cập nhật DB --- */
$sqlUpdate = "
    UPDATE coupon 
    SET code='$code',
        discountAmount='$discountAmount',
        conditionAmount='$conditionAmount',
        description='$description',
        image='$newImageName',
        active='$active'
    WHERE id=$id
";

if ($conn->query($sqlUpdate)) {
    header("Location: ../index.php?mod=manage&ac=coupons&msg=updated");
    exit();
} else {
    echo "Lỗi cập nhật DB: " . $conn->error;
}
?>
