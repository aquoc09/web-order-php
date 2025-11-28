<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn, $currentUser;

// Kiểm tra quyền admin
if (!$currentUser || $currentUser['role'] !== 'admin') {
    echo "<div class='main-content'>";
    echo "<div class='main-content-inner'>";
    echo "<div class='alert alert-danger m-3'>Bạn không có quyền thêm user!</div>";
    echo "<a href='index.php?mod=home' class='btn btn-secondary m-3'>Quay lại</a>";
    echo "</div class='main-content'>";
    echo "</div class='main-content-inner'>";
    exit;
}
?>

<div class="main-content">
  <div class="main-content-inner">
    <div class="add-product-wrapper">
      <div class="add-product">

        <form action="modules/saveUser.php" method="POST" enctype="multipart/form-data" class="frm-add-product">
            
            <!-- LEFT -->
            <div class="frm-product-left">

                <div class="frm-title"><h4>Thêm user mới</h4></div>

                <div class="frm-field">
                    <label for="fullName">Họ và tên:</label>
                    <input type="text" name="fullName" required placeholder="Nhập họ tên">
                </div>

                <div class="frm-field">
                    <label for="username">Username:</label>
                    <input type="text" name="username" required placeholder="Nhập username">
                </div>

                <div class="frm-group-flex">
                    <div class="frm-field">
                        <label>Email:</label>
                        <input type="email" name="email" required placeholder="Email">
                    </div>

                    <div class="frm-field">
                        <label>Số điện thoại:</label>
                        <input type="text" name="phone" placeholder="Số điện thoại">
                    </div>
                </div>

                <div class="frm-field">
                    <label>Địa chỉ:</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>

                <div class="frm-group-flex">
                    <div class="frm-field">
                        <label>Role:</label>
                        <select name="role" class="form-select p-2" required>
                            <option value="user">User</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="frm-field">
                        <label>Mật khẩu:</label>
                        <input type="password" name="password" required placeholder="Nhập mật khẩu">
                    </div>
                </div>

                <div class="mt-2">
                    <label><input type="checkbox" name="active" value="1" checked> Active</label>
                </div>

                <div class="mt-3">
                    <button type="reset" class="btn btn-danger">Huỷ</button>
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>

            </div>

            <!-- RIGHT -->
            <div class="frm-product-right">
                <div class="frm-title"><h4>Avatar</h4></div>
                <div class="imageUpload">
                    <div class="imageUpload-inner">

                        <div class="imageUpload-icon">
                            <i class="bi bi-arrow-up-circle-fill"></i>
                        </div>

                        <button type="button" class="btn-upload" onclick="document.getElementById('file').click()">Tải lên</button>

                        <input type="file" id="file" name="userImage" accept="image/*" hidden>

                        <div class="imageUpload-description">
                            Click để tải hình ảnh lên
                        </div>

                        <div class="pic-uploaded">
                            <img id="previewImage" src="" style="max-width:100%; display:none;">
                        </div>

                    </div>
                </div>
            </div>

        </form>

      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('file').addEventListener('change', function(e){
  const img = document.getElementById('previewImage');
  img.src = URL.createObjectURL(e.target.files[0]);
  img.style.display = 'block';
});
</script>
