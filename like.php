
<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");
$posts = json_decode(file_get_contents('posts.json'), true);
foreach ($posts as &$post) {
    if ($post['id'] == $_POST['post_id']) {
        $post['likes']++;
        break;
    }
}
file_put_contents('posts.json', json_encode($posts, JSON_PRETTY_PRINT));
header("Location: profile.php");
