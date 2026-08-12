<?php
session_start();
require_once '../config/db.php';

// Security check: only logged-in students can access
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

$appStmt = $pdo->prepare("SELECT status FROM tutor_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$appStmt->execute([$_SESSION['user_id']]);
$latestApp = $appStmt->fetch();

// Fetch bookings for this student with tutor details
try {
    $stmt = $pdo->prepare("
        SELECT b.*, u.full_name AS tutor_name, u.email AS tutor_email, u.headline AS tutor_headline 
        FROM bookings b 
        JOIN users u ON b.tutor_id = u.id 
        WHERE b.student_id = ? 
        ORDER BY b.id DESC
    ");
    $stmt->execute([$student_id]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    $bookings = [];
}

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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Student Sidebar (Assuming you have a standard student layout or sidebar) -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Student</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="bookings.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Tutor Bookings</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user me-2"></i> Profile Settings</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            <?php if ($currentUser['role'] === 'student'): ?>
                <?php if (!$latestApp): ?>
                    <!-- No application yet: Show Apply Link -->
                    <li><a href="become-teacher.php" class="nav-link p-2 rounded text-success fw-bold"><i class="fa-solid fa-chalkboard-user me-2"></i> Become a Teacher</a></li>
                <?php elseif ($latestApp['status'] === 'pending'): ?>
                    <!-- Application is under review -->
                    <li><span class="nav-link p-2 rounded text-warning fw-bold"><i class="fa-solid fa-clock me-2"></i> Application Pending</span></li>
                <?php elseif ($latestApp['status'] === 'rejected'): ?>
                    <!-- If rejected, allow them to apply again -->
                    <li><a href="become-teacher.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-rotate-right me-2"></i> Re-apply as Teacher</a></li>
                <?php endif; ?>
            <?php else: ?>
                <!-- Already a tutor or admin -->
                <li><span class="nav-link p-2 rounded text-primary fw-bold"><i class="fa-solid fa-check-circle me-2"></i> Faculty Member</span></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">My Booking Requests</h2>
                <p class="text-muted">Track the status of your 1-on-1 session requests with our expert tutors.</p>
            </div>
            <a href="../tutor.php" class="btn btn-primary fw-bold px-4 py-2"><i class="fa-solid fa-plus me-2"></i> Book New Session</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Bookings Table Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tutor Info</th>
                            <th>Subject & Topic</th>
                            <th>Schedule</th>
                            <th>Mode</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bookings) > 0): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($booking['tutor_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['tutor_headline'] ?? 'Expert Tutor'); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($booking['subject']); ?></div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($booking['message']); ?>">
                                            <?php echo htmlspecialchars($booking['message'] ?? 'No message'); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark"><i class="fa-regular fa-calendar me-1 text-primary"></i> <?php echo htmlspecialchars($booking['booking_date']); ?></div>
                                        <small class="text-muted"><i class="fa-regular fa-clock me-1 text-primary"></i> <?php echo htmlspecialchars($booking['time_slot'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <i class="fa-solid fa-<?php echo ($booking['class_mode'] == 'Online') ? 'video' : 'person-chalkboard'; ?> me-1 text-primary"></i> 
                                            <?php echo htmlspecialchars($booking['class_mode'] ?? 'Online'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $booking['status'];
                                            $badgeClass = 'bg-warning text-dark';
                                            if ($status === 'approved') $badgeClass = 'bg-success text-white';
                                            elseif ($status === 'rejected') $badgeClass = 'bg-danger text-white';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 text-uppercase small fw-bold">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($status === 'approved'): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($booking['tutor_email']); ?>" class="btn btn-sm btn-outline-primary fw-bold"><i class="fa-regular fa-envelope me-1"></i> Contact</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Pending Review</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <div class="mb-2"><i class="fa-solid fa-calendar-xmark fs-1 text-secondary opacity-50"></i></div>
                                    <p class="mb-0">You haven't booked any sessions yet.</p>
                                    <a href="tutors.php" class="btn btn-sm btn-primary mt-3 fw-bold">Find a Tutor</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>