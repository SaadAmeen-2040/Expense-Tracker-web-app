<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';

$userData = authenticate();
$userId = $userData['id'];
$method = $_SERVER['REQUEST_METHOD'];

// Handle potential ID in path if using mod_rewrite (e.g. /api/expenses/123)
// For simplicity, we can also handle it via query param or parsing REQUEST_URI
$requestUri = $_SERVER['REQUEST_URI'];
$uriParts = explode('/', trim($requestUri, '/'));
$expenseId = end($uriParts);
if (!is_numeric($expenseId) && strpos($expenseId, '.') === false && $expenseId !== 'expenses') {
    // expenseId is likely a string ID from the frontend
} else {
    $expenseId = null;
}

// Alternatively, let's just check if there's an ID after 'expenses'
if (preg_match('/expenses\/([a-zA-Z0-9_-]+)/', $requestUri, $matches)) {
    $expenseId = $matches[1];
}

switch ($method) {
    case 'GET':
        $stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY date DESC, id DESC");
        $stmt->execute([$userId]);
        $expenses = $stmt->fetchAll();
        echo json_encode(['expenses' => $expenses]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO expenses (id, title, amount, category, date, notes, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['id'],
            $data['title'],
            $data['amount'],
            $data['category'],
            $data['date'],
            $data['notes'] ?? '',
            $userId
        ]);
        http_response_code(201);
        echo json_encode(['message' => 'Expense added']);
        break;

    case 'PUT':
        if (!$expenseId) {
            http_response_code(400);
            echo json_encode(['error' => 'Expense ID required']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("UPDATE expenses SET title = ?, amount = ?, category = ?, date = ?, notes = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $data['title'],
            $data['amount'],
            $data['category'],
            $data['date'],
            $data['notes'] ?? '',
            $expenseId,
            $userId
        ]);
        echo json_encode(['message' => 'Expense updated']);
        break;

    case 'DELETE':
        if (!$expenseId) {
            http_response_code(400);
            echo json_encode(['error' => 'Expense ID required']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->execute([$expenseId, $userId]);
        echo json_encode(['message' => 'Expense deleted']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
?>
