<?php
require_once __DIR__ . '/../../database/conf.php';

/* Lấy dữ liệu */
$id        = $_POST['id']?? "";
$fullName  = $_POST['fullName']?? "";
$username  = $_POST['username'] ?? "";
$email     = $_POST['email']?? "";
$phone     = $_POST['phone']?? "";
$address   = $_POST['address']?? "";

$role      = $_POST['role']?? "";
$active    = isset($_POST['active']) ? 1 : 0;

/* Nếu có nhập mật khẩu mới thì hash, không thì giữ nguyên */
$password = $_POST['password']?? "";
$newPasswordHash = null;

if(!empty($password)){
    $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
}

if(!empty($username)){
    /* Kiểm tra username trùng (trừ chính user đó) */
    $sqlCheck = "SELECT id FROM user WHERE username=? AND id<>?";
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    $rsCheck = $stmt->get_result();

    if($rsCheck->num_rows > 0){
        header("Location: ../index.php?mod=manage&ac=users&msg=error-user-exist");
        exit();
    }
}

/* Lấy thông tin user cũ */
$sqlOld = "SELECT userImage FROM user WHERE id=?";
$stmtOld = $conn->prepare($sqlOld);
$stmtOld->bind_param("i", $id);
$stmtOld->execute();
$oldData = $stmtOld->get_result()->fetch_assoc();

$oldImage = $oldData['userImage'];

/* Tạo thư mục nếu chưa có - đặt ở tầng gốc project */
$root = realpath(__DIR__ . "/../../images"); // D:/Programs/wamp64/www/web_order_php
$folder = $root . "/users";

if(!is_dir($folder)){
    mkdir($folder, 0777, true);
}

/* Xử lý upload ảnh */
$newImageName = $oldImage; // giữ ảnh cũ nếu không upload

if(isset($_FILES['userImage']) && $_FILES['userImage']['error'] == 0){

    $ext = strtolower(pathinfo($_FILES['userImage']['name'], PATHINFO_EXTENSION));
    $newImageName = strtolower($username) . "_" . time() . "." . $ext;

    $tmp = $_FILES['userImage']['tmp_name'];
    $target = $folder . "/" . $newImageName;

    if(move_uploaded_file($tmp, $target)){
        // Xóa ảnh cũ nếu tồn tại
        if(!empty($oldImage) && file_exists($folder . "/" . $oldImage)){
            unlink($folder . "/" . $oldImage);
        }
    } else {
        header("Location: ../index.php?mod=manage&ac=users&msg=error-upload");
        exit();
    }
}

/* Cập nhật DB */
if($newPasswordHash){
    // Có cập nhật mật khẩu
    $sql = "UPDATE user SET fullname=?, username=?, password=?, email=?, phone=?, address=?, role=?, userImage=?, active=? 
            WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssii", 
        $fullName, $username, $newPasswordHash, $email, $phone, $address, 
        $role, $newImageName, $active, $id
    );

} else {
    // Không cập nhật mật khẩu
    $sql = "UPDATE user SET fullname=?, username=?, email=?, phone=?, address=?, role=?, userImage=?, active=? 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", 
        $fullName, $username, $email, $phone, $address, 
        $role, $newImageName, $active, $id
    );
}

if($stmt->execute()){
    header("Location: ../index.php?mod=manage&ac=users&msg=updated");
    exit();
} else {
    header("Location: ../index.php?mod=manage&ac=users&msg=error-update");
    exit();
}
?>
