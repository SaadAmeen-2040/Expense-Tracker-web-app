<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';

$userData = authenticate();
$userId = $userData['id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->prepare("SELECT username, email, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        echo json_encode(['user' => $user]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $oldPassword = $data['old_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        if (!$email) {
            http_response_code(400);
            echo json_encode(['error' => 'Email is required']);
            exit;
        }

        // Get current user data to verify password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($newPassword) {
            // Verify old password before changing
            if (!password_verify($oldPassword, $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Current password incorrect']);
                exit;
            }
            
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
            $stmt->execute([$email, $newHash, $userId]);
        } else {
            // Only update email
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $userId]);
        }

        echo json_encode(['message' => 'Profile updated successfully']);
        break;

    default:
        http_response_code(405);
        break;
}
?>
