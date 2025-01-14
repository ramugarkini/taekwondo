<?php
	//for MySQLi OOP
	// Get the base URL (you can use $_SERVER['HTTP_HOST'] or a predefined constant)
	$baseUrl = $_SERVER['HTTP_HOST'];

	// Set the database connection based on the base URL
	if ($baseUrl === 'taekwondo.000.pe') {
	    $conn = new mysqli('hostname', 'username', 'password', 'database');
	} else {
	    // Local connection for development
	    $conn = new mysqli('localhost', 'username', '', 'database');
	}

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	////////////////

	//for MySQLi Procedural
	// $conn = mysqli_connect('localhost', 'root', '', 'mydatabase');
	// if(!$conn){
	//     die("Connection failed: " . mysqli_connect_error());
	// }
	////////////////
?>
