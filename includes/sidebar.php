<?php
// Get the current page name
$activePage = basename($_SERVER['PHP_SELF']);
?>

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
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- sidebar menu: style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li class="<?= $activePage == 'admin_dashboard.php' ? 'active' : '' ?>">
                <a href="admin_dashboard.php">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= $activePage == 'manage_books.php' ? 'active' : '' ?>">
                <a href="manage_books.php">
                    <i class="fa fa-book"></i> <span>Manage Books</span>
                </a>
            </li>
            <li class="<?= $activePage == 'borrow_request.php' ? 'active' : '' ?>">
                <a href="borrow_request.php">
                    <i class="fa fa-list"></i> <span>Borrow Requests</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-yellow pending-count">0</small>
                    </span>
                </a>
            </li>
            <li class="<?= $activePage == 'borrowed_books.php' ? 'active' : '' ?>">
                <a href="borrowed_books.php">
                    <i class="fa fa-check-circle"></i> <span>Borrowed Books</span>
                </a>
            </li>
            <li class="<?= $activePage == 'returned_books.php' ? 'active' : '' ?>">
                <a href="returned_books.php">
                    <i class="fa fa-history"></i> <span>Return History</span>
                </a>
            </li>
            <li class="<?= $activePage == 'manage_students.php' ? 'active' : '' ?>">
                <a href="manage_students.php">
                    <i class="fa fa-users"></i> <span>Manage Students</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>


