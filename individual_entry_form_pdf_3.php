<?php
include_once('connection.php');
include_once('crypto.php');

// Get the full request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Parse the URI to get the path elements
$uri_segments = explode('/', trim($request_uri, '/'));

// Check if an ID is passed in the URL
if (isset($uri_segments[1]) && intval(decrypt($uri_segments[1], $key)) > 0) {
    $id = intval(decrypt($uri_segments[1], $key));

    // Fetch data from the database based on the ID
    $query = $conn->prepare("SELECT name FROM individual_entry_form WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    // If no data is found for the given ID, redirect or show an error
    if (!$row && $id != 0) {
        $_SESSION['error'] = "Record not found!";
        header("Location: /");
        exit();
    }
}

// Define the file path
$file_path = 'uploads/pdf/IndividualEntryForm3.pdf';

// Check if the file exists
if (file_exists($file_path)) {
    // Set a custom filename for inline display
    $custom_filename = "Individual Entry Form " . $row['name'] . " 3.pdf";

    // Set headers to display PDF inline in the browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $custom_filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');

    // Output the PDF file directly to the browser
    readfile($file_path);
    exit;
} else {
    echo "The file does not exist.";
}
?>
