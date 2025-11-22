<?php
function generateToken($conn, $userId){
    // Tạo token
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", time() + 86400); // 24h
    $refreshTime = date("Y-m-d H:i:s", time() + 172800); // 48h

    // Lưu token vào DB
    $stmt2 = $conn->prepare("INSERT INTO user_tokens (user_id, token, expires_at, refresh_time) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("isss", $userId, $token, $expires, $refreshTime);
    $stmt2->execute();

    return $token;
}
?>