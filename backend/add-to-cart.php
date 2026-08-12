<?php
session_start();
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    if (!in_array($course_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $course_id;
    }
}
header("Location: ../cart.php");