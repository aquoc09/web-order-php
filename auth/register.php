<?php
include '../database/conf.php'; // Kết nối DB
include '../function/validateValue.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $fullName = $_POST['fullName'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';

    //Kiểm tra giá trị
    checkRegister($username,$password,$confirmPassword,$fullName,$phone,$email);



    $checkSql = "SELECT * FROM user WHERE username = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("s", $username);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows >0 ) {
        $error = urlencode("Username đã tồn tại");
        header("Location: ../register-form.php?error=$error");
        exit;
    }

    // Hash password trước khi lưu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $default_role = 'user';
    // Thêm user mới vào database
    $insertSql = "INSERT INTO user (username, password, fullName, phone, email, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($insertSql);
    $stmtInsert->bind_param("ssssss", $username, $hashedPassword, $fullName, $phone, $email, $default_role);

    if ($stmtInsert->execute()) {
        // Redirect về trang index hoặc dashboard
        header("Location: ../index.php");
        exit;
    } else {
        $error = urlencode("Đăng ký thất bại. Vui lòng thử lại");
        header("Location: ../register-form.php?error=$error");
        exit;
    }
}
?>
