<?php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = get_db_connection();
$message = '';
$message_type = '';

$action = $_GET['action'] ?? 'list';
$view_id = intval($_GET['view'] ?? 0);
$delete_id = intval($_GET['delete'] ?? 0);

if ($delete_id > 0 && $db) {
    try {
        $stmt = $db->prepare("DELETE FROM portfolio_enquiries WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);
        $message = "Enquiry deleted successfully!";
        $message_type = "success";
    } catch (Exception $e) {}
}
if ($view_id > 0 && $db) {
    try { $stmt=$db->prepare("UPDATE portfolio_enquiries SET is_read = 1 WHERE id = :id"); $stmt->execute([':id'=>$view_id]); } catch (Exception $e) {}
}
$enquiries=[];$active_enquiry=null;
if($db){try{$stmt=$db->query("SELECT * FROM portfolio_enquiries ORDER BY created_at DESC");$enquiries=$stmt->fetchAll();if($view_id>0){foreach($enquiries as $eq){if(intval($eq['id'])===$view_id){$active_enquiry=$eq;break;}}}}catch(Exception $e){}}
?>
<main class="admin-main"><div class="admin-topbar"><div><h3 class="font-weight-bold mb-0">Contact Enquiries</h3><small class="admin-text-muted">Messages Submitted from Portfolio Website</small></div></div>
<?php if(!empty($message)): ?><div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($message); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
<?php if($active_enquiry): ?><div class="admin-card mb-4"><div class="d-flex justify-content-between align-items-center mb-3"><h5 class="text-info font-weight-bold mb-0"><i class="fas fa-envelope-open me-2"></i> Enquiry Details #<?php echo $active_enquiry['id']; ?></h5><a href="enquiries.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a></div><div class="row g-3 mb-3"><div class="col-md-4"><small class="admin-text-muted d-block">Sender Name</small><strong class="admin-text-primary fs-5"><?php echo htmlspecialchars($active_enquiry['name']); ?></strong></div><div class="col-md-4"><small class="admin-text-muted d-block">Email Address</small><a href="mailto:<?php echo htmlspecialchars($active_enquiry['email']); ?>" class="text-info font-weight-bold"><?php echo htmlspecialchars($active_enquiry['email']); ?></a></div><div class="col-md-4"><small class="admin-text-muted d-block">Phone Number</small><span class="admin-text-primary font-weight-bold"><?php echo htmlspecialchars($active_enquiry['phone']); ?></span></div></div><div class="mb-3"><small class="admin-text-muted d-block">Subject</small><div class="text-warning font-weight-bold"><?php echo htmlspecialchars($active_enquiry['subject']); ?></div></div><div class="p-3 rounded border border-secondary mb-3" style="background: var(--admin-input-bg);"><small class="admin-text-muted d-block mb-1">Message Content:</small><p class="admin-text-primary mb-0 leading-relaxed"><?php echo nl2br(htmlspecialchars($active_enquiry['message'])); ?></p></div><div class="d-flex justify-content-between align-items-center pt-2"><small class="admin-text-muted">Received on: <?php echo date('F j, Y g:i A', strtotime($active_enquiry['created_at'])); ?></small><a href="mailto:<?php echo htmlspecialchars($active_enquiry['email']); ?>?subject=RE: <?php echo urlencode($active_enquiry['subject']); ?>" class="btn btn-info text-dark font-weight-bold"><i class="fas fa-reply me-1"></i> Reply via Email</a></div></div><?php endif; ?>
<div class="admin-card"><div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Date & Time</th><th>Sender Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php if(empty($enquiries)): ?><tr><td colspan="7" class="text-center admin-text-muted py-4">No contact messages received yet.</td></tr><?php else: foreach($enquiries as $eq): ?><tr class="<?php echo ($active_enquiry&&$active_enquiry['id']==$eq['id'])?'table-active':''; ?>"><td class="small admin-text-muted"><?php echo date('M d, Y H:i', strtotime($eq['created_at'])); ?></td><td class="font-weight-bold admin-text-primary"><?php echo htmlspecialchars($eq['name']); ?></td><td><?php echo htmlspecialchars($eq['email']); ?></td><td><?php echo htmlspecialchars($eq['phone']); ?></td><td class="small admin-text-secondary"><?php echo htmlspecialchars($eq['subject']); ?></td><td><?php if($eq['is_read']): ?><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Read</span><?php else: ?><span class="badge bg-danger">New</span><?php endif; ?></td><td><a href="enquiries.php?view=<?php echo $eq['id']; ?>" class="btn btn-sm btn-info text-dark me-1" title="View Message"><i class="fas fa-eye"></i></a><a href="enquiries.php?action=delete&id=<?php echo $eq['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" title="Delete"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; endif; ?></tbody></table></div></div></main>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
