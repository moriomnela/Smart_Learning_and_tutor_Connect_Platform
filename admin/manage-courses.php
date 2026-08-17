<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in admin can access
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Course Deletion by Admin
if (isset($_GET['delete_id'])) {
    $course_id = intval($_GET['delete_id']);
    try {
        $del_stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $del_stmt->execute([$course_id]);
        $_SESSION['success'] = "Course deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete course.";
    }
    header("Location: manage-courses.php");
    exit;
}

// Fetch all courses along with tutor information, total lessons, and total enrollments
try {
    $stmt = $pdo->query("
        SELECT c.*, u.full_name AS tutor_name, u.email AS tutor_email,
               (SELECT COUNT(*) FROM course_lessons WHERE course_id = c.id) AS lesson_count,
               (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS student_count
        FROM courses c 
        JOIN users u ON c.tutor_id = u.id 
        ORDER BY c.id DESC
    ");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Manage Courses</title>
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
            <li><a href="manage-courses.php" class="nav-link p-2 active rounded fw-bold text-primary bg-light"><i class="fa-solid fa-book-open me-2"></i> Manage Courses</a></li>
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
            <h2 class="fw-bold text-dark">Platform Courses</h2>
            <p class="text-muted">Monitor and manage all courses published by platform tutors in a card layout.</p>
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

        <!-- Course Cards Grid -->
        <div class="row g-4">
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $c): 
                    $display_price = !empty($c['discount_price']) && $c['discount_price'] > 0 ? $c['discount_price'] : $c['price'];
                    
                    // Course image handling
                    $course_img = $c['image'] ?? '';
                    if (empty($course_img)) {
                        $img_url = '../assets/img/popular_courses/img1.avif';
                    } elseif (str_starts_with($course_img, 'http')) {
                        $img_url = $course_img;
                    } else {
                        $img_url = '../assets/img/courses/' . $course_img;
                    }
                ?>
                    <div class="col-md-4 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white d-flex flex-column">
                            <!-- Course Thumbnail -->
                            <div class="position-relative" style="height: 180px;">
                                <img src="<?php echo htmlspecialchars($img_url); ?>" alt="Course Image" class="w-100 h-100 object-fit-cover" onerror="this.src='../assets/img/popular_courses/img1.avif';">
                                <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 px-3 py-2 rounded-pill">
                                    ৳ <?php echo number_format($display_price, 2); ?>
                                </span>
                            </div>

                            <!-- Course Content -->
                            <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 small fw-bold">
                                            <i class="fa-regular fa-user me-1"></i> <?php echo htmlspecialchars($c['tutor_name']); ?>
                                        </span>
                                        <small class="text-muted"><?php echo $c['student_count']; ?> Students</small>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2 line-clamp-2"><?php echo htmlspecialchars($c['title']); ?></h5>
                                    <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($c['subtitle'] ?? '', 0, 75)) . '...'; ?></p>
                                </div>

                                <div>
                                    <div class="d-flex align-items-center justify-content-between border-top pt-3 mb-3 text-muted small">
                                        <span><i class="fa-regular fa-file-lines me-1 text-primary"></i> <?php echo $c['lesson_count']; ?> Lessons</span>
                                        <span><i class="fa-solid fa-chart-simple me-1 text-primary"></i> <?php echo htmlspecialchars($c['level'] ?? 'All Levels'); ?></span>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="../course-details.php?id=<?php echo $c['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold flex-grow-1">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>
                                        <a href="manage-courses.php?delete_id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold px-3" onclick="return confirm('Are you sure you want to delete this course?');" title="Delete Course">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <div class="mb-2"><i class="fa-solid fa-book-open fs-1 text-secondary opacity-50"></i></div>
                        <p class="text-muted mb-0">No courses found on the platform.</p>
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