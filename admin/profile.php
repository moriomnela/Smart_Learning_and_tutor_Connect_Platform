<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in admins
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle Admin Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $avatar_name = $_SESSION['avatar'] ?? 'default-avatar.png';

    // Handle Avatar Upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['avatar']['name']);
        $upload_dir = '../assets/img/profiles/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $avatar_name = $file_name;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, avatar = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $avatar_name, $admin_id]);

        // Update session variables instantly
        $_SESSION['full_name'] = $full_name;
        $_SESSION['avatar'] = $avatar_name;

        $_SESSION['success'] = "Admin profile updated successfully!";
        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update profile.";
    }
}

// Fetch admin details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

$page_title = "Admin Profile Settings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Admin Profile</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Admin Sidebar (Keep your admin sidebar links here) -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a></li>
            <li><a href="profile.php" class="nav-link active fw-bold text-primary bg-light p-2 rounded"><i class="fa-solid fa-user-gear me-2"></i> Admin Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Admin Profile Settings</h2>
            <p class="text-muted">Manage your administrator account credentials.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 p-5 bg-white col-lg-7">
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                
                <!-- Avatar Preview -->
                <div class="d-flex align-items-center gap-4 mb-4 pb-3 border-bottom">
                    <?php 
                        $avatar = $admin['avatar'] ?? 'default-avatar.png';
                        $avatar_url = ($avatar === 'default-avatar.png' || empty($avatar)) ? '../assets/img/profiles/default-avatar.png' : '../assets/img/profiles/' . $avatar;
                    ?>
                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" class="rounded-circle object-fit-cover shadow-sm border" width="90" height="90" onerror="this.src='../assets/img/profiles/default-avatar.png'">
                    <div>
                        <label class="form-label fw-bold">Profile Picture</label>
                        <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="full_name" class="form-control py-2" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control py-2" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>

                <button type="submit" name="update_admin" class="btn btn-primary px-5 py-2 fw-bold">Update Profile</button>
            </form>
        </div>

    </div>
</div>

<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>