<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Delete Message
if (isset($_GET['delete_id'])) {
    $msg_id = intval($_GET['delete_id']);
    try {
        $del_stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $del_stmt->execute([$msg_id]);
        $_SESSION['success'] = "Message deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete message.";
    }
    header("Location: contacts.php");
    exit;
}

// Mark as Read / Unread toggle if needed, or fetch all messages
try {
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY id DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $messages = [];
}

$page_title = "Admin - Contact Messages";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Contact Messages</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Admin Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px;height: 100vh;position: sticky;top: 0;overflow: auto;scrollbar-width: thin;scrollbar-color: transparent transparent;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Admin</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chart-line me-2"></i> Overview</a></li>
            <li><a href="site-stat.php" class="nav-link p-2 rounded text-black"><i class="fa-solid fa-chart-pie me-2"></i> Site Statistics</a></li>
            <li><a href="applications.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Teacher Applications</a></li>
            <li><a href="manage-teachers.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-tie me-2"></i> Manage Teachers</a></li>
            <li><a href="manage-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> Manage Courses</a></li>
            <li><a href="withdrawals.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-money-bill-transfer me-2"></i> Withdrawals</a></li>
            <li><a href="earning.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Admin Earnings</a></li>
            <li><a href="manage-students.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-graduate me-2"></i> Manage Students</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Write Blog</a></li>
            <li><a href="manage-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> Manage Blogs</a></li>
            <li><a href="contacts.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-envelope-open-text me-2"></i> Messages</a></li>
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
        <div class="mb-4">
            <h2 class="fw-bold text-dark">User Support Messages</h2>
            <p class="text-muted">Review inquiries and messages sent through the contact form.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Messages Table Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sender Info</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($messages)): ?>
                            <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($msg['name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($msg['email']); ?></small>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark"><?php echo htmlspecialchars($msg['subject']); ?></span>
                                </td>
                                <td>
                                    <div class="text-muted text-truncate" style="max-width: 300px;">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo date('M d, Y - h:i A', strtotime($msg['created_at'])); ?></small>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary fw-bold px-2 me-1" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $msg['id']; ?>" title="View Message">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="contacts.php?delete_id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold px-2" onclick="return confirm('Are you sure you want to delete this message?');" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- View Message Modal -->
                            <div class="modal fade" id="viewModal<?php echo $msg['id']; ?>" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                  <div class="modal-body p-4">
                                      <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($msg['subject']); ?></h5>
                                      <p class="text-muted small mb-3">From: <strong><?php echo htmlspecialchars($msg['name']); ?></strong> (&lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;)</p>
                                      <hr>
                                      <p class="text-dark bg-light p-3 rounded-3 mb-3"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                      <small class="text-muted">Received on: <?php echo date('M d, Y - h:i A', strtotime($msg['created_at'])); ?></small>
                                  </div>
                                  <div class="modal-footer border-0">
                                      <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="btn btn-primary btn-sm px-4">Reply via Email</a>
                                      <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="fa-solid fa-inbox fs-1 text-secondary opacity-50"></i></div>
                                    <p class="mb-0">No messages found.</p>
                                </td>
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