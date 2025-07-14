<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../dashboard.php");
    exit();
}

$user_id = intval($_GET['id']);
$msg = '';
$error = '';

// Fetch user data
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $email) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $hashed, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }

        if ($stmt->execute()) {
            $msg = "✅ User updated successfully.";
        } else {
            $error = "❌ Update failed. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Name and email are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - Cytonn Task Manager</title>
    <link rel="stylesheet" href="../css/users.css">

</head>
<body>
    <div class="dashboard-container">
        <header class="main-header">
            <div class="header-left">
                <h1>Edit User</h1>
            </div>
            <div class="header-right">
                <nav>
                    <a href="../dashboard.php">← Back to Dashboard</a>
                    <a href="../../logout.php" class="logout-link">Logout</a>
                </nav>
            </div>
        </header>

        <main class="main-content">
            <?php if ($msg): ?><p class="success"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
            <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

            <form method="POST" class="form-box">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label>New Password (leave blank to keep current password):</label>
                <input type="password" name="password" placeholder="Enter new password if needed">

                <button type="submit">Update User</button>
            </form>
        </main>
    </div>
</body>
</html>
