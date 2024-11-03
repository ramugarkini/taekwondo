<?php
session_start();
include_once('connection.php'); // Include database connection
include 'crypto.php'; 

// Check if the request is an AJAX request and is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date_of_birth']) && isset($_POST['name'])) {
    $date_of_birth = $_POST['date_of_birth'];
    $name = $_POST['name'];

    // Prepare a query to check if there's a matching row in the table
    $stmt = $conn->prepare("SELECT id FROM individual_entry_form WHERE date_of_birth = ? AND name = ?");
    $stmt->bind_param("ss", $date_of_birth, $name); // Bind parameters to prevent SQL injection
    $stmt->execute();
    $stmt->store_result();

    // Check if a match was found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id);
        $stmt->fetch();
        // Return the ID as a JSON response
        echo json_encode(['status' => 'success', 'id' => encrypt($id, $key)]);
    } else {
        // No match found
        echo json_encode(['status' => 'error', 'message' => 'No details found.']);
    }

    $stmt->close();
    $conn->close();
    exit(); // End the script after sending the response
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Individual Entry Form</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="datatable/dataTable.bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background color */
        }
        .form-container {
            margin-top: 50px; /* Spacing from the top */
            padding: 30px; /* Padding around the form */
            border-radius: 8px; /* Rounded corners */
            background-color: #ffffff; /* White background for the form */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Subtle shadow effect */
        }
        .form-header {
            margin-bottom: 20px; /* Space below the header */
        }
        .alert {
            display: none; /* Initially hidden */
        }
    </style>
</head>
<body>
<div class="container">
	<div class="col-sm-8 col-sm-offset-2">
	    <div class="form-container">
	        <h1 class="text-center form-header">Individual Entry Form Check</h1>
	        <form id="entryCheckForm"> <!-- Form for checking existing entries -->
	            <div class="form-group">
	                <label for="name">Name:</label>
	                <input type="text" name="name" class="form-control" id="name" placeholder="Enter your name" required>
	            </div>
	            <div class="form-group">
	                <label for="date_of_birth">Date of Birth:</label>
	                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" required>
	            </div>
	            <button type="submit" class="btn btn-primary btn-block">Check Entry</button> <!-- Submit button -->
	        </form>
	        <div class="text-center mt-3">
	            <a href="/individual_entry_form/<?php echo encrypt(0, $key); ?>" class="btn btn-link">New User? Create New Entry</a> 
	        </div>
	        <div id="alertMessage" class="alert alert-danger mt-3"></div> <!-- Message display -->
	    </div>
	</div>
</div>

<script src="jquery/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#entryCheckForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Gather form data
        var formData = {
            name: $('#name').val(),
            date_of_birth: $('#date_of_birth').val()
        };

        // Make AJAX request
        $.ajax({
            type: 'POST',
            url: 'index.php', // Same page to handle the AJAX request
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect to the specific entry page if a match is found
                    window.location.href = '/individual_entry_form/' + response.id;
                } else {
                    // Display error message if no match is found
                    $('#alertMessage').text(response.message).show(); // Show message
                }
            },
            error: function() {
                $('#alertMessage').text('An error occurred while checking the entry.').show(); // Show error message
            }
        });
    });
});
</script>
</body>
</html>
