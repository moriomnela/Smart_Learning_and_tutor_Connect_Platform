<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Start Meta -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title of Site -->
    <title>Smart_Learning_and_tutor_Connect_Platform</title>
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="assets/img/Homepage-1/favicon-1.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- aminated CSS -->
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <!-- font awesome -->
    <link rel="stylesheet" href="assets/css/all.min.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="assets/css/animate.css">
    <!-- Nice Select CSS -->
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <!-- Swiper -->
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <!-- Magnific -->
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <!-- Mean menu -->
    <link rel="stylesheet" href="assets/css/meanmenu.min.css">
    <!-- default CSS -->
    <link rel="stylesheet" href="assets/css/default.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- slick CSS -->
    <link rel="stylesheet" href="assets/css/slick.css">
</head>

<body>
    <!--Header- area Start-->
    <header>
        <div class="header-area-top align-items-center overflow-hidden">
            <div class="custom__container">
                <div class="top-area">
                    <div class="row justify-content-between">
                        <div class="col-xl-8">
                            <ul>
                                <div class="top-header-left">
                                    <li>
                                        <i class="fas fa-map-marker-alt"></i>
                                        8502 Preston Rd. Inglewood, Maine 98380
                                    </li>
                                    <li>
                                        <i class="far fa-envelope-open"></i>
                                        <span>info@barab.com</span>
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i>
                                        <span>Monday - Friday : 10 AM to 8PM</span>
                                    </li>
                                </div>
                            </ul>
                        </div>
                        <div class="col-xl-4">
                            <div class="top_header_right">
                                <div class="contactinfo">
                                    <div class="call-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?php
                                    session_start();
                                    if (isset($_SESSION['user_id'])) {
                                        // User logged in
                                        echo '<a href="actions/logout.php">Logout</a>';
                                    } else {
                                        // User not logged in
                                        echo '<a href="login.php">Login/Signup</a>';
                                    }
                                    ?>
                                </div>
                                <div class="social-icons">
                                    <li><a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="header-main">
        <div class="custom__container">
            <div class="row align-items-center">
                <div class="col-xl-3">
                    <div class="logo">
                        <a href="index.php">
                            <img src="assets/img/All_img/logo.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="header_menu">
                        <div class="main-menu text-center pt-20 pb-20">
                            <nav id="mobile-menu">
                                <ul>
                                    <li><a href="index.php">Home</a></li>
                                    <li><a href="about.php">About us</a></li>
                                    <li><a href="menu.php">Menu</a></li>
                                    <li><a href="blog.php">blog</a></li>
                                    <li><a href="contact.php">Contact</a></li>
                                </ul>
                            </nav>
                        </div>
                        <div class="mobile-menu"></div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="header__icon d-flex align-items-center justify-content-end">
                        <div class="header__info-wishlist tpcolor__greenish ml-10">
                            <a href="favorite.php" class="tp-search-toggle"><i class="fa-regular fa-heart"></i></a>
                        </div>
                        <div class="header__info-wishlist tpcolor__greenish ml-10">
                            <button class="tp-search-toggle" onclick="window.location.href='cart.php';">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                        <div class="reserve_button">
                            <a href="#" class="button-1">Reserve A table</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Header- area end -->
    <main>
        <!--Slider - area start-->
        <section class="banner-area">
            <div class="swiper banner-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide single-banner-slider">
                        <div class="row align-items-center">
                            <div class="col-xl-7 col-lg-6">
                                <div class="slider-text">
                                    <h1 class="wow fadeInUp animated">Healthy & Delicious Vegan Dishes Guilt Free Eating</h1>
                                    <p class="wow fadeInUp animated"> Fresh, organic, and eco-friendly meals made with love. Experience healthy eating that’s sustainable and delicious</p>
                                    <div class="btn-group">
                                        <a href="" class="button-2 wow fadeInDown animated">order now</a>
                                        <a href="" class="button-3 wow fadeInDown animated">contact us</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-6">
                                <div class="banner-img">
                                    <img src="assets/img/banner_img/banner_img_1.png"
                                        class="wow animate__animated animate__fadeInRight" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide single-banner-slider">
                        <div class="row align-items-center">
                            <div class="col-xl-7 col-lg-6">
                                <div class="slider-text">
                                    <h1 class="wow fadeInUp animated">Healthy & Delicious Vegan Dishes Guilt Free Eating</h1>
                                    <p class="wow fadeInUp animated"> Fresh, organic, and eco-friendly meals made with love. Experience healthy eating that’s sustainable and delicious</p>
                                    <div class="btn-group">
                                        <a href="" class="button-2 wow fadeInDown animated">order now</a>
                                        <a href="" class="button-3 wow fadeInDown animated">contact us</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-6">
                                <div class="banner-img">
                                    <img src="assets/img/banner_img/banner_img_2.jpg"
                                        class="wow animate__animated animate__fadeInRight" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- pagination -->
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>
        <!--Slider - area End-->
        <!--category - area start-->
        <section class="category-area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section-title text-center mt-0">
                            <h4 class="subtitle">Food Category</h4>
                            <h2 class="title">Vegan Fast Foods Category</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom__container">
                <div class="swiper category-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide category_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="category-card">
                                        <div class="category-img">
                                            <img src="assets/img/category/category_1.png" alt="Groceries">
                                        </div>
                                        <div class="category-content">
                                            <div class="category_name">
                                                <h6>Chickpea Curry</h6>
                                                <span>19 Items Available</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide category_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="category-card">
                                        <div class="category-img">
                                            <img src="assets/img/category/category_2.png" alt="Groceries">
                                        </div>
                                        <div class="category-content">
                                            <div class="category_name">
                                                <h6>Chickpea Curry</h6>
                                                <span>19 Items Available</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide category_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="category-card">
                                        <div class="category-img">
                                            <img src="assets/img/category/category_3.png" alt="Groceries">
                                        </div>
                                        <div class="category-content">
                                            <div class="category_name">
                                                <h6>Chickpea Curry</h6>
                                                <span>19 Items Available</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide category_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="category-card">
                                        <div class="category-img">
                                            <img src="assets/img/category/category_4.png" alt="Groceries">
                                        </div>
                                        <div class="category-content">
                                            <div class="category_name">
                                                <h6>Chickpea Curry</h6>
                                                <span>19 Items Available</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide category_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="category-card">
                                        <div class="category-img">
                                            <img src="assets/img/category/category_1.png" alt="Groceries">
                                        </div>
                                        <div class="category-content">
                                            <div class="category_name">
                                                <h6>Chickpea Curry</h6>
                                                <span>19 Items Available</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide category_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="category-card">
                                        <div class="category-img">
                                            <img src="assets/img/category/category_2.png" alt="Groceries">
                                        </div>
                                        <div class="category-content">
                                            <div class="category_name">
                                                <h6>Chickpea Curry</h6>
                                                <span>19 Items Available</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </section>
        <!--category - area end-->
        <!--about-us - area start-->
        <section class="about-us mb-100">
            <div class="about-area">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="about-img">
                                <img src="assets/img/All_img/about_img.png" alt="">
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="about-content pl-60 mt-30">
                                <div class="about-text">
                                    <h4 class="subtitle">About Us</h4>
                                    <h1 class="title">Where Sustainability Meets the Art of Delicious Vegan Living</h1>
                                    <p class="box-text">We are passionate about creating healthy, delicious, and eco-friendly vegan meals. Our mission is to promote sustainable living and inspire a cruelty-free lifestyle through food that’s good for you and the planet</p>
                                </div>
                                <div class="about-list">
                                    <ul>
                                        <li></li>
                                    </ul>
                                </div>
                                <a href="" class="button-2">Visit Our Barab</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--about-us - area end-->
        <!--menu-area start-->
        <section class="menu-area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section-title text-center mt-0">
                            <h4 class="subtitle">Explore Our Menu</h4>
                            <h2 class="title">Sushi Lovers’ Pick – Most Popular Rolls</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom-container">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="menu-img-left">
                            <img src="assets/img/menu/menu-img.png" alt="">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="menu-wrapper d-flex">
                            <div class="item-img">
                                <img src="assets/img/menu/menu-item-1.png" alt="">
                            </div>
                            <div class="menu-content d-flex align-items-end">
                                <div class="menu-list">
                                    <h3 class="menu-title">Grilled Salmon with Dil Sauce</h3>
                                    <p class="menu-text">Candied Jerusalem artichokes, truffle</p>
                                </div>
                                <div class="menu-price">
                                    <span class="product-price"><span class="sign">৳.</span> 500</span>
                                </div>
                            </div>
                        </div>
                        <div class="menu-wrapper d-flex">
                            <div class="item-img">
                                <img src="assets/img/menu/menu-item-2.png" alt="">
                            </div>
                            <div class="menu-content d-flex align-items-end">
                                <div class="menu-list">
                                    <h3 class="menu-title">Roast Beef with Vegetable</h3>
                                    <p class="menu-text">Candied Jerusalem artichokes, truffle</p>
                                </div>
                                <div class="menu-price">
                                    <span class="product-price"><span class="sign">৳.</span> 630</span>
                                </div>
                            </div>
                        </div>
                        <div class="menu-wrapper d-flex">
                            <div class="item-img">
                                <img src="assets/img/menu/menu-item-3.png" alt="">
                            </div>
                            <div class="menu-content d-flex align-items-end">
                                <div class="menu-list">
                                    <h3 class="menu-title">Marrakesh Vegetarian Curry</h3>
                                    <p class="menu-text">Candied Jerusalem artichokes, truffle</p>
                                </div>
                                <div class="menu-price">
                                    <span class="product-price"><span class="sign">৳.</span> 590</span>
                                </div>
                            </div>
                        </div>
                        <div class="menu-wrapper d-flex">
                            <div class="item-img">
                                <img src="assets/img/menu/menu-item-4.png" alt="">
                            </div>
                            <div class="menu-content d-flex align-items-end">
                                <div class="menu-list">
                                    <h3 class="menu-title">Spicy Vegan Potato Curry</h3>
                                    <p class="menu-text">Candied Jerusalem artichokes, truffle</p>
                                </div>
                                <div class="menu-price">
                                    <span class="product-price"><span class="sign">৳.</span> 700</span>
                                </div>
                            </div>
                        </div>
                        <div class="menu-wrapper d-flex">
                            <div class="item-img">
                                <img src="assets/img/menu/menu-item-5.png" alt="">
                            </div>
                            <div class="menu-content d-flex align-items-end">
                                <div class="menu-list">
                                    <h3 class="menu-title">Apple Pie with Cream</h3>
                                    <p class="menu-text">Candied Jerusalem artichokes, truffle</p>
                                </div>
                                <div class="menu-price">
                                    <span class="product-price"><span class="sign">৳.</span> 600</span>
                                </div>
                            </div>
                        </div>
                        <div class="menu-wrapper d-flex">
                            <div class="item-img">
                                <img src="assets/img/menu/menu-item-6.png" alt="">
                            </div>
                            <div class="menu-content d-flex align-items-end">
                                <div class="menu-list">
                                    <a href="">
                                        <h3 class="menu-title">French Onion Soup</h3>
                                    </a>
                                    <p class="menu-text">Candied Jerusalem artichokes, truffle</p>
                                </div>
                                <div class="menu-price">
                                    <span class="product-price"><span class="sign">৳.</span> 350</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="menu-right-img">
                        <div class="menu-img-right">
                            <img src="assets/img/menu/right_img.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--menu-area end-->
        <!--cta-area start-->
        <div class="cta-area">
            <div class="row">
                <div class="col-xl-4">
                    <div class="cta-wrapper">
                        <div class="cta-title">
                            <h5>Super Delicious</h5>
                            <h3>Greenie</h3>
                        </div>
                        <div class="cta-img">
                            <img src="assets/img/All_img/cta-img-1-.png" alt="">
                        </div>
                        <div class="cta-offer-price">
                            <h4>40% OFF</h4>
                        </div>
                        <div class="cta-button">
                            <a href="" class="button-1">order now</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="cta-wrapper cta-wrapper-2">
                        <div class="cta-title cta-title-2">
                            <h5>Super Delicious</h5>
                            <h3>Best Veggie</h3>
                        </div>
                        <div class="cta-img">
                            <img src="assets/img/All_img/cta-img-2-.png" alt="">
                        </div>
                        <div class="cta-offer-price cta-offer-price-2">
                            <h4>45% OFF</h4>
                        </div>
                        <div class="cta-button">
                            <a href="" class="button-1 cta-btn-2">order now</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="cta-wrapper cta-wrapper-3">
                        <div class="cta-title">
                            <h5>Super Delicious</h5>
                            <h3>Vegora</h3>
                        </div>
                        <div class="cta-img">
                            <img src="assets/img/All_img/cta-img-3-.png" alt="">
                        </div>
                        <div class="cta-offer-price cta-offer-price-3">
                            <h4>50% OFF</h4>
                        </div>
                        <div class="cta-button">
                            <a href="" class="button-1 cta-btn-3">order now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--cta-area end-->
        <!--why-choose-us-area start-->
        <div class="why-choose-area mb-100 mt-100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-5">
                        <div class="why-choose-us-content">
                            <div class="why-choose-us-text">
                                <h4 class="subtitle">Why Choose Us</h4>
                                <h1 class="title">Our Clients Choose Us</h1>
                                <p class="box-text">Choosing a vegan lifestyle is more than just a diet—it’s a step toward a healthier body and a healthier planet. By embracing plant-based meals you natura lower the risk of chronic diseases</p>
                            </div>
                            <div class="why-choose-us-list">
                                <ul d-flex>
                                    <div class="list">
                                        <li><img src="assets/img/All_img/why-choose-icon-1.png" alt="">
                                            <h4>Plant-Based Delights</h4>
                                        </li>
                                        <li><img src="assets/img/All_img/why-choose-icon-2.png" alt="">
                                            <h4>Plant-Based Delights</h4>
                                        </li>
                                    </div>
                                    <div class="list">
                                        <li><img src="assets/img/All_img/why-choose-icon-3.png" alt="">
                                            <h4>Plant-Based Delights</h4>
                                        </li>
                                        <li><img src="assets/img/All_img/why-choose-icon-4.png" alt="">
                                            <h4>Plant-Based Delights</h4>
                                        </li>
                                    </div>
                                </ul>
                            </div>
                        </div>
                        <div class="why-choose-us-button">
                            <a href="" class="button-1">See All Dishes</a>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="why-choose-img d-flex">
                            <div class="why-choose-img-1 mr-30">
                                <img src="assets/img/All_img/why-choose-img-1.png" alt="">
                                <div class="why-4-content">
                                    <img src="" alt="">
                                </div>
                            </div>
                            <div class="why-choose-img-2">
                                <img src="assets/img/All_img/why-choose-img-2.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--why-choose-us-area end-->
        <!--testimonial-area start-->
        <div class="testimonial-area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section-title text-center mt-0">
                            <h4>Testimonial</h4>
                            <h2>Loved by Our Community</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom__container">
                <div class="swiper testimonial-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide testimonial_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="testimonial-card">
                                        <div class="testimonial-reating">
                                            <ul class="star">
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                        <div class="category-content">
                                            <h5 class="content">“As someone who follows a plant diet health
                                                reasons I often struggle to find places that serve
                                                delicious and nutritious restaurant truly delivers
                                                feeling energetic and satisfied.”</h5>
                                            <h4 class="name text-center">David Anderson</h4>
                                            <p class="rest-name text-center">Food Vegan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide testimonial_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="testimonial-card">
                                        <div class="testimonial-reating">
                                            <ul class="star">
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                        <div class="category-content">
                                            <h5 class="content">“As someone who follows a plant diet health
                                                reasons I often struggle to find places that serve
                                                delicious and nutritious restaurant truly delivers
                                                feeling energetic and satisfied.”</h5>
                                            <h4 class="name text-center">David Anderson</h4>
                                            <p class="rest-name text-center">Food Vegan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide testimonial_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="testimonial-card">
                                        <div class="testimonial-reating">
                                            <ul class="star">
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                        <div class="category-content">
                                            <h5 class="content">“As someone who follows a plant diet health
                                                reasons I often struggle to find places that serve
                                                delicious and nutritious restaurant truly delivers
                                                feeling energetic and satisfied.”</h5>
                                            <h4 class="name text-center">David Anderson</h4>
                                            <p class="rest-name text-center">Food Vegan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--testimonial-area end-->
        <!--fast-food-area start-->
        <section class="food_card_area">
            <div class="container">
                <div class="section-title">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-xl-6">
                            <h4 class="subtitle">Our Vegan Foods</h4>
                            <h2 class="title mb-0">Best selling Vegan foods</h2>
                        </div>
                        <div class="col-auto">
                            <div class="section-title-slider-button">
                                <div class="swiper-button-prev section-title-arrow"></div>
                                <div class="swiper-button-next section-title-arrow"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom__container">
                <div class="swiper food-card-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide food-card_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="food-card">
                                        <div class="food-card-img">
                                            <img src="assets/img/fast-food/fast_food_1.png" alt="Groceries">
                                        </div>
                                        <div class="food-card-content">
                                            <div class="food-card_name">
                                                <h6>Avocado Toast</h6>
                                                <span>৳320.00</span>
                                            </div>
                                            <a href="" class="button-2">Add To cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide food-card_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="food-card">
                                        <div class="food-card-img">
                                            <img src="assets/img/fast-food/fast_food_2.png" alt="Groceries">
                                        </div>
                                        <div class="food-card-content">
                                            <div class="food-card_name">
                                                <h6>Avocado Toast</h6>
                                                <span>৳320.00</span>
                                            </div>
                                            <a href="" class="button-2">Add To cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide food-card_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="food-card">
                                        <div class="food-card-img">
                                            <img src="assets/img/fast-food/fast_food_3.png" alt="Groceries">
                                        </div>
                                        <div class="food-card-content">
                                            <div class="food-card_name">
                                                <h6>Avocado Toast</h6>
                                                <span>৳320.00</span>
                                            </div>
                                            <a href="" class="button-2">Add To cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide food-card_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="food-card">
                                        <div class="food-card-img">
                                            <img src="assets/img/fast-food/fast_food_4.png" alt="Groceries">
                                        </div>
                                        <div class="food-card-content">
                                            <div class="food-card_name">
                                                <h6>Avocado Toast</h6>
                                                <span>৳320.00</span>
                                            </div>
                                            <a href="" class="button-2">Add To cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide food-card_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="food-card">
                                        <div class="food-card-img">
                                            <img src="assets/img/fast-food/fast_food_2.png" alt="Groceries">
                                        </div>
                                        <div class="food-card-content">
                                            <div class="food-card_name">
                                                <h6>Avocado Toast</h6>
                                                <span>৳320.00</span>
                                            </div>
                                            <a href="" class="button-2">Add To cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide food-card_slider">
                            <div class="row mb-40 ml-10">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="food-card">
                                        <div class="food-card-img">
                                            <img src="assets/img/fast-food/fast_food_1.png" alt="Groceries">
                                        </div>
                                        <div class="food-card-content">
                                            <div class="food-card_name">
                                                <h6>Avocado Toast</h6>
                                                <span>৳320.00</span>
                                            </div>
                                            <a href="" class="button-2">Add To cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--fast-food-area end-->
        <!--latest-news-area start-->
        <div class="latest-news-area">
            <div class="custom__container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section-title  mt-0">
                            <h4 class="subtitle">Latest news</h4>
                            <h2 class="title">Latest news and updates</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom__container">
                <div class="swiper latest_news-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide latest_news_slider">
                            <div class="row mb-40">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="latest_news-card">
                                        <div class="latest_news-image">
                                            <img src="assets/img/All_img/blog1.png" alt="">
                                        </div>
                                        <div class="latest_news-icon d-flex">
                                            <div class="user d-flex">
                                                <a href=""><i class="far fa-user"></i></a>
                                                <p class="name">By Adam Smith</p>
                                            </div>
                                            <div class="cal d-flex">
                                                <a href=""><i class="far fa-calendar"></i></a>
                                                <p class="name">August 31, 2025</p>
                                            </div>
                                        </div>
                                        <div class="latest_news-content">
                                            <h5 class="content">Guide to Living a Healthy Plant-Based Lifestyle for Beginners</h5>
                                            <a href="">Read more <i class="fas fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide latest_news_slider">
                            <div class="row mb-40">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="latest_news-card">
                                        <div class="latest_news-image">
                                            <img src="assets/img/All_img/blog2.png" alt="">
                                        </div>
                                        <div class="latest_news-icon d-flex">
                                            <div class="user d-flex">
                                                <a href=""><i class="far fa-user"></i></a>
                                                <p class="name">By Adam Smith</p>
                                            </div>
                                            <div class="cal d-flex">
                                                <a href=""><i class="far fa-calendar"></i></a>
                                                <p class="name">August 31, 2025</p>
                                            </div>
                                        </div>
                                        <div class="latest_news-content">
                                            <h5 class="content">Easy Vegan Recipes That Will Save You Time and Keep You Healthy</h5>
                                            <a href="">Read more <i class="fas fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide latest_news_slider">
                            <div class="row mb-40">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="latest_news-card">
                                        <div class="latest_news-image">
                                            <img src="assets/img/All_img/blog3.png" alt="">
                                        </div>
                                        <div class="latest_news-icon d-flex">
                                            <div class="user d-flex">
                                                <a href=""><i class="far fa-user"></i></a>
                                                <p class="name">By Adam Smith</p>
                                            </div>
                                            <div class="cal d-flex">
                                                <a href=""><i class="far fa-calendar"></i></a>
                                                <p class="name">August 31, 2025</p>
                                            </div>
                                        </div>
                                        <div class="latest_news-content">
                                            <h5 class="content">Choosing Organic Ingredients Makes a Big Difference in Your Daily Meals</h5>
                                            <a href="">Read more <i class="fas fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide latest_news_slider">
                            <div class="row mb-40">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="latest_news-card">
                                        <div class="latest_news-image">
                                            <img src="assets/img/All_img/blog1.png" alt="">
                                        </div>
                                        <div class="latest_news-icon d-flex">
                                            <div class="user d-flex">
                                                <a href=""><i class="far fa-user"></i></a>
                                                <p class="name">By Adam Smith</p>
                                            </div>
                                            <div class="cal d-flex">
                                                <a href=""><i class="far fa-calendar"></i></a>
                                                <p class="name">August 31, 2025</p>
                                            </div>
                                        </div>
                                        <div class="latest_news-content">
                                            <h5 class="content">Guide to Living a Healthy Plant-Based Lifestyle for Beginners</h5>
                                            <a href="">Read more <i class="fas fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide latest_news_slider">
                            <div class="row mb-40">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="latest_news-card">
                                        <div class="latest_news-image">
                                            <img src="assets/img/All_img/blog2.png" alt="">
                                        </div>
                                        <div class="latest_news-icon d-flex">
                                            <div class="user d-flex">
                                                <a href=""><i class="far fa-user"></i></a>
                                                <p class="name">By Adam Smith</p>
                                            </div>
                                            <div class="cal d-flex">
                                                <a href=""><i class="far fa-calendar"></i></a>
                                                <p class="name">August 31, 2025</p>
                                            </div>
                                        </div>
                                        <div class="latest_news-content">
                                            <h5 class="content">Easy Vegan Recipes That Will Save You Time and Keep You Healthy</h5>
                                            <a href="">Read more <i class="fas fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide latest_news_slider">
                            <div class="row mb-40">
                                <div class="col-xl-12 col-lg-3 col-md-4 col-6">
                                    <div class="latest_news-card">
                                        <div class="latest_news-image">
                                            <img src="assets/img/All_img/blog3.png" alt="">
                                        </div>
                                        <div class="latest_news-icon d-flex">
                                            <div class="user d-flex">
                                                <a href=""><i class="far fa-user"></i></a>
                                                <p class="name">By Adam Smith</p>
                                            </div>
                                            <div class="cal d-flex">
                                                <a href=""><i class="far fa-calendar"></i></a>
                                                <p class="name">August 31, 2025</p>
                                            </div>
                                        </div>
                                        <div class="latest_news-content">
                                            <h5 class="content">Choosing Organic Ingredients Makes a Big Difference in Your Daily Meals</h5>
                                            <a href="">Read more <i class="fas fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--latest-news-area end-->
        <!--Slider - area start-->
        <section class="letstalk-area">
            <div class="letstalk">
                <div class="row align-items-center">
                    <div class="col-xl-12 col-lg-12">
                        <div class="letstalk-text text-center">
                            <h1 class="wow fadeInUp animated">LET'S TALK</h1>
                        </div>
                        <div class="fram">
                            <a href="">Instant Free quote</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Slider - area End-->



        <!-- ============ Shop single product quick view modal ================= -->
        <div class="quick-view-modal modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content natural-scroll">
                    <div class="modal-header mb-30">
                        <h5 class="modal-title" id="quickViewLabel">
                            <span class="icon">
                                <i class="fa-regular fa-eye"></i>
                            </span>
                            Quick View
                        </h5>
                        <button type="" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-5">
                                <div class="product-image-view">
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="image-1" role="tabpanel" aria-labelledby="image-view-1">
                                            <img src="assets/img/shop/Oil.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 ps-xl-5">
                                <div class="product-details pb-60">
                                    <span class="subtitle">Groceries</span>
                                    <h3>Sunflower Oil</h3>
                                    <div class="price__review">
                                        <h5 class="price">৳890.00</h5>
                                        <div class="review">
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star empty"></i>
                                            </div>
                                            <span class="total-review">(6 Reviews)</span>
                                        </div>
                                    </div>
                                    <p class="abt-product"> The Mineral UV Filter formulas avoid the use of any nanoparticles. Instead, a refined dispersion... </p>
                                    <div class="product-quantity">
                                        <h6 class="title">Quantity</h6>
                                        <div class="quantity-wrapper">
                                            <div class="quantity-counter">
                                                <div class="decreaseBtn" id="decrease">
                                                    <svg width="12" height="2" viewBox="0 0 12 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 1H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <span class="count" id="quantity">1</span>
                                                <div class="increaseBtn" id="increase">
                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 6H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M6 11V1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <a href='addToCart.php?.id=<?php echo $row["prodect_id"] ?>' class="button-2">Add to cart</a>

                                            <div class="favorite-button">
                                                <div class="icon">
                                                    <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.52254 8.4852C2.19696 10.5434 4.01585 12.375 5.674 13.6961C7.36651 15.0447 9.75034 15.0409 11.4435 13.6932C13.1121 12.3651 14.9421 10.5284 15.5969 8.48848C16.3421 6.20266 15.6504 3.30724 12.9018 2.43836C11.5701 2.01911 10.0168 2.27427 8.94447 3.08817C8.7203 3.25719 8.40913 3.26047 8.18329 3.09309C7.04737 2.2554 5.56349 2.01008 4.21093 2.43836C1.46649 3.30642 0.777247 6.20184 1.52254 8.4852ZM8.56367 17C8.45995 17 8.35706 16.9754 8.26338 16.9253C8.00159 16.785 1.83524 13.4511 0.331538 8.86197C0.331338 8.86136 0.330443 8.86084 0.330371 8.8602C0.330354 8.86004 0.330342 8.86005 0.330293 8.85991C-0.613109 5.97026 0.437522 2.33904 3.82929 1.26672C4.21448 1.14448 4.60804 1.06482 5.00324 1.02657C7.27014 0.807191 9.85567 0.832092 12.124 1.0362C12.5201 1.07185 12.9117 1.14815 13.2888 1.26672C16.684 2.34071 17.738 5.97127 16.7953 8.86013C15.3407 13.3973 9.12828 16.7817 8.86479 16.9237C8.77111 16.9745 8.66739 17 8.56367 17Z" fill="currentColor" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.5116 7.78932C13.1879 7.78932 12.9135 7.54564 12.8867 7.22402C12.8315 6.5496 12.3715 5.9802 11.7165 5.77262C11.3861 5.6676 11.2055 5.32054 11.3117 4.9981C11.4196 4.67483 11.7701 4.49926 12.1013 4.60099C13.2414 4.96282 14.0402 5.95312 14.1381 7.12393C14.1657 7.46278 13.9089 7.75979 13.5634 7.78686C13.5459 7.7885 13.5291 7.78932 13.5116 7.78932Z" fill="currentColor" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="meta-wrap">
                                        <li><span class="key">Availability: </span> 40 In stock</li>
                                        <li><span class="key">Categories: </span> Fresh Pamp</li>
                                        <li><span class="key">Tags: </span> Oil, fresh</li>
                                    </ul>
                                    <div class="social-links">
                                        <ul class="list-wrap d-flex align-items-center">
                                            <li><a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="https://www.linkedin.com/" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                            <li><a href="https://www.vimeo.com/" target="_blank"><i class="fab fa-vimeo-v"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="payment-method">
                                        <span>Guaranteed Safe & Secure Checkout</span>
                                        <div class="methods">
                                            <div class="single-method">
                                                <img src="assets/img/shop/payment-method-1.png" alt="img">
                                            </div>
                                            <div class="single-method">
                                                <img src="assets/img/shop/payment-method-2.png" alt="img">
                                            </div>
                                            <div class="single-method">
                                                <img src="assets/img/shop/payment-method-3.png" alt="img">
                                            </div>
                                            <div class="single-method">
                                                <img src="assets/img/shop/payment-method-4.png" alt="img">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="footer-bg" style="background-image: url(assets/img/Homepage-1/footer-bg.png)">
        <div class="footer-area pt-40 pb-60">
            <div class="container">
                <div class="row">
                    <div class="footer-logo text-center pb-60">
                        <div class="footer-border-left"></div>
                        <a href=""><img src="assets/img/All_img/footerLogo.png" alt=""></a>
                        <div class="footer-border-right"></div>
                    </div>
                    <div class="col-xl-4 col-lg-3 col-md-4">
                        <div class="footer-widget-left">
                            <h3>Useful Links</h3>
                            <ul>
                                <li><a href="#">Home</a></li>
                                <li><a href="#">Our History</a></li>
                                <li><a href="#">Favorite Place</a></li>
                                <li><a href="#">Contact Us</a></li>
                                <li><a href="#">Our Brand</a></li>
                                <li><a href="#">Place To Get Lost</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-8">
                        <div class="footer-widget-middle mb-30 text-center">
                            <div class="icon text-center">
                                <i class="far fa-clock"></i>
                            </div>
                            <p class="footer-subtitle">We’re currently open!</p>
                            <p class="time">Opening Hours: 8:00AM To 10:00PM</p>
                            <p>Sunday - Thursday</p>
                        </div>
                        <div class="footer-social text-center">
                            <li>
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </li>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-3 col-md-4">
                        <div class="footer-widget-right">
                            <h3>My Account</h3>
                            <ul>
                                <li><a href="#">Burgers</a></li>
                                <li><a href="#">Crispy Flavors</a></li>
                                <li><a href="#">Breakfast Menu</a></li>
                                <li><a href="#">Desserts</a></li>
                                <li><a href="#">Kids Menus</a></li>
                                <li><a href="#">Be verages</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <div class="container">
                <div class="copyright-border">
                    <div class="row justify-content-between">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="copyright">
                                <p>Copyright © 2019 Xisen. All rights reserv</p>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="footer-menu">
                                <ul>
                                    <li>About</li>
                                    <li>Tips & Tricks</li>
                                    <li>Service</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll Btn End -->
    <!-- Main JS -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- wow JS -->
    <script src="assets/js/wow.min.js"></script>
    <!-- Counter up -->
    <script src="assets/js/jquery.counterup.min.js"></script>
    <!-- Popper JS -->
    <script src="assets/js/popper.min.js"></script>
    <!-- Progressbar JS -->
    <script src="assets/js/progressbar.min.js"></script>
    <!-- Magnific JS -->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Swiper JS -->
    <script src="assets/js/swiper-bundle.min.js"></script>
    <!-- Waypoints JS -->
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <!-- Isotope JS -->
    <script src="assets/js/isotope.pkgd.min.js"></script>
    <!-- Mean menu -->
    <script src="assets/js/jquery.meanmenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/fontawesome.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <!-- one-page nav JS -->
    <script src="assets/js/one-page-nav-min.js"></script>
</body>

</html>