
<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");
$posts = json_decode(file_get_contents('posts.json'), true);
$myPosts = array_filter($posts, fn($p) => $p['username'] == $_SESSION['username']);
?>
<?php include 'header.php'; ?>
<div class="container text-white mt-5">
  <h2>@<?= $_SESSION['username'] ?></h2>
  <p>والله خير الماكرين</p>
  <div class="row">
    <?php foreach ($myPosts as $post): ?>
    <div class="col-md-4">
      <div class="card mb-3">
        <img src="<?= $post['image'] ?>" class="card-img-top">
        <div class="card-body">
          <p><?= htmlspecialchars($post['caption']) ?></p>
          <form method="post" action="like.php">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <button class="btn btn-sm btn-outline-primary" type="submit">❤️ <?= $post['likes'] ?></button>
          </form>
          <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include 'footer.php'; ?>
