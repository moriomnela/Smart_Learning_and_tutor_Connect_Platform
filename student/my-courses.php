<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in students can access this page
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

$appStmt = $pdo->prepare("SELECT status FROM tutor_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$appStmt->execute([$_SESSION['user_id']]);
$latestApp = $appStmt->fetch();

try {
    // Fetch courses enrolled by this specific student along with tutor name
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name AS tutor_name, e.enrolled_at 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        JOIN users u ON c.tutor_id = u.id 
        WHERE e.student_id = ? 
        ORDER BY e.id DESC
    ");
    $stmt->execute([$student_id]);
    $enrolled_courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $enrolled_courses = [];
}

$page_title = "My Enrolled Courses";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Smart Learning & Tutor Connect Platform</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <!-- Magnific-Popup CSS -->
    <link rel="stylesheet" href="../assets/css/magnific-popup.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="../assets/css/swiper-bundle.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Student Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Student</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
            <li><a href="my-courses.php" class="nav-link active fw-bold text-primary bg-light p-2 rounded"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Tutor Bookings</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user me-2"></i> Profile Settings</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            <?php if ($currentUser['role'] === 'student'): ?>
                <?php if (!$latestApp): ?>
                    <li><a href="become-teacher.php" class="nav-link p-2 rounded text-success fw-bold"><i class="fa-solid fa-chalkboard-user me-2"></i> Become a Teacher</a></li>
                <?php elseif ($latestApp['status'] === 'pending'): ?>
                    <li><span class="nav-link p-2 rounded text-warning fw-bold"><i class="fa-solid fa-clock me-2"></i> Application Pending</span></li>
                <?php elseif ($latestApp['status'] === 'rejected'): ?>
                    <li><a href="become-teacher.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-rotate-right me-2"></i> Re-apply as Teacher</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><span class="nav-link p-2 rounded text-primary fw-bold"><i class="fa-solid fa-check-circle me-2"></i> Faculty Member</span></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">My Enrolled Courses</h2>
            <p class="text-muted">Access all the courses you have enrolled in and continue your learning.</p>
        </div>

        <!-- Flash Messages -->
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

        <div class="row g-4">
            <?php if (count($enrolled_courses) > 0): ?>
                <?php foreach ($enrolled_courses as $course): ?>
                    <?php 
                        // Calculate progress for each course dynamically
                        $c_id = $course['id'];
                        
                        // Total lessons for this course
                        $l_stmt = $pdo->prepare("SELECT COUNT(*) FROM course_lessons WHERE course_id = ?");
                        $l_stmt->execute([$c_id]);
                        $total_lessons = $l_stmt->fetchColumn();

                        // Completed lessons by this student for this course
                        $p_stmt = $pdo->prepare("SELECT COUNT(*) FROM lesson_progress WHERE student_id = ? AND course_id = ?");
                        $p_stmt->execute([$student_id, $c_id]);
                        $completed_lessons = $p_stmt->fetchColumn();

                        $progress_percent = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;
                    ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden">
                            <img src="../assets/img/courses/<?php echo htmlspecialchars($course['image']); ?>" alt="Course Thumbnail" class="card-img-top" style="height: 180px; object-fit: cover;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?php echo htmlspecialchars($course['subtitle']); ?></span>
                                    <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($course['title']); ?></h5>
                                    <p class="text-muted small mb-3">Instructor: <span class="fw-bold text-dark"><?php echo htmlspecialchars($course['tutor_name']); ?></span></p>
                                    
                                    <!-- Progress Bar Section Added Here -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between small mb-1 text-muted">
                                            <span>Progress</span>
                                            <span class="fw-bold text-dark"><?php echo $progress_percent; ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress_percent; ?>%;" aria-valuenow="<?php echo $progress_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <span class="text-muted small" style="font-size: 11px;"><i class="fa-solid fa-calendar-days me-1"></i> <?php echo date('M d, Y', strtotime($course['enrolled_at'])); ?></span>
                                    <a href="learn-course.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary btn-sm fw-bold px-3">Continue</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="bg-white p-5 rounded-4 text-center shadow-sm">
                        <h4 class="text-dark fw-bold mb-2">No Enrolled Courses Yet</h4>
                        <p class="text-muted mb-4">You haven't enrolled in any courses. Explore our course catalog and start learning today!</p>
                        <a href="../courses.php" class="btn btn-primary fw-bold px-4 py-2">Explore Courses</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- jQuery -->
<script src="../assets/js/jquery-3.6.0.min.js"></script>
<!-- Popper.js -->
<script src="../assets/js/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="../assets/js/bootstrap.min.js"></script>
<!-- Magnific-Popup JS -->
<script src="../assets/js/jquery.magnific-popup.min.js"></script>
<!-- Swiper JS -->
<script src="../assets/js/swiper-bundle.min.js"></script>
<!-- FontAwesome JS -->
<script src="../assets/js/fontawesome.min.js"></script>
<!-- Progressbar JS -->
<script src="../assets/js/progressbar.min.js"></script>
<!-- Custom JS -->
<script src="../assets/js/main.js"></script>
</body>
</html>