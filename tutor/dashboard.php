<?php
session_start();

// security check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>


