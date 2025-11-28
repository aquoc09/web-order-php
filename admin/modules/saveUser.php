<?php
require_once __DIR__ . '/../../database/conf.php';

$fullName = $_POST['fullName'];
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];

$role  = $_POST['role'];
$active   = isset($_POST['active']) ? 1 : 0;

/* Mã hoá mật khẩu */
$hashed = password_hash($password, PASSWORD_DEFAULT);

/* Kiểm tra username trùng */
$sqlCheck = "SELECT id FROM user WHERE username='$username'";
$rsCheck  = $conn->query($sqlCheck);

if($rsCheck->num_rows > 0){
    header("Location: ../index.php?mod=general&ac=users&msg=error-user-exist");
    exit();
}


/* Tạo thư mục nếu chưa có - đặt ở tầng gốc project */
$root = realpath(__DIR__ . "/../../images"); // D:/Programs/wamp64/www/web_order_php
$folder = $root . "/users";

if(!is_dir($folder)){
    mkdir($folder, 0777, true);
}

/* Xử lý upload ảnh */
if(isset($_FILES['userImage']) && $_FILES['userImage']['error'] == 0){
    
    $ext = strtolower(pathinfo($_FILES['userImage']['name'], PATHINFO_EXTENSION));
    $imgName = strtolower($username) . "_" . time() . "." . $ext;
    
    //$imgName = basename($_FILES['productImage']['name']);
    $tmp     = $_FILES['userImage']['tmp_name'];

    $target = $folder . "/" . $imgName;
    if(move_uploaded_file($tmp, $target)){
        /* Thêm user */
        $sql = "INSERT INTO user(fullname, username, password, email, phone, address, role, userImage, active)
                    VALUES ('$fullName', '$username', '$hashed', '$email', '$phone', '$address', '$role', '$imgName', '$active')";


        if($conn->query($sql)){
            header("Location: ../index.php?mod=manage&ac=users&msg=success");
            exit();
        } else {
            header("Location: ../index.php?mod=manage&ac=users&msg=error-save-user");
            exit();
        }
    } else {
        echo "Lỗi upload file.";
    }
} else {
    echo "Chưa chọn file ảnh hoặc file bị lỗi.";
}
?>
