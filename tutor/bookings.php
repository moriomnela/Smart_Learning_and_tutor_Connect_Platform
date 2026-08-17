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

// Handle Status Update (Approve / Reject)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';

    try {
        // 1. Fetch booking info to get student_id
        $get_booking = $pdo->prepare("SELECT student_id, subject FROM bookings WHERE id = ? AND tutor_id = ?");
        $get_booking->execute([$booking_id, $tutor_id]);
        $booking = $get_booking->fetch();

        if ($booking) {
            // 2. Update booking status
            $update_stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ? AND tutor_id = ?");
            $update_stmt->execute([$new_status, $booking_id, $tutor_id]);

            // 3. Insert Notification for the Student
            $student_id = $booking['student_id'];
            $notif_title = "Your booking for " . $booking['subject'] . " has been " . $new_status . ".";
            $notif_link = "bookings.php";
            
            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, link, is_read) VALUES (?, ?, ?, 0)");
            $notif_stmt->execute([$student_id, $notif_title, $notif_link]);

            $_SESSION['success'] = "Booking status updated to " . $new_status . "!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update booking status.";
    }
    header("Location: bookings.php");
    exit;
}

// Fetch bookings for this tutor (Including time_slot and class_mode)
try {
    $stmt = $pdo->prepare("
        SELECT b.*, u.full_name AS student_name, u.email AS student_email 
        FROM bookings b 
        JOIN users u ON b.student_id = u.id 
        WHERE b.tutor_id = ? 
        ORDER BY b.id DESC
    ");
    $stmt->execute([$tutor_id]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    // Ekhane print korale asli error ta dekhte parbi
    echo "Query Error: " . $e->getMessage();
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
    <!-- Magnific-Popup CSS -->
    <link rel="stylesheet" href="../assets/css/magnific-popup.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="../assets/css/swiper-bundle.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Tutor Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px;height: 100vh;position: sticky;top: 0;overflow: auto;scrollbar-width: thin;scrollbar-color: transparent transparent;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Tutor</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
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
            <h2 class="fw-bold text-dark">Student Bookings</h2>
            <p class="text-muted">Manage lesson and consultation requests from students.</p>
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
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Schedule & Mode</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bookings) > 0): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($booking['student_name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($booking['student_email']); ?></div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary"><?php echo htmlspecialchars($booking['subject']); ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><i class="fa-regular fa-calendar me-1 text-primary"></i> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></div>
                                        <small class="text-muted"><i class="fa-regular fa-clock me-1 text-primary"></i> <?php echo htmlspecialchars($booking['time_slot'] ?? 'N/A'); ?></small>
                                        <span class="badge bg-light text-dark border ms-1"><?php echo htmlspecialchars($booking['class_mode'] ?? 'Online'); ?></span>
                                    </td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($booking['message'] ?? 'No message'); ?></small></td>
                                    <td>
                                        <?php if ($booking['status'] === 'approved'): ?>
                                            <span class="badge bg-success px-3 py-2">Approved</span>
                                        <?php elseif ($booking['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger px-3 py-2">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <a href="bookings.php?action=approve&id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-success fw-bold me-1">Approve</a>
                                            <a href="bookings.php?action=reject&id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold">Reject</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No booking requests found.</td>
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