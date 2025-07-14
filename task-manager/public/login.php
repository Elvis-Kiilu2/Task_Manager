<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once  __DIR__ . '/../includes/db.php';


$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $expected_role = $_POST['role'] === 'admin' ? 1 : 0;

    $stmt = $conn->prepare("SELECT id, name, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashed_password, $is_admin);
        $stmt->fetch();

        if ($expected_role != $is_admin) {
            $error = "Incorrect login panel for this account.";
        } elseif (!password_verify($password, $hashed_password)) {
            $error = "Invalid password.";
        } else {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['is_admin'] = $is_admin;
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Task Manager</title>
    <style>
        body {
            font-family: Arial;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 50px;
            background: #f8f8f8;
        }
        .container {
            display: flex;
            gap: 50px;
        }
        .login-box {
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h2 {
            margin-top: 0;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #1976d2;
            color: white;
            border: none;
            margin-top: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-box">
        <h2>User Login</h2>
        <form method="POST" action="login.php">
            <input type="hidden" name="role" value="user">
            <?php if ($error && ($_POST['role'] ?? '') === 'user'): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login as User</button>
        </form>
    </div>

    <div class="login-box">
        <h2>Admin Login</h2>
        <form method="POST" action="login.php">
            <input type="hidden" name="role" value="admin">
            <?php if ($error && ($_POST['role'] ?? '') === 'admin'): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login as Admin</button>
        </form>
    </div>
</div>
</body>
</html>
