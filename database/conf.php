<?php
// // Bắt đầu session nếu chưa bắt đầu
// if(session_status() == PHP_SESSION_NONE){
//     session_start();
// }

// // Cấu hình database
// $host = "localhost";      // tên host, thường là localhost
// $user = "root";           // username MySQL
// $pass = "";               // password MySQL
// $db   = "quanlynhahang";        // tên database

// // Kết nối
// $conn = new mysqli($host, $user, $pass, $db);

// // Kiểm tra kết nối
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// // Thiết lập charset (UTF-8)
// $conn->set_charset("utf8");

// // Hàm tiện lợi để escape input (tránh SQL Injection)
// function escape($str) {
//     global $conn;
//     return $conn->real_escape_string($str);
// }

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") ?: "";
$db   = getenv("DB_NAME") ?: "quanlynhahang";
$port = getenv("DB_PORT") ?: 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

function escape($str) {
    global $conn;
    return $conn->real_escape_string($str);
}


?>
