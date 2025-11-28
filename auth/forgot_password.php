<?php
include '../database/conf.php';
include '../function/validateValue.php';
include '../application_config.php';
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require '../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../login-form.php?error=Email không tồn tại");
        exit;
    }

    // Tạo mật khẩu mới
    $newPassword = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ0123456789"), 0, 8);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update DB
    $sql = "UPDATE user SET password=? WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashedPassword, $email);
    $stmt->execute();

    try {
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = MAIL_USERNAME;                     //SMTP username
        $mail->Password   = MAIL_APP_PASSWORD;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom(MAIL_SENDER, 'PQ Restaurant');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'RESET PASSWORD';
        $mail->Body    = "Đây là mật khẩu được làm mới dành cho bạn, không tiết lộ cho bất cứ ai và hãy đặt lại mật khẩu:
                             <b>$newPassword</b>";

        $mail->send();
        header("Location: ../login-form.php?message='Gửi mật khẩu thành công'");
    } catch (Exception $e) {
        header("Location: ../login-form.php?message='Gửi mật khẩu thất bại'");
    }

    exit;
}
?>
