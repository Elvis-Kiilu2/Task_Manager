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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Cytonn Task Manager</title>
    <link rel="stylesheet" href="../css/users.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Users</h2>

            <div class="nav-link">
                <a href="../dashboard.php">← Back to Dashboard</a>
            </div>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td class="action-buttons text-center">
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="edit-btn">✏️ Edit</a>
                                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this user?');">🗑️ Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
    </div>

</body>
</html>
