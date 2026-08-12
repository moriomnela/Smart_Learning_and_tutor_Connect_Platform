<?php
session_start();
require_once '../config/db.php'; 

// Security Check: If not logged in or not a student, redirect to login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

$appStmt = $pdo->prepare("SELECT status FROM tutor_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$appStmt->execute([$_SESSION['user_id']]);
$latestApp = $appStmt->fetch();

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
    <!-- Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Student</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Tutor Bookings</a></li>
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
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
                <p class="text-muted">Here is a quick overview of your learning journey.</p>
            </div>
            <div>
                <a href="../tutor.php" class="btn btn-primary fw-bold px-4 py-2">Find a Tutor</a>
                <a href="../courses.php" class="btn btn-primary fw-bold px-4 py-2">Find a Course</a>
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
                            <h3 class="fw-bold mb-0">3</h3>
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
                            <h3 class="fw-bold mb-0">2</h3>
                            <span class="text-muted small">Active Tutor Bookings</span>
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
                            <h3 class="fw-bold mb-0">1</h3>
                            <span class="text-muted small">Certificates Earned</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Enrolled Courses Section -->
        <div class="bg-white p-4 rounded-4 shadow-sm border-0">
            <h4 class="fw-bold mb-4">Continue Learning</h4>
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
                        <tr>
                            <td class="fw-bold">Complete Web Development Bootcamp</td>
                            <td>Nusrat Jahan</td>
                            <td>
                                <div class="progress" style="height: 8px; width: 150px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted">45% Completed</span>
                            </td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary fw-bold">Resume</a></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">HSC Physics Masterclass</td>
                            <td>Rafiqul Islam</td>
                            <td>
                                <div class="progress" style="height: 8px; width: 150px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted">80% Completed</span>
                            </td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary fw-bold">Resume</a></td>
                        </tr>
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