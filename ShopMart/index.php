<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8eef1;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #891f42;
            padding: 15px;
            text-align: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
        }

        .navbar a:hover {
            text-decoration: underline;
            color: #fbe7ec;
        }

        .content {
            text-align: center;
            margin-top: 100px;
        }

        h2 {
            color: #891f42;
        }

        .logout-btn {
            background-color: #89301f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 8px;
        }

        .logout-btn:hover {
            background-color: #6f2718;
        }

        /* ✅ styling tambahan untuk card dashboard */
        .card {
            background: white;
            width: 400px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="navbar">
        <?php if (isset($_SESSION['user'])): ?>
            <!-- ✅ Jika SUDAH login -->
            <a href="index.php?page=home">Home</a>
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="product.php">Product Master</a>
            <a href="index.php?page=user">User Master</a>
            <a href="index.php?page=supplier">Supplier Master</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <!-- ❌ Jika BELUM login -->
            <a href="index.php?page=login">Login</a>
            <a href="index.php?page=register">Register</a>
        <?php endif; ?>
    </div>

    <div class="content">
        <?php
        // ✅ Jika user belum login tapi mencoba akses halaman selain login/register, arahkan ke login
        if (!isset($_SESSION['user']) && isset($_GET['page']) && !in_array($_GET['page'], ['login', 'register'])) {
            echo "<script>alert('Please login first!'); window.location='index.php?page=login';</script>";
            exit;
        }

        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            // ✅ tambahkan halaman yang diizinkan untuk user login
            $allowed_pages = ['login', 'register', 'data', 'home', 'dashboard', 'product', 'user', 'supplier'];
            if (in_array($page, $allowed_pages)) {
                if ($page === 'dashboard') {
                    echo "<div class='card' style='max-width:700px;width:85%;margin:60px auto;background:#fff;padding:36px;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,0.12);text-align:center;'>";
                    echo "<h2 style='color:#891f42;margin-bottom:18px;'>Welcome to the Dashboard</h2>";
                    echo "<p style='font-size:18px;color:#555;line-height:1.6;'>Use the navigation links above to manage users, products, and suppliers. This space will stay clean and focused so you can get to the tools you need quickly.</p>";
                    echo "</div>";
                } elseif ($page === 'supplier') {
                    if (isset($_GET['delete_supplier'])) {
                        $deleteId = intval($_GET['delete_supplier']);
                        if ($deleteId > 0) {
                            $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
                            $stmt->bind_param("i", $deleteId);
                            $stmt->execute();
                        }
                        echo "<script>window.location='index.php?page=supplier';</script>";
                        exit;
                    }

                    $editingSupplier = null;
                    if (isset($_GET['edit_supplier'])) {
                        $editId = intval($_GET['edit_supplier']);
                        if ($editId > 0) {
                            $stmt = $conn->prepare("SELECT supplier_id, supplier_name, product_name, location, contact_info, category_id FROM suppliers WHERE supplier_id = ?");
                            $stmt->bind_param("i", $editId);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $editingSupplier = $result->fetch_assoc();
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_supplier'])) {
                        $supplierId = intval($_POST['supplier_id'] ?? 0);
                        $name = trim($_POST['supplier_name']);
                        $productName = trim($_POST['product_name']);
                        $location = trim($_POST['location']);
                        $contact = trim($_POST['contact_info']);
                        $categoryId = intval($_POST['category_id']);
                        if ($name && $productName && $location && $categoryId) {
                            if ($supplierId > 0) {
                                $stmt = $conn->prepare("UPDATE suppliers SET supplier_name = ?, product_name = ?, location = ?, contact_info = ?, category_id = ? WHERE supplier_id = ?");
                                $stmt->bind_param("ssssii", $name, $productName, $location, $contact, $categoryId, $supplierId);
                                $stmt->execute();
                            } else {
                                $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, product_name, location, contact_info, category_id) VALUES (?, ?, ?, ?, ?)");
                                $stmt->bind_param("ssssi", $name, $productName, $location, $contact, $categoryId);
                                $stmt->execute();
                            }
                        }
                        echo "<script>window.location='index.php?page=supplier';</script>";
                        exit;
                    }

                    // Ensure required categories exist
                    $requiredCategories = ['Household Goods', 'Electronics'];
                    foreach ($requiredCategories as $catName) {
                        $check = $conn->query("SELECT category_id FROM categories WHERE category_name = '" . $conn->real_escape_string($catName) . "'");
                        if ($check->num_rows == 0) {
                            $conn->query("INSERT INTO categories (category_name) VALUES ('" . $conn->real_escape_string($catName) . "')");
                        }
                    }

                    $cats = $conn->query("SELECT category_id, category_name FROM categories WHERE category_name IN ('Household Goods', 'Electronics') ORDER BY category_name");
                    echo "<div class='card' style='max-width:1100px;width:90%;margin:30px auto;background:#fff;padding:32px;border-radius:10px;box-shadow:0 3px 16px rgba(0,0,0,0.10);'><h2>Supplier Master</h2>
    <form method='post' class='add-form' style='margin-bottom:38px;display:flex;gap:16px;justify-content:center;width:100%;flex-wrap:wrap;'>
      <input type='hidden' name='supplier_id' value='" . ($editingSupplier['supplier_id'] ?? "") . "'>
      <input type='text' name='supplier_name' placeholder='Supplier Name' value='" . htmlspecialchars($editingSupplier['supplier_name'] ?? '') . "' required style='flex:1 1 200px;min-width:0;padding:12px;font-size:18px;border-radius:5px;border:1px solid #ccc;'>
      <input type='text' name='product_name' placeholder='Product Name' value='" . htmlspecialchars($editingSupplier['product_name'] ?? '') . "' required style='flex:1 1 200px;min-width:0;padding:12px;font-size:18px;border-radius:5px;border:1px solid #ccc;'>
      <input type='text' name='location' placeholder='Location' value='" . htmlspecialchars($editingSupplier['location'] ?? '') . "' required style='flex:1 1 200px;min-width:0;padding:12px;font-size:18px;border-radius:5px;border:1px solid #ccc;'>
      <input type='text' name='contact_info' placeholder='Contact Info' value='" . htmlspecialchars($editingSupplier['contact_info'] ?? '') . "' style='flex:1 1 200px;min-width:0;padding:12px;font-size:18px;border-radius:5px;border:1px solid #ccc;'>
      <select name='category_id' required style='flex:1 1 180px;min-width:0;padding:12px;font-size:18px;border-radius:5px;border:1px solid #ccc;'>
        <option value=''>Select Category</option>";
                    if ($cats && $cats->num_rows > 0) {
                        foreach ($cats->fetch_all(MYSQLI_ASSOC) as $cat) {
                            $selected = ($editingSupplier && (int)$editingSupplier['category_id'] === (int)$cat['category_id']) ? " selected" : "";
                            echo "<option value='" . (int)$cat['category_id'] . "'" . $selected . ">" . htmlspecialchars($cat['category_name']) . "</option>";
                        }
                    }
                    $buttonLabel = $editingSupplier ? 'Update Supplier' : 'Add Supplier';
                    echo "</select>
      <button type='submit' name='save_supplier' style='padding:12px 28px;font-size:18px;border-radius:5px;border:none;background:#891f42;color:#fff;font-weight:bold;cursor:pointer;'>" . $buttonLabel . "</button>
      " . ($editingSupplier ? "<a href='index.php?page=supplier' style='padding:12px 22px;font-size:18px;border-radius:5px;border:1px solid #ddd;background:#fbe7ec;color:#891f42;font-weight:bold;text-decoration:none;'>Cancel</a>" : "") . "
    </form>
    <style>table.supply-table{width:100%;border-collapse:collapse;margin:15px 0;} .supply-table th{background:#891f42;color:#fff;padding:16px;font-size:20px;} .supply-table td{background:#fbe7ec;padding:13px 10px;border-bottom:1px solid #f0cbd4;font-size:16px;} .supply-table th,.supply-table td {text-align:left;} .supply-table tr:nth-child(even){background:#f6d2dd;} .supplier-actions{white-space:nowrap;} .supplier-btn{display:inline-block;padding:6px 12px;font-size:15px;border:none;border-radius:4px;margin-right:6px;color:#fff;background:#891f42;text-decoration:none;} .supplier-btn.edit{background:#89301f;} .supplier-btn.delete{background:#c0392b;} .supplier-btn:hover{opacity:0.9;}</style>
    <table class='supply-table'><tr><th>Supplier Name</th><th>Product Name</th><th>Location</th><th>Contact Info</th><th>Category</th><th>Action</th></tr>";
                    $stmt = $conn->query("SELECT s.supplier_id, s.supplier_name, s.product_name, s.location, s.contact_info, c.category_name FROM suppliers s LEFT JOIN categories c ON s.category_id = c.category_id ORDER BY c.category_name, s.supplier_name");
                    if ($stmt && $stmt->num_rows > 0) {
                        foreach ($stmt->fetch_all(MYSQLI_ASSOC) as $sup) {
                            echo "<tr><td>" . htmlspecialchars($sup['supplier_name']) . "</td><td>" . htmlspecialchars($sup['product_name']) . "</td><td>" . htmlspecialchars($sup['location']) . "</td><td>" . htmlspecialchars($sup['contact_info']) . "</td><td>" . htmlspecialchars($sup['category_name']) . "</td><td class='supplier-actions'><a class='supplier-btn edit' href='index.php?page=supplier&edit_supplier=" . (int)$sup['supplier_id'] . "'>Edit</a><a class='supplier-btn delete' href='index.php?page=supplier&delete_supplier=" . (int)$sup['supplier_id'] . "' onclick=\"return confirm('Delete this supplier?');\">Delete</a></td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No suppliers found.</td></tr>";
                    }
                    echo "</table></div>";
                } elseif ($page === 'product') {
                    $products = $conn->query("SELECT p.id, p.name, p.price, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.id");
                    echo "<div class='card' style='max-width:1100px;width:90%;margin:30px auto;background:#fff;padding:32px;border-radius:10px;box-shadow:0 3px 16px rgba(0,0,0,0.10);'><h2>Product Master</h2>
    <style>table.product-table{width:100%;border-collapse:collapse;margin:15px 0;} .product-table th{background:#891f42;color:#fff;padding:16px;font-size:18px;} .product-table td{background:#fbe7ec;padding:13px 10px;border-bottom:1px solid #f0cbd4;font-size:16px;} .product-table th,.product-table td{text-align:left;} .product-table tr:nth-child(even){background:#f6d2dd;}</style>
    <table class='product-table'><tr><th>ID</th><th>Product Name</th><th>Category</th><th>Price</th></tr>";
                    if ($products && $products->num_rows > 0) {
                        foreach ($products->fetch_all(MYSQLI_ASSOC) as $prod) {
                            echo "<tr><td>" . htmlspecialchars($prod['id']) . "</td><td>" . htmlspecialchars($prod['name']) . "</td><td>" . htmlspecialchars($prod['category_name']) . "</td><td>$" . htmlspecialchars(number_format($prod['price'], 2)) . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No products found.</td></tr>";
                    }
                    echo "</table></div>";
                } elseif ($page === 'user') {
                    if (isset($_GET['delete_user'])) {
                        $uid = intval($_GET['delete_user']);
                        if ($uid > 0) {
                            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                            $stmt->bind_param("i", $uid);
                            $stmt->execute();
                        }
                        echo "<script>window.location='index.php?page=user';</script>";
                        exit;
                    }
                    if (isset($_GET['reset_user'])) {
                        $uid = intval($_GET['reset_user']);
                        if ($uid > 0) {
                            $resetPass = password_hash('changeme', PASSWORD_DEFAULT);
                            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id = ?");
                            $stmt->bind_param("si", $resetPass, $uid);
                            $stmt->execute();
                        }
                        echo "<script>window.location='index.php?page=user';</script>";
                        exit;
                    }
                    $stmt = $conn->query("SELECT id, username, email FROM users");
                    $users = $stmt->fetch_all(MYSQLI_ASSOC);
                    echo "<div class='card' style='max-width:1100px;width:90%;margin:30px auto;background:#fff;padding:32px;border-radius:10px;box-shadow:0 3px 16px rgba(0,0,0,0.10);'><h2>User List</h2>\n<style>table.user-table{width:100%;border-collapse:collapse;margin:15px 0;} .user-table th{background:#891f42;color:#fff;padding:16px;font-size:20px;} .user-table td{background:#fbe7ec;padding:13px 10px;border-bottom:1px solid #f0cbd4;font-size:16px;} .user-table th,.user-table td {text-align:left;} .user-table tr:nth-child(even){background:#f6d2dd;} .user-actions{white-space:nowrap;} .user-btn{display:inline-block;padding:6px 12px;font-size:15px;border:none;border-radius:4px;margin-right:6px;color:#fff;background:#891f42;text-decoration:none;cursor:pointer;} .user-btn.delete{background:#c0392b;} .user-btn.reset{background:#89301f;} .user-btn:hover{opacity:0.9;}</style>\n<table class='user-table'><tr><th>ID</th><th>Username</th><th>Email</th><th>Action</th></tr>";
                    foreach ($users as $u) {
                        echo "<tr><td>".htmlspecialchars($u['id'])."</td><td>".htmlspecialchars($u['username'])."</td><td>".htmlspecialchars($u['email'])."</td><td class='user-actions'>"
                        ."<a class='user-btn delete' href='index.php?page=user&delete_user=".$u['id']."' onclick=\"return confirm('Are you sure to delete this user?');\">Delete</a>"
                        ."<a class='user-btn reset' href='index.php?page=user&reset_user=".$u['id']."' onclick=\"return confirm('Reset this user password to default?');\">Reset Password</a>"
                        ."</td></tr>";
                    }
                    echo "</table></div>";
                } elseif ($page === 'home') {
                    echo "<div class='card'><h2>Welcome Home</h2><p>Hello, " . htmlspecialchars($_SESSION['user']['username']) . "!</p></div>";
                } else {
                    include $page.".php";
                }
            } else {
                echo "<h2>Page not found.</h2>";
            }
        } else {
            // ✅ Halaman default (Home)
            if (isset($_SESSION['user'])) {
                echo "<h2>Hello, welcome <b>" . htmlspecialchars($_SESSION['user']['username']) . "</b>!</h2>";
                echo "<p>You have successfully logged in using your email: <b>" . htmlspecialchars($_SESSION['user']['email']) . "</b></p>";
            } else {
                echo "<h3>Welcome to NelsonMart</h3>";
                echo "<p>Shop with us for affordable deals.</p>";
            }
        }
        ?>
    </div>

</body>
</html>
