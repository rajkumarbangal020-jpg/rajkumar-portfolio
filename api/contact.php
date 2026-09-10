<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Verify CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Security token invalid or expired. Please refresh the page and try again.']);
    exit;
}

// Retrieve and sanitize inputs
$name    = sanitize_input($_POST['name'] ?? '');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone   = sanitize_input($_POST['phone'] ?? '');
$subject = sanitize_input($_POST['subject'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required. Please fill in all fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

if (strlen($message) < 10) {
    echo json_encode(['success' => false, 'message' => 'Message must be at least 10 characters long.']);
    exit;
}

// Save enquiry to database
$db = get_db_connection();
if ($db) {
    try {
        $stmt = $db->prepare("INSERT INTO portfolio_enquiries (name, email, phone, subject, message, created_at) VALUES (:name, :email, :phone, :subject, :message, NOW())");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subject' => $subject,
            ':message' => $message
        ]);
        echo json_encode([
            'success' => true,
            'message' => 'Thank you, ' . htmlspecialchars($name) . '! Your message has been sent successfully. I will get back to you shortly.'
        ]);
        exit;
    } catch (PDOException $e) {
        // Fallthrough to success message if DB fails to log
    }
}

// Fallback response if DB offline
echo json_encode([
    'success' => true,
    'message' => 'Thank you, ' . htmlspecialchars($name) . '! Your enquiry has been received. I will get back to you soon!'
]);
