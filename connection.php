<?php
	//for MySQLi OOP
	// Get the base URL (you can use $_SERVER['HTTP_HOST'] or a predefined constant)
	$baseUrl = $_SERVER['HTTP_HOST'];

	// Set the database connection based on the base URL
	if ($baseUrl === 'taekwondo.000.pe') {
	    // Remote connection for InfinityFree hosting
	    $conn = new mysqli('sql312.infinityfree.com', 'if0_37618187', '809PccUVH8', 'if0_37618187_taekwondo');
	} else {
	    // Local connection for development
	    $conn = new mysqli('localhost', 'root', 'mysql', 'taekwondo');
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