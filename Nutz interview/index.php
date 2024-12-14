<?php
session_start();
require 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


if (isset($_POST['create_post'])) {
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];
    $visibility = $_POST['visibility'];
    
    $audio_url = $video_url = null;

   
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] == 0) {
        $audio_file = $_FILES['audio'];
        $audio_url = 'uploads/audio/' . basename($audio_file['name']);
        move_uploaded_file($audio_file['tmp_name'], $audio_url);
    }

    
    if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
        $video_file = $_FILES['video'];
        $video_url = 'uploads/video/' . basename($video_file['name']);
        move_uploaded_file($video_file['tmp_name'], $video_url);
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, audio_url, video_url, visibility) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $content, $audio_url, $video_url, $visibility])) {
        echo "Post created successfully!";
    } else {
        echo "Error creating post.";
    }
}


$posts_stmt = $pdo->prepare("SELECT * FROM posts WHERE visibility = 'public' OR user_id = ?");
$posts_stmt->execute([$_SESSION['user_id']]);
$posts = $posts_stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Nutz Interview</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <!-- Create Post Form -->
    <form method="post" enctype="multipart/form-data">
        <h2>Create Post</h2>
        <textarea name="content" placeholder="Write your post..." required></textarea>
        <label for="visibility">Visibility</label>
        <select name="visibility" required>
            <option value="public">Public</option>
            <option value="private">Private</option>
        </select>
        <input type="file" name="audio" accept="audio/*">
        <input type="file" name="video" accept="video/*">
        <button type="submit" name="create_post">Create Post</button>
    </form>

   
    <h2>Your Posts</h2>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <p><?= htmlspecialchars($post['content']) ?></p>
            <?php if ($post['audio_url']): ?>
                <audio controls>
                    <source src="<?= $post['audio_url'] ?>" type="audio/mpeg">
                </audio>
            <?php endif; ?>
            <?php if ($post['video_url']): ?>
                <video controls>
                    <source src="<?= $post['video_url'] ?>" type="video/mp4">
                </video>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
