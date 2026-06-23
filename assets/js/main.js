const container = document.querySelector(".container");
const lekeBtns = document.querySelector(".like-btn");


(function ($) {
"use strict";

    let min = 1;
    let max = 50;

    $(document).on("click", ".increaseBtn", function(){
        let $counter = $(this).siblings(".count");
        let current = parseInt($counter.text());
        if(current < max){
            $counter.text(current + 1);
        }
    });

    $(document).on("click", ".decreaseBtn", function(){
        let $counter = $(this).siblings(".count");
        let current = parseInt($counter.text());
        if(current > min){
            $counter.text(current - 1);
        }
    });

// meanmenu
$('#mobile-menu').meanmenu({
	meanMenuContainer: '.mobile-menu',
	meanScreenWidth: "992"
});

// One Page Nav
//var top_offset = $('.header-area').height() - 10;
//$('.main-menu nav ul').onePageNav({
	//currentClass: 'active',
	//scrollOffset: top_offset,
//});
$(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 245) {
		$(".header-sticky").removeClass("sticky");
	} else {
		$(".header-sticky").addClass("sticky");
	}
});




//Initialize Bannar Swiper
var swiper = new Swiper(".banner-slider", {
	loop: true,
	slidesPerView: 1,
	effect: "fade",
	fadeEffect: {
		crossFade: true
	},
	autoplay: {
		delay: 3000,
		disableOnInteraction: false,
	},
	speed: 1000,
	pagination: {
		el: ".swiper-pagination",
		clickable: true,
	},
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
	on: {
    slideChangeTransitionStart: function () {
      // WOW animation re-init on every slide
      document.querySelectorAll('.wow').forEach(el => {
        el.classList.remove('animate__animated');
        el.style.visibility = 'hidden';
      });
      new WOW().init();
    }
  }
});

//category Initialize Swiper

//  var swiper = new Swiper(".category-swiper", {
//       spaceBetween: 30,
//       centeredSlides: true,
//       autoplay: {
//         delay: 5000,
//         disableOnInteraction: false,
//       },
//       pagination: {
//         el: ".swiper-pagination",
//         clickable: true,
//       },
//       navigation: {
//         nextEl: ".swiper-button-next",
//         prevEl: ".swiper-button-prev",
//       },
//     });


//category Initialize Swiper
var swiper = new Swiper(".category-swiper", {
	  loop: true,
      slidesPerView: 5,
      spaceBetween: 0,
	  autoplay: {
        delay: 3000,
      },
	  speed: 1000,

      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
       navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        640: {
          slidesPerView: 1,
          spaceBetween: 0,
        },
        768: {
          slidesPerView: 4,
          spaceBetween: 0,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 0,
        },
      },
    });
//testimonial Initialize Swiper
var swiper = new Swiper(".testimonial-swiper", {
	  loop: true,
      slidesPerView: 5,
      spaceBetween: 0,
	  autoplay: {
        delay: 3000,
      },
	  speed: 1000,

      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
       navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        640: {
          slidesPerView: 1,
          spaceBetween: 0,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 0,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 0,
        },
      },
    });


//food-card-area Initialize Swiper
var swiper = new Swiper(".food-card-swiper", {
	  loop: true,
      slidesPerView: 5,
      spaceBetween: 0,
	  autoplay: {
        delay: 3000,
      },
	  speed: 1000,

      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
       navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        640: {
          slidesPerView: 1,
          spaceBetween: 0,
        },
        768: {
          slidesPerView: 4,
          spaceBetween: 0,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 0,
        },
      },
    });

    
//letest_news Initialize Swiper
var swiper = new Swiper(".latest_news-swiper", {
	  loop: true,
      slidesPerView: 5,
      spaceBetween: 0,
	  autoplay: {
        delay: 3000,
      },
	  speed: 1000,

      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
       navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        640: {
          slidesPerView: 1,
          spaceBetween: 0,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 0,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 0,
        },
      },
    });
/* magnificPopup img view */
$('.popup-image').magnificPopup({
	type: 'image',
	gallery: {
	  enabled: true
	}
});

/* magnificPopup video view */
$('.popup-video').magnificPopup({
	type: 'iframe'
});


// isotop
$('.grid').imagesLoaded( function() {
	// init Isotope
	var $grid = $('.grid').isotope({
	  itemSelector: '.grid-item',
	  percentPosition: true,
	  masonry: {
		// use outer width of grid-sizer for columnWidth
		columnWidth: '.grid-item',
	  }
	});
});

// filter items on button click
$('.portfolio-menu').on( 'click', 'button', function() {
  var filterValue = $(this).attr('data-filter');
  $grid.isotope({ filter: filterValue });
});

//for menu active class
$('.portfolio-menu button').on('click', function(event) {
	$(this).siblings('.active').removeClass('active');
	$(this).addClass('active');
	event.preventDefault();
});




// scrollToTop
$.scrollUp({
	scrollName: 'scrollUp', // Element ID
	topDistance: '300', // Distance from top before showing element (px)
	topSpeed: 300, // Speed back to top (ms)
	animation: 'fade', // Fade, slide, none
	animationInSpeed: 200, // Animation in speed (ms)
	animationOutSpeed: 200, // Animation out speed (ms)
	scrollText: '<i class="icofont icofont-long-arrow-up"></i>', // Text for element
	activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
});

$(document).ready(function() {
	$('select').niceSelect();
  });

// WOW active
new WOW().init();


})(jQuery);

