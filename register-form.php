<?php
    $errorUsername = $_GET['errorUsername']??'';
    $errorPassword = $_GET['errorPassword']??'';
    $errorConfirmPassword = $_GET['errorConfirmPassword']??'';
    $errorName = $_GET['errorName']??'';
    $errorPhone = $_GET['errorPhone']??'';
    $errorEmail = $_GET['errorEmail']??'';
?>
<?php include 'includes/header.php';?>
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
    <title>Đăng kí</title>
    <link rel="icon" href="../img/logo/logo40x40-circle.png" type="image/png">
</head>
<!-- Header -->
    
<body>
    <!-- Inject Javascript -->
    
    

    <!-- Register form -->
    <div class="booking-container">
        <h2>Đăng kí</h2>
        <form id="registerForm" method='POST' action='auth/register.php'>
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required />
                <?php if(!empty($errorUsername)): ?>
                    <span class="error-message" name="errorUsername"><?= htmlspecialchars($errorUsername)?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required />
                <div class="showPassword">
                <i class="bi bi-eye ms-2" id="togglePassword" style="cursor: pointer;" onclick="showPassword()">    Hiển thị mật khẩu</i>
                </div>
                <?php if(!empty($errorPassword)): ?>
                    <span class="error-message" name="errorPassword"><?= htmlspecialchars($errorPassword)?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Xác nhận mật khẩu:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required />
                <div class="showPassword">
                <i class="bi bi-eye ms-2" id="toggleConfirmPassword" style="cursor: pointer;" onclick="confirmPassword()">    Hiển thị mật khẩu</i>
                </div>
                <?php if(!empty($errorConfirmPassword)): ?>
                    <span class="error-message" name="errorConfirmPassword"><?= htmlspecialchars($errorConfirmPassword)?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Họ và tên:</label>
                <input type="text" id="fullName" name="fullName" required />
                <?php if(!empty($errorName)): ?>
                    <span class="error-message" name="errorName"><?= htmlspecialchars($errorName)?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="tel" id="phone" name="phone" required />
                <?php if(!empty($errorPhone)): ?>
                    <span class="error-message" name="errorPhone"><?= htmlspecialchars($errorPhone)?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required />
                <?php if(!empty($errorEmail)): ?>
                    <span class="error-message" name="errorEmail"><?= htmlspecialchars($errorEmail)?></span>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn-register justify-content-center">Đăng kí</button>
            </div>
        </form>
    </div>
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script src="js/identity.js"></script>
</body>

</html>