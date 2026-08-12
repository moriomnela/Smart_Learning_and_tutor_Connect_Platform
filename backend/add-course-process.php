<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in tutors
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutor_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $price = trim($_POST['price']);
    $discount_price = !empty($_POST['discount_price']) ? trim($_POST['discount_price']) : NULL;
    $learning_outcomes = trim($_POST['learning_outcomes']);
    $description = trim($_POST['description']);
    
    // Image Handling
    $imageName = '';
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['course_image']['tmp_name'];
        $fileName = $_FILES['course_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '../assets/img/courses/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imageName = $newFileName;
            } else {
                $_SESSION['error'] = "Error moving the uploaded file.";
                header("Location: ../tutor/add-course.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed.";
            header("Location: ../tutor/add-course.php");
            exit;
        }
    }

    if (empty($title) || empty($subtitle) || empty($price) || empty($learning_outcomes) || empty($description) || empty($imageName)) {
        $_SESSION['error'] = "All required fields must be filled out.";
        header("Location: ../tutor/add-course.php");
        exit;
    }

    try {
        // Insert query matching all table columns
        $stmt = $pdo->prepare("INSERT INTO courses (tutor_id, title, subtitle, price, discount_price, description, learning_outcomes, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tutor_id, $title, $subtitle, $price, $discount_price, $description, $learning_outcomes, $imageName]);

        $_SESSION['success'] = "Course published successfully!";
        header("Location: ../tutor/add-course.php");
        exit;

    } catch (PDOException $e) {
        error_log("Add Course Error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to publish course. Please try again.";
        header("Location: ../tutor/add-course.php");
        exit;
    }
} else {
    header("Location: ../tutor/dashboard.php");
    exit;
}
?>