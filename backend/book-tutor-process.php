<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $student_id = $_SESSION['user_id'];
    $tutor_id = intval($_POST['tutor_id']);
    $subject = trim($_POST['subject']);
    $booking_date = $_POST['booking_date'];
    $time_slot = $_POST['time_slot'] ?? '4:00 PM';
    $class_mode = $_POST['class_mode'] ?? 'Online';
    $message = trim($_POST['message'] ?? '');

    try {
        // 1. Insert Booking Request into database
        $stmt = $pdo->prepare("INSERT INTO bookings (student_id, tutor_id, subject, booking_date, time_slot, class_mode, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$student_id, $tutor_id, $subject, $booking_date, $time_slot, $class_mode, $message]);

        // 2. Insert Notification for Student
        $stmt_student = $pdo->prepare("INSERT INTO notifications (user_id, title, link, is_read) VALUES (?, ?, ?, 0)");
        $stmt_student->execute([$student_id, "Booking request for " . $subject . " is pending.", "bookings.php"]);

        // 3. Insert Notification for Tutor
        $stmt_tutor = $pdo->prepare("INSERT INTO notifications (user_id, title, link, is_read) VALUES (?, ?, ?, 0)");
        $stmt_tutor->execute([$tutor_id, "New booking request from a student!", "bookings.php"]);

        $_SESSION['success'] = "Booking request submitted successfully!";
        // Correct path: from backend folder, go to root (../) then to student/bookings.php
        header("Location: ../student/bookings.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../tutor-details.php?id=" . $tutor_id);
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>