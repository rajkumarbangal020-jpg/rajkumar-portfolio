<?php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = get_db_connection();
$message = '';
$message_type = '';
$action = $_GET['action'] ?? 'list';
$edit_id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = sanitize_input($_POST['category'] ?? 'Frontend');
    $name = sanitize_input($_POST['name'] ?? '');
    $icon_class = sanitize_input($_POST['icon_class'] ?? 'fas fa-code');
    $proficiency_level = sanitize_input($_POST['proficiency_level'] ?? 'Proficient');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if ($db) {
        try {
            if ($edit_id > 0) {
                $stmt=$db->prepare("UPDATE skills SET category = :cat, name = :name, icon_class = :icon, proficiency_level = :prof, sort_order = :sort, is_active = :active WHERE id = :id");
                $stmt->execute([':cat'=>$category,':name'=>$name,':icon'=>$icon_class,':prof'=>$proficiency_level,':sort'=>$sort_order,':active'=>$is_active,':id'=>$edit_id]);
                $message="Skill updated!";$message_type="success";
            } else {
                $stmt=$db->prepare("INSERT INTO skills (category, name, icon_class, proficiency_level, sort_order, is_active) VALUES (:cat, :name, :icon, :prof, :sort, :active)");
                $stmt->execute([':cat'=>$category,':name'=>$name,':icon'=>$icon_class,':prof'=>$proficiency_level,':sort'=>$sort_order,':active'=>$is_active]);
                $message="New skill added!";$message_type="success";
            }
            $action='list';
        } catch (Exception $e) {$message="Database error: ".$e->getMessage();$message_type="danger";}
    }
}
if($action==='delete'&&$edit_id>0&&$db){try{$stmt=$db->prepare("DELETE FROM skills WHERE id = :id");$stmt->execute([':id'=>$edit_id]);$message="Skill deleted!";$message_type="success";}catch(Exception $e){}$action='list';}
$skills=get_portfolio_skills();$edit_skill=null;if($action==='edit'&&$edit_id>0){foreach($skills as $s){if(intval($s['id'])===$edit_id){$edit_skill=$s;break;}}}
?>
<main class="admin-main"><div class="admin-topbar"><div><h3 class="font-weight-bold mb-0">Technical Skills</h3><small class="admin-text-muted">Manage Tech Stack, Frameworks & Icons</small></div><div><?php if($action==='list'): ?><a href="skills.php?action=add" class="btn btn-info text-dark font-weight-bold"><i class="fas fa-plus me-1"></i> Add Skill</a><?php else: ?><a href="skills.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a><?php endif; ?></div></div>
<?php if(!empty($message)): ?><div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($message); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
<?php if($action==='add'||$action==='edit'): ?><div class="admin-card"><h5 class="font-weight-bold mb-4"><?php echo $action==='edit'?'Edit Technical Skill':'Add New Skill'; ?></h5><form method="POST" action="skills.php<?php echo $action==='edit'?'?action=edit&id='.$edit_id:''; ?>"><div class="row g-3 mb-3"><div class="col-md-4"><label class="form-label small font-weight-bold">Category *</label><select name="category" class="form-select"><?php $cats=['Frontend','Backend','Database','Tools','Learning'];$cur_cat=$edit_skill['category']??'Frontend';foreach($cats as $c): ?><option value="<?php echo $c; ?>" <?php echo $cur_cat===$c?'selected':''; ?>><?php echo $c; ?></option><?php endforeach; ?></select></div><div class="col-md-5"><label class="form-label small font-weight-bold">Skill Name *</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_skill['name']??''); ?>" placeholder="PHP 8" required></div><div class="col-md-3"><label class="form-label small font-weight-bold">Icon Class</label><input type="text" name="icon_class" class="form-control" value="<?php echo htmlspecialchars($edit_skill['icon_class']??'fab fa-php'); ?>" placeholder="fab fa-php"></div></div><div class="row g-3 mb-4"><div class="col-md-4"><label class="form-label small font-weight-bold">Proficiency Level</label><select name="proficiency_level" class="form-select"><?php $levels=['Advanced','Proficient','Intermediate','Learning'];$cur_lvl=$edit_skill['proficiency_level']??'Proficient';foreach($levels as $l): ?><option value="<?php echo $l; ?>" <?php echo $cur_lvl===$l?'selected':''; ?>><?php echo $l; ?></option><?php endforeach; ?></select></div><div class="col-md-4"><label class="form-label small font-weight-bold">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($edit_skill['sort_order']??'1'); ?>"></div><div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($edit_skill['is_active'])||$edit_skill['is_active']==1)?'checked':''; ?>><label class="form-check-label ms-2" for="is_active">Active</label></div></div></div><button type="submit" class="btn btn-info text-dark font-weight-bold px-4 py-2"><i class="fas fa-save me-1"></i> Save Skill</button></form></div><?php else: ?><div class="admin-card"><div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Icon</th><th>Skill Name</th><th>Category</th><th>Proficiency</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($skills as $s): ?><tr><td><i class="<?php echo htmlspecialchars($s['icon_class']); ?> fs-4 text-info"></i></td><td class="font-weight-bold admin-text-primary"><?php echo htmlspecialchars($s['name']); ?></td><td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($s['category']); ?></span></td><td class="small admin-text-muted"><?php echo htmlspecialchars($s['proficiency_level']); ?></td><td><?php if($s['is_active']??1): ?><span class="badge badge-active">Active</span><?php else: ?><span class="badge badge-inactive">Inactive</span><?php endif; ?></td><td><a href="skills.php?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-edit"></i></a><a href="skills.php?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div><?php endif; ?></main>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
