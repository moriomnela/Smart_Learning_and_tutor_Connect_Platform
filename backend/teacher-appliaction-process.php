<?php
session_start();
require_once '../config/db.php';

// Security check: only logged-in students can apply
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $expertise = trim($_POST['expertise']);
    $experience = trim($_POST['experience']);
    $qualification = trim($_POST['qualification']);

    if (empty($expertise) || empty($experience) || empty($qualification)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../student/become-teacher.php");
        exit;
    }

    try {
        // Check if the user has already submitted a pending application
        $checkStmt = $pdo->prepare("SELECT id FROM tutor_applications WHERE user_id = ? AND status = 'pending'");
        $checkStmt->execute([$user_id]);

        if ($checkStmt->rowCount() > 0) {
            $_SESSION['error'] = "You already have a pending application.";
            header("Location: ../student/become-teacher.php");
            exit;
        }

        // Insert new application
        $stmt = $pdo->prepare("INSERT INTO tutor_applications (user_id, expertise, experience, qualification) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $expertise, $experience, $qualification]);

        $_SESSION['success'] = "Application submitted successfully! Please wait for admin approval.";
        header("Location: ../student/become-teacher.php");
        exit;

    } catch (PDOException $e) {
        error_log("Teacher Application Error: " . $e->getMessage());
        $_SESSION['error'] = "An unexpected error occurred. Please try again.";
        header("Location: ../student/become-teacher.php");
        exit;
    }
} else {
    header("Location: ../student/become-teacher.php");
    exit;
}
?>