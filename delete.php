
<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");
$posts = json_decode(file_get_contents('posts.json'), true);
$posts = array_filter($posts, fn($p) => $p['id'] != $_GET['id'] || $p['username'] != $_SESSION['username']);
file_put_contents('posts.json', json_encode(array_values($posts), JSON_PRETTY_PRINT));
header("Location: profile.php");
