<?php
session_start();
require_once '../config/db.php';

// Security check: only admins
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['user_id'])) {
    $app_id = $_GET['id'];
    $user_id = $_GET['user_id'];

    try {
        // Begin transaction to ensure both updates happen together
        $pdo->beginTransaction();

        // 1. Update application status to approved
        $updateApp = $pdo->prepare("UPDATE tutor_applications SET status = 'approved' WHERE id = ?");
        $updateApp->execute([$app_id]);

        // 2. Update user role to tutor in users table
        $updateUser = $pdo->prepare("UPDATE users SET role = 'tutor' WHERE id = ?");
        $updateUser->execute([$user_id]);

        $pdo->commit();

        $_SESSION['success'] = "Tutor application approved successfully!";
        header("Location: ../admin/applications.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Approval Error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to approve application.";
        header("Location: ../admin/applications.php");
        exit;
    }
} else {
    header("Location: ../admin/dashboard.php");
    exit;
}
?>