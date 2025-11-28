<?php
include_once '../database/conf.php';
$token = $_COOKIE['auth_token'] ?? '';
if ($token) {
    $stmt = $conn->prepare("DELETE FROM user_tokens WHERE token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    setcookie("auth_token", "", time() - 3600, "/");
}
header("Location: ../index.php");
exit;
?>