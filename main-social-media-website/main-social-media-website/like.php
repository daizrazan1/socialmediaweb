<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$posts = json_decode(file_get_contents('data/posts.json'), true);
$follows = json_decode(file_get_contents('data/follows.json'), true);

foreach ($posts as &$post) {
    if ($post['id'] == $_POST['post_id']) {
        $post['likes']++;
        
        // Add notification
        $follows['notifications'][] = [
            'from_user' => $_SESSION['username'],
            'to_user' => $post['username'],
            'type' => 'like',
            'post_id' => $post['id'],
            'time' => time()
        ];
        break;
    }
}

file_put_contents('data/posts.json', json_encode($posts, JSON_PRETTY_PRINT));
file_put_contents('data/follows.json', json_encode($follows, JSON_PRETTY_PRINT));

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
