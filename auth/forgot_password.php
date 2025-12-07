<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../database/conf.php';
require __DIR__ . '/../function/sendMailGmailApi.php'; // file có hàm sendMailGmail()

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    if (empty($email)) {
        exit("Vui lòng nhập email!");
    }

    // Kiểm tra email tồn tại
    $stmt = $conn->prepare("SELECT id FROM user WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $rs = $stmt->get_result();

    if ($rs->num_rows === 0) {
        exit("Email không tồn tại!");
    }

    // Tạo mật khẩu mới 8 số
    $chars = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
    $newPassword = substr(str_shuffle($chars), 0, 8);

    // Mã hoá mật khẩu
    $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

    // Cập nhật DB
    $update = $conn->prepare("UPDATE user SET password=? WHERE email=?");
    $update->bind_param("ss", $hashed, $email);
    $update->execute();

    // Gửi email bằng Gmail API
    $subject = "RESET PASSWORD PQ RESTAURANT";
    $body = "
        Chào bạn,<br><br>
        Mật khẩu mới của bạn là: <b>$newPassword</b><br><br>
        Vui lòng đăng nhập và đổi mật khẩu ngay.<br><br>
        Trân trọng,<br>
        PQ Restaurant
    ";

    try {
        sendMailGmail($email, $subject, $body);
        header("Location: ../login-form.php?message='Đã gửi email thành công cho $email'");
        exit;
    } catch (Exception $e) {
        header("Location: ../login-form.php?message='Gửi mail thất bại");
        exit;
    }
}
