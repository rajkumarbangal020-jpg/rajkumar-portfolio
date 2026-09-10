<?php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$projects = get_portfolio_projects();
$skills = get_portfolio_skills();
$experiences = get_portfolio_experiences();

$enquiries = [];
$unread_count = 0;
$db = get_db_connection();
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM portfolio_enquiries ORDER BY created_at DESC LIMIT 5");
        $enquiries = $stmt->fetchAll();
        $countStmt = $db->query("SELECT COUNT(*) FROM portfolio_enquiries WHERE is_read = 0");
        $unread_count = $countStmt->fetchColumn();
    } catch (Exception $e) {}
}
?>
<main class="admin-main">
    <div class="admin-topbar">
        <div><h3 class="font-weight-bold mb-0">Dashboard Overview</h3><small class="admin-text-muted">Welcome back, <?php echo htmlspecialchars($current_admin); ?>!</small></div>
        <div><?php if (is_db_connected()): ?><span class="badge bg-success"><i class="fas fa-database me-1"></i> MySQL Database Connected</span><?php else: ?><span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Running in Fallback Mode</span><?php endif; ?></div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="admin-card text-center"><i class="fas fa-project-diagram text-info display-5 mb-2"></i><h2 class="font-weight-bold mb-0 admin-text-primary"><?php echo count($projects); ?></h2><span class="admin-text-muted small">Total Projects</span></div></div>
        <div class="col-md-3"><div class="admin-card text-center"><i class="fas fa-code text-success display-5 mb-2"></i><h2 class="font-weight-bold mb-0 admin-text-primary"><?php echo count($skills); ?></h2><span class="admin-text-muted small">Technical Skills</span></div></div>
        <div class="col-md-3"><div class="admin-card text-center"><i class="fas fa-briefcase text-warning display-5 mb-2"></i><h2 class="font-weight-bold mb-0 admin-text-primary"><?php echo count($experiences); ?></h2><span class="admin-text-muted small">Work Experiences</span></div></div>
        <div class="col-md-3"><div class="admin-card text-center"><i class="fas fa-envelope text-danger display-5 mb-2"></i><h2 class="font-weight-bold mb-0 admin-text-primary"><?php echo $unread_count; ?></h2><span class="admin-text-muted small">Unread Enquiries</span></div></div>
    </div>
    <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-3"><h5 class="font-weight-bold mb-0 admin-text-primary"><i class="fas fa-inbox text-info me-2"></i> Recent Enquiries</h5><a href="enquiries.php" class="btn btn-sm btn-outline-info">View All Enquiries</a></div>
        <div class="table-responsive"><table class="table table-dark-custom align-middle mb-0"><thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Status</th><th>Action</th></tr></thead><tbody>
        <?php if (empty($enquiries)): ?><tr><td colspan="7" class="text-center admin-text-muted py-4">No contact enquiries received yet.</td></tr>
        <?php else: foreach ($enquiries as $enq): ?><tr><td class="small admin-text-muted"><?php echo date('M d, Y', strtotime($enq['created_at'])); ?></td><td class="font-weight-bold admin-text-primary"><?php echo htmlspecialchars($enq['name']); ?></td><td><?php echo htmlspecialchars($enq['email']); ?></td><td><?php echo htmlspecialchars($enq['phone']); ?></td><td><?php echo htmlspecialchars($enq['subject']); ?></td><td><?php if ($enq['is_read']): ?><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Read</span><?php else: ?><span class="badge bg-danger">New</span><?php endif; ?></td><td><a href="enquiries.php?view=<?php echo $enq['id']; ?>" class="btn btn-sm btn-info text-dark" title="Read Message"><i class="fas fa-eye"></i></a></td></tr><?php endforeach; endif; ?>
        </tbody></table></div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
