<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$posts = json_decode(file_get_contents('data/posts.json'), true);
$users = json_decode(file_get_contents('data/users.json'), true);
$comments = json_decode(file_get_contents('data/comments.json'), true);
$post_id = $_GET['id'];

$current_post = null;
foreach ($posts as $post) {
    if ($post['id'] == $post_id) {
        $current_post = $post;
        break;
    }
}

if (!$current_post) die("Post not found");

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comments['comments'][] = [
        'post_id' => $post_id,
        'username' => $_SESSION['username'],
        'text' => $_POST['comment'],
        'time' => time()
    ];
    file_put_contents('data/comments.json', json_encode($comments, JSON_PRETTY_PRINT));

    // Add notification for comment
    $follows = json_decode(file_get_contents('data/follows.json'), true);
    $follows['notifications'][] = [
        'from_user' => $_SESSION['username'],
        'to_user' => $current_post['username'],
        'type' => 'comment',
        'post_id' => $post_id,
        'time' => time()
    ];
    file_put_contents('data/follows.json', json_encode($follows, JSON_PRETTY_PRINT));

    header("Location: view_post.php?id=" . $post_id);
    exit;
}

$post_comments = array_filter($comments['comments'], fn($c) => $c['post_id'] == $post_id);
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="card bg-dark text-white">
        <div class="d-flex align-items-center p-3">
            <img src="<?= htmlspecialchars($current_post['profile_pic'] ?? 'https://via.placeholder.com/40') ?>" 
                 class="rounded-circle me-2" style="width: 40px; height: 40px;">
            <a href="view_profile.php?username=<?= $current_post['username'] ?>" 
               class="text-white text-decoration-none">@<?= htmlspecialchars($current_post['username']) ?></a>
        </div>
        <img src="<?= htmlspecialchars($current_post['image']) ?>" class="card-img-top">
        <div class="card-body">
            <p class="text-white"><?= htmlspecialchars($current_post['caption']) ?></p>
            <div class="d-flex justify-content-between align-items-center">
                <form method="post" action="like.php">
                    <input type="hidden" name="post_id" value="<?= $current_post['id'] ?>">
                    <button class="btn btn-sm btn-outline-primary">❤️ <?= $current_post['likes'] ?></button>
                </form>
                <span class="text-muted"><?= count($post_comments) ?> comments</span>
            </div>
            
            <div class="mt-4">
                <form method="post">
                    <div class="input-group">
                        <input type="text" name="comment" class="form-control" placeholder="Add a comment...">
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </form>
                
                <div class="mt-3">
                    <?php if (empty($post_comments)): ?>
                        <p class="text-center text-muted">No comments yet. Be the first to comment!</p>
                    <?php else: ?>
                        <?php foreach ($post_comments as $comment): ?>
                            <div class="mb-2">
                                <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                                <?= htmlspecialchars($comment['text']) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
