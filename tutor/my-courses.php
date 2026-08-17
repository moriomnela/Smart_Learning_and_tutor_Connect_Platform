<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in tutors
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];

// Fetch courses uploaded by this specific tutor
$stmt = $pdo->prepare("SELECT * FROM courses WHERE tutor_id = ? ORDER BY id DESC");
$stmt->execute([$tutor_id]);
$courses = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - My Courses</title>
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
    <!-- Tutor Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; height: 100vh; position: sticky; top: 0;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Tutor</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>            
            <li><a href="manage-certificates.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-certificate me-2"></i> Certificate Requests</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">My Published Courses</h2>
                <p class="text-muted">Manage all the courses you have created, update details, or add contents.</p>
            </div>
            <div>
                <a href="add-course.php" class="btn btn-primary fw-bold px-4 py-2"><i class="fa-solid fa-plus me-2"></i> Add New Course</a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden d-flex flex-column">
                            <div class="position-relative">
                                <img src="../assets/img/courses/<?php echo htmlspecialchars($course['image']); ?>" alt="Course Thumbnail" class="card-img-top" style="height: 180px; object-fit: cover;">
                                <span class="position-absolute top-0 start-0 m-3 badge bg-success px-3 py-2">Active</span>
                            </div>
                            <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                                <div>
                                    <span class="text-muted small fw-bold text-uppercase"><?php echo htmlspecialchars($course['subtitle'] ?? 'Course'); ?></span>
                                    <h5 class="fw-bold text-dark mb-2 mt-1"><?php echo htmlspecialchars($course['title']); ?></h5>
                                    <p class="text-muted small mb-3"><?php echo substr(htmlspecialchars($course['description']), 0, 75); ?>...</p>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                                        <span class="fw-bold text-primary fs-5">৳ <?php echo number_format($course['price'], 2); ?></span>
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <a href="../course-details.php?id=<?php echo $course['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold flex-grow-1"><i class="fa-solid fa-eye me-1"></i> View Details</a>
                                        <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary fw-bold flex-grow-1"><i class="fa-solid fa-pen-to-square me-1"></i> Manage & Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="bg-white p-5 rounded-4 text-center shadow-sm">
                        <div class="mb-3"><i class="fa-solid fa-book-open fs-1 text-secondary opacity-50"></i></div>
                        <h5 class="fw-bold text-dark">No Courses Published Yet</h5>
                        <p class="text-muted mb-3">You haven't published any courses yet. Share your knowledge with students!</p>
                        <a href="add-course.php" class="btn btn-primary fw-bold px-4 py-2">Create Your First Course</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- jQuery & JS -->
<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>