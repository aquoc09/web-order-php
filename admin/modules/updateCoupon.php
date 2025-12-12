<?php

require_once __DIR__ . '/../../database/conf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        die("Coupon ID is required!");
    }

    // Lấy dữ liệu từ form
    $code = $_POST['code'] ?? '';
    $discountAmount = $_POST['discountAmount'] ?? '';
    $conditionAmount = $_POST['conditionAmount'] ?? '';
    $active = isset($_POST['active']) ? 1 : 0;

    // Admin được quyền update tất cả
    $sql = "UPDATE `coupon`
            SET 
                code = ?, 
                discountAmount = ?, 
                conditionAmount = ?, 
                active = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sddii",
        $code,
        $discountAmount,
        $conditionAmount,
        $active,
        $id
    );

    if ($stmt->execute()) {
        header("Location: ../index.php?mod=manage&ac=coupons&msg=updated");
        exit;
    } else {
        echo "Update failed: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
