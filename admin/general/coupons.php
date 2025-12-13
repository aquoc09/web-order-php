<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!";
    echo "<a href='../../index.php'>Trở về trang chủ</a>";
    exit;
}
?>

<div class="main-content">
  <div class="main-content-inner">
    <div class="add-product-wrapper">
      <div class="add-product">

        <form action="modules/saveCoupon.php" method="POST" enctype="multipart/form-data" class="frm-add-product">

          <div class="frm-product-left">

            <div class="frm-title"><h4>Thêm mã khuyến mãi</h4></div>

            <div class="frm-field">
              <label>Mã coupon:</label>
              <input type="text" name="code" required placeholder="VD: SALE10">
            </div>

            <div class="frm-group-flex">
              <div class="frm-field">
                <label>Số tiền giảm:</label>
                <input type="number" name="discountAmount" required step="0.01">
              </div>

              <div class="frm-field">
                <label>Điều kiện áp dụng (đơn tối thiểu):</label>
                <input type="number" name="conditionAmount" required step="0.01">
              </div>
            </div>

            <div class="frm-field">
              <label>Mô tả coupon:</label>
              <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="frm-group-flex mt-3">
              <label>
                <input type="checkbox" name="active" value="1"> Kích hoạt
              </label>
            </div>

            <div class="mt-3">
              <button type="reset" class="btn btn-danger">Huỷ</button>
              <button type="submit" class="btn btn-success">Lưu coupon</button>
            </div>

          </div>

          <div class="frm-product-right">
            <div class="frm-title"><h4>Hình ảnh khuyến mãi</h4></div>

            <div class="imageUpload">
              <div class="imageUpload-inner">

                <div class="imageUpload-icon">
                  <i class="bi bi-arrow-up-circle-fill"></i>
                </div>

                <button type="button" class="btn-upload"
                        onclick="document.getElementById('couponImage').click()">
                  Tải lên
                </button>

                <input type="file" id="couponImage" name="image" accept="image/*" hidden>

                <div class="imageUpload-description">
                  Click để tải hình ảnh khuyến mãi
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
document.getElementById('couponImage').addEventListener('change', function(e){
  const img = document.getElementById('previewImage');
  img.src = URL.createObjectURL(e.target.files[0]);
  img.style.display = 'block';
});
</script>
