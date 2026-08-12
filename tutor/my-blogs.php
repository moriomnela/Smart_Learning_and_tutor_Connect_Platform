<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['role'], ['tutor', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

$author_id = $_SESSION['user_id'];

// Handle Delete Blog
if (isset($_GET['delete_id'])) {
    $blog_id = intval($_GET['delete_id']);
    try {
        $del_stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ? AND author_id = ?");
        $del_stmt->execute([$blog_id, $author_id]);
        $_SESSION['success'] = "Blog post deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete blog post.";
    }
    header("Location: my-blogs.php");
    exit;
}

// Fetch Blogs posted by this author
try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE author_id = ? ORDER BY id DESC");
    $stmt->execute([$author_id]);
    $blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $blogs = [];
}

$page_title = "My Blogs";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - My Blogs</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> <?php echo ucfirst($_SESSION['role']); ?></h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">My Blog Posts</h2>
                <p class="text-muted">Manage all the articles and study guides you have published.</p>
            </div>
            <a href="add-blog.php" class="btn btn-primary fw-bold px-4 py-2"><i class="fa-solid fa-plus me-2"></i> Write New Blog</a>
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

        <!-- Blogs Grid Cards -->
        <div class="row g-4">
            <?php if (!empty($blogs)): ?>
                <?php foreach ($blogs as $blog): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden d-flex flex-column">
                        <!-- Blog Thumbnail Image -->
                        <div class="position-relative" style="height: 180px; overflow: hidden;">
                            <img src="../<?php echo htmlspecialchars($blog['image'] ?? 'https://dummyimage.com/600x400/1e3a8a/ffffff.jpg&text=Blog+Image'); ?>" alt="Blog Image" class="w-100 h-100 object-fit-cover">
                            <span class="position-absolute top-0 start-0 m-3 badge bg-primary px-3 py-2"><?php echo htmlspecialchars($blog['category']); ?></span>
                        </div>
                        
                        <!-- Blog Content Body -->
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <div class="text-muted small mb-2">
                                <i class="fa-regular fa-calendar-alt text-primary me-1"></i> <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">
                                <a href="../blog-details.php?id=<?php echo $blog['id']; ?>" target="_blank" class="text-dark text-decoration-none"><?php echo htmlspecialchars($blog['title']); ?></a>
                            </h5>
                            <p class="text-muted small flex-grow-1 mb-3">
                                <?php echo substr(htmlspecialchars($blog['excerpt']), 0, 90); ?>...
                            </p>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <div>
                                    <a href="../blog-details.php?id=<?php echo $blog['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold px-2 me-1" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="edit-blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-warning fw-bold px-2 text-dark" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                                <a href="my-blogs.php?delete_id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold px-2" title="Delete" onclick="return confirm('Are you sure you want to delete this blog post?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <div class="mb-3"><i class="fa-solid fa-newspaper fs-1 text-secondary opacity-50"></i></div>
                        <h5 class="fw-bold text-dark">No Blogs Published Yet</h5>
                        <p class="text-muted">You haven't published any articles yet. Share your thoughts with students!</p>
                        <a href="add-blog.php" class="btn btn-primary fw-bold mt-2 px-4 py-2 mx-auto" style="width: fit-content;">Write Your First Blog</a>
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