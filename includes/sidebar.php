<div class="d-flex flex-column">
<?php
$selected_id = $_GET['category_id'] ?? -1;
$sql = "SELECT * FROM category";
$result = $conn->query($sql);
if($result):
while($cat = $result->fetch_assoc()):
    $activeClass = ($cat['id'] == $selected_id) ? 'active' : '';
?>
<h5 class="d-flex align-items-center">
    <a href="./menu.php?category_id=<?=$cat['id']?>" class="text-decoration-none fw-bold nav-item-text-title" 
        style="<?= ($activeClass=='active')?'color:#dc552c':'' ?>">
        <?= htmlspecialchars($cat['name']) ?>
    </a>
</h5>
<hr class="text-warning mt-0 mb-2" style="width: 50px; height: 2px; opacity: 1;">
<?php 
    endwhile; 
endif;
?>
</div>
