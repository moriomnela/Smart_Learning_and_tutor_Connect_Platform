<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutor_id = $_SESSION['user_id'];
    $requested_amount = floatval($_POST['amount']); // Student er select kora amount
    $method = trim($_POST['method']);
    // 2. Insert record
    $stmt = $pdo->prepare("INSERT INTO withdrawals (tutor_id, amount, method, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$tutor_id, $requested_amount, $method]);

    $_SESSION['success'] = "Withdrawal request of ৳" . $requested_amount . " submitted!";
    header("Location: ../tutor/earnings.php");
    exit;
}
?>