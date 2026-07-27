<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    // session create
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];

    // success message (flash message)
    $_SESSION['success'] = "Login Successful 🎉";

    // redirect dashboard
    header("Location: ../admin/dashboard.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid email or password";
    header("Location: ../login.php");
    exit();
}
