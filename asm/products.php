<?php
require 'db.php';
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý sản phẩm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <h2 class="mb-4">📦 Quản lý sản phẩm</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ Thêm sản phẩm thành công!</div>
  <?php elseif (isset($_GET['updated'])): ?>
    <div class="alert alert-warning">✏️ Cập nhật sản phẩm!</div>
  <?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert alert-danger">🗑️ Đã xoá sản phẩm!</div>
  <?php endif; ?>

  <a href="add_product.php" class="btn btn-success mb-3">➕ Thêm sản phẩm</a>

  <table class="table table-bordered table-hover">
    <thead class="table-dark">
      <tr>
        <th>Ảnh</th>
        <th>Tên</th>
        <th>Giá</th>
        <th>Mô tả</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><img src="uploads/<?= htmlspecialchars($p['image']) ?>" width="70" style="border-radius:8px;"></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= number_format($p['price']) ?> VND</td>
          <td><?= htmlspecialchars($p['description']) ?></td>
          <td>
            <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
            <a href="delete_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá sản phẩm này?')">Xoá</a>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn btn-outline-secondary">← Quay lại Dashboard</a>
</div>
</body>
</html>
