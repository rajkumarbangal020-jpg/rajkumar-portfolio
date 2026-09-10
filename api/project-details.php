<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($project_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid project ID.']);
    exit;
}

$projects = get_portfolio_projects();
$matched_project = null;

foreach ($projects as $p) {
    if (intval($p['id']) === $project_id) {
        $matched_project = $p;
        $matched_project['thumbnail'] = get_project_image_url($matched_project['thumbnail']);
        break;
    }
}

if ($matched_project) {
    echo json_encode([
        'success' => true,
        'project' => $matched_project
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Project not found.'
    ]);
}
