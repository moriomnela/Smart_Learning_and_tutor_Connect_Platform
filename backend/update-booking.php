<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    die("Unauthorized");
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $booking_id = intval($_GET['id']);
    $new_status = ($_GET['status'] === 'approved') ? 'approved' : 'rejected';
    $tutor_id = $_SESSION['user_id'];

    // Update status only if the booking belongs to this tutor
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ? AND tutor_id = ?");
    $stmt->execute([$new_status, $booking_id, $tutor_id]);

    $_SESSION['success'] = "Booking status updated to " . $new_status;
    header("Location: ../tutor/bookings.php");
    exit;
}
?>