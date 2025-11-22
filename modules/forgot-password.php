<?php
include '../database/conf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../forgot-password.php?error=Email không tồn tại");
        exit;
    }

    // Tạo mật khẩu mới
    $newPassword = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 8);

    // Update DB
    $sql = "UPDATE user SET password=? WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $newPassword, $email);
    $stmt->execute();

    // Gửi email cho user
    $subject = "Khôi phục mật khẩu";
    $message = "Mật khẩu mới của bạn là: " . $newPassword;
    $headers = "From: no-reply@yourdomain.com";

    mail($email, $subject, $message, $headers);

    header("Location: ../forgot-password.php?success=Mật khẩu mới đã gửi qua email");
    exit;
}
?>
