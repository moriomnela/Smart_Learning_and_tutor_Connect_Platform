<?php
ob_start(); // Headers already sent error prevent korar jonno
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Checkout";
include 'components/template-top.php';

include 'components/header.php';
include 'components/page_banner.php';
include 'components/checkout.php';
include 'components/footer.php';

include 'components/template-bottom.php';

ob_end_flush();