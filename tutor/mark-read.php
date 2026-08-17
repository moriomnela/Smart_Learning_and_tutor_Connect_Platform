<?php
session_start();
require_once '../config/db.php';

// Security Check: Ensure user is logged in
if (!isset($_SESSION['is_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

$notif_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$redirect_url = isset($_GET['url']) ? $_GET['url'] : 'dashboard.php';

if ($notif_id > 0) {
    // Mark specific notification as read in database for the logged-in tutor
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$notif_id, $_SESSION['user_id']]);
}

// Redirect back to the intended page
header("Location: " . $redirect_url);
exit;
?>