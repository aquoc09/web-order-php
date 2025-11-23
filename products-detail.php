<?php
include 'includes/header.php'; // header + navbar

// Get product ID from URL
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo "<div class='container'><h2>Product not found.</h2></div>";
    include 'includes/footer.php';
    exit;
}

// Fetch product details from the database
$sql = "
    SELECT p.*, c.categoryCode 
    FROM product p 
    JOIN category c ON p.category_id = c.id 
    WHERE p.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<div class='container'><h2>Product not found.</h2></div>";
    include 'includes/footer.php';
    exit;
}
?>

<!-- Breadcrumb -->
<div class="container py-2">
    <nav class="breadcrumb-nav small text-muted">
        <a href="index.php" class="text-decoration-none text-muted">Trang chủ</a> >
        <a href="menu.php?category_id=<?= $product['category_id'] ?>" class="text-decoration-none text-muted"><?= ucfirst($product['categoryCode']) ?></a> >
        <span class="text-dark"><?= htmlspecialchars($product['name']) ?></span>
    </nav>
</div>

<div class="container py-4">
    <div class="row">
        <div class="col-md-6">
            <img src="img/food/menu/<?= $product['categoryCode'] ?>/<?= $product['productImage'] ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p class="text-muted"><?= htmlspecialchars($product['description']) ?></p>
            <p class="fs-4 fw-bold"><?= number_format($product['price'], 0, ',', '.') ?> ₫</p>
            
            <div id="add-to-cart-message"></div>
            <form id="add-to-cart-form">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="row">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Số lượng</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>
</div>

<script src="js/product-detail.js"></script>

<?php include 'includes/footer.php'; ?>
