<?php
require_once __DIR__ . '/../../database/conf.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check for ID
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing coupon ID"]);
    exit;
}

$id = intval($_GET['id']);

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM coupon WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $coupon = $result->fetch_assoc();
    // Ensure numeric types are correctly cast
    $coupon['id'] = intval($coupon['id']);
    $coupon['discountAmount'] = floatval($coupon['discountAmount']);
    $coupon['conditionAmount'] = floatval($coupon['conditionAmount']);
    $coupon['active'] = intval($coupon['active']);
    echo json_encode($coupon);
} else {
    // Return a 404 Not Found status code
    http_response_code(404);
    echo json_encode(["error" => "Coupon not found"]);
}

$stmt->close();
$conn->close();
?>