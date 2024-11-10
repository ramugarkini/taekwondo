<?php

ob_start();
include 'crypto.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
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
    </div>

    <script src="/jquery/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script src="/datatable/jquery.dataTables.min.js"></script>
    <script src="/datatable/dataTable.bootstrap.min.js"></script>

</body>
</html>
<?php ob_end_flush(); ?>
