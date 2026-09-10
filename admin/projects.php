<?php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = get_db_connection();
$message = '';
$message_type = '';

$action = $_GET['action'] ?? 'list';
$edit_id = intval($_GET['id'] ?? 0);

// Handle Form Submissions (Create / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title                = sanitize_input($_POST['title'] ?? '');
    $short_description    = sanitize_input($_POST['short_description'] ?? '');
    $full_description     = sanitize_input($_POST['full_description'] ?? '');
    $problem_solved        = sanitize_input($_POST['problem_solved'] ?? '');
    $challenges_solutions = sanitize_input($_POST['challenges_solutions'] ?? '');
    $my_responsibilities  = sanitize_input($_POST['my_responsibilities'] ?? '');
    $technologies         = sanitize_input($_POST['technologies'] ?? '');
    $category             = sanitize_input($_POST['category'] ?? 'PHP & CodeIgniter');
    $live_url             = sanitize_input($_POST['live_url'] ?? '#');
    $github_url           = sanitize_input($_POST['github_url'] ?? '#');
    $sort_order           = intval($_POST['sort_order'] ?? 0);
    $is_active            = isset($_POST['is_active']) ? 1 : 0;
    $slug                 = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $features_input = $_POST['features'] ?? '';
    $features_arr = array_filter(array_map('trim', explode("\n", $features_input)));
    $features_json = json_encode(array_values($features_arr));

    $thumbnail_path = $_POST['existing_thumbnail'] ?? 'uploads/projects/school_management.png';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['thumbnail']['tmp_name'];
        $fileName = $_FILES['thumbnail']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'project_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
            $uploadDir1 = __DIR__ . '/../uploads/projects/';
            $uploadDir2 = __DIR__ . '/../assets/projects/';
            @mkdir($uploadDir1, 0777, true);
            @mkdir($uploadDir2, 0777, true);
            $dest1 = $uploadDir1 . $newFileName;
            $dest2 = $uploadDir2 . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest1)) {
                @copy($dest1, $dest2);
                $thumbnail_path = 'uploads/projects/' . $newFileName;
            }
        }
    }

    if ($db) {
        try {
            if ($edit_id > 0) {
                $stmt = $db->prepare("UPDATE projects SET title = :title, slug = :slug, short_description = :short_desc, full_description = :full_desc, problem_solved = :prob, challenges_solutions = :chal, my_responsibilities = :resp, features = :feat, technologies = :tech, thumbnail = :thumb, live_url = :live, github_url = :git, category = :cat, sort_order = :sort, is_active = :active WHERE id = :id");
                $stmt->execute([':title'=>$title, ':slug'=>$slug, ':short_desc'=>$short_description, ':full_desc'=>$full_description, ':prob'=>$problem_solved, ':chal'=>$challenges_solutions, ':resp'=>$my_responsibilities, ':feat'=>$features_json, ':tech'=>$technologies, ':thumb'=>$thumbnail_path, ':live'=>$live_url, ':git'=>$github_url, ':cat'=>$category, ':sort'=>$sort_order, ':active'=>$is_active, ':id'=>$edit_id]);
                $message = "Project updated successfully!";
                $message_type = "success";
            } else {
                $stmt = $db->prepare("INSERT INTO projects (title, slug, short_description, full_description, problem_solved, challenges_solutions, my_responsibilities, features, technologies, thumbnail, live_url, github_url, category, sort_order, is_active) VALUES (:title, :slug, :short_desc, :full_desc, :prob, :chal, :resp, :feat, :tech, :thumb, :live, :git, :cat, :sort, :active)");
                $stmt->execute([':title'=>$title, ':slug'=>$slug, ':short_desc'=>$short_description, ':full_desc'=>$full_description, ':prob'=>$problem_solved, ':chal'=>$challenges_solutions, ':resp'=>$my_responsibilities, ':feat'=>$features_json, ':tech'=>$technologies, ':thumb'=>$thumbnail_path, ':live'=>$live_url, ':git'=>$github_url, ':cat'=>$category, ':sort'=>$sort_order, ':active'=>$is_active]);
                $message = "New Project added successfully!";
                $message_type = "success";
            }
            $action = 'list';
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = "danger";
        }
    } else {
        $message = "Database is offline. Unable to persist changes to MySQL.";
        $message_type = "warning";
    }
}

if ($action === 'delete' && $edit_id > 0) {
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM projects WHERE id = :id");
            $stmt->execute([':id' => $edit_id]);
            $message = "Project deleted successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error deleting record: " . $e->getMessage();
            $message_type = "danger";
        }
    }
    $action = 'list';
}

$projects = get_portfolio_projects();
$edit_project = null;
if ($action === 'edit' && $edit_id > 0) {
    foreach ($projects as $p) {
        if (intval($p['id']) === $edit_id) {
            $edit_project = $p;
            break;
        }
    }
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <div>
            <h3 class="font-weight-bold mb-0">Manage Projects</h3>
            <small class="admin-text-muted">Add, Edit, Delete and Reorder Portfolio Projects</small>
        </div>
        <div>
            <?php if ($action === 'list'): ?>
                <a href="projects.php?action=add" class="btn btn-info text-dark font-weight-bold"><i class="fas fa-plus me-1"></i> Add New Project</a>
            <?php else: ?>
                <a href="projects.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="admin-card">
            <h5 class="font-weight-bold mb-4"><?php echo $action === 'edit' ? 'Edit Project Details' : 'Add New Portfolio Project'; ?></h5>
            <form method="POST" action="projects.php<?php echo $action === 'edit' ? '?action=edit&id='.$edit_id : ''; ?>" enctype="multipart/form-data">
                <input type="hidden" name="existing_thumbnail" value="<?php echo htmlspecialchars($edit_project['thumbnail'] ?? 'uploads/projects/school_management.png'); ?>">
                <div class="row g-3 mb-3">
                    <div class="col-md-8"><label class="form-label small font-weight-bold">Project Title *</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($edit_project['title'] ?? ''); ?>" required></div>
                    <div class="col-md-4"><label class="form-label small font-weight-bold">Category *</label><select name="category" class="form-select"><?php $cats=['PHP & CodeIgniter','Web Apps & PWA','Management Systems','Full Stack & APIs']; $current_cat=$edit_project['category']??'PHP & CodeIgniter'; foreach($cats as $c): ?><option value="<?php echo $c; ?>" <?php echo $current_cat===$c?'selected':''; ?>><?php echo $c; ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="mb-3"><label class="form-label small font-weight-bold">Short Description *</label><input type="text" name="short_description" class="form-control" value="<?php echo htmlspecialchars($edit_project['short_description'] ?? ''); ?>" required></div>
                <div class="mb-3"><label class="form-label small font-weight-bold">Full Detailed Overview *</label><textarea name="full_description" rows="4" class="form-control" required><?php echo htmlspecialchars($edit_project['full_description'] ?? ''); ?></textarea></div>
                <div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label small font-weight-bold">Problem Solved</label><textarea name="problem_solved" rows="3" class="form-control"><?php echo htmlspecialchars($edit_project['problem_solved'] ?? ''); ?></textarea></div><div class="col-md-6"><label class="form-label small font-weight-bold">Technical Challenges & Solutions</label><textarea name="challenges_solutions" rows="3" class="form-control"><?php echo htmlspecialchars($edit_project['challenges_solutions'] ?? ''); ?></textarea></div></div>
                <div class="mb-3"><label class="form-label small font-weight-bold">My Key Responsibilities</label><textarea name="my_responsibilities" rows="2" class="form-control"><?php echo htmlspecialchars($edit_project['my_responsibilities'] ?? ''); ?></textarea></div>
                <div class="mb-3"><label class="form-label small font-weight-bold">Core Features (One per line) *</label><?php $feats_text=''; if(!empty($edit_project['features'])){$farr=is_array($edit_project['features'])?$edit_project['features']:json_decode($edit_project['features'],true); if(is_array($farr))$feats_text=implode("\n",$farr);} ?><textarea name="features" rows="5" class="form-control" required><?php echo htmlspecialchars($feats_text); ?></textarea></div>
                <div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label small font-weight-bold">Technologies Used *</label><input type="text" name="technologies" class="form-control" value="<?php echo htmlspecialchars($edit_project['technologies'] ?? ''); ?>" required></div><div class="col-md-6"><label class="form-label small font-weight-bold">Project Thumbnail Image</label><input type="file" name="thumbnail" class="form-control"></div></div>
                <div class="row g-3 mb-4"><div class="col-md-4"><label class="form-label small font-weight-bold">Live Demo URL</label><input type="text" name="live_url" class="form-control" value="<?php echo htmlspecialchars($edit_project['live_url'] ?? '#'); ?>"></div><div class="col-md-4"><label class="form-label small font-weight-bold">GitHub Repository URL</label><input type="text" name="github_url" class="form-control" value="<?php echo htmlspecialchars($edit_project['github_url'] ?? '#'); ?>"></div><div class="col-md-2"><label class="form-label small font-weight-bold">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($edit_project['sort_order'] ?? '1'); ?>"></div><div class="col-md-2 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($edit_project['is_active']) || $edit_project['is_active']==1)?'checked':''; ?>><label class="form-check-label ms-2" for="is_active">Active</label></div></div></div>
                <button type="submit" class="btn btn-info text-dark font-weight-bold px-4 py-2"><i class="fas fa-save me-1"></i> Save Project</button>
            </form>
        </div>
    <?php else: ?>
        <div class="admin-card"><div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Thumbnail</th><th>Title</th><th>Category</th><th>Technologies</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($projects as $p): ?><tr><td><img src="<?php echo get_project_image_url($p['thumbnail']); ?>" width="60" height="40" class="rounded object-fit-cover" alt="Thumb"></td><td class="font-weight-bold admin-text-primary"><?php echo htmlspecialchars($p['title']); ?></td><td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($p['category']); ?></span></td><td class="small project-technologies"><?php echo htmlspecialchars($p['technologies']); ?></td><td><?php if($p['is_active']??1): ?><span class="badge badge-active">Active</span><?php else: ?><span class="badge badge-inactive">Inactive</span><?php endif; ?></td><td><a href="projects.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-edit"></i></a><a href="projects.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
