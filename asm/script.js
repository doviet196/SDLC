let allProducts = [];
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Lấy dữ liệu sản phẩm từ server
async function fetchProducts() {
  try {
    const res = await fetch("get_products.php");
    allProducts = await res.json();
    renderProductList(allProducts);
  } catch (err) {
    console.error("Lỗi khi lấy sản phẩm:", err);
  }
}

// Hiển thị danh sách sản phẩm
function renderProductList(products) {
  const list = document.getElementById("productList");
  if (!list) return;
  list.innerHTML = "";

  if (products.length === 0) {
    list.innerHTML = "<p>Không có sản phẩm nào.</p>";
    return;
  }

  for (let p of products) {
    const card = document.createElement("div");
    card.className = "product-card";

    card.innerHTML = `
      <img src="uploads/${p.image}" alt="${p.name}">
      <div class="product-info">
        <h4>${p.name}</h4>
        <p>${Number(p.price).toLocaleString()} VND</p>
        <p class="desc">${p.description ?? ""}</p>
        <button onclick="addToCart('${p.name}', ${p.price}, 'uploads/${p.image}')">
          🛒 Thêm vào giỏ
        </button>
      </div>
    `;
    list.appendChild(card);
  }
}

// Tìm kiếm
function handleSearch() {
  const keyword = document.getElementById("searchInput")?.value.trim().toLowerCase();
  const results = allProducts.filter(p => p.name.toLowerCase().includes(keyword));
  renderProductList(results);
}

// Lọc sản phẩm
function filterProducts(event) {
  event.preventDefault();
  const min = parseInt(document.getElementById("minPrice").value) || 0;
  const max = parseInt(document.getElementById("maxPrice").value) || Infinity;
  const type = document.getElementById("productType").value.toLowerCase();

  const filtered = allProducts.filter(p => {
    return (
      p.price >= min &&
      p.price <= max &&
      (type === "" || p.name.toLowerCase().includes(type))
    );
  });

  renderProductList(filtered);
}

// Thêm sản phẩm vào giỏ
function addToCart(name, price, image) {
  const existing = cart.find(item => item.name === name);
  if (existing) {
    existing.quantity++;
  } else {
    cart.push({ name, price, image, quantity: 1 });
  }
  syncCart();
  showToast("✅ Đã thêm vào giỏ hàng");
}

// Tăng/giảm/xóa sản phẩm
function increaseQuantity(i) {
  cart[i].quantity++;
  syncCart();
}
function decreaseQuantity(i) {
  if (cart[i].quantity > 1) {
    cart[i].quantity--;
  } else {
    cart.splice(i, 1);
  }
  syncCart();
}
function removeFromCart(i) {
  cart.splice(i, 1);
  syncCart();
}

// Cập nhật giỏ và hiển thị
function syncCart() {
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartPopup();
}

function updateCartPopup() {
  const cartItems = document.getElementById("cartItems");
  const cartTotal = document.getElementById("cartTotal");
  const cartCount = document.getElementById("cartCount");

  if (!cartItems || !cartTotal || !cartCount) return;

  cartItems.innerHTML = "";
  let total = 0, quantity = 0;

  cart.forEach((item, i) => {
    total += item.price * item.quantity;
    quantity += item.quantity;

    const itemHTML = `
      <div class="cart-item">
        <img src="${item.image}" alt="${item.name}">
        <div>
          <h4>${item.name}</h4>
          <p>${item.price.toLocaleString()} VND</p>
          <div class="quantity-control">
            <button onclick="decreaseQuantity(${i})">–</button>
            <span>${item.quantity}</span>
            <button onclick="increaseQuantity(${i})">+</button>
          </div>
        </div>
        <button class="remove-button" onclick="removeFromCart(${i})">✖</button>
      </div>
    `;
    cartItems.insertAdjacentHTML("beforeend", itemHTML);
  });

  cartTotal.innerText = "Tổng cộng: " + total.toLocaleString() + " VND";
  cartCount.innerText = quantity;
}

// Toggle giỏ hàng
function toggleCartPopup() {
  document.getElementById("cartPopup")?.classList.toggle("open");
}

// Thanh toán
function checkout() {
  if (cart.length === 0) {
    showToast("🛑 Giỏ hàng đang trống.");
    return;
  }
  cart = [];
  localStorage.removeItem("cart");
  updateCartPopup();
  showToast("✅ Thanh toán thành công!");
}

// Hiển thị Toast
function showToast(message) {
  const toast = document.createElement("div");
  toast.className = "toast";
  toast.innerText = message;
  document.getElementById("toast-container").appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// Load khi DOM sẵn sàng
document.addEventListener("DOMContentLoaded", () => {
  fetchProducts();
  updateCartPopup();
});
