<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle Delete (Admin can delete any blog)
if (isset($_GET['delete_id'])) {
    $blog_id = intval($_GET['delete_id']);
    try {
        $del_stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $del_stmt->execute([$blog_id]);
        $_SESSION['success'] = "Blog post deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete blog post.";
    }
    header("Location: manage-blogs.php");
    exit;
}

// Fetch ALL Blogs with Author Details
try {
    $stmt = $pdo->query("
        SELECT b.*, u.full_name AS author_name 
        FROM blogs b 
        JOIN users u ON b.author_id = u.id 
        ORDER BY b.id DESC
    ");
    $all_blogs = $stmt->fetchAll();

    // Separate into Admin's blogs and Tutors' blogs
    $my_blogs = array_filter($all_blogs, fn($b) => $b['author_id'] == $admin_id);
    $tutors_blogs = array_filter($all_blogs, fn($b) => $b['author_id'] != $admin_id);

} catch (PDOException $e) {
    $my_blogs = [];
    $tutors_blogs = [];
}

$page_title = "Admin - Manage Blogs";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Admin Manage Blogs</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Admin Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chart-line me-2"></i> Overview</a></li>
            <li><a href="withdrawals.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="add-blog.php" class="nav-link text-black p-2 rounded"><i class="fa-solid fa-pen-nib me-2"></i> Write Blog</a></li>
            <li><a href="manage-blogs.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-book-open-reader me-2"></i> Manage Blogs</a></li>
            <li><a href="contacts.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-envelope-open-text me-2"></i> Messages</a></li>            
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Manage All Blogs</h2>
                <p class="text-muted">Review, edit, or delete any blog post published by tutors or admin.</p>
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

        <!-- SECTION 1: YOUR BLOGS -->
        <div class="mb-5">
            <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">Your Blogs (Admin Posts)</h4>
            <div class="row g-4">
                <?php if (!empty($my_blogs)): ?>
                    <?php foreach ($my_blogs as $blog): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden d-flex flex-column">
                            <div class="position-relative" style="height: 180px; overflow: hidden;">
                                <img src="../<?php echo htmlspecialchars($blog['image'] ?? 'https://dummyimage.com/600x400/1e3a8a/ffffff.jpg&text=Blog+Image'); ?>" alt="Blog Image" class="w-100 h-100 object-fit-cover">
                                <span class="position-absolute top-0 start-0 m-3 badge bg-primary px-3 py-2"><?php echo htmlspecialchars($blog['category']); ?></span>
                            </div>
                            <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                <div class="text-muted small mb-2 d-flex justify-content-between">
                                    <span><i class="fa-regular fa-calendar-alt text-primary me-1"></i> <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                                    <span class="fw-bold text-secondary">By <?php echo htmlspecialchars($blog['author_name']); ?></span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">
                                    <a href="../blog-details.php?id=<?php echo $blog['id']; ?>" target="_blank" class="text-dark text-decoration-none"><?php echo htmlspecialchars($blog['title']); ?></a>
                                </h5>
                                <p class="text-muted small flex-grow-1 mb-3">
                                    <?php echo substr(htmlspecialchars($blog['excerpt']), 0, 80); ?>...
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <div>
                                        <a href="../blog-details.php?id=<?php echo $blog['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold px-2 me-1"><i class="fa-solid fa-eye"></i></a>
                                        <a href="../tutor/edit-blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-warning fw-bold px-2 text-dark"><i class="fa-solid fa-pen-to-square"></i></a>
                                    </div>
                                    <a href="manage-blogs.php?delete_id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold px-2" onclick="return confirm('Delete this blog post?');"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-muted">You haven't published any blogs yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SECTION 2: TUTORS' BLOGS -->
        <div>
            <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">Tutors' Blogs</h4>
            <div class="row g-4">
                <?php if (!empty($tutors_blogs)): ?>
                    <?php foreach ($tutors_blogs as $blog): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden d-flex flex-column">
                            <div class="position-relative" style="height: 180px; overflow: hidden;">
                                <img src="../<?php echo htmlspecialchars($blog['image'] ?? 'https://dummyimage.com/600x400/1e3a8a/ffffff.jpg&text=Blog+Image'); ?>" alt="Blog Image" class="w-100 h-100 object-fit-cover">
                                <span class="position-absolute top-0 start-0 m-3 badge bg-primary px-3 py-2"><?php echo htmlspecialchars($blog['category']); ?></span>
                            </div>
                            <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                <div class="text-muted small mb-2 d-flex justify-content-between">
                                    <span><i class="fa-regular fa-calendar-alt text-primary me-1"></i> <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                                    <span class="fw-bold text-secondary">By <?php echo htmlspecialchars($blog['author_name']); ?></span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">
                                    <a href="../blog-details.php?id=<?php echo $blog['id']; ?>" target="_blank" class="text-dark text-decoration-none"><?php echo htmlspecialchars($blog['title']); ?></a>
                                </h5>
                                <p class="text-muted small flex-grow-1 mb-3">
                                    <?php echo substr(htmlspecialchars($blog['excerpt']), 0, 80); ?>...
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <div>
                                        <a href="../blog-details.php?id=<?php echo $blog['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold px-2 me-1"><i class="fa-solid fa-eye"></i></a>
                                    </div>
                                    <a href="manage-blogs.php?delete_id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold px-2" onclick="return confirm('Delete this blog post?');"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-muted">No blog posts found from tutors.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>