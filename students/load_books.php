<?php

global $conn;
require_once '../config/db_connection.php';   // ← creates $conn (PDO)

header('Content-Type: application/json');
require_once '../authentication/auth_check_student.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if ($search === '') {
        // No search term – return all books
        $stmt = $conn->prepare(
            "SELECT id, title, author, isbn, category, description,
                    quantity, available_quantity
             FROM   books
             ORDER  BY title ASC"
        );
        $stmt->execute();
    } else {
        // Search term present – use wildcard search
        $searchWild = "%{$search}%";
        $stmt = $conn->prepare(
            "SELECT id, title, author, isbn, category, description,
                    quantity, available_quantity
             FROM   books
             WHERE  title   LIKE :s
                OR  author  LIKE :s
                OR  isbn    LIKE :s
                OR  category LIKE :s
             ORDER  BY title ASC"
        );
        $stmt->execute(['s' => $searchWild]);
    }

    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data'   => $books
    ]);
} catch (PDOException $e) {
    // Any DB error is caught here
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
