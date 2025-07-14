<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../dashboard.php");
    exit();
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $email && $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $name, $email, $hashed);

        if ($stmt->execute()) {
            $msg = "✅ User added successfully.";
        } else {
            $error = "❌ Failed to add user. Email might already exist.";
        }
    } else {
        $error = "⚠️ All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User - Cytonn Task Manager</title>
    <link rel="stylesheet" href="../css/users.css">


</head>
<body>
    <div class="dashboard-container">
        <h2 style="text-align: center;">Add New User</h2>
        <p><a href="../dashboard.php">← Back to Dashboard</a></p>

        <?php if ($msg): ?><p class="success"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <form method="POST" action="add_user.php">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">➕ Add User</button>
        </form>
    </div>
</body>
</html>
