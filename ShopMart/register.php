<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        echo "<p style='color:red; text-align:center;'>All fields are required!</p>";
    } else {
        
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "<p style='color:red; text-align:center;'>Email already registered! Use another email.</p>";
        } else {
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                echo "<p style='color:green; text-align:center;'>Registration successful! Please login.</p>";
                echo "<meta http-equiv='refresh' content='2;url=index.php?page=login'>";
            } else {
                echo "<p style='color:red; text-align:center;'>Error occurred while saving data.</p>";
            }
        }
    }
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8eef1;
        margin: 30px;
        padding: 25px;
    }

    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 55vh;
    }

    .register-box {
        background: white;
        padding: 10px;
        border-radius: 3px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 300px; /* <<< kecil seperti login */
        text-align: center;
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    h2 {
        margin-bottom: 18px;
        color: #891f42;
        font-size: 24px;
    }

    label {
        display: block;
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        color: #891f42;
        font-size: 14px;
    }

    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        margin-bottom: 15px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus {
        border-color: #891f42;
        box-shadow: 0 0 6px rgba(137, 31, 66, 0.3);
        outline: none;
    }

    button {
        width: 100%;
        background-color: #891f42;
        color: white;
        padding: 14px 0;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.25s, transform 0.1s;
    }

    button:hover {
        background-color: #89301f;
        transform: translateY(-1px);
    }

    button:active {
        transform: translateY(0);
    }

    p {
        font-size: 12px;
        margin-top: 12px;
    }

    a {
        color: #89301f;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        .register-box {
            width: 80%;
            padding: 30px 20px;
        }
    }
</style>

<div class="register-container">
    <div class="register-box">
        <h2>Register</h2>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Enter username" required>
            
            <label for ="location">Location:</lable>
            <input type="text" name="location" placeholder="Enter location" required>


            <label for ="Phone Number">Phone Number:</lable>
            <input type="text" name="Phone Number" placeholder="Enter Phone Number" required>

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Enter email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="index.php?page=login">Login here</a></p>
    </div>
</div>
