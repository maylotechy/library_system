<?php
global $conn;
require '../config/db_connection.php';

// Check if user is logged in and is a student
require_once '../authentication/auth_check_student.php';

if ($_SESSION['user_type'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Only students can borrow books']);
    exit;
}

// Check if book ID is provided
if (!isset($_POST['book_id'])){
    echo json_encode(['status' => 'error', 'message' => 'Book ID is required']);
    exit;
}

$book_id = $_POST['book_id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if the book exists and is available
    $stmt = $conn->prepare("SELECT available_quantity FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo json_encode(['status' => 'error', 'message' => 'Book not found']);
        exit;
    }

    if ($book['available_quantity'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'This book is currently not available']);
        exit;
    }

    // Check if the student already has a pending request for this book
    $stmt = $conn->prepare("SELECT id FROM borrowings WHERE book_id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$book_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You already have a pending request for this book']);
        exit;
    }

    // Insert the borrowing request
    $stmt = $conn->prepare("INSERT INTO borrowings (book_id, user_id, status, request_date) VALUES (?, ?, 'pending', NOW())");
    $stmt->execute([$book_id, $user_id]);

    echo json_encode(['status' => 'success', 'message' => 'Book borrow request submitted successfully']);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
}
?>