
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = json_decode(file_get_contents('users.json'), true);
    foreach ($users as $user) {
        if ($user['username'] === $_POST['username']) {
            die("Username taken.");
        }
    }
    $users[] = [
        "username" => $_POST['username'],
        "password" => password_hash($_POST['password'], PASSWORD_DEFAULT)
    ];
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    $_SESSION['username'] = $_POST['username'];
    header("Location: profile.php");
    exit;
}
?>
<form method="post">
  <input name="username" required placeholder="Username">
  <input name="password" type="password" required placeholder="Password">
  <button type="submit">Register</button>
</form>
