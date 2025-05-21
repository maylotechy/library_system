<?php

global $conn;
require_once '../config/db_connection.php'; // Assumes $conn is a PDO object

header('Content-Type: application/json');

require_once '../authentication/auth_check_student.php';

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare(
        "SELECT b.title, b.author, br.borrow_date, br.due_date, br.status
         FROM borrowings br
         JOIN books b ON br.book_id = b.id
         WHERE br.user_id = ?
           AND (br.status = 'pending' OR br.status = 'approved')
         ORDER BY br.borrow_date DESC"
    );

    $stmt->execute([$user_id]);
    $borrowings = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['borrow_date'] = date('M d, Y', strtotime($row['borrow_date']));
        if (!empty($row['due_date'])) {
            $row['due_date'] = date('M d, Y', strtotime($row['due_date']));
        }
        $borrowings[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $borrowings
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
