<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "About Us";
include 'components/template-top.php';

include 'components/header.php';
include 'components/page_banner.php';
include 'components/about.php';
include 'components/categories.php';
include 'components/testimonial.php';
include 'components/popular-teachers.php';
include 'components/instructor-cta.php';
include 'components/footer.php';

include 'components/template-bottom.php';
