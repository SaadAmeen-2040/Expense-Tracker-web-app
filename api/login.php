<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'jwt_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $payload = [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    $token = JWTHelper::generate($payload);
    // Set Session for PHP-only navigation
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];

    echo json_encode([
        'token' => $token,
        'role' => $user['role'],
        'username' => $user['username']
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
?>

