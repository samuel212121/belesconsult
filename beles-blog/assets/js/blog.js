document.addEventListener("DOMContentLoaded", function () {
    // Reveal-on-scroll animation
    const reveals = document.querySelectorAll('.reveal-element');
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('active'); });
        }, { threshold: 0.07, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(el => obs.observe(el));
    } else {
        reveals.forEach(el => el.classList.add('active'));
    }

    // Mobile nav burger (basic toggle, expand as needed for your full nav)
    const burger = document.querySelector('.nav-burger');
    const navLinks = document.querySelector('.slim-nav-links');
    if (burger && navLinks) {
        burger.addEventListener('click', () => {
            navLinks.classList.toggle('mobile-open');
        });
    }
});
