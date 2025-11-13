<?php
$servername = "localhost";
$username = "root"; // default user XAMPP
$password = ""; // biasanya kosong di XAMPP
$dbname = "db_users"; // sesuaikan dengan nama database kamu

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
