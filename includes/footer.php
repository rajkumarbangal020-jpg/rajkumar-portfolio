<?php
$info = get_personal_info();
?>
<footer class="footer-custom">
    <div class="container">
        <div class="row g-4 align-items-center mb-4">
            <div class="col-lg-5">
                <a class="navbar-brand fs-3 mb-2" href="#home">
                    <i class="fas fa-code text-gradient"></i>
                    <span>Rajkumar Bangal</span>
                </a>
                <p class="text-muted small mb-0">
                    Junior PHP Developer specializing in CodeIgniter, MySQL & Full Stack Web Application Development. Building responsive, secure, and business-focused digital solutions.
                </p>
            </div>
            
            <div class="col-lg-4">
                <h6 class="text-uppercase text-info font-weight-bold mb-3">Quick Navigation</h6>
                <div class="d-flex flex-wrap gap-3 small">
                    <a href="#about" class="text-muted text-decoration-none">About Me</a>
                    <a href="#skills" class="text-muted text-decoration-none">Skills</a>
                    <a href="#projects" class="text-muted text-decoration-none">Projects</a>
                    <a href="#experience" class="text-muted text-decoration-none">Experience</a>
                    <a href="#contact" class="text-muted text-decoration-none">Contact</a>
                    <a href="<?php echo BASE_URL; ?>admin/login.php" class="text-info text-decoration-none" target="_blank"><i class="fas fa-user-shield me-1"></i> Admin Portal</a>
                </div>
            </div>

            <div class="col-lg-3 text-lg-end">
                <h6 class="text-uppercase text-info font-weight-bold mb-3">Connect</h6>
                <div class="d-flex justify-content-lg-end gap-2">
                    <a href="<?php echo htmlspecialchars($info['linkedin_url']); ?>" target="_blank" class="social-icon-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="<?php echo htmlspecialchars($info['github_url']); ?>" target="_blank" class="social-icon-btn" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="mailto:<?php echo htmlspecialchars($info['email']); ?>" class="social-icon-btn" title="Email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>

        <hr class="border-secondary opacity-25 my-4">

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between text-muted small">
            <p class="mb-2 mb-md-0">
                &copy; <?php echo date('Y'); ?> <strong>Rajkumar Bangal</strong>. All Rights Reserved. Designed & Developed with PHP & Bootstrap.
            </p>
            <a href="#home" class="btn btn-sm btn-outline-custom rounded-circle p-2" title="Back to Top"><i class="fas fa-arrow-up"></i></a>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>
