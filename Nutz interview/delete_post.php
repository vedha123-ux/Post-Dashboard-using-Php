<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Check if the post exists and belongs to the logged-in user
    $sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Delete the post
        $delete_sql = "DELETE FROM posts WHERE id = ?";
        $delete_stmt = $pdo->prepare($delete_sql);
        if ($delete_stmt->execute([$post_id])) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error deleting the post.";
        }
    } else {
        echo "Post not found or you don't have permission to delete it.";
    }
}
?>
