<?php
// Start session
global  $conn;
require_once '../authentication/auth_check_student.php';

// Include database connection
require '../config/db_connection.php';

// Get user data
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Library Management System</title>
    <!-- AdminLTE 3 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" role="button">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="student_dashboard.php" class="brand-link">
            <span class="brand-text font-weight-light">Library System</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block"><?php echo $full_name; ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" id="dashboard-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" id="search-books-link">
                            <i class="nav-icon fas fa-search"></i>
                            <p>Search Books</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" id="borrowed-books-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>My Borrowed Books</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" id="borrow-history-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Borrowing History</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content pt-4">
            <div class="container-fluid">
                <div id="dashboard-content">
                    <!-- Dashboard Content -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <?php
                                    // Count pending book requests using PDO
                                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowings WHERE user_id = ? AND status = 'pending'");
                                    $stmt->execute([$user_id]);
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <h3><?php echo $row['count']; ?></h3>
                                    <p>Pending Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <?php
                                    // Count approved books using PDO
                                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowings WHERE user_id = ? AND status = 'approved'");
                                    $stmt->execute([$user_id]);
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <h3><?php echo $row['count']; ?></h3>
                                    <p>Currently Borrowed</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <?php
                                    // Count returned books using PDO
                                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowings WHERE user_id = ? AND status = 'returned'");
                                    $stmt->execute([$user_id]);
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <h3><?php echo $row['count']; ?></h3>
                                    <p>Returned Books</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-undo"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <?php
                                    // Count rejected requests using PDO
                                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM borrowings WHERE user_id = ? AND status = 'rejected'");
                                    $stmt->execute([$user_id]);
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <h3><?php echo $row['count']; ?></h3>
                                    <p>Rejected Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recently Borrowed Books</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Book Title</th>
                                            <th>Borrow Date</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        // Get recent borrowings using PDO
                                        $stmt = $conn->prepare("
                                            SELECT b.title, br.borrow_date, br.due_date, br.status 
                                            FROM borrowings br
                                            JOIN books b ON br.book_id = b.id
                                            WHERE br.user_id = ?
                                            ORDER BY br.borrow_date DESC
                                            LIMIT 5
                                        ");
                                        $stmt->execute([$user_id]);
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        if (count($result) > 0) {
                                            foreach ($result as $row) {
                                                echo "<tr>";
                                                echo "<td>" . $row['title'] . "</td>";
                                                echo "<td>" . date('M d, Y', strtotime($row['borrow_date'])) . "</td>";
                                                echo "<td>" . ($row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : 'N/A') . "</td>";

                                                $status_class = '';
                                                switch ($row['status']) {
                                                    case 'pending':
                                                        $status_class = 'badge-warning';
                                                        break;
                                                    case 'approved':
                                                        $status_class = 'badge-success';
                                                        break;
                                                    case 'rejected':
                                                        $status_class = 'badge-danger';
                                                        break;
                                                    case 'returned':
                                                        $status_class = 'badge-info';
                                                        break;
                                                }

                                                echo "<td><span class='badge " . $status_class . "'>" . ucfirst($row['status']) . "</span></td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center'>No recent borrowings</td></tr>";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="search-books-content" style="display: none;">
                    <!-- Search Books Content -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Search Books</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <input type="text" id="search-input" class="form-control" placeholder="Search by title, author, or ISBN...">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="search-button" class="btn btn-primary btn-block">Search</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="books-table">
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>ISBN</th>
                                        <th>Category</th>
                                        <th>Available</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Books will be loaded here via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="borrowed-books-content" style="display: none;">
                    <!-- Borrowed Books Content -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">My Borrowed Books</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody id="borrowed-books-table">
                                <!-- Borrowed books will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="borrow-history-content" style="display: none;">
                    <!-- Borrowing History Content -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Borrowing History</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody id="history-table">
                                <!-- History will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Library Management System</strong>
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // Navigation handling
        $('#dashboard-link').click(function() {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('#dashboard-content').show();
            $('#search-books-content, #borrowed-books-content, #borrow-history-content').hide();
        });

        $('#search-books-link').click(function() {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('#search-books-content').show();
            $('#dashboard-content, #borrowed-books-content, #borrow-history-content').hide();
            loadBooks();
        });

        $('#borrowed-books-link').click(function() {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('#borrowed-books-content').show();
            $('#dashboard-content, #search-books-content, #borrow-history-content').hide();
            loadBorrowedBooks();
        });

        $('#borrow-history-link').click(function() {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('#borrow-history-content').show();
            $('#dashboard-content, #search-books-content, #borrowed-books-content').hide();
            loadBorrowHistory();
        });

        // Search functionality
        $('#search-button').click(function() {
            loadBooks($('#search-input').val());
        });

        $('#search-input').keypress(function(e) {
            if (e.which === 13) {
                loadBooks($('#search-input').val());
            }
        });

        // Load books function
        function loadBooks(search = '') {
            $.ajax({
                url: 'load_books.php',
                type: 'GET',
                data: { search: search },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '';
                        if (response.data.length > 0) {
                            response.data.forEach(function(book) {
                                html += '<tr>';
                                html += '<td>' + book.title + '</td>';
                                html += '<td>' + book.author + '</td>';
                                html += '<td>' + book.isbn + '</td>';
                                html += '<td>' + book.category + '</td>';
                                html += '<td>' + book.available_quantity + '</td>';

                                if (book.available_quantity > 0) {
                                    html += '<td><button class="btn btn-sm btn-primary borrow-btn" data-id="' + book.id + '"><span class="btn-text">Borrow</span> <span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span></button></td>';
                                } else {
                                    html += '<td><button class="btn btn-sm btn-secondary" disabled>Not Available</button></td>';
                                }

                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="6" class="text-center">No books found</td></tr>';
                        }
                        $('#books-table tbody').html(html);

                        // Attach borrow button event
                        $('.borrow-btn').click(function() {
                            borrowBook($(this).data('id'));
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to load books. Please try again.');
                }
            });
        }

        // Load borrowed books function
        function loadBorrowedBooks() {
            console.log('loadBorrowedBooks called')
            $.ajax({
                url: 'borrowed_books.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '';
                        if (response.data.length > 0) {
                            response.data.forEach(function(item) {
                                html += '<tr>';
                                html += '<td>' + item.title + '</td>';
                                html += '<td>' + item.author + '</td>';
                                html += '<td>' + item.borrow_date + '</td>';
                                html += '<td>' + (item.due_date || 'N/A') + '</td>';

                                let statusClass = '';
                                switch (item.status) {
                                    case 'pending':
                                        statusClass = 'badge-warning';
                                        break;
                                    case 'approved':
                                        statusClass = 'badge-success';
                                        break;
                                    case 'rejected':
                                        statusClass = 'badge-danger';
                                        break;
                                }

                                html += '<td><span class="badge ' + statusClass + '">' + item.status.charAt(0).toUpperCase() + item.status.slice(1) + '</span></td>';
                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="5" class="text-center">No borrowed books</td></tr>';
                        }
                        $('#borrowed-books-table').html(html);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to load borrowed books. Please try again.');
                }
            });
        }

        // Load borrowing history function
        function loadBorrowHistory() {
            $.ajax({
                url: 'borrowing_history.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '';
                        if (response.data.length > 0) {
                            response.data.forEach(function(item) {
                                html += '<tr>';
                                html += '<td>' + item.title + '</td>';
                                html += '<td>' + item.author + '</td>';
                                html += '<td>' + item.borrow_date + '</td>';
                                html += '<td>' + (item.due_date || 'N/A') + '</td>';
                                html += '<td>' + (item.return_date || 'N/A') + '</td>';

                                let statusClass = '';
                                switch (item.status) {
                                    case 'pending':
                                        statusClass = 'badge-warning';
                                        break;
                                    case 'approved':
                                        statusClass = 'badge-success';
                                        break;
                                    case 'rejected':
                                        statusClass = 'badge-danger';
                                        break;
                                    case 'returned':
                                        statusClass = 'badge-info';
                                        break;
                                }

                                html += '<td><span class="badge ' + statusClass + '">' + item.status.charAt(0).toUpperCase() + item.status.slice(1) + '</span></td>';
                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="6" class="text-center">No borrowing history</td></tr>';
                        }
                        $('#history-table').html(html);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to load borrowing history. Please try again.');
                }
            });
        }

        // Borrow book function
        function borrowBook(bookId) {
            const button = $(`.borrow-btn[data-id="${bookId}"]`);

            // Show spinner and change text
            button.find('.btn-text').text('Processing');
            button.find('.spinner').show();
            button.prop('disabled', true);
            $.ajax({
                url: 'borrow_book.php',
                type: 'POST',
                data: { book_id: bookId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        loadBooks($('#search-input').val());
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to process borrowing request. Please try again.');
                }
            });
        }
    });
</script>
</body>
</html>