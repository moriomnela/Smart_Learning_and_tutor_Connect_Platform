<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Pagination and Search setup
$limit = 6; // 6 blogs per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Total blogs count for pagination (with search condition)
    if (!empty($search)) {
        $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE title LIKE ? OR category LIKE ?");
        $total_stmt->execute(["%$search%", "%$search%"]);
    } else {
        $total_stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
    }
    $total_blogs = $total_stmt->fetchColumn();
    $total_pages = ceil($total_blogs / $limit);

    // Fetch blogs with author name using JOIN (with search condition)
    if (!empty($search)) {
        $stmt = $pdo->prepare("
            SELECT b.*, u.full_name AS author_name 
            FROM blogs b 
            JOIN users u ON b.author_id = u.id 
            WHERE b.title LIKE ? OR b.category LIKE ?
            ORDER BY b.id DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    } else {
        $stmt = $pdo->prepare("
            SELECT b.*, u.full_name AS author_name 
            FROM blogs b 
            JOIN users u ON b.author_id = u.id 
            ORDER BY b.id DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    $blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $blogs = [];
    $total_pages = 1;
}

?>

<section class="blog-section py-5">
    <div class="container">
        <!-- Section Header -->
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="section-title fw-bold">Latest Educational Insights</h2>
                <p class="subtitle text-muted">Tips, news, and resources for students and teachers</p>
            </div>
        </div>

        <!-- Search Bar Form -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <form action="blogs.php" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                    <input type="text" name="search" class="form-control border-0 px-4 py-3 shadow-none" placeholder="Search blogs by title or category..." value="<?php echo htmlspecialchars($search); ?>">
                    <?php if (!empty($search)): ?>
                        <a href="blogs.php" class="btn btn-light border-0 px-3 d-flex align-items-center text-muted" title="Clear Search"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                    <button class="btn btn-primary px-4 fw-bold" type="submit"><i class="fa-solid fa-search me-1"></i> Search</button>
                </form>
            </div>
        </div>

        <!-- Blog Cards Grid -->
        <div class="row g-4 mb-5">
            <?php if (!empty($blogs)): ?>
                <?php foreach ($blogs as $blog): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-card h-100 shadow-sm bg-white rounded-4 overflow-hidden border-0">
                        <div class="blog-image position-relative" style="height: 200px; overflow: hidden;">
                            <img src="<?php echo htmlspecialchars($blog['image'] ?? 'https://dummyimage.com/600x400/1e3a8a/ffffff.jpg&text=Blog+Image'); ?>" alt="Blog Image" class="img-fluid w-100 h-100 object-fit-cover">
                            <span class="blog-category position-absolute top-0 start-0 m-3 badge bg-primary px-3 py-2"><?php echo htmlspecialchars($blog['category']); ?></span>
                        </div>
                        <div class="blog-content p-4 d-flex flex-column h-60">
                            <div class="blog-meta text-muted small mb-2 d-flex gap-3">
                                <span><i class="fa-regular fa-calendar-alt text-primary me-1"></i> <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                                <span><i class="fa-regular fa-user text-primary me-1"></i> <?php echo htmlspecialchars($blog['author_name']); ?></span>
                            </div>
                            <h4 class="blog-title fw-bold mb-2">
                                <a href="blog-details.php?id=<?php echo $blog['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($blog['title']); ?></a>
                            </h4>
                            <p class="blog-excerpt text-muted small flex-grow-1"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                            <a href="blog-details.php?id=<?php echo $blog['id']; ?>" class="read-more text-primary fw-bold text-decoration-none mt-3">Read More <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <div class="mb-3"><i class="fa-solid fa-search fs-1 text-secondary opacity-50"></i></div>
                        <h4 class="fw-bold text-dark">No Blog Posts Found</h4>
                        <p class="text-muted mb-3">We couldn't find any articles matching your search query.</p>
                        <a href="blogs.php" class="btn btn-outline-primary btn-sm fw-bold px-4 py-2">Reset Search</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="row">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center custom-pagination">
                        <!-- Previous Button -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="blogs.php?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><i class="fa-solid fa-angle-left"></i></a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="blogs.php?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="blogs.php?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><i class="fa-solid fa-angle-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>