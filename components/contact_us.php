<section class="contact-page-wrapper">
    <!-- Full Width Map Section -->
    <div class="map-container">
        <!-- Google Map Iframe (Tongi, Dhaka) -->
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d58359.81881647414!2d90.35471465241777!3d23.896675001140082!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c4488706e2d9%3A0xee450040bc1c72cb!2sTongi!5e0!3m2!1sen!2sbd!4v1715000000000!5m2!1sen!2sbd" 
            width="100%" 
            height="450" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

    <!-- Overlapping Content Section -->
    <div class="container contact-content-overlap">
        <div class="row g-0 shadow-lg rounded-4 overflow-hidden bg-white">
            
            <!-- Left Side: Contact Info (Primary Gradient Background) -->
            <div class="col-lg-5 contact-info-bg p-5">
                <h3 class="mb-4 text-white">Get In Touch</h3>
                <p class="mb-5 text-white-50">Have questions about finding a tutor or joining as a teacher? Send us a message and we'll respond ASAP.</p>
                
                <div class="info-item d-flex align-items-center mb-4">
                    <div class="icon-box-premium">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="info-text ms-3">
                        <h5 class="text-white mb-1">Our Location</h5>
                        <p class="mb-0 text-white-50">Tongi, Dhaka Division, Bangladesh</p>
                    </div>
                </div>

                <div class="info-item d-flex align-items-center mb-4">
                    <div class="icon-box-premium">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <div class="info-text ms-3">
                        <h5 class="text-white mb-1">Call Us</h5>
                        <p class="mb-0 text-white-50">+880 1234 567 890</p>
                    </div>
                </div>

                <div class="info-item d-flex align-items-center">
                    <div class="icon-box-premium">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="info-text ms-3">
                        <h5 class="text-white mb-1">Email Us</h5>
                        <p class="mb-0 text-white-50">support@sltcp.com</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Floating Label Contact Form -->
            <div class="col-lg-7 p-5">
                <h3 class="mb-4 text-dark font-weight-bold">Send a Message</h3>
                <form action="backend/contact-process.php" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating custom-floating">
                                <input type="text" name="name" class="form-control" id="floatingName" placeholder="John Doe" required>
                                <label for="floatingName">Your Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating custom-floating">
                                <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="name@example.com" required>
                                <label for="floatingEmail">Email Address</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating custom-floating">
                                <input type="text" name="subject" class="form-control" id="floatingSubject" placeholder="Subject" required>
                                <label for="floatingSubject">Subject</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating custom-floating">
                                <textarea name="message" class="form-control" id="floatingMessage" placeholder="Leave a message here" style="height: 150px" required></textarea>
                                <label for="floatingMessage">Your Message</label>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-premium w-100">Send Message <i class="fa-regular fa-paper-plane ms-2"></i></button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>