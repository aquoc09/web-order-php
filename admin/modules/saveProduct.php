<?php
require_once __DIR__ . '/../../database/conf.php'; // kết nối DB

$name        = $_POST['name'];
$productCode = $_POST['productCode'];
$price       = $_POST['price'];
$desc        = $_POST['description'];
$category_id = $_POST['category_id'];

$active      = isset($_POST['active']) ? 1 : 0;
$inStock     = isset($_POST['inStock']) ? 1 : 0;
$inPopular   = isset($_POST['inPopular']) ? 1 : 0;

/* Lấy categoryCode để đặt folder */
$sqlCat = "SELECT categoryCode FROM category WHERE id=$category_id";
$rsCat = $conn->query($sqlCat)->fetch_assoc();
$categoryCode = $rsCat['categoryCode'];

/* Tạo thư mục nếu chưa có - đặt ở tầng gốc project */
$root = realpath(__DIR__ . "/../../"); // D:/Programs/wamp64/www/web_order_php
$folder = $root . "/images/" . $categoryCode;

if(!is_dir($folder)){
    mkdir($folder, 0777, true);
}

/* Xử lý upload ảnh */
if(isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0){
    
    $ext = strtolower(pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION));
    $imgName = strtolower($productCode) . "_" . time() . "." . $ext;
    
    //$imgName = basename($_FILES['productImage']['name']);
    $tmp     = $_FILES['productImage']['tmp_name'];

    $target = $folder . "/" . $imgName;
    if(move_uploaded_file($tmp, $target)){
        // lưu DB
        $sql = "INSERT INTO product(name, productCode, price, productImage, description, category_id, inStock, inPopular, active)
                VALUES ('$name', '$productCode', '$price', '$imgName', '$desc', '$category_id', '$inStock', '$inPopular', '$active')";

        if($conn->query($sql)){
            header("Location: ../index.php?mod=manage&ac=products&msg=success");
            exit();
        } else {
            echo "Lỗi lưu DB: " . $conn->error;
        }
    } else {
        echo "Lỗi upload file.";
    }
} else {
    echo "Chưa chọn file ảnh hoặc file bị lỗi.";
}
?>
