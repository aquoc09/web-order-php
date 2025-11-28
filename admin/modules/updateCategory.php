<?php
require_once __DIR__ . '/../../database/conf.php';

$id = $_POST['id'];
$name        = $_POST['name'];
$productCategory = $_POST['productCategory'];
$active      = isset($_POST['active']) ? 1 : 0;



$sql = "UPDATE `category` 
        SET `name`='$name',`categoryCode`='$categoryCode',`active`='$active' 
        WHERE `id` = '$id'";

if($conn->query($sql)){
    header("Location: ../index.php?mod=manage&ac=products&msg=success");
    exit();
} else {
    echo "Lỗi lưu DB: " . $conn->error;
}

?>
