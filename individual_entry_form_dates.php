<?php
ob_start();
session_start();
include_once('connection.php');

// Get the full request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Parse the URI to get the path elements
$uri_segments = explode('/', trim($request_uri, '/'));

// Check for the Entry Form Dates path
$base_path = 'individual_entry_form_dates';

include 'crypto.php';

$user_id = $_SESSION['user_details']['user_id'];
if ($user_id > 1){
    header('Location: /logout');
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Individual Entry Form Dates</title>
    <link rel="icon" href="/public/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/datatable/dataTable.bootstrap.min.css">
    <style>
        .height10 { height:10px; }
        .mtop10 { margin-top:10px; }
        .modal-label { position:relative; top:7px; }
    </style>
</head>
<body>
    <div id="wrapper">
        <?php include 'menu.php'; ?>

            <?php 
                // Adjust the condition based on your needs
                if (!isset($uri_segments[1])) { 
                // if (count($uri_segments) === 2 && $uri_segments[0] === $base_path && $uri_segments[1] == "__") { 
                    include("individual_entry_form_dates_list.php");
                } elseif (count($uri_segments) === 2 && $uri_segments[0] === $base_path && is_numeric(decrypt($uri_segments[1], $key)) && decrypt($uri_segments[1], $key) >= 0) {
                    include("individual_entry_form_dates_edit.php");
                } else {
                    // Optionally handle cases where the number of segments does not match expected values
                    // header("HTTP/1.0 404 Not Found");
                    // echo "<div class='alert alert-danger'>Invalid request.</div>";
                    // exit;
                    header('Location: /');
                    exit();
                }
            ?>
        </div>
    </div>


    <script src="/jquery/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script src="/datatable/jquery.dataTables.min.js"></script>
    <script src="/datatable/dataTable.bootstrap.min.js"></script>
    <script>
    $(document).ready(function(){
        $('#myTable').DataTable();

        $(document).on('click', '.close', function(){
            $('.alert').hide();
        });
    });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>