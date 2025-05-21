<?php
global $conn;
header('Content-Type: application/json');
require_once '../config/db_connection.php'; // ensures $pdo is defined

try {
    $stmt = $conn->prepare("
        SELECT b.id, u.full_name AS student_name, bk.title AS book_title, 
               DATE_FORMAT(b.request_date, '%Y-%m-%d %H:%i') AS request_date, 
               b.status
        FROM borrowings b
        JOIN users u ON b.user_id = u.id
        JOIN books bk ON b.book_id = bk.id
        ORDER BY b.request_date DESC
        LIMIT 5
    ");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $requests
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching recent requests: ' . $e->getMessage()
    ]);
}
?>
