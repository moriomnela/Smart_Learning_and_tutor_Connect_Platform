<?php
// Make sure database connection file is included
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/db.php';
}

// Fetch top tutors/instructors from database along with their total published courses count
try {
    $teachers_stmt = $pdo->prepare("
        SELECT u.*, 
               (SELECT COUNT(*) FROM courses WHERE tutor_id = u.id) AS course_count
        FROM users u 
        WHERE u.role = 'tutor' 
        ORDER BY course_count DESC, u.id DESC 
        LIMIT 8
    ");
    $teachers_stmt->execute();
    $popular_teachers = $teachers_stmt->fetchAll();
} catch (PDOException $e) {
    $popular_teachers = [];
}
?>

<section class="popular-teachers-section">
    <div class="container">

        <div class="section-header d-flex justify-content-between align-items-end mb-5">
            <div>
                <span class="badge-top">Elite Faculty</span>
                <h2 class="section-title">Learn From Top <span class="highlight-text">Instructors</span></h2>
            </div>
            <div class="teacher-slider-nav d-none d-md-flex">
                <div class="teacher-prev"><i class="fa-solid fa-arrow-left"></i></div>
                <div class="teacher-next"><i class="fa-solid fa-arrow-right"></i></div>
            </div>
        </div>

        <div class="swiper popular-teachers-swiper">
            <div class="swiper-wrapper">

                <?php if (count($popular_teachers) > 0): ?>
                    <?php foreach ($popular_teachers as $teacher): 
                        // Teacher avatar handling
                        $avatar = $teacher['avatar'] ?? 'default-avatar.png';
                        if ($avatar === 'default-avatar.png' || empty($avatar)) {
                            $avatar_url = 'assets/img/profiles/default-avatar.png';
                        } elseif (str_starts_with($avatar, 'assets/')) {
                            $avatar_url = $avatar;
                        } else {
                            $avatar_url = 'assets/img/profiles/' . $avatar;
                        }

                        $teacher_name = $teacher['full_name'] ?? 'Instructor';
                        $teacher_headline = $teacher['headline'] ?? 'Expert Instructor & Mentor';
                        $total_courses = $teacher['course_count'] ?? 0;
                    ?>
                        <div class="swiper-slide">
                            <div class="premium-teacher-card">
                                <div class="teacher-thumb-box">
                                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="<?php echo htmlspecialchars($teacher_name); ?>" class="teacher-img object-fit-cover" onerror="this.src='assets/img/profiles/default-avatar.png';">

                                    <div class="social-flyout">
                                        <a href="<?php echo !empty($teacher['linkedin']) ? htmlspecialchars($teacher['linkedin']) : '#'; ?>" class="social-link" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                                        <a href="<?php echo !empty($teacher['facebook']) ? htmlspecialchars($teacher['facebook']) : '#'; ?>" class="social-link" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                                    </div>
                                    <span class="courses-badge"><?php echo $total_courses; ?> Course<?php echo $total_courses == 1 ? '' : 's'; ?></span>
                                </div>

                                <div class="teacher-meta-content">
                                    <h4 class="teacher-name"><?php echo htmlspecialchars($teacher_name); ?></h4>
                                    <p class="teacher-subject"><?php echo htmlspecialchars($teacher_headline); ?></p>
                                    <a href="tutor-details.php?id=<?php echo $teacher['id']; ?>" class="btn-profile-trigger">
                                        <span>View Full Profile</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swiper-slide">
                        <div class="text-center py-4 text-muted w-100">No popular instructors found at the moment.</div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</section>