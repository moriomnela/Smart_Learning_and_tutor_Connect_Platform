<?php
session_start();

// Include the database connection file
require_once '../config/db.php';

// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize input data
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if the terms checkbox was checked
    $terms = isset($_POST['terms']) ? true : false;

    // 1. Basic empty field validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../register.php");
        exit;
    }

    // 2. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../register.php");
        exit;
    }

    // 3. Verify that passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../register.php");
        exit;
    }

    // 4. Verify terms agreement
    if (!$terms) {
        $_SESSION['error'] = "You must agree to the Terms of Service.";
        header("Location: ../register.php");
        exit;
    }

    try {
        // 5. Check if the email already exists in the database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "This email is already registered. Please login.";
            header("Location: ../register.php");
            exit;
        }

        // 6. Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 7. Insert the new user into the database
        // Note: Defaulting the role to 'student' since the form doesn't specify it
        $role = 'student'; 
        
        $insertStmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$fullname, $email, $hashed_password, $role]);

        // register successful
        $_SESSION['success'] = "Account created successfully! You can now log in.";
        header("Location: ../login.php");
        exit;

    } catch (PDOException $e) {
        // Log the error internally and show a safe error message to the user
        error_log("register Error: " . $e->getMessage());
        $_SESSION['error'] = "An unexpected error occurred. Please try again later.";
        header("Location: ../register.php");
        exit;
    }
} else {
    // Redirect to the register page if accessed directly without form submission
    header("Location: ../register.php");
    exit;
}
?>