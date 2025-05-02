<?php
session_start();
session_destroy(); // Clear any existing session
header("Location: login.php");
exit;
?>
