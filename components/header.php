<header class="main-header sticky-top">
  <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
    <div class="container">
      <!-- Site Logo -->
      <a class="navbar-brand logo" href="index.php">
        SLT<span>CP</span>
      </a>

      <!-- Mobile Navigation Toggles -->
      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation Links Navigation Menu -->
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 nav-list">
          <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'about_us.php') ? 'active' : ''; ?>" href="about_us.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'courses.php') ? 'active' : ''; ?>" href="courses.php">Courses</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'tutor.php') ? 'active' : ''; ?>" href="tutor.php">Tutors</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>" href="blog.php">Blog</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a></li>
        </ul>

        <!-- User Authentication Actions -->
        <div class="nav-actions d-flex align-items-center">
          <a href="login.php" class="btn-signin me-3">Sign In</a>
          <a href="register.php" class="btn-cta">Get Started</a>
        </div>
      </div>
    </div>
  </nav>
</header>