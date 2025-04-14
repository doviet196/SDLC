<?php
require 'db.php';
$id = $_GET['id'];
$product = $pdo->query("SELECT * FROM products WHERE id = $id")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    $image = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = uniqid() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
    }

    $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, description=?, image=?, category=? WHERE id=?");
    $stmt->execute([$name, $price, $description, $image, $category, $id]);

    header("Location: products.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa sản phẩm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow p-4">
    <h3 class="mb-4">✏️ Sửa sản phẩm</h3>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Tên sản phẩm</label>
        <input name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Giá</label>
        <input name="price" type="number" class="form-control" value="<?= $product['price'] ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Loại sản phẩm</label>
        <select name="category" class="form-select" required>
          <option value="">-- Chọn loại sản phẩm --</option>
          <?php
          $types = ['ghế','bàn','sofa','tủ','quầy'];
          foreach ($types as $type) {
              $selected = $product['category'] === $type ? 'selected' : '';
              echo "<option value='$type' $selected>" . ucfirst($type) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Ảnh sản phẩm</label>
        <input type="file" name="image" class="form-control">
        <?php if ($product['image']): ?>
          <div class="mt-2">
            <img src="uploads/<?= $product['image'] ?>" alt="Ảnh sản phẩm" class="img-thumbnail" width="150">
          </div>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
      <a href="products.php" class="btn btn-secondary ms-2">← Quay lại</a>
    </form>
  </div>
</div>
</body>
</html>
