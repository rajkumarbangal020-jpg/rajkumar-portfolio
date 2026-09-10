<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$projects = get_portfolio_projects();
$project = null;

foreach ($projects as $p) {
    if (intval($p['id']) === $project_id) {
        $project = $p;
        break;
    }
}

if (!$project) {
    header("Location: index.php#projects");
    exit;
}

$page_title = htmlspecialchars($project['title']) . " | Rajkumar Bangal Portfolio";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-5" style="padding-top: calc(var(--nav-height) + 40px) !important;">
    <div class="container py-4">
        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php#projects">Projects</a></li>
                <li class="breadcrumb-item active text-info" aria-current="page"><?php echo htmlspecialchars($project['title']); ?></li>
            </ol>
        </nav>

        <div class="glass-card mb-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <img src="<?php echo get_project_image_url($project['thumbnail']); ?>" class="img-fluid rounded-3 border border-secondary shadow-lg w-100" alt="<?php echo htmlspecialchars($project['title']); ?>" onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>uploads/projects/placeholder.png';">
                </div>
                <div class="col-lg-6">
                    <span class="badge bg-info text-dark font-weight-bold mb-2"><?php echo htmlspecialchars($project['category']); ?></span>
                    <h1 class="font-weight-bold text-main mb-3"><?php echo htmlspecialchars($project['title']); ?></h1>
                    <p class="text-muted leading-relaxed mb-4"><?php echo htmlspecialchars($project['short_description']); ?></p>

                    <div class="mb-4">
                        <h6 class="text-uppercase text-info font-weight-bold mb-2">Technologies Used</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $techs = explode(',', $project['technologies']);
                            foreach ($techs as $t):
                            ?>
                                <span class="tech-tag"><?php echo htmlspecialchars(trim($t)); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <?php if (!empty($project['live_url']) && $project['live_url'] !== '#'): ?>
                            <a href="<?php echo htmlspecialchars($project['live_url']); ?>" target="_blank" class="btn btn-primary-custom">
                                <i class="fas fa-external-link-alt"></i> Live Demo
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($project['github_url']) && $project['github_url'] !== '#'): ?>
                            <a href="<?php echo htmlspecialchars($project['github_url']); ?>" target="_blank" class="btn btn-outline-custom">
                                <i class="fab fa-github"></i> GitHub Source Code
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card mb-4">
                    <h3 class="text-info font-weight-bold mb-3"><i class="fas fa-file-alt me-2"></i> Comprehensive Overview</h3>
                    <div class="richtext-light leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($project['full_description'])); ?>
                    </div>
                </div>

                <?php if (!empty($project['problem_solved'])): ?>
                    <div class="glass-card mb-4">
                        <h4 class="text-warning font-weight-bold mb-3"><i class="fas fa-exclamation-triangle me-2"></i> Problem Solved</h4>
                        <div class="richtext-light leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($project['problem_solved'])); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($project['challenges_solutions'])): ?>
                    <div class="glass-card mb-4">
                        <h4 class="text-purple font-weight-bold mb-3"><i class="fas fa-lightbulb me-2"></i> Technical Challenges & Solution</h4>
                        <div class="richtext-light leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($project['challenges_solutions'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <?php if (!empty($project['my_responsibilities'])): ?>
                    <div class="glass-card mb-4">
                        <h4 class="text-success font-weight-bold mb-3"><i class="fas fa-tasks me-2"></i> My Responsibilities</h4>
                        <div class="richtext-light small leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($project['my_responsibilities'])); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="glass-card">
                    <h4 class="text-info font-weight-bold mb-3"><i class="fas fa-check-circle me-2"></i> Core Features</h4>
                    <ul class="list-unstyled small text-muted mb-0">
                        <?php 
                        $features = json_decode($project['features'], true);
                        if (is_array($features)):
                            foreach ($features as $f): 
                        ?>
                                <li class="mb-2"><i class="fas fa-angle-right text-info me-2"></i><?php echo htmlspecialchars($f); ?></li>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
