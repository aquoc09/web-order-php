<?php
session_start();
include_once 'database/conf.php';
//include_once 'includes/utils.php';
require_once 'auth/vnpay_config.php';

// Check if order_id is in session
if (!isset($_SESSION['order_id_for_vnpay'])) {
    // Redirect or show error if order_id is not set
    header('Location: checkout.php?error=missing_order');
    die();
}
$order_id = $_SESSION['order_id_for_vnpay'];

// Fetch order total from DB to ensure data integrity
$sql = "SELECT totalMoney FROM `order` WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die('Order not found.');
}
$order_data = $result->fetch_assoc();
$total_price = $order_data['totalMoney'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

$vnp_TxnRef = $order_id; //Mã đơn hàng. Lấy từ DB sau khi đã tạo.
$vnp_OrderInfo = 'Thanh toán đơn hàng';
$vnp_OrderType = 'billpayment';
$vnp_Amount = $total_price * 100;
$vnp_Locale = 'vn';
$vnp_BankCode = 'NCB';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
);

if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}
if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
    $inputData['vnp_Bill_State'] = $vnp_Bill_State;
}

//var_dump($inputData);
ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

// Unset the session variable after using it
unset($_SESSION['order_id_for_vnpay']);

header('Location: ' . $vnp_Url);
die();
