<?php
session_start();
if (!isset($_SESSION['username'])) die("Login required.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = json_decode(file_get_contents('data/users.json'), true);
    $username_exists = false;
    if (!empty($_POST['new_username']) && $_POST['new_username'] !== $_SESSION['username']) {
        foreach ($users as $existingUser) {
            if ($existingUser['username'] === $_POST['new_username']) {
                $username_exists = true;
                break;
            }
        }
    }
    
    if ($username_exists) {
        $error = "Username already taken";
    } else {
        foreach ($users as &$user) {
            if ($user['username'] === $_SESSION['username']) {
                if (!empty($_POST['new_username'])) {
                $old_username = $_SESSION['username'];
                $user['username'] = $_POST['new_username'];
                $_SESSION['username'] = $_POST['new_username'];
                
                // Update posts
                $posts = json_decode(file_get_contents('data/posts.json'), true);
                foreach ($posts as &$post) {
                    if ($post['username'] === $old_username) {
                        $post['username'] = $_POST['new_username'];
                    }
                }
                file_put_contents('data/posts.json', json_encode($posts, JSON_PRETTY_PRINT));
            }
            $user['bio'] = $_POST['bio'] ?? '';
            $user['profile_pic'] = $_POST['profile_pic'] ?? '';
            break;
        }
    }
    }
    file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));
    header("Location: profile.php");
    exit;
}

$users = json_decode(file_get_contents('data/users.json'), true);
$current_user = null;
foreach ($users as $user) {
    if ($user['username'] === $_SESSION['username']) {
        $current_user = $user;
        break;
    }
}
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h3 class="text-center">Edit Profile</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input name="new_username" class="form-control" value="<?= htmlspecialchars($current_user['username']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($current_user['bio'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Picture URL</label>
                            <input name="profile_pic" class="form-control" value="<?= htmlspecialchars($current_user['profile_pic'] ?? '') ?>">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
