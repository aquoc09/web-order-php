<?php
include_once __DIR__ . '/includes/header.php';

// Nếu người dùng chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!$currentUser) {
    header("Location: ./login-form.php");
    exit();
}

$errors = [];
$success = '';

// Xử lý khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'] ?? $currentUser['fullName'];
    $email = $_POST['email'] ?? $currentUser['email'];
    $phone = $_POST['phone'] ?? $currentUser['phone'];
    $address = $_POST['address'] ?? $currentUser['address'];
    $userId = $currentUser['id'];

    // Validate dữ liệu (có thể thêm các kiểm tra phức tạp hơn)
    if (empty($fullName)) {
        $errors[] = "Họ và tên không được để trống.";
    }
    if (empty($phone)) {
        $errors[] = "Số điện thoại không được để trống.";
    }

    if (empty($errors)) {

        $avatarFileName = $currentUser['userImage']; // Giữ lại ảnh cũ nếu không upload mới

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['avatar']['tmp_name'];
            $fileName = time() . '_' . basename($_FILES['avatar']['name']);
            $targetDir = __DIR__ . '/images/users/';
            $targetPath = $targetDir . $fileName;

            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Kiểm tra định dạng ảnh
            $validExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($ext, $validExt)) {
                $errors[] = "Chỉ cho phép file JPG, PNG, GIF, WEBP.";
            } else {
                // Upload OK → lưu file
                if (move_uploaded_file($fileTmp, $targetPath)) {

                    // XÓA ảnh cũ nếu có
                    if (!empty($currentUser['userImage'])) {
                        $oldPath = __DIR__ . '/images/users/' . $currentUser['userImage'];
                        if (file_exists($oldPath)) unlink($oldPath);
                    }
                    $avatarFileName = $fileName;
                } else {
                    $errors[] = "Không thể upload ảnh.";
                }
            }
        }

        // Cập nhật thông tin người dùng vào database
        $update_sql = "UPDATE user 
            SET fullName = ?, email = ?, phone = ?, address = ?, userImage = ?, updatedAt = NOW() 
            WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssi", $fullName, $email, $phone, $address, $avatarFileName, $userId);


        if ($update_stmt->execute()) {
            $success = "Cập nhật thông tin thành công!";
            // Lấy lại thông tin người dùng mới nhất
            $sql = "SELECT * FROM user WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentUser = $result->fetch_assoc();
            $stmt->close();
        } else {
            $errors[] = "Lỗi khi cập nhật thông tin: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
}
?>

<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Thông tin tài khoản</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form action="user-form.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Ảnh đại diện</label><br>

                            <?php if (!empty($currentUser['userImage'])): ?>
                                <img src="images/users/<?= $currentUser['userImage'] ?>" 
                                    alt="Avatar" 
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                            <?php else: ?>
                                <p class="text-muted">Chưa có ảnh</p>
                            <?php endif; ?>

                            <input type="file" name="avatar" class="form-control mt-2">
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($currentUser['username']) ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" value="<?= htmlspecialchars($currentUser['fullName'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($currentUser['address'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cập nhật thông tin</button>
                        <a href="change-password.php" class="btn btn-secondary w-100 mt-2">Đổi mật khẩu</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>