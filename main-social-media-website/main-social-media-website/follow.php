<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$follows = json_decode(file_get_contents('data/follows.json'), true);
$follow_data = ['follower' => $_SESSION['username'], 'following' => $_POST['username']];

$existing = array_search($follow_data, $follows['follows']);
if ($existing !== false) {
    array_splice($follows['follows'], $existing, 1);
} else {
    $follows['follows'][] = $follow_data;
    $follows['notifications'][] = [
        'from_user' => $_SESSION['username'],
        'to_user' => $_POST['username'],
        'type' => 'follow',
        'time' => time()
    ];
}

file_put_contents('data/follows.json', json_encode($follows, JSON_PRETTY_PRINT));
echo "Success";
?>
