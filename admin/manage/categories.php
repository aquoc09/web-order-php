<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

// Lấy danh sách phân loại
$sql = "SELECT c.*
        FROM category c
        ORDER BY c.id ASC";
$result = $conn->query($sql);
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="products-wrapper">
            <div class="d-flex justify-content-between mb-3">
                <h3>Danh mục</h3>
                <button type="button" class="btn btn-success">
                    <a class="text-light text-btn" href="index.php?mod=general&ac=categories">Thêm loại</a>
                </button>
            </div>
            <div class="products">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên loại</th>
                            <th>Mã loại</th>
                            <th>Active</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>" 
                            data-name="<?= htmlspecialchars($row['name']) ?>"
                            data-categoryCode="<?= htmlspecialchars($row['categoryCode']) ?>"
                            data-active="<?= $row['active'] ?>">
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['categoryCode']) ?></td>
                            <td><?= $row['active'] ? "Có" : "Không" ?></td>
                            <td>
                                <button class="btn btn-primary btn-edit" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                                    Sửa
                                </button>
                                <button class="btn btn-danger btn-delete" data-id="<?= $row['id'] ?>">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Chưa có phân loại món nào</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="POST" action="modules/saveCategory.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Chỉnh sửa danh mục</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">
            <div class="mb-3">
                <label>Tên loại</label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mã loại</label>
                <input type="text" name="categoryCode" id="edit-categoryCode" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="edit-active" name="active" value="1">
                <label class="form-check-label" for="edit-active">Active</label>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {

        let id = this.dataset.id;
        document.getElementById('edit-id').value = id;

        fetch("modules/getCategory.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Fill text fields
            document.getElementById('edit-name').value = data.name;
            document.getElementById('edit-categoryCode').value = data.categoryCode;

            // Checkbox
            document.getElementById('edit-active').checked = data.active == 1;
        })
        .catch(err => {
            alert("Lỗi tải dữ liệu!");
            console.log(err);
        });
    });
});
</script>
<script>
// --- XÓA LOẠI ---
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {

        let id = this.dataset.id;

        if (!confirm("Bạn có chắc muốn xóa loại này? Hành động này không thể hoàn tác!")) {
            return;
        }

        // Gửi request xóa bằng fetch
        fetch("modules/deleteCategory.php?id=" + id, {
            method: "GET"
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Đã xóa loại thành công!");

                // Xóa dòng khỏi bảng mà không cần reload trang
                let row = document.querySelector(`tr[data-id='${id}']`);
                if (row) row.remove();

            } else {
                alert("Xóa thất bại: " + data.message);
            }
        })
        .catch(err => {
            console.log(err);
            alert("Lỗi kết nối khi xóa!");
        });
    });
});
</script>


