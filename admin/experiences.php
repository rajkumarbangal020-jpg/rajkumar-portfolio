<?php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = get_db_connection();
$message = '';
$message_type = '';

$action = $_GET['action'] ?? 'list';
$edit_id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = sanitize_input($_POST['company'] ?? '');
    $role = sanitize_input($_POST['role'] ?? '');
    $duration = sanitize_input($_POST['duration'] ?? '');
    $is_current = isset($_POST['is_current']) ? 1 : 0;
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $resp_text = $_POST['responsibilities'] ?? '';
    $resp_arr = array_filter(array_map('trim', explode("\n", $resp_text)));
    $resp_json = json_encode(array_values($resp_arr));
    if ($db) {
        try {
            if ($edit_id > 0) {
                $stmt = $db->prepare("UPDATE experiences SET company = :company, role = :role, duration = :duration, is_current = :is_current, responsibilities = :resp, sort_order = :sort WHERE id = :id");
                $stmt->execute([':company'=>$company, ':role'=>$role, ':duration'=>$duration, ':is_current'=>$is_current, ':resp'=>$resp_json, ':sort'=>$sort_order, ':id'=>$edit_id]);
                $message = "Experience updated successfully!";
                $message_type = "success";
            } else {
                $stmt = $db->prepare("INSERT INTO experiences (company, role, duration, is_current, responsibilities, sort_order) VALUES (:company, :role, :duration, :is_current, :resp, :sort)");
                $stmt->execute([':company'=>$company, ':role'=>$role, ':duration'=>$duration, ':is_current'=>$is_current, ':resp'=>$resp_json, ':sort'=>$sort_order]);
                $message = "Experience record added successfully!";
                $message_type = "success";
            }
            $action = 'list';
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

if ($action === 'delete' && $edit_id > 0 && $db) {
    try { $stmt=$db->prepare("DELETE FROM experiences WHERE id = :id"); $stmt->execute([':id'=>$edit_id]); $message="Experience deleted!"; $message_type="success"; } catch (Exception $e) {}
    $action='list';
}

$experiences = get_portfolio_experiences();
$edit_exp = null;
if ($action === 'edit' && $edit_id > 0) {
    foreach ($experiences as $ex) { if (intval($ex['id']) === $edit_id) { $edit_exp=$ex; break; } }
}
?>
<main class="admin-main">
<div class="admin-topbar"><div><h3 class="font-weight-bold mb-0">Work Experience Timeline</h3><small class="admin-text-muted">Manage Companies, Positions & Roles</small></div><div><?php if ($action === 'list'): ?><a href="experiences.php?action=add" class="btn btn-info text-dark font-weight-bold"><i class="fas fa-plus me-1"></i> Add Experience</a><?php else: ?><a href="experiences.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a><?php endif; ?></div></div>
<?php if (!empty($message)): ?><div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($message); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="admin-card"><h5 class="font-weight-bold mb-4"><?php echo $action === 'edit' ? 'Edit Work Experience' : 'Add New Work Experience'; ?></h5><form method="POST" action="experiences.php<?php echo $action === 'edit' ? '?action=edit&id='.$edit_id : ''; ?>">
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label small font-weight-bold">Company Name *</label><input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($edit_exp['company'] ?? ''); ?>" placeholder="WEBANQUETS.IN" required></div><div class="col-md-6"><label class="form-label small font-weight-bold">Job Role / Title *</label><input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($edit_exp['role'] ?? ''); ?>" placeholder="IT Intern / Web Developer" required></div></div>
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label small font-weight-bold">Duration *</label><input type="text" name="duration" class="form-control" value="<?php echo htmlspecialchars($edit_exp['duration'] ?? ''); ?>" placeholder="December 2025 – April 2026" required></div><div class="col-md-3"><label class="form-label small font-weight-bold">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($edit_exp['sort_order'] ?? '1'); ?>"></div><div class="col-md-3 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_current" id="is_current" <?php echo (!empty($edit_exp['is_current'])) ? 'checked' : ''; ?>><label class="form-check-label ms-2" for="is_current">Current Role</label></div></div></div>
<div class="mb-4"><label class="form-label small font-weight-bold">Responsibilities (One bullet per line) *</label><?php $resp_text=''; if (!empty($edit_exp['responsibilities'])) { $rarr=is_array($edit_exp['responsibilities'])?$edit_exp['responsibilities']:json_decode($edit_exp['responsibilities'],true); if (is_array($rarr)) $resp_text=implode("\n",$rarr); } ?><textarea name="responsibilities" rows="6" class="form-control" placeholder="Worked with PHP web applications&#10;Assisted with database tasks" required><?php echo htmlspecialchars($resp_text); ?></textarea></div>
<button type="submit" class="btn btn-info text-dark font-weight-bold px-4 py-2"><i class="fas fa-save me-1"></i> Save Experience</button></form></div>
<?php else: ?><div class="admin-card"><div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Company</th><th>Role</th><th>Duration</th><th>Actions</th></tr></thead><tbody><?php foreach ($experiences as $ex): ?><tr><td class="font-weight-bold admin-text-primary"><?php echo htmlspecialchars($ex['company']); ?></td><td><span class="text-info font-weight-bold"><?php echo htmlspecialchars($ex['role']); ?></span></td><td class="small admin-text-muted"><?php echo htmlspecialchars($ex['duration']); ?></td><td><a href="experiences.php?action=edit&id=<?php echo $ex['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-edit"></i></a><a href="experiences.php?action=delete&id=<?php echo $ex['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div><?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
