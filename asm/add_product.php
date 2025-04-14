<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = (int)$_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $image = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $description, $image, $category]);

    header("Location: products.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm sản phẩm</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: #ffffff;
      padding: 30px 35px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      width: 400px;
      max-width: 95%;
    }

    .form-container h3 {
      margin-bottom: 25px;
      text-align: center;
      color: #003366;
    }

    .form-container input,
    .form-container select,
    .form-container textarea {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .form-container textarea {
      resize: vertical;
      min-height: 80px;
    }

    .form-container button {
      width: 100%;
      padding: 10px 0;
      background-color: #003366;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .form-container button:hover {
      background-color: #0056b3;
    }

    .form-container a {
      display: block;
      margin-top: 12px;
      text-align: center;
      font-size: 14px;
      color: #555;
      text-decoration: none;
    }

    .form-container a:hover {
      color: #003366;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h3><i class="fas fa-plus-circle"></i> Add products</h3>
  <form method="POST" enctype="multipart/form-data">
    <input name="name" placeholder="Name product" required>
    <input name="price" type="number" placeholder="Price" required>
    <textarea name="description" placeholder="Details..."></textarea>

    <select name="category" required>
      <option value="">--  Select product type --</option>
      <option value="ghế">Ghế</option>
      <option value="bàn">Bàn</option>
      <option value="sofa">Sofa</option>
      <option value="tủ">Tủ</option>
      <option value="quầy">Quầy</option>
      <option value="giường">Giường</option>
      <option value="đèn">Đèn</option>
      <option value="tranh">Tranh</option>
      <option value="gương">Gương</option>
    </select>

    <input type="file" name="image" accept="image/*">

    <button type="submit"><i class="fas fa-plus"></i> Add</button>
    <a href="products.php"><i class="fas fa-arrow-left"></i> Return</a>
  </form>
</div>

</body>
</html>
