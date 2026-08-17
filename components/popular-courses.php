<?php
// Make sure database connection file is included if not already
if (!isset($pdo)) {
    // Adjust path based on where index.php or parent file is located 
    // (Jodi components folder root e thake, tabole 'config/db.php' hobe)
    require_once __DIR__ . '/../config/db.php'; 
}

// Fetch popular courses from database along with tutor info, total lessons count, and total enrolled students count
try {
    $pop_courses_stmt = $pdo->prepare("
        SELECT c.*, u.full_name AS tutor_name, u.avatar AS tutor_avatar,
               (SELECT COUNT(*) FROM course_lessons WHERE course_id = c.id) AS lesson_count,
               (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS student_count
        FROM courses c 
        JOIN users u ON c.tutor_id = u.id 
        ORDER BY student_count DESC, c.id DESC 
        LIMIT 8
    ");
    $pop_courses_stmt->execute();
    $popular_courses = $pop_courses_stmt->fetchAll();
} catch (PDOException $e) {
    $popular_courses = [];
}
?>

<section class="popular-courses-section">
    <div class="container">

        <div class="section-header d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge-top">Top Rated Classes</span>
                <h2 class="section-title">Explore Our Popular Courses</h2>
            </div>
            <!-- Slider controllers perfectly aligned right -->
            <div class="course-slider-nav d-none d-md-flex">
                <div class="course-prev"><i class="fa-solid fa-arrow-left"></i></div>
                <div class="course-next"><i class="fa-solid fa-arrow-right"></i></div>
            </div>
        </div>

        <!-- Swiper Container -->
        <div class="swiper popular-courses-swiper">
            <div class="swiper-wrapper">

                <?php if (count($popular_courses) > 0): ?>
                    <?php foreach ($popular_courses as $course): 
                        // Author name and fallback letter
                        $author_name = $course['tutor_name'] ?? 'Instructor';
                        $first_letter = strtoupper(substr($author_name, 0, 1));
                        
                        // Author avatar handling
                        $tutor_avatar = $course['tutor_avatar'] ?? '';
                        if (!empty($tutor_avatar) && $tutor_avatar !== 'default-avatar.png') {
                            $avatar_path = (str_starts_with($tutor_avatar, 'assets/')) ? $tutor_avatar : 'assets/img/profiles/' . $tutor_avatar;
                        } else {
                            $avatar_path = '';
                        }

                        // Course image handling
                        $course_img = $course['image'] ?? '';
                        if (empty($course_img)) {
                            $img_url = 'assets/img/popular_courses/img1.avif';
                        } elseif (str_starts_with($course_img, 'http')) {
                            $img_url = $course_img;
                        } else {
                            $img_url = 'assets/img/courses/' . $course_img;
                        }

                        // Pricing calculation (discount vs regular)
                        $display_price = !empty($course['discount_price']) && $course['discount_price'] > 0 ? $course['discount_price'] : $course['price'];
                    ?>
                        <!-- Dynamic Slide -->
                        <div class="swiper-slide">
                            <div class="edura-course-card">
                                <div class="card-thumb">
                                    <img src="<?php echo htmlspecialchars($img_url); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" onerror="this.src='assets/img/popular_courses/img1.avif';">
                                    <span class="time-badge"><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($course['duration'] ?? '6h 0m'); ?></span>
                                </div>
                                <div class="card-body-content">
                                    <div class="rating-layer d-flex align-items-center gap-1">
                                        <div class="stars">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                        </div>
                                        <span class="rating-text">(5.00)</span>
                                    </div>
                                    <h4 class="course-title-text"><a href="course-details.php?id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a></h4>
                                    <div class="course-specs d-flex align-items-center justify-content-between">
                                        <span><i class="fa-regular fa-file-lines"></i> Lesson <?php echo $course['lesson_count']; ?></span>
                                        <span><i class="fa-regular fa-user"></i> Students <?php echo $course['student_count']; ?></span>
                                        <span><i class="fa-solid fa-chart-simple"></i> <?php echo htmlspecialchars($course['level'] ?? 'All Levels'); ?></span>
                                    </div>
                                    <div class="card-footer-layer d-flex align-items-center justify-content-between">
                                        <div class="author-block d-flex align-items-center gap-2">
                                            <?php if (!empty($avatar_path) && file_exists($avatar_path)): ?>
                                                <img src="<?php echo htmlspecialchars($avatar_path); ?>" alt="Author" class="rounded-circle object-fit-cover" width="30" height="30">
                                            <?php else: ?>
                                                <div class="author-avatar-fallback"><?php echo $first_letter; ?></div>
                                            <?php endif; ?>
                                            <span class="author-name"><?php echo htmlspecialchars($author_name); ?></span>
                                        </div>
                                        <div class="price-tag">৳ <?php echo number_format($display_price, 2); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swiper-slide">
                        <div class="text-center py-4 text-muted">No popular courses found at the moment.</div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</section>