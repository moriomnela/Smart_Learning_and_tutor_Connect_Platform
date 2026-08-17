<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in admin can access
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all tutor applications along with user details
try {
    $stmt = $pdo->query("
        SELECT ta.*, u.full_name, u.email, u.avatar 
        FROM tutor_applications ta 
        JOIN users u ON ta.user_id = u.id 
        ORDER BY ta.id DESC
    ");
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $applications = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Manage Teacher Applications</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Admin Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px;height: 100vh;position: sticky;top: 0;overflow: auto;scrollbar-width: thin;scrollbar-color: transparent transparent;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chart-line me-2"></i> Overview</a></li>
            <li><a href="site-stat.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-chart-pie me-2"></i> Site Statistics</a></li>
            <li><a href="applications.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-chalkboard-user me-2"></i> Teacher Applications</a></li>
            <li><a href="manage-teachers.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-tie me-2"></i> Manage Teachers</a></li>
            <li><a href="manage-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> Manage Courses</a></li>
            <li><a href="withdrawals.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="earning.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Admin Earnings</a></li>
            <li><a href="manage-students.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-graduate me-2"></i> Manage Students</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Write Blog</a></li>
            <li><a href="manage-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> Manage Blogs</a></li>
            <li><a href="contacts.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-envelope-open-text me-2"></i> Messages</a></li>
            <li><a href="profile.php" class="nav-link text-dark p-2 rounded"><i class="fa-solid fa-user-gear me-2"></i>Profile Settings</a></li>
            <li><a href="../tutor.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chalkboard-user me-2"></i> Browse Tutors</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>

            <li><a href="../courses.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-book-bookmark me-2"></i> Browse Courses</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>            
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Teacher Applications</h2>
            <p class="text-muted">Review and manage instructor application submissions from registered students.</p>
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

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Applicant</th>
                            <th>Expertise & Headline</th>
                            <th>Rate & Lang</th>
                            <th>Qualification & Bio</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($applications) > 0): ?>
                            <?php foreach ($applications as $app): 
                                $status = $app['status'];
                                $badgeClass = 'bg-warning text-dark';
                                if ($status === 'approved') $badgeClass = 'bg-success';
                                elseif ($status === 'rejected') $badgeClass = 'bg-danger';
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['full_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($app['email']); ?></small>
                                        <div class="x-small text-muted"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($app['location'] ?? 'N/A'); ?></div>
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
                                        <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($app['experience'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($app['experience'] ?? 'No bio provided'); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?> px-2 py-1 text-uppercase small fw-bold">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($status === 'pending'): ?>
                                            <a href="../backend/approve-tutor.php?id=<?php echo $app['id']; ?>&user_id=<?php echo $app['user_id']; ?>" class="btn btn-sm btn-success fw-bold me-1 mb-1">Approve</a>
                                            <a href="../backend/reject-tutor.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold mb-1">Reject</a>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="fa-solid fa-folder-open fs-1 text-secondary opacity-50"></i></div>
                                    <p class="mb-0">No teacher applications found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>