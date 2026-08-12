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
    $headline = trim($_POST['headline']);
    $hourly_rate = floatval($_POST['hourly_rate']);
    $location = trim($_POST['location']);
    $languages = trim($_POST['languages']);
    $study_mode = trim($_POST['study_mode']);
    $qualification = trim($_POST['qualification']);
    $experience = trim($_POST['experience']);

    // Check if any required field is empty
    if (empty($expertise) || empty($headline) || empty($hourly_rate) || empty($location) || empty($languages) || empty($study_mode) || empty($qualification) || empty($experience)) {
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

        // Insert new application with all fields
        $stmt = $pdo->prepare("INSERT INTO tutor_applications (user_id, expertise, headline, hourly_rate, location, languages, study_mode, qualification, experience, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $expertise, $headline, $hourly_rate, $location, $languages, $study_mode, $qualification, $experience]);

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
    header("Location: ../student/dashboard.php");
    exit;
}
?>