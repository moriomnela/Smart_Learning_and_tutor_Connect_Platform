<?php
session_start();
require_once '../config/db.php';

// Security Check: Ensure only logged-in tutors can access this page
if (!isset($_SESSION['is_logged_in'] ) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];

// Fetch current tutor details including avatar
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$tutor_id]);
$currentTutor = $stmt->fetch();

// 1. Fetch Active Students Count (Unique students who booked or enrolled)
try {
    $stu_stmt = $pdo->prepare("SELECT COUNT(DISTINCT student_id) FROM bookings WHERE tutor_id = ?");
    $stu_stmt->execute([$tutor_id]);
    $active_students_count = $stu_stmt->fetchColumn();
} catch (PDOException $e) {
    $active_students_count = 0;
}

// 2. Fetch Published Courses Count
try {
    $crs_stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE tutor_id = ?");
    $crs_stmt->execute([$tutor_id]);
    $published_courses_count = $crs_stmt->fetchColumn();
} catch (PDOException $e) {
    $published_courses_count = 0;
}

// 3. Fetch Total Earnings (Sum of course prices or booking payments if stored, fallback to 0)
try {
    // Assuming earnings can be calculated or fetched from enrollments on tutor's courses
    $earn_stmt = $pdo->prepare("
        SELECT SUM(c.price) AS total_earn 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE c.tutor_id = ?
    ");
    $earn_stmt->execute([$tutor_id]);
    $total_earnings = $earn_stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    $total_earnings = 0;
}

// 4. Fetch Recent Student Bookings for this Tutor
try {
    $book_stmt = $pdo->prepare("
        SELECT b.*, u.full_name AS student_name 
        FROM bookings b 
        JOIN users u ON b.student_id = u.id 
        WHERE b.tutor_id = ? 
        ORDER BY b.id DESC 
        LIMIT 5
    ");
    $book_stmt->execute([$tutor_id]);
    $recent_bookings = $book_stmt->fetchAll();
} catch (PDOException $e) {
    $recent_bookings = [];
}

// 5. Fetch Dynamic Notifications for Tutor
try {
    $notif_stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 5");
    $notif_stmt->execute([$tutor_id]);
    $notifications = $notif_stmt->fetchAll();

    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $count_stmt->execute([$tutor_id]);
    $notif_count = $count_stmt->fetchColumn();
} catch (PDOException $e) {
    $notifications = [];
    $notif_count = 0;
}

$page_title = "Tutor Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Tutor Dashboard</title>
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
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Tutor</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        
        <!-- Top Header with Profile Picture & Notification Bell -->
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <?php 
                    $avatar = $currentTutor['avatar'] ?? 'default-avatar.png';
                    if ($avatar === 'default-avatar.png' || empty($avatar)) {
                        $avatar_url = '../assets/img/profiles/default-avatar.png';
                    } elseif (str_starts_with($avatar, 'assets/')) {
                        $avatar_url = '../' . $avatar;
                    } else {
                        $avatar_url = '../assets/img/profiles/' . $avatar;
                    }
                ?>
                <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Profile Picture" class="rounded-circle object-fit-cover shadow border" width="70" height="70" onerror="this.src='../assets/img/profiles/default-avatar.png'">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Welcome, <?php echo htmlspecialchars($currentTutor['full_name']); ?>!</h2>
                    <p class="text-muted mb-0 small">Manage your teaching schedule, courses, and student interactions here.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border shadow-sm rounded-circle position-relative p-0 d-flex align-items-center justify-content-center" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-bell text-secondary fs-5"></i>
                        <?php if ($notif_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                                <?php echo $notif_count; ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3 rounded-4 mt-2" aria-labelledby="notifDropdown" style="width: 320px; max-height: 380px; overflow-y: auto; z-index: 1050;">
                        <li class="dropdown-header fw-bold text-dark border-bottom pb-2 mb-2 px-0 d-flex justify-content-between align-items-center">
                            <span>Notifications</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $notif_count; ?> New</span>
                        </li>
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach ($notifications as $notif): 
                                $bg_class = ($notif['is_read'] == 0) ? 'bg-white border-start border-primary border-4' : 'bg-light text-muted opacity-75';
                            ?>
                                <li class="mb-2">
                                    <a class="dropdown-item d-flex align-items-start gap-2 p-2 text-wrap rounded-3 text-decoration-none shadow-sm <?php echo $bg_class; ?>" href="mark-read.php?id=<?php echo $notif['id']; ?>&url=<?php echo urlencode($notif['link'] ?? '#'); ?>">
                                        <i class="fa-solid fa-bell text-primary mt-1 fs-5"></i>
                                        <div>
                                            <div class="small fw-bold text-dark"><?php echo htmlspecialchars($notif['title']); ?></div>
                                            <div class="x-small text-muted" style="font-size: 11px;"><?php echo date('M d, Y - h:i A', strtotime($notif['created_at'])); ?></div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted text-center small py-3">No notifications found</span></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div>
                    <a href="add-course.php" class="btn btn-primary fw-bold px-4 py-2"><i class="fa-solid fa-plus me-2"></i> Add New Course</a>
                </div>
            </div>
        </div>

        <!-- Tutor Stats Grid (Dynamic Counts) -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $active_students_count; ?></h3>
                            <span class="text-muted small">Active Students</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $published_courses_count; ?></h3>
                            <span class="text-muted small">Published Courses</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">৳ <?php echo number_format($total_earnings, 2); ?></h3>
                            <span class="text-muted small">Total Earnings</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Student Bookings Table (Dynamic) -->
        <div class="bg-white p-4 rounded-4 shadow-sm border-0 mb-5">
            <h4 class="fw-bold mb-4">Recent Lesson Bookings</h4>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_bookings) > 0): ?>
                            <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($booking['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['booking_date'] . ' - ' . $booking['time_slot']); ?></td>
                                    <td>
                                        <?php if ($booking['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php elseif ($booking['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger">Declined</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <a href="booking-action.php?id=<?php echo $booking['id']; ?>&action=approve" class="btn btn-sm btn-success fw-bold me-1">Accept</a>
                                            <a href="booking-action.php?id=<?php echo $booking['id']; ?>&action=reject" class="btn btn-sm btn-outline-danger fw-bold">Decline</a>
                                        <?php else: ?>
                                            <a href="bookings.php" class="btn btn-sm btn-outline-primary fw-bold">View Details</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No recent lesson bookings found.</td>
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
<!-- Popper.js & Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome JS -->
<script src="../assets/js/fontawesome.min.js"></script>
<!-- Custom JS -->
<script src="../assets/js/main.js"></script>
</body>
</html>