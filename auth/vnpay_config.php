<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Chỉnh sửa ở đây.
// $vnp_TmnCode = "YOUR_TMN_CODE"; //Website ID in VNPAY System
// $vnp_HashSecret = "YOUR_SECRET_KEY"; //Secret key
// $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
// $vnp_Returnurl = "http://localhost/web-order-php/vnpay_return.php";
// $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";


$vnp_TmnCode = getenv("VNP_TMNCODE");
$vnp_HashSecret = getenv("VNP_HASHSECRET");
$vnp_Url = getenv("VNP_PAYMENT_URL");
$vnp_Returnurl = getenv("VNP_RETURN_URL");
$vnp_apiUrl = getenv("VNP_API_URL");

//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));

