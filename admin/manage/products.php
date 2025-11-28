<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

// Lấy danh sách sản phẩm
$sql = "SELECT p.id, p.name, p.productCode, p.price, p.active, p.productImage, c.name AS categoryName, c.categoryCode
        FROM product p
        LEFT JOIN category c ON p.category_id = c.id
        ORDER BY p.id ASC";
$result = $conn->query($sql);
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="products-wrapper">
            <div class="d-flex justify-content-between mb-3">
                <h3>Món ăn</h3>
                <button type="button" class="btn btn-success">
                    <a class="text-light text-btn" href="index.php?mod=general&ac=products">Thêm món</a>
                </button>
            </div>
            <div class="products">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên Món</th>
                            <th>Mã Món</th>
                            <th>Active</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>" 
                            data-name="<?= htmlspecialchars($row['name']) ?>"
                            data-productCode="<?= htmlspecialchars($row['productCode']) ?>"
                            data-active="<?= $row['active'] ?>">
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if(!empty($row['productImage'])): ?>
                                    <img src="../images/<?= $row['categoryCode'] ?>/<?= $row['productImage'] ?>" 
                                        alt="<?= htmlspecialchars($row['name']) ?>" 
                                        style="width:50px; height:50px; object-fit:cover;">
                                <?php else: ?>
                                    <span>Chưa có</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['productCode']) ?></td>
                            <td><?= $row['active'] ? "Có" : "Không" ?></td>
                            <td>
                                <button class="btn btn-primary btn-edit" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                                    Sửa
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Chưa có món ăn nào</td></tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
<?php
    $cateSql = "SELECT id, name, categoryCode FROM category ORDER BY name ASC";
    $cateResult = $conn->query($cateSql);
?>
<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="POST" action="modules/updateProduct.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Chỉnh sửa món ăn</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">
            <div class="mb-3">
                <label>Hình ảnh hiện tại</label><br>
                <img id="edit-image-preview" src="" style="width:80px; height:80px; object-fit:cover; border:1px solid #ccc;">
            </div>
            <div class="mb-3">
                <label>Đổi hình (nếu muốn)</label>
                <input type="file" name="productImage" class="form-control">
            </div>
            <div class="mb-3">
                <label>Tên món</label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mã món</label>
                <input type="text" name="productCode" id="edit-productCode" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Danh mục</label>
                <select id="edit-category" name="category_id" class="form-select" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php while($c = $cateResult->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" data-code="<?= $c['categoryCode'] ?>">
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Giá bán</label>
                <input type="number" name="price" id="edit-price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mô tả</label>
                <input type="text" name="description" id="edit-description" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="edit-active" name="active" value="1">
                <label class="form-check-label" for="edit-active">Active</label>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="edit-inStock" name="inStock" value="1">
                <label class="form-check-label" for="edit-active">InStock</label>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="edit-inPopular" name="inPopular" value="1">
                <label class="form-check-label" for="edit-active">InPupular</label>
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

        fetch("modules/getProduct.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Fill text fields
            document.getElementById('edit-name').value = data.name;
            document.getElementById('edit-productCode').value = data.productCode;
            document.getElementById('edit-price').value = data.price;
            document.getElementById('edit-description').value = data.description;

            // Checkbox
            document.getElementById('edit-active').checked = data.active == 1;
            document.getElementById('edit-inStock').checked = data.inStock == 1;
            document.getElementById('edit-inPopular').checked = data.inPopular == 1;

            // Category selectbox
            document.getElementById('edit-category').value = data.category_id;

            // Image preview
            let imgPath = "../images/" + data.categoryCode + "/" + data.productImage;
            document.getElementById('edit-image-preview').src = imgPath;
        })
        .catch(err => {
            alert("Lỗi tải dữ liệu!");
            console.log(err);
        });
    });
});
</script>


