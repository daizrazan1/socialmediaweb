<!DOCTYPE html>
<html lang="en">
<head>
  <title>Instagram Clone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<nav class="navbar navbar-dark bg-black px-3">
  <div class="container">
    <?php if (isset($_SESSION['username'])): ?>
    <a class="navbar-brand" href="feed.php">Home</a>
    <div class="nav-center">
      <a href="feed.php" class="btn btn-outline-light btn-sm mx-1">Feed</a>
      <a href="profile.php" class="btn btn-outline-light btn-sm mx-1">Profile</a>
      <a href="post.php" class="btn btn-outline-light btn-sm mx-1">New Post</a>
      <?php
      $follows_data = json_decode(file_get_contents('data/follows.json'), true);
      $last_read = $follows_data['read_notifications'][$_SESSION['username']] ?? 0;
      $unread_count = count(array_filter($follows_data['notifications'], 
          fn($n) => $n['to_user'] === $_SESSION['username'] && $n['time'] > $last_read
      ));
      ?>
      <a href="notifications.php" class="btn btn-outline-light btn-sm mx-1 position-relative">
        Notifications
        <?php if ($unread_count > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $unread_count ?>
          </span>
        <?php endif; ?>
      </a>
    </div>
    <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    <?php endif; ?>
  </div>
</nav>
