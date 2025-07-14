<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once  __DIR__ .'/../includes/auth.php';
require_once  __DIR__ .'/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id']);
    $new_status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    // Validate allowed status values
    $allowed_statuses = ['Pending', 'In Progress', 'Completed'];
    if (!in_array($new_status, $allowed_statuses)) {
        die("Invalid status value.");
    }

    // Ensure the task belongs to this user
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $new_status, $task_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        header("Location: dashboard.php");
        exit();
    } else {
        die("Failed to update task status or unauthorized.");
    }
}
?>
