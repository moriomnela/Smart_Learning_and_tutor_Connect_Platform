<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Get Tutor ID from URL, default to 1 if missing
$tutor_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

try {
    // Fetch Tutor Details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'tutor'");
    $stmt->execute([$tutor_id]);
    $tutor = $stmt->fetch();

    if (!$tutor) {
        // If tutor not found, redirect to tutors list
        header("Location: tutors.php");
        exit;
    }

    // Fetch Courses created by this tutor
    $course_stmt = $pdo->prepare("SELECT * FROM courses WHERE tutor_id = ?");
    $course_stmt->execute([$tutor_id]);
    $tutor_courses = $course_stmt->fetchAll();

} catch (PDOException $e) {
    $tutor = [];
    $tutor_courses = [];
}

$page_title = htmlspecialchars($tutor['full_name']) . " - Tutor Profile";
?>

<section class="tutor-profile-section pb-5">
    
    <!-- Tutor Cover Area -->
    <div class="tutor-cover-bg" style="background-image: url('assets/img/banner/tutor-details-bg.jpg'); height: 300px; background-size: cover; background-position: center;">
    </div>

    <div class="container" style="margin-top: -100px;">
        <!-- Tutor Header Info (Overlapping Cover) -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="tutor-header-card bg-white p-4 rounded-4 shadow-sm d-flex flex-column flex-md-row align-items-center gap-4">
                    <div class="tutor-avatar-wrap position-relative">
                        <img src="assets/img/profiles/<?php echo !empty($tutor['avatar']) ? htmlspecialchars($tutor['avatar']) : 'default-avatar.png'; ?>" 
                             alt="<?php echo htmlspecialchars($tutor['full_name']); ?>" 
                             class="img-fluid rounded-circle border border-5 border-white shadow" 
                             style="width: 160px; height: 160px; object-fit: cover;"
                             onerror="this.src='assets/img/default-avatar.png';">
                        <span class="verified-badge position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow"><i class="fa-solid fa-circle-check"></i></span>
                    </div>
                    
                    <div class="tutor-basic-info flex-grow-1 text-center text-md-start mt-3 mt-md-0">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                            <h2 class="tutor-name fw-bold mb-0"><?php echo htmlspecialchars($tutor['full_name']); ?></h2>
                            <div class="tutor-rating mt-2 mt-md-0">
                                <i class="fa-solid fa-star text-warning"></i>
                                <span class="fw-bold text-dark ms-1">4.9</span>
                                <span class="text-muted ms-1">(128 Reviews)</span>
                            </div>
                        </div>
                        <h5 class="text-primary fw-bold mb-3"><?php echo htmlspecialchars($tutor['headline'] ?? 'Expert Instructor & Mentor'); ?></h5>
                        
                        <div class="tutor-tags d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                            <span class="badge custom-badge bg-light text-dark border px-3 py-2"><i class="fa-solid fa-location-dot me-1 text-primary"></i> <?php echo htmlspecialchars($tutor['location'] ?? 'Dhaka, Bangladesh'); ?></span>
                            <span class="badge custom-badge bg-light text-dark border px-3 py-2"><i class="fa-solid fa-language me-1 text-primary"></i> <?php echo htmlspecialchars($tutor['languages'] ?? 'English & Bengali'); ?></span>
                            <span class="badge custom-badge bg-light text-dark border px-3 py-2"><i class="fa-solid fa-video me-1 text-primary"></i> <?php echo htmlspecialchars($tutor['study_mode'] ?? 'Online & Offline'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <!-- Left Side: Tutor Details -->
            <div class="col-lg-8">
                
                <!-- About Section -->
                <div class="tutor-section-box mb-5 bg-white p-4 rounded-4 shadow-sm">
                    <h4 class="section-title mb-3 fw-bold">About Me</h4>
                    <p class="text-muted line-height-lg"><?php echo nl2br(htmlspecialchars($tutor['bio'] ?? 'Hello! I am a passionate educator dedicated to making complex concepts easy to understand and helping students achieve their academic goals.')); ?></p>
                </div>

                <!-- Education & Qualifications -->
                <div class="tutor-section-box mb-5 bg-white p-4 rounded-4 shadow-sm">
                    <h4 class="section-title mb-4 fw-bold">Education & Qualifications</h4>
                    <div class="timeline-area">
                        <div class="timeline-item d-flex gap-3 mb-4">
                            <div class="timeline-icon mt-1">
                                <i class="fa-solid fa-graduation-cap text-primary fs-4"></i>
                            </div>
                            <div class="timeline-content border-start border-2 border-primary ps-4 pb-3">
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($tutor['education_title'] ?? 'B.Sc in Engineering'); ?></h5>
                                <span class="text-secondary fw-bold d-block mb-2"><?php echo htmlspecialchars($tutor['education_institute'] ?? 'University Graduate'); ?></span>
                                <span class="text-muted small"><i class="fa-regular fa-calendar me-1"></i> <?php echo htmlspecialchars($tutor['education_year'] ?? '2016 - 2020'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subjects Offered -->
                <div class="tutor-section-box mb-5 bg-white p-4 rounded-4 shadow-sm">
                    <h4 class="section-title mb-3 fw-bold">Subjects I Teach</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="subject-tag badge bg-primary bg-opacity-10 text-primary p-2 px-3">Physics</span>
                        <span class="subject-tag badge bg-primary bg-opacity-10 text-primary p-2 px-3">Mathematics</span>
                        <span class="subject-tag badge bg-primary bg-opacity-10 text-primary p-2 px-3">Admission Prep</span>
                    </div>
                </div>

                <!-- Tutor's Online Courses -->
                <div class="tutor-section-box mb-5">
                    <h4 class="section-title mb-4 fw-bold">Online Courses by <?php echo htmlspecialchars($tutor['full_name']); ?></h4>
                    <div class="row g-4">
                        <?php if (count($tutor_courses) > 0): ?>
                            <?php foreach ($tutor_courses as $course): ?>
                                <div class="col-md-6">
                                    <div class="tutor-course-card bg-white rounded-4 shadow-sm border overflow-hidden h-100 d-flex flex-column">
                                        <div class="course-thumb position-relative">
                                            <img src="assets/img/courses/<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image" class="img-fluid w-100" style="height: 180px; object-fit: cover;" onerror="this.src='https://dummyimage.com/600x400/1e3a8a/ffffff.jpg&text=Course';">
                                            <span class="badge bg-primary position-absolute top-0 end-0 m-3 fs-6">৳ <?php echo number_format($course['price'], 2); ?></span>
                                        </div>
                                        <div class="course-content p-4 d-flex flex-column flex-grow-1">
                                            <span class="text-primary fw-bold small text-uppercase mb-2 d-block"><?php echo htmlspecialchars($course['subtitle']); ?></span>
                                            <h5 class="fw-bold mb-3"><a href="course-details.php?id=<?php echo $course['id']; ?>" class="text-dark text-decoration-none course-title-link"><?php echo htmlspecialchars($course['title']); ?></a></h5>
                                            
                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center text-muted small mb-4">
                                                    <span><i class="fa-solid fa-star text-warning me-1"></i> 4.8 (120)</span>
                                                    <span><i class="fa-solid fa-user-graduate me-1 text-primary"></i> Enrolled</span>
                                                </div>
                                                <a href="course-details.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary w-100 fw-bold rounded-3">View Course</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="bg-white p-4 rounded-4 text-center border">
                                    <p class="text-muted mb-0">This tutor hasn't published any courses yet.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Right Side: Sticky Booking Sidebar -->
            <div class="col-lg-4">
                <div class="tutor-booking-widget p-4 bg-white rounded-4 shadow-lg border sticky-top" style="top: 100px;">
                    
                    <div class="pricing-header mb-4 border-bottom pb-3">
                        <h3 class="fw-bold text-primary mb-0">৳ <?php echo number_format($tutor['hourly_rate'] ?? 800, 0); ?> <span class="fs-6 text-muted fw-normal">/ hour</span></h3>
                        <p class="text-muted small mt-1 mb-0">Monthly package available upon discussion.</p>
                    </div>

                    <div class="tutor-stats mb-4">
                        <div class="stat-item d-flex justify-content-between mb-2">
                            <span class="text-muted"><i class="fa-solid fa-users me-2"></i> Active Students</span>
                            <span class="fw-bold text-dark"><?php echo $tutor['active_students'] ?? 24; ?></span>
                        </div>
                        <div class="stat-item d-flex justify-content-between mb-2">
                            <span class="text-muted"><i class="fa-solid fa-clock me-2"></i> Response Time</span>
                            <span class="fw-bold text-dark">Within 1 hour</span>
                        </div>
                        <div class="stat-item d-flex justify-content-between">
                            <span class="text-muted"><i class="fa-solid fa-calendar-check me-2"></i> Free Trial</span>
                            <span class="fw-bold text-success">Offered</span>
                        </div>
                    </div>

                    <!-- Book Lesson Action Form / Trigger -->
                    <div class="action-buttons d-flex flex-column gap-3 mb-4">
                        <form action="/SLTCP/backend/book-tutor-process.php" method="POST" class="d-flex flex-column gap-3">
                            <input type="hidden" name="tutor_id" value="<?php echo $tutor['id']; ?>">
                            <input type="hidden" name="subject" value="General Mentorship / Session">
                            <input type="hidden" name="booking_date" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            <input type="hidden" name="message" value="Booked directly from tutor profile profile page.">
                            
                            <button type="button" class="btn btn-primary w-100 py-3 fw-bold fs-5 rounded-3 text-white" data-bs-toggle="modal" data-bs-target="#bookingModal">Book a Lesson</button>
                        </form>
                    </div>

                    <div class="availability-box bg-light p-3 rounded-3 text-center">
                        <p class="mb-2 fw-bold text-dark fs-6">Available Teaching Days</p>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                            <span class="badge bg-success">Sun</span>
                            <span class="badge bg-success">Mon</span>
                            <span class="badge bg-secondary text-white-50">Tue</span>
                            <span class="badge bg-success">Wed</span>
                            <span class="badge bg-secondary text-white-50">Thu</span>
                            <span class="badge bg-success">Fri</span>
                            <span class="badge bg-success">Sat</span>
                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>

    
</section>
<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"> 
    <div class="modal-content rounded-4 border-0 shadow">
      <form action="/SLTCP/backend/book-tutor-process.php" method="POST">
        <div class="modal-header border-bottom-0 p-4">
          <h5 class="modal-title fw-bold">Request a Session with <?php echo htmlspecialchars($tutor['full_name']); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
            <input type="hidden" name="tutor_id" value="<?php echo $tutor['id']; ?>">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Subject / Topic</label>
                    <input type="text" name="subject" class="form-control py-2" required placeholder="E.g., HSC Physics 2nd Paper">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="booking_date" class="form-control py-2" required>
                </div>
                
                <!-- Time & Mode Section -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Time (E.g., 4:00 PM)</label>
                    <input type="text" name="time_slot" class="form-control py-2" required placeholder="e.g., 4:00 PM">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Preferred Mode</label>
                    <div class="d-flex gap-3 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="class_mode" id="online" value="Online" checked>
                            <label class="form-check-label" for="online">Online</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="class_mode" id="offline" value="Offline">
                            <label class="form-check-label" for="offline">Offline</label>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-bold">Message for Tutor</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Additional details, meeting link requests, etc..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer border-top-0 p-4">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">Submit Booking Request</button>
        </div>
      </form>
    </div>
  </div>
</div>
