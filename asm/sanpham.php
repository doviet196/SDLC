<?php
session_start();
require 'db.php';
$user = $_SESSION['user'] ?? null;

// Lấy danh sách loại sản phẩm
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''")->fetchAll(PDO::FETCH_COLUMN);


// Xử lý lọc và sắp xếp
$category = $_GET['category'] ?? '';
$min = $_GET['min'] ?? 0;
$max = $_GET['max'] ?? 100000000;
$sort = $_GET['sort'] ?? 'new';

$query = "SELECT * FROM products WHERE price BETWEEN ? AND ?";
$params = [$min, $max];

if ($category && in_array($category, $categories)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

// Thêm điều kiện sắp xếp
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'name_az':
        $query .= " ORDER BY name ASC";
        break;
    case 'name_za':
        $query .= " ORDER BY name DESC";
        break;
    default:
        $query .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sản phẩm - BTEC Furniture</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <script defer src="script.js"></script>
</head>
<body>
<header>
  <div class="logo"><a href="noithat.php"><img src="logo.jpg" alt="Logo"></a></div>
  <nav class="navigation">
    <ul>
      <li><a href="noithat.php">HomePage</a></li>
      <li><a href="sanpham.php" class="active">Products</a></li>
      <?php if ($user): ?>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Log Out</a></li>
        <li style="color:white;">👤 <?= htmlspecialchars($user['username']) ?></li>
      <?php else: ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>
<style>.filter-form {
  padding: 20px;
  background-color: #f8f9fa;
  margin: 30px auto;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
  max-width: 1100px;
}

.filter-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 15px;
  align-items: center;
}

.filter-grid select,
.filter-grid input {
  padding: 10px 12px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.3s;
}

.filter-grid input:focus,
.filter-grid select:focus {
  border-color: #007bff;
  outline: none;
}

.filter-grid button {
  background-color: #003366;
  color: white;
  border: none;
  padding: 10px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
  transition: background 0.3s;
}

.filter-grid button:hover {
  background-color: #0056b3;
}
</style>
<main>
<section class="filter-form">
  <form method="GET">
    <div class="filter-grid">

      <!-- Chọn loại sản phẩm -->
      <select name="category" class="filter-select">
  <option value="">-- Full option --</option>
  <?php
  $presetTypes = [
    'ghế' => 'Ghế',
    'bàn' => 'Bàn',
    'sofa' => 'Sofa',
    'tủ' => 'Tủ',
    'quầy' => 'Quầy',
    'giường' => 'Giường',
    'đèn' => 'Đèn',
    'tranh' => 'Tranh',
    'gương' => 'Gương',
  ];

  foreach ($presetTypes as $value => $label) {
    $selected = ($category === $value) ? 'selected' : '';
    echo "<option value=\"$value\" $selected>$label</option>";
  }

  // Nếu còn danh mục khác không thuộc danh sách trên, cũng hiển thị thêm
  foreach ($categories as $c) {
    if (!array_key_exists(strtolower($c), $presetTypes)) {
      $selected = ($category === $c) ? 'selected' : '';
      echo "<option value=\"$c\" $selected>" . ucfirst($c) . "</option>";
    }
  }
  ?>
</select>


      <!-- Khoảng giá -->
      <input type="number" name="min" placeholder="From" value="<?= htmlspecialchars($min ?? 0) ?>">
      <input type="number" name="max" placeholder="To" value="<?= htmlspecialchars($max ?? 100000000) ?>">

      <!-- Sắp xếp -->
      <select name="sort" class="filter-select">
        <option value="new" <?= $sort == 'new' ? 'selected' : '' ?>>🕒 New</option>
        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>⬆ Up</option>
        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>⬇ Downn</option>
        <option value="name_az" <?= $sort == 'name_az' ? 'selected' : '' ?>>🔤 Name A-Z</option>
        <option value="name_za" <?= $sort == 'name_za' ? 'selected' : '' ?>>🔠 Name Z-A</option>
      </select>

      <button type="submit" class="filter-button"><i class="fas fa-filter"></i> Filter</button>
    </div>
  </form>
</section>
  <section>
    <h2 class="section-header">📦 Filter Results</h2>
    <div class="products">
      <?php if (count($products) === 0): ?>
        <p>Not Found.</p>
      <?php else: ?>
        <?php foreach ($products as $p): ?>
          <div class="product-card">
            <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            <div class="product-info">
              <h4><?= htmlspecialchars($p['name']) ?></h4>
              <p><?= number_format($p['price']) ?> VND</p>
              <p class="desc"><?= htmlspecialchars($p['description']) ?></p>
              <button onclick="addToCart('<?= addslashes($p['name']) ?>', <?= $p['price'] ?>, 'uploads/<?= $p['image'] ?>')">🛒 Add to cart</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<!-- Nút giỏ hàng -->
<button class="cart-button" onclick="toggleCartPopup()">
  <i class="fas fa-shopping-cart"></i>
  <span class="cart-count" id="cartCount">0</span>
</button>

<!-- Popup giỏ hàng -->
<div class="cart-popup" id="cartPopup">
  <h2>🛍️ Cart</h2>
  <div id="cartItems"></div>
  <div id="cartTotal" class="cart-total"></div>
  <div class="buttons">
    <button onclick="checkout()">Checkout</button>
    <button onclick="toggleCartPopup()">Close</button>
  </div>
</div>

<!-- Toast -->
<div id="toast-container"></div>

<!-- Footer -->
<footer>
  <p>© 2024 BTEC Furniture Store</p>
</footer>
</body>
</html>
