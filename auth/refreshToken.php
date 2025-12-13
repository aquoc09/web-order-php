<?php
include_once '../database/conf.php';
include 'generateToken.php';
$token = $_COOKIE['auth_token'] ?? '';
if ($token) {
    $stmt = $conn->prepare("SELECT * FROM user_tokens WHERE token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $countTokenStmt->get_result();
    $returnToken = $result->fetch_assoc();
    if($returnToken){
        $now = new DateTime();
        $refreshTime = new DateTime($returnToken['refresh_time']);
        $userId = $returnToken['user_id'];
        if($refreshTime > $now){
            $token = generateToken($conn, $userId);
            // Gửi token về browser bằng cookie
            setcookie("auth_token", $token, time() + 86400, "/", "", false, true); // httponly = true
        }
    }
}
header("Location: ../index.php");
exit;
?>