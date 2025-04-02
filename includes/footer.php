    </main>
    
    <!-- 现代化页脚 -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h4 class="footer-title">Malaysia's 1st Additional Car Protection</h4>
                    <p class="mb-4 text-white-50">Comprehensive protection for your vehicle beyond standard insurance. Trusted by thousands of drivers across Malaysia.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo url('index.php'); ?>" class="text-decoration-none">Home</a></li>
                        <li><a href="<?php echo url('pages/about.php'); ?>" class="text-decoration-none">About Us</a></li>
                        <li><a href="<?php echo url('pages/how-it-works.php'); ?>" class="text-decoration-none">How It Works</a></li>
                        <li><a href="<?php echo url('plans.php'); ?>" class="text-decoration-none">Plans & Pricing</a></li>
                        <li><a href="<?php echo url('pages/faq.php'); ?>" class="text-decoration-none">FAQ</a></li>
                        <li><a href="<?php echo url('pages/contact.php'); ?>" class="text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Resources</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo url('faq.php'); ?>"><?php echo __('nav_faq'); ?></a></li>
                        <li><a href="<?php echo url('about.php'); ?>"><?php echo __('nav_about'); ?></a></li>
                        <li><a href="<?php echo url('blog.php'); ?>"><?php echo __('nav_blog'); ?></a></li>
                        <li><a href="<?php echo url('contact.php'); ?>"><?php echo __('nav_contact'); ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="text-white mb-4">Contact Us</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Main Street, Kuala Lumpur, Malaysia</li>
                        <li><i class="fas fa-phone me-2"></i> +60 3-1234 5678</li>
                        <li><i class="fas fa-envelope me-2"></i> info@carprotection.my</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-md-0 text-white-50"><?php echo __('footer_copyright'); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="<?php echo url('terms.php'); ?>" class="text-white-50 text-decoration-none me-3"><?php echo __('footer_terms'); ?></a>
                        <a href="<?php echo url('privacy.php'); ?>" class="text-white-50 text-decoration-none"><?php echo __('footer_privacy'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (如果需要) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- 自定义JS -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    
    <?php if (isset($additional_js) && !empty($additional_js)): ?>
        <?php foreach ($additional_js as $js_file): ?>
        <script src="<?php echo asset('js/' . $js_file); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- User Avatar Style -->
    <style>
        .avatar-circle-sm {
            width: 30px;
            height: 30px;
            background-color: var(--primary);
            border-radius: 50%;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        .initials-sm {
            line-height: 1;
        }
    </style>
    
    <!-- Logout Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLinks = document.querySelectorAll('.logout-link');
            logoutLinks.forEach(link => {
                link.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    try {
                        const response = await fetch('<?php echo url("api/auth.php?action=logout"); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            window.location.href = '<?php echo url("index.php"); ?>';
                        }
                    } catch (error) {
                        console.error('Logout error:', error);
                    }
                });
            });
        });
    </script>
</body>
</html> 