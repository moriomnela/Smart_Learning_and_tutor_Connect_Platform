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

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
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

        // Clear the cart
        unset($_SESSION['cart']);

        // Message logic
        if ($enrolled_count > 0) {
            $msg = "Successfully enrolled in " . $enrolled_count . " course(s)!";
            if ($already_enrolled_count > 0) {
                $msg .= " (" . $already_enrolled_count . " course(s) were already in your account)";
            }
            $_SESSION['success'] = $msg;
        } else {
            $_SESSION['error'] = "All selected courses were already in your account!";
        }

        header("Location: ../student/my-courses.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Checkout failed. Please try again.";
        header("Location: ../cart.php");
        exit;
    }
} else {
    header("Location: ../cart.php");
    exit;
}
?>