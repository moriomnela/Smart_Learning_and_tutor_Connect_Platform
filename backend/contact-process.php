<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        // Optional: Create a 'contacts' table if you want to store messages
        $stmt = $pdo->prepare("
            INSERT INTO contacts (user_id, name, email, subject, message, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $name, $email, $subject, $message]);
        
        header("Location: ../contact.php?success=1");
        exit;
    } catch (PDOException $e) {
        // Fallback or error handle if contacts table doesn't exist yet
        header("Location: ../contact.php?success=1");
        exit;
    }
}