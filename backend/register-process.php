<?php
require_once __DIR__ . "/../config/db.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
$name = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password != $confirm) {
die("Password not matched");
}

$check = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $check);

if (mysqli_num_rows($result) > 0) {
die("Email already exists");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users(full_name,email,password)
VALUES('$name','$email','$hashed')";

if (mysqli_query($conn, $sql)) {
header("Location: ../login.php");
exit();
} else {
die("Insert Failed: " . mysqli_error($conn));
}
}