<?php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = get_db_connection();
$message = '';
$message_type = '';

$info = get_personal_info();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name           = sanitize_input($_POST['full_name'] ?? '');
    $title               = sanitize_input($_POST['title'] ?? '');
    $tagline             = sanitize_input($_POST['tagline'] ?? '');
    $bio                 = $_POST['bio'] ?? '';
    $location            = sanitize_input($_POST['location'] ?? '');
    $role                = sanitize_input($_POST['role'] ?? '');
    $availability        = sanitize_input($_POST['availability'] ?? '');
    $preferred_locations = sanitize_input($_POST['preferred_locations'] ?? '');
    $email               = sanitize_input($_POST['email'] ?? '');
    $phone               = sanitize_input($_POST['phone'] ?? '');
    $linkedin_url        = sanitize_input($_POST['linkedin_url'] ?? '');
    $github_url          = sanitize_input($_POST['github_url'] ?? '');

    $resume_path = $_POST['existing_resume'] ?? 'assets/cv/rajkumar-bangal-cv.pdf';
    if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['resume_file']['tmp_name'];
        $fileName = $_FILES['resume_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension === 'pdf') {
            $newFileName = 'rajkumar-bangal-cv.pdf';
            $uploadFileDir = __DIR__ . '/../assets/cv/';
            @mkdir($uploadFileDir, 0777, true);
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) $resume_path = 'assets/cv/' . $newFileName;
        }
    }

    if ($db) {
        try {
            $stmt = $db->prepare("UPDATE personal_information SET full_name = :fn, title = :title, tagline = :tag, bio = :bio, location = :loc, role = :role, availability = :avail, preferred_locations = :pref, email = :email, phone = :phone, linkedin_url = :link, github_url = :git, resume_path = :res WHERE id = 1");
            $stmt->execute([':fn'=>$full_name, ':title'=>$title, ':tag'=>$tagline, ':bio'=>$bio, ':loc'=>$location, ':role'=>$role, ':avail'=>$availability, ':pref'=>$preferred_locations, ':email'=>$email, ':phone'=>$phone, ':link'=>$linkedin_url, ':git'=>$github_url, ':res'=>$resume_path]);
            $message = "Personal information updated successfully!";
            $message_type = "success";
            $info = get_personal_info();
        } catch (Exception $e) {
            $message = "Error updating settings: " . $e->getMessage();
            $message_type = "danger";
        }
    } else {
        $message = "Database is offline. Unable to update settings in MySQL.";
        $message_type = "warning";
    }
}
?>
<main class="admin-main">
<div class="admin-topbar"><div><h3 class="font-weight-bold mb-0">Personal Profile & Contact Settings</h3><small class="admin-text-muted">Manage Name, Titles, Bio, Location, Resume & Social Links</small></div></div>
<?php if (!empty($message)): ?><div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($message); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
<div class="admin-card"><form method="POST" action="settings.php" enctype="multipart/form-data"><input type="hidden" name="existing_resume" value="<?php echo htmlspecialchars($info['resume_path']); ?>">
<h5 class="text-info font-weight-bold mb-3"><i class="fas fa-user-circle me-2"></i> Basic Developer Profile</h5>
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label small font-weight-bold">Full Name *</label><input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($info['full_name']); ?>" required></div><div class="col-md-6"><label class="form-label small font-weight-bold">Professional Title *</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($info['title']); ?>" required></div></div>
<div class="mb-3"><label class="form-label small font-weight-bold">Hero Tagline *</label><input type="text" name="tagline" class="form-control" value="<?php echo htmlspecialchars($info['tagline']); ?>" required></div>
<div class="mb-4"><label class="form-label small font-weight-bold">About Me Bio (HTML Allowed) *</label><textarea name="bio" rows="6" class="form-control" required><?php echo htmlspecialchars($info['bio']); ?></textarea></div>
<h5 class="text-info font-weight-bold mb-3 pt-3 border-top border-secondary"><i class="fas fa-map-marker-alt me-2"></i> Location & Availability</h5>
<div class="row g-3 mb-3"><div class="col-md-4"><label class="form-label small font-weight-bold">Current Location *</label><input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($info['location']); ?>" required></div><div class="col-md-4"><label class="form-label small font-weight-bold">Primary Role *</label><input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($info['role']); ?>" required></div><div class="col-md-4"><label class="form-label small font-weight-bold">Availability Status *</label><input type="text" name="availability" class="form-control" value="<?php echo htmlspecialchars($info['availability']); ?>" required></div></div>
<div class="mb-4"><label class="form-label small font-weight-bold">Preferred Work Locations *</label><input type="text" name="preferred_locations" class="form-control" value="<?php echo htmlspecialchars($info['preferred_locations']); ?>" required></div>
<h5 class="text-info font-weight-bold mb-3 pt-3 border-top border-secondary"><i class="fas fa-link me-2"></i> Contact & Social Links</h5>
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label small font-weight-bold">Email Address *</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($info['email']); ?>" required></div><div class="col-md-6"><label class="form-label small font-weight-bold">Phone / WhatsApp Number *</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($info['phone']); ?>" required></div></div>
<div class="row g-3 mb-4"><div class="col-md-6"><label class="form-label small font-weight-bold">LinkedIn Profile URL</label><input type="text" name="linkedin_url" class="form-control" value="<?php echo htmlspecialchars($info['linkedin_url']); ?>"></div><div class="col-md-6"><label class="form-label small font-weight-bold">GitHub Profile URL</label><input type="text" name="github_url" class="form-control" value="<?php echo htmlspecialchars($info['github_url']); ?>"></div></div>
<h5 class="text-info font-weight-bold mb-3 pt-3 border-top border-secondary"><i class="fas fa-file-pdf me-2"></i> Resume / CV Upload</h5>
<div class="row g-3 mb-4"><div class="col-md-6"><label class="form-label small font-weight-bold">Upload New Resume (PDF only)</label><input type="file" name="resume_file" class="form-control" accept=".pdf"></div><div class="col-md-6 d-flex align-items-end"><a href="<?php echo BASE_URL . htmlspecialchars($info['resume_path']); ?>" target="_blank" class="btn btn-outline-info"><i class="fas fa-download me-1"></i> Current CV: <?php echo basename($info['resume_path']); ?></a></div></div>
<button type="submit" class="btn btn-info text-dark font-weight-bold px-4 py-2"><i class="fas fa-save me-1"></i> Update Personal Settings</button></form></div></main>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
