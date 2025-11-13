<?php
include 'conn.php';

// Handle new product form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $company_name = trim($_POST['company_name']);

    if ($name && $category && $price && $company_name) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, company_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $category, $price, $company_name);
        $stmt->execute();
    }
}
// Fetch products grouped by category
$categories = ['Household Goods', 'Electronics'];
$products_by_category = [];
foreach ($categories as $cat) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category=?");
    $stmt->bind_param("s", $cat);
    $stmt->execute();
    $result = $stmt->get_result();
    $products_by_category[$cat] = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Catalog</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f8eef1; }
    .container { width: 90%; max-width: 900px; margin: 30px auto; background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.07); }
    h2 { color: #891f42; text-align: center; margin-bottom: 30px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
    th, td { padding: 12px 10px; border: 1px solid #f0cbd4; }
    th { background: #891f42; color: #fff; font-size: 18px; }
    tr:nth-child(even) { background: #f6d2dd; }
    .add-form { margin-bottom: 38px; display: flex; gap: 10px; justify-content: center; }
    .add-form input, .add-form select { padding: 8px; font-size: 16px; border-radius: 5px; border: 1px solid #f0cbd4; }
    .add-form button { padding: 9px 18px; font-size: 16px; border-radius: 5px; border: none; background: #891f42; color: #fff; font-weight: bold; cursor: pointer; transition: background 0.2s ease-in-out; }
    .add-form button:hover { background: #89301f; }
    h3 { color: #891f42; margin-top: 35px; margin-bottom: 14px; }
  </style>
</head>
<body>
  <div class="navbar" style="background: #891f42; padding: 15px; text-align: center; margin-bottom: 30px;">
    <a href="index.php?page=home" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Home</a>
    <a href="index.php?page=dashboard" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Dashboard</a>
    <a href="product.php" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Product Master</a>
    <a href="index.php?page=user" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">User Master</a>
    <a href="index.php?page=supplier" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Supplier Master</a>
    <a href="logout.php" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Logout</a>
</div>
  <div class="container">
    <h2>Product Catalog</h2>
    <form method="post" class="add-form">
      <input type="text" name="name" placeholder="Product name" required />
      <select name="category" required>
        <option value="">Select category</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat ?>"><?= $cat ?></option>
        <?php endforeach; ?>
      </select>
      <input type="number" name="price" placeholder="Price" step="0.01" min="0.01" required />
      <input type="text" name="company_name" placeholder="Company name (supplier)" required />
      <button type="submit">Add Product</button>
    </form>
    <?php foreach ($categories as $cat): ?>
      <h3><?= $cat ?></h3>
      <table>
        <tr>
          <th>Product</th><th>Price</th><th>Company</th>
        </tr>
        <?php if (!empty($products_by_category[$cat])): ?>
          <?php foreach ($products_by_category[$cat] as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>$<?= number_format($p['price'], 2) ?></td>
            <td><?= htmlspecialchars($p['company_name'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
        <tr><td colspan="3">No products in this category yet.</td></tr>
        <?php endif; ?>
      </table>
    <?php endforeach; ?>
  </div>
</body>
</html>
