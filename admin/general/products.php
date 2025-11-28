<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

$sql = "SELECT id, name, categoryCode FROM category WHERE active = 1";
$rs  = $conn->query($sql);
?>

<div class="main-content">
  <div class="main-content-inner">
    <div class="add-product-wrapper">
      <div class="add-product">

        <form action="modules/saveProduct.php" method="POST" enctype="multipart/form-data" class="frm-add-product">
            
            <div class="frm-product-left">

                <div class="frm-title"><h4>Thêm món ăn / combo</h4></div>

                <div class="frm-field">
                <label for="name">Tên món:</label>
                <input type="text" name="name" required placeholder="Tên sản phẩm">
                </div>

                <div class="frm-field">
                <label for="productCode">Mã món:</label>
                <input type="text" name="productCode" required placeholder="Nhập mã món">
                </div>

                <div class="frm-group-flex">
                    <div class="frm-field">
                        <label for="price">Giá bán:</label>
                        <input type="number" name="price" required placeholder="Giá bán">
                    </div>

                    <div class="frm-field">
                        <label>Danh mục món ăn:</label>
                        <select name="category_id" class="form-select p-2" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php while($row = $rs->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" data-code="<?= $row['categoryCode'] ?>">
                            <?= $row['name'] ?>
                            </option>
                        <?php endwhile; ?>
                        </select>
                    </div>

                </div>

                <div class="frm-field">
                    <label>Mô tả món ăn:</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="frm-group-flex mt-3">
                    <label><input type="checkbox" name="active" value="1"> Hiển thị (active)</label>
                    <label><input type="checkbox" name="inStock" value="1"> Còn hàng</label>
                    <label><input type="checkbox" name="inPopular" value="1"> Phổ biến</label>
                </div>

                <div class="mt-3">
                    <button type="reset" class="btn btn-danger">Huỷ</button>
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>
            </div>

            <div class="frm-product-right">
                <div class="frm-title"><h4>Hình ảnh</h4></div>
                <div class="imageUpload">
                    <div class="imageUpload-inner">

                        <div class="imageUpload-icon">
                        <i class="bi bi-arrow-up-circle-fill"></i>
                        </div>

                        <button type="button" class="btn-upload" onclick="document.getElementById('file').click()">Tải lên</button>

                        <input type="file" id="file" name="productImage" accept="image/*" hidden>

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
