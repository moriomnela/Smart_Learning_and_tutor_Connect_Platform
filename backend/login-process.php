<?php
session_start();

// Include database connection
require_once '../config/db.php';

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize input data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: ../login.php");
        exit;
    }

    try {
        // 2. Fetch the user from the database based on email
        $stmt = $pdo->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 3. Verify if user exists and the password matches the hashed password
        if ($user && password_verify($password, $user['password'])) {
            
            // Generate a new session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['is_logged_in'] = true;

            // 4. Redirect user based on their role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($user['role'] === 'tutor') {
                header("Location: ../tutor/dashboard.php");
            } else {
                // Default fallback for students
                header("Location: ../student/dashboard.php");
            }
            exit;

        } else {
            // Invalid credentials
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: ../login.php");
            exit;
        }

    } catch (PDOException $e) {
        // Log error and show generic message
        error_log("Login Error: " . $e->getMessage());
        $_SESSION['error'] = "An unexpected error occurred. Please try again later.";
        header("Location: ../login.php");
        exit;
    }
} else {
    // Redirect to login if accessed directly
    header("Location: ../login.php");
    exit;
}
?>