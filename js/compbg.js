document.addEventListener("DOMContentLoaded", function () {
            
            // 1. MOBILE RESPONSIVE HAMBURGER & DROPDOWN ACCORDION ENGINE
            const menuToggle = document.getElementById('menuToggleTrigger');
            const navLinksList = document.querySelector('.nav-links-list');
            const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');

            if (menuToggle && navLinksList) {
                menuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.classList.toggle('is-open');
                    navLinksList.classList.toggle('is-active');
                });
            }

            // Close responsive menu drawer when clicking anywhere outside of boundaries
            document.addEventListener('click', function(event) {
                if (navLinksList && navLinksList.classList.contains('is-active')) {
                    const isClickInside = navLinksList.contains(event.target) || menuToggle.contains(event.target);
                    if (!isClickInside) {
                        menuToggle.classList.remove('is-open');
                        navLinksList.classList.remove('is-active');
                    }
                }
            });

            dropdownTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    if (window.innerWidth <= 1024) {
                        e.preventDefault();
                        e.stopPropagation();
                        const parent = this.parentElement;
                        const panel = this.nextElementSibling;
                        
                        parent.classList.toggle('chevron-active');
                        panel.classList.toggle('mobile-open');
                    }
                });
            });
            
            // 2. SCROLL REVEAL MECHANICS ENGINE USING INTERSECTION OBSERVER
            const revealElements = document.querySelectorAll('.reveal-element');
            const stickyLogo = document.querySelector('.sticky-nav-logo-wrap');
            
            if ('IntersectionObserver' in window) {
                const scrollObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('active');
                            if(entry.target.classList.contains('financial-metric-card')) {
                                triggerKineticCounter();
                            }
                        }
                    });
                }, { 
                    threshold: 0.1,
                    rootMargin: "0px 0px -50px 0px"
                });
                
                revealElements.forEach(el => scrollObserver.observe(el));
            } else {
                revealElements.forEach(el => el.classList.add('active'));
                triggerKineticCounter();
            }

            // Sticky header navigation logo scroll indicator control mechanism
            window.addEventListener('scroll', function() {
                if (window.scrollY > 90) {
                    if (stickyLogo) {
                        stickyLogo.style.opacity = '1';
                        stickyLogo.style.visibility = 'visible';
                    }
                } else {
                    if (stickyLogo) {
                        stickyLogo.style.opacity = '0';
                        stickyLogo.style.visibility = 'hidden';
                    }
                }
            });

            // 3. KINETIC COUNTER MECHANICAL FUNCTION
            function triggerKineticCounter() {
                const counterElement = document.querySelector('.metric-big-number');
                if (!counterElement || counterElement.classList.contains('counted')) return;
                
                counterElement.classList.add('counted');
                const targetValue = parseInt(counterElement.getAttribute('data-target'), 10);
                let initialCount = 0;
                const counterDuration = 2000; 
                const loopStepIncrement = targetValue / (counterDuration / 16);
                
                const frameExecution = () => {
                    initialCount += loopStepIncrement;
                    if (initialCount < targetValue) {
                        counterElement.innerText = Math.floor(initialCount);
                        requestAnimationFrame(frameExecution);
                    } else {
                        counterElement.innerText = targetValue;
                    }
                };
                requestAnimationFrame(frameExecution);
            }
        });