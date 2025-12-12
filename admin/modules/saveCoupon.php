<?php
require_once __DIR__ . '/../../database/conf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Method Not Allowed";
    exit;
}

$code = $_POST['code'] ?? '';
$discountAmount = $_POST['discountAmount'] ?? null;
$conditionAmount = $_POST['conditionAmount'] ?? null;
$active = isset($_POST['active']) ? 1 : 0;

// Basic validation
if (empty($code) || !is_numeric($discountAmount) || !is_numeric($conditionAmount) || $discountAmount < 0 || $conditionAmount < 0) {
    // Redirect with an error message
    header("Location: ../index.php?mod=general&ac=coupons&msg=error_invalid_input");
    exit();
}

$sql = "INSERT INTO coupon(code, discountAmount, conditionAmount, active) VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Handle prepare error
    error_log("Prepare failed: " . $conn->error);
    header("Location: ../index.php?mod=general&ac=coupons&msg=error_db");
    exit();
}

$stmt->bind_param("sddi", $code, $discountAmount, $conditionAmount, $active);

if($stmt->execute()){
    header("Location: ../index.php?mod=manage&ac=coupons&msg=add_success");
    exit();
} else {
    // Handle execute error
    error_log("Execute failed: " . $stmt->error);
    header("Location: ../index.php?mod=general&ac=coupons&msg=error_db");
    exit();
}

$stmt->close();
$conn->close();

?>
