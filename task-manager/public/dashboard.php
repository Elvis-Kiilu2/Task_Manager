<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

// Fetch all tasks regardless of user role
$query = "SELECT t.*, u.name AS assigned_to 
          FROM tasks t 
          JOIN users u ON t.user_id = u.id 
          ORDER BY deadline ASC";
$tasks = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Cytonn Task Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <header class="main-header">
            <div class="header-left">
                <h1>Task Manager</h1>
            </div>
            <div class="header-right">
                <span class="welcome-msg">Welcome, <?= htmlspecialchars($name) ?><?= $is_admin ? " (Admin)" : "" ?>!</span>
                <nav>
                    <?php if ($is_admin): ?>
                        <a href="users/manage_users.php">Users</a>
                    <?php endif; ?>
                    <a href="logout.php" class="logout-link">Logout</a>
                </nav>
            </div>
        </header>

        <div class="dashboard-layout">
            <?php if ($is_admin): ?>
                <aside class="sidebar">
                    <h2>Admin Panel</h2>
                    <ul>
                        <li><a href="users/add_user.php">➕ Add User</a></li>
                        <li><a href="assign_task.php">📝 Assign Task</a></li>
                        <li><a href="users/manage_users.php">👥 Manage Users</a></li>
                    </ul>
                </aside>
            <?php endif; ?>

            <main class="main-content">
                <h3>All Tasks</h3>
                <table class="task-table">
                    <thead>
                        <tr>
                            <th>Assigned To</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $tasks->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['assigned_to']) ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= htmlspecialchars($row['deadline']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <?php if ($is_admin): ?>
                                        <a href="edit_task.php?id=<?= $row['id'] ?>" class="edit-btn">✏️ Edit</a>
                                    <?php elseif ($row['user_id'] == $user_id): ?>
                                        <form method="POST" action="update_status.php" style="display:inline-block;">
                                            <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                                            <select name="status">
                                                <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="In Progress" <?= $row['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="Completed" <?= $row['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                            <button type="submit">Update</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="readonly-tag">View Only</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
</body>
</html>
