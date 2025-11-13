<?php
session_status();
include "koneksi.php";

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php?page=login");
    exit();
}
?>

<div style="display: flex; justify-content: center; align-items: flex-start; min-height: 80vh;">
    <div style="width: 100%; margin-top: 100px; background: white; padding: 45px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

        <h3 style="text-align: center; margin-bottom: 18px; font-size: 24px;">User List</h3>

        <table border="1" cellspacing="0" cellpadding="12" style="width: 100%; border-collapse: collapse; text-align: center; font-size: 16px;">
            <thead style="background: #212121; color: white;">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $query = "SELECT * FROM users ORDER BY id ASC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>
                                <a href='edit_user.php?id={$row['id']}'>Edit</a> |
                                <a href='delete_user.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No user data available.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
