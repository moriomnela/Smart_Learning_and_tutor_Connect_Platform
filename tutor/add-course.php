<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Add New Course";
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

<div class="dashboard-wrapper d-flex">
    <!-- Tutor Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Tutor</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>            
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Create a New Course</h2>
            <p class="text-muted">Fill out the details below to publish your course for students.</p>
        </div>

        <div class="bg-white p-5 rounded-4 shadow-sm border-0 col-lg-8">
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger rounded-3 mb-4 fw-medium">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success rounded-3 mb-4 fw-medium">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <!-- Added enctype for image upload -->
            <form action="../backend/add-course-process.php" method="POST" enctype="multipart/form-data">
            
                <div class="mb-3">
                    <label class="form-label fw-bold">Course Subtitle</label>
                    <input type="text" name="subtitle" class="form-control" placeholder="E.g., Web Development" required>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label text-muted fw-bold">Course Title</label>
                    <input type="text" id="title" name="title" class="form-control py-3" placeholder="E.g., Complete Web Development Bootcamp" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label text-muted fw-bold">Course Price (BDT)</label>
                    <input type="number" step="0.01" id="price" name="price" class="form-control py-3" placeholder="E.g., 2500" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">What You Will Learn (Comma separated)</label>
                    <textarea name="learning_outcomes" class="form-control" placeholder="E.g., Responsive Design, API Development, MySQL Basics" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Discounted Price (if any)</label>
                    <input type="number" step="0.01" name="discount_price" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="course_image" class="form-label text-muted fw-bold">Course Thumbnail Image</label>
                    <input type="file" id="course_image" name="course_image" class="form-control py-3" accept="image/*" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label text-muted fw-bold">Course Description</label>
                    <textarea id="description" name="description" rows="5" class="form-control py-3" placeholder="Describe what students will learn in this course..." required></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="dashboard.php" class="btn btn-outline-secondary px-4 py-2 fw-bold">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">Publish Course</button>
                </div>

            </form>
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