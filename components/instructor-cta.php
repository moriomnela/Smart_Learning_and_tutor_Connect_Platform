<?php
// Make sure database connection file and session are active
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/db.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine button link and text based on user login status and role
$cta_link = "login.php";
$cta_text = "Start Teaching Today";

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    $role = $_SESSION['role'] ?? '';
    
    if ($role === 'student') {
        $cta_link = "student/become-teacher.php";
        $cta_text = "Apply as an Instructor";
    } elseif ($role === 'tutor') {
        $cta_link = "tutor/dashboard.php";
        $cta_text = "Go to Tutor Dashboard";
    } elseif ($role === 'admin') {
        $cta_link = "admin/dashboard.php";
        $cta_text = "Go to Admin Dashboard";
    }
}
?>

<section class="instructor-cta-section">
    <div class="container">
        <div class="cta-card-wrapper">
            <div class="cta-mesh-bg"></div>

            <div class="row align-items-center position-relative" style="z-index: 5;">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="cta-badge">
                        <i class="fa-solid fa-graduation-cap"></i> Become an Instructor
                    </div>
                    <h2 class="cta-main-title">Teach the Next Generation. <span class="gradient-text">Earn on Your Terms.</span></h2>
                    <p class="cta-paragraph">Join a premium network of global educators. Share your expertise, launch high-impact courses, and build a sustainable scalable business with our advanced instructor suite.</p>

                    <div class="cta-perks-list">
                        <div class="perk-item"><i class="fa-solid fa-circle-check"></i> <span>Keep up to 85% revenue split</span></div>
                        <div class="perk-item"><i class="fa-solid fa-circle-check"></i> <span>Advanced live analytics dashboard</span></div>
                    </div>

                    <a href="<?php echo htmlspecialchars($cta_link); ?>" class="cta-premium-btn">
                        <span><?php echo htmlspecialchars($cta_text); ?></span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="cta-visual-container">
                        <div class="main-image-frame">
                            <img src="assets/img/popular_teacher/teacher4.avif" alt="Instructor Platform" class="instructor-main-img">
                        </div>

                        <div class="floating-stat-card">
                            <div class="stat-icon-wrapper"><i class="fa-solid fa-chart-line"></i></div>
                            <div class="stat-info">
                                <span class="label">Total Instructor Payout</span>
                                <h4 class="amount">$248,500+</h4>
                            </div>
                        </div>

                        <div class="floating-badge-tag">
                            <span class="pulse-dot"></span> Live in Dhaka
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>