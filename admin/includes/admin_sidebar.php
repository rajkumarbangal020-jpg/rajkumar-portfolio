<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <i class="fas fa-shield-alt text-info fs-3"></i>
        <div class="sidebar-title">
            <h6 class="mb-0 admin-text-primary font-weight-bold">Admin Portal</h6>
            <small class="admin-text-muted">Portfolio Manager</small>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i><span class="menu-text">Dashboard</span></a></li>
        <li><a href="projects.php" class="<?php echo $currentPage === 'projects.php' ? 'active' : ''; ?>"><i class="fas fa-project-diagram"></i><span class="menu-text">Projects</span></a></li>
        <li><a href="experiences.php" class="<?php echo $currentPage === 'experiences.php' ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i><span class="menu-text">Experience</span></a></li>
        <li><a href="skills.php" class="<?php echo $currentPage === 'skills.php' ? 'active' : ''; ?>"><i class="fas fa-code"></i><span class="menu-text">Skills</span></a></li>
        <li><a href="education.php" class="<?php echo $currentPage === 'education.php' ? 'active' : ''; ?>"><i class="fas fa-graduation-cap"></i><span class="menu-text">Education</span></a></li>
        <li><a href="certifications.php" class="<?php echo $currentPage === 'certifications.php' ? 'active' : ''; ?>"><i class="fas fa-certificate"></i><span class="menu-text">Certifications</span></a></li>
        <li><a href="enquiries.php" class="<?php echo $currentPage === 'enquiries.php' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i><span class="menu-text">Enquiries</span></a></li>
        <li><a href="settings.php" class="<?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i><span class="menu-text">Personal Info</span></a></li>
        <li class="mt-4"><a href="<?php echo BASE_URL; ?>" target="_blank" class="text-info"><i class="fas fa-external-link-alt"></i><span class="menu-text">View Website</span></a></li>
        <li><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></li>
    </ul>
</aside>
