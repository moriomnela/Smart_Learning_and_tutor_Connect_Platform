<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Handle Search & Filters
$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$mode = isset($_GET['mode']) ? trim($_GET['mode']) : '';

try {
    // Base SQL query to fetch users with 'tutor' role
    $sql = "SELECT u.*, 
            (SELECT COUNT(*) FROM courses c WHERE c.tutor_id = u.id) AS course_count 
            FROM users u 
            WHERE u.role = 'tutor'";
    
    $params = [];

    // Apply Search Query filter (searches by name or email/bio if applicable)
    if (!empty($search_query)) {
        $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }

    // Note: If you have category or mode columns in your users/tutors table, 
    // you can uncomment and adjust these filters:
    /*
    if (!empty($category)) {
        $sql .= " AND u.category = ?";
        $params[] = $category;
    }
    if (!empty($mode)) {
        $sql .= " AND u.study_mode = ?";
        $params[] = $mode;
    }
    */

    $sql .= " ORDER BY u.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tutors = $stmt->fetchAll();

} catch (PDOException $e) {
    $tutors = [];
}
?>

<section class="find-tutor-section py-5">
    <div class="container">
        <!-- Search Box -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-12">
                <div class="find-tutor-box shadow-sm p-4 bg-white rounded-4">
                    <div class="text-center mb-4">
                        <h2 class="section-title">Find Your Perfect Tutor</h2>
                        <p class="subtitle text-muted">Search by subject, level, or study mode</p>
                    </div>
                    
                    <form action="tutors.php" method="GET" class="search-form">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" class="form-control custom-input" placeholder="E.g., Physics, Web Dev...">
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select custom-select">
                                    <option value="" <?php echo ($category == '') ? 'selected' : ''; ?>>All Categories</option>
                                    <option value="science" <?php echo ($category == 'science') ? 'selected' : ''; ?>>Science</option>
                                    <option value="commerce" <?php echo ($category == 'commerce') ? 'selected' : ''; ?>>Commerce</option>
                                    <option value="arts" <?php echo ($category == 'arts') ? 'selected' : ''; ?>>Arts & Humanities</option>
                                    <option value="programming" <?php echo ($category == 'programming') ? 'selected' : ''; ?>>Programming</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="mode" class="form-select custom-select">
                                    <option value="" <?php echo ($mode == '') ? 'selected' : ''; ?>>Study Mode</option>
                                    <option value="online" <?php echo ($mode == 'online') ? 'selected' : ''; ?>>Online (Zoom/Meet)</option>
                                    <option value="offline" <?php echo ($mode == 'offline') ? 'selected' : ''; ?>>In-Person (Home)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100 search-btn btn-primary py-2 fw-bold">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tutor Cards Grid -->
            <div class="row g-4">
            <?php if (count($tutors) > 0): ?>
                <?php foreach ($tutors as $tutor): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="premium-teacher-card">
                            <div class="teacher-thumb-box">
                                <img src="assets/img/profiles/<?php echo !empty($tutor['avatar']) ? htmlspecialchars($tutor['avatar']) : 'default-avatar.png'; ?>" 
                                    alt="Instructor Profile" 
                                    class="teacher-img"
                                    onerror="this.src='assets/img/default-avatar.png';">

                                <div class="social-flyout">
                                    <a href="#" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
                                    <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
                                </div>
                                
                                <!-- Dynamic Course Count Badge -->
                                <span class="courses-badge"><?php echo $tutor['course_count']; ?> Courses</span>
                            </div>

                            <div class="teacher-meta-content">
                                <h4 class="teacher-name"><?php echo htmlspecialchars($tutor['full_name']); ?></h4>
                                <p class="teacher-subject">Expert Instructor & Mentor</p>
                                
                                <a href="tutor-details.php?id=<?php echo $tutor['id']; ?>" class="btn-profile-trigger">
                                    <span>View Full Profile</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="bg-white p-5 rounded-4 text-center shadow-sm">
                        <h4 class="text-dark fw-bold mb-2">No Tutors Found</h4>
                        <p class="text-muted mb-4">We couldn't find any tutors matching your search criteria.</p>
                        <a href="tutors.php" class="btn btn-primary fw-bold px-4 py-2">Reset Search</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
