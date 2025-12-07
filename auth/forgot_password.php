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
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $rs = $stmt->get_result();

    if ($rs->num_rows === 0) {
        exit("Email không tồn tại!");
    }

    // Tạo mật khẩu mới 8 số
    $newPassword = str_pad(random_int(0, 99999999), 8, "0", STR_PAD_LEFT);

    // Mã hoá mật khẩu
    $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

    // Cập nhật DB
    $update = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $update->bind_param("ss", $hashed, $email);
    $update->execute();

    // Gửi email bằng Gmail API
    $subject = "Mật khẩu mới của bạn";
    $body = "
        Chào bạn,<br><br>
        Mật khẩu mới của bạn là: <b>$newPassword</b><br><br>
        Vui lòng đăng nhập và đổi mật khẩu ngay.<br><br>
        Trân trọng,<br>
        PQ Restaurant
    ";

    try {
        sendMailGmail($email, $subject, $body);
        echo "Mật khẩu mới đã được gửi tới email của bạn!";
    } catch (Exception $e) {
        echo "Không thể gửi email: " . $e->getMessage();
    }
}
