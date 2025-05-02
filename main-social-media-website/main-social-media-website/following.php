<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

$follows = json_decode(file_get_contents('data/follows.json'), true);
$viewing_username = $_GET['username'] ?? $_SESSION['username'];
$following = array_filter($follows['follows'], fn($f) => $f['follower'] === $viewing_username);
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-white mb-4">Following</h2>
            <?php foreach ($following as $follow): ?>
                <div class="card bg-dark text-white mb-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <a href="view_profile.php?username=<?= $follow['following'] ?>" 
                           class="text-white text-decoration-none">
                            @<?= htmlspecialchars($follow['following']) ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
