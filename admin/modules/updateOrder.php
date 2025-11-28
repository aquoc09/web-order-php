<?php

require_once __DIR__ . '/../../database/conf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        die("Order ID is required!");
    }

    // Các field có thể chỉnh sửa
    $status = $_POST['status'] ?? '';
    $address = $_POST['address'] ?? '';
    $note = $_POST['note'] ?? '';
    $orderDate = $_POST['orderDate'] ?? '';
    $payment = $_POST['payment'] ?? '';
    $totalMoney = $_POST['totalMoney'] ?? '';

        // Admin được quyền update tất cả
        $sql = "UPDATE `order`
                SET 
                    status = ?, 
                    address = ?, 
                    note = ?, 
                    orderDate = ?, 
                    paymentMethod = ?, 
                    totalMoney = ?, 
                    updateAt = NOW()
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $status,
            $address,
            $note,
            $orderDate,
            $payment,
            $totalMoney,
            $id
        );

    if ($stmt->execute()) {
        header("Location: ../index.php?mod=manage&ac=orders&msg=success");
        exit;
    } else {
        echo "Update failed: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
