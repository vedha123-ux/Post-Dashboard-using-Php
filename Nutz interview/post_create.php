<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $visibility = $_POST['visibility'];
    $media_path = null;

    // Handle file upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $upload_dir = 'uploads/';
        $file_name = basename($_FILES['media']['name']);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the server's upload directory
        if (move_uploaded_file($_FILES['media']['tmp_name'], $file_path)) {
            $media_path = $file_path;
        } else {
            echo "Error uploading the file.";
            exit();
        }
    }

    // Insert the post into the database
    $sql = "INSERT INTO posts (user_id, content, media_path, visibility) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$user_id, $content, $media_path, $visibility])) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error creating the post.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Create Post</title>
</head>
<body>
    <div class="container">
        <h1>Create Post</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            <input type="file" name="media">
            <select name="visibility">
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>
            <button type="submit">Post</button>
        </form>
    </div>
</body>
</html>
