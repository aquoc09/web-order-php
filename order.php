<?php
include_once './includes/header.php';

// Check if user is logged in
if (!isset($currentUser)) {
    // Nếu không có thông tin user, chuyển hướng về login
    header('Location: login-form.php');
    exit();
}

$user_id = $currentUser['id'];

?>

<div class="container my-5">
    <h1 class="text-center mb-4">Lịch sử đặt hàng</h1>

    <?php
    // Check if an order_id is provided for detail view
    if (isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];

        // Fetch order details
        $order_sql = "SELECT * FROM `order` WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $order_result = $stmt->get_result();

        if ($order_result->num_rows > 0) {
            $order = $order_result->fetch_assoc();

            // Fetch order items
            $order_detail_sql = "
                SELECT od.*, p.name as product_name, p.price as product_price
                FROM order_detail od
                JOIN product p ON od.product_id = p.id
                WHERE od.order_id = ?";
            $stmt_detail = $conn->prepare($order_detail_sql);
            $stmt_detail->bind_param("i", $order_id);
            $stmt_detail->execute();
            $order_detail_result = $stmt_detail->get_result();
            ?>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Chi tiết đơn hàng #<?php echo $order['id']; ?></h4>
                    <a href="order.php" class="btn btn-secondary btn-sm float-end">Quay lại danh sách</a>
                </div>
                <div class="card-body">
                    <p><strong>Ngày đặt:</strong> <?php echo date("d/m/Y H:i:s", strtotime($order['orderDate'])); ?></p>
                    <p><strong>Trạng thái:</strong> <?php echo $order['status']; ?></p>
                    <p><strong>Địa chỉ giao hàng:</strong> <?php echo $order['address']; ?></p>
                    <p><strong>Ghi chú:</strong> <?php echo $order['note']; ?></p>
                    <p><strong>Tổng tiền:</strong> <?php echo number_format($order['totalMoney'], 0, ',', '.'); ?> VNĐ</p>

                    <h5 class="mt-4">Các sản phẩm đã đặt:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($order_detail_result->num_rows > 0) {
                                while ($item = $order_detail_result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $item['product_name']; ?></td>
                                        <td><?php echo $item['numOfProducts']; ?></td>
                                        <td><?php echo number_format($item['product_price'], 0, ',', '.'); ?> VNĐ</td>
                                        <td><?php echo number_format($item['totalMoney'], 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center">Không có sản phẩm nào trong đơn hàng này.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
        } else {
            echo '<div class="alert alert-danger">Không tìm thấy đơn hàng hoặc bạn không có quyền xem đơn hàng này.</div>';
        }
    } else {
        // Display list of orders
        $orders_sql = "SELECT * FROM `order` WHERE user_id = ? ORDER BY orderDate DESC";
        $stmt_orders = $conn->prepare($orders_sql);
        $stmt_orders->bind_param("i", $user_id);
        $stmt_orders->execute();
        $orders_result = $stmt_orders->get_result();

        if ($orders_result->num_rows > 0) {
            ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($order = $orders_result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date("d/m/Y H:i:s", strtotime($order['orderDate'])); ?></td>
                                <td><?php echo number_format($order['totalMoney'], 0, ',', '.'); ?> VNĐ</td>
                                <td><?php echo $order['status']; ?></td>
                                <td>
                                    <a href="order.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        } else {
            echo '<div class="alert alert-info text-center">Bạn chưa có đơn hàng nào.</div>';
        }
    }
    ?>
</div>

<?php
include_once 'includes/footer.php';
?>
