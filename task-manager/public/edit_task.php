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
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    if ($title && $deadline && $status) {
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, deadline = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $description, $deadline, $status, $task_id);
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
    } else {
        $error = "All fields except description are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
</head>
<body>
    <h2>Edit Task</h2>
    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if ($msg): ?><p style="color:green"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <?php if ($task): ?>
    <form method="POST">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea><br><br>

        <label>Deadline:</label><br>
        <input type="date" name="deadline" value="<?= htmlspecialchars($task['deadline']) ?>" required><br><br>

        <label>Status:</label><br>
        <select name="status" required>
            <option <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select><br><br>

        <button type="submit">Update Task</button>
    </form>
    <?php endif; ?>
</body>
</html>
