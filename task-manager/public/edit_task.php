<?php
require_once  __DIR__ .'/../includes/auth.php';
require_once  __DIR__ .'/../includes/db.php';

// Restrict to admins
if (!$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$msg = '';

// Load task to edit
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = (int)$_GET['id'];
$task = null;

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $task = $result->fetch_assoc();
} else {
    $error = "Task not found.";
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    if ($title && $start_date && $deadline && $status) {
        if (strtotime($start_date) > strtotime($deadline)) {
            $error = "Start date cannot be after the deadline.";
        } else {
            $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, start_date = ?, deadline = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $title, $description, $start_date, $deadline, $status, $task_id);
            if ($stmt->execute()) {
                $msg = "Task updated successfully.";
            } else {
                $error = "Update failed.";
            }
            $stmt->close();

            // Refresh task data
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $task = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    } else {
        $error = "All fields except description are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task - Cytonn Task Manager</title>
    <link rel="stylesheet" href="css/tasks.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <header class="main-header">
            <div class="header-left">
                <h1>Edit Task</h1>
            </div>
            <div class="header-right">
                <nav>
                    <a href="dashboard.php">← Back to Dashboard</a>
                    <a href="logout.php" class="logout-link">Logout</a>
                </nav>
            </div>
        </header>

        <main class="main-content">
            <?php if ($msg): ?><p class="success"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
            <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

            <?php if ($task): ?>
            <form method="POST" class="form-box">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>

                <label>Description</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>

                <label>Start Date</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($task['start_date']) ?>" required>

                <label>Deadline</label>
                <input type="date" name="deadline" value="<?= htmlspecialchars($task['deadline']) ?>" required>

                <label>Status</label>
                <select name="status" required>
                    <option <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>

                <button type="submit">💾 Update Task</button>
            </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
