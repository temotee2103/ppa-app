/**
 * Admin Panel JavaScript
 * Handles various interactive elements and functionality in the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
            const sidebar = document.querySelector('.admin-sidebar');
            sidebar.classList.toggle('toggled');
            
            // Save state to localStorage
            if (sidebar.classList.contains('toggled')) {
                localStorage.setItem('sidebar-toggled', 'true');
            } else {
                localStorage.setItem('sidebar-toggled', 'false');
            }
        });
        
        // Check for saved state
        if (localStorage.getItem('sidebar-toggled') === 'true') {
            document.body.classList.add('sidebar-toggled');
            document.querySelector('.admin-sidebar').classList.add('toggled');
        }
    }
    
    // Close sidebar on small screens when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
            const sidebar = document.querySelector('.admin-sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            
            if (sidebar && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                document.body.classList.remove('sidebar-toggled');
                sidebar.classList.remove('toggled');
                localStorage.setItem('sidebar-toggled', 'false');
            }
        }
    });
    
    // Prevent closing dropdown when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            if (event.target.tagName !== 'A' || event.target.getAttribute('data-bs-toggle') === 'dropdown') {
                event.stopPropagation();
            }
        });
    });
    
    // Password toggle visibility
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.querySelector(targetId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.querySelector('i').classList.remove('fa-eye');
                this.querySelector('i').classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.querySelector('i').classList.remove('fa-eye-slash');
                this.querySelector('i').classList.add('fa-eye');
            }
        });
    });
    
    // Initialize DataTables if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.admin-datatable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)"
            },
            pageLength: 10,
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>' +
                 '<"row"<"col-md-12"t>>' +
                 '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
        });
    }
    
    // Confirmation dialogs
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(event) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Tooltips initialization
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // File input display filename
    document.querySelectorAll('.custom-file-input').forEach(fileInput => {
        fileInput.addEventListener('change', function() {
            let fileName = this.files[0].name;
            let nextSibling = this.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });
    
    // Date Range Picker initialization
    if (typeof daterangepicker !== 'undefined') {
        $('.date-range-picker').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }
    
    // Chart.js initialization for dashboard charts
    if (typeof Chart !== 'undefined') {
        initCharts();
    }
});

/**
 * Initialize Chart.js charts on dashboard
 */
function initCharts() {
    // Sales Chart
    if (document.getElementById('salesChart')) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartsData.sales.labels || [],
                datasets: [{
                    label: 'Monthly Sales',
                    data: chartsData.sales.data || [],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4e73df',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return 'RM ' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'RM ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Revenue Sources Chart
    if (document.getElementById('revenueSourcesChart')) {
        const ctx = document.getElementById('revenueSourcesChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartsData.revenueSources.labels || [],
                datasets: [{
                    data: chartsData.revenueSources.data || [],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                    hoverBorderColor: 'rgba(234, 236, 244, 1)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Claims Status Chart
    if (document.getElementById('claimsStatusChart')) {
        const ctx = document.getElementById('claimsStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartsData.claims.labels || [],
                datasets: [{
                    label: 'Claims by Status',
                    data: chartsData.claims.data || [],
                    backgroundColor: [
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(54, 185, 204, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

/**
 * Handle CSV export
 */
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), filename);
}

/**
 * Helper to download CSV
 */
function downloadCSV(csv, filename) {
    let csvFile;
    let downloadLink;
    
    // CSV file
    csvFile = new Blob([csv], {type: 'text/csv'});
    
    // Download link
    downloadLink = document.createElement('a');
    
    // File name
    downloadLink.download = filename;
    
    // Create link to file
    downloadLink.href = window.URL.createObjectURL(csvFile);
    
    // Hide download link
    downloadLink.style.display = 'none';
    
    // Add link to DOM
    document.body.appendChild(downloadLink);
    
    // Click download link
    downloadLink.click();
} 