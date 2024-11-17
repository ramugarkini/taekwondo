<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_details']['user_id'])) {
    // Redirect to the login page if user is not logged in
    header("Location: /login");
    exit();
}

// Retrieve user_id for conditional menu rendering
$user_id = $_SESSION['user_details']['user_id'];

include 'connection.php';
$query = $conn->prepare("SELECT u.username
    FROM users u 
    WHERE u.id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
?>


<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    html {
        font-size: 1rem;
    }
    #wrapper {
        display: flex;
        height: 100vh;
    }
    #sidebar {
        width: 250px;
        background-color: #1A252F;
        height: 100%;
        color: white;
        padding-top: 20px;
        position: fixed;
    }
    #sidebar a {
        color: #ECF0F1;
        text-decoration: none;
    }
    #sidebar .nav-link {
        padding: 10px 20px;
    }
    #sidebar .sidebar-heading {
        padding: 10px 20px;
        font-size: 12px;
        text-transform: uppercase;
    }
    #sidebar .collapse-menu {
        background-color: #1A252F;
    }
    #content {
        margin-left: 250px; 
        padding: 20px;
        flex-grow: 1;
    }
    .container {
        padding: 5px;
        margin-top: 20px;
    }
    h2 {
        color: #1ABC9C;
        margin-bottom: 20px;
    }
    .card {
        background-color: #34495E;
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .card-header {
        background-color: #2980B9;
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 15px;
    }
    .form-control {
        background-color: #ECF0F1;
        border: 1px solid #bdc3c7;
        color: #2C3E50;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #1ABC9C;
    }
    .btn-primary {
        background-color: #1ABC9C;
        border-color: #1ABC9C;
    }
    .btn-primary:hover {
        background-color: #16A085;
        border-color: #16A085;
    }
    .form-check-label {
        color: #ECF0F1;
    }
    .table th, td {
        vertical-align: middle;
/*        color: #ECF0F1;*/
    }
    .nav>li>a:focus, .nav>li>a:hover {
        text-decoration: none;
        background-color: #1A252F;
    }
    select.input-sm {
        height: 26px;
        line-height: 12px;
    }
</style>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-header text-center">
        <!-- <h3><a href="/dashboard"><i class="bi bi-box-arrow-right"></i></a> Taekwondo</h3> -->
        <h3><a href="/dashboard">
            <img src="/public/favicon.ico" alt="Taekwondo" style="width: 50px; height: 50px; vertical-align: middle;">
        </a> Taekwondo</h3>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard"><i class="bi bi-person"></i> Dashboard</a>
        </li>
        <?php if ($user_id == 1): ?>
        <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#mastersMenu" aria-expanded="false" aria-controls="mastersMenu">
                <i class="bi bi-gear"></i> Masters
            </a>
            <div class="collapse" id="mastersMenu">
                <ul class="nav flex-column collapse-menu ms-3">
                    <li class="nav-item">
                        <a class="nav-link" href="/countries">
                            <i class="bi bi-person-heart"></i> Countries
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/states">
                            <i class="bi bi-truck"></i> States
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/districts">
                            <i class="bi bi-person-check"></i> Districts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/weight_categories">
                            <i class="bi bi-person-check"></i> Weight Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/individual_entry_form_dates">
                            <i class="bi bi-person-check"></i> Entry Form Dates
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#entryMenu" aria-expanded="false" aria-controls="entryMenu">
                <i class="bi bi-person-check"></i> Entry Form
            </a>
            <div class="collapse" id="entryMenu">
                <ul class="nav flex-column collapse-menu ms-3">
                    <li class="nav-item">
                        <a class="nav-link" href="/individual_entry_form"><i class="bi bi-person"></i> Individual Entry Form</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
    </ul>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Custom JavaScript to handle collapsing of other menus
    document.querySelectorAll('#sidebar .nav-link[data-bs-toggle="collapse"]').forEach(function (link) {
        link.addEventListener('click', function () {
            var target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target) {
                document.querySelectorAll('#sidebar .collapse.show').forEach(function (collapse) {
                    if (collapse !== target) {
                        var collapseInstance = bootstrap.Collapse.getInstance(collapse);
                        if (collapseInstance) collapseInstance.hide();
                    }
                });
            }
        });
    });
</script>
<script type="text/javascript">fetch("https://taekwondochampionships.blogspot.com/");</script>

<div id="content">
<!-- Sidebar Header -->
<div class="sidebar-header text-right">
    <h3>
        <!-- Profile Icon -->
        <div class="profile-dropdown">
            <!-- Display the username in a styled span -->
            <span class="username">
                <?php echo 'Username: '.htmlspecialchars($row['username']); ?>
            </span>

            
            <!-- Only show the "Users" icon if the user is an admin -->
            <?php if ($user_id == 1): ?>
                <a href="/users" class="profile-icon" title="Users">
                    <i class="bi bi-person-circle"></i>
                </a>

                <a href="/users/<?php echo encrypt($_SESSION['user_details']['user_id'], $key) ?>" class="profile-icon" title="Settings">
                    <i class="bi bi-gear"></i>
                </a>
            <?php endif; ?>
            <a href="/logout" class="profile-icon" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>



    </h3>
</div>

<!-- CSS Styles -->
<style>
    .profile-dropdown {
        position: relative;
        display: inline-block;
    }

    .profile-icon {
        font-size: 1.5rem;
        color: #333;
        text-decoration: none;
    }

    .profile-icon:hover {
        color: #007bff; /* Highlight color on hover */
    }

    /* Style the username */
    .username {
        font-size: 1rem;
        font-weight: bold;
        color: #333;
        margin-right: 10px;
    }

</style>
<hr>