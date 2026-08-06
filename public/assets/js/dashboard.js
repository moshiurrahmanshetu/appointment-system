// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard JS loaded');
    
    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    
    console.log('Sidebar element:', sidebar);
    console.log('Sidebar toggle:', sidebarToggle);
    console.log('Sidebar backdrop:', sidebarBackdrop);
    
    // Load sidebar state from localStorage
    const sidebarState = localStorage.getItem('sidebarState');
    console.log('Initial sidebar state:', sidebarState);
    
    if (sidebarState === 'collapsed' && window.innerWidth > 991) {
        sidebar.classList.add('collapsed');
        console.log('Sidebar collapsed on load');
    }
    
    // Toggle sidebar (desktop)
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Sidebar toggle clicked');
            console.log('Window width:', window.innerWidth);
            
            if (window.innerWidth > 991) {
                // Desktop: collapse/expand
                sidebar.classList.toggle('collapsed');
                console.log('Sidebar collapsed:', sidebar.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
                
                // Destroy and reinitialize tooltips for collapsed state
                destroyTooltips();
                if (sidebar.classList.contains('collapsed')) {
                    initTooltips();
                }
            } else {
                // Mobile: show sidebar as offcanvas
                sidebar.classList.toggle('mobile-open');
                sidebarBackdrop.classList.toggle('show');
            }
        });
    } else {
        console.error('Sidebar toggle button not found');
    }
    
    // Close sidebar on backdrop click (mobile)
    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            sidebarBackdrop.classList.remove('show');
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            // Desktop: remove mobile classes
            sidebar.classList.remove('mobile-open');
            sidebarBackdrop.classList.remove('show');
            
            // Restore collapsed state if it was saved
            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'collapsed') {
                sidebar.classList.add('collapsed');
                initTooltips();
            } else {
                sidebar.classList.remove('collapsed');
                destroyTooltips();
            }
        } else {
            // Mobile: remove collapsed state
            sidebar.classList.remove('collapsed');
            destroyTooltips();
        }
    });
    
    // Initialize tooltips for collapsed sidebar
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('.sidebar.collapsed .nav-link[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Destroy tooltips
    function destroyTooltips() {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(function(tooltip) {
            const tooltipInstance = bootstrap.Tooltip.getInstance(tooltip);
            if (tooltipInstance) {
                tooltipInstance.dispose();
            }
        });
    }
    
    // Initialize tooltips on page load if sidebar is collapsed
    if (sidebar && sidebar.classList.contains('collapsed')) {
        initTooltips();
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.classList.add('fade');
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Initialize dropdowns
    const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    const dropdownList = dropdownTriggerList.map(function (dropdownTriggerEl) {
        return new bootstrap.Dropdown(dropdownTriggerEl);
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.classList.contains('no-loading')) {
                const btnText = submitButton.querySelector('.btn-text');
                const btnLoading = submitButton.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.classList.add('d-none');
                    btnLoading.classList.remove('d-none');
                    submitButton.disabled = true;
                } else {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                }
            }
        });
    });
    
    // Active menu highlighting
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link:not(.disabled)');
    navLinks.forEach(function(link) {
        const href = link.getAttribute('href');
        if (href && currentPath === href) {
            link.classList.add('active');
        }
    });
    
    // Card hover effects
    const cards = document.querySelectorAll('.card');
    cards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
        });
    });
});

// Utility functions
function showLoading() {
    const loader = document.createElement('div');
    loader.className = 'loading-overlay';
    loader.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    document.body.appendChild(loader);
}

function hideLoading() {
    const loader = document.querySelector('.loading-overlay');
    if (loader) {
        loader.remove();
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDateTime(date) {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}