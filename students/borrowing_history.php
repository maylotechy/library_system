<?php
global $conn;

require_once '../config/db_connection.php'; // Assumes $conn is a PDO instance

header('Content-Type: application/json');
require_once '../authentication/auth_check_student.php';

$user_id = $_SESSION['user_id'];


try {
    $stmt = $conn->prepare(
        "SELECT b.title, b.author, br.borrow_date, br.due_date, br.return_date, br.status
         FROM borrowings br
         JOIN books b ON br.book_id = b.id
         WHERE br.user_id = :user_id
         ORDER BY br.borrow_date DESC"
    );

    $stmt->execute(['user_id' => $user_id]);
    $history = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Format dates
        $row['borrow_date'] = date('M d, Y', strtotime($row['borrow_date']));
        if (!empty($row['due_date'])) {
            $row['due_date'] = date('M d, Y', strtotime($row['due_date']));
        }
        if (!empty($row['return_date'])) {
            $row['return_date'] = date('M d, Y', strtotime($row['return_date']));
        }
        $history[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $history
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
