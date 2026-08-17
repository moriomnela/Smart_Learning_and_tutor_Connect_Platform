<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify course belongs to this tutor
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND tutor_id = ?");
$stmt->execute([$course_id, $tutor_id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: my-courses.php");
    exit;
}

// 1. Handle Course Details Update & Notify Students
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    $subtitle = trim($_POST['subtitle']);
    $title = trim($_POST['title']);
    $price = floatval($_POST['price']);
    $discount_price = !empty($_POST['discount_price']) ? floatval($_POST['discount_price']) : null;
    $learning_outcomes = trim($_POST['learning_outcomes']);
    $description = trim($_POST['description']);
    $image_name = $course['image'];

    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['course_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['course_image']['name']);
        $upload_dir = '../assets/img/courses/';
        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $image_name = $file_name;
        }
    }

    try {
        $updateStmt = $pdo->prepare("UPDATE courses SET subtitle = ?, title = ?, price = ?, discount_price = ?, learning_outcomes = ?, description = ?, image = ? WHERE id = ?");
        $updateStmt->execute([$subtitle, $title, $price, $discount_price, $learning_outcomes, $description, $image_name, $course_id]);

        // --- NOTIFY ENROLLED STUDENTS (FIXED WITH 'type' COLUMN) ---
        $enroll_stmt = $pdo->prepare("SELECT student_id FROM enrollments WHERE course_id = ?");
        $enroll_stmt->execute([$course_id]);
        $enrolled_students = $enroll_stmt->fetchAll();

        // Database table-e 'type' column thakay ekhane explicit type pass kora lagbe
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, link, is_read, type) VALUES (?, ?, ?, 0, 'course_update')");
        
        foreach ($enrolled_students as $student) {
            $notif_stmt->execute([
                $student['student_id'], 
                "Update in your course: " . $title, 
                "learn-course.php?id=" . $course_id
            ]);
        }
        // -----------------------------------------------------------

        $_SESSION['success'] = "Course updated and students notified successfully! (Total students: " . count($enrolled_students) . ")";
        header("Location: edit-course.php?id=" . $course_id);
        exit;
    } catch (PDOException $e) {
        // Eta dile asli database error screen-e dekhte parbi jodi abar jhamela kore
        echo "Database Error: " . $e->getMessage();
        exit;
    }
}

// 2. Handle Add Video Lesson
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lesson'])) {
    $l_title = trim($_POST['lesson_title']);
    $l_url = trim($_POST['video_url']);
    $l_duration = trim($_POST['duration']);
    $l_desc = trim($_POST['lesson_description']);

    if (strpos($l_url, 'watch?v=') !== false) {
        $l_url = str_replace('watch?v=', 'embed/', $l_url);
    }

    $pdo->prepare("INSERT INTO course_lessons (course_id, title, video_url, duration, description) VALUES (?, ?, ?, ?, ?)")
        ->execute([$course_id, $l_title, $l_url, $l_duration, $l_desc]);
    $_SESSION['success'] = "Video lesson added successfully!";
    header("Location: edit-course.php?id=" . $course_id);
    exit;
}

// 3. Handle Add Study Note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $n_title = trim($_POST['note_title']);
    $file_path = "";
    if (isset($_FILES['note_file']) && $_FILES['note_file']['error'] === UPLOAD_ERR_OK) {
        $f_tmp = $_FILES['note_file']['tmp_name'];
        $f_name = time() . '_' . basename($_FILES['note_file']['name']);
        $f_dir = '../assets/uploads/notes/';
        if (!is_dir($f_dir)) mkdir($f_dir, 0777, true);
        if (move_uploaded_file($f_tmp, $f_dir . $f_name)) {
            $file_path = 'assets/uploads/notes/' . $f_name;
        }
    }
    if ($file_path) {
        $pdo->prepare("INSERT INTO course_notes (course_id, title, file_path) VALUES (?, ?, ?)")
            ->execute([$course_id, $n_title, $file_path]);
        $_SESSION['success'] = "Study note uploaded successfully!";
    }
    header("Location: edit-course.php?id=" . $course_id);
    exit;
}

// 4. Handle Add Chapter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_chapter'])) {
    $c_name = trim($_POST['chapter_name']);
    $pdo->prepare("INSERT INTO course_chapters (course_id, chapter_name) VALUES (?, ?)")->execute([$course_id, $c_name]);
    $_SESSION['success'] = "Chapter added successfully!";
    header("Location: edit-course.php?id=$course_id"); 
    exit;
}

// 5. Handle Add Quiz (MCQ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_quiz'])) {
    $chapter_id = intval($_POST['chapter_id']);
    $q_question = trim($_POST['question']);
    $opt_a = trim($_POST['option_a']);
    $opt_b = trim($_POST['option_b']);
    $opt_c = trim($_POST['option_c']);
    $opt_d = trim($_POST['option_d']);
    $correct = trim($_POST['correct_option']);

    if($chapter_id > 0) {
        $pdo->prepare("INSERT INTO course_quizzes (course_id, chapter_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$course_id, $chapter_id, $q_question, $opt_a, $opt_b, $opt_c, $opt_d, $correct]);
        $_SESSION['success'] = "Quiz added to chapter successfully!";
    } else {
        $_SESSION['error'] = "Please select a chapter!";
    }
    header("Location: edit-course.php?id=" . $course_id);
    exit;
}

// 6. Handle Live Class Schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_live_class'])) {
    $lc_title = trim($_POST['live_title']);
    $lc_link = trim($_POST['meeting_link']);
    $lc_time = trim($_POST['schedule_time']);

    $pdo->prepare("INSERT INTO live_classes (course_id, tutor_id, title, meeting_link, schedule_time) VALUES (?, ?, ?, ?, ?)")
        ->execute([$course_id, $tutor_id, $lc_title, $lc_link, $lc_time]);
    $_SESSION['success'] = "Live class scheduled successfully!";
    header("Location: edit-course.php?id=" . $course_id);
    exit;
}

// 7. Handle Unified Deletions (Lessons, Notes, Quizzes, Live Classes, Chapters)
if (isset($_GET['delete_type']) && isset($_GET['del_id'])) {
    $del_id = intval($_GET['del_id']);
    $type = $_GET['delete_type'];
    $tbl = '';

    if ($type === 'lesson') $tbl = 'course_lessons';
    elseif ($type === 'note') $tbl = 'course_notes';
    elseif ($type === 'quiz') $tbl = 'course_quizzes';
    elseif ($type === 'live') $tbl = 'live_classes';
    elseif ($type === 'chapter') {
        $pdo->prepare("DELETE FROM course_quizzes WHERE chapter_id = ?")->execute([$del_id]);
        $tbl = 'course_chapters';
    }

    if ($tbl) {
        $pdo->prepare("DELETE FROM $tbl WHERE id = ? AND course_id = ?")->execute([$del_id, $course_id]);
        $_SESSION['success'] = ucfirst($type) . " deleted successfully!";
        header("Location: edit-course.php?id=" . $course_id);
        exit;
    }
}

// Fetch existing data for rendering
$chapters = $pdo->prepare("SELECT * FROM course_chapters WHERE course_id = ?"); $chapters->execute([$course_id]); $chapters = $chapters->fetchAll();
$lessons = $pdo->prepare("SELECT * FROM course_lessons WHERE course_id = ?"); $lessons->execute([$course_id]); $lessons = $lessons->fetchAll();
$notes = $pdo->prepare("SELECT * FROM course_notes WHERE course_id = ?"); $notes->execute([$course_id]); $notes = $notes->fetchAll();
$quizzes = $pdo->prepare("SELECT * FROM course_quizzes WHERE course_id = ?"); $quizzes->execute([$course_id]); $quizzes = $quizzes->fetchAll();
$live_classes = $pdo->prepare("SELECT * FROM live_classes WHERE course_id = ?"); $live_classes->execute([$course_id]); $live_classes = $live_classes->fetchAll();

$page_title = "Manage Course Contents";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Manage Course</title>
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
            <li><a href="my-courses.php" class="nav-link active p-2 rounded fw-bold text-primary bg-light"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>            
            <li><a href="manage-certificates.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-certificate me-2"></i> Certificate Requests</a></li>
            <li><a href="profile.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Manage Course: <?php echo htmlspecialchars($course['title']); ?></h2>
                <p class="text-muted">Update course info, video lessons, notes, quizzes, and live classes.</p>
            </div>
            <a href="my-courses.php" class="btn btn-outline-secondary fw-bold px-4 py-2">Back to Courses</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Nav tabs -->
        <ul class="nav nav-pills mb-4 gap-2" id="courseTab" role="tablist">
            <li class="nav-item"><button class="nav-link active fw-bold px-4" data-bs-toggle="tab" data-bs-target="#edit-info" type="button">Course Details</button></li>
            <li class="nav-item"><button class="nav-link fw-bold px-4" data-bs-toggle="tab" data-bs-target="#lessons" type="button">Video Lessons (<?php echo count($lessons); ?>)</button></li>
            <li class="nav-item"><button class="nav-link fw-bold px-4" data-bs-toggle="tab" data-bs-target="#notes" type="button">Study Notes (<?php echo count($notes); ?>)</button></li>
            <li class="nav-item"><button class="nav-link fw-bold px-4" data-bs-toggle="tab" data-bs-target="#quizzes" type="button">Quizzes (<?php echo count($quizzes); ?>)</button></li>
            <li class="nav-item"><button class="nav-link fw-bold px-4" data-bs-toggle="tab" data-bs-target="#live-classes" type="button">Live Classes (<?php echo count($live_classes); ?>)</button></li>
        </ul>

        <div class="tab-content">
            <!-- TAB 1: EDIT COURSE DETAILS -->
            <div class="tab-pane fade show active" id="edit-info">
                <div class="bg-white p-5 rounded-4 shadow-sm border-0 col-lg-9">
                    <form action="edit-course.php?id=<?php echo $course_id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Course Subtitle</label>
                            <input type="text" name="subtitle" class="form-control" value="<?php echo htmlspecialchars($course['subtitle']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Course Title</label>
                            <input type="text" name="title" class="form-control py-2" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Course Price (BDT)</label>
                                <input type="number" step="0.01" name="price" class="form-control py-2" value="<?php echo htmlspecialchars($course['price']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Discounted Price</label>
                                <input type="number" step="0.01" name="discount_price" class="form-control py-2" value="<?php echo htmlspecialchars($course['discount_price']); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">What You Will Learn</label>
                            <textarea name="learning_outcomes" class="form-control" rows="2" required><?php echo htmlspecialchars($course['learning_outcomes']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Thumbnail</label>
                            <div class="mb-2"><img src="../assets/img/courses/<?php echo htmlspecialchars($course['image']); ?>" width="150" class="rounded-3 border"></div>
                            <input type="file" name="course_image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Course Description</label>
                            <textarea name="description" rows="4" class="form-control" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                        </div>
                        <button type="submit" name="update_course" class="btn btn-primary px-5 py-2 fw-bold">Save Changes</button>
                    </form>
                </div>
            </div>

            <!-- TAB 2: VIDEO LESSONS -->
            <div class="tab-pane fade" id="lessons">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-3">Add Video Lesson</h4>
                            <form action="edit-course.php?id=<?php echo $course_id; ?>" method="POST">
                                <div class="mb-3"><label class="form-label fw-bold">Lesson Title</label><input type="text" name="lesson_title" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label fw-bold">Video URL (YouTube)</label><input type="text" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required></div>
                                <div class="mb-3"><label class="form-label fw-bold">Duration</label><input type="text" name="duration" class="form-control" placeholder="E.g., 15 mins"></div>
                                <div class="mb-3"><label class="form-label fw-bold">Description</label><textarea name="lesson_description" class="form-control" rows="2"></textarea></div>
                                <button type="submit" name="add_lesson" class="btn btn-primary fw-bold w-100">Add Lesson</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-3">Existing Lessons</h4>
                            <?php if(count($lessons) > 0): foreach($lessons as $l): ?>
                                <div class="p-3 border rounded-3 mb-3 bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($l['title']); ?></h6>
                                        <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> <?php echo htmlspecialchars($l['duration'] ?? 'N/A'); ?></small>
                                    </div>
                                    <div>
                                        <a href="<?php echo htmlspecialchars($l['video_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold me-1">
                                            <i class="fa-solid fa-play me-1"></i> Watch Video
                                        </a>
                                        <a href="edit-course.php?id=<?php echo $course_id; ?>&delete_type=lesson&del_id=<?php echo $l['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this lesson?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; else: ?> 
                                <p class="text-muted">No video lessons added yet.</p> 
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: STUDY NOTES -->
            <div class="tab-pane fade" id="notes">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-3">Upload Study Note</h4>
                            <form action="edit-course.php?id=<?php echo $course_id; ?>" method="POST" enctype="multipart/form-data">
                                <div class="mb-3"><label class="form-label fw-bold">Note Title</label><input type="text" name="note_title" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label fw-bold">Select PDF/Doc File</label><input type="file" name="note_file" class="form-control" accept=".pdf,.doc,.docx" required></div>
                                <button type="submit" name="add_note" class="btn btn-primary fw-bold w-100">Upload Note</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-3">Uploaded Notes</h4>
                            <?php if(count($notes) > 0): foreach($notes as $n): ?>
                                <div class="p-3 border rounded-3 mb-2 bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($n['title']); ?></h6>
                                    <div>
                                        <a href="../<?php echo htmlspecialchars($n['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success me-1"><i class="fa-solid fa-download"></i></a>
                                        <a href="edit-course.php?id=<?php echo $course_id; ?>&delete_type=note&del_id=<?php echo $n['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this note?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </div>
                            <?php endforeach; else: ?> <p class="text-muted">No study notes uploaded yet.</p> <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab 3: Quizzes -->
            <div class="tab-pane fade" id="quizzes">
                <div class="row g-4">
                    <!-- Left Side: Forms for Chapter & Quiz -->
                    <div class="col-lg-5">
                        <!-- Add Chapter Form -->
                        <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                            <h5 class="fw-bold mb-3">Add Chapter</h5>
                            <form action="edit-course.php?id=<?php echo $course_id; ?>" method="POST">
                                <div class="mb-3">
                                    <input type="text" name="chapter_name" class="form-control" placeholder="Chapter Name (e.g., Chapter 1)" required>
                                </div>
                                <button type="submit" name="add_chapter" class="btn btn-primary btn-sm w-100 fw-bold">Add Chapter</button>
                            </form>
                        </div>

                        <!-- Add Quiz Form -->
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h5 class="fw-bold mb-3">Add MCQ Quiz</h5>
                            <form action="edit-course.php?id=<?php echo $course_id; ?>" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Select Chapter</label>
                                    <select name="chapter_id" class="form-select" required>
                                        <option value="">Choose Chapter</option>
                                        <?php foreach($chapters as $c): ?>
                                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['chapter_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <textarea name="question" class="form-control" rows="2" placeholder="Enter Question" required></textarea>
                                </div>
                                <div class="mb-2"><input type="text" name="option_a" class="form-control" placeholder="Option A" required></div>
                                <div class="mb-2"><input type="text" name="option_b" class="form-control" placeholder="Option B" required></div>
                                <div class="mb-2"><input type="text" name="option_c" class="form-control" placeholder="Option C" required></div>
                                <div class="mb-3"><input type="text" name="option_d" class="form-control" placeholder="Option D" required></div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Correct Option</label>
                                    <select name="correct_option" class="form-select" required>
                                        <option value="a">Option A</option>
                                        <option value="b">Option B</option>
                                        <option value="c">Option C</option>
                                        <option value="d">Option D</option>
                                    </select>
                                </div>
                                <button type="submit" name="add_quiz" class="btn btn-primary fw-bold w-100">Add Quiz</button>
                            </form>
                        </div>
                    </div>

                    <!-- Right Side: Chapter Accordion with Quizzes & Chapter Delete Option -->
                    <div class="col-lg-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-3">Course Chapters & Quizzes</h4>
                            
                            <?php if(count($chapters) > 0): ?>
                                <div class="accordion" id="chapterAccordion">
                                    <?php foreach($chapters as $index => $c): ?>
                                        <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                                                <h2 class="accordion-header position-relative" id="headingChap<?php echo $c['id']; ?>">
                                                    <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?> fw-bold pe-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChap<?php echo $c['id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapseChap<?php echo $c['id']; ?>">
                                                        <?php echo htmlspecialchars($c['chapter_name']); ?>
                                                    </button>
                                                    <!-- Chapter Delete Button positioned nicely on the right inside header -->
                                                    <a href="edit-course.php?id=<?php echo $course_id; ?>&delete_type=chapter&del_id=<?php echo $c['id']; ?>#quizzes" class="btn btn-sm text-danger position-absolute top-50 end-0 translate-middle-y me-3" style="z-index: 5;" onclick="event.stopPropagation(); return confirm('Delete this chapter and all its quizzes?');" title="Delete Chapter">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                </h2>
                                            <div id="collapseChap<?php echo $c['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="headingChap<?php echo $c['id']; ?>" data-bs-parent="#chapterAccordion">
                                                <div class="accordion-body bg-light">
                                                    <?php 
                                                        $q_in_chapter = [];
                                                        foreach($quizzes as $q) {
                                                            if(isset($q['chapter_id']) && $q['chapter_id'] == $c['id']) {
                                                                $q_in_chapter[] = $q;
                                                            }
                                                        }
                                                    ?>

                                                    <?php if(count($q_in_chapter) > 0): ?>
                                                        <?php foreach($q_in_chapter as $qi => $q): ?>
                                                            <div class="p-3 bg-white border rounded-3 mb-2 shadow-sm d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="fw-bold text-dark mb-1"><?php echo ($qi + 1) . '. ' . htmlspecialchars($q['question']); ?></h6>
                                                                    <ul class="small text-muted mb-2 ps-3">
                                                                        <li>A: <?php echo htmlspecialchars($q['option_a']); ?></li>
                                                                        <li>B: <?php echo htmlspecialchars($q['option_b']); ?></li>
                                                                        <li>C: <?php echo htmlspecialchars($q['option_c']); ?></li>
                                                                        <li>D: <?php echo htmlspecialchars($q['option_d']); ?></li>
                                                                    </ul>
                                                                    <span class="badge bg-success">Correct: Option <?php echo strtoupper($q['correct_option']); ?></span>
                                                                </div>
                                                                <a href="edit-course.php?id=<?php echo $course_id; ?>&delete_type=quiz&del_id=<?php echo $q['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this quiz?');">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted small mb-0 text-center py-2">No quizzes added in this chapter yet.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No chapters created yet. Add a chapter first to add quizzes.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TAB 5: LIVE CLASSES -->
            <div class="tab-pane fade" id="live-classes">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-3">Schedule Live Class</h4>
                            <form action="edit-course.php?id=<?php echo $course_id; ?>" method="POST">
                                <div class="mb-3"><label class="form-label fw-bold">Session Title</label><input type="text" name="live_title" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label fw-bold">Meeting Link</label><input type="text" name="meeting_link" class="form-control" placeholder="https://meet.google.com/..." required></div>
                                <div class="mb-3"><label class="form-label fw-bold">Schedule Date & Time</label><input type="datetime-local" name="schedule_time" class="form-control" required></div>
                                <button type="submit" name="add_live_class" class="btn btn-primary fw-bold w-100">Schedule Class</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-3">Scheduled Live Classes</h4>
                            <?php if(count($live_classes) > 0): foreach($live_classes as $lc): ?>
                                <div class="p-3 border rounded-3 mb-2 bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($lc['title']); ?></h6>
                                        <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> <?php echo date('M d, Y - h:i A', strtotime($lc['schedule_time'])); ?></small>
                                    </div>
                                    <div>
                                        <a href="<?php echo htmlspecialchars($lc['meeting_link']); ?>" target="_blank" class="btn btn-sm btn-outline-danger me-1"><i class="fa-solid fa-video"></i></a>
                                        <a href="edit-course.php?id=<?php echo $course_id; ?>&delete_type=live&del_id=<?php echo $lc['id']; ?>" class="btn btn-sm btn-outline-dark" onclick="return confirm('Delete this live class?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </div>
                            <?php endforeach; else: ?> <p class="text-muted">No live classes scheduled yet.</p> <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
<script>
    $(document).ready(function() {
        // 1. Remember active tab on click and append hash to URL
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var targetTab = $(e.target).data('bs-target');
            localStorage.setItem('activeCourseTab_<?php echo $course_id; ?>', targetTab);
        });

        // 2. Restore active tab on page reload
        var activeTab = localStorage.getItem('activeCourseTab_<?php echo $course_id; ?>');
        if (activeTab) {
            var triggerEl = document.querySelector('button[data-bs-target="' + activeTab + '"]');
            if (triggerEl) {
                var tabObj = new bootstrap.Tab(triggerEl);
                tabObj.show();
            }
        }

        // 3. Also append active tab hash to form actions so after submit it stays on same tab
        $('form').on('submit', function() {
            var currentTab = localStorage.getItem('activeCourseTab_<?php echo $course_id; ?>');
            if (currentTab) {
                var actionUrl = $(this).attr('action') || window.location.pathname + window.location.search;
                // remove existing hash if any and add current tab
                actionUrl = actionUrl.split('#')[0] + currentTab;
                $(this).attr('action', actionUrl);
            }
        });
    });
</script>
</body>
</html>