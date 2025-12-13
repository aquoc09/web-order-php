<div class="d-flex flex-column">

    <?php 
    $selected_id = $_GET['category_id'] ?? -1;
    ?>

    <!-- MỤC "TẤT CẢ" -->
    <h5 class="d-flex align-items-center">
        <a href="./menu.php?category_id=-1" 
           class="text-decoration-none fw-bold nav-item-text-title"
           style="<?= ($selected_id == -1) ? 'color:#dc552c' : '' ?>">
           Tất cả
        </a>
    </h5>
    <hr class="text-warning mt-0 mb-2" style="width: 50px; height: 2px; opacity: 1;">

    <?php
    // Các danh mục khác
    $sql = "SELECT * FROM category ORDER BY id ASC";
    $result = $conn->query($sql);

    if ($result):
        while ($cat = $result->fetch_assoc()):
            $activeClass = ($cat['id'] == $selected_id) ? 'active' : '';
    ?>
        <h5 class="d-flex align-items-center">
            <a href="./menu.php?category_id=<?= $cat['id'] ?>" 
               class="text-decoration-none fw-bold nav-item-text-title"
               style="<?= ($activeClass == 'active') ? 'color:#dc552c' : '' ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        </h5>
        <hr class="text-warning mt-0 mb-2" style="width: 50px; height: 2px; opacity: 1;">
    <?php 
        endwhile;
    endif;
    ?>

</div>
