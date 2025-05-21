<?php
global $conn;
header('Content-Type: application/json');
require_once '../config/db_connection.php';

try {
    // Get total books count
    $stmt = $conn->query("SELECT COUNT(*) as totalBooks FROM books");
    $totalBooks = $stmt->fetch(PDO::FETCH_ASSOC)['totalBooks'];

    // Get available books count
    $stmt = $conn->query("SELECT SUM(available_quantity) as availableBooks FROM books");
    $availableBooks = $stmt->fetch(PDO::FETCH_ASSOC)['availableBooks'];

    // Get pending requests count
    $stmt = $conn->query("SELECT COUNT(*) as pendingRequests FROM borrowings WHERE status = 'pending'");
    $pendingRequests = $stmt->fetch(PDO::FETCH_ASSOC)['pendingRequests'];

    // Get borrowed books count
    $stmt = $conn->query("SELECT COUNT(*) as borrowedBooks FROM borrowings WHERE status = 'approved' AND return_date IS NULL");
    $borrowedBooks = $stmt->fetch(PDO::FETCH_ASSOC)['borrowedBooks'];

    echo json_encode([
        'success' => true,
        'totalBooks' => $totalBooks,
        'availableBooks' => $availableBooks,
        'pendingRequests' => $pendingRequests,
        'borrowedBooks' => $borrowedBooks
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching dashboard data: ' . $e->getMessage()
    ]);
}
?>
