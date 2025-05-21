<?php

// Define book management functions
function addBook($title, $author, $isbn, $category, $description, $quantity) {
    global $conn;

    try {
        // Check if ISBN already exists
        $check_stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ?");
        $check_stmt->execute([$isbn]);
        if ($check_stmt->rowCount() > 0) {
            $_SESSION['error'] = "A book with this ISBN already exists.";
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, category, description, quantity, available_quantity) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Set available quantity equal to quantity initially
        $result = $stmt->execute([$title, $author, $isbn, $category, $description, $quantity, $quantity]);

        return $result;
    } catch (PDOException $e) {
        error_log("Database error in addBook: " . $e->getMessage());
        return false;
    }
}

function updateBook($id, $title, $author, $isbn, $category, $description, $quantity) {
    global $conn;

    try {
        $check_stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ? AND id != ?");
        $check_stmt->execute([$isbn, $id]);
        if ($check_stmt->rowCount() > 0) {
            $_SESSION['error'] = "Another book with this ISBN already exists.";
            return false;
        }

        $get_stmt = $conn->prepare("SELECT quantity, available_quantity FROM books WHERE id = ?");
        $get_stmt->execute([$id]);
        $current_book = $get_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current_book) {
            $_SESSION['error'] = "Book not found.";
            return false;
        }

        $borrowed = $current_book['quantity'] - $current_book['available_quantity'];
        $new_available = max(0, $quantity - $borrowed);

        $stmt = $conn->prepare("UPDATE books SET 
                              title = ?,
                              author = ?,
                              isbn = ?,
                              category = ?,
                              description = ?,
                              quantity = ?,
                              available_quantity = ?
                              WHERE id = ?");

        $result = $stmt->execute([$title, $author, $isbn, $category, $description, $quantity, $new_available, $id]);

        return $result;
    } catch (PDOException $e) {
        error_log("Database error in updateBook: " . $e->getMessage());
        return false;
    }
}

function deleteBook($id) {
    global $conn;

    error_log("Attempting to delete book ID: $id");

    try {
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM borrowings WHERE book_id = ?");
        if (!$check_stmt->execute([$id])) {
            error_log("Borrowing check query failed");
            $_SESSION['error'] = "Database error checking borrowings";
            return false;
        }

        $borrowed_count = $check_stmt->fetchColumn();
        error_log("Active borrowings found: $borrowed_count");

        if ($borrowed_count > 0) {
            $_SESSION['error'] = "Cannot delete this book as it is currently borrowed by users.";
            return false;
        }

        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        if (!$stmt->execute([$id])) {
            error_log("Delete query failed");
            $_SESSION['error'] = "Database error deleting book";
            return false;
        }

        $deleted = $stmt->rowCount();
        error_log("Rows deleted: $deleted");

        return $deleted > 0;

    } catch (PDOException $e) {
        error_log("PDO Exception: " . $e->getMessage());
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        return false;
    }
}

function getAllBooks() {
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT * FROM books ORDER BY title ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllBooks: " . $e->getMessage());
        return [];
    }
}



// ========== Student Management Functions ==========

function addStudent($conn, $data) {
    if (empty($data['username']) || empty($data['password']) || empty($data['email']) || empty($data['full_name'])) {
        return ['status' => 'error', 'message' => 'All fields are required'];
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$data['username'], $data['email']]);
    if ($stmt->fetch()) {
        return ['status' => 'error', 'message' => 'Username or email already exists'];
    }

    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (username, password, email, full_name, user_type)
        VALUES (?, ?, ?, ?, 'student')
    ");

    if ($stmt->execute([$data['username'], $hashedPassword, $data['email'], $data['full_name']])) {
        $newId = $conn->lastInsertId();
        return [
            'status' => 'success',
            'message' => 'Student added successfully!',
            'data' => [
                'id' => $newId,
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name']
            ]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Error adding student. Please try again.'];
    }
}

function editStudent($conn, $data) {
    if (empty($data['username']) || empty($data['email']) || empty($data['full_name'])) {
        return ['status' => 'error', 'message' => 'Username, email and full name are required'];
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$data['username'], $data['email'], $data['student_id']]);
    if ($stmt->fetch()) {
        return ['status' => 'error', 'message' => 'Username or email already exists for another user'];
    }

    if (!empty($data['password'])) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            UPDATE users 
            SET username = ?, email = ?, full_name = ?, password = ? 
            WHERE id = ? AND user_type = 'student'
        ");
        $result = $stmt->execute([$data['username'], $data['email'], $data['full_name'], $hashedPassword, $data['student_id']]);
    } else {
        $stmt = $conn->prepare("
            UPDATE users 
            SET username = ?, email = ?, full_name = ? 
            WHERE id = ? AND user_type = 'student'
        ");
        $result = $stmt->execute([$data['username'], $data['email'], $data['full_name'], $data['student_id']]);
    }

    if ($result) {
        return [
            'status' => 'success',
            'message' => 'Student updated successfully!',
            'data' => [
                'id' => $data['student_id'],
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name']
            ]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Error updating student. Please try again.'];
    }
}

function deleteStudent($conn, $studentId) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ?");
    $stmt->execute([$studentId]);
    $hasBorrowings = $stmt->fetchColumn() > 0;

    if ($hasBorrowings) {
        return ['status' => 'error', 'message' => 'Cannot delete student with active borrowings'];
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND user_type = 'student'");
    if ($stmt->execute([$studentId])) {
        return [
            'status' => 'success',
            'message' => 'Student deleted successfully!',
            'data' => ['id' => $studentId]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Error deleting student. Please try again.'];
    }
}
function getAllStudents(PDO $conn): array {
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_type = :user_type");
        $stmt->execute(['user_type' => 'student']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [];
    }
}
function processBorrowRequest($requestId, $action, $adminId, $conn) {
    $result = [
        'success' => false,
        'message' => ''
    ];

    try {
        $conn->beginTransaction();

        // Get the borrowing record
        $stmt = $conn->prepare("SELECT * FROM borrowings WHERE id = ?");
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

        $stmt = $conn->prepare("
            UPDATE borrowings 
            SET status = ?, admin_id = ?, borrow_date = ?, due_date = ?
            WHERE id = ?
        ");

        // Calculate due date (1 day from now for approval)
        $dueDate = $action === 'approve' ? date('Y-m-d H:i:s', strtotime('+7 day')) : null;

        $stmt->execute([
            $newStatus,
            $adminId,
            $now,
            $dueDate,
            $requestId
        ]);

        // If approved, update book available quantity
        if ($action === 'approve') {
            $stmt = $conn->prepare("
                UPDATE books 
                SET available_quantity = available_quantity - 1 
                WHERE id = ? AND available_quantity > 0
            ");
            $stmt->execute([$borrowing['book_id']]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("No available copies of this book");
            }
        }

        $conn->commit();
        $result['success'] = true;
        $result['message'] = "Request processed successfully!";
    } catch(Exception $e) {
        $conn->rollBack();
        $result['message'] = $e->getMessage();
    }

    return $result;
}

