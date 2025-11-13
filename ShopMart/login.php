<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // bisa username atau email
    $password   = trim($_POST['password']);

    if (empty($identifier) || empty($password)) {
        echo "<p style='color:red; text-align:center;'>Username/Email and password are required!</p>";
    } else {
        // Cek apakah input adalah email atau username
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $query = "SELECT * FROM users WHERE email = ? AND password = ?";
        } else {
            $query = "SELECT * FROM users WHERE username = ? AND password = ?";
        }

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $identifier, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ];

            echo "<p style='color:green; text-align:center;'>Login successful! Redirecting to the main page...</p>";
            echo "<meta http-equiv='refresh' content='2;url=index.php'>";
        } else {
            echo "<script>alert('Incorrect username/email or password!'); window.location.href='login.php';</script>";
        } 
    }
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color:#f8eef1;
        margin: 10px;
        padding: 2px;
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 20vh;
    }

    .login-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(137, 31, 66, 0.28);
        width: 400px;
        text-align: center; /* agar kotaknya tetap rapi di tengah */
    }

    h2 {
        margin-bottom: 12px;
        color: #891f42;
        font-size: 24px;
        text-align: center;
    }

    /* --- bagian ini penting --- */
    label {
        display: block;
        text-align: left; 
        margin-bottom: 6px;
        font-weight: bold;
        color: #891f42;
        font-size: 14px;
    }

    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 15px;
        margin-bottom: 15px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus {
        border-color:#891f42;
        box-shadow: 0 0 6px rgba(137, 31, 66, 0.35);
        outline: none;
    }

    button {
        width: 100%;
        background-color:#891f42;
        color: white;
        padding: 14px 0;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.25s, transform 0.1s;
    }

    button:hover {
        background-color:#89301f;
        transform: translateY(-1px);
    }

    button:active {
        transform: translateY(0);
    }

    p {
        font-size: 8px;
        margin-top: 10px;
    }

    a {
        color:#89301f;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>
        <form method="post" action="">
            <label for="username">Username or Email:</label>
            <input type="text" name="identifier" placeholder="Enter username or email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="index.php?page=register">Register here</a></p>
    </div>
</div>
