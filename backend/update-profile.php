<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $user_id = $_SESSION['user_id'];
    
    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileName = $_FILES['avatar']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = 'avatar_' . $user_id . '.' . $fileExtension;
    $uploadFileDir = '../assets/img/avatars/';
    
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0755, true);
    }

    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([$newFileName, $user_id]);
        $_SESSION['success'] = "Avatar updated!";
    }
    header("Location: ../" . $_SESSION['role'] . "/profile.php");
}
?>