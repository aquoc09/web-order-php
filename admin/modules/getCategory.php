<?php
require_once __DIR__ . '/../../database/conf.php';

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing ID"]);
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT c.*
        FROM category c
        WHERE c.id = $id
        LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Product not found"]);
}
?>
