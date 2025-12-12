<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}

global $conn;

// Fetch list of coupons
$sql = "SELECT * FROM coupon ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="coupons-wrapper">
            <div class="d-flex justify-content-between mb-3">
                <h3>Coupons</h3>
                <button type="button" class="btn btn-success">
                    <a class="text-light text-btn" href="index.php?mod=general&ac=coupons">Add Coupon</a>
                </button>
            </div>
            <div class="coupons">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Discount Amount</th>
                            <th>Condition Amount</th>
                            <th>Active</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['code']) ?></td>
                            <td><?= number_format($row['discountAmount'], 0, ',', '.') ?> %</td>
                            <td><?= number_format($row['conditionAmount'], 0, ',', '.') ?> ₫</td>
                            <td><?= $row['active'] ? "Yes" : "No" ?></td>
                            <td>
                                <button class="btn btn-primary btn-edit" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No coupons found</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Edit Coupon Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="POST" action="modules/updateCoupon.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Chỉnh sửa Coupon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">
            <div class="mb-3">
                <label>Code</label>
                <input type="text" name="code" id="edit-code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Discount Amount</label>
                <input type="number" name="discountAmount" id="edit-discountAmount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Condition Amount</label>
                <input type="number" name="conditionAmount" id="edit-conditionAmount" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="edit-active" name="active" value="1">
                <label class="form-check-label" for="edit-active">Active</label>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
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

        // Fetch coupon data
        fetch(`modules/getCoupon.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            document.getElementById('edit-code').value = data.code;
            document.getElementById('edit-discountAmount').value = data.discountAmount;
            document.getElementById('edit-conditionAmount').value = data.conditionAmount;
            document.getElementById('edit-active').checked = data.active == 1;
        })
        .catch(err => {
            alert("Error loading coupon data!");
            console.log(err);
        });
    });
});
</script>
