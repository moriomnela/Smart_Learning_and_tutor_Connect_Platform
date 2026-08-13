<?php
// Output buffering ba session jodi template-top e thake, tahoke ekhane dorkar nai, 
// tobu safety r jonno check rakha valo.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Get course ID from URL
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // 1. Fetch course details along with tutor information (JOIN for dynamic avatar)
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name AS tutor_name, u.avatar AS tutor_avatar 
        FROM courses c 
        JOIN users u ON c.tutor_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        // Since headers are already sent by template-top, use JS/HTML fallback or safe exit
        echo '<div class="container py-5 text-center"><div class="alert alert-danger">Course not found! <a href="courses.php">Back to Courses</a></div></div>';
        exit;
    }

    // 2. Fetch existing lessons, notes, quizzes, live classes for this course
    $lessons = $pdo->prepare("SELECT * FROM course_lessons WHERE course_id = ?"); 
    $lessons->execute([$course_id]); 
    $lessons = $lessons->fetchAll();

    $notes = $pdo->prepare("SELECT * FROM course_notes WHERE course_id = ?"); 
    $notes->execute([$course_id]); 
    $notes = $notes->fetchAll();

    $quizzes = $pdo->prepare("SELECT * FROM course_quizzes WHERE course_id = ?"); 
    $quizzes->execute([$course_id]); 
    $quizzes = $quizzes->fetchAll();

    $live_classes = $pdo->prepare("SELECT * FROM live_classes WHERE course_id = ?"); 
    $live_classes->execute([$course_id]); 
    $live_classes = $live_classes->fetchAll();

    // 3. Check if current student is enrolled
    $is_enrolled = false;
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
        $en_stmt = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
        $en_stmt->execute([$_SESSION['user_id'], $course_id]);
        $is_enrolled = $en_stmt->fetch();
    }

} catch (PDOException $e) {
    echo '<div class="container py-5 text-center"><div class="alert alert-danger">Database error occurred.</div></div>';
    exit;
}

// Convert comma-separated learning outcomes into an array
$outcomes = !empty($course['learning_outcomes']) ? explode(',', $course['learning_outcomes']) : [];

// Format Tutor Avatar path dynamically
$tutor_avatar = $course['tutor_avatar'] ?? 'default-avatar.png';
if ($tutor_avatar !== 'default-avatar.png' && !str_starts_with($tutor_avatar, 'assets/')) {
    $tutor_img_path = 'assets/img/profiles/' . $tutor_avatar;
} else {
    $tutor_img_path = ($tutor_avatar === 'default-avatar.png') ? 'assets/img/profiles/default-avatar.png' : $tutor_avatar;
}

// Fetch chapters and quizzes for this course
    $chapters = $pdo->prepare("SELECT * FROM course_chapters WHERE course_id = ?"); 
    $chapters->execute([$course_id]); 
    $chapters = $chapters->fetchAll();

    $quizzes = $pdo->prepare("SELECT * FROM course_quizzes WHERE course_id = ?"); 
    $quizzes->execute([$course_id]); 
    $quizzes = $quizzes->fetchAll();

$page_title = $course['title'];
?>

<section class="course-details-section py-5">
    <div class="container">
        
        <!-- Course Title & Basic Info (Top Area) -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="course-header">
                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($course['subtitle']); ?></span>
                    <h1 class="course-title text-dark fw-bold mb-3"><?php echo htmlspecialchars($course['title']); ?></h1>
                    
                    <div class="course-meta d-flex flex-wrap gap-4 align-items-center text-muted">
                        <div class="instructor d-flex align-items-center gap-2">
                            <img src="<?php echo htmlspecialchars($tutor_img_path); ?>" alt="Instructor" class="rounded-circle object-fit-cover" width="45" height="45" onerror="this.src='assets/img/profiles/default-avatar.png'">
                            <span class="text-dark fw-bold"><?php echo htmlspecialchars($course['tutor_name']); ?></span>
                        </div>
                        <div class="rating">
                            <i class="fa-solid fa-star text-warning"></i>
                            <i class="fa-solid fa-star text-warning"></i>
                            <i class="fa-solid fa-star text-warning"></i>
                            <i class="fa-solid fa-star text-warning"></i>
                            <i class="fa-solid fa-star-half-stroke text-warning"></i>
                            <span class="ms-1 text-dark fw-bold">4.8</span> (1,240 Reviews)
                        </div>
                        <div class="students">
                            <i class="fa-solid fa-user-graduate me-1 text-primary"></i> 5,430 Students
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <!-- Left Side: Main Content -->
            <div class="col-lg-8">
                
                <!-- Course Preview using Magnific Popup -->
                <div class="course-preview-img mb-5 rounded-4 overflow-hidden shadow-sm position-relative bg-dark">
                    <?php if (!empty($lessons) && !empty($lessons[0]['video_url'])): ?>
                        <?php 
                            $raw_preview_url = $lessons[0]['video_url'];
                            $modal_video_url = $raw_preview_url;
                            if (strpos($raw_preview_url, 'watch?v=') !== false) {
                                parse_str(parse_url($raw_preview_url, PHP_URL_QUERY), $p_params);
                                if (isset($p_params['v'])) {
                                    $modal_video_url = "https://www.youtube.com/watch?v=" . $p_params['v'];
                                }
                            }
                            $thumb_img = !empty($course['image']) ? 'assets/img/courses/' . $course['image'] : 'https://dummyimage.com/800x450/1e3a8a/ffffff.jpg';
                        ?>
                        <a href="<?php echo htmlspecialchars($modal_video_url); ?>" class="popup-youtube text-decoration-none position-relative d-block text-center" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?php echo htmlspecialchars($thumb_img); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-fluid w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover; opacity: 0.7;">
                            <div class="z-1 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg position-absolute top-50 start-50 translate-middle" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-play fs-3 ms-1"></i>
                            </div>
                            <div class="position-absolute bottom-0 start-0 p-3 text-white z-1 bg-dark bg-opacity-75 w-100 text-start">
                                <span class="badge bg-primary mb-1">Free Preview</span>
                                <h5 class="mb-0 fw-bold text-white"><?php echo htmlspecialchars($lessons[0]['title']); ?></h5>
                            </div>
                        </a>
                    <?php else: ?>
                        <img src="assets/img/courses/<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-fluid w-100" style="max-height: 450px; object-fit: cover;">
                    <?php endif; ?>
                </div>

                <!-- What You Will Learn -->
                <?php if (!empty($outcomes)): ?>
                <div class="course-section mb-5">
                    <h3 class="section-heading mb-4 fw-bold">What You Will Learn</h3>
                    <div class="learning-list p-4 rounded-4 bg-white shadow-sm border">
                        <div class="row g-3">
                            <?php foreach ($outcomes as $outcome): ?>
                                <div class="col-md-6 d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-check text-success mt-1"></i>
                                    <span><?php echo htmlspecialchars(trim($outcome)); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Course Description -->
                <div class="course-section mb-5">
                    <h3 class="section-heading mb-3 fw-bold">Course Description</h3>
                    <div class="bg-white p-4 rounded-4 shadow-sm border text-muted line-height-lg">
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                    </div>
                </div>

                <!-- Course Contents & Curriculum -->
                <div class="course-section mb-5">
                    <h3 class="section-heading mb-4 fw-bold">Course Contents & Curriculum</h3>
                    
                    <ul class="nav nav-pills mb-3 gap-2" id="courseContentTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active fw-bold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tab-lessons" type="button">Video Lessons (<?php echo count($lessons); ?>)</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tab-notes" type="button">Study Notes (<?php echo count($notes); ?>)</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tab-quizzes" type="button">Quizzes (<?php echo count($quizzes); ?>)</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold px-3 py-2" data-bs-toggle="tab" data-bs-target="#tab-live" type="button">Live Classes (<?php echo count($live_classes); ?>)</button></li>
                    </ul>

                    <div class="tab-content bg-white p-4 rounded-4 shadow-sm border" id="courseContentTabContent">
                        
                        <!-- TAB 1: LESSONS -->
                        <div class="tab-pane fade show active" id="tab-lessons">
                            <h5 class="fw-bold mb-3">Video Lessons</h5>
                            <?php if (!empty($lessons)): ?>
                                <div class="list-group">
                                    <?php foreach ($lessons as $index => $l): 
                                        $is_free_preview = ($index === 0);
                                        $can_view = $is_free_preview || $is_enrolled || (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'tutor']));
                                    ?>
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 border">
                                            <div>
                                                <h6 class="fw-bold mb-1"><?php echo ($index + 1) . '. ' . htmlspecialchars($l['title']); ?></h6>
                                                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> <?php echo htmlspecialchars($l['duration'] ?? '10 mins'); ?></small>
                                            </div>
                                            <div>
                                                <?php if ($can_view): ?>
                                                    <a href="<?php echo htmlspecialchars($l['video_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                                                        <i class="fa-solid fa-play me-1"></i> Watch <?php echo $is_free_preview ? '(Free Preview)' : ''; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-lock me-1"></i> Locked (Enroll to Watch)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No video lessons available yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- TAB 2: NOTES -->
                        <div class="tab-pane fade" id="tab-notes">
                            <h5 class="fw-bold mb-3">Study Notes & Materials</h5>
                            <?php if (!empty($notes)): ?>
                                <div class="list-group">
                                    <?php foreach ($notes as $n): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 border">
                                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($n['title']); ?></h6>
                                            <?php if ($is_enrolled || (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'tutor']))): ?>
                                                <a href="<?php echo htmlspecialchars($n['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-download me-1"></i> Download Note</a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-lock me-1"></i> Locked</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No study notes uploaded yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- TAB 3: QUIZZES -->
                        <div class="tab-pane fade" id="tab-quizzes">
                            <h5 class="fw-bold mb-3">Course Quizzes & Assessments</h5>
                            <?php if (!empty($chapters)): ?>
                                <?php if ($is_enrolled || (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'tutor']))): ?>
                                    <div class="accordion" id="chapterQuizAccordion">
                                        <?php foreach ($chapters as $index => $c): ?>
                                            <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                                                <h2 class="accordion-header" id="headingChap<?php echo $c['id']; ?>">
                                                    <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?> fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChap<?php echo $c['id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapseChap<?php echo $c['id']; ?>">
                                                        <i class="fa-solid fa-book-open me-2 text-primary"></i> <?php echo htmlspecialchars($c['chapter_name']); ?>
                                                    </button>
                                                </h2>
                                                <div id="collapseChap<?php echo $c['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="headingChap<?php echo $c['id']; ?>" data-bs-parent="#chapterQuizAccordion">
                                                    <div class="accordion-body bg-light">
                                                        <?php 
                                                            $q_in_chapter = [];
                                                            foreach($quizzes as $q) {
                                                                if(isset($q['chapter_id']) && $q['chapter_id'] == $c['id']) {
                                                                    $q_in_chapter[] = $q;
                                                                }
                                                            }
                                                        ?>

                                                        <?php if (!empty($q_in_chapter)): ?>
                                                            <div class="accordion" id="innerQuizAccordion<?php echo $c['id']; ?>">
                                                                <?php foreach ($q_in_chapter as $qi => $q): ?>
                                                                    <div class="accordion-item mb-2 border rounded-3 overflow-hidden">
                                                                        <h2 class="accordion-header" id="headingQ<?php echo $c['id'] . '_' . $qi; ?>">
                                                                            <button class="accordion-button collapsed fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ<?php echo $c['id'] . '_' . $qi; ?>">
                                                                                Q<?php echo ($qi + 1) . ': ' . htmlspecialchars($q['question']); ?>
                                                                            </button>
                                                                        </h2>
                                                                        <div id="collapseQ<?php echo $c['id'] . '_' . $qi; ?>" class="accordion-collapse collapse" data-bs-parent="#innerQuizAccordion<?php echo $c['id']; ?>">
                                                                            <div class="accordion-body bg-white">
                                                                                <ul class="list-unstyled mb-2 small">
                                                                                    <li><strong>A.</strong> <?php echo htmlspecialchars($q['option_a']); ?></li>
                                                                                    <li><strong>B.</strong> <?php echo htmlspecialchars($q['option_b']); ?></li>
                                                                                    <li><strong>C.</strong> <?php echo htmlspecialchars($q['option_c']); ?></li>
                                                                                    <li><strong>D.</strong> <?php echo htmlspecialchars($q['option_d']); ?></li>
                                                                                </ul>
                                                                                <span class="badge bg-success">Correct Option: <?php echo strtoupper($q['correct_option']); ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <p class="text-muted small mb-0 text-center py-2">No quizzes available in this chapter yet.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning mb-0"><i class="fa-solid fa-lock me-2"></i> Enroll in this course to take quizzes and check your answers.</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0">No chapters or quizzes available for this course yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- TAB 4: LIVE CLASSES -->
                        <div class="tab-pane fade" id="tab-live">
                            <h5 class="fw-bold mb-3">Scheduled Live Classes</h5>
                            <?php if (!empty($live_classes)): ?>
                                <div class="list-group">
                                    <?php foreach ($live_classes as $lc): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 border">
                                            <div>
                                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($lc['title']); ?></h6>
                                                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> <?php echo date('M d, Y - h:i A', strtotime($lc['schedule_time'])); ?></small>
                                            </div>
                                            <div>
                                                <?php if ($is_enrolled || (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'tutor']))): ?>
                                                    <a href="<?php echo htmlspecialchars($lc['meeting_link']); ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-video me-1"></i> Join Class</a>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-lock me-1"></i> Locked</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No live classes scheduled yet.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Side: Sticky Sidebar Widget -->
            <div class="col-lg-4">
                <div class="course-sidebar-widget p-4 bg-white rounded-4 shadow-lg border sticky-top" style="top: 90px;">
                    <div class="price-area mb-4">
                        <h2 class="fw-bold text-dark mb-0">৳ <?php echo number_format($course['price'], 2); ?></h2>
                        <?php if (!empty($course['discount_price']) && $course['discount_price'] > 0): ?>
                            <span class="text-decoration-line-through text-muted ms-2 fs-5">৳ <?php echo number_format($course['discount_price'], 2); ?></span>
                            <?php 
                                $discount = (($course['discount_price'] - $course['price']) / $course['discount_price']) * 100;
                                if ($discount > 0):
                            ?>
                                <span class="badge bg-danger ms-2"><?php echo round($discount); ?>% OFF</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="action-buttons d-flex flex-column gap-3 mb-4">
                        <?php if ($is_enrolled): ?>
                            <div class="alert alert-success text-center fw-bold mb-0 py-3">You are already enrolled!</div>
                        <?php elseif (isset($_SESSION['is_logged_in']) && $_SESSION['role'] === 'student'): ?>
                            <a href="backend/enroll-process.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary w-100 py-3 fw-bold fs-5 rounded-3 text-white text-center text-decoration-none">Enroll Now</a>
                            <a href="backend/add-to-cart.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-secondary w-100 py-3 fw-bold rounded-3">Add to Cart</a>
                        <?php elseif (isset($_SESSION['is_logged_in']) && $_SESSION['role'] !== 'student'): ?>
                            <p class="text-muted text-center small mb-0">Tutors or Admins cannot enroll in courses.</p>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary w-100 py-3 fw-bold fs-5 rounded-3 text-white text-center text-decoration-none">Login to Enroll</a>
                            <a href="backend/add-to-cart.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-secondary w-100 py-3 fw-bold rounded-3">Add to Cart</a>
                        <?php endif; ?>
                    </div>

                    <div class="course-includes">
                        <h5 class="fw-bold mb-3">This course includes:</h5>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-0 text-muted">
                            <li><i class="fa-solid fa-video me-3 text-primary"></i> <?php echo count($lessons); ?> Video Lessons</li>
                            <li><i class="fa-solid fa-file-lines me-3 text-primary"></i> <?php echo count($notes); ?> Study Notes</li>
                            <li><i class="fa-solid fa-question-circle me-3 text-primary"></i> <?php echo count($quizzes); ?> Quizzes & Assessments</li>
                            <li><i class="fa-solid fa-chalkboard-user me-3 text-primary"></i> <?php echo count($live_classes); ?> Live Classes</li>
                            <li><i class="fa-solid fa-certificate me-3 text-primary"></i> Certificate of completion</li>
                        </ul>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Magnific Popup Activation Script for this component -->
<script>
    $(document).ready(function() {
        $('.popup-youtube').magnificPopup({
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: true,
            callbacks: {
                open: function() { $('body').css('overflow', 'hidden'); },
                close: function() { $('body').css('overflow', 'auto'); }
            }
        });
    });
</script>