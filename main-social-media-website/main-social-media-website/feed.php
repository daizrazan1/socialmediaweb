<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$posts = json_decode(file_get_contents('data/posts.json'), true);
$users = json_decode(file_get_contents('data/users.json'), true);
$follows = json_decode(file_get_contents('data/follows.json'), true);
$comments = json_decode(file_get_contents('data/comments.json'), true);

$following = array_filter($follows['follows'], fn($f) => $f['follower'] === $_SESSION['username']);
$following_users = array_map(fn($f) => $f['following'], $following);
$following_users[] = $_SESSION['username'];

// Sort posts by timestamp in descending order (newest first)
usort($posts, function($a, $b) {
    $a_time = $a['timestamp'] ?? 0;
    $b_time = $b['timestamp'] ?? 0;
    return $b_time - $a_time;
});
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php foreach ($posts as $post): ?>
                <div class="card bg-dark text-white mb-4">
                    <div class="d-flex align-items-center p-3">
                        <?php
                        $post_user = array_filter($users, fn($u) => $u['username'] === $post['username']);
                        $post_user = reset($post_user);
                        $profile_pic = $post_user['profile_pic'] ?? null;
                        if (!$profile_pic) {
                            $initial = strtoupper(substr($post['username'], 0, 1));
                            echo "<div class='rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary' style='width: 40px; height: 40px;'>";
                            echo "<span class='text-white'>{$initial}</span>";
                            echo "</div>";
                        } else {
                            echo "<img src='" . htmlspecialchars($profile_pic) . "' class='rounded-circle me-2' style='width: 40px; height: 40px;'>";
                        }
                        ?> 
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <a href="view_profile.php?username=<?= $post['username'] ?>" 
                               class="text-white text-decoration-none">@<?= htmlspecialchars($post['username']) ?></a>
                            <?php if ($post['username'] !== $_SESSION['username']): ?>
                                <button onclick="followUser('<?= $post['username'] ?>')" 
                                        class="btn btn-sm <?= in_array($post['username'], $following_users) ? 'btn-success' : 'btn-primary' ?> follow-btn"
                                        data-username="<?= $post['username'] ?>">
                                    <?= in_array($post['username'], $following_users) ? '✓ Following' : 'Follow' ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="view_post.php?id=<?= $post['id'] ?>" class="text-decoration-none">
                        <img src="<?= htmlspecialchars($post['image']) ?>" class="card-img-top" style="aspect-ratio: 1;">
                    </a>
                    <div class="card-body">
                        <p class="text-white"><?= htmlspecialchars($post['caption']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
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
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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
