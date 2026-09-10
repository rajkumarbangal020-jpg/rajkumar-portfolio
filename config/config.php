<?php
// Configuration File for Rajkumar Bangal Portfolio
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_NAME', 'Rajkumar Bangal | Portfolio');
define('DEVELOPER_NAME', 'Rajkumar Bangal');
define('DEVELOPER_TITLE', 'Junior PHP Developer | CodeIgniter Developer | Full Stack Web Developer');
define('DEVELOPER_LOCATION', 'Kolkata, West Bengal, India');
define('DEVELOPER_EMAIL', '');
define('DEVELOPER_PHONE', '');
define('DEVELOPER_LINKEDIN', '');
define('DEVELOPER_GITHUB', 'https://github.com/rajkumarbangal020-jpg');

// Set base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = preg_replace('#/(admin|api).*$#i', '', $scriptDir);
if ($basePath === '/') {
    $basePath = '';
}
define('BASE_URL', $protocol . "://" . $host . $basePath . '/');

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security Sanitization Helper Functions
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function get_csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

// Clean inline dark styles or dark text classes from dynamic HTML content
function sanitize_html_styles($html) {
    if (empty($html)) return '';
    $html = preg_replace('/style\s*=\s*["\'][^"\']*color\s*:\s*(black|#000|#000000|#111|#111111|#212529|#1f2937|#0f172a)[^"\']*["\']/i', '', $html);
    $html = preg_replace('/class\s*=\s*["\']([^"\']*\b)(text-dark|text-black)(\b[^"\']*)["\']/i', 'class="$1text-light$3"', $html);
    return $html;
}

// Project Image URL Resolver & Fallback Helper
function get_project_image_url($thumbnail = '') {
    $thumbnail = trim($thumbnail ?? '');
    
    if (!empty($thumbnail)) {
        $cleanPath = ltrim($thumbnail, '/');
        $basename = basename($thumbnail);

        $candidates = [
            // 1. Direct path from project root
            ['file' => __DIR__ . '/../' . $cleanPath, 'url' => BASE_URL . $cleanPath],
            // 2. File in uploads/projects/
            ['file' => __DIR__ . '/../uploads/projects/' . $basename, 'url' => BASE_URL . 'uploads/projects/' . $basename],
            // 3. File in assets/projects/
            ['file' => __DIR__ . '/../assets/projects/' . $basename, 'url' => BASE_URL . 'assets/projects/' . $basename]
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand['file']) && is_file($cand['file']) && filesize($cand['file']) > 0) {
                return $cand['url'];
            }
        }
    }

    // Default Fallback Placeholder Image
    if (file_exists(__DIR__ . '/../uploads/projects/placeholder.png')) {
        return BASE_URL . 'uploads/projects/placeholder.png';
    }
    if (file_exists(__DIR__ . '/../assets/projects/placeholder.png')) {
        return BASE_URL . 'assets/projects/placeholder.png';
    }

    return BASE_URL . 'assets/images/developer_hero.svg';
}
