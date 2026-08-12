<?php
session_start();
require_once '../config/db.php';

// Security check: only admin can approve
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['user_id'])) {
    $app_id = intval($_GET['id']);
    $user_id = intval($_GET['user_id']);

    try {
        $pdo->beginTransaction();

        // 1. Fetch application details
        $stmt = $pdo->prepare("SELECT * FROM tutor_applications WHERE id = ?");
        $stmt->execute([$app_id]);
        $app = $stmt->fetch();

        if ($app) {
            // 2. Update user role and profile details (Mapping tutor_applications 'experience' to users table 'bio')
            $updateUser = $pdo->prepare("UPDATE users SET role = 'tutor', headline = ?, hourly_rate = ?, location = ?, languages = ?, study_mode = ?, bio = ? WHERE id = ?");
            $updateUser->execute([
                $app['headline'] ?? '',
                $app['hourly_rate'] ?? 0,
                $app['location'] ?? '',
                $app['languages'] ?? '',
                $app['study_mode'] ?? '',
                $app['experience'] ?? '', // Ekhane application er experience ta user er bio/experience hishebe jacche
                $user_id
            ]);

            // 3. Update application status to approved
            $updateApp = $pdo->prepare("UPDATE tutor_applications SET status = 'approved' WHERE id = ?");
            $updateApp->execute([$app_id]);

            $pdo->commit();
            $_SESSION['success'] = "Teacher application approved successfully!";
        } else {
            $_SESSION['error'] = "Application not found.";
        }

        header("Location: ../admin/dashboard.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        // Temporary debug print to see if any other column gives error
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: ../admin/dashboard.php");
    exit;
}
?>