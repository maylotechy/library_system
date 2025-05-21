<?php
global $conn;
require_once '../config/db_connection.php';
require_once '../helper/functions.php';
require_once '../authentication/auth_check_admin.php';

// Handle book return
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book'])) {
    $borrowingId = intval($_POST['borrowing_id']);

    try {
        $conn->beginTransaction();

        // Get the borrowing record
        $stmt = $conn->prepare("SELECT * FROM borrowings WHERE id = ? AND status = 'approved'");
        $stmt->execute([$borrowingId]);
        $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$borrowing) {
            throw new Exception("Borrowing record not found or already returned");
        }

        // Update the borrowing record
        $now = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("
            UPDATE borrowings 
            SET status = 'returned', return_date = ?
            WHERE id = ?
        ");
        $stmt->execute([$now, $borrowingId]);

        // Update book available quantity
        $stmt = $conn->prepare("
            UPDATE books 
            SET available_quantity = available_quantity + 1 
            WHERE id = ?
        ");
        $stmt->execute([$borrowing['book_id']]);

        $conn->commit();
        // Store toastr notification data
        $_SESSION['toastr'] = [
            'type' => 'success',
            'message' => "Book returned successfully!"
        ];
    } catch(Exception $e) {
        $conn->rollBack();
        // Store error message for toastr
        $_SESSION['toastr'] = [
            'type' => 'error',
            'message' => $e->getMessage()
        ];
    }
    header('Location: borrowed_books.php');
    exit;
}

// Get all borrowed books (approved but not returned)
$stmt = $conn->prepare("
    SELECT b.id, b.book_id, b.user_id, b.borrow_date, b.due_date,
           bk.title as book_title, u.full_name as student_name
    FROM borrowings b
    JOIN books bk ON b.book_id = bk.id
    JOIN users u ON b.user_id = u.id
    WHERE b.status = 'approved' AND b.return_date IS NULL
    ORDER BY b.borrow_date DESC
");
$stmt->execute();
$borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management | Borrowed Books</title>
    <?php include '../includes/header.php'; ?>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
                Borrowed Books
                <small>Currently borrowed books</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Legacy success/error alerts (keeping for backwards compatibility) -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Currently Borrowed Books</h3>
                        </div>
                        <div class="box-body">
                            <?php if (count($borrowedBooks) > 0): ?>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Book</th>
                                        <th>Borrow Date</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($borrowedBooks as $book): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($book['id']); ?></td>
                                            <td><?= htmlspecialchars($book['student_name']); ?></td>
                                            <td><?= htmlspecialchars($book['book_title']); ?></td>
                                            <td><?= htmlspecialchars($book['borrow_date']); ?></td>
                                            <td><?= htmlspecialchars($book['due_date']); ?></td>
                                            <td>
                                                <form method="POST" action="borrowed_books.php">
                                                    <input type="hidden" name="borrowing_id" value="<?= $book['id']; ?>">
                                                    <button type="submit" name="return_book" class="btn btn-xs btn-primary">
                                                        <i class="fa fa-check"></i> Mark as Returned
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No currently borrowed books found.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Initialize Toastr -->
    <script>
        // Configure toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        <?php if (isset($_SESSION['toastr'])): ?>
        // Display toastr notification
        toastr.<?= $_SESSION['toastr']['type']; ?>('<?= $_SESSION['toastr']['message']; ?>');
        <?php unset($_SESSION['toastr']); ?>
        <?php endif; ?>
    </script>
</div>
</body>
</html>