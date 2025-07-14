<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id']);
    $new_status = trim($_POST['status']);
    $user_id = $_SESSION['user_id'];

    $allowed_statuses = ['Pending', 'In Progress', 'Completed'];
    if (!in_array($new_status, $allowed_statuses)) {
        die("⛔ Invalid status value.");
    }

    // Confirm this task is owned by the current user
    $stmt = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("❌ Unauthorized: You can only update your own tasks.");
    }

    // Proceed to update status
    $update = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $update->bind_param("si", $new_status, $task_id);

    if ($update->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        die("⚠️ Failed to update task status.");
    }
}
?>
