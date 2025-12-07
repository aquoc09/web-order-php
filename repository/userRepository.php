<?php
function createUser($user, $conn){
    $username      = $user['username']      ?? null;
    $password      = $user['password']      ?? null;
    $fullName      = $user['fullName']      ?? null;
    $phone         = $user['phone']         ?? null;
    $email         = $user['email']         ?? null;
    $googleId      = $user['googleAccountId'] ?? null;
    $facebookId    = $user['facebookAccountId'] ?? null;
    $active        = 1;
    $default_role  = 'user';

    if ($username !== null) {
        $checkSql = "SELECT * FROM user WHERE username = ?";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->bind_param("s", $username);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows > 0) {
            return false; // username đã tồn tại
        }
    }


    $hashedPassword = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

    // Thêm user mới vào database
    $insertSql = "INSERT INTO user (username, password, fullName, phone, email, role, googleAccountId, facebookAccountId, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($insertSql);
    $stmtInsert->bind_param("ssssssssi", $username, $hashedPassword, $fullName, $phone, $email, $default_role, $googleId, $facebookId, $active);

    return $stmtInsert->execute();
}

function findUserById($id, $conn){
    $sql = "SELECT * FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        return $user;
    }
    return null;
}

function findUserByGoogleId($googleId, $conn){
    $sql = "SELECT * FROM user WHERE googleAccountId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $googleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        return $user;
    }
    return null;
}

function findUserByEmail($email, $conn){
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        return $user;
    }
    return null;
}

function updateGoogleId($userId, $googleAccountId, $conn){
    $stmt = $conn->prepare("UPDATE user SET googleAccountId = ? WHERE id = ?");
    $stmt->bind_param("si", $googleAccountId, $userId);
    return $stmt->execute();
}

?>