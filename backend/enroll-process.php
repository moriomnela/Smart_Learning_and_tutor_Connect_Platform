<?php
session_start();
require_once '../config/db.php';

// Security: Only logged-in students can enroll
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['course_id'])) {
    $student_id = $_SESSION['user_id'];
    $course_id = intval($_GET['course_id']);

    try {
        // Check if already enrolled
        $check = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
        $check->execute([$student_id, $course_id]);

        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "You are already enrolled in this course!";
        } else {
            // Insert enrollment
            $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
            $stmt->execute([$student_id, $course_id]);
            $_SESSION['success'] = "Successfully enrolled in the course!";
        }

        header("Location: ../student/my-courses.php");
        exit;

    } catch (PDOException $e) {
        error_log("Enrollment Error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to enroll. Please try again.";
        header("Location: ../courses.php");
        exit;
    }
} else {
    header("Location: ../courses.php");
    exit;
}
?>