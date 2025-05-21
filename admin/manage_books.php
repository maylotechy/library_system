<?php
require_once '../config/db_connection.php';
require_once '../helper/functions.php';

require_once '../authentication/auth_check_admin.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set header to return JSON
    header('Content-Type: application/json');

    // Add book
    if (isset($_POST['action']) && $_POST['action'] === 'add_book') {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $isbn = trim($_POST['isbn']);
        $category = trim($_POST['category']);
        $description = trim($_POST['description']);
        $quantity = intval($_POST['quantity']);

        if (addBook($title, $author, $isbn, $category, $description, $quantity)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Book added successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => isset($_SESSION['error']) ? $_SESSION['error'] : 'Error adding book. Please try again.'
            ]);
            unset($_SESSION['error']);
        }
        exit;
    }
    // Update book
    elseif (isset($_POST['action']) && $_POST['action'] === 'update_book') {
        $id = intval($_POST['book_id']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $isbn = trim($_POST['isbn']);
        $category = trim($_POST['category']);
        $description = trim($_POST['description']);
        $quantity = intval($_POST['quantity']);

        if (updateBook($id, $title, $author, $isbn, $category, $description, $quantity)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Book updated successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => isset($_SESSION['error']) ? $_SESSION['error'] : 'Error updating book. Please try again.'
            ]);
            unset($_SESSION['error']);
        }
        exit;
    }
    // Delete book
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete_book') {
        $id = intval($_POST['book_id']);

        try {
            if (deleteBook($id)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Book deleted successfully!'
                ]);
            } else {
                throw new Exception($_SESSION['error'] ?? 'Error deleting book. Please try again.');
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}

// Get all books
$books = getAllBooks();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management | Manage Books</title>
    <!-- Include all your CSS and JS files from the dashboard -->
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
                Manage Books
                <small>Add, edit, and delete books</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Books List</h3>
                            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#addBookModal">
                                <i class="fa fa-plus"></i> Add New Book
                            </button>
                        </div>
                        <div class="box-body">
                            <table id="booksTable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>ISBN</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Available</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($books as $book): ?>
                                    <tr id="book-row-<?= $book['id']; ?>">
                                        <td><?= htmlspecialchars($book['id']); ?></td>
                                        <td><?= htmlspecialchars($book['title']); ?></td>
                                        <td><?= htmlspecialchars($book['author']); ?></td>
                                        <td><?= htmlspecialchars($book['isbn']); ?></td>
                                        <td><?= htmlspecialchars($book['category']); ?></td>
                                        <td><?= htmlspecialchars($book['quantity']); ?></td>
                                        <td><?= htmlspecialchars($book['available_quantity']); ?></td>
                                        <td>
                                            <button class="btn btn-xs btn-primary edit-btn"
                                                    data-id="<?= $book['id']; ?>"
                                                    data-title="<?= htmlspecialchars($book['title']); ?>"
                                                    data-author="<?= htmlspecialchars($book['author']); ?>"
                                                    data-isbn="<?= htmlspecialchars($book['isbn']); ?>"
                                                    data-category="<?= htmlspecialchars($book['category']); ?>"
                                                    data-description="<?= htmlspecialchars($book['description']); ?>"
                                                    data-quantity="<?= htmlspecialchars($book['quantity']); ?>">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-xs btn-danger delete-btn"
                                                    data-id="<?= $book['id']; ?>"
                                                    data-title="<?= htmlspecialchars($book['title']); ?>">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" role="dialog" aria-labelledby="addBookModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addBookForm">
                    <input type="hidden" name="action" value="add_book">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="addBookModalLabel">Add New Book</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="isbn">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addBookBtn">
                            <span class="btn-text">Add Book</span>
                            <span class="spinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" role="dialog" aria-labelledby="editBookModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editBookForm">
                    <input type="hidden" name="action" value="update_book">
                    <input type="hidden" id="edit_book_id" name="book_id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="editBookModalLabel">Edit Book</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_title">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_author">Author</label>
                            <input type="text" class="form-control" id="edit_author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_isbn">ISBN</label>
                            <input type="text" class="form-control" id="edit_isbn" name="isbn" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_category">Category</label>
                            <input type="text" class="form-control" id="edit_category" name="category" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_quantity">Quantity</label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updateBookBtn">
                            <span class="btn-text">Update Book</span>
                            <span class="spinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Book Modal -->
    <div class="modal fade" id="deleteBookModal" tabindex="-1" role="dialog" aria-labelledby="deleteBookModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="deleteBookForm">
                    <input type="hidden" name="action" value="delete_book">
                    <input type="hidden" id="delete_book_id" name="book_id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="deleteBookModalLabel">Delete Book</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete "<span id="delete_book_title"></span>"?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="deleteBookBtn">
                            <span class="btn-text">Delete</span>
                            <span class="spinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</div>
<!-- jQuery 3 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 3.3.7 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/js/adminlte.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    // Toastr notification configuration
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $(document).ready(function() {
        // Display existing session messages as toasts
        <?php if (isset($_SESSION['success'])): ?>
        toastr.success('<?= addslashes($_SESSION['success']) ?>');
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        toastr.error('<?= addslashes($_SESSION['error']) ?>');
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        // Initialize DataTable
        var table = $('#booksTable').DataTable({
            "responsive": true,
            "autoWidth": false
        });

        // Handle edit button click
        $(document).on('click', '.edit-btn', function() {
            var $btn = $(this);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

            var bookId = $(this).data('id');
            var title = $(this).data('title');
            var author = $(this).data('author');
            var isbn = $(this).data('isbn');
            var category = $(this).data('category');
            var description = $(this).data('description');
            var quantity = $(this).data('quantity');

            $('#edit_book_id').val(bookId);
            $('#edit_title').val(title);
            $('#edit_author').val(author);
            $('#edit_isbn').val(isbn);
            $('#edit_category').val(category);
            $('#edit_description').val(description);
            $('#edit_quantity').val(quantity);

            $('#editBookModal').modal('show');
            $btn.html('<i class="fa fa-edit"></i> Edit').prop('disabled', false);
        });

        // Handle delete button click
        $(document).on('click', '.delete-btn', function() {
            var $btn = $(this);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

            var bookId = $(this).data('id');
            var title = $(this).data('title');

            $('#delete_book_id').val(bookId);
            $('#delete_book_title').text(title);

            $('#deleteBookModal').modal('show');
            $btn.html('<i class="fa fa-trash"></i> Delete').prop('disabled', false);
        });

        // Add Book Form AJAX submission
        $('#addBookForm').on('submit', function(e) {
            e.preventDefault();

            $('#addBookBtn .btn-text').hide();
            $('#addBookBtn .spinner').show();
            $('#addBookBtn').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'manage_books.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#addBookModal').modal('hide');
                        $('#addBookForm')[0].reset();
                        // Reload the page to refresh the table
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while processing your request.');
                },
                complete: function() {
                    $('#addBookBtn .btn-text').show();
                    $('#addBookBtn .spinner').hide();
                    $('#addBookBtn').prop('disabled', false);
                }
            });
        });

        // Edit Book Form AJAX submission
        $('#editBookForm').on('submit', function(e) {
            e.preventDefault();

            $('#updateBookBtn .btn-text').hide();
            $('#updateBookBtn .spinner').show();
            $('#updateBookBtn').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'manage_books.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#editBookModal').modal('hide');
                        // Reload the page to refresh the table
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while processing your request.');
                },
                complete: function() {
                    $('#updateBookBtn .btn-text').show();
                    $('#updateBookBtn .spinner').hide();
                    $('#updateBookBtn').prop('disabled', false);
                }
            });
        });

        // Delete Book Form AJAX submission
        $('#deleteBookForm').on('submit', function(e) {
            e.preventDefault();

            $('#deleteBookBtn .btn-text').hide();
            $('#deleteBookBtn .spinner').show();
            $('#deleteBookBtn').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'manage_books.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#deleteBookModal').modal('hide');
                        location.reload();

                        // Better way to remove the row
                        var bookId = $('#delete_book_id').val();
                        $('#book-row-' + bookId).remove();

                        // If using DataTables, you should also redraw
                        table.draw();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while processing your request.');
                },
                complete: function() {
                    $('#deleteBookBtn .btn-text').show();
                    $('#deleteBookBtn .spinner').hide();
                    $('#deleteBookBtn').prop('disabled', false);
                }
            });
        });

        // Reset form when modal is closed
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $(this).find('.btn-text').show();
            $(this).find('.spinner').hide();
            $(this).find('button[type="submit"]').prop('disabled', false);
        });
    });
</script>
</body>
</html>