<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $post_id = $_POST['id'];
    $content = $_POST['content'];
    $visibility = $_POST['visibility'];

    $sql = "UPDATE posts SET content = ?, visibility = ? WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$content, $visibility, $post_id, $user_id])) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating the post.";
    }
}

// Fetch the post details
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    $sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found or access denied.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Edit Post</title>
</head>
<body>
    <div class="container">
        <h1>Edit Post</h1>
        <form method="POST" action="">
            <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            <select name="visibility">
                <option value="public" <?php echo $post['visibility'] == 'public' ? 'selected' : ''; ?>>Public</option>
                <option value="private" <?php echo $post['visibility'] == 'private' ? 'selected' : ''; ?>>Private</option>
            </select>
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
            <button type="submit">Update Post</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
