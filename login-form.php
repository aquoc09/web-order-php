<?php
    $error = $_GET['error']??'';
    include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/form.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Grandstander:ital,wght@0,100..900;1,100..900&family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <title>Đăng nhập</title>
    <link rel="icon" href="../img/logo/logo40x40-circle.png" type="image/png">
</head>
<body>
    <!-- Inject Javascript -->
    

    <!-- Login form -->
    <div class="booking-container">
        <h2>Đăng nhập</h2>
        <form method='POST' id="loginForm" action="modules/login.php">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required />
                <span class="error-message" id="usernameErrorMessage"></span>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required />
                <div class="showPassword">
                <i class="bi bi-eye ms-2" id="togglePassword" style="cursor: pointer;" onclick="showPassword()">    Hiển thị mật khẩu</i>
                </div>
                <span class="error-message" id="passwordErrorMessage" name="password"></span>
            </div>

            <div class="d-flex row justify-content-center gap-3">
                <button type="submit" class="btn-booking col-md-4 justify-content-center">Đăng nhập</button>
                <button type="signin" class="btn-register col-md-4 justify-content-center" onclick="window.location.href='register-form.php'">Đăng kí</button>
            </div>
        </form>
    </div>
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script src="js/identity.js"></script>
</body>

</html>