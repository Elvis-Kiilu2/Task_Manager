<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (!$_SESSION['is_admin']) {
    header("Location: dashboard.php");
    exit();
}

$msg = '';
$error = '';

// Fetch all users
$users = $conn->query("SELECT id, name, email FROM users WHERE is_admin = 0");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline = $_POST['deadline'];
    $user_id = $_POST['user_id'];

    if ($title && $deadline && $user_id) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, deadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $description, $deadline);

        if ($stmt->execute()) {
            // Send email
            $emailQuery = $conn->prepare("SELECT email FROM users WHERE id = ?");
            $emailQuery->bind_param("i", $user_id);
            $emailQuery->execute();
            $emailQuery->bind_result($recipient_email);
            $emailQuery->fetch();
            $emailQuery->close();

            $subject = "New Task Assigned";
            $message = "You have been assigned a new task: $title\n\nDeadline: $deadline";
            $headers = "From: taskmanager@localhost";

            mail($recipient_email, $subject, $message, $headers);

            $msg = "Task assigned and email sent.";
        } else {
            $error = "Failed to assign task.";
        }
        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Task</title>
</head>
<body>
    <h2>Assign Task</h2>
    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if ($msg): ?><p style="color:green"><?= $msg ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>

    <form method="POST" action="assign_task.php">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4"></textarea><br><br>

        <label>Deadline:</label><br>
        <input type="date" name="deadline" required><br><br>

        <label>Assign To:</label><br>
        <select name="user_id" required>
            <option value="">Select User</option>
            <?php while($u = $users->fetch_assoc()): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['email'] ?>)</option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Assign Task</button>
    </form>
</body>
</html>
