<section class="find-courses">
  <div class="find-courses__container">
    
    <!-- Section Header -->
    <div class="find-courses__header">
      <h2 class="find-courses__title">Explore Our Top Courses</h2>
      <p class="subtitle">Find the right course to boost your skills and advance your career.</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="find-courses__filter-bar">
      <div class="find-courses__search">
        <input type="text" placeholder="Search courses (e.g., Web Development, Data Science)..." class="find-courses__input">
        <button class="find-courses__btn-search">Search</button>
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
    </div>

    <!-- Course Grid -->
    <div class="find-courses__grid">
      
      <!-- Course Card 1 -->
      <article class="course-card">
        <div class="course-card__badge">Bestseller</div>
        <img src="https://via.placeholder.com/400x250" alt="Course Thumbnail" class="course-card__img">
        <div class="course-card__body">
          <span class="course-card__category">Web Development</span>
          <h3 class="course-card__title">Full-Stack Modern Web Development</h3>
          <p class="text-small">Master HTML, CSS, JavaScript, and React with real-world project development.</p>
          <div class="course-card__meta">
            <span class="course-card__rating">★ 4.9 (1.2k)</span>
            <span class="course-card__price">$49.99</span>
          </div>
          <a href="#" class="course-card__btn">Enroll Now</a>
        </div>
      </article>

      <!-- Course Card 2 -->
      <article class="course-card">
        <div class="course-card__badge">Featured</div>
        <img src="https://via.placeholder.com/400x250" alt="Course Thumbnail" class="course-card__img">
        <div class="course-card__body">
          <span class="course-card__category">Data Science</span>
          <h3 class="course-card__title">Python for Data Science & Machine Learning</h3>
          <p class="text-small">Learn Python programming, pandas, NumPy, and machine learning fundamentals.</p>
          <div class="course-card__meta">
            <span class="course-card__rating">★ 4.8 (850)</span>
            <span class="course-card__price">$59.99</span>
          </div>
          <a href="#" class="course-card__btn">Enroll Now</a>
        </div>
      </article>

      <!-- Course Card 3 -->
      <article class="course-card">
        <img src="https://via.placeholder.com/400x250" alt="Course Thumbnail" class="course-card__img">
        <div class="course-card__body">
          <span class="course-card__category">UI/UX Design</span>
          <h3 class="course-card__title">UI/UX Design Fundamentals with Figma</h3>
          <p class="text-small">Design modern web and mobile application interfaces from scratch.</p>
          <div class="course-card__meta">
            <span class="course-card__rating">★ 4.7 (620)</span>
            <span class="course-card__price">$39.99</span>
          </div>
          <a href="#" class="course-card__btn">Enroll Now</a>
        </div>
      </article>

    </div>
  </div>
</section>