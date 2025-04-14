<?php
session_start();
require 'db.php';

// Lấy danh sách đơn hàng
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý đơn hàng</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2>📦 Quản lý đơn hàng</h2>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover mt-3">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Khách hàng</th>
          <th>Email</th>
          <th>Tổng tiền</th>
          <th>Trạng thái</th>
          <th>Thời gian</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
          <td>#<?= $order['id'] ?></td>
          <td><?= htmlspecialchars($order['customer_name']) ?></td>
          <td><?= htmlspecialchars($order['customer_email']) ?></td>
          <td><?= number_format($order['total_amount']) ?> VND</td>
          <td><span class="badge bg-info text-dark"><?= $order['status'] ?></span></td>
          <td><?= $order['created_at'] ?></td>
          <td>
            <a href="edit_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
            <a href="delete_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa đơn hàng này?')">Xóa</a>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">← Quay lại trang chính</a>
  </div>
</body>
</html>
