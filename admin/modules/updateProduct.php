<?php
require_once __DIR__ . '/../../database/conf.php';

// Lấy dữ liệu từ form
$id          = $_POST['id'];
$name        = $_POST['name'];
$productCode = $_POST['productCode'];
$price       = $_POST['price'];
$desc        = $_POST['description'];
$category_id = $_POST['category_id'];

$active      = isset($_POST['active']) ? 1 : 0;
$inStock     = isset($_POST['inStock']) ? 1 : 0;
$inPopular   = isset($_POST['inPopular']) ? 1 : 0;

/* --- Lấy thông tin sản phẩm cũ để biết ảnh cũ và category cũ --- */
$sqlOld = "SELECT productImage, category_id FROM product WHERE id=$id";
$old = $conn->query($sqlOld)->fetch_assoc();

$oldImage = $old['productImage'];
$oldCategory = $old['category_id'];

/* --- Lấy categoryCode mới để xác định thư mục ảnh --- */
$sqlCat = "SELECT categoryCode FROM category WHERE id=$category_id";
$rsCat = $conn->query($sqlCat)->fetch_assoc();
$categoryCode = $rsCat['categoryCode'];

/* --- Thư mục ảnh chính xác --- */
$root   = realpath(__DIR__ . "/../../");
$folder = $root . "/images/" . $categoryCode;

if(!is_dir($folder)){
    mkdir($folder, 0777, true);
}

$newImageName = $oldImage; // mặc định giữ ảnh cũ

/* --- Nếu có upload ảnh mới --- */
if(isset($_FILES['productImage']) && $_FILES['productImage']['error'] === 0){

    $ext = strtolower(pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION));
    $imgName = strtolower($productCode) . "_" . time() . "." . $ext;
    //$imgName = basename($_FILES['productImage']['name']);
    $tmp     = $_FILES['productImage']['tmp_name'];
    $target  = $folder . "/" . $imgName;

    if(move_uploaded_file($tmp, $target)){
        $newImageName = $imgName;

        // Xóa ảnh cũ nếu tồn tại và tên khác
        if(!empty($oldImage) && file_exists($root . "/images/" . getOldCatCode($conn, $oldCategory) . "/" . $oldImage)){
            unlink($root . "/images/" . getOldCatCode($conn, $oldCategory) . "/" . $oldImage);
        }

    } else {
        echo "Lỗi upload ảnh mới.";
        exit();
    }
}

/* --- Cập nhật DB --- */
$sqlUpdate = "
    UPDATE product 
    SET name='$name', 
        productCode='$productCode',
        price='$price',
        description='$desc',
        category_id='$category_id',
        productImage='$newImageName',
        active='$active',
        inStock='$inStock',
        inPopular='$inPopular'
    WHERE id=$id
";

if($conn->query($sqlUpdate)){
    header("Location: ../index.php?mod=manage&ac=products&msg=updated");
    exit();
} else {
    echo "Lỗi cập nhật DB: " . $conn->error;
}


/* --- Function lấy categoryCode cũ --- */
function getOldCatCode($conn, $cate_id){
    $sql = "SELECT categoryCode FROM category WHERE id=$cate_id";
    return $conn->query($sql)->fetch_assoc()['categoryCode'];
}
?>
