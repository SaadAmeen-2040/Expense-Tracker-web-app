<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';

$userData = authenticate();
isAdmin($userData);

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$userId = null;
if (preg_match('/users\/(\d+)/', $requestUri, $matches)) {
    $userId = $matches[1];
}

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("
            SELECT u.id, u.username, u.role, COUNT(e.id) as expenseCount
            FROM users u
            LEFT JOIN expenses e ON u.id = e.user_id
            GROUP BY u.id
        ");
        $users = $stmt->fetchAll();
        echo json_encode(['users' => $users]);
        break;

    case 'DELETE':
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID required']);
            exit;
        }
        if ($userId == 1) {
            http_response_code(403);
            echo json_encode(['error' => 'Cannot delete main admin']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        // Expenses are deleted via CASCADE foreign key
        echo json_encode(['message' => 'User and their expenses deleted']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
?>
