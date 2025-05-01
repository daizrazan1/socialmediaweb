
<?php
session_start();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = json_decode(file_get_contents('users.json'), true);
    foreach ($users as $user) {
        if ($user['username'] === $_POST['username'] && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: profile.php");
            exit;
        }
    }
    $errors[] = "Invalid username or password.";
}
?>
<form method="post">
  <input name="username" required placeholder="Username">
  <input name="password" type="password" required placeholder="Password">
  <button type="submit">Login</button>
</form>
