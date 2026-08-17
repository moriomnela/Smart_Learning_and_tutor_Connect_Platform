<?php
session_start();
require_once '../config/db.php';

// Security Check: Ensure only logged-in admins can access this page
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// 1. Fetch System Stats Dynamically
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalTutors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'tutor'")->fetchColumn();
try {
    $totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
} catch (PDOException $e) {
    $totalCourses = 0;
}
$pendingRequests = $pdo->query("SELECT COUNT(*) FROM tutor_applications WHERE status = 'pending'")->fetchColumn();

// 2. Fetch Pending Teacher Applications for the Table (Updated with bio & languages)
$appStmt = $pdo->query("
    SELECT ta.id, ta.user_id, ta.expertise, ta.headline, ta.hourly_rate, ta.location, ta.languages, ta.experience, ta.qualification, u.full_name 
    FROM tutor_applications ta 
    JOIN users u ON ta.user_id = u.id 
    WHERE ta.status = 'pending' 
    ORDER BY ta.id DESC 
    LIMIT 5
");
$pendingApps = $appStmt->fetchAll();

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
    <!-- Admin Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; height: 100vh; position: sticky; top: 0;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-chart-line me-2"></i> Overview</a></li>
            <li><a href="withdrawals.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Write Blog</a></li>
            <li><a href="manage-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> Manage Blogs</a></li>
            <li><a href="contacts.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-envelope-open-text me-2"></i> Messages</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Admin Control Center</h2>
                <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Here is what's happening on SLTCP today.</p>
            </div>
            <div>
                <span class="badge bg-primary fs-6 px-3 py-2">System Status: Active</span>
            </div>
        </div>

        <!-- System Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $totalUsers - 1; ?></h3>
                            <span class="text-muted small">Total Users</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $totalTutors; ?></h3>
                            <span class="text-muted small">Active Tutors</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $totalCourses; ?></h3>
                            <span class="text-muted small">Published Courses</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $pendingRequests; ?></h3>
                            <span class="text-muted small">Pending Requests</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Teacher Applications Table -->
        <div class="bg-white p-4 rounded-4 shadow-sm border-0 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Recent Teacher Applications</h4>
                <a href="applications.php" class="btn btn-sm btn-outline-primary fw-bold">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Applicant Name</th>
                            <th>Expertise & Headline</th>
                            <th>Rate & Lang</th>
                            <th>Bio & Qualification</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pendingApps) > 0): ?>
                            <?php foreach ($pendingApps as $app): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['full_name']); ?></div>
                                        <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($app['location'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($app['expertise']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($app['headline'] ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border mb-1">৳ <?php echo number_format($app['hourly_rate'] ?? 0, 0); ?>/hr</span>
                                        <small class="text-muted d-block"><i class="fa-solid fa-language me-1"></i><?php echo htmlspecialchars($app['languages'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold small text-dark"><?php echo htmlspecialchars($app['qualification']); ?></div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($app['experience'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($app['experience'] ?? 'No bio provided'); ?>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <a href="../backend/approve-tutor.php?id=<?php echo $app['id']; ?>&user_id=<?php echo $app['user_id']; ?>" class="btn btn-sm btn-success fw-bold me-1">Approve</a>
                                        <a href="../backend/reject-tutor.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold">Reject</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No pending teacher applications found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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

