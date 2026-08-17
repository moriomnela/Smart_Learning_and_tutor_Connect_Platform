<?php
session_start();
require_once '../config/db.php'; 

// Security Check: If not logged in or not a student, redirect to login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch latest user info including avatar and full name
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$student_id]);
$currentUser = $stmt->fetch();

$appStmt = $pdo->prepare("SELECT * FROM tutor_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$appStmt->execute([$student_id]);
$latestApp = $appStmt->fetch();

// 1. Fetch Enrolled Courses Count & List with Progress
try {
    $en_stmt = $pdo->prepare("
        SELECT c.*, u.full_name AS tutor_name 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        JOIN users u ON c.tutor_id = u.id 
        WHERE e.student_id = ? 
        ORDER BY e.id DESC
    ");
    $en_stmt->execute([$student_id]);
    $enrolled_courses = $en_stmt->fetchAll();
} catch (PDOException $e) {
    $enrolled_courses = [];
}

$enrolled_count = count($enrolled_courses);

// 2. Fetch Active Tutor Bookings Count
try {
    $book_stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE student_id = ?");
    $book_stmt->execute([$student_id]);
    $booking_count = $book_stmt->fetchColumn();
} catch (PDOException $e) {
    $booking_count = 0;
}

try {
    $recent_bookings_stmt = $pdo->prepare("
        SELECT b.*, u.full_name AS tutor_name 
        FROM bookings b 
        JOIN users u ON b.tutor_id = u.id 
        WHERE b.student_id = ? 
        ORDER BY b.id DESC 
        LIMIT 5
    ");
    $recent_bookings_stmt->execute([$student_id]);
    $recent_bookings = $recent_bookings_stmt->fetchAll();
} catch (PDOException $e) {
    $recent_bookings = [];
}

// 3. Fetch Certificates Earned Count (Only Approved)
try {
    $cert_stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates WHERE student_id = ? AND status = 'approved'");
    $cert_stmt->execute([$student_id]);
    $cert_count = $cert_stmt->fetchColumn();
} catch (PDOException $e) {
    $cert_count = 0;
}

// 4. Fetch Real Dynamic Notifications from Database
try {
    $notif_stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 10");
    $notif_stmt->execute([$student_id]);
    $notifications = $notif_stmt->fetchAll();

    // Count strictly UNREAD notifications for the red badge
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $count_stmt->execute([$student_id]);
    $notif_count = $count_stmt->fetchColumn();
} catch (PDOException $e) {
    $notifications = [];
    $notif_count = 0;
}


$page_title = "Student Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px;height: 100vh;position: sticky;top: 0;overflow: auto;scrollbar-width: thin;scrollbar-color: transparent transparent;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Student</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Tutor Bookings</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Profile Settings</a></li>
            <li><a href="../tutor.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chalkboard-user me-2"></i> Browse Tutors</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>

            <li><a href="../courses.php" target="_blank" class="nav-link p-2 rounded text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-book-bookmark me-2"></i> Browse Courses</span> 
                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
            </a></li>
            <?php if ($currentUser['role'] === 'student'): ?>
                <?php if (!$latestApp): ?>
                    <li class="mt-4"><a href="become-teacher.php" class="nav-link p-2 rounded text-success fw-bold"><i class="fa-solid fa-chalkboard-user me-2"></i> Become a Teacher</a></li>
                <?php elseif ($latestApp['status'] === 'pending'): ?>
                    <li class="mt-4"><span class="nav-link p-2 rounded text-warning fw-bold"><i class="fa-solid fa-clock me-2"></i> Application Pending</span></li>
                <?php elseif ($latestApp['status'] === 'rejected'): ?>
                    <li class="mt-4"><a href="become-teacher.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-rotate-right me-2"></i> Re-apply as Teacher</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><span class="nav-link p-2 rounded text-primary fw-bold"><i class="fa-solid fa-check-circle me-2"></i> Faculty Member</span></li>
            <?php endif; ?>
            <li><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        
        <!-- Top Header with Profile Picture & Notification Bell -->
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <?php 
                    $avatar = $currentUser['avatar'] ?? 'default-avatar.png';
                    if ($avatar === 'default-avatar.png' || empty($avatar)) {
                        $avatar_url = '../assets/img/profiles/default-avatar.png';
                    } elseif (str_starts_with($avatar, 'assets/')) {
                        $avatar_url = '../' . $avatar;
                    } else {
                        $avatar_url = '../assets/img/profiles/' . $avatar;
                    }
                ?>
                <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Profile Picture" class="rounded-circle object-fit-cover shadow border" width="70" height="70" style="height: 70px;" onerror="this.src='../assets/img/profiles/default-avatar.png'">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Welcome back, <?php echo htmlspecialchars($currentUser['full_name']); ?>!</h2>
                    <p class="text-muted mb-0 small">Here is a quick overview of your learning journey.</p>
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
                                    <!-- Clicking individual notification passes its ID to mark-read.php -->
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
                    <a href="../tutor.php" class="btn btn-primary fw-bold px-3 py-2">Find a Tutor</a>
                    <a href="../courses.php" class="btn btn-outline-primary fw-bold px-3 py-2 ms-2">Find a Course</a>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $enrolled_count; ?></h3>
                            <span class="text-muted small">Enrolled Courses</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $booking_count; ?></h3>
                            <span class="text-muted small">Total Tutor Bookings</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $cert_count; ?></h3>
                            <span class="text-muted small">Certificates Earned</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Enrolled Courses Section with Live Progress -->
        <div class="bg-white p-4 rounded-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-4">Continue Learning</h4>
                <a href="my-courses.php" class="btn btn-sm btn-outline-primary fw-bold">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Course Title</th>
                            <th>Instructor</th>
                            <th>Progress</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($enrolled_courses) > 0): ?>
                            <?php foreach ($enrolled_courses as $course): 
                                $c_id = $course['id'];
                                
                                $l_stmt = $pdo->prepare("SELECT COUNT(*) FROM course_lessons WHERE course_id = ?");
                                $l_stmt->execute([$c_id]);
                                $total_lessons = $l_stmt->fetchColumn();

                                $p_stmt = $pdo->prepare("SELECT COUNT(*) FROM lesson_progress WHERE student_id = ? AND course_id = ?");
                                $p_stmt->execute([$student_id, $c_id]);
                                $completed_lessons = $p_stmt->fetchColumn();

                                $progress_percent = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;
                            ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td><?php echo htmlspecialchars($course['tutor_name']); ?></td>
                                    <td>
                                        <div class="progress mb-1" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress_percent; ?>%;" aria-valuenow="<?php echo $progress_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="small text-muted"><?php echo $progress_percent; ?>% Completed</span>
                                    </td>
                                    <td><a href="learn-course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary fw-bold">Resume</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No enrolled courses found. <a href="../courses.php">Explore courses</a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Booking Requests Table -->
        <div class="bg-white p-4 rounded-4 shadow-sm border-0 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Recent Tutor Bookings</h4>
                <a href="bookings.php" class="btn btn-sm btn-outline-primary fw-bold">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tutor</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_bookings) > 0): ?>
                            <?php foreach ($recent_bookings as $booking): 
                                $status = $booking['status'];
                                $badge = ($status === 'approved') ? 'bg-success' : (($status === 'rejected') ? 'bg-danger' : 'bg-warning text-dark');
                            ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($booking['tutor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['subject']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                                    <td><span class="badge <?php echo $badge; ?> text-uppercase small"><?php echo $status; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No recent bookings found.</td>
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
<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome JS -->
<script src="../assets/js/fontawesome.min.js"></script>
<!-- Custom JS -->
<script src="../assets/js/main.js"></script>
</body>
</html>