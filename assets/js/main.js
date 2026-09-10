/**
 * Rajkumar Bangal Portfolio - Main JavaScript
 * Handles: Theme Toggle, Typing Animation, Counter Scroll Reveal, Project Filters, Modal AJAX, Contact Form AJAX
 */

document.addEventListener('DOMContentLoaded', function () {
    // --------------------------------------------------------
    // 1. Dark / Light Theme Toggle
    // --------------------------------------------------------
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const themeIcon = document.getElementById('themeIcon');
    const currentTheme = localStorage.getItem('portfolio_theme') || 'dark';

    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            let activeTheme = document.documentElement.getAttribute('data-theme');
            let newTheme = activeTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('portfolio_theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeIcon) return;
        if (theme === 'light') {
            themeIcon.className = 'fas fa-moon';
            themeToggleBtn.setAttribute('title', 'Switch to Dark Mode');
        } else {
            themeIcon.className = 'fas fa-sun';
            themeToggleBtn.setAttribute('title', 'Switch to Light Mode');
        }
    }

    // --------------------------------------------------------
    // 2. Animated Typing Effect
    // --------------------------------------------------------
    const typingElement = document.getElementById('typingText');
    if (typingElement) {
        const titles = [
            "Junior PHP Developer",
            "CodeIgniter Developer",
            "Full Stack Web Developer",
            "MySQL & Database Specialist"
        ];
        let titleIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let typeSpeed = 100;

        function typeLoop() {
            const currentTitle = titles[titleIndex];
            
            if (isDeleting) {
                typingElement.textContent = currentTitle.substring(0, charIndex - 1);
                charIndex--;
                typeSpeed = 50;
            } else {
                typingElement.textContent = currentTitle.substring(0, charIndex + 1);
                charIndex++;
                typeSpeed = 100;
            }

            if (!isDeleting && charIndex === currentTitle.length) {
                typeSpeed = 2000;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                titleIndex = (titleIndex + 1) % titles.length;
                typeSpeed = 500;
            }

            setTimeout(typeLoop, typeSpeed);
        }
        typeLoop();
    }

    // --------------------------------------------------------
    // 3. Navbar Sticky Effect & Active State
    // --------------------------------------------------------
    const navbar = document.querySelector('.navbar-custom');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    });

    // --------------------------------------------------------
    // 4. Animated Counter Numbers
    // --------------------------------------------------------
    const counters = document.querySelectorAll('.counter-number');
    let animated = false;

    function runCounters() {
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target') || '0', 10);
            const count = parseInt(counter.innerText || '0', 10);
            const increment = Math.ceil(target / 40);

            if (count < target) {
                counter.innerText = Math.min(count + increment, target) + '+';
                setTimeout(runCounters, 30);
            } else {
                counter.innerText = target + '+';
            }
        });
    }

    const counterSection = document.getElementById('counters');
    if (counterSection) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !animated) {
                animated = true;
                counters.forEach(c => c.innerText = '0');
                runCounters();
            }
        }, { threshold: 0.5 });
        observer.observe(counterSection);
    }

    // --------------------------------------------------------
    // 5. Project Filtering
    // --------------------------------------------------------
    const filterBtns = document.querySelectorAll('.project-filter-btn');
    const projectItems = document.querySelectorAll('.project-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            projectItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                if (filterValue === 'all' || itemCategory === filterValue) {
                    item.style.display = 'block';
                    item.classList.add('animate__fadeIn');
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // --------------------------------------------------------
    // 6. Dynamic Project Modal Handler
    // --------------------------------------------------------
    const projectModal = document.getElementById('projectDetailModal');
    if (projectModal) {
        projectModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const projectId = button.getAttribute('data-project-id');

            const modalTitle = document.getElementById('modalProjectTitle');
            const modalBody = document.getElementById('modalProjectBody');

            if (!modalBody) return;

            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading Project Specifications...</p>
                </div>
            `;

            fetch(`api/project-details.php?id=${projectId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const proj = data.project;
                        modalTitle.textContent = proj.title;

                        let featuresList = '';
                        if (proj.features) {
                            const feats = typeof proj.features === 'string' ? JSON.parse(proj.features) : proj.features;
                            featuresList = feats.map(f => `<li class="modal-feature-item mb-1"><i class="fas fa-check-circle text-info me-2"></i>${f}</li>`).join('');
                        }

                        let techTags = proj.technologies.split(',').map(t => `<span class="tech-tag me-1 mb-1">${t.trim()}</span>`).join('');

                        modalBody.innerHTML = `
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <img src="${proj.thumbnail}" class="img-fluid rounded-3 mb-3 border border-secondary shadow-sm" alt="${proj.title}">
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        ${techTags}
                                    </div>
                                    <div class="d-flex gap-2">
                                        ${proj.live_url && proj.live_url !== '#' ? `<a href="${proj.live_url}" target="_blank" class="btn btn-primary-custom btn-sm"><i class="fas fa-external-link-alt"></i> Live Demo</a>` : ''}
                                        ${proj.github_url && proj.github_url !== '#' ? `<a href="${proj.github_url}" target="_blank" class="btn btn-outline-custom btn-sm"><i class="fab fa-github"></i> Source Code</a>` : ''}
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h5 class="text-info font-weight-bold mb-2">Project Overview</h5>
                                    <p class="text-secondary mb-3">${proj.full_description || proj.short_description}</p>
                                    ${proj.problem_solved ? `<h6 class="text-warning mt-3"><i class="fas fa-exclamation-triangle me-1"></i> Problem Solved</h6><p class="small text-secondary mb-3">${proj.problem_solved}</p>` : ''}
                                    ${proj.my_responsibilities ? `<h6 class="text-success mt-3"><i class="fas fa-tasks me-1"></i> Key Responsibilities</h6><p class="small text-secondary mb-3">${proj.my_responsibilities}</p>` : ''}
                                    <h6 class="text-info mt-3"><i class="fas fa-star me-1"></i> Core Features</h6>
                                    <ul class="list-unstyled small text-secondary">${featuresList}</ul>
                                </div>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `<div class="alert alert-danger">Failed to load project details. Please try again.</div>`;
                    }
                })
                .catch(err => {
                    modalBody.innerHTML = `<div class="alert alert-danger">Error fetching data. Check server connectivity.</div>`;
                });
        });
    }

    // --------------------------------------------------------
    // 7. Contact Form AJAX Submission
    // --------------------------------------------------------
    const contactForm = document.getElementById('contactForm');
    const formAlert = document.getElementById('contactFormAlert');

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> Sending Message...`;
            if (formAlert) formAlert.style.display = 'none';

            const formData = new FormData(contactForm);

            fetch('api/contact.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                if (formAlert) {
                    formAlert.style.display = 'block';
                    if (data.success) {
                        formAlert.className = 'alert alert-success mt-3 rounded-3';
                        formAlert.innerHTML = `<i class="fas fa-check-circle me-2"></i> ${data.message}`;
                        contactForm.reset();
                    } else {
                        formAlert.className = 'alert alert-danger mt-3 rounded-3';
                        formAlert.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i> ${data.message}`;
                    }
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                if (formAlert) {
                    formAlert.style.display = 'block';
                    formAlert.className = 'alert alert-danger mt-3 rounded-3';
                    formAlert.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i> Network error. Please try again later.`;
                }
            });
        });
    }
});
