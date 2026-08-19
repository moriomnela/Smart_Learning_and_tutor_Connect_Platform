<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Security Check: Only logged-in students can access checkout
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'student') {
    $_SESSION['error'] = "Please sign in as a student to proceed to checkout.";
    header("Location: login.php");
    exit;
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: cart.php");
    exit;
}

// Fetch cart items for summary
$cart_courses = [];
$total_price = 0;

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

// Fetch student details for auto-filling the form
$student_id = $_SESSION['user_id'];
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$student_id]);
$student = $user_stmt->fetch();
?>

<section class="checkout-section py-5" style="min-height: 80vh;">
    <div class="container">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Secure Checkout</h2>
            <p class="text-muted">Complete your billing details and confirm your course enrollment.</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger rounded-3 mb-4 fw-medium">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="backend/checkout-process.php" method="POST">
            <div class="row g-4">
                <!-- Billing Details Column -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h4 class="fw-bold mb-4">Billing Information</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="017xxxxxxxx" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">City / Location</label>
                                <input type="text" name="city" class="form-control" placeholder="Dhaka" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h4 class="fw-bold mb-4">Payment Method</h4>
                        <div class="d-flex flex-column gap-3">
                            <div class="form-check border p-3 rounded-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="bkash" value="bKash" checked>
                                <label class="form-check-label fw-bold text-dark w-100" for="bkash">
                                    <i class="fa-solid fa-mobile-screen-button text-danger me-2"></i> bKash / Nagad (Mobile Banking Simulator)
                                </label>
                            </div>
                            <div class="form-check border p-3 rounded-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="Card">
                                <label class="form-check-label fw-bold text-dark w-100" for="card">
                                    <i class="fa-solid fa-credit-card text-primary me-2"></i> Credit / Debit Card
                                </label>
                            </div>
                            <div class="form-check border p-3 rounded-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="free" value="Free Enrolment">
                                <label class="form-check-label fw-bold text-dark w-100" for="free">
                                    <i class="fa-solid fa-gift text-success me-2"></i> Direct Enrolment (Free / Institutional)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px;">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
                        
                        <div class="d-flex flex-column gap-3 mb-4" style="max-height: 250px; overflow-y: auto;">
                            <?php foreach ($cart_courses as $course): 
                                $total_price += $course['price'];
                            ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small fw-bold text-dark text-truncate" style="max-width: 180px;"><?php echo htmlspecialchars($course['title']); ?></div>
                                    <div class="small text-muted">৳ <?php echo number_format($course['price'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Subtotal</span>
                            <span class="fw-bold text-dark">৳ <?php echo number_format($total_price, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Platform Fee</span>
                            <span class="fw-bold text-success">Free</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5 text-dark">Total Payable</span>
                            <span class="fw-bold fs-5 text-primary">৳ <?php echo number_format($total_price, 2); ?></span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5 rounded-3 text-white text-center shadow-sm">
                            Confirm & Pay Now
                        </button>
                        <a href="cart.php" class="btn btn-outline-secondary w-100 py-2 mt-2 fw-medium rounded-3 text-center">
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>