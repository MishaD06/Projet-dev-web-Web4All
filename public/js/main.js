document.addEventListener('DOMContentLoaded', function () {

    /* ========== BURGER MENU ========== */
    const burger = document.querySelector('.burger');
    const navMenu = document.querySelector('.nav-menu');

    if (burger && navMenu) {
        burger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            const icon = burger.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
    }

    /* Fermer le menu au clic sur un lien (mobile) */
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768 && navMenu) {
                navMenu.classList.remove('active');
                const icon = burger && burger.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            }
        });
    });

    /* Fermer le menu en cliquant en dehors */
    document.addEventListener('click', function (e) {
        if (navMenu && burger && !navMenu.contains(e.target) && !burger.contains(e.target)) {
            navMenu.classList.remove('active');
            const icon = burger.querySelector('i');
            if (icon) {
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-times');
            }
        }
    });

    /* ========== LIEN ACTIF ========== */
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && (href === currentPage || href.endsWith('/' + currentPage))) {
            link.classList.add('active');
        }
    });

});
