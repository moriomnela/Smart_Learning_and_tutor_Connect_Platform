<?php
require_once 'config/db.php';

// Handle search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT c.*, u.full_name AS tutor_name FROM courses c JOIN users u ON c.tutor_id = u.id WHERE c.title LIKE ? OR c.description LIKE ? ORDER BY c.id DESC");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT c.*, u.full_name AS tutor_name FROM courses c JOIN users u ON c.tutor_id = u.id ORDER BY c.id DESC");
    }
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
}

?>

<section class="find-courses">
  <div class="find-courses__container">
    
    <!-- Section Header -->
    <div class="find-courses__header">
      <h1 class="find-courses__title">Explore Our Top Courses</h1>
      <p class="subtitle">Find the right course to boost your skills and advance your career.</p>
    </div>

    <!-- Search & Filter Bar -->
    <form action="courses.php" method="GET" class="find-courses__filter-bar">
      <div class="find-courses__search">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search courses (e.g., Web Development, Data Science)..." class="find-courses__input">
        <button type="submit" class="find-courses__btn-search">Search</button>
      </div>

      <div class="find-courses__select-group">
        <select class="find-courses__select">
          <option value="">All Categories</option>
          <option value="web-dev">Web Development</option>
          <option value="data-science">Data Science</option>
          <option value="ui-ux">UI/UX Design</option>
        </select>

        <select class="find-courses__select">
          <option value="">Sort By</option>
          <option value="popular">Most Popular</option>
          <option value="rating">Highest Rated</option>
          <option value="newest">Newest</option>
        </select>
      </div>
    </form>

    <!-- Course Grid -->
    <div class="find-courses__grid">
      
      <?php if (count($courses) > 0): ?>
          <?php foreach ($courses as $course): ?>
              <article class="course-card">
                <div class="course-card__badge">Instructor: <?php echo htmlspecialchars($course['tutor_name']); ?></div>
                <img src="assets/img/courses/<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="course-card__img">
                <div class="course-card__body">
                  <span class="course-card__category">Active Course</span>
                  <h3 class="course-card__title"><?php echo htmlspecialchars($course['title']); ?></h3>
                  <p class="text-small"><?php echo htmlspecialchars($course['description']); ?></p>
                  <div class="course-card__meta">
                    <span class="course-card__rating">★ 4.8</span>
                    <span class="course-card__price">৳ <?php echo number_format($course['price'], 2); ?></span>
                  </div>
                  <a href="course-details.php?id=<?php echo $course['id']; ?>" class="course-card__btn">View Details</a>
                </div>
              </article>
          <?php endforeach; ?>
      <?php else: ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
              <h3>No courses found</h3>
              <p>We couldn't find any courses matching your search criteria.</p>
              <a href="courses.php" style="color: var(--primary-color); font-weight: bold;">View All Courses</a>
          </div>
      <?php endif; ?>

    </div>
  </div>
</section>
