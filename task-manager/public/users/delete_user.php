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

$user_id = intval($_GET['id']);

// Prevent deleting own account
if ($user_id == $_SESSION['user_id']) {
    die("You cannot delete your own account.");
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    header("Location: ../dashboard.php");
    exit();
} else {
    die("Failed to delete user.");
}
?>