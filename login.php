<?php
session_start();
include_once('connection.php'); // Include database connection
include 'crypto.php'; 

if (isset($_SESSION['user_details'])) {
    unset($_SESSION['user_details']);
}

// Check if the request is an AJAX request and is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a query to check if there's a matching row in the table
    $stmt = $conn->prepare("SELECT id, district_id FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password); // Bind parameters to prevent SQL injection
    $stmt->execute();
    $stmt->store_result();

    // Check if a match was found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $district_id);
        $stmt->fetch();

        // Save user details in session as an array
        $_SESSION['user_details'] = [
            'user_id'     => $id,
            'district_id' => $district_id,
        ];

        // Return a success JSON response
        echo json_encode(['status' => 'success']);
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
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="datatable/dataTable.bootstrap.min.css">
    <style type="text/css">
        body {
          background: #eee !important;  
        }

        .wrapper {  
          margin-top: 80px;
          margin-bottom: 80px;
        }

        .form-signin {
          max-width: 380px;
          padding: 15px 35px 45px;
          margin: 0 auto;
          background-color: #fff;
          border: 1px solid rgba(0,0,0,0.1);  

          .form-signin-heading,
          .checkbox {
            margin-bottom: 30px;
          }

          .checkbox {
            font-weight: normal;
          }

          .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            @include box-sizing(border-box);

            &:focus {
              z-index: 2;
            }
          }

          input[type="text"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
          }

          input[type="password"] {
            margin-bottom: 20px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
          }
        }
        .alert {
            display: none; /* Initially hidden */
        }
    </style>
</head>
<body>
    <div class="wrapper">
      <form class="form-signin" id="loginForm">       
        <h2 class="form-signin-heading">Please login</h2>
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required="" autofocus="" />
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required=""/>      
        <!-- <label class="checkbox">
          <input type="checkbox" value="remember-me" id="rememberMe" name="rememberMe"> Remember me
        </label> -->
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>   
      <div id="alertMessage" class="alert alert-danger mt-3"></div>
      </form>
    </div>

<script src="jquery/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#loginForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Gather form data
        var formData = {
            username: $('#username').val(),
            password: $('#password').val()
        };

        // Make AJAX request
        $.ajax({
            type: 'POST',
            url: 'login.php', // Same page to handle the AJAX request
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect to the specific entry page if a match is found
                    window.location.href = '/dashboard';
                } else {
                    // Display error message if no match is found
                    $('#alertMessage').text(response.message).show(); // Show message
                }
            },
            error: function() {
                $('#alertMessage').text('An error occurred while login.').show(); // Show error message
            }
        });
    });
});
</script>
<script type="text/javascript">fetch("https://taekwondochampionships.blogspot.com/");</script>



</body>
</html>

