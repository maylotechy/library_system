<?php
global $conn;
header('Content-Type: application/json');
require_once '../config/db_connection.php';

try {
    $stmt = $conn->prepare("
        SELECT b.id, u.full_name as student_name, bk.title as book_title, 
               DATE_FORMAT(b.return_date, '%Y-%m-%d %H:%i') as return_date
        FROM borrowings b
        JOIN users u ON b.user_id = u.id
        JOIN books bk ON b.book_id = bk.id
        WHERE b.status = 'returned'
        ORDER BY b.return_date DESC
        LIMIT 5
    ");
    $stmt->execute();
    $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $returns
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching recent returns: ' . $e->getMessage()
    ]);
}
?>