<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';

$userData = authenticate();
isAdmin($userData);

$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$expenseStats = $pdo->query("SELECT COUNT(*) as totalExpenses, SUM(amount) as totalAmount FROM expenses")->fetch();

echo json_encode([
    'totalUsers' => (int)$userCount,
    'totalExpenses' => (int)$expenseStats['totalExpenses'],
    'totalAmount' => (float)($expenseStats['totalAmount'] ?? 0)
]);
?>
