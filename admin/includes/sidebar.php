<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../../index.php'>Trở về trang chủ</a>";
    exit;
}

$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
$ac  = isset($_GET['ac']) ? $_GET['ac'] : '';
?>

<div class="admin-navigation">
    <div class="admin-nav-container">
        <div class="admin-nav">

            <!-- CHUNG -->
            <ul class="nav-list list-unstyled">
                <li class="root-nav-item">
                    <div class="nav-item-title">CHUNG</div>
                    <ul class="item-group list-unstyled">
                        <li class="nav-item <?= ($mod=='general' && $ac=='coupons') ? 'active' : '' ?>">
                            <a href="index.php?mod=general&ac=coupons">
                                <i class="bi bi-plus-square"></i>
                                Thêm khuyến mãi
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='general' && $ac=='products') ? 'active' : '' ?>">
                            <a href="index.php?mod=general&ac=products">
                                <i class="bi bi-plus-square"></i>
                                Thêm món ăn
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='general' && $ac=='categories') ? 'active' : '' ?>">
                            <a href="index.php?mod=general&ac=categories">
                                <i class="bi bi-plus-square"></i>
                                Thêm danh mục
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='general' && $ac=='users') ? 'active' : '' ?>">
                            <a href="index.php?mod=general&ac=users">
                                <i class="bi bi-plus-square"></i>
                                Thêm user
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>

            <!-- QUẢN LÍ -->
            <ul class="nav-list list-unstyled">
                <li class="root-nav-item">
                    <div class="nav-item-title">QUẢN LÍ</div>
                    <ul class="item-group list-unstyled">

                        <li class="nav-item <?= ($mod=='manage' && $ac=='revenues') ? 'active' : '' ?>">
                            <a href="index.php?mod=manage&ac=revenues">
                                <i class="bi bi-speedometer2"></i>
                                Doanh thu/Revenues
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='manage' && $ac=='products') ? 'active' : '' ?>">
                            <a href="index.php?mod=manage&ac=products">
                                <i class="bi bi-bag-plus-fill"></i>
                                Món ăn/Products
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='manage' && $ac=='categories') ? 'active' : '' ?>">
                            <a href="index.php?mod=manage&ac=categories">
                                <i class="bi bi-link-45deg"></i>
                                Danh mục/Categories
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='manage' && $ac=='orders') ? 'active' : '' ?>">
                            <a href="index.php?mod=manage&ac=orders">
                                <i class="bi bi-box"></i>
                                Đơn hàng/Orders
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='manage' && $ac=='users') ? 'active' : '' ?>">
                            <a href="index.php?mod=manage&ac=users">
                                <i class="bi bi-people"></i>
                                Người dùng/Users
                            </a>
                        </li>

                        <li class="nav-item <?= ($mod=='manage' && $ac=='coupons') ? 'active' : '' ?>">
                            <a href="index.php?mod=manage&ac=coupons">
                                <i class="bi bi-tag"></i>
                                Khuyến mãi/Coupons
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>

        </div>
    </div>
</div>
