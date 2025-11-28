<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../../index.php'>Trở về trang chủ</a>";
    exit;
}
?>

<div class="main-content">
  <div class="main-content-inner">
    <div class="add-product-wrapper">
      <div class="add-product">

        <form action="modules/saveCategory.php" method="POST" enctype="multipart/form-data" class="frm-add-product">
            
            <div class="frm-product-left">

                <div class="frm-title"><h4>Thêm loại món</h4></div>

                <div class="frm-field">
                <label for="name">Tên loại:</label>
                <input type="text" name="name" required placeholder="Tên loại">
                </div>

                <div class="frm-field">
                <label for="productCode">Mã loại:</label>
                <input type="text" name="categoryCode" required placeholder="Nhập mã loại">
                </div>

                <div class="frm-group-flex mt-3">
                    <label><input type="checkbox" name="active" value="1"> Hiển thị (active)</label>
                </div>

                <div class="mt-3">
                    <button type="reset" class="btn btn-danger">Huỷ</button>
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>