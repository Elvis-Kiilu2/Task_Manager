<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (!$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}

$msg = '';
$error = '';

// Fetch all non-admin users
$users = $conn->query("SELECT id, name, email FROM users WHERE is_admin = 0");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];
    $user_id = $_POST['user_id'];

    if ($title && $start_date && $deadline && $user_id) {
        // Validate start_date <= deadline
        if (strtotime($start_date) > strtotime($deadline)) {
            $error = "Start date cannot be after deadline.";
        } else {
            $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, start_date, description, deadline) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $title, $start_date, $description, $deadline);

            if ($stmt->execute()) {
                $emailQuery = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
                $emailQuery->bind_param("i", $user_id);
                $emailQuery->execute();
                $emailQuery->bind_result($recipient_name, $recipient_email);
                $emailQuery->fetch();
                $emailQuery->close();

                // Email notification
                $subject = "New Task Assigned";
                $message = "Hello $recipient_name,\n\n"
                         . "You have been assigned a new task:\n"
                         . "Title: $title\n"
                         . "Description: $description\n"
                         . "Start Date: $start_date\n"
                         . "Deadline: $deadline\n\n"
                         . "Please log in to your dashboard to view and update the task.\n\n"
                         . "Regards,\nTask Manager System";
                $headers = "From: taskmanager@localhost";

                mail($recipient_email, $subject, $message, $headers);
                $msg = "✅ Task assigned and email sent to $recipient_email.";
            } else {
                $error = "❌ Failed to assign task. Please try again.";
            }
            $stmt->close();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Task - Cytonn Task Manager</title>
    <link rel="stylesheet" href="css/tasks.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <header class="main-header">
            <div class="header-left">
                <h1>Assign Task</h1>
            </div>
            <div class="header-right">
                <nav>
                    <a href="dashboard.php">← Back to Dashboard</a>
                    <a href="logout.php" class="logout-link">Logout</a>
                </nav>
            </div>
        </header>

        <main class="main-content">
            <?php if ($msg): ?><p class="success"><?= $msg ?></p><?php endif; ?>
            <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

            <form method="POST" action="assign_task.php" class="form-box">
                <label>Title</label>
                <input type="text" name="title" required>

                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Optional..."></textarea>

                <label>Start Date</label>
                <input type="date" name="start_date" required>

                <label>Deadline</label>
                <input type="date" name="deadline" required>

                <label>Assign To</label>
                <select name="user_id" required>
                    <option value="">Select User</option>
                    <?php while ($u = $users->fetch_assoc()): ?>
                        <option value="<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">➕ Assign Task</button>
            </form>
        </main>
    </div>
</body>
</html>
