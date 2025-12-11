<?php
    $errorUsername = $_GET['errorUsername']??'';
    $errorPassword = $_GET['errorPassword']??'';
    $errorConfirmPassword = $_GET['errorConfirmPassword']??'';
    $errorName = $_GET['errorName']??'';
    $errorPhone = $_GET['errorPhone']??'';
    $errorEmail = $_GET['errorEmail']??'';
?>


<!-- Header -->
<?php include 'includes/header.php';?>
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