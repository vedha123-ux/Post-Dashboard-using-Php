<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the post ID is provided
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Check if the post belongs to the logged-in user
    $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$post_id, $user_id])) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error deleting the post.";
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
