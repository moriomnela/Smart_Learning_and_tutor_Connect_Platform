<?php
session_start();
require_once '../config/db.php';

// Security: Only students can buy/enroll
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $course_id = intval($_GET['id']);

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add course to cart if not already present in cart
    if (!in_array($course_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $course_id;
    }

    // Redirect directly to checkout page
    header("Location: ../checkout.php");
    exit;
} else {
    header("Location: ../courses.php");
    exit;
}
?>