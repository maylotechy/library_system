<?php
require_once '../authentication/auth_check_admin.php'

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Library Management | Admin Dashboard</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/skins/_all-skins.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.google../apis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="admin_dashboard.php" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>L</b>MS</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Library</b>Management</span>
        </a>
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="../img/admin.png" class="user-image" alt="User Image">
                            <span class="hidden-xs">Admin User</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="../img/admin.png" class="img-circle" alt="User Image">
                                <p>
                                    Admin User
                                    <small>Library Administrator</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="../logout.php" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="/../api/placeholder/160/160" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>Admin User</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>
                <li class="active">
                    <a href="admin_dashboard.php">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="manage_books.php">
                        <i class="fa fa-book"></i> <span>Manage Books</span>
                    </a>
                </li>
                <li>
                    <a href="borrow_request.php">
                        <i class="fa fa-list"></i> <span>Borrow Requests</span>
                        <span class="pull-right-container">
              <small class="label pull-right bg-yellow pending-count">0</small>
            </span>
                    </a>
                </li>
                <li>
                    <a href="borrowed_books.php">
                        <i class="fa fa-check-circle"></i> <span>Borrowed Books</span>
                    </a>
                </li>
                <li>
                    <a href="returned_books.php">
                        <i class="fa fa-history"></i> <span>Return History</span>
                    </a>
                </li>
                <li>
                    <a href="manage_students.php">
                        <i class="fa fa-users"></i> <span>Manage Students</span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboard
                <small>Control panel</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 id="total-books">0</h3>
                            <p>Total Books</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-book"></i>
                        </div>
                        <a href="manage_books.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3 id="available-books">0</h3>
                            <p>Available Books</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check"></i>
                        </div>
                        <a href="manage_books.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="pending-requests">0</h3>
                            <p>Pending Requests</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-list"></i>
                        </div>
                        <a href="borrow_requests.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3 id="borrowed-books">0</h3>
                            <p>Borrowed Books</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bookmark"></i>
                        </div>
                        <a href="borrowed_books.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-7 connectedSortable">
                    <!-- Recent borrow requests -->
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Recent Borrow Requests</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Student</th>
                                        <th>Book</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody id="recent-requests">
                                    <!-- Data will be loaded here via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <a href="borrow_requests.php" class="btn btn-sm btn-info btn-flat pull-right">View All Requests</a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.Left col -->

                <!-- right col (We are only adding the ID to make the widgets sortable)-->
                <section class="col-lg-5 connectedSortable">
                    <!-- Recently returned -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Recently Returned Books</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <ul class="products-list product-list-in-box" id="recent-returns">
                                <!-- Data will be loaded here via AJAX -->
                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-center">
                            <a href="returned_books.php" class="uppercase">View All Returned Books</a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- right col -->
            </div>
            <!-- /.row (main row) -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; 2025 <a href="#">Library Management System</a>.</strong> All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/js/adminlte.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
        $(document).ready(function() {
        // Configure toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 5000
        };

        // Load dashboard data
        loadDashboardData();

        // Load recent borrow requests
        loadRecentRequests();

        // Load recent returns
        loadRecentReturns();

        // Refresh data every 30 seconds
        setInterval(function() {
        loadDashboardData();
        loadRecentRequests();
        loadRecentReturns();
    }, 30000);

        // Handle approve/reject buttons
        $(document).on('click', '.approve-btn', function() {
        var requestId = $(this).data('id');
        approveRequest(requestId);
    });

        $(document).on('click', '.reject-btn', function() {
        var requestId = $(this).data('id');
        rejectRequest(requestId);
    });
    });

        function loadDashboardData() {
        $.ajax({
            url: '../api/get_dashboard_data.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#total-books').text(data.totalBooks || 0);
                    $('#available-books').text(data.availableBooks || 0);
                    $('#pending-requests').text(data.pendingRequests || 0);
                    $('#borrowed-books').text(data.borrowedBooks || 0);
                    $('.pending-count').text(data.pendingRequests || 0);
                } else {
                    toastr.error('Error loading dashboard data');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Error loading dashboard data: ' + error);
            }
        });
    }

        function loadRecentRequests() {
        $.ajax({
            url: '../api/get_recent_request.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data.success && data.data.length > 0) {
                    $.each(data.data, function(i, request) {
                        var statusClass = '';
                        switch(request.status.toLowerCase()) {
                            case 'pending':
                                statusClass = 'label-warning';
                                break;
                            case 'approved':
                                statusClass = 'label-success';
                                break;
                            case 'rejected':
                                statusClass = 'label-danger';
                                break;
                            case 'returned':
                                statusClass = 'label-info';
                                break;
                            default:
                                statusClass = 'label-default';
                        }

                        html += '<tr>' +
                            '<td>' + request.id + '</td>' +
                            '<td>' + request.student_name + '</td>' +
                            '<td>' + request.book_title + '</td>' +
                            '<td>' + (request.request_date || 'N/A') + '</td>' +
                            '<td><span class="label ' + statusClass + '">' + request.status + '</span></td>' +
                            '<td>';
                        html += '</td></tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center">No recent requests found</td></tr>';
                }
                $('#recent-requests').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error('Error loading recent requests: ' + error);
            }
        });
    }

        function loadRecentReturns() {
        $.ajax({
            url: '../api/get_recent_returns.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data.success && data.data.length > 0) {
                    $.each(data.data, function(i, item) {
                        html += '<li class="item">' +
                            '<div class="product-img">' +
                            '<img src="../img/book.png" alt="Book Image">' +
                            '</div>' +
                            '<div class="product-info">' +
                            '<a href="javascript:void(0)" class="product-title">' + item.book_title +
                            '<span class="label label-success pull-right">Returned</span></a>' +
                            '<span class="product-description">' +
                            'Returned by: ' + item.student_name + ' on ' + item.return_date +
                            '</span>' +
                            '</div>' +
                            '</li>';
                    });
                } else {
                    html = '<li class="item text-center">No recent returns found</li>';
                }
                $('#recent-returns').html(html);
            },
            error: function(xhr, status, error) {
                toastr.error('Error loading recent returns: ' + error);
            }
        });
    }



</script>
</body>
</html>