<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Approve / Reject Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $withdraw_id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action === 'approve') ? 'completed' : 'rejected';

    try {
        $stmt = $pdo->prepare("UPDATE withdrawals SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $withdraw_id]);
        $_SESSION['success'] = "Withdrawal request updated to " . $new_status . "!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update status.";
    }
    header("Location: withdrawals.php");
    exit;
}

// Fetch all withdrawal requests with tutor details
try {
    $stmt = $pdo->query("
        SELECT w.*, u.full_name AS tutor_name, u.email AS tutor_email 
        FROM withdrawals w 
        JOIN users u ON w.tutor_id = u.id 
        ORDER BY w.id DESC
    ");
    $withdrawals = $stmt->fetchAll();
} catch (PDOException $e) {
    $withdrawals = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Manage Withdrawals</title>
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
            <li><a href="withdrawals.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Write Blog</a></li>
            <li><a href="manage-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> Manage Blogs</a></li>
            <li><a href="contacts.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-envelope-open-text me-2"></i> Messages</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Withdrawal Requests</h2>
            <p class="text-muted">Review and process payout requests from tutors.</p>
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
                            <th>Tutor Info</th>
                            <th>Amount</th>
                            <th>Method / Account</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($withdrawals) > 0): ?>
                            <?php foreach ($withdrawals as $w): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($w['tutor_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($w['tutor_email']); ?></small>
                                </td>
                                <td><span class="fw-bold text-success">৳ <?php echo number_format($w['amount'], 2); ?></span></td>
                                <td><code><?php echo htmlspecialchars($w['method']); ?></code></td>
                                <td>
                                    <?php if ($w['status'] === 'completed'): ?>
                                        <span class="badge bg-success px-3 py-2">Completed</span>
                                    <?php elseif ($w['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger px-3 py-2">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($w['created_at'])); ?></small></td>
                                <td class="text-end">
                                    <?php if ($w['status'] === 'pending'): ?>
                                        <a href="withdrawals.php?action=approve&id=<?php echo $w['id']; ?>" class="btn btn-sm btn-success fw-bold me-1">Approve (Paid)</a>
                                        <a href="withdrawals.php?action=reject&id=<?php echo $w['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold">Reject</a>
                                    <?php else: ?>
                                        <span class="text-muted small">Processed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No withdrawal requests found.</td>
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