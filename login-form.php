<?php
    $errorUsername = $_GET['errorUsername']??'';
    $errorPassword = $_GET['errorPassword']??'';
    $error = $_GET['error']??'';
    $message = $_GET['message']??'';
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
    <!-- Login form -->
    <div class="booking-container">
        <h2>Đăng nhập</h2>
        <form method='POST' id="loginForm" action="auth/login.php">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required />
                <?php if(!empty($errorUsername)): ?>
                    <span class="error-message" id="passwordErrorMessage" name="errorUsername"><?= htmlspecialchars($errorUsername)?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required />
                <?php if(!empty($errorPassword)): ?>
                    <span class="error-message" id="passwordErrorMessage" name="errorPassword"><?= htmlspecialchars($errorPassword)?></span>
                <?php endif; ?>
                <div class="showPassword">
                <i class="bi bi-eye ms-2" id="togglePassword" style="cursor: pointer;" onclick="showPassword()">    Hiển thị mật khẩu</i>
                </div>
                <div class="showPassword">
                    <i class="bi bi-key ms-2" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                        Quên mật khẩu?
                    </i>
                </div>
                <?php if(!empty($error)): ?>
                    <span class="error-message" id="passwordErrorMessage" name="error"><?= htmlspecialchars($error)?></span>
                <?php endif; ?>
            </div>

            <div class="d-flex row justify-content-center gap-3">
                <button type="submit" class="btn-booking col-md-4 justify-content-center">Đăng nhập</button>
                <button type="signin" class="btn-register col-md-4 justify-content-center" onclick="window.location.href='register-form.php'">Đăng kí</button>
            </div>
            <!-- Divider -->
            <div class="text-center my-3">
                <span>— Hoặc —</span>
            </div>

            <!-- Social Login Buttons -->
            <div class="d-flex row justify-content-center gap-3">

                <!-- Google Login -->
                <button type="button" class="btn btn-outline-danger col-md-4 d-flex align-items-center justify-content-center"
                    onclick="window.location.href='auth/login_google.php'">
                    <i class="bi bi-google me-2"></i> Đăng nhập với Google
                </button>

                <!-- Facebook Login
                <button type="button" class="btn btn-outline-primary col-md-4 d-flex align-items-center justify-content-center"
                    onclick="window.location.href='auth/login_facebook.php'">
                    <i class="bi bi-facebook me-2"></i> Đăng nhập với Facebook
                </button> -->
            </div>
        </form>
    </div>
    <?php if(!empty($message)):
        echo "<script>alert('" . addslashes($message) . "');</script>";
    endif; ?>
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="forgotPasswordLabel">Quên mật khẩu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <p>Nhập email của bạn. Chúng tôi sẽ gửi đặt lại mật khẩu.</p>

            <form method="POST" action="auth/forgot_password.php" id="forgotForm">
            <div class="mb-3">
                <label for="forgotEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="forgotEmail" name="email" required>
            </div>
            </form>

            <div id="forgotError" class="text-danger"></div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" form="forgotForm" class="btn btn-primary">Gửi email</button>
        </div>
        </div>
    </div>
    </div>


    <!-- js -->
    <script src="js/identity.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>