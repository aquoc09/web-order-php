<?php
include '../function/generateToken.php';
include '../function/validateValue.php';
include '../database/conf.php'; // Kết nối DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    checkLogin($username, $password);

    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {

            $countTokenSql = "SELECT * FROM user_tokens WHERE user_id = ?";
            $countTokenStmt = $conn->prepare($countTokenSql);
            $countTokenStmt->bind_param("i", $user['id']);
            $countTokenStmt->execute();
            $resultCountToken = $countTokenStmt->get_result();
            if($resultCountToken->num_rows >=3 ){
                // Lấy bản ghi đầu tiên
                $firstTokenRow = $resultCountToken->fetch_assoc(); // associative array
                $firstToken = $firstTokenRow['token'];

                // Xóa token đầu tiên để có thể có không gian lưu token mới
                $deleteStmt = $conn->prepare("DELETE FROM user_tokens WHERE token = ?");
                $deleteStmt->bind_param("s", $firstToken);
                $deleteStmt->execute();
            }

            // Tạo token
            $token = generateToken($conn, $user['id']);

            // Gửi token về browser bằng cookie
            setcookie("auth_token", $token, time() + 86400, "/", "", false, true); // httponly = true

            header("Location: ../index.php?message=Đăng nhập thành công");
            exit;
        } else {
            $error = "Sai mật khẩu";
            header("Location: ../login-form.php?error=$error");
            exit;
        }
    } else {
        $error = "Không tìm thấy người dùng";
        header("Location: ../login-form.php?error=$error");
        exit;
    }
}
?>
