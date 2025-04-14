<?php
session_start();
require 'db.php';

// Kiểm tra user đăng nhập
if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = "Please Login to checkout!";
    header("Location: login.php");
    exit;
}

// Lấy thông tin khách hàng
$customer_name = $_SESSION['user']['name'];
$customer_email = $_SESSION['user']['email'];

// Giỏ hàng được lưu trong session
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['error'] = "Cart empty!";
    header("Location: cart.php");
    exit;
}

// Tính tổng tiền
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Thêm vào bảng orders
$status = 'pending';
$created_at = date("Y-m-d H:i:s");

$stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$customer_name, $customer_email, $total, $status, $created_at]);

// Xóa giỏ hàng
unset($_SESSION['cart']);

$_SESSION['success'] = "🎉 Checkout Successfully !";
header("Location: orders.php");
exit;
?>
