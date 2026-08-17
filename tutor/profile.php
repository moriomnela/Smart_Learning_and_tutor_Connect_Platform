<?php
session_start();
require_once '../config/db.php';

// Security Check: Only logged-in tutors can access this page
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];

// Handle Profile & Professional Details Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $headline = trim($_POST['headline']);
    $hourly_rate = floatval($_POST['hourly_rate']);
    $location = trim($_POST['location']);
    $languages = trim($_POST['languages']);
    $study_mode = trim($_POST['study_mode']);
    $education_title = trim($_POST['education_title']);
    $education_institute = trim($_POST['education_institute']);
    $education_year = trim($_POST['education_year']);
    $bio = trim($_POST['bio']);
    
    $avatar_name = $_SESSION['avatar'] ?? 'default-avatar.png';

    // Handle Avatar Upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['avatar']['name']);
        $upload_dir = '../assets/img/profiles/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $avatar_name = $file_name;
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE users SET 
                full_name = ?, email = ?, headline = ?, 
                hourly_rate = ?, location = ?, languages = ?, study_mode = ?, 
                education_title = ?, education_institute = ?, education_year = ?, 
                bio = ?, avatar = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $full_name, $email, $headline, 
            $hourly_rate, $location, $languages, $study_mode, 
            $education_title, $education_institute, $education_year, 
            $bio, $avatar_name, $tutor_id
        ]);

        // Update session variables instantly
        $_SESSION['full_name'] = $full_name;
        $_SESSION['avatar'] = $avatar_name;

        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update profile: " . $e->getMessage();
    }
}

// Fetch current tutor details from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$tutor_id]);
$tutor = $stmt->fetch();

$page_title = "Edit Tutor Profile";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTCP - Edit Tutor Profile</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="dashboard-wrapper d-flex">
    <!-- Tutor Sidebar -->
    <div class="dashboard-sidebar bg-white border-end p-4" style="width: 280px; min-height: 100vh;">
        <h4 class="fw-bold text-primary mb-4">SLTCP<span class="text-warning">.</span> Tutor</h4>
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li><a href="dashboard.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-chalkboard-user me-2"></i> Overview</a></li>
            <li><a href="my-courses.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open me-2"></i> My Courses</a></li>
            <li><a href="add-course.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-plus-circle me-2"></i> Add New Course</a></li>
            <li><a href="bookings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-calendar-check me-2"></i> Student Bookings</a></li>
            <li><a href="earnings.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-wallet me-2"></i> Earnings</a></li>
            <li><a href="add-blog.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-pen-nib me-2"></i> Add New Blog</a></li>
            <li><a href="my-blogs.php" class="nav-link p-2 rounded text-dark"><i class="fa-solid fa-book-open-reader me-2"></i> My Blogs</a></li>            
            <li><a href="profile.php" class="nav-link active fw-bold text-primary bg-light p-2 rounded"><i class="fa-solid fa-user-gear me-2"></i> Edit Profile</a></li>
            <li class="mt-4"><a href="../logout.php" class="nav-link p-2 rounded text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content flex-grow-1 p-5 bg-light">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Edit Professional Teaching Profile</h2>
            <p class="text-muted">Update your qualifications, hourly rate, and teaching bio.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 p-5 bg-white col-lg-10">
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                
                <!-- Profile Avatar Preview -->
                <div class="d-flex align-items-center gap-4 mb-4 pb-3 border-bottom">
                    <?php 
                        $avatar = $tutor['avatar'] ?? 'default-avatar.png';
                        $avatar_url = ($avatar === 'default-avatar.png' || empty($avatar)) ? '../assets/img/profiles/default-avatar.png' : '../assets/img/profiles/' . $avatar;
                    ?>
                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" class="rounded-circle object-fit-cover shadow-sm border" width="90" height="90" onerror="this.src='../assets/img/profiles/default-avatar.png'">
                    <div>
                        <label class="form-label fw-bold">Profile Picture</label>
                        <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">Upload PNG, JPG or GIF (Max size 2MB).</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="full_name" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['full_name']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['email']); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Professional Headline *</label>
                        <input type="text" name="headline" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['headline'] ?? ''); ?>" placeholder="E.g., Senior Physics & Mathematics Expert (BUET)" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Hourly Rate (BDT) *</label>
                        <input type="number" step="0.01" name="hourly_rate" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['hourly_rate'] ?? '800'); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Location *</label>
                        <input type="text" name="location" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['location'] ?? ''); ?>" placeholder="E.g., Dhanmondi, Dhaka" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Languages Known *</label>
                        <input type="text" name="languages" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['languages'] ?? ''); ?>" placeholder="English & Bengali" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Study Mode *</label>
                        <input type="text" name="study_mode" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['study_mode'] ?? ''); ?>" placeholder="Online & Offline" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Highest Educational Qualification *</label>
                        <input type="text" name="education_title" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['education_title'] ?? ''); ?>" placeholder="E.g., B.Sc in EEE from BUET" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Institution *</label>
                        <input type="text" name="education_institute" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['education_institute'] ?? ''); ?>" placeholder="E.g., University Graduate" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Education Year *</label>
                        <input type="text" name="education_year" class="form-control py-2" value="<?php echo htmlspecialchars($tutor['education_year'] ?? ''); ?>" placeholder="E.g., 2016 - 2020" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Teaching Experience & Background (Bio) *</label>
                    <textarea name="bio" rows="4" class="form-control" placeholder="Briefly describe your background, years of experience, and teaching methodology..." required><?php echo htmlspecialchars($tutor['bio'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary px-5 py-2 fw-bold">Save Changes</button>
            </form>
        </div>

    </div>
</div>

<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/fontawesome.min.js"></script>
</body>
</html>