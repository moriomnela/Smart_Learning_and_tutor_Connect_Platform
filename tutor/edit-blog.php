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
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch existing blog data
try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ? AND author_id = ?");
    $stmt->execute([$blog_id, $author_id]);
    $blog = $stmt->fetch();

    if (!$blog) {
        $_SESSION['error'] = "Blog post not found or unauthorized.";
        header("Location: my-blogs.php");
        exit;
    }
} catch (PDOException $e) {
    header("Location: my-blogs.php");
    exit;
}

// Handle Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $tags = trim($_POST['tags']);
    $image_name = $blog['image']; // Keep old image by default

    // New Image Upload Handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $upload_dir = '../assets/img/blogs/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $image_name = 'assets/img/blogs/' . $file_name;
        }
    }

    try {
        $update_stmt = $pdo->prepare("
            UPDATE blogs SET title = ?, category = ?, excerpt = ?, content = ?, image = ?, tags = ? 
            WHERE id = ? AND author_id = ?
        ");
        $update_stmt->execute([$title, $category, $excerpt, $content, $image_name, $tags, $blog_id, $author_id]);
        
        $_SESSION['success'] = "Blog post updated successfully!";
        header("Location: my-blogs.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to update blog: " . $e->getMessage();
    }
}

$page_title = "Edit Blog";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Edit Blog</title>
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

    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Edit Blog Post</h2>
            <p class="text-muted">Update your article details and save changes.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger rounded-3 mb-4 fw-medium"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <form action="edit-blog.php?id=<?php echo $blog_id; ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-bold">Blog Title *</label>
                        <input type="text" name="title" class="form-control py-2" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category *</label>
                        <select name="category" class="form-select py-2" required>
                            <option value="Study Tips" <?php if($blog['category'] == 'Study Tips') echo 'selected'; ?>>Study Tips</option>
                            <option value="For Tutors" <?php if($blog['category'] == 'For Tutors') echo 'selected'; ?>>For Tutors</option>
                            <option value="Career Advice" <?php if($blog['category'] == 'Career Advice') echo 'selected'; ?>>Career Advice</option>
                            <option value="Tech & Programming" <?php if($blog['category'] == 'Tech & Programming') echo 'selected'; ?>>Tech & Programming</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tags</label>
                        <input type="text" name="tags" class="form-control py-2" value="<?php echo htmlspecialchars($blog['tags']); ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Featured Image (Leave blank to keep current)</label>
                        <input type="file" name="image" class="form-control py-2" accept="image/*">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Short Excerpt *</label>
                        <textarea name="excerpt" class="form-control" rows="3" required><?php echo htmlspecialchars($blog['excerpt']); ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Full Post Content *</label>
                        <textarea name="content" class="form-control" rows="8" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">Update Blog Post</button>
                        <a href="my-blogs.php" class="btn btn-secondary px-4 py-2 ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>