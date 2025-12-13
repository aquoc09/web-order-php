<?php
include 'includes/header.php'; // header + navbar

// Get category from GET parameter
$category_id = !empty($_GET['category_id']) ? $_GET['category_id'] : null;
$category = null;

// Fetch the specific category if an ID is provided
if ($category_id) {
    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM category WHERE id = ?";
    $stmt_cat = $conn->prepare($sql);
    $stmt_cat->bind_param("s", $category_id);
    $stmt_cat->execute();
    $result = $stmt_cat->get_result();
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    }
    $stmt_cat->close();
}
?>
<style>
    .product-image {
        height: 400px;
        width: 100%;
        object-fit: cover;
    }
</style>
<!-- Breadcrumb -->
<div class="container py-2">
    <nav class="breadcrumb-nav small text-muted">
        <a href="index.php" class="text-decoration-none text-muted">Trang chủ</a> >
        <span class="text-dark"><?= ucfirst($category['name'] ?? 'Tất cả sản phẩm') ?></span>
    </nav>
</div>

<div class="container d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="product-grid py-4 w-100">
        <?php
        // Pagination settings
        $items_per_page = 6;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $items_per_page;

        // Base queries
        $count_sql = "SELECT COUNT(p.id) FROM product p";
        // JOIN with category to get categoryCode for image paths
        $select_sql = "SELECT p.*, c.categoryCode FROM product p JOIN category c ON p.category_id = c.id";

        // Append WHERE clause if a category is selected
        if ($category) {
            $count_sql .= " WHERE p.category_id = ?";
            $select_sql .= " WHERE p.category_id = ?";
        }

        // Get total number of products for pagination
        $stmt_total = $conn->prepare($count_sql);
        if ($category) {
            // Có chọn category -> lọc theo category
            $total_items_sql = "SELECT COUNT(*) FROM product WHERE category_id = ?";
            $stmt_total = $conn->prepare($total_items_sql);
            $stmt_total->bind_param("i", $category['id']);
        } else {
            // Không chọn category -> lấy tất cả
            $total_items_sql = "SELECT COUNT(*) FROM product";
            $stmt_total = $conn->prepare($total_items_sql);
        }
        $stmt_total->execute();
        $total_items_result = $stmt_total->get_result();
        $total_items = $total_items_result->fetch_row()[0];
        $total_pages = ceil($total_items / $items_per_page);
        $stmt_total->close();

        // Get products for the current page, ordered by latest
        $select_sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";
        $stmt_items = $conn->prepare($select_sql);
        if ($category) {
            $sql = "SELECT * FROM product WHERE category_id = ? LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $category['id'], $items_per_page, $offset);
        } else {
            // Không chọn danh mục -> lấy tất cả
            $sql = "SELECT * FROM product LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $items_per_page, $offset);
        }
        $stmt->execute();
        $items = $stmt->get_result();

        ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4">
        <?php if($items && $items->num_rows > 0): ?>
        <?php while($item = $items->fetch_assoc()): ?>
            <div class="col">
                <div class="text-center">
                    <a href="products-detail.php?id=<?= $item['id'] ?>" class="text-decoration-none text-dark">
                        <img src="images/<?= htmlspecialchars($item['categoryCode']) ?>/<?= htmlspecialchars($item['productImage']) ?>" class="img-fluid rounded product-image" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="fw-medium mt-2"><?= htmlspecialchars($item['name']) ?></div>
                    </a>
                    <div class="text-muted"><?= number_format($item['price'],0,',','.') ?> ₫</div>
                    <button class="btn btn-primary btn-sm mt-2 add-to-cart" 
                            data-id="<?= $item['id'] ?>" 
                            data-name="<?= htmlspecialchars($item['name']) ?>" 
                            data-price="<?= $item['price'] ?>"
                            data-image="images/<?= htmlspecialchars($item['categoryCode']) ?>/<?= htmlspecialchars($item['productImage']) ?>">
                        Thêm vào giỏ
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Không tìm thấy sản phẩm nào.</p>
        <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?category_id=<?= $category_id ?>&page=<?= $page - 1 ?>">Previous</a></li>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if($i == $page) echo 'active'; ?>"><a class="page-link" href="?category_id=<?= $category_id ?>&page=<?= $i ?>"><?= $i ?></a></li>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?category_id=<?= $category_id ?>&page=<?= $page + 1 ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script src="js/cart.js"></script>
<?php include 'includes/footer.php'; ?>
