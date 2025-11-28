<?php
require_once __DIR__ . '/../../database/conf.php';

$name        = $_POST['name'];
$categoryCode = $_POST['categoryCode'];
$active      = isset($_POST['active']) ? 1 : 0;

$sql = "INSERT INTO category(name, categoryCode, active)
                VALUES ('$name', '$categoryCode', '$active')";

if($conn->query($sql)){
    header("Location: ../index.php?mod=manage&ac=categories&msg=success");
    exit();
} else {
    echo "Lỗi lưu DB: " . $conn->error;
}

?>
