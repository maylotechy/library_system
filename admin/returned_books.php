<?php
global $conn;
require_once '../config/db_connection.php';
require_once '../helper/functions.php';
require_once '../authentication/auth_check_admin.php';

// Get all returned books
$stmt = $conn->prepare("
    SELECT b.id, b.book_id, b.user_id, b.borrow_date, b.return_date,
           bk.title as book_title, u.full_name as student_name
    FROM borrowings b
    JOIN books bk ON b.book_id = bk.id
    JOIN users u ON b.user_id = u.id
    WHERE b.status = 'returned'
    ORDER BY b.return_date DESC
");
$stmt->execute();
$returnedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management | Return History</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/sidebar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Return History
                <small>Books that have been returned</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Returned Books</h3>
                        </div>
                        <div class="box-body">
                            <?php if (count($returnedBooks) > 0): ?>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Book</th>
                                        <th>Borrow Date</th>
                                        <th>Return Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($returnedBooks as $book): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($book['id']); ?></td>
                                            <td><?= htmlspecialchars($book['student_name']); ?></td>
                                            <td><?= htmlspecialchars($book['book_title']); ?></td>
                                            <td><?= htmlspecialchars($book['borrow_date']); ?></td>
                                            <td><?= htmlspecialchars($book['return_date']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No returned books found.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>