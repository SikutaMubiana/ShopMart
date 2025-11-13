<?php
include 'conn.php';
// Handle new supplier form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name']);
    $items = trim($_POST['items']);
    $packs = trim($_POST['packs']);
    if ($company_name && $items && $packs) {
        $stmt = $conn->prepare("INSERT INTO suppliers (company_name, items, packs) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $company_name, $items, $packs);
        $stmt->execute();
    }
}
// Create suppliers table if not exists (only runs if table is NOT there, safe if you already have the table)
$conn->query("CREATE TABLE IF NOT EXISTS suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(100) NOT NULL,
  items VARCHAR(255) NOT NULL,
  packs VARCHAR(255) NOT NULL
)");
// Fetch suppliers
$result = $conn->query("SELECT company_name, items, packs FROM suppliers");
$suppliers = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Supplier Master</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f8eef1; }
    .container { width: 90%; max-width: 1100px; margin: 30px auto; background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.07); }
    h2 { color: #891f42; text-align: center; margin-bottom: 30px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
    th, td { padding: 12px 10px; border: 1px solid #f0cbd4; }
    th { background: #891f42; color: #fff; font-size: 18px; }
    tr:nth-child(even) { background: #f6d2dd; }
    td { background: #fbe7ec; font-size: 16px; }
    tr:hover { background: #f2c0cf; }
    .add-form {
      margin-bottom: 38px;
      display: flex;
      gap: 16px;
      justify-content: center;
      width: 100%;
    }
    .add-form input {
      flex: 1 1 0;
      min-width: 0;
      padding: 12px;
      font-size: 18px;
      border-radius: 5px;
      border: 1px solid #f0cbd4;
    }
    .add-form button {
      padding: 12px 28px;
      font-size: 18px;
      border-radius: 5px;
      border: none;
      background: #891f42;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s ease-in-out;
    }
    .add-form button:hover { background: #89301f; }
  </style>
</head>
<body>
  <div class="navbar" style="background: #891f42; padding: 15px; text-align: center; margin-bottom: 30px;">
    <a href="index.php?page=home" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Home</a>
    <a href="index.php?page=dashboard" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Dashboard</a>
    <a href="product.php" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Product Master</a>
    <a href="index.php?page=user" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">User Master</a>
    <a href="supplier.php" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Supplier Master</a>
    <a href="logout.php" style="color: white; text-decoration: none; font-weight: bold; margin: 0 15px;">Logout</a>
  </div>
  <div class="container">
    <h2>Supplier Master</h2>
    <form method="post" class="add-form">
      <input type="text" name="company_name" placeholder="Company name" required />
      <input type="text" name="items" placeholder="Items" required />
      <input type="text" name="packs" placeholder="Packs" required />
      <button type="submit">Add Supplier</button>
    </form>
    <table>
      <tr>
        <th>Company Name</th>
        <th>Items</th>
        <th>Packs</th>
      </tr>
      <?php if (!empty($suppliers)): ?>
        <?php foreach ($suppliers as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['company_name']) ?></td>
          <td><?= htmlspecialchars($s['items']) ?></td>
          <td><?= htmlspecialchars($s['packs']) ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="3">No suppliers found.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</body>
</html>
