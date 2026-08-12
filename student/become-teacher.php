<?php
session_start();
require_once '../config/db.php'; 

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
<body class="bg-light">

    <section class="become-teacher-section py-5 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        
                        <!-- Header -->
                        <div class="text-center mb-5">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-size: 1.75rem;">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <h2 class="fw-bold text-dark">Join Our Faculty Ecosystem</h2>
                            <p class="text-muted">Share your expertise, mentor ambitious learners, and build your professional teaching portfolio on SLTCP.</p>
                        </div>

                        <!-- Flash Messages -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger rounded-3 mb-4 fw-medium">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Application Form -->
                        <form action="../backend/teacher-application-process.php" method="POST" class="needs-validation" novalidate>
                            
                            <div class="mb-4">
                                <label for="expertise" class="form-label fw-bold text-dark">Area of Expertise / Subjects <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-book"></i></span>
                                    <input type="text" id="expertise" name="expertise" class="form-control bg-light border-start-0 py-3" placeholder="E.g., Physics, Higher Mathematics" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="headline" class="form-label fw-bold text-dark">Professional Headline <span class="text-danger">*</span></label>
                                <input type="text" id="headline" name="headline" class="form-control bg-light py-3" placeholder="E.g., Senior Physics & Mathematics Expert (BUET)" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="hourly_rate" class="form-label fw-bold text-dark">Hourly Rate (BDT) <span class="text-danger">*</span></label>
                                    <input type="number" id="hourly_rate" name="hourly_rate" class="form-control bg-light py-3" placeholder="800" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="location" class="form-label fw-bold text-dark">Location <span class="text-danger">*</span></label>
                                    <input type="text" id="location" name="location" class="form-control bg-light py-3" placeholder="E.g., Dhanmondi, Dhaka" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="languages" class="form-label fw-bold text-dark">Languages Known <span class="text-danger">*</span></label>
                                    <input type="text" id="languages" name="languages" class="form-control bg-light py-3" placeholder="English & Bengali" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="study_mode" class="form-label fw-bold text-dark">Study Mode <span class="text-danger">*</span></label>
                                    <input type="text" id="study_mode" name="study_mode" class="form-control bg-light py-3" placeholder="Online & Offline" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="qualification" class="form-label fw-bold text-dark">Highest Educational Qualification <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <input type="text" id="qualification" name="qualification" class="form-control bg-light border-start-0 py-3" placeholder="E.g., B.Sc in EEE from BUET" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="experience" class="form-label fw-bold text-dark">Teaching Experience & Background (Bio) <span class="text-danger">*</span></label>
                                <textarea id="experience" name="experience" rows="4" class="form-control bg-light py-3" placeholder="Briefly describe your background, years of experience, and teaching methodology..." required></textarea>
                            </div>

                            <div class="d-flex gap-3 justify-content-between align-items-center mt-5">
                                <a href="dashboard.php" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-bold">Cancel</a>
                                <button type="submit" class="btn btn-primary px-5 py-3 rounded-3 fw-bold shadow-sm">Submit Application</button>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- jQuery & Bootstrap JS -->
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>