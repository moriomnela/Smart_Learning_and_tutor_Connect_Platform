<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // 1. Fetch Current Blog Details with Author Information (Added u.avatar)
    $stmt = $pdo->prepare("
        SELECT b.*, u.full_name AS author_name, u.email AS author_email, u.role AS author_role, u.avatar AS author_avatar 
        FROM blogs b 
        JOIN users u ON b.author_id = u.id 
        WHERE b.id = ?
    ");
    $stmt->execute([$blog_id]);
    $blog = $stmt->fetch();

    if (!$blog) {
        header("Location: blog.php");
        exit;
    }

    // 2. Fetch Recent Posts for Sidebar
    $recent_stmt = $pdo->prepare("SELECT id, title, image, created_at FROM blogs WHERE id != ? ORDER BY id DESC LIMIT 2");
    $recent_stmt->execute([$blog_id]);
    $recent_posts = $recent_stmt->fetchAll();

    // 3. Fetch Categories with Count
    $cat_stmt = $pdo->query("SELECT category, COUNT(*) as count FROM blogs GROUP BY category");
    $categories = $cat_stmt->fetchAll();

} catch (PDOException $e) {
    header("Location: blog.php");
    exit;
}

?>

<section class="blog-details-section py-5">
    <div class="container">
        <div class="row g-5">
            
            <!-- Left Side: Main Blog Content -->
            <div class="col-lg-8">
                <article class="single-post bg-white rounded-4 shadow-sm border overflow-hidden">
                    
                    <!-- Featured Image -->
                    <div class="post-thumb">
                        <img src="<?php echo htmlspecialchars($blog['image'] ?? 'https://dummyimage.com/900x500/1e3a8a/ffffff.jpg&text=Blog+Featured+Image'); ?>" alt="Blog Featured Image" class="img-fluid w-100" style="max-height: 450px; object-fit: cover;">
                    </div>

                    <!-- Post Body -->
                    <div class="post-content p-4 p-md-5">
                        <div class="post-meta d-flex flex-wrap gap-3 mb-3 text-muted small">
                            <span><i class="fa-regular fa-calendar text-primary me-2"></i> <?php echo date('F d, Y', strtotime($blog['created_at'])); ?></span>
                            <span><i class="fa-regular fa-user text-primary me-2"></i> By <?php echo htmlspecialchars($blog['author_name']); ?></span>
                            <span><i class="fa-regular fa-folder text-primary me-2"></i> <?php echo htmlspecialchars($blog['category']); ?></span>
                        </div>
                        
                        <h2 class="post-title fw-bold text-dark mb-4"><?php echo htmlspecialchars($blog['title']); ?></h2>
                        
                        <div class="post-body text-muted line-height-lg">
                            <?php 
                                echo nl2br($blog['content']); 
                            ?>
                        </div>
                        
                        <hr class="my-5">
                        
                        <!-- Tags and Share -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <div class="post-tags d-flex gap-2 align-items-center">
                                <span class="fw-bold text-dark me-2">Tags:</span>
                                <?php 
                                if (!empty($blog['tags'])) {
                                    $tags_arr = explode(',', $blog['tags']);
                                    foreach ($tags_arr as $tag) {
                                        echo '<span class="badge custom-tag bg-light text-dark border px-2 py-1">' . trim(htmlspecialchars($tag)) . '</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted small">No tags</span>';
                                }
                                ?>
                            </div>
                            <div class="post-share d-flex gap-2 align-items-center">
                                <span class="fw-bold text-dark me-2">Share:</span>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" target="_blank" class="share-icon facebook text-decoration-none btn btn-sm btn-outline-primary"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>&text=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-icon twitter text-decoration-none btn btn-sm btn-outline-info text-white"><i class="fa-brands fa-twitter"></i></a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" target="_blank" class="share-icon linkedin text-decoration-none btn btn-sm btn-outline-secondary"><i class="fa-brands fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Author Box with Dynamic Avatar -->
                <?php 
                    $author_avatar = $blog['author_avatar'] ?? '';
                    if (!empty($author_avatar) && $author_avatar !== 'default-avatar.png') {
                        $author_avatar_path = (str_starts_with($author_avatar, 'assets/')) ? $author_avatar : 'assets/img/profiles/' . $author_avatar;
                    } else {
                        $author_avatar_path = 'assets/img/profiles/default-avatar.png';
                    }
                ?>
                <div class="author-box mt-5 bg-white p-4 p-md-5 rounded-4 shadow-sm border d-flex flex-column flex-md-row align-items-center gap-4">
                    <div class="author-img-wrap shrink-0">
                        <img src="<?php echo htmlspecialchars($author_avatar_path); ?>" alt="<?php echo htmlspecialchars($blog['author_name']); ?>" class="rounded-circle img-fluid object-fit-cover border" width="90" height="90" onerror="this.src='assets/img/profiles/default-avatar.png';">
                    </div>
                    <div class="author-info text-center text-md-start">
                        <h4 class="fw-bold mb-2">
                            Written by 
                            <?php if (isset($blog['author_role']) && $blog['author_role'] === 'tutor'): ?>
                                <a href="tutor-details.php?id=<?php echo $blog['author_id']; ?>" class="text-primary text-decoration-none">
                                    <?php echo htmlspecialchars($blog['author_name']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-dark"><?php echo htmlspecialchars($blog['author_name']); ?></span>
                            <?php endif; ?>
                        </h4>
                        <p class="text-muted mb-0">An experienced educator and contributor on our platform, sharing valuable insights, study guides, and professional advice for students and tutors.</p>
                    </div>
                </div>

                <!-- Comments Section (Static UI kept intact) -->
                <div class="comments-area mt-5 bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                    <h3 class="fw-bold mb-4">Leave a Reply</h3>
                    <p class="text-muted mb-4 small">Your email address will not be published. Required fields are marked *</p>
                    <form action="#" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control custom-input" placeholder="Your Name *" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control custom-input" placeholder="Your Email *" required>
                            </div>
                            <div class="col-12">
                                <textarea class="form-control custom-input" rows="5" placeholder="Your Comment *" required></textarea>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold rounded-3">Post Comment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4">
                <aside class="blog-sidebar">
                    
                    <!-- Search Widget -->
                    <div class="widget search-widget bg-white p-4 rounded-4 shadow-sm border mb-4">
                        <h4 class="widget-title fw-bold mb-3">Search</h4>
                        <form action="blog.php" method="GET" class="position-relative">
                            <input type="text" name="search" class="form-control custom-input pe-5" placeholder="Search blog...">
                            <button type="submit" class="search-submit-btn border-0 bg-transparent position-absolute top-50 end-0 translate-middle-y me-3 text-muted"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <div class="widget category-widget bg-white p-4 rounded-4 shadow-sm border mb-4">
                        <h4 class="widget-title fw-bold mb-3">Categories</h4>
                        <ul class="list-unstyled mb-0 widget-list">
                            <?php foreach ($categories as $cat): ?>
                                <li class="mb-2"><a href="blog.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none"><?php echo htmlspecialchars($cat['category']); ?> <span class="badge bg-light text-primary border"> (<?php echo $cat['count']; ?>)</span></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Recent Posts Widget -->
                    <div class="widget recent-post-widget bg-white p-4 rounded-4 shadow-sm border mb-4">
                        <h4 class="widget-title fw-bold mb-3">Recent Posts</h4>
                        <?php if (!empty($recent_posts)): ?>
                            <?php foreach ($recent_posts as $rp): ?>
                            <div class="recent-post-item d-flex gap-3 mb-3">
                                <img src="<?php echo htmlspecialchars($rp['image'] ?? 'https://dummyimage.com/80x80/1e3a8a/ffffff.jpg&text=Post'); ?>" alt="Post" class="rounded-3" width="70" height="70" style="object-fit:cover;">
                                <div>
                                    <h6 class="mb-1"><a href="blog-details.php?id=<?php echo $rp['id']; ?>" class="text-dark text-decoration-none post-title-link"><?php echo htmlspecialchars($rp['title']); ?></a></h6>
                                    <span class="text-muted small"><?php echo date('M d, Y', strtotime($rp['created_at'])); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0">No recent posts available.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Tags Widget -->
                    <div class="widget tags-widget bg-white p-4 rounded-4 shadow-sm border">
                        <h4 class="widget-title fw-bold mb-3">Popular Tags</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge custom-tag bg-light text-dark border px-2 py-1">Education</span>
                            <span class="badge custom-tag bg-light text-dark border px-2 py-1">Online Learning</span>
                            <span class="badge custom-tag bg-light text-dark border px-2 py-1">Programming</span>
                            <span class="badge custom-tag bg-light text-dark border px-2 py-1">Exams</span>
                            <span class="badge custom-tag bg-light text-dark border px-2 py-1">Success</span>
                        </div>
                    </div>

                </aside>
            </div>
            
        </div>
    </div>
</section>