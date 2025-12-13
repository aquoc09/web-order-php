<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

$sql = "SELECT * FROM coupon ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="products-wrapper">

            <div class="d-flex justify-content-between mb-3">
                <h3>Coupon / Khuyến mãi</h3>
                <a href="index.php?mod=general&ac=coupons" class="btn btn-success text-light">
                    Thêm coupon
                </a>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình</th>
                        <th>Mã</th>
                        <th>Giảm</th>
                        <th>Điều kiện</th>
                        <th>Active</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if($row['image']): ?>
                                    <img src="../images/coupons/<?= $row['image'] ?>"
                                         style="width:50px;height:50px;object-fit:cover;">
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['code']) ?></td>
                            <td><?= number_format($row['discountAmount']) ?> đ</td>
                            <td><?= number_format($row['conditionAmount']) ?> đ</td>
                            <td><?= $row['active'] ? 'Có' : 'Không' ?></td>
                            <td>
                                <button class="btn btn-primary btn-edit"
                                        data-id="<?= $row['id'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    Sửa
                                </button>
                                <button class="btn btn-danger btn-delete"
                                        data-id="<?= $row['id'] ?>">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">Chưa có coupon</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- Modal -->
 <div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="editForm" method="POST" action="modules/updateCoupon.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Sửa Coupon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">

            <div class="mb-3">
                <label>Hình hiện tại</label><br>
                <img id="edit-image-preview"
                     style="width:80px;height:80px;object-fit:cover;border:1px solid #ccc;">
            </div>

            <div class="mb-3">
                <label>Đổi hình</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="mb-3">
                <label>Mã coupon</label>
                <input type="text" id="edit-code" name="code" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Số tiền giảm</label>
                <input type="number" id="edit-discount" name="discountAmount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Điều kiện áp dụng</label>
                <input type="number" id="edit-condition" name="conditionAmount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Mô tả</label>
                <textarea id="edit-description" name="description" class="form-control"></textarea>
            </div>

            <div class="form-check">
                <input type="checkbox" id="edit-active" name="active" value="1" class="form-check-input">
                <label class="form-check-label">Active</label>
            </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button class="btn btn-primary">Lưu</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        let id = this.dataset.id;

        fetch("modules/getCoupon.php?id=" + id)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit-id').value = data.id;
            document.getElementById('edit-code').value = data.code;
            document.getElementById('edit-discount').value = data.discountAmount;
            document.getElementById('edit-condition').value = data.conditionAmount;
            document.getElementById('edit-description').value = data.description;
            document.getElementById('edit-active').checked = data.active == 1;

            if (data.image) {
                document.getElementById('edit-image-preview').src =
                    "../images/coupons/" + data.image;
            }
        });
    });
});
</script>

<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        let id = this.dataset.id;

        if (!confirm("Bạn chắc chắn muốn xóa coupon này?")) return;

        fetch("modules/deleteCoupon.php?id=" + id)
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                document.querySelector(`tr[data-id='${id}']`).remove();
                alert("Đã xóa coupon!");
            } else {
                alert(data.message);
            }
        });
    });
});
</script>
