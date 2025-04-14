<?php
session_start();
require 'db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "🗑️ Xóa đơn hàng thành công!";
}
header("Location: orders.php");
exit;
