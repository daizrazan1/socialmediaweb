<?php
session_start();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = json_decode(file_get_contents('data/users.json'), true);
    $username_exists = false;
    foreach ($users as $user) {
        if ($user['username'] === $_POST['username']) {
            $username_exists = true;
            break;
        }
    }

    if ($username_exists) {
        $errors[] = "Username already taken";
    } else {
        $users[] = [
            'username' => $_POST['username'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];
        file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));
        $_SESSION['username'] = $_POST['username'];
        header("Location: feed.php");
        exit;
    }
}
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h3 class="text-center">Register</h3>
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
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        <p>Already have an account? <a href="login.php" class="text-primary">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>