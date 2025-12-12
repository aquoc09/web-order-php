<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../index.php'>Trở về trang chủ</a>";
    exit;
}
    function loadModule(){
        $mod = isset($_GET['mod'])? $_GET['mod'] : "";
        $ac = isset($_GET['ac'])? $_GET['ac'] : "";
        $validMod = ['general', 'manage'];
        $validAc = ['dashboard','products', 'categories', 'orders', 'users', 'coupons'];
        if(!in_array($mod, $validMod)){
            include 'home.php';
            return;
        }
        switch ($mod){
            case 'general':
                if(!in_array($ac, $validAc)){
                    include 'home.php';
                    return;
                }
                if($ac == 'dashboard') include 'general/dashboard.php';
                if($ac == 'categories') include 'general/categories.php';
                if($ac == 'products') include 'general/products.php';
                if($ac == 'users') include 'general/users.php';
                if($ac == 'coupons') include 'general/coupons.php';
                break;
            case 'manage':
                if(!in_array($ac, $validAc)){
                    include 'home.php';
                    return;
                }
                if($ac == 'products') include 'manage/products.php';
                if($ac == 'categories') include 'manage/categories.php';
                if($ac == 'orders') include 'manage/orders.php';
                if($ac == 'users') include 'manage/users.php';
                if($ac == 'coupons') include 'manage/coupons.php';
                break;
            default:
                include 'home.php';
                break;
        }
    }

?>