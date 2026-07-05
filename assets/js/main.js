(function () {
  "use strict";

  // 1. Initialize Categories Swiper Slider
  function initCategoriesSwiper() {
    const swiperContainer = document.querySelector('.categories-swiper');

    if (swiperContainer && typeof Swiper !== 'undefined') {
      new Swiper('.categories-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: '.swiper-button-next-custom',
          prevEl: '.swiper-button-prev-custom',
        },
        pagination: {
          el: '.swiper-pagination-custom',
          clickable: true,
        },
        breakpoints: {
          576: { slidesPerView: 2 },
          768: { slidesPerView: 3 },
          992: { slidesPerView: 4 },
          1200: { slidesPerView: 5 },
          1400: { slidesPerView: 5 }
        }
      });
    }
  }

  // 2. Initialize Popular Courses Swiper Slider
  function initPopularCoursesSwiper() {
    const courseContainer = document.querySelector('.popular-courses-swiper');

    if (courseContainer && typeof Swiper !== 'undefined') {
      new Swiper('.popular-courses-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        navigation: {
          nextEl: '.course-next',
          prevEl: '.course-prev',
        },
        breakpoints: {
          576: { slidesPerView: 1 },
          768: { slidesPerView: 2 },
          992: { slidesPerView: 3 },
          1200: { slidesPerView: 3 }
        }
      });
    }
  }

  function initPopularTeachersSwiper() {
    const teacherContainer = document.querySelector('.popular-teachers-swiper');

    if (teacherContainer && typeof Swiper !== 'undefined') {
      new Swiper('.popular-teachers-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        navigation: {
          nextEl: '.teacher-next',
          prevEl: '.teacher-prev',
        },
        breakpoints: {
          576: { slidesPerView: 1 },
          768: { slidesPerView: 2 },
          992: { slidesPerView: 3 },
          1200: { slidesPerView: 4 } // Fits beautifully on the 1440px viewport
        }
      });
    }
  }

  // Initialize Testimonial Deck Swiper
  function initTestimonialSwiper() {
    const testiContainer = document.querySelector('.testimonial-swiper');

    if (testiContainer && typeof Swiper !== 'undefined') {
      new Swiper('.testimonial-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        navigation: {
          nextEl: '.testi-next',
          prevEl: '.testi-prev',
        },
        breakpoints: {
          768: { slidesPerView: 1 },
          992: { slidesPerView: 2 } 
        }
      });
    }
  }

  // Toggle Password Visibility Logic
  function initPasswordToggle() {
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('userPassword');

    if (toggleBtn && passwordInput) {
      toggleBtn.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle eye icon class
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
    }
  }


  document.addEventListener('DOMContentLoaded', function () {
    initPasswordToggle();
  });

  // Master controller initialization logic
  function initAllSliders() {
    initCategoriesSwiper();
    initPopularCoursesSwiper();
    initPopularTeachersSwiper()
    initTestimonialSwiper()
  }

  /* Core Event Loader Trigger */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllSliders);
  } else {
    initAllSliders();
  }
})();