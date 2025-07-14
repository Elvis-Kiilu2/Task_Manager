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

$result = $conn->query("SELECT id, name, email FROM users WHERE is_admin = 0");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
    <h2>Manage Users</h2>
    <p><a href="../dashboard.php">← Back to Dashboard</a></p>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
