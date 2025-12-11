<?php
session_start();
// __DIR__ là thư mục hiện tại (includes), /../ là lùi ra thư mục cha (web-order-php)
include_once __DIR__ . '/../database/conf.php';
// include_once __DIR__ . '/../auth/refreshToken.php';
$token = $_COOKIE['auth_token'] ?? '';

if ($token) {
    $sql = "SELECT u.* FROM user_tokens t 
            JOIN user u ON u.id = t.user_id
            WHERE t.token = ? AND t.expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $currentUser = $result->fetch_assoc();
    } else {
        $currentUser = null;
    }
} else {
    $currentUser = null;
}

// Get cart count
$cart_count = 0;
if ($currentUser) {
    $count_sql = "SELECT SUM(ci.quantity) as total_items 
                  FROM cart c 
                  JOIN cart_item ci ON c.id = ci.cartId 
                  WHERE c.user_id = ? AND c.cartStatus = 'active'";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $currentUser['id']);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    if ($row = $count_result->fetch_assoc()) {
        $cart_count = (int)($row['total_items'] ?? 0);
    }
    $count_stmt->close();
}
$name='';
if($currentUser!=null){
    $name = isset($currentUser['fullName']) ?
        $name = $currentUser['fullName']:
        $name =$currentUser['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQ Restaurant</title>
    <link rel="icon" type="image/png" href="./img/logo40x40-circle.png">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body style="background-color: #EADEC5;">

<div class="fixed-top-wrapper">
    <!-- Top bar user -->
    <div class="top-bar" id="user-header">
        <?php if(isset($currentUser)): ?>
            <span>Xin chào, <?= htmlspecialchars($name) ?></span>
            <a href="./auth/logout.php" id="logoutBtn"><i class="bi bi-box-arrow-right"></i>Đăng xuất</a>
            <?php if($currentUser['role'] === 'admin' || $currentUser['role'] === 'manager'): ?>
                <a href="./admin/index.php"><i class="bi bi-person-gear"></i>Quản lí</a>
            <?php endif; ?>
            <a href="./user-form.php"><i class="bi bi-person-circle"></i>Tài khoản</a>
            <a href="./cart.php" class="text-decoration-none"><i class="bi bi-cart"></i> Giỏ hàng (<span id="cart-count"><?= $cart_count ?></span>)</a>
        <?php else: ?>
            <a href="./register-form.php"><i class="bi bi-person-plus"></i>Đăng ký</a>
            <a href="./login-form.php"><i class="bi bi-person-circle"></i>Đăng nhập</a>
        <?php endif; ?>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-custom">
        <div class="container-fluid" id="header-menu">
            <a class="navbar-brand d-flex align-items-center" href="./index.php">
                    <div class="logo"></div>
                </a>

                <div class="hmenu navbar-expand-lg d-none d-lg-block">
                    <ul class="navbar-nav flex-row">
                    <li class="nav-item">
                        <a class="nav-link" href="./promotion.php">Ưu Đãi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./menu.php">Thực Đơn</a>
                    </li>
                    <li>
                        <a class="nav-link" href="./info.php">Thông tin</a>
                    </li>
                    </ul>
                </div>

                <!-- Toggle button (hamburger menu) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar links -->
                <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="./promotion.php">Ưu Đãi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./menu.php">Thực đơn</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./order.php">Order</a>
                        </li>
                        <li>
                            <a class="nav-link" href="./info.php">Thông tin</a>
                        </li>
                    </ul>
                </div>
        </div>
    </nav>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("logoutBtn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (confirm("Bạn có chắc muốn đăng xuất?")) {
                window.location.href = "./auth/logout.php";
            }
        });
    }
});
</script>

