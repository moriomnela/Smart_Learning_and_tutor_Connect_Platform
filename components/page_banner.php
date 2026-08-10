<?php 
    $title = isset($page_title) ? $page_title : 'SLTCP Platform';
?>

<section class="page-banner">
    <!-- Solid/Gradient Background -->
    <div class="banner-bg"></div>
    
    <div class="container relative-z">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="banner-title"><?php echo $title; ?></h1>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $title; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- SVG Wave Shape Divider bottom -->
    <div class="custom-shape-divider-bottom">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,120.22,187.4,103.11,232.88,89.5,278.43,72.76,321.39,56.44Z" class="shape-fill"></path>
        </svg>
    </div>
</section>