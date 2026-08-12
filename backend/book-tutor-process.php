<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized Access");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['user_id'];
    $tutor_id = intval($_POST['tutor_id']);
    $subject = trim($_POST['subject']);
    $date = trim($_POST['booking_date']);
    $time_slot = $_POST['time_slot']; // New
    $class_mode = $_POST['class_mode']; // New
    $message = trim($_POST['message']);

    // Updated SQL query
    $stmt = $pdo->prepare("INSERT INTO bookings (student_id, tutor_id, subject, booking_date, time_slot, class_mode, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$student_id, $tutor_id, $subject, $date, $time_slot, $class_mode, $message]);

    $_SESSION['success'] = "Booking request sent!";
    header("Location: ../student/bookings.php");
    exit;
}
?>