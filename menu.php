<?php
include 'includes/header.php'; // header + navbar

$category_id = $_GET['category_id'] ?? -1; // default
$category = null;
$sql = "SELECT * FROM category";
$result_categories = $conn->query($sql);
if($result_categories){
    while($cat = $result_categories->fetch_assoc()){
        if($cat['id']==$category_id){
            $category = $cat;
            break;
        }
    }
}


?>

<!-- Breadcrumb -->
<div class="container py-2">
    <nav class="breadcrumb-nav small text-muted">
        <a href="index.php" class="text-decoration-none text-muted">Trang chủ</a> >
        <span class="text-dark"><?= ucfirst($category['name']?? '') ?></span>
    </nav>
</div>

<div class="container d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="product-grid py-4 w-100">
        <?php
        $sql = "SELECT * FROM product WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category['id']);
        $stmt->execute();
        $items = $stmt->get_result();
        ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4">
        <?php while($item = $items->fetch_assoc()): ?>
            <div class="col">
                <div class="text-center">
                    <a href="products-detail.php?id=<?= $item['id'] ?>" class="text-decoration-none text-dark">
                        <img src="img/food/menu/<?= $category['categoryCode'] ?>/<?= $item['productImage'] ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="fw-medium mt-2"><?= htmlspecialchars($item['name']) ?></div>
                    </a>
                    <div class="text-muted"><?= number_format($item['price'],0,',','.') ?> ₫</div>
                    <button class="btn btn-primary btn-sm mt-2 add-to-cart" 
                            data-id="<?= $item['id'] ?>" 
                            data-name="<?= htmlspecialchars($item['name']) ?>" 
                            data-price="<?= $item['price'] ?>"
                            data-image="img/food/menu/<?= $category['categoryCode'] ?>/<?= $item['productImage'] ?>">
                        Thêm vào giỏ
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="js/cart.js"></script>
<?php include 'includes/footer.php'; ?>
