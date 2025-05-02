<?php
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = json_decode(file_get_contents('data/users.json'), true);
    $found = false;
    foreach ($users as $user) {
        if ($user['username'] === $_POST['username']) {
            $found = true;
            if (password_verify($_POST['password'], $user['password'])) {
                $_SESSION['username'] = $user['username'];
                header("Location: feed.php");
                exit;
            }
            break;
        }
    }
    if (!$found) {
        $errors[] = "Username not found.";
    } else {
        $errors[] = "Incorrect password.";
    }
}
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h3 class="text-center">Login</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php echo implode('<br>', $errors); ?>
                        </div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <input name="username" class="form-control" required placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <input name="password" type="password" class="form-control" required placeholder="Password">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        <p>Don't have an account? <a href="register.php" class="text-primary">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>