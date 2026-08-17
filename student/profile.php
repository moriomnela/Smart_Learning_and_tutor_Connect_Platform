<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch User Data Safely ($currentUser variable used consistently)
$currentUser = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $currentUser = $stmt->fetch() ?: [];
} catch (PDOException $e) {
    $currentUser = [];
}

// Check latest tutor application status for sidebar
$appStmt = $pdo->prepare("SELECT status FROM tutor_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$appStmt->execute([$user_id]);
$latestApp = $appStmt->fetch();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $avatar = $currentUser['avatar'] ?? 'default-avatar.png';

    // Avatar Upload Handling
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['avatar']['name']);
        $upload_dir = '../assets/img/profiles/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $avatar = 'assets/img/profiles/' . $file_name;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, avatar = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $avatar, $user_id]);
        
        $_SESSION['full_name'] = $full_name;
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to update profile: " . $e->getMessage();
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];

    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_pass, $user['password'])) {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_pass = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_pass->execute([$hashed_pass, $user_id]);
            
            $_SESSION['success'] = "Password changed successfully!";
            header("Location: profile.php");
            exit;
        } else {
            $pass_error = "Current password is incorrect.";
        }
    } catch (PDOException $e) {
        $pass_error = "Something went wrong.";
    }
}

$page_title = "Profile Settings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Profile Settings</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; height: 100vh; position: sticky; top: 0;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Student</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Tutor Bookings</a></li>
            <li><a href="profile.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-user-gear me-2"></i> Profile Settings</a></li>
            <li><a href="../tutor.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chalkboard-user me-2"></i> Browse Tutors</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>

            <li><a href="../courses.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-book-bookmark me-2"></i> Browse Courses</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>
            
            <?php if (isset($currentUser['role']) && $currentUser['role'] === 'student'): ?>
                <?php if (!$latestApp): ?>
                    <li class="mt-4"><a href="become-teacher.php" class="nav-link p-2 rounded text-success fw-bold"><i class="fa-solid fa-chalkboard-user me-2"></i> Become a Teacher</a></li>
                <?php elseif ($latestApp['status'] === 'pending'): ?>
                    <li><span class="nav-link p-2 rounded text-warning fw-bold"><i class="fa-solid fa-clock me-2"></i> Application Pending</span></li>
                <?php elseif ($latestApp['status'] === 'rejected'): ?>
                    <li><a href="become-teacher.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-rotate-right me-2"></i> Re-apply as Teacher</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><span class="nav-link p-2 rounded text-primary fw-bold"><i class="fa-solid fa-check-circle me-2"></i> Faculty Member</span></li>
            <?php endif; ?>
            
            <li><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Profile Settings</h2>
            <p class="text-muted">Manage your personal information, avatar, account credentials, and preferences.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger rounded-3 mb-4 fw-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Update Profile Info Card -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold mb-4">Personal Information</h4>
                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        
                        <!-- Avatar Preview -->
                        <div class="mb-4 d-flex align-items-center gap-3">
                            <div style="width: 70px; height: 70px; border-radius: 50%; overflow: hidden; background: #e9ecef;" class="d-flex align-items-center justify-content-center border">
                                <?php if (!empty($currentUser['avatar']) && $currentUser['avatar'] !== 'default-avatar.png'): ?>
                                    <img src="../<?php echo htmlspecialchars($currentUser['avatar']); ?>" alt="Avatar" class="w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <img src="../assets/img/profiles/default-avatar.png" alt="Default Avatar" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none'">
                                    <i class="fa-solid fa-user fs-3 text-secondary position-absolute"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="form-label fw-bold mb-1">Profile Avatar</label>
                                <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control py-2" value="<?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control py-2" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                        </div>

                        <div class="mt-4">
                            <button type="submit" name="update_profile" class="btn btn-primary px-4 py-2 fw-bold rounded-3">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold mb-4">Security Settings</h4>
                    
                    <?php if (isset($pass_error)): ?>
                        <div class="alert alert-danger rounded-3 mb-3 small"><?php echo $pass_error; ?></div>
                    <?php endif; ?>

                    <form action="profile.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Password</label>
                            <input type="password" name="current_password" class="form-control py-2" required placeholder="Enter current password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control py-2" required placeholder="Enter new password">
                        </div>
                        <div class="mt-4">
                            <button type="submit" name="change_password" class="btn btn-dark px-4 py-2 fw-bold rounded-3">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>