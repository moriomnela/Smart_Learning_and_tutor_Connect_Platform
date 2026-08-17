<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];

try {
    // 1. Total from Approved Sessions (Bookings)
    $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE tutor_id = ? AND status = 'approved'");
    $stmt1->execute([$tutor_id]);
    $session_count = $stmt1->fetchColumn();

    $rate_stmt = $pdo->prepare("SELECT hourly_rate FROM users WHERE id = ?");
    $rate_stmt->execute([$tutor_id]);
    $hourly_rate = $rate_stmt->fetchColumn() ?? 0;
    
    $session_earnings = $session_count * $hourly_rate;

    // 2. Course-wise Revenue Details & Total Course Earnings
    $course_details_stmt = $pdo->prepare("
        SELECT c.title, c.price, COUNT(e.id) AS enrollment_count, 
               (COUNT(e.id) * c.price) AS total_course_revenue
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        WHERE c.tutor_id = ?
        GROUP BY c.id
    ");
    $course_details_stmt->execute([$tutor_id]);
    $course_stats = $course_details_stmt->fetchAll();

    // Calculate total course revenue from stats array safely
    $course_earnings = array_sum(array_column($course_stats, 'total_course_revenue'));

    // 3. Total Overall Gross Revenue
    $total_earnings = $session_earnings + $course_earnings;
    
    // Commission Percentage (10%)
    $commission_rate = 0.10; 
    $deduction = $total_earnings * $commission_rate;
    $net_balance = $total_earnings - $deduction;

    // 4. Fetch Total Completed Withdrawals
    $withdraw_sum_stmt = $pdo->prepare("
        SELECT SUM(amount) 
        FROM withdrawals 
        WHERE tutor_id = ? AND status = 'completed'
    ");
    $withdraw_sum_stmt->execute([$tutor_id]);
    $total_withdrawn = $withdraw_sum_stmt->fetchColumn() ?? 0;

    // 5. Final Available Balance after subtracting withdrawn amount
    $available_balance = $net_balance - $total_withdrawn;
    if ($available_balance < 0) {
        $available_balance = 0;
    }

} catch (PDOException $e) {
    $total_earnings = 0;
    $session_count = 0;
    $course_earnings = 0;
    $hourly_rate = 0;
    $net_balance = 0;
    $total_withdrawn = 0;
    $available_balance = 0;
    $course_stats = [];
}

// Fetch Withdrawal History
$withdraw_stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE tutor_id = ? ORDER BY id DESC");
$withdraw_stmt->execute([$tutor_id]);
$withdraw_history = $withdraw_stmt->fetchAll();

$page_title = "Tutor Earnings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Tutor Earnings</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Tutor Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; height: 100vh; position: sticky; top: 0;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Tutor</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>            
            <li><a href="manage-certificates.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-certificate me-2"></i> Certificate Requests</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Earnings & Wallet</h2>
            <p class="text-muted">Track your revenue generated from completed sessions and courses.</p>
        </div>

        <!-- Earnings Section Cards -->
        <div class="row g-4 mb-4">
            <!-- Net Available Balance -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 text-white rounded-4" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                    <small class="text-white-50 fw-bold text-uppercase">Net Available Balance</small>
                    <h2 class="fw-bold mt-2 mb-0 text-white">৳ <?php echo number_format($available_balance, 2); ?></h2>
                    <small class="text-white-50">Gross: ৳ <?php echo number_format($total_earnings, 2); ?> | Withdrawn: ৳ <?php echo number_format($total_withdrawn, 2); ?></small>
                </div>
            </div>
            <!-- Session Income -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                    <small class="text-muted fw-bold text-uppercase">From Sessions</small>
                    <h4 class="fw-bold mt-2 text-primary mb-1">৳ <?php echo number_format($session_earnings, 2); ?></h4>
                    <span class="small text-muted"><?php echo $session_count; ?> Sessions completed</span>
                </div>
            </div>
            <!-- Course Income -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                    <small class="text-muted fw-bold text-uppercase">From Courses</small>
                    <h4 class="fw-bold mt-2 text-success mb-1">৳ <?php echo number_format($course_earnings, 2); ?></h4>
                    <span class="small text-muted">Course sales revenue</span>
                </div>
            </div>
        </div>

        <!-- Course-wise Revenue Breakdown Table -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold mb-4">Course-wise Revenue Breakdown</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course Title</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Enrollments</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($course_stats)): ?>
                            <?php foreach ($course_stats as $stat): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($stat['title']); ?></div>
                                </td>
                                <td class="text-center">৳ <?php echo number_format($stat['price'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo $stat['enrollment_count']; ?> Students</span>
                                </td>
                                <td class="text-end fw-bold text-success">৳ <?php echo number_format($stat['total_course_revenue'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">You haven't added any courses yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Withdrawal History -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold mb-4">Withdrawal History</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($withdraw_history)): ?>
                            <?php foreach ($withdraw_history as $w): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($w['created_at'])); ?></td>
                                <td><span class="fw-bold text-dark">৳ <?php echo number_format($w['amount'], 2); ?></span></td>
                                <td><code><?php echo htmlspecialchars($w['method']); ?></code></td>
                                <td>
                                    <?php if ($w['status'] === 'completed'): ?>
                                        <span class="badge bg-success px-3 py-1">Completed</span>
                                    <?php elseif ($w['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger px-3 py-1">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark px-3 py-1">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No withdrawal requests yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payout Info Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold mb-3">Payout Information</h5>
            <p class="text-muted small">Your earnings are calculated based on your approved student sessions and course enrollments. Payouts are processed automatically every week.</p>
            <div class="alert alert-info mb-0">
                <i class="fa-solid fa-circle-info me-2"></i> Current Hourly Rate: <strong>৳ <?php echo number_format($hourly_rate, 2); ?></strong> 
                <a href="profile.php" class="alert-link ms-2">Update Rate</a>
            </div>
            <div class="mt-4">
                <button class="btn btn-warning fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="fa-solid fa-money-bill-transfer me-2"></i> Request Withdrawal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <form action="../backend/request-withdraw.php" method="POST">
        <div class="modal-body p-4">
            <h5 class="fw-bold mb-3">Request Withdrawal</h5>
            <p class="text-muted">Available Net Balance: <strong>৳ <?php echo number_format($available_balance, 2); ?></strong></p>
            
            <div class="mb-3">
                <label class="fw-bold">Amount to Withdraw</label>
                <input type="number" name="amount" class="form-control" max="<?php echo $available_balance; ?>" required placeholder="Enter amount">
            </div>
            <div class="mb-3">
                <label class="fw-bold">Payment Method</label>
                <input type="text" name="method" class="form-control" required placeholder="e.g., Bkash - 01XXXXXXXXX">
            </div>
        </div>
        <div class="modal-footer border-0">
          <button type="submit" class="btn btn-primary px-4">Confirm Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>