<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Handle Item Removal from Cart
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    if (($key = array_search($remove_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
    header("Location: cart.php");
    exit;
}

$cart_courses = [];
$total_price = 0;

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    // Convert cart IDs to comma-separated list for SQL query
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, u.full_name AS tutor_name 
            FROM courses c 
            JOIN users u ON c.tutor_id = u.id 
            WHERE c.id IN ($placeholders)
        ");
        $stmt->execute($_SESSION['cart']);
        $cart_courses = $stmt->fetchAll();
    } catch (PDOException $e) {
        $cart_courses = [];
    }
}

?>

<section class="cart-section py-5 bg-light" style="min-height: 80vh;">
    <div class="container">
        
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Shopping Cart</h2>
            <p class="text-muted">Review your selected courses before proceeding.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($cart_courses) > 0): ?>
            <div class="row g-4">
                <!-- Cart Items List -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h4 class="fw-bold mb-4">Courses in Cart (<?php echo count($cart_courses); ?>)</h4>
                        
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($cart_courses as $course): 
                                $total_price += $course['price'];
                            ?>
                                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 gap-3">
                                    <img src="assets/img/courses/<?php echo htmlspecialchars($course['image']); ?>" alt="Course" class="rounded-3" width="100" height="70" style="object-fit: cover;">
                                    
                                    <div class="flex-grow-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary mb-1"><?php echo htmlspecialchars($course['subtitle']); ?></span>
                                        <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($course['title']); ?></h5>
                                        <p class="text-muted small mb-0">Instructor: <?php echo htmlspecialchars($course['tutor_name']); ?></p>
                                    </div>

                                    <div class="text-end">
                                        <h5 class="fw-bold text-dark mb-2">৳ <?php echo number_format($course['price'], 2); ?></h5>
                                        <a href="cart.php?remove=<?php echo $course['id']; ?>" class="text-danger small text-decoration-none fw-bold">
                                            <i class="fa-solid fa-trash-can me-1"></i> Remove
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Subtotal</span>
                            <span class="fw-bold text-dark">৳ <?php echo number_format($total_price, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Discount</span>
                            <span class="fw-bold text-success">৳ 0.00</span>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5 text-dark">Total</span>
                            <span class="fw-bold fs-5 text-primary">৳ <?php echo number_format($total_price, 2); ?></span>
                        </div>

                        <!-- Checkout / Bulk Enroll Button -->
                        <a href="checkout.php" class="btn btn-primary w-100 py-3 fw-bold fs-5 rounded-3 text-white text-center text-decoration-none shadow-sm">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty Cart State -->
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                <div class="py-4">
                    <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3 opacity-50"></i>
                    <h3 class="fw-bold text-dark mb-2">Your cart is empty</h3>
                    <p class="text-muted mb-4">Looks like you haven't added any courses to your cart yet.</p>
                    <a href="courses.php" class="btn btn-primary fw-bold px-5 py-2">Explore Courses</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>
