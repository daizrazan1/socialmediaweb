<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$users = json_decode(file_get_contents('data/users.json'), true);
$posts = json_decode(file_get_contents('data/posts.json'), true);
$follows = json_decode(file_get_contents('data/follows.json'), true);
$comments = json_decode(file_get_contents('data/comments.json'), true);

$viewing_username = $_GET['username'];
$current_user = null;
$is_own_profile = $viewing_username === $_SESSION['username'];

foreach ($users as $user) {
    if ($user['username'] === $viewing_username) {
        $current_user = $user;
        break;
    }
}

if (!$current_user) die("User not found");

$userPosts = array_filter($posts, fn($p) => $p['username'] === $viewing_username && (!($p['is_private'] ?? false) || $is_own_profile));
$followers = array_filter($follows['follows'], fn($f) => $f['following'] === $viewing_username);
$following = array_filter($follows['follows'], fn($f) => $f['follower'] === $viewing_username);
$is_following = in_array(['follower' => $_SESSION['username'], 'following' => $viewing_username], $follows['follows']);
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 text-center">
            <img src="<?= htmlspecialchars($current_user['profile_pic'] ?? 'https://via.placeholder.com/150') ?>" 
                 class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
        </div>
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                <h2>@<?= htmlspecialchars($viewing_username) ?></h2>
                <div class="ms-4">
                    <span class="me-3"><strong><?= count($userPosts) ?></strong> posts</span>
                    <a href="followers.php?username=<?= $viewing_username ?>" class="me-3 text-decoration-none text-white">
                        <strong><?= count($followers) ?></strong> followers
                    </a>
                    <a href="following.php?username=<?= $viewing_username ?>" class="text-decoration-none text-white">
                        <strong><?= count($following) ?></strong> following
                    </a>
                </div>
            </div>
            <p><?= htmlspecialchars($current_user['bio'] ?? '') ?></p>
            <?php if ($is_own_profile): ?>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
            <?php else: ?>
                <button onclick="followUser('<?= $viewing_username ?>')" 
                        class="btn <?= $is_following ? 'btn-success' : 'btn-primary' ?> follow-btn"
                        data-username="<?= $viewing_username ?>">
                    <?= $is_following ? '✓ Following' : 'Follow' ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <h3 class="mt-5 mb-2">Posts</h3>
    <div class="row g-0 mt-0">
        <?php foreach ($userPosts as $post): ?>
        <div class="col-md-8 mb-4">
            <div class="card bg-dark text-white">
                <img src="<?= htmlspecialchars($post['image']) ?>" class="card-img-top" style="aspect-ratio: 1;">
                <div class="card-body">
                    <p class="text-white"><?= htmlspecialchars($post['caption']) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3">
                            <form method="post" action="like.php">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <button class="btn btn-sm btn-outline-primary">❤️ <?= $post['likes'] ?></button>
                            </form>
                            <a href="view_post.php?id=<?= $post['id'] ?>" class="text-decoration-none">
                                <span class="text-muted">
                                    <?= count(array_filter($comments['comments'], fn($c) => $c['post_id'] == $post['id'])) ?> comments
                                </span>
                            </a>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <?php
                            $timestamp = $post['timestamp'] ?? 0;
                            if ($timestamp === 0) {
                                echo '<span class="text-muted">Beta</span>';
                            } else {
                                $diff = time() - $timestamp;
                                if ($diff < 60) echo '<span class="text-muted">Just now</span>';
                                else if ($diff < 3600) echo '<span class="text-muted">' . floor($diff/60) . ' minutes ago</span>';
                                else if ($diff < 86400) echo '<span class="text-muted">' . floor($diff/3600) . ' hours ago</span>';
                                else if ($diff < 604800) echo '<span class="text-muted">' . floor($diff/86400) . ' days ago</span>';
                                else if ($diff < 2592000) echo '<span class="text-muted">' . floor($diff/604800) . ' weeks ago</span>';
                                else if ($diff < 31536000) echo '<span class="text-muted">' . floor($diff/2592000) . ' months ago</span>';
                                else echo '<span class="text-muted">' . floor($diff/31536000) . ' years ago</span>';
                            }
                            ?>
                            <?php if ($is_own_profile): ?>
                                <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function followUser(username) {
    const btn = document.querySelector(`.follow-btn[data-username="${username}"]`);
    fetch('follow.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `username=${encodeURIComponent(username)}`
    }).then(() => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        btn.innerHTML = '✓ Following';
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });
}
</script>
<?php include 'footer.php'; ?>
