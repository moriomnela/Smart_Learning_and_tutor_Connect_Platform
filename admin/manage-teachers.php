<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in admin can access
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Teacher Deletion by Admin
if (isset($_GET['delete_id'])) {
    $teacher_id = intval($_GET['delete_id']);
    try {
        $del_stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'tutor'");
        $del_stmt->execute([$teacher_id]);
        $_SESSION['success'] = "Teacher account deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete teacher account.";
    }
    header("Location: manage-teachers.php");
    exit;
}

// Fetch all tutors/teachers along with their published courses count and total students
try {
    $stmt = $pdo->query("
        SELECT u.*, 
               (SELECT COUNT(*) FROM courses WHERE tutor_id = u.id) AS course_count,
               (SELECT COUNT(DISTINCT student_id) FROM bookings WHERE tutor_id = u.id) AS student_count
        FROM users u 
        WHERE u.role = 'tutor' 
        ORDER BY u.id DESC
    ");
    $teachers = $stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Manage Teachers</title>
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
            <li><a href="manage-teachers.php" class="nav-link p-2 active rounded fw-bold text-primary bg-light"><i class="fa-solid fa-user-tie me-2"></i> Manage Teachers</a></li>
            <li><a href="manage-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> Manage Courses</a></li>
            <li><a href="withdrawals.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="earning.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Admin Earnings</a></li>
            <li><a href="manage-students.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-graduate me-2"></i> Manage Students</a></li>
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
            <h2 class="fw-bold text-dark">Platform Teachers</h2>
            <p class="text-muted">Monitor and manage all registered instructors and tutors on the platform.</p>
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

        <!-- Teachers Cards Grid -->
        <div class="row g-4">
            <?php if (count($teachers) > 0): ?>
                <?php foreach ($teachers as $tch): 
                    $avatar = $tch['avatar'] ?? 'default-avatar.png';
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
                                <!-- Teacher Header with Avatar -->
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Teacher Avatar" class="rounded-circle object-fit-cover border shadow-sm" width="60" height="60" onerror="this.src='../assets/img/profiles/default-avatar.png';">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($tch['full_name']); ?></h5>
                                        <small class="text-muted d-block text-truncate" style="max-width: 180px;"><?php echo htmlspecialchars($tch['email']); ?></small>
                                    </div>
                                </div>

                                <h3 class="text-muted small mb-3"><?php echo htmlspecialchars($tch['headline'] ?? 'Expert Instructor & Mentor'); ?></h3>

                                <!-- Teacher Activity Stats -->
                                <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-3 mb-3 small text-muted">
                                    <span><i class="fa-solid fa-book me-1 text-primary"></i> <strong><?php echo $tch['course_count']; ?></strong> Courses</span>
                                    <span><i class="fa-solid fa-users me-1 text-success"></i> <strong><?php echo $tch['student_count']; ?></strong> Students</span>
                                </div>
                            </div>

                            <!-- Card Footer / Actions -->
                            <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                <a href="../tutor-details.php?id=<?php echo $tch['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Profile
                                </a>
                                <a href="manage-teachers.php?delete_id=<?php echo $tch['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold px-3" onclick="return confirm('Are you sure you want to delete this teacher account?');" title="Delete Teacher">
                                    <i class="fa-solid fa-trash me-2"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <div class="mb-2"><i class="fa-solid fa-chalkboard-user fs-1 text-secondary opacity-50"></i></div>
                        <p class="text-muted mb-0">No registered teachers found on the platform.</p>
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