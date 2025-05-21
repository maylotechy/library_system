<?php
global $conn;
require_once '../config/db_connection.php';
require_once '../helper/functions.php';
require_once '../authentication/auth_check_admin.php';

// Handle request processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_request'])) {
    $requestId = intval($_POST['request_id']);
    $action = $_POST['action'];

    // Use the function to process the request
    $result = processBorrowRequest($requestId, $action, $_SESSION['user_id'], $conn);

    if ($result['success']) {
        $_SESSION['toastr'] = [
            'type' => 'success',
            'message' => $result['message']
        ];
    } else {
        $_SESSION['toastr'] = [
            'type' => 'error',
            'message' => $result['message']
        ];
    }

    header('Location: borrow_request.php');
    exit;
}

// Get all pending requests
$stmt = $conn->prepare("
    SELECT b.id, b.book_id, b.user_id, b.status, b.request_date, b.borrow_date, b.due_date,
           bk.title as book_title, u.full_name as student_name
    FROM borrowings b
    JOIN books bk ON b.book_id = bk.id
    JOIN users u ON b.user_id = u.id
    WHERE b.status = 'pending'
    ORDER BY b.request_date DESC
");
$stmt->execute();
$pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management | Borrow Requests</title>
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
                Borrow Requests
                <small>Manage book borrowing requests</small>
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
                            <h3 class="box-title">Pending Requests</h3>
                        </div>
                        <div class="box-body">
                            <?php if (count($pendingRequests) > 0): ?>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Student</th>
                                        <th>Book</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($pendingRequests as $request): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($request['id']); ?></td>
                                            <td><?= htmlspecialchars($request['student_name']); ?></td>
                                            <td><?= htmlspecialchars($request['book_title']); ?></td>
                                            <td><?= htmlspecialchars($request['request_date']); ?></td>
                                            <td>
                                                <form method="POST" action="borrow_request.php" style="display: inline-block;">
                                                    <input type="hidden" name="request_id" value="<?= $request['id']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" name="process_request" class="btn btn-xs btn-success">
                                                        <i class="fa fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="borrow_request.php" style="display: inline-block;">
                                                    <input type="hidden" name="request_id" value="<?= $request['id']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" name="process_request" class="btn btn-xs btn-danger">
                                                        <i class="fa fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No pending borrow requests found.
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