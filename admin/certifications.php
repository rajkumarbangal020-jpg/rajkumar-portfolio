<?php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = get_db_connection();
$message = '';
$message_type = '';
$action = $_GET['action'] ?? 'list';
$edit_id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $issuing_organization = sanitize_input($_POST['issuing_organization'] ?? '');
    $issue_year = sanitize_input($_POST['issue_year'] ?? '');
    $credential_url = sanitize_input($_POST['credential_url'] ?? '#');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    if ($db) {
        try {
            if ($edit_id > 0) {
                $stmt = $db->prepare("UPDATE certifications SET title = :t, issuing_organization = :org, issue_year = :yr, credential_url = :url, sort_order = :sort WHERE id = :id");
                $stmt->execute([':t'=>$title, ':org'=>$issuing_organization, ':yr'=>$issue_year, ':url'=>$credential_url, ':sort'=>$sort_order, ':id'=>$edit_id]);
                $message="Certification updated!"; $message_type="success";
            } else {
                $stmt = $db->prepare("INSERT INTO certifications (title, issuing_organization, issue_year, credential_url, sort_order) VALUES (:t, :org, :yr, :url, :sort)");
                $stmt->execute([':t'=>$title, ':org'=>$issuing_organization, ':yr'=>$issue_year, ':url'=>$credential_url, ':sort'=>$sort_order]);
                $message="Certification added!"; $message_type="success";
            }
            $action='list';
        } catch (Exception $e) { $message="Database error: ".$e->getMessage(); $message_type="danger"; }
    }
}
if ($action === 'delete' && $edit_id > 0 && $db) {
    try { $stmt=$db->prepare("DELETE FROM certifications WHERE id = :id"); $stmt->execute([':id'=>$edit_id]); $message="Certification deleted!"; $message_type="success"; } catch (Exception $e) {}
    $action='list';
}
$certifications=get_portfolio_certifications();
$edit_cert=null;
if ($action==='edit' && $edit_id>0) { foreach($certifications as $c){ if(isset($c['id']) && intval($c['id'])===$edit_id){$edit_cert=$c;break;} } }
?>
<main class="admin-main">
<div class="admin-topbar"><div><h3 class="font-weight-bold mb-0">Certifications & Diplomas</h3><small class="admin-text-muted">Manage Certifications & Professional Training</small></div><div><?php if ($action==='list'): ?><a href="certifications.php?action=add" class="btn btn-info text-dark font-weight-bold"><i class="fas fa-plus me-1"></i> Add Certificate</a><?php else: ?><a href="certifications.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a><?php endif; ?></div></div>
<?php if(!empty($message)): ?><div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($message); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
<?php if($action==='add'||$action==='edit'): ?><div class="admin-card"><h5 class="font-weight-bold mb-4"><?php echo $action==='edit'?'Edit Certification':'Add New Certification'; ?></h5><form method="POST" action="certifications.php<?php echo $action==='edit'?'?action=edit&id='.$edit_id:''; ?>">
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label small font-weight-bold">Certification Title *</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($edit_cert['title']??''); ?>" placeholder="DOEACC / NIELIT O Level" required></div><div class="col-md-6"><label class="form-label small font-weight-bold">Issuing Institute *</label><input type="text" name="issuing_organization" class="form-control" value="<?php echo htmlspecialchars($edit_cert['issuing_organization']??''); ?>" placeholder="e.g. PHP Kolkata / WEBEL / NIELIT" required></div></div>
<div class="row g-3 mb-3"><div class="col-md-4"><label class="form-label small font-weight-bold">Issue Year *</label><input type="text" name="issue_year" class="form-control" value="<?php echo htmlspecialchars($edit_cert['issue_year']??''); ?>" placeholder="2024" required></div><div class="col-md-6"><label class="form-label small font-weight-bold">Credential / Verify Link</label><input type="text" name="credential_url" class="form-control" value="<?php echo htmlspecialchars($edit_cert['credential_url']??'#'); ?>" placeholder="https://..."></div><div class="col-md-2"><label class="form-label small font-weight-bold">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($edit_cert['sort_order']??'1'); ?>"></div></div>
<button type="submit" class="btn btn-info text-dark font-weight-bold px-4 py-2 mt-3"><i class="fas fa-save me-1"></i> Save Certificate</button></form></div>
<?php else: ?><div class="admin-card"><div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Certificate Name</th><th>Institute</th><th>Year</th><th>Verification</th><th>Actions</th></tr></thead><tbody><?php foreach($certifications as $c): ?><tr><td class="font-weight-bold admin-text-primary"><?php echo htmlspecialchars($c['title']); ?></td><td><span class="text-info font-weight-bold"><?php echo htmlspecialchars($c['issuing_organization']); ?></span></td><td class="small admin-text-muted"><?php echo htmlspecialchars($c['issue_year']); ?></td><td><a href="<?php echo htmlspecialchars($c['credential_url']); ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="fas fa-external-link-alt"></i> Verify</a></td><td><?php if(isset($c['id'])): ?><a href="certifications.php?action=edit&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-edit"></i></a><a href="certifications.php?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm"><i class="fas fa-trash"></i></a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div><?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
