<?php
session_start();
require_once '../config/db.php';

// Security check: only admins can access this
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $app_id = $_GET['id'];

    try {
        // Update application status to rejected
        $stmt = $pdo->prepare("UPDATE tutor_applications SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$app_id]);

        $_SESSION['success'] = "Tutor application rejected successfully.";
        header("Location: ../admin/dashboard.php");
        exit;

    } catch (PDOException $e) {
        error_log("Rejection Error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to reject application.";
        header("Location: ../admin/dashboard.php");
        exit;
    }
} else {
    header("Location: ../admin/dashboard.php");
    exit;
}
?>