<?php
global $pdo;
header('Content-Type: application/json');
require_once '../config/db_connection.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get request data
$requestId = isset($_POST['requestId']) ? intval($_POST['requestId']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($requestId <= 0 || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Get the borrowing record
    $stmt = $pdo->prepare("SELECT * FROM borrowings WHERE id = ?");
    $stmt->execute([$requestId]);
    $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$borrowing) {
        throw new Exception("Borrowing record not found");
    }

    if ($borrowing['status'] !== 'pending') {
        throw new Exception("This request has already been processed");
    }

    // Update the borrowing record
    $newStatus = $action === 'approve' ? 'approved' : 'rejected';
    $now = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        UPDATE borrowings 
        SET status = ?, admin_id = ?, borrow_date = ?, due_date = ?
        WHERE id = ?
    ");

    // Calculate due date (1 day from now for approval)
    $dueDate = $action === 'approve' ? date('Y-m-d H:i:s', strtotime('+1 day')) : null;

    $stmt->execute([
        $newStatus,
        $_SESSION['user_id'] ?? null, // Assuming admin is logged in
        $now,
        $dueDate,
        $requestId
    ]);

    // If approved, update book available quantity
    if ($action === 'approve') {
        $stmt = $pdo->prepare("
            UPDATE books 
            SET available_quantity = available_quantity - 1 
            WHERE id = ? AND available_quantity > 0
        ");
        $stmt->execute([$borrowing['book_id']]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("No available copies of this book");
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Request processed successfully'
    ]);
} catch(Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>