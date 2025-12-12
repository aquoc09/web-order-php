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

        <form action="modules/saveCoupon.php" method="POST" class="frm-add-product">
            
            <div class="frm-product-left">

                <div class="frm-title"><h4>Thêm ưu đãi</h4></div>

                <div class="frm-field">
                    <label for="code">Mã ưu đãi:</label>
                    <input type="text" name="code" required placeholder="Nhập mã ưu đãi">
                </div>

                <div class="frm-field">
                    <label for="discountAmount">Số tiền giảm:</label>
                    <input type="number" name="discountAmount" required placeholder="Nhập phần trăm giảm số tiền giảm" step="0.01">
                </div>

                <div class="frm-field">
                    <label for="conditionAmount">Điều kiện (tối thiểu):</label>
                    <input type="number" name="conditionAmount" required placeholder="Số tiền tối thiểu để áp dụng" step="0.01">
                </div>

                <div class="frm-group-flex mt-3">
                    <label><input type="checkbox" name="active" value="1" checked> Hiển thị (active)</label>
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
