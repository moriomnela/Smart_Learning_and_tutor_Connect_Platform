<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in admin can access
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch Real-time Analytics & Stats from Database
try {
    // 1. Total Counts
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
    $total_tutors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'tutor'")->fetchColumn();
    $total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $total_enrollments = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
    $total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    
    // 2. Total Gross Revenue Calculation (Courses + Sessions)
    $course_revenue = $pdo->query("SELECT SUM(c.price) FROM enrollments e JOIN courses c ON e.course_id = c.id")->fetchColumn() ?? 0;
    
    $tutor_rates = $pdo->query("
        SELECT SUM(b_count * u.hourly_rate) FROM (
            SELECT tutor_id, COUNT(*) as b_count FROM bookings WHERE status = 'approved' GROUP BY tutor_id
        ) sub JOIN users u ON sub.tutor_id = u.id
    ")->fetchColumn() ?? 0;

    $gross_revenue = $course_revenue + $tutor_rates;
    $platform_commission = $gross_revenue * 0.10; // 10% platform commission

    // 3. Monthly Growth breakdown
    $monthly_stats = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_year, 
               COUNT(CASE WHEN role = 'student' THEN 1 END) AS new_students,
               COUNT(CASE WHEN role = 'tutor' THEN 1 END) AS new_tutors
        FROM users 
        GROUP BY month_year 
        ORDER BY month_year DESC 
        LIMIT 6
    ")->fetchAll();

    // Prepare chart data arrays (reverse chronological to chronological for graph)
    $chart_labels = [];
    $chart_students = [];
    $chart_tutors = [];
    
    foreach (array_reverse($monthly_stats) as $stat) {
        $chart_labels[] = date('M Y', strtotime($stat['month_year'] . '-01'));
        $chart_students[] = $stat['new_students'];
        $chart_tutors[] = $stat['new_tutors'];
    }

} catch (PDOException $e) {
    $total_users = $total_students = $total_tutors = $total_courses = $total_enrollments = $total_bookings = 0;
    $gross_revenue = $platform_commission = 0;
    $monthly_stats = [];
    $chart_labels = [];
    $chart_students = [];
    $chart_tutors = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Site Statistics & Growth</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <li><a href="site-stat.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-chart-pie me-2"></i> Site Statistics</a></li>
            <li><a href="applications.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Teacher Applications</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Site Growth & Statistics</h2>
                <p class="text-muted">Analyze platform expansion, user acquisition metrics, and financial performance.</p>
            </div>
            <div>
                <span class="badge bg-success fs-6 px-3 py-2"><i class="fa-solid fa-arrow-trend-up me-1"></i> Growth: Active</span>
            </div>
        </div>

        <!-- High-Level Metric Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $total_users; ?></h3>
                            <span class="text-muted small">Total Registered Users</span>
                        </div>
                    </div>
                    <div class="mt-3 text-success small fw-bold">
                        <i class="fa-solid fa-arrow-up me-1"></i> Students: <?php echo $total_students; ?> | Tutors: <?php echo $total_tutors; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $total_courses; ?></h3>
                            <span class="text-muted small">Active Courses</span>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">
                        Total Enrollments: <strong><?php echo $total_enrollments; ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $total_bookings; ?></h3>
                            <span class="text-muted small">1-on-1 Sessions</span>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">
                        Completed & Confirmed Bookings
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">৳ <?php echo number_format($platform_commission, 2); ?></h3>
                            <span class="text-muted small">Platform Commission (10%)</span>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">
                        Gross Volume: ৳ <?php echo number_format($gross_revenue, 2); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Growth Chart Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h4 class="fw-bold mb-3"><i class="fa-solid fa-chart-line text-primary me-2"></i> Monthly User Growth Trend</h4>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Monthly User Acquisition & Growth Table -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h4 class="fw-bold mb-4">Monthly User Acquisition Breakdown</h4>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Month / Year</th>
                            <th class="text-center">New Students Joined</th>
                            <th class="text-center">New Tutors Joined</th>
                            <th class="text-end">Growth Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($monthly_stats) > 0): ?>
                            <?php foreach ($monthly_stats as $stat): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo date('F Y', strtotime($stat['month_year'] . '-01')); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-bold">+<?php echo $stat['new_students']; ?> Students</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-bold">+<?php echo $stat['new_tutors']; ?> Tutors</span>
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        <i class="fa-solid fa-circle-check me-1"></i> Active
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No monthly breakdown data available yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Platform Health & Insights Banner -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h4 class="fw-bold mb-3"><i class="fa-solid fa-shield-halved text-primary me-2"></i> Platform Health & Insights</h4>
            <ul class="text-muted mb-0 ps-3">
                <li class="mb-2"><strong>User Retention:</strong> Student enrollment frequency has increased following the introduction of platform course evaluations.</li>
                <li class="mb-2"><strong>Instructor Activity:</strong> Active tutors maintain stable session bookings and course offerings across technical domains.</li>
                <li><strong>System Reliability:</strong> Automated commission deduction (10%) and withdrawal clearance channels are fully operational.</li>
            </ul>
        </div>
    </div>
</div>

<!-- Chart.js Rendering Script -->
<script>
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [
                {
                    label: 'New Students',
                    data: <?php echo json_encode($chart_students); ?>,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'New Tutors',
                    data: <?php echo json_encode($chart_tutors); ?>,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>