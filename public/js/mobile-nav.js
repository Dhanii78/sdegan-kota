/**
 * S-DEGAN Mobile Navigation & Header Bar Script
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Create Mobile Topbar if not present
    let topbar = document.querySelector('.mobile-topbar');
    
    if (!topbar) {
        topbar = document.createElement('div');
        topbar.className = 'mobile-topbar';
        
        // Find logo image src if available
        let logoSrc = '/images/logo.png';
        const existingLogo = document.querySelector('.logo img');
        if (existingLogo && existingLogo.src) {
            logoSrc = existingLogo.src;
        }

        topbar.innerHTML = `
            <button type="button" class="mobile-hamburger-btn" aria-label="Toggle Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="mobile-topbar-brand">
                <img src="${logoSrc}" alt="S-DEGAN Logo" class="mobile-topbar-logo" onerror="this.style.display='none'">
                <span class="mobile-topbar-title">S-DEGAN</span>
            </div>
        `;
        document.body.insertBefore(topbar, document.body.firstChild);
    }

    // 2. Create Mobile Overlay if not present
    let overlay = document.querySelector('.mobile-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'mobile-overlay';
        document.body.appendChild(overlay);
    }

    const navbar = document.querySelector('.navbar');
    const hamburgerBtn = topbar.querySelector('.mobile-hamburger-btn');
    const existingToggleBtn = document.querySelector('.toggle-btn');

    function toggleMenu() {
        if (!navbar) return;
        const isOpen = navbar.classList.contains('visible') || navbar.classList.contains('show');
        const isMobile = window.innerWidth <= 768;
        
        if (isOpen) {
            navbar.classList.remove('visible', 'show');
            if (isMobile) {
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        } else {
            navbar.classList.add('visible', 'show');
            if (isMobile) {
                overlay.classList.add('active');
                document.body.classList.add('sidebar-open');
            }
        }
    }

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });
    }

    if (existingToggleBtn) {
        existingToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', toggleMenu);
    }

    // Close menu when clicking on any menu link inside drawer on mobile
    if (navbar) {
        const menuLinks = navbar.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navbar.classList.remove('visible', 'show');
                    overlay.classList.remove('active');
                    document.body.classList.remove('sidebar-open');
                }
            });
        });
    }
});

