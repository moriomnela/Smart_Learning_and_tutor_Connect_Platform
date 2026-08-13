<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in students
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Verify if student is actually enrolled in this course
$en_check = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$en_check->execute([$student_id, $course_id]);
if (!$en_check->fetch()) {
    $_SESSION['error'] = "You are not enrolled in this course!";
    header("Location: my-courses.php");
    exit;
}

// 2. Fetch Course & Tutor details
$course_stmt = $pdo->prepare("SELECT c.*, u.full_name AS tutor_name FROM courses c JOIN users u ON c.tutor_id = u.id WHERE c.id = ?");
$course_stmt->execute([$course_id]);
$course = $course_stmt->fetch();

if (!$course) {
    header("Location: my-courses.php");
    exit;
}

// 3. Handle Lesson Completion Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_lesson'])) {
    $lesson_id = intval($_POST['lesson_id']);
    
    $chk = $pdo->prepare("SELECT * FROM lesson_progress WHERE student_id = ? AND course_id = ? AND lesson_id = ?");
    $chk->execute([$student_id, $course_id, $lesson_id]);
    
    if (!$chk->fetch()) {
        $ins = $pdo->prepare("INSERT INTO lesson_progress (student_id, course_id, lesson_id) VALUES (?, ?, ?)");
        $ins->execute([$student_id, $course_id, $lesson_id]);
    }
    header("Location: learn-course.php?id=" . $course_id . "&lesson=" . $lesson_id);
    exit;
}

// 4. Fetch Course Contents
$lessons = $pdo->prepare("SELECT * FROM course_lessons WHERE course_id = ?");
$lessons->execute([$course_id]);
$lessons = $lessons->fetchAll();

$chapters = $pdo->prepare("SELECT * FROM course_chapters WHERE course_id = ?");
$chapters->execute([$course_id]);
$chapters = $chapters->fetchAll();

$quizzes = $pdo->prepare("SELECT * FROM course_quizzes WHERE course_id = ?");
$quizzes->execute([$course_id]);
$quizzes = $quizzes->fetchAll();

$notes = $pdo->prepare("SELECT * FROM course_notes WHERE course_id = ?");
$notes->execute([$course_id]);
$notes = $notes->fetchAll();

$live_classes = $pdo->prepare("SELECT * FROM live_classes WHERE course_id = ?");
$live_classes->execute([$course_id]);
$live_classes = $live_classes->fetchAll();

// 5. Fetch Completed Lessons for Progress Tracking
$prog_stmt = $pdo->prepare("SELECT lesson_id FROM lesson_progress WHERE student_id = ? AND course_id = ?");
$prog_stmt->execute([$student_id, $course_id]);
$completed_lessons = $prog_stmt->fetchAll(PDO::FETCH_COLUMN);

$total_lessons = count($lessons);
$completed_count = count($completed_lessons);
$progress_percent = ($total_lessons > 0) ? round(($completed_count / $total_lessons) * 100) : 0;

// 6. Active Selected Lesson
$current_lesson_id = isset($_GET['lesson']) ? intval($_GET['lesson']) : (!empty($lessons) ? $lessons[0]['id'] : 0);
$active_lesson = null;
foreach ($lessons as $l) {
    if ($l['id'] == $current_lesson_id) {
        $active_lesson = $l;
        break;
    }
}
if (!$active_lesson && !empty($lessons)) {
    $active_lesson = $lessons[0];
}

// 7. Handle Quiz Submission (Ekbar submit korle abar submit korar option thakbe na, result ar answers dekhabe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $chapter_id = intval($_POST['chapter_id']);
    
    // Check if already attempted/submitted
    $chk_res = $pdo->prepare("SELECT id FROM quiz_results WHERE student_id = ? AND chapter_id = ?");
    $chk_res->execute([$student_id, $chapter_id]);
    
    if (!$chk_res->fetch()) {
        $submitted_answers = $_POST['answers'] ?? [];
        
        $q_stmt = $pdo->prepare("SELECT * FROM course_quizzes WHERE chapter_id = ?");
        $q_stmt->execute([$chapter_id]);
        $chap_quizzes = $q_stmt->fetchAll();
        
        $score = 0;
        $total_q = count($chap_quizzes);
        
        // Save detailed answers if needed, or just score
        $ins = $pdo->prepare("INSERT INTO quiz_results (student_id, course_id, chapter_id, score, total_questions) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$student_id, $course_id, $chapter_id, 0, $total_q]); // temporary or calculate score
        $result_id = $pdo->lastInsertId();

        foreach ($chap_quizzes as $q) {
            $qid = $q['id'];
            $user_ans = $submitted_answers[$qid] ?? '';
            $is_correct = (strtolower(trim($user_ans)) === strtolower(trim($q['correct_option']))) ? 1 : 0;
            if ($is_correct) $score++;

            // Save individual answer choice
            $ans_stmt = $pdo->prepare("INSERT INTO student_quiz_answers (student_id, quiz_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
            // Note: Make sure student_quiz_answers table exists or handle via JSON/session. Alternatively, we update total score directly.
        }

        // Update exact score
        $upd = $pdo->prepare("UPDATE quiz_results SET score = ? WHERE id = ?");
        $upd->execute([$score, $result_id]);

        $_SESSION['quiz_msg'] = "Quiz submitted successfully! Your Score: $score / $total_q";
    }
    
    header("Location: learn-course.php?id=" . $course_id);
    exit;
}

// 8. Check and Issue Certificate if 100% completed
if ($progress_percent == 100) {
    $cert_chk = $pdo->prepare("SELECT * FROM certificates WHERE student_id = ? AND course_id = ?");
    $cert_chk->execute([$student_id, $course_id]);
    if (!$cert_chk->fetch()) {
        $cert_code = "SLTCP-CERT-" . strtoupper(uniqid());
        $ins_cert = $pdo->prepare("INSERT INTO certificates (student_id, course_id, certificate_code) VALUES (?, ?, ?)");
        $ins_cert->execute([$student_id, $course_id, $cert_code]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - <?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<!-- Top Learning Navbar -->
<nav class="navbar navbar-dark bg-white px-4 py-3 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <a href="my-courses.php" class="text-decoration-none fs-5"><i class="fa-solid fa-arrow-left me-2"></i> Back to My Courses</a>
            <span>|</span>
            <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($course['title']); ?></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div style="width: 300px;">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Progress</span>
                    <span class="fw-bold"><?php echo $progress_percent; ?>%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress_percent; ?>%;" aria-valuenow="<?php echo $progress_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="row g-4">
        
        <!-- Left Side: Video Player & Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-dark mb-4">
                <?php if ($active_lesson && !empty($active_lesson['video_url'])): ?>
                    <?php 
                        $v_url = $active_lesson['video_url'];
                        if (strpos($v_url, 'watch?v=') !== false) {
                            $v_url = str_replace('watch?v=', 'embed/', $v_url);
                        }
                    ?>
                    <div class="ratio ratio-16x9">
                        <iframe src="<?php echo htmlspecialchars($v_url); ?>" allowfullscreen></iframe>
                    </div>
                <?php else: ?>
                    <div class="text-center text-white py-5" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                        <p class="mb-0">Select a lesson from the curriculum to start watching.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($active_lesson): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($active_lesson['title']); ?></h4>
                        
                        <?php $is_completed = in_array($active_lesson['id'], $completed_lessons); ?>
                        <form action="" method="POST">
                            <input type="hidden" name="lesson_id" value="<?php echo $active_lesson['id']; ?>">
                            <?php if ($is_completed): ?>
                                <button type="button" class="btn btn-success btn-sm fw-bold px-4" disabled><i class="fa-solid fa-check me-1"></i> Completed</button>
                            <?php else: ?>
                                <button type="submit" name="complete_lesson" class="btn btn-primary btn-sm fw-bold px-4">Mark as Complete</button>
                            <?php endif; ?>
                        </form>
                    </div>
                    <p class="text-muted small mb-0"><i class="fa-regular fa-clock me-1"></i> Duration: <?php echo htmlspecialchars($active_lesson['duration'] ?? '10 mins'); ?></p>
                    <?php if (!empty($active_lesson['description'])): ?>
                        <hr class="my-3">
                        <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($active_lesson['description'])); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Interactive Tabs: Notes & Live Classes -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <ul class="nav nav-pills mb-3 gap-2" id="learnTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active fw-bold px-3 py-2" data-bs-toggle="tab" data-bs-target="#notes-tab" type="button">Study Notes (<?php echo count($notes); ?>)</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold px-3 py-2" data-bs-toggle="tab" data-bs-target="#live-tab" type="button">Live Classes (<?php echo count($live_classes); ?>)</button></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="notes-tab">
                        <?php if (count($notes) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($notes as $n): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center p-3 border rounded-3 mb-2">
                                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($n['title']); ?></h6>
                                        <a href="../<?php echo htmlspecialchars($n['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-download me-1"></i> Download Note</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No study notes available for this course.</p>
                        <?php endif; ?>
                    </div>
                    <div class="tab-pane fade" id="live-tab">
                        <?php if (count($live_classes) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($live_classes as $lc): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center p-3 border rounded-3 mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($lc['title']); ?></h6>
                                            <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> <?php echo date('M d, Y - h:i A', strtotime($lc['schedule_time'])); ?></small>
                                        </div>
                                        <a href="<?php echo htmlspecialchars($lc['meeting_link']); ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-video me-1"></i> Join Class</a>
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

        <!-- Right Side: Curriculum Sidebar with One-Time Quiz Exam & Certificate -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 20px; max-height: 90vh; overflow-y: auto;">
                <h4 class="fw-bold mb-3">Course Curriculum</h4>
                
                <!-- Video Lessons List -->
                <div class="mb-4">
                    <h6 class="text-muted fw-bold text-uppercase small mb-2">Video Lessons</h6>
                    <div class="list-group">
                        <?php foreach ($lessons as $index => $l): 
                            $is_active = ($l['id'] == $current_lesson_id);
                            $is_done = in_array($l['id'], $completed_lessons);
                        ?>
                            <a href="learn-course.php?id=<?php echo $course_id; ?>&lesson=<?php echo $l['id']; ?>" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border mb-2 rounded-3 <?php echo $is_active ? 'active bg-primary text-white' : ''; ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid <?php echo $is_done ? 'fa-circle-check text-success' : 'fa-play-circle'; ?> <?php echo $is_active ? 'text-white' : 'text-primary'; ?>"></i>
                                    <span class="fw-bold small"><?php echo ($index + 1) . '. ' . htmlspecialchars($l['title']); ?></span>
                                </div>
                                <small class="<?php echo $is_active ? 'text-white-50' : 'text-muted'; ?>"><?php echo htmlspecialchars($l['duration'] ?? '10m'); ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Chapter Quizzes & Exam Section (One-Time Submit & Answer View) -->
                <div>
                    <h6 class="text-muted fw-bold text-uppercase small mb-2">Chapter Quizzes & Exam</h6>
                    <?php if (isset($_SESSION['quiz_msg'])): ?>
                        <div class="alert alert-info py-2 small mb-2">
                            <?php echo $_SESSION['quiz_msg']; unset($_SESSION['quiz_msg']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (count($chapters) > 0): ?>
                        <div class="accordion" id="trackerChapterAccordion">
                            <?php foreach ($chapters as $ci => $c): ?>
                                <div class="accordion-item mb-2 border rounded-3 overflow-hidden">
                                    <h2 class="accordion-header" id="trackerHeading<?php echo $c['id']; ?>">
                                        <button class="accordion-button collapsed fw-bold small py-2" type="button" data-bs-toggle="collapse" data-bs-target="#trackerCollapse<?php echo $c['id']; ?>">
                                            <?php echo htmlspecialchars($c['chapter_name']); ?>
                                        </button>
                                    </h2>
                                    <div id="trackerCollapse<?php echo $c['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#trackerChapterAccordion">
                                        <div class="accordion-body bg-light p-3">
                                            <?php 
                                                $chap_quizzes = [];
                                                foreach($quizzes as $q) {
                                                    if(isset($q['chapter_id']) && $q['chapter_id'] == $c['id']) {
                                                        $chap_quizzes[] = $q;
                                                    }
                                                }

                                                // Check if already attempted
                                                $sc_stmt = $pdo->prepare("SELECT * FROM quiz_results WHERE student_id = ? AND chapter_id = ?");
                                                $sc_stmt->execute([$student_id, $c['id']]);
                                                $q_res = $sc_stmt->fetch();
                                            ?>

                                            <?php if (count($chap_quizzes) > 0): ?>
                                                <?php if ($q_res): ?>
                                                    <!-- ALREADY SUBMITTED: SHOW SCORE & CORRECT ANSWERS -->
                                                    <div class="alert alert-success py-2 px-3 small mb-2 text-center">
                                                        Exam Completed! Your Score: <strong><?php echo $q_res['score']; ?> / <?php echo $q_res['total_questions']; ?></strong>
                                                    </div>
                                                    <hr class="my-2">
                                                    <?php foreach($chap_quizzes as $qi => $q): ?>
                                                        <div class="mb-2 bg-white p-2 border rounded-2 small">
                                                            <div class="fw-bold text-dark mb-1">Q<?php echo ($qi+1); ?>: <?php echo htmlspecialchars($q['question']); ?></div>
                                                            <ul class="list-unstyled ps-2 text-muted mb-1">
                                                                <li>A. <?php echo htmlspecialchars($q['option_a']); ?></li>
                                                                <li>B. <?php echo htmlspecialchars($q['option_b']); ?></li>
                                                                <li>C. <?php echo htmlspecialchars($q['option_c']); ?></li>
                                                                <li>D. <?php echo htmlspecialchars($q['option_d']); ?></li>
                                                            </ul>
                                                            <span class="badge bg-success">Correct Option: <?php echo strtoupper($q['correct_option']); ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <!-- NOT YET SUBMITTED: SHOW EXAM FORM -->
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="chapter_id" value="<?php echo $c['id']; ?>">
                                                        <?php foreach($chap_quizzes as $qi => $q): ?>
                                                            <div class="mb-3 bg-white p-2 border rounded-2 small">
                                                                <div class="fw-bold text-dark mb-1">Q<?php echo ($qi+1); ?>: <?php echo htmlspecialchars($q['question']); ?></div>
                                                                <div class="ps-2">
                                                                    <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['id']; ?>]" value="a" required> <label class="form-check-label">A. <?php echo htmlspecialchars($q['option_a']); ?></label></div>
                                                                    <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['id']; ?>]" value="b"> <label class="form-check-label">B. <?php echo htmlspecialchars($q['option_b']); ?></label></div>
                                                                    <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['id']; ?>]" value="c"> <label class="form-check-label">C. <?php echo htmlspecialchars($q['option_c']); ?></label></div>
                                                                    <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['id']; ?>]" value="d"> <label class="form-check-label">D. <?php echo htmlspecialchars($q['option_d']); ?></label></div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <button type="submit" name="submit_quiz" class="btn btn-sm btn-primary w-100 fw-bold" onclick="return confirm('Are you sure to submit? You cannot change answers later.');">Submit Quiz Exam</button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p class="text-muted small mb-0">No quizzes in this chapter.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small">No chapters available.</p>
                    <?php endif; ?>
                </div>

                <!-- Certificate Download Banner if 100% Completed -->
                <?php if ($progress_percent == 100): ?>
                    <?php 
                        $cert_stmt = $pdo->prepare("SELECT * FROM certificates WHERE student_id = ? AND course_id = ?");
                        $cert_stmt->execute([$student_id, $course_id]);
                        $certificate = $cert_stmt->fetch();
                    ?>
                    <div class="card border-0 bg-success bg-opacity-10 rounded-4 p-4 text-center mt-4 shadow-sm">
                        <h5 class="fw-bold text-success mb-2"><i class="fa-solid fa-award me-2"></i> Course Completed!</h5>
                        <p class="text-muted small mb-3">Congratulations on finishing all lessons. Your dummy certificate is ready.</p>
                        <a href="certificate.php?code=<?php echo $certificate['certificate_code'] ?? ''; ?>" target="_blank" class="btn btn-success fw-bold btn-sm py-2"><i class="fa-solid fa-download me-1"></i> Download Certificate</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>