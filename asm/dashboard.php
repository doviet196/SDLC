<?php
session_start();
require 'db.php';

// Chỉ cho admin truy cập
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    header('Location: login.php');
    exit;
}

$adminName = $_SESSION['user']['username'] ?? 'Admin';

// Đếm số lượng
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { display: flex; min-height: 100vh; }
    .sidebar {
      width: 220px;
      background: #343a40;
      color: white;
      padding: 1rem;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 10px 0;
      display: block;
    }
    .sidebar a:hover {
      background-color: #495057;
      border-radius: 5px;
      padding-left: 10px;
    }
    .main {
      flex: 1;
      padding: 2rem;
      background-color: #f8f9fa;
    }
    .card {
      border: none;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .card:hover {
      transform: scale(1.01);
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h5 class="text-center mb-4">🛠️ Admin Dashboard</h5>
  <a href="dashboard.php">🏠 Homepage</a>
  <a href="users.php">👥 User</a>
  <a href="products.php">📦 Products</a>
  <a href="logout.php" class="text-danger">🚪 Exit</a>
</div>

<!-- Main content -->
<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🎉 Welcome <span class="text-primary"><?= htmlspecialchars($adminName) ?></span>!</h3>
    <a href="logout.php" class="btn btn-outline-secondary">Exit</a>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title">👥 User</h5>
          <p class="card-text">Tổng: <strong><?= $userCount ?></strong> Account</p>
          <a href="users.php" class="btn btn-light btn-sm">List</a>
          <a href="add_user.php" class="btn btn-outline-light btn-sm">Add new</a>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title">📦 Products</h5>
          <p class="card-text">Total: <strong><?= $productCount ?></strong> Product</p>
          <a href="products.php" class="btn btn-light btn-sm">List</a>
          <a href="add_product.php" class="btn btn-outline-light btn-sm">Add new</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <h5 class="card-title">📦 The Orders</h5>
      <p class="card-text">View and manage customer orders.</p>
      <a href="orders.php" class="btn btn-info btn-sm">Order</a>
    </div>
  </div>
</div>
  <div class="alert alert-info">
    📌This is the admin dashboard. You can manage users and products here.
  </div>
</div>

</body>
</html>
