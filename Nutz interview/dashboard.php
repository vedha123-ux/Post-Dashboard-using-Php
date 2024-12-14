<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch posts from the database
$sql = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="dashboard.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="container">
        <h1>My Posts</h1>
        
        <!-- Button to create a new post -->
        <a href="post_create.php" class="btn-create">Create Post</a>

        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <div class="post-header">
                        <span class="post-time"><?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></span>
                        <span class="post-visibility"><?php echo ucfirst($post['visibility']); ?></span>
                    </div>
                    <p class="post-content"><?php echo htmlspecialchars($post['content']); ?></p>

                    <!-- Display Media -->
                    <?php if ($post['media_path']): ?>
                        <div class="media">
                            <?php if (strpos($post['media_path'], '.jpg') !== false || strpos($post['media_path'], '.png') !== false || strpos($post['media_path'], '.jpeg') !== false): ?>
                                <img src="<?php echo $post['media_path']; ?>" alt="Post Image" class="post-image">
                            <?php elseif (strpos($post['media_path'], '.mp4') !== false): ?>
                                <video width="320" height="240" controls class="post-video">
                                    <source src="<?php echo $post['media_path']; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-actions">
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn-edit">Edit</a>
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no posts yet. Create one!</p>
        <?php endif; ?>

    </div>
</body>
</html>
