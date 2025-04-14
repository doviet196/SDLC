<?php
session_start();
require 'db.php';

$id = $_GET['id'];
$user = $pdo->query("SELECT * FROM users WHERE id = $id")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $avatar = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $avatar = uniqid() . '_' . $_FILES['avatar']['name'];
        move_uploaded_file($_FILES['avatar']['tmp_name'], "../uploads/$avatar");
    }

    $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=?, avatar=? WHERE id=?");
    $stmt->execute([$username, $email, $role, $avatar, $id]);

    $_SESSION['success'] = "✅ Cập nhật người dùng thành công!";
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa người dùng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5 col-md-6">
    <h3>✏️ Sửa người dùng</h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label>Tên đăng nhập</label>
        <input name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input name="email" type="email" class="form-control" value="<?= $user['email'] ?>" required>
      </div>
      <div class="mb-3">
        <label>Vai trò</label>
        <select name="role" class="form-select">
          <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
          <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Ảnh đại diện</label>
        <input type="file" name="avatar" class="form-control">
        <?php if ($user['avatar']): ?>
          <img src="../uploads/<?= $user['avatar'] ?>" width="100" class="mt-2">
        <?php endif; ?>
      </div>
      <button type="submit" class="btn btn-primary">Cập nhật</button>
      <a href="users.php" class="btn btn-secondary">← Quay lại</a>
    </form>
  </div>
</body>
</html>
