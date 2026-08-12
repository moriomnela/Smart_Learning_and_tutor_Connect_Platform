<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

// Check if user is logged in and is either a tutor or admin
if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['role'], ['tutor', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

$author_id = $_SESSION['user_id'];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $tags = trim($_POST['tags']);

    // Image Upload Handling
    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $upload_dir = '../assets/img/blogs/';
        
        // Create directory if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $image_name = 'assets/img/blogs/' . $file_name;
        }
    }

    // Fallback dummy image if upload fails
    if (empty($image_name)) {
        $image_name = 'https://dummyimage.com/900x500/1e3a8a/ffffff.jpg&text=Blog+Featured+Image';
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO blogs (author_id, title, category, excerpt, content, image, tags, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$author_id, $title, $category, $excerpt, $content, $image_name, $tags]);
        
        $_SESSION['success'] = "Blog post published successfully!";
        header("Location: add-blog.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to publish blog: " . $e->getMessage();
    }
}

$page_title = "Add New Blog";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Add New Blog</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Sidebar (Dynamic based on role) -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> <?php echo ucfirst($_SESSION['role']); ?></h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Publish a New Blog Post</h2>
            <p class="text-muted">Share your knowledge, study tips, or insights with students and tutors.</p>
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

        <!-- Blog Form Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <form action="add-blog.php" method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    
                    <!-- Blog Title -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Blog Title *</label>
                        <input type="text" name="title" class="form-control py-2" required placeholder="E.g., Top 10 Effective Study Techniques for Board Exams">
                    </div>

                    <!-- Category & Tags -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category *</label>
                        <select name="category" class="form-select py-2" required>
                            <option value="" selected disabled>Select Category</option>
                            <option value="Study Tips">Study Tips</option>
                            <option value="For Tutors">For Tutors</option>
                            <option value="Career Advice">Career Advice</option>
                            <option value="Tech & Programming">Tech & Programming</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tags (Comma separated)</label>
                        <input type="text" name="tags" class="form-control py-2" placeholder="Exams, Productivity, Success">
                    </div>

                    <!-- Featured Image -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Featured Image</label>
                        <input type="file" name="image" class="form-control py-2" accept="image/*">
                        <small class="text-muted">Recommended size: 900x500 pixels.</small>
                    </div>

                    <!-- Excerpt / Short Description -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Short Excerpt *</label>
                        <textarea name="excerpt" class="form-control" rows="3" required placeholder="Write a short summary that will appear on the blog card grid..."></textarea>
                    </div>

                    <!-- Full Body Content -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Full Post Content *</label>
                        <textarea name="content" class="form-control" rows="8" required placeholder="Write your full detailed blog article here... (HTML or plain text supported)"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">Publish Blog Post</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>