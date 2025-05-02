<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$follows_data = json_decode(file_get_contents('data/follows.json'), true);
$notifications = array_filter($follows_data['notifications'], function($n) {
    return $n['to_user'] === $_SESSION['username'];
});

// Mark notifications as read
$follows_data['read_notifications'][$_SESSION['username']] = time();
file_put_contents('data/follows.json', json_encode($follows_data, JSON_PRETTY_PRINT));
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Notifications</h2>
            <?php foreach ($notifications as $notification): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($notification['from_user']); ?>
                <?php 
                    if ($notification['type'] === 'follow') {
                        echo ' followed you';
                    } else if ($notification['type'] === 'like') {
                        echo ' liked your post';
                    } else if ($notification['type'] === 'comment') {
                        echo ' commented on your post';
                    }
                ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
