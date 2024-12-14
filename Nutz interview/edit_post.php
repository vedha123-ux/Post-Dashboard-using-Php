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

    // Fetch the existing post data
    $sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found or you don't have permission to edit it.";
        exit();
    }

    // Update post if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $content = $_POST['content'];
        $visibility = $_POST['visibility'];
        $media_path = $post['media_path'];  // Keep existing media path if not changed

        // Handle file upload (if new media uploaded)
        if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
            $upload_dir = 'uploads/';
            $file_name = basename($_FILES['media']['name']);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['media']['tmp_name'], $file_path)) {
                $media_path = $file_path;
            } else {
                echo "Error uploading the file.";
                exit();
            }
        }

        // Update the post in the database
        $update_sql = "UPDATE posts SET content = ?, media_path = ?, visibility = ? WHERE id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        if ($update_stmt->execute([$content, $media_path, $visibility, $post_id])) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error updating the post.";
        }
    }
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
        <form method="POST" action="" enctype="multipart/form-data">
            <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            <input type="file" name="media">
            <select name="visibility">
                <option value="public" <?php echo $post['visibility'] == 'public' ? 'selected' : ''; ?>>Public</option>
                <option value="private" <?php echo $post['visibility'] == 'private' ? 'selected' : ''; ?>>Private</option>
            </select>
            <button type="submit">Update Post</button>
        </form>
    </div>
</body>
</html>
