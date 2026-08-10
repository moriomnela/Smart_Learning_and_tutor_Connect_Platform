<?php
session_start();

// Security Check: Ensure only logged-in admins can access this page
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Admin Dashboard";
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
    <!-- Admin Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-chart-line me-2"></i> Overview</a></li>
            <li><a href="users.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-users me-2"></i> Manage Users</a></li>
            <li><a href="applications.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-check me-2"></i> Teacher Requests</a></li>
            <li><a href="courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book me-2"></i> All Courses</a></li>
            <li><a href="settings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-gear me-2"></i> Settings</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Admin Control Center</h2>
                <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Here is what's happening on SLTCP today.</p>
            </div>
            <div>
                <span class="badge bg-primary fs-6 px-3 py-2">System Status: Active</span>
            </div>
        </div>

        <!-- System Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">1,420</h3>
                            <span class="text-muted small">Total Users</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">85</h3>
                            <span class="text-muted small">Active Tutors</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">42</h3>
                            <span class="text-muted small">Published Courses</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger p-3 rounded-3 fs-4">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">5</h3>
                            <span class="text-muted small">Pending Requests</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Teacher Applications Table -->
        <div class="bg-white p-4 rounded-4 shadow-sm border-0 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Recent Teacher Applications</h4>
                <a href="applications.php" class="btn btn-sm btn-outline-primary fw-bold">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Applicant Name</th>
                            <th>Expertise</th>
                            <th>Qualification</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Tanvir Ahmed</td>
                            <td>Accounting & Finance</td>
                            <td>BBA from IBA, DU</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-success fw-bold me-1">Approve</a>
                                <a href="#" class="btn btn-sm btn-outline-danger fw-bold">Reject</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sadia Sultana</td>
                            <td>Biology & Chemistry</td>
                            <td>M.Sc from Dhaka University</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-success fw-bold me-1">Approve</a>
                                <a href="#" class="btn btn-sm btn-outline-danger fw-bold">Reject</a>
                            </td>
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