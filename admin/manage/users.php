<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

// Lấy danh sách user
$sql = "SELECT id, userImage, username, fullName, email, phone, role, active, address, googleAccountId, facebookAccountId, createdAt, updatedAt 
        FROM user
        ORDER BY id ASC";
$result = $conn->query($sql);

global $currentUser;
// Lấy thông tin user đang login
$currentUserRole = $currentUser['role']; // admin / manager
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="products-wrapper">
            <div class="d-flex justify-content-between mb-3">
                <h3>Danh sách user</h3>

                <?php if ($currentUserRole === 'admin'): ?>
                <button type="button" class="btn btn-success">
                    <a class="text-light text-btn" href="index.php?mod=general&ac=users">Thêm user</a>
                </button>
                <?php endif; ?>
            </div>

            <div class="products">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Login google</th>
                            <th>Login facebook</th>
                            <th>Active</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>"
                            data-username="<?= $row['username'] !== null ? htmlspecialchars($row['username']) : '' ?>"
                            data-fullname="<?= $row['fullName'] !== null ? htmlspecialchars($row['fullName']) : '' ?>"
                            data-email="<?= $row['email'] !== null ? htmlspecialchars($row['email']) : '' ?>"
                            data-phone="<?= $row['phone'] !== null ? htmlspecialchars($row['phone']) : '' ?>"
                            data-role="<?= $row['role'] !== null ? htmlspecialchars($row['role']) : ''  ?>"
                            data-address="<?= $row['address'] !== null ? htmlspecialchars($row['address']) : ''  ?>"
                            data-facebokAccounId="<?= $row['facebookAccountId'] ?>"
                            data-googleAccounId="<?= $row['googleAccountId'] ?>"
                            data-active="<?= $row['active'] ?>"
                            data-image="<?= $row['userImage'] ?>"
                            data-created="<?= $row['createdAt'] ?>"
                            data-updated="<?= $row['updatedAt'] ?>"
                        >

                            <td><?= $row['id'] ?></td>

                            <td>
                                <?php if(!empty($row['userImage'])): ?>
                                    <img src="../images/users/<?= $row['userImage'] ?>" 
                                        alt="<?= htmlspecialchars($row['fullName']) ?>" 
                                        style="width:50px; height:50px; object-fit:cover;">
                                <?php else: ?>
                                    <span>Chưa có</span>
                                <?php endif; ?>
                            </td>

                            <td><?= $row['fullName'] !== null ? htmlspecialchars($row['fullName']) : '' ?></td>
                            <td><?= $row['email'] !== null ? htmlspecialchars($row['email']) : '' ?></td>
                            <td><?= $row['phone'] !== null ? htmlspecialchars($row['phone']) : '' ?></td>
                            <td><?= $row['role'] !== null ? htmlspecialchars($row['role']) : ''  ?></td>
                            <td><?= $row['googleAccountId'] ? "Có" : "Không" ?></td>
                            <td><?= $row['facebookAccountId'] ? "Có" : "Không" ?></td>
                            <td><?= $row['active'] ? "Có" : "Không" ?></td>

                            <td>
                                <button 
                                    class="btn btn-primary btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                >
                                    Xem / Sửa
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr><td colspan="9" class="text-center">Chưa có user nào</td></tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Edit User -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="POST" action="modules/updateUser.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thông tin người dùng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

            <!-- ID -->
            <input type="hidden" name="id" id="edit-id">

            <!-- Avatar -->
            <div class="mb-3">
                <label>Avatar</label><br>
                <img id="edit-image-preview" src="" style="width:80px; height:80px; object-fit:cover; border:1px solid #ccc;">
            </div>

            <?php if ($currentUserRole === 'admin'): ?>
            <div class="mb-3">
                <label>Chọn ảnh mới</label>
                <input type="file" name="userImage" class="form-control">
            </div>
            <?php endif; ?>

            <!-- Full name -->
            <div class="mb-3">
                <label>Họ tên</label>
                <input type="text" name="fullName" id="edit-fullName" class="form-control">
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" id="edit-username" class="form-control">
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" id="edit-email" class="form-control">
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" id="edit-phone" class="form-control">
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label>Địa chỉ</label>
                <input type="text" name="address" id="edit-address" class="form-control">
            </div>

            <!-- Role -->
            <div class="mb-3">
                <label>Role</label>
                <select name="role" id="edit-role" class="form-select">
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="user">User</option>
                </select>
            </div>

            <!-- Active -->
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="edit-active" name="active" value="1">
                <label class="form-check-label">Active</label>
            </div>

            <!-- Created / Updated -->
            <div class="mb-2"><strong>Ngày tạo:</strong> <span id="edit-created"></span></div>
            <div class="mb-2"><strong>Cập nhật:</strong> <span id="edit-updated"></span></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>

          <?php if ($currentUserRole === 'admin'): ?>
          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {

        let tr = this.closest("tr");

        // Set values
        document.getElementById("edit-id").value = tr.dataset.id;
        document.getElementById("edit-fullName").value = tr.dataset.fullname;
        document.getElementById("edit-username").value = tr.dataset.username;
        document.getElementById("edit-email").value = tr.dataset.email;
        document.getElementById("edit-phone").value = tr.dataset.phone;
        document.getElementById("edit-address").value = tr.dataset.address;
        document.getElementById("edit-role").value = tr.dataset.role;

        document.getElementById("edit-active").checked = tr.dataset.active == "1";

        document.getElementById("edit-created").textContent = tr.dataset.created;
        document.getElementById("edit-updated").textContent = tr.dataset.updated;

        // Avatar
        document.getElementById("edit-image-preview").src = 
            "../images/users/" + tr.dataset.image;

        // Nếu ROLE là manager → chỉ xem
        let isManager = "<?= $currentUserRole ?>" === "manager";

        document.querySelectorAll("#editForm input, #editForm select")
            .forEach(el => {
                if (el.name !== "id") {
                    el.disabled = isManager;
                }
            });

        document.getElementById("edit-username").disabled  = true;
    });
});
</script>