<section class="find-tutor-section py-5">
    <div class="container">
        <!-- Search Box -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-12">
                <div class="find-tutor-box shadow-sm">
                    <div class="text-center mb-4">
                        <h2 class="section-title">Find Your Perfect Tutor</h2>
                        <p class="subtitle">Search by subject, level, or study mode</p>
                    </div>
                    
                    <form action="tutors.php" method="GET" class="search-form">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="search_query" class="form-control custom-input" placeholder="E.g., Physics, Web Dev...">
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select custom-select">
                                    <option value="" selected>All Categories</option>
                                    <option value="science">Science</option>
                                    <option value="commerce">Commerce</option>
                                    <option value="arts">Arts & Humanities</option>
                                    <option value="programming">Programming</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="mode" class="form-select custom-select">
                                    <option value="" selected>Study Mode</option>
                                    <option value="online">Online (Zoom/Meet)</option>
                                    <option value="offline">In-Person (Home)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100 search-btn">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tutor Cards Grid -->
        <div class="row g-4">
            <!-- Dummy Tutor Card 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="premium-teacher-card">
                    <div class="teacher-thumb-box">
                        <img src="assets/img/popular_teacher/teacher1.avif" alt="Instructor Profile" class="teacher-img">

                        <div class="social-flyout">
                            <a href="#" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
                        </div>
                        <span class="courses-badge">12 Courses</span>
                    </div>

                    <div class="teacher-meta-content">
                        <h4 class="teacher-name">Anika Rahman</h4>
                        <p class="teacher-subject">Lead UI/UX Designer & Mentor</p>
                        <a href="#" class="btn-profile-trigger">
                            <span>View Full Profile</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dummy Tutor Card 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="premium-teacher-card">
                    <div class="teacher-thumb-box">
                        <img src="assets/img/popular_teacher/teacher1.avif" alt="Instructor Profile" class="teacher-img">

                        <div class="social-flyout">
                            <a href="#" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
                        </div>
                        <span class="courses-badge">12 Courses</span>
                    </div>

                    <div class="teacher-meta-content">
                        <h4 class="teacher-name">Anika Rahman</h4>
                        <p class="teacher-subject">Lead UI/UX Designer & Mentor</p>
                        <a href="#" class="btn-profile-trigger">
                            <span>View Full Profile</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dummy Tutor Card 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="premium-teacher-card">
                    <div class="teacher-thumb-box">
                        <img src="assets/img/popular_teacher/teacher1.avif" alt="Instructor Profile" class="teacher-img">

                        <div class="social-flyout">
                            <a href="#" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
                        </div>
                        <span class="courses-badge">12 Courses</span>
                    </div>

                    <div class="teacher-meta-content">
                        <h4 class="teacher-name">Anika Rahman</h4>
                        <p class="teacher-subject">Lead UI/UX Designer & Mentor</p>
                        <a href="#" class="btn-profile-trigger">
                            <span>View Full Profile</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>