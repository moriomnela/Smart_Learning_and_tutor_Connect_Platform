<?php
include 'components/template-top.php';
?>

<section class="auth-page-section">
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">

            <!-- Left Column: Visual Brand Block (Synchronized with Login) -->
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
                    <p class="welcome-desc">Join our elite ecosystem today. Sync up with industry titans, unlock practical build modules, and accelerate your path toward high-impact engineering roles.</p>

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

            <!-- Right Column: Registration Form Block -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center form-side-wrapper">
                <div class="auth-form-card">

                    <div class="form-header mb-4">
                        <h2 class="auth-title">Create Account</h2>
                        <p class="auth-subtitle">Already have an account? <a href="login.php" class="link-trigger">Login Here</a></p>
                    </div>

                    <!-- Social Registration Bridge -->
                    <div class="social-auth-grid mb-4">
                        <a href="#" class="btn-social-auth">
                            <i class="fa-brands fa-google text-danger"></i>
                            <span>Sign up with Google</span>
                        </a>
                    </div>

                    <div class="auth-divider mb-4">
                        <span>Or register with email</span>
                    </div>

                    <!-- Main Registration Form -->
                    <form action="backend/register-process.php" method="POST" class="main-auth-form">

                        <!-- Full Name Input Group -->
                        <div class="input-group-block mb-3">
                            <label for="regName" class="form-label-custom">Full Name</label>
                            <div class="input-control-wrap">
                                <span class="input-icon-slot"><i class="fa-regular fa-user"></i></span>
                                <input type="text" id="regName" name="fullname" class="form-input-custom" placeholder="John Doe" required>
                            </div>
                        </div>

                        <!-- Email Input Group -->
                        <div class="input-group-block mb-3">
                            <label for="regEmail" class="form-label-custom">Email Address</label>
                            <div class="input-control-wrap">
                                <span class="input-icon-slot"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" id="regEmail" name="email" class="form-input-custom" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <!-- Password Input Group -->
                        <div class="input-group-block mb-3">
                            <label for="userPassword" class="form-label-custom">Password</label>
                            <div class="input-control-wrap">
                                <span class="input-icon-slot"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" id="userPassword" name="password" class="form-input-custom" placeholder="••••••••" required>
                                <span class="password-toggle-slot" id="togglePassword"><i class="fa-regular fa-eye"></i></span>
                            </div>
                        </div>

                        <!-- Confirm Password Input Group -->
                        <div class="input-group-block mb-3">
                            <label for="confirmPassword" class="form-label-custom">Confirm Password</label>
                            <div class="input-control-wrap">
                                <span class="input-icon-slot"><i class="fa-solid fa-shield-halved"></i></span>
                                <input type="password" id="confirmPassword" name="confirm_password" class="form-input-custom" placeholder="••••••••" required>
                                <span class="password-toggle-slot" id="toggleConfirmPassword"><i class="fa-regular fa-eye"></i></span>
                            </div>
                        </div>

                        <!-- Terms and Conditions Checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input-custom" type="checkbox" id="termsAgreement" name="terms" required>
                            <label class="form-check-label-custom" for="termsAgreement">
                                I agree to the <a href="terms.php" class="text-decoration-none style-color" style="color: #2b6eff; fw-bold">Terms of Service</a> & Privacy Policy
                            </label>
                        </div>

                        <!-- Action Submit Trigger -->
                        <button type="submit" class="btn-auth-submit w-100">
                            <span>Create Free Account</span>
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