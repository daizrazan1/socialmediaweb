<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$users = json_decode(file_get_contents('data/users.json'), true);
$posts = json_decode(file_get_contents('data/posts.json'), true);
$follows = json_decode(file_get_contents('data/follows.json'), true);
$comments = json_decode(file_get_contents('data/comments.json'), true);

$current_user = null;
foreach ($users as $user) {
    if ($user['username'] === $_SESSION['username']) {
        $current_user = $user;
        break;
    }
}

$followers = array_filter($follows['follows'], fn($f) => $f['following'] === $_SESSION['username']);
$following = array_filter($follows['follows'], fn($f) => $f['follower'] === $_SESSION['username']);
$myPosts = array_filter($posts, fn($p) => $p['username'] === $_SESSION['username']);
?>
<?php include 'header.php'; ?>
<div class="container text-white mt-5">
    <div class="row">
        <div class="col-md-4 text-center">
            <img src="<?= htmlspecialchars($current_user['profile_pic'] ?? 'https://via.placeholder.com/150') ?>" 
                 class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
        </div>
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                <h2 class="me-4">@<?= htmlspecialchars($_SESSION['username']) ?></h2>
                <a href="followers.php" class="me-3 text-decoration-none text-white">
                    <strong><?= count($followers) ?></strong> followers
                </a>
                <a href="following.php" class="text-decoration-none text-white">
                    <strong><?= count($following) ?></strong> following
                </a>
            </div>
            <p><?= htmlspecialchars($current_user['bio'] ?? '') ?></p>
            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
    
    <h3 class="mt-5 mb-2">My Posts</h3>
    <div class="row g-0 mt-0">
        <?php foreach ($myPosts as $post): ?>
        <div class="col-md-8 mb-4">
            <div class="card bg-dark">
                <a href="view_post.php?id=<?= $post['id'] ?>" class="text-decoration-none">
                    <img src="<?= htmlspecialchars($post['image']) ?>" class="card-img-top" style="aspect-ratio: 1;">
                </a>
                <div class="card-body">
                    <p class="text-white"><?= htmlspecialchars($post['caption']) ?></p>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="d-flex gap-2">
                                <form method="post" action="like.php">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button class="btn btn-sm btn-outline-primary">❤️ <?= $post['likes'] ?></button>
                                </form>
                                <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    💬 <?= count(array_filter($comments['comments'], fn($c) => $c['post_id'] == $post['id'])) ?>
                                </a>
                            </div>
                        </div>
                        <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'footer.php'; ?>
