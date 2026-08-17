<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in admin can access
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

try {
    // Fetch Detailed Earnings per Tutor for the Table & Platform Calculations
    $stmt = $pdo->prepare("
        SELECT 
            u.id AS tutor_id, 
            u.full_name, 
            u.email, 
            u.hourly_rate,
            (SELECT COALESCE(SUM(c.price), 0) 
             FROM courses c 
             JOIN enrollments e ON c.id = e.course_id 
             WHERE c.tutor_id = u.id) AS course_revenue,
            (SELECT COUNT(*) 
             FROM bookings 
             WHERE tutor_id = u.id AND status = 'approved') AS session_count
        FROM users u 
        WHERE u.role = 'tutor'
        ORDER BY u.id DESC
    ");
    $stmt->execute();
    $tutors_earnings = $stmt->fetchAll();

    $total_platform_gross = 0;
    $total_course_rev = 0;
    $total_session_rev = 0;
    $grand_total_net = 0;

    foreach ($tutors_earnings as $t) {
        $session_rev = $t['session_count'] * $t['hourly_rate'];
        $gross = $t['course_revenue'] + $session_rev;
        
        $total_course_rev += $t['course_revenue'];
        $total_session_rev += $session_rev;
        $total_platform_gross += $gross;
    }

    // Admin's actual earnings from the platform (10% commission cut)
    $admin_total_commission = $total_platform_gross * 0.10;
    $grand_total_net = $total_platform_gross - $admin_total_commission;

} catch (PDOException $e) {
    $total_platform_gross = $total_course_rev = $total_session_rev = $admin_total_commission = $grand_total_net = 0;
    $tutors_earnings = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Admin Earnings Overview</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Admin Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px;height: 100vh;position: sticky;top: 0;overflow: auto;scrollbar-width: thin;scrollbar-color: transparent transparent;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded"><i class="fa-solid fa-chart-line me-2"></i> Overview</a></li>
            <li><a href="site-stat.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-chart-pie me-2"></i> Site Statistics</a></li>
            <li><a href="applications.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Teacher Applications</a></li>
            <li><a href="manage-teachers.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-tie me-2"></i> Manage Teachers</a></li>
            <li><a href="manage-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> Manage Courses</a></li>
            <li><a href="withdrawals.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="earning.php" class="nav-link p-2 active rounded fw-bold text-primary bg-light"><i class="fa-solid fa-wallet me-2"></i> Admin Earnings</a></li>
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
            <h2 class="fw-bold text-dark">Admin Financial Overview</h2>
            <p class="text-muted">Track platform commission earnings, total transaction volume, and tutor payout liabilities.</p>
        </div>

        <!-- 3 Admin Summary Cards -->
        <div class="row g-4 mb-5">
            <!-- Admin Total Commission Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 text-white rounded-4 h-100" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                    <small class="text-white-50 fw-bold text-uppercase">Admin Commission (10%)</small>
                    <h2 class="fw-bold mt-2 mb-0 text-white">৳ <?php echo number_format($admin_total_commission, 2); ?></h2>
                    <small class="text-white-50 mt-2 d-block">Platform earnings from all sales</small>
                </div>
            </div>
            <!-- Total Gross Volume Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-4 h-100">
                    <small class="text-muted fw-bold text-uppercase">Total Gross Volume</small>
                    <h3 class="fw-bold mt-2 text-primary mb-1">৳ <?php echo number_format($total_platform_gross, 2); ?></h3>
                    <span class="small text-muted">Courses: ৳ <?php echo number_format($total_course_rev, 2); ?> | Sessions: ৳ <?php echo number_format($total_session_rev, 2); ?></span>
                </div>
            </div>
            <!-- Total Payout Liability Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-4 h-100">
                    <small class="text-muted fw-bold text-uppercase">Total Payout Liability</small>
                    <h3 class="fw-bold mt-2 text-success mb-1">৳ <?php echo number_format($grand_total_net, 2); ?></h3>
                    <span class="small text-muted">Net balance due to all tutors</span>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown Table -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h4 class="fw-bold mb-4">Tutor-wise Earnings Breakdown</h4>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tutor Name</th>
                            <th class="text-center">Course Rev.</th>
                            <th class="text-center">Session Rev.</th>
                            <th class="text-center">Gross Revenue</th>
                            <th class="text-center">10% Commission</th>
                            <th class="text-end text-success fw-bold">Net Payout</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tutors_earnings) > 0): 
                            foreach ($tutors_earnings as $t):
                                $session_rev = $t['session_count'] * $t['hourly_rate'];
                                $gross = $t['course_revenue'] + $session_rev;
                                $commission = $gross * 0.10;
                                $net = $gross - $commission;
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($t['full_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($t['email']); ?></small>
                                </td>
                                <td class="text-center">৳ <?php echo number_format($t['course_revenue'], 2); ?></td>
                                <td class="text-center">৳ <?php echo number_format($session_rev, 2); ?></td>
                                <td class="text-center fw-bold">৳ <?php echo number_format($gross, 2); ?></td>
                                <td class="text-center text-danger">৳ <?php echo number_format($commission, 2); ?></td>
                                <td class="text-end fw-bold text-success">৳ <?php echo number_format($net, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-secondary">
                            <td colspan="5" class="text-end fw-bold">Total Platform Payout Liability:</td>
                            <td class="text-end fw-bold text-primary">৳ <?php echo number_format($grand_total_net, 2); ?></td>
                        </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No tutor earnings found.</td>
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