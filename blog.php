<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Blog";
include 'components/template-top.php';

include 'components/header.php';
include 'components/page_banner.php';
include 'components/blog.php';
include 'components/footer.php';

include 'components/template-bottom.php';
