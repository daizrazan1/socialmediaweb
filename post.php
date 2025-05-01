
<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posts = json_decode(file_get_contents('posts.json'), true);
    $id = time();
    $imgPath = "uploads/$id.jpg";
    move_uploaded_file($_FILES['image']['tmp_name'], $imgPath);
    $posts[] = [
        "id" => $id,
        "username" => $_SESSION['username'],
        "image" => $imgPath,
        "caption" => $_POST['caption'],
        "likes" => 0
    ];
    file_put_contents('posts.json', json_encode($posts, JSON_PRETTY_PRINT));
    header("Location: profile.php");
    exit;
}
?>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="image" required>
  <textarea name="caption" placeholder="Caption" required></textarea>
  <button type="submit">Post</button>
</form>
