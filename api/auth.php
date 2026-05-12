<?php
require_once 'jwt_helper.php';

function authenticate() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        $userData = JWTHelper::validate($token);
        if ($userData) return $userData;
    }

    // Fallback to PHP Session
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['user_role']
        ];
    }

    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

function isAdmin($userData) {
    if ($userData['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Require Admin Role']);
        exit;
    }
}
?>
