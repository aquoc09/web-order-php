<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../database/conf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    if (empty($email)) {
        exit("Vui lòng nhập email!");
    }

    // Check user tồn tại
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $rs = $stmt->get_result();

    if ($rs->num_rows === 0) {
        exit("Email không tồn tại trong hệ thống!");
    }

    // Tạo mật khẩu ngẫu nhiên 8 số
    $newPassword = str_pad(random_int(0, 99999999), 8, "0", STR_PAD_LEFT);

    // Hash mật khẩu
    $hashedPass = password_hash($newPassword, PASSWORD_BCRYPT);

    // Cập nhật DB
    $update = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $update->bind_param("ss", $hashedPass, $email);
    $update->execute();

    // Gửi mail bằng Gmail API (OAuth2)
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->Port       = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth   = true;

        $mail->AuthType = 'XOAUTH2';
        $provider = new Google([
            'clientId'     => getenv('GOOGLE_CLIENT_ID_MAIL'),
            'clientSecret' => getenv('GOOGLE_CLIENT_SECRET_MAIL'),
        ]);

        $mail->setOAuth(new OAuth([
            'provider'        => $provider,
            'clientId'        => getenv('GOOGLE_CLIENT_ID_MAIL'),
            'clientSecret'    => getenv('GOOGLE_CLIENT_SECRET_MAIL'),
            'refreshToken'    => getenv('GOOGLE_REFRESH_TOKEN'),
            'userName'        => getenv('MAIL_SENDER'),
        ]));

        // Sender & Receiver
        $mail->setFrom(getenv('MAIL_SENDER'), "PQ Restaurant");
        $mail->addAddress($email);

        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = "Mật khẩu mới của bạn";
        $mail->Body = "
            Chào bạn,<br><br>
            Mật khẩu mới của bạn là: <b>$newPassword</b><br><br>
            Hãy đăng nhập và đổi mật khẩu ngay để đảm bảo bảo mật.<br><br>
            Trân trọng.
        ";

        $mail->send();

        echo "Mật khẩu mới đã được gửi tới email của bạn!";

    } catch (Exception $e) {
        echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
    }
}
?>