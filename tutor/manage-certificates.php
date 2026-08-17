<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in tutors
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];

// Handle Status Update (Approve / Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_cert'])) {
    $cert_id = intval($_POST['cert_id']);
    $action = $_POST['action_type']; // 'approved' or 'rejected'

    if (in_array($action, ['approved', 'rejected'])) {
        // Verify certificate belongs to a course taught by this tutor
        $chk_stmt = $pdo->prepare("
            SELECT c.id FROM certificates cert 
            JOIN courses c ON cert.course_id = c.id 
            WHERE cert.id = ? AND c.tutor_id = ?
        ");
        $chk_stmt->execute([$cert_id, $tutor_id]);

        if ($chk_stmt->fetch()) {
            $upd = $pdo->prepare("UPDATE certificates SET status = ? WHERE id = ?");
            $upd->execute([$action, $cert_id]);
            $_SESSION['success'] = "Certificate request has been " . $action . " successfully!";
        } else {
            $_SESSION['error'] = "Unauthorized action!";
        }
    }
    header("Location: manage-certificates.php");
    exit;
}

// Fetch all certificate requests for courses belonging to this tutor
try {
    $req_stmt = $pdo->prepare("
        SELECT cert.*, c.title AS course_title, u.full_name AS student_name, u.email AS student_email 
        FROM certificates cert
        JOIN courses c ON cert.course_id = c.id
        JOIN users u ON cert.student_id = u.id
        WHERE c.tutor_id = ?
        ORDER BY cert.id DESC
    ");
    $req_stmt->execute([$tutor_id]);
    $requests = $req_stmt->fetchAll();
} catch (PDOException $e) {
    $requests = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SLTCP - Manage Certificate Requests</title>
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
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>            
            <li><a href="profile.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-certificate me-2"></i> Certificate Requests</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <h2 class="fw-bold mb-4">Student Certificate Requests</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-body p-4">
                <?php if (count($requests) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Course Title</th>
                                    <th>Certificate Code</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($req['student_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($req['student_email']); ?></small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary"><?php echo htmlspecialchars($req['course_title']); ?></span>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($req['certificate_code']); ?></code>
                                        </td>
                                        <td>
                                            <?php if ($req['status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark px-2 py-1">Pending</span>
                                            <?php elseif ($req['status'] === 'approved'): ?>
                                                <span class="badge bg-success px-2 py-1">Approved</span>
                                            <?php elseif ($req['status'] === 'rejected'): ?>
                                                <span class="badge bg-danger px-2 py-1">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($req['status'] === 'pending'): ?>
                                                <form action="" method="POST" class="d-inline-flex gap-2">
                                                    <input type="hidden" name="cert_id" value="<?php echo $req['id']; ?>">
                                                    <button type="submit" name="action_cert" value="1" onclick="this.form.action_type.value='approved'" class="btn btn-sm btn-success fw-bold px-3">
                                                        <i class="fa-solid fa-check me-1"></i> Approve
                                                    </button>
                                                    <input type="hidden" name="action_type" value="">
                                                    <button type="submit" name="action_cert" value="1" onclick="this.form.action_type.value='rejected'" class="btn btn-sm btn-outline-danger fw-bold px-3">
                                                        <i class="fa-solid fa-xmark me-1"></i> Reject
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Action completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">No certificate requests found from students yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>