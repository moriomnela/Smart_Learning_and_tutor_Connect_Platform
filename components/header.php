<?php
// Header session & latest avatar sync check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect if we are inside a subfolder (like student/, tutor/, admin/)
$is_subfolder = file_exists('../config/db.php') || file_exists('../db.php');
$path_prefix = $is_subfolder ? '../' : '';

// Fetch latest avatar from database safely if user is logged in
if (isset($_SESSION['is_logged_in']) && isset($_SESSION['user_id'])) {
    $db_file = $path_prefix . 'config/db.php';
    if (file_exists($db_file)) {
        require_once $db_file;
        try {
            $nav_stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
            $nav_stmt->execute([$_SESSION['user_id']]);
            $nav_user = $nav_stmt->fetch();
            if ($nav_user && !empty($nav_user['avatar'])) {
                $_SESSION['avatar'] = $nav_user['avatar'];
            }
        } catch (PDOException $e) {
            // Fallback silently
        }
    }
}
?>
<header class="main-header sticky-top">
  <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
    <div class="container">
      <!-- Site Logo -->
      <a class="navbar-brand logo" href="<?php echo $path_prefix; ?>index.php">
        SLT<span>CP</span>
      </a>

      <!-- Mobile Navigation Toggles -->
      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation Links Navigation Menu -->
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 nav-list">
        <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about_us.php') ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>about_us.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'courses.php') ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>courses.php">Courses</a></li>
        <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'tutor.php') ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>tutor.php">Tutors</a></li>
        <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>blog.php">Blogs</a></li>
        <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>contact.php">Contact</a></li>
        </ul>

        <!-- User Authentication Actions -->
        <div class="nav-actions d-flex align-items-center gap-3">
            
            <!-- Cart Icon (Always Visible for everyone) -->
            <a href="<?php echo $path_prefix; ?>cart.php" class="cart-icon-circle overflow-visible position-relative d-flex align-items-center justify-content-center text-dark text-decoration-none" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-cart-shopping fs-5"></i>
                <?php if(isset($_SESSION['cart']) && is_array($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                        <?php echo count($_SESSION['cart']); ?>
                    </span>
                <?php endif; ?>
            </a>

            <?php if (isset($_SESSION['is_logged_in'])): ?>
                <?php 
                    $avatar_path = $_SESSION['avatar'] ?? 'default-avatar.png';
                    
                    if ($avatar_path === 'default-avatar.png' || empty($avatar_path)) {
                        $avatar_img = $path_prefix . 'assets/img/profiles/default-avatar.png';
                    } elseif (str_starts_with($avatar_path, 'assets/')) {
                        $avatar_img = $path_prefix . $avatar_path;
                    } else {
                        $avatar_img = $path_prefix . 'assets/img/profiles/' . $avatar_path;
                    }
                ?>
                <!-- User Info + Avatar Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="cart-icon-circle me-2">
                            <img src="<?php echo htmlspecialchars($avatar_img); ?>" 
                                width="45" height="45" style="object-fit: cover;" onerror="this.src='<?php echo $path_prefix; ?>assets/img/profiles/default-avatar.png'">
                        </div>
                        <div class="d-none d-lg-block text-start">
                            <div class="small fw-bold text-dark lh-1"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                            <div class="x-small text-muted"><?php echo ucfirst($_SESSION['role']); ?></div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?php echo $path_prefix . $_SESSION['role']; ?>/dashboard.php"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo $path_prefix; ?>logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                    </ul>
                </div>

            <?php else: ?>
                <a href="<?php echo $path_prefix; ?>login.php" class="btn-signin me-2">Sign In</a>
                <a href="<?php echo $path_prefix; ?>register.php" class="btn-cta">Get Started</a>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
</header>