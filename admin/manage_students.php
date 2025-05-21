<?php
// Include necessary files and start session
global $conn;
require_once '../config/db_connection.php';
require_once '../helper/functions.php';
require_once '../authentication/auth_check_admin.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set header to return JSON
    header('Content-Type: application/json');

    // Add Student
    if (isset($_POST['action']) && $_POST['action'] === 'add_student') {
        $data = [
            'username'   => trim($_POST['username']),
            'password'   => trim($_POST['password']),
            'email'      => trim($_POST['email']),
            'full_name'  => trim($_POST['full_name']),
        ];
        $response = addStudent($conn, $data);
        echo json_encode($response);
        exit;
    }
    // Update Student
    elseif (isset($_POST['action']) && $_POST['action'] === 'edit_student') {
        $data = [
            'student_id' => intval($_POST['student_id']),
            'username'   => trim($_POST['username']),
            'email'      => trim($_POST['email']),
            'full_name'  => trim($_POST['full_name']),
            'password'   => trim($_POST['password']),
        ];
        $response = editStudent($conn, $data);
        echo json_encode($response);
        exit;
    }
    // Delete Student
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete_student') {
        $studentId = intval($_POST['student_id']);
        $response = deleteStudent($conn, $studentId);
        echo json_encode($response);
        exit;
    }
    // Get Student Data
    elseif (isset($_POST['action']) && $_POST['action'] === 'get_student') {
        $studentId = intval($_POST['student_id']);

        try {
            $stmt = $conn->prepare("SELECT id, username, email, full_name FROM users WHERE id = ? AND user_type = 'student'");
            $stmt->execute([$studentId]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $student
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Student not found'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}

// Get all students
$students = getAllStudents($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management | Manage Students</title>
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
                Manage Students
                <small>Add, edit, and delete students</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Students List</h3>
                            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#addStudentModal">
                                <i class="fa fa-plus"></i> Add New Student
                            </button>
                        </div>
                        <div class="box-body">
                            <table id="studentsTable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr id="student-row-<?= $student['id']; ?>">
                                        <td><?= htmlspecialchars($student['id']); ?></td>
                                        <td class="student-username"><?= htmlspecialchars($student['username']); ?></td>
                                        <td class="student-fullname"><?= htmlspecialchars($student['full_name']); ?></td>
                                        <td class="student-email"><?= htmlspecialchars($student['email']); ?></td>
                                        <td>
                                            <button class="btn btn-xs btn-primary edit-btn"
                                                    data-id="<?= $student['id']; ?>">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-xs btn-danger delete-btn"
                                                    data-id="<?= $student['id']; ?>"
                                                    data-name="<?= htmlspecialchars($student['full_name']); ?>">
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

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addStudentForm">
                    <input type="hidden" name="action" value="add_student">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="addStudentModalLabel">Add New Student</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addStudentBtn">
                            <span class="btn-text">Add Student</span>
                            <span class="spinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editStudentForm">
                    <input type="hidden" name="action" value="edit_student">
                    <input type="hidden" id="edit_student_id" name="student_id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="editStudentModalLabel">Edit Student</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_username">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_password">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Leave blank to keep current password">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_full_name">Full Name</label>
                            <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updateStudentBtn">
                            <span class="btn-text">Update Student</span>
                            <span class="spinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Student Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1" role="dialog" aria-labelledby="deleteStudentModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="deleteStudentForm">
                    <input type="hidden" name="action" value="delete_student">
                    <input type="hidden" id="delete_student_id" name="student_id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="deleteStudentModalLabel">Delete Student</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete "<span id="delete_student_name"></span>"?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="deleteStudentBtn">
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
        var table = $('#studentsTable').DataTable({
            "responsive": true,
            "autoWidth": false
        });

        // Handle edit button click
        $(document).on('click', '.edit-btn', function() {
            var $btn = $(this);


            var studentId = $(this).data('id');

            $.ajax({
                type: 'POST',
                url: 'manage_students.php',
                data: {
                    action: 'get_student',
                    student_id: studentId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#edit_student_id').val(response.data.id);
                        $('#edit_username').val(response.data.username);
                        $('#edit_email').val(response.data.email);
                        $('#edit_full_name').val(response.data.full_name);
                        $('#edit_password').val(''); // Clear password field

                        $('#editStudentModal').modal('show');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while fetching student data.');
                },
                complete: function() {
                    $btn.html('<i class="fa fa-edit"></i> Edit').prop('disabled', false);
                }
            });
        });

        // Handle delete button click
        $(document).on('click', '.delete-btn', function() {
            var $btn = $(this);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

            var studentId = $(this).data('id');
            var studentName = $(this).data('name');

            $('#delete_student_id').val(studentId);
            $('#delete_student_name').text(studentName);

            $('#deleteStudentModal').modal('show');
            $btn.html('<i class="fa fa-trash"></i> Delete').prop('disabled', false);
        });

        // Add Student Form AJAX submission
        $('#addStudentForm').on('submit', function(e) {
            e.preventDefault();

            $('#addStudentBtn .btn-text').hide();
            $('#addStudentBtn .spinner').show();
            $('#addStudentBtn').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'manage_students.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#addStudentModal').modal('hide');
                        $('#addStudentForm')[0].reset();
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
                    $('#addStudentBtn .btn-text').show();
                    $('#addStudentBtn .spinner').hide();
                    $('#addStudentBtn').prop('disabled', false);
                }
            });
        });

        // Edit Student Form AJAX submission
        $('#editStudentForm').on('submit', function(e) {
            e.preventDefault();

            $('#updateStudentBtn .btn-text').hide();
            $('#updateStudentBtn .spinner').show();
            $('#updateStudentBtn').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'manage_students.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#editStudentModal').modal('hide');
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
                    $('#updateStudentBtn .btn-text').show();
                    $('#updateStudentBtn .spinner').hide();
                    $('#updateStudentBtn').prop('disabled', false);
                }
            });
        });

        // Delete Student Form AJAX submission
        $('#deleteStudentForm').on('submit', function(e) {
            e.preventDefault();

            $('#deleteStudentBtn .btn-text').hide();
            $('#deleteStudentBtn .spinner').show();
            $('#deleteStudentBtn').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'manage_students.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#deleteStudentModal').modal('hide');
                        location.reload();

                        // Remove the row from the table
                        var studentId = $('#delete_student_id').val();
                        $('#student-row-' + studentId).remove();

                        // If using DataTables, redraw the table
                        table.draw();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while processing your request.');
                },
                complete: function() {
                    $('#deleteStudentBtn .btn-text').show();
                    $('#deleteStudentBtn .spinner').hide();
                    $('#deleteStudentBtn').prop('disabled', false);
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