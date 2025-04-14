<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$updateMsg = "";
$passMsg = "";

// Cập nhật hồ sơ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone'];
    $address = $_POST['address'];

    // Xử lý upload avatar (nếu có)
    $avatar = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $avatarName = uniqid() . "_" . basename($_FILES['avatar']['name']);
        $target = "uploads/" . $avatarName;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            $avatar = $avatarName;
        }
    }

    $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, avatar=? WHERE id=?");
    $stmt->execute([$name, $email, $phone, $address, $avatar, $userId]);

    // Cập nhật session
    $_SESSION['user'] = array_merge($_SESSION['user'], [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'avatar' => $avatar
    ]);

    $updateMsg = "✅ Hồ sơ đã được cập nhật.";
}

// Đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $passMsg = "❌ Mật khẩu hiện tại không đúng.";
    } elseif ($new !== $confirm) {
        $passMsg = "❌ Mật khẩu mới không khớp.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->execute([$hashed, $userId]);
        $passMsg = "✅ Đổi mật khẩu thành công.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Hồ sơ người dùng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f4f6f9; }
    .profile-box {
      max-width: 800px;
      margin: 50px auto;
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
    }
    .avatar-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #0d6efd;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="profile-box">
    <div class="text-center mb-4">
      <img src="uploads/<?= htmlspecialchars($_SESSION['user']['avatar'] ?? 'default.jpg') ?>" class="avatar-img mb-2" alt="Avatar">
      <h3><?= htmlspecialchars($_SESSION['user']['name']) ?></h3>
      <p class="text-muted">VIP USER</p>
    </div>

    <?php if ($updateMsg): ?><div class="alert alert-success"><?= $updateMsg ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">New Avatar:</label>
        <input type="file" name="avatar" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Username:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone number:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" class="form-control">
      </div>
      <button type="submit" name="update_profile" class="btn btn-primary w-100">💾 Update</button>
    </form>

    <hr class="my-4">

    <h5 class="mb-3">🔐 Change password</h5>
    <?php if ($passMsg): ?>
      <div class="alert <?= str_starts_with($passMsg, '✅') ? 'alert-success' : 'alert-danger' ?>"><?= $passMsg ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label>Password:</label>
        <input type="password" name="current_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>New Password:</label>
        <input type="password" name="new_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Fill New Password:</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" name="change_password" class="btn btn-warning w-100">Change Password</button>
    </form>

    <div class="text-center mt-4">
      <a href="logout.php" class="btn btn-outline-danger">⏏️ Exit</a>
    </div>
  </div>
</div>

</body>
</html>
<div class="text-center mt-4 d-flex justify-content-between">
  <a href="noithat.php" class="btn btn-outline-secondary">🔙 Return</a>
  <a href="logout.php" class="btn btn-outline-danger">⏏️ Exit</a>
</div>

