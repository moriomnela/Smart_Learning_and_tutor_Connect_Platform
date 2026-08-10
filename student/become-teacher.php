<?php
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../SLTCP/login.php");
    exit;
}

$page_title = "Apply as a Teacher";
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

<section class="become-teacher-section py-5 bg-light min-vh-100 d-flex align-items-center">
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
                                <input type="text" id="expertise" name="expertise" class="form-control bg-light border-start-0 py-3" placeholder="E.g., Physics, Higher Mathematics, Full-Stack Development" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="qualification" class="form-label fw-bold text-dark">Highest Educational Qualification <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-graduation-cap"></i></span>
                                <input type="text" id="qualification" name="qualification" class="form-control bg-light border-start-0 py-3" placeholder="E.g., B.Sc in EEE from BUET, M.Sc from Dhaka University" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="experience" class="form-label fw-bold text-dark">Teaching Experience & Background <span class="text-danger">*</span></label>
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