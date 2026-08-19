<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$enrolled_count = 0;
$already_enrolled_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    
    // Optional: Get billing info if needed for logs
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? 'bKash');

    if (empty($phone) || empty($city)) {
        $_SESSION['error'] = "Please fill in all required billing information fields.";
        header("Location: ../checkout.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        foreach ($_SESSION['cart'] as $course_id) {
            // Check if already enrolled
            $check = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
            $check->execute([$student_id, $course_id]);

            if ($check->rowCount() === 0) {
                $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $course_id]);
                $enrolled_count++;
            } else {
                $already_enrolled_count++;
            }
        }

        $pdo->commit();

        // Clear the cart after successful payment/enrollment
        unset($_SESSION['cart']);

        // Success message logic
        if ($enrolled_count > 0) {
            $msg = "Payment successful via " . htmlspecialchars($payment_method) . "! Successfully enrolled in " . $enrolled_count . " course(s).";
            if ($already_enrolled_count > 0) {
                $msg .= " (" . $already_enrolled_count . " course(s) were already in your account)";
            }
            $_SESSION['success'] = $msg;
        } else {
            $_SESSION['error'] = "All selected courses were already in your account!";
            header("Location: ../student/my-courses.php");
            exit;
        }

        header("Location: ../student/my-courses.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Checkout and payment processing failed. Please try again.";
        header("Location: ../checkout.php");
        exit;
    }
} else {
    header("Location: ../cart.php");
    exit;
}
?>