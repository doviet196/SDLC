<?php
session_start();
require 'db.php';

$id = $_GET['id'];
$order = $pdo->query("SELECT * FROM orders WHERE id = $id")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);

    $_SESSION['success'] = "✅ Cập nhật trạng thái đơn hàng thành công!";
    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Cập nhật đơn hàng</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5 col-md-6">
    <h3>✏️ Cập nhật đơn hàng #<?= $id ?></h3>

    <form method="POST">
      <div class="mb-3">
        <label>Trạng thái</label>
        <select name="status" class="form-select">
          <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
          <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
          <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
          <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
        </select>
      </div>
      <button class="btn btn-primary">Cập nhật</button>
      <a href="orders.php" class="btn btn-secondary">← Quay lại</a>
    </form>
  </div>
</body>
</html>
