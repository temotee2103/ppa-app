            </div> <!-- End of main-content -->
        </div> <!-- End of user-content -->
    </div> <!-- End of user-wrapper -->
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-secondary">
                    Select "Logout" below if you are ready to end your current session.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <a href="../logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to top button -->
    <button id="backToTop" class="btn btn-primary btn-circle position-fixed" style="bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; display: none; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle for mobile
            const sidebarToggleTop = document.getElementById('sidebarToggleTop');
            const sidebar = document.querySelector('.user-sidebar');
            const content = document.querySelector('.user-content');
            
            if (sidebarToggleTop) {
                sidebarToggleTop.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    content.classList.toggle('sidebar-open');
                });
            }
            
            // Activate tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Back to top button
            const backToTopButton = document.getElementById('backToTop');
            
            if (backToTopButton) {
                backToTopButton.style.display = 'flex';
                backToTopButton.style.opacity = '0';
                backToTopButton.style.transform = 'scale(0.8)';
                backToTopButton.style.transition = 'all 0.3s ease';
                
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        backToTopButton.style.opacity = '1';
                        backToTopButton.style.transform = 'scale(1)';
                    } else {
                        backToTopButton.style.opacity = '0';
                        backToTopButton.style.transform = 'scale(0.8)';
                    }
                });
                
                backToTopButton.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            
            // Focus outline only for keyboard navigation
            document.addEventListener('mousedown', function() {
                document.body.classList.add('using-mouse');
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    document.body.classList.remove('using-mouse');
                }
            });
            
            // Add style to hide focus outlines when using mouse
            const style = document.createElement('style');
            style.textContent = `
                .using-mouse :focus {
                    outline: none !important;
                    box-shadow: none !important;
                }
            `;
            document.head.append(style);
        });
    </script>
</body>
</html> 