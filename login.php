<?php
include 'components/template-top.php';
?>

<section class="auth-page-section">
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">

            <!-- Left Column: Visual Brand Block -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center brand-side-wrapper">
                <!-- Glowing Ambient Orbs for Premium Tech Aesthetic -->
                <div class="ambient-glow-orb-1"></div>
                <div class="ambient-glow-orb-2"></div>

                <div class="brand-content-overlay text-center">
                    <!-- Logo Block -->
                    <div class="logo-box mb-4">
                        <a href="index.php">
                            <h2 class="display-6 fw-bold text-white m-0">SLTP<span style="color: #ffb100;">.</span></h2>
                        </a>
                    </div>

                    <!-- Engaging Copy -->
                    <h3 class="welcome-title">Your Blueprint for Tech Excellence Starts Here</h3>
                    <p class="welcome-desc">Log back into your tailored workstation. Sync up with elite industry titans, track your live build challenges, and gear up to claim your next high-impact engineering role.</p>

                    <!-- Premium Floating Live Stats Grid -->
                    <div class="auth-stats-grid mt-5">
                        <div class="stat-item-card">
                            <h4>12k+</h4>
                            <p>Active Learners</p>
                        </div>
                        <div class="stat-item-card">
                            <h4>98%</h4>
                            <p>Success Rate</p>
                        </div>
                        <div class="stat-item-card">
                            <h4>50+</h4>
                            <p>Global Mentors</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Login Form Block -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center form-side-wrapper">
                <div class="auth-form-card">

                    <div class="form-header mb-4">
                        <h2 class="auth-title">Account Login</h2>
                        <p class="auth-subtitle">Don't have an account? <a href="register.php" class="link-trigger">Register Now</a></p>
                    </div>

                    <!-- Social Login Bridge via FontAwesome -->
                    <div class="social-auth-grid mb-4">
                        <a href="#" class="btn-social-auth">
                            <i class="fa-brands fa-google text-danger"></i>
                            <span>Sign in with Google</span>
                        </a>
                    </div>

                    <div class="auth-divider mb-4">
                        <span>Or log in with email</span>
                    </div>

                    <!-- Main Authentication Form -->
                    <form action="backend/login-process.php" method="POST" class="main-auth-form">

                        <!-- Email Input Group -->
                        <div class="input-group-block mb-3">
                            <label for="userEmail" class="form-label-custom">Email Address</label>
                            <div class="input-control-wrap">
                                <span class="input-icon-slot"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" id="userEmail" name="email" class="form-input-custom" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <!-- Password Input Group -->
                        <div class="input-group-block mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="userPassword" class="form-label-custom mb-0">Password</label>
                                <a href="forgot-password.php" class="link-forgot">Forgot Password?</a>
                            </div>
                            <div class="input-control-wrap">
                                <span class="input-icon-slot"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" id="userPassword" name="password" class="form-input-custom" placeholder="••••••••" required>
                                <span class="password-toggle-slot" id="togglePassword"><i class="fa-regular fa-eye"></i></span>
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input-custom" type="checkbox" id="rememberMe" name="remember_me">
                            <label class="form-check-label-custom" for="rememberMe">
                                Keep me logged in on this device
                            </label>
                        </div>

                        <!-- Action Submit Trigger -->
                        <button type="submit" class="btn-auth-submit w-100">
                            <span>Sign In to Account</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</section>

<?php
include 'components/footer.php';
include 'components/template-bottom.php';
?>