<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$is_admin = $_SESSION['is_admin'];

// Fetch tasks
if ($is_admin) {
    $query = "SELECT t.*, u.name AS assigned_to FROM tasks t JOIN users u ON t.user_id = u.id ORDER BY deadline ASC";
} else {
    $query = "SELECT * FROM tasks WHERE user_id = $user_id ORDER BY deadline ASC";
}

$tasks = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Task Manager</title>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($name) ?>!</h2>

    <p><a href="logout.php">Logout</a></p>

    <?php if ($is_admin): ?>
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="users/add_user.php">Add User</a></li>
            <li><a href="assign_task.php">Assign Task</a></li>
        </ul>
    <?php endif; ?>

    <h3>Your Tasks</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <?php if ($is_admin): ?><th>Assigned To</th><?php endif; ?>
            <th>Title</th>
            <th>Description</th>
            <th>Deadline</th>
            <th>Status</th>
            <?php if (!$is_admin): ?><th>Action</th><?php endif; ?>
        </tr>

        <?php while($row = $tasks->fetch_assoc()): ?>
            <tr>
                <?php if ($is_admin): ?><td><?= htmlspecialchars($row['assigned_to']) ?></td><?php endif; ?>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['deadline']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>

                <?php if (!$is_admin): ?>
                <td>
                    <form method="POST" action="update_status.php">
                        <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                        <select name="status">
                            <option <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option <?= $row['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option <?= $row['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
