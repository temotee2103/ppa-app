            </div>
            <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    
    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Select "Logout" below if you are ready to end your current session.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to top button -->
    <button id="backToTop" class="btn btn-primary btn-circle position-fixed" style="bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; display: none; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Bootstrap Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <?php if (!empty($additional_js)): ?>
        <?php foreach($additional_js as $js_file): ?>
            <script src="assets/js/<?php echo $js_file; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom JS -->
    <script>
    $(document).ready(function() {
        // Initialize DataTables
        if ($('.admin-datatable').length) {
            $('.admin-datatable').DataTable({
                responsive: true
            });
        }
        
        // Sidebar toggle for mobile
        $('#sidebarToggleTop').on('click', function() {
            $('.admin-sidebar').toggleClass('show');
        });
        
        // Handle sidebar active states
        $('.nav-link').each(function() {
            if ($(this).hasClass('active')) {
                $(this).closest('.nav-item').addClass('active');
            }
        });
        
        // Confirmation dialogs
        $('[data-confirm]').on('click', function(e) {
            e.preventDefault();
            var message = $(this).data('confirm');
            if (confirm(message)) {
                window.location = $(this).attr('href');
            }
        });
        
        // Add animation classes to elements as they come into view
        function animateElements() {
            $('.fade-in:not(.animated)').each(function() {
                var position = $(this).offset().top;
                var scroll = $(window).scrollTop();
                var windowHeight = $(window).height();
                
                if (scroll + windowHeight - 100 > position) {
                    $(this).addClass('animated');
                }
            });
        }
        
        // Run animation on page load
        animateElements();
        
        // Run animation on scroll
        $(window).on('scroll', function() {
            animateElements();
        });
    });
    </script>
</body>
</html> 