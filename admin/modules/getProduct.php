<?php
require_once __DIR__ . '/../../database/conf.php';

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing ID"]);
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT p.*, c.categoryCode 
        FROM product p
        LEFT JOIN category c ON p.category_id = c.id
        WHERE p.id = $id
        LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Product not found"]);
}
?>
