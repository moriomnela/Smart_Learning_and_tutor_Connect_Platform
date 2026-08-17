<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in admin can access
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Student Deletion by Admin
if (isset($_GET['delete_id'])) {
    $student_id = intval($_GET['delete_id']);
    try {
        $del_stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        $del_stmt->execute([$student_id]);
        $_SESSION['success'] = "Student account deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete student account.";
    }
    header("Location: manage-students.php");
    exit;
}

// Fetch all students along with their total enrolled courses count
try {
    $stmt = $pdo->query("
        SELECT u.*, 
               (SELECT COUNT(*) FROM enrollments WHERE student_id = u.id) AS enrollment_count,
               (SELECT COUNT(*) FROM bookings WHERE student_id = u.id) AS booking_count
        FROM users u 
        WHERE u.role = 'student' 
        ORDER BY u.id DESC
    ");
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    $students = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Manage Students</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Admin Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px;height: 100vh;position: sticky;top: 0;overflow: auto;scrollbar-width: thin;scrollbar-color: transparent transparent;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chart-line me-2"></i> Overview</a></li>
            <li><a href="site-stat.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-chart-pie me-2"></i> Site Statistics</a></li>
            <li><a href="applications.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Teacher Applications</a></li>
            <li><a href="manage-teachers.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-tie me-2"></i> Manage Teachers</a></li>
            <li><a href="manage-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> Manage Courses</a></li>
            <li><a href="withdrawals.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="earning.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Admin Earnings</a></li>
            <li><a href="manage-students.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-user-graduate me-2"></i> Manage Students</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Write Blog</a></li>
            <li><a href="manage-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> Manage Blogs</a></li>
            <li><a href="contacts.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-envelope-open-text me-2"></i> Messages</a></li>
            <li><a href="profile.php" class="nav-link text-dark p-2 rounded"><i class="fa-solid fa-user-gear me-2"></i>Profile Settings</a></li>
            <li><a href="../tutor.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chalkboard-user me-2"></i> Browse Tutors</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>

            <li><a href="../courses.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-book-bookmark me-2"></i> Browse Courses</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>            
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Platform Students</h2>
            <p class="text-muted">Monitor and manage all registered student accounts on the platform.</p>
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

        <!-- Students Cards Grid -->
        <div class="row g-4">
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $stu): 
                    $avatar = $stu['avatar'] ?? 'default-avatar.png';
                    if ($avatar === 'default-avatar.png' || empty($avatar)) {
                        $avatar_url = '../assets/img/profiles/default-avatar.png';
                    } elseif (str_starts_with($avatar, 'assets/')) {
                        $avatar_url = '../' . $avatar;
                    } else {
                        $avatar_url = '../assets/img/profiles/' . $avatar;
                    }
                ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white d-flex flex-column justify-content-between">
                            <div>
                                <!-- Student Header with Avatar -->
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Student Avatar" class="rounded-circle object-fit-cover border shadow-sm" width="60" height="60" onerror="this.src='../assets/img/profiles/default-avatar.png';">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($stu['full_name']); ?></h5>
                                        <small class="text-muted d-block text-truncate" style="max-width: 180px;"><?php echo htmlspecialchars($stu['email']); ?></small>
                                    </div>
                                </div>

                                <!-- Student Activity Stats -->
                                <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-3 mb-3 small text-muted">
                                    <span><i class="fa-solid fa-book-open me-1 text-primary"></i> <strong><?php echo $stu['enrollment_count']; ?></strong> Courses</span>
                                    <span><i class="fa-solid fa-calendar-check me-1 text-warning"></i> <strong><?php echo $stu['booking_count']; ?></strong> Bookings</span>
                                </div>
                            </div>

                            <!-- Card Footer / Actions -->
                            <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 11px;">Joined: <?php echo date('M d, Y', strtotime($stu['created_at'] ?? 'now')); ?></small>
                                <a href="manage-students.php?delete_id=<?php echo $stu['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold px-3" onclick="return confirm('Are you sure you want to delete this student account?');" title="Delete Student">
                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <div class="mb-2"><i class="fa-solid fa-user-graduate fs-1 text-secondary opacity-50"></i></div>
                        <p class="text-muted mb-0">No registered students found on the platform.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>