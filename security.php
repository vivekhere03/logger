<?php
// Security Configuration File

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Access denied');
}

// Security Headers
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\';');
}

// Input Sanitization
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate User Input
function validateInput($input, $type = 'string', $maxLength = 255) {
    $input = trim($input);
    
    switch ($type) {
        case 'username':
            return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $input) ? $input : false;
        case 'password':
            return strlen($input) >= 6 && strlen($input) <= 50 ? $input : false;
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) ? $input : false;
        case 'int':
            return is_numeric($input) ? intval($input) : false;
        case 'string':
        default:
            return strlen($input) <= $maxLength ? $input : false;
    }
}

// CSRF Protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Rate Limiting
function checkRateLimit($action, $limit = 5, $window = 300) {
    $key = $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    
    $now = time();
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 0, 'window' => $now];
    }
    
    $data = $_SESSION['rate_limit'][$key];
    
    if ($now - $data['window'] > $window) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'window' => $now];
        return true;
    }
    
    if ($data['count'] >= $limit) {
        return false;
    }
    
    $_SESSION['rate_limit'][$key]['count']++;
    return true;
}

// Secure Session Configuration
function secureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
}

// Initialize security
setSecurityHeaders();
secureSession();
?>
