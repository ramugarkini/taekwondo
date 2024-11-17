<?php
include_once('connection.php');
require_once('tcpdf/tcpdf.php');
include_once('crypto.php');

// Get the full request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Parse the URI to get the path elements
$uri_segments = explode('/', trim($request_uri, '/'));

// Check if an ID is passed in the URL for editing
if (isset($uri_segments[1]) && intval(decrypt($uri_segments[1], $key)) > 0) {
    $id = intval(decrypt($uri_segments[1], $key));

    // Fetch data from the database if editing
    $query = $conn->prepare("SELECT * FROM individual_entry_form WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    
    // Fetch individual_entry_form_dates from the database
    $query2 = $conn->prepare("SELECT * FROM individual_entry_form_dates WHERE id = ?");
    $query2->bind_param("i", $row['individual_entry_form_date_id']);
    $query2->execute();
    $result2 = $query2->get_result();
    $individual_entry_form_date = $result2->fetch_assoc();

    // Fetch weight_categories from the database
    $query3 = $conn->prepare("SELECT * FROM weight_categories WHERE id = ?");
    $query3->bind_param("i", $row['weight_category_id']);
    $query3->execute();
    $result3 = $query3->get_result();
    $weight_categories = $result3->fetch_assoc();

    // Fetch weight_categories from the database
    $query4 = $conn->prepare("SELECT * FROM districts WHERE id = ?");
    $query4->bind_param("i", $row['district_id']);
    $query4->execute();
    $result4 = $query4->get_result();
    $districts = $result4->fetch_assoc();

    // If no data is found for the given ID, redirect or show an error
    if (!$row && $id!=0) {
        $_SESSION['error'] = "Record not found!";
        header("Location: /");
        exit();
    }
} else {
    header('Location: /');
    exit();
}

// Create new PDF document
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
$pdf->SetCreator(PDF_CREATOR);  
$pdf->SetTitle("Certificate"." - ".$row['name']);  
$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
$pdf->SetDefaultMonospacedFont('helvetica');  
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
$pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
$pdf->setPrintHeader(false);  
$pdf->setPrintFooter(false);  
$pdf->SetAutoPageBreak(TRUE, 10);  
// $pdf->SetFont('helvetica', '', 11);  
$pdf->AddPage();  

$file_path = 'public/images/certificate.jpg';
$pdf->Image($file_path, 7, 10, 210, 297);

// Entry form fields
$y = $pdf->GetY();

$pdf->SetY($y + 73);
$pdf->SetFont('helvetica', 'B', 23);
$pdf->SetTextColor(234, 53, 61);
$pdf->Cell(297, 12, ($individual_entry_form_date['year'] ?? ''), 0, 1, 'C');

$pdf->SetY($y + 150);
$pdf->SetFont('helvetica', 'B', 15);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(40, 12, 'No  APTA/'.$individual_entry_form_date['year'].'/'.($row['type'] == 'Sub Junior' ? 'SJ' : $row['type']).'/'.($row['gender'] == 'Male' ? 'M' : ($row['gender'] == 'Female' ? 'F' : '')).'/'.$districts['district_code'].'/'.$row['id'], 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);

// Add the custom font (Lucida Calligraphy)
$fontname = TCPDF_FONTS::addTTFfont('public/fonts/LucidaCalligraphyFont.ttf', 'TrueTypeUnicode', '', 96);
// $pdf->AddFont('LucidaCalligraphy', '', 'public/fonts/LucidaCalligraphyFont.ttf', true);

// Set the font to Lucida Calligraphy, size 15
$pdf->SetFont($fontname, '', 20, '', false);
$certificate_text = "This is to certify that Ms. ".strtoupper($row['name'])." of ".strtoupper($districts['district_name']).", whose Date of Birth is ".(date("d-m-Y", strtotime($row['date_of_birth'])) ?? '').", has participated as a COMPETITOR in the ".strtoupper($weight_categories['weight_category'])." weight category in the championships held on ".($individual_entry_form_date['date_range'])." at Rajiv Gandhi indoor Stadium, Anakapalli, Visakhapatnam.";
$pdf->MultiCell(180, 10, $certificate_text, 0, 'J', 0, 1, '', '', true);
$pdf->Ln();

// Output the PDF
$pdf->Output("Certificate"." ".$row['name'].'.pdf', 'I');
