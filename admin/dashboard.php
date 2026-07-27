<?php
session_start();

// security check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>


<?php
if (isset($_SESSION['success'])) {
?>
    <div class="alert alert-success">
        <?php
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
    </div>
<?php
}
?>

