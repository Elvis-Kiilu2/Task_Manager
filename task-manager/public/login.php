<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/includes/db.php';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Cytonn Task Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page" backgroundima="url">
    <div class="auth-container">
        <h1 style="text-align: center;">Cytonn Task Manager</h1>
        <p class="subtext">
            Manage and assign tasks efficiently across your team. Choose the appropriate login panel below.
        </p>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="login-panels">
            <!-- Admin Login -->
            <div class="login-box">
                <h3>Admin Login</h3>
                <form method="POST" action="login.php">
                    <input type="hidden" name="role" value="admin">
                    <label>Email</label>
                    <input type="email" name="email" required>

                    <label>Password</label>
                    <input type="password" name="password" required>

                    <button type="submit">Login as Admin</button>
                </form>
            </div>

            <!-- User Login -->
            <div class="login-box">
                <h3>User Login</h3>
                <form method="POST" action="login.php">
                    <input type="hidden" name="role" value="user">
                    <label>Email</label>
                    <input type="email" name="email" required>

                    <label>Password</label>
                    <input type="password" name="password" required>

                    <button type="submit">Login as User</button>
                </form>
            </div>
        </div>

        <footer class="footer-note">
            <p>Need help? Contact your system administrator.</p>
        </footer>
    </div>
</body>
</html>
