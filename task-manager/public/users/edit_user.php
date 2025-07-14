<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once  __DIR__ .'/../../includes/auth.php';
require_once  __DIR__ .'/../../includes/db.php';


if (!$_SESSION['is_admin']) {
    header("Location: ../dashboard.php");
    exit();
}

$user_id = intval($_GET['id']);
$msg = '';
$error = '';

// Fetch user info
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
            $msg = "User updated.";
        } else {
            $error = "Update failed.";
        }
        $stmt->close();
    } else {
        $error = "Name and email are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <p><a href="../dashboard.php">← Back to Dashboard</a></p>

    <?php if ($msg): ?><p style="color:green"><?= $msg ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>

    <form method="POST" action="">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label>New Password (leave blank to keep existing):</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit">Update User</button>
    </form>
</body>
</html>
