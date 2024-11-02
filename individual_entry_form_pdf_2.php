<?php
include_once('connection.php');
require_once('tcpdf/tcpdf.php');

// Get the full request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Parse the URI to get the path elements
$uri_segments = explode('/', trim($request_uri, '/'));

// Check if an ID is passed in the URL for editing
if (isset($uri_segments[1]) && intval($uri_segments[1]) > 0) {
    $id = intval($uri_segments[1]);

    // Fetch data from the database if editing
    $query = $conn->prepare("SELECT * FROM individual_entry_form WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    // If no data is found for the given ID, redirect or show an error
    if (!$row && $id!=0) {
        $_SESSION['error'] = "Record not found!";
        header("Location: /");
        exit();
    }
}

// Create new PDF document
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
$pdf->SetCreator(PDF_CREATOR);  
$pdf->SetTitle("Individual Entry Form"." - ".$row['name']);  
$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
$pdf->SetDefaultMonospacedFont('helvetica');  
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
$pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
$pdf->setPrintHeader(false);  
$pdf->setPrintFooter(false);  
$pdf->SetAutoPageBreak(TRUE, 10);  
$pdf->SetFont('helvetica', '', 11);  
$pdf->AddPage();  

// Generate PDF content
// Set font for header
$pdf->SetFont('helvetica', 'B', 16); // Bold font for header
$pdf->Cell(0, 5, '2023 NATIONAL OPEN KYORUGI & POOMSAE', 0, 1, 'C');
$pdf->Cell(0, 5, 'TAEKWONDO CHAMPIONSHIPS', 0, 1, 'C'); // Centered header
$pdf->Ln(1);

// Set font for sub-header
$pdf->SetFont('helvetica', '', 12); // Regular font for sub-header
$pdf->Cell(0, 5, '5th to 8th October 2023', 0, 1, 'C'); // Centered sub-header
$pdf->Cell(0, 5, 'Noida Indoor Stadium, Sector 21A, Noida, Uttar Pradesh-201301', 'B', 1, 'C'); // Centered address
$pdf->Cell(0, 5, 'Organizer: Uttar Pradesh Taekwondo Association', 'T', 1, 'C'); // Centered organizer
$pdf->Cell(0, 5, 'Promoter: Taekwondo Federation of India', 0, 1, 'C'); // Centered promoter

// Add a line break for spacing
$pdf->Ln(5);

// Set font for the header
// Set the fill color to #f2dede (light red)
$pdf->SetFillColor(242, 222, 222); // RGB values
$pdf->SetTextColor(0, 123, 255);
// Set font for the title
$pdf->SetFont('helvetica', 'B', 16); // Bold font for the title
// Create the cell with fill color
$pdf->Cell(0, 10, 'INDIVIDUAL ENTRY FORM', 1, 1, 'C', 1); // '1' at the end fills the cell with color
// Reset fill color if needed for subsequent cells
$pdf->SetFillColor(255, 255, 255); // Set fill color back to white for the next cells
$pdf->SetTextColor(0, 0, 0);


// Add a line break
$pdf->Ln(5);

// Set font for the table

// Entry form fields
$pdf->SetFont('helvetica', '', 10);
$y = $pdf->GetY();
$pdf->Cell(10, 5, (isset($row['type']) && $row['type'] == 'sub_junior' ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(40, 5, 'Sub-Junior', 1, 0, 'C');
$pdf->Cell(40, 10, 'Male', 1, 0, 'C');
$pdf->Cell(10, 10, (isset($row['gender']) && $row['gender'] == 'Male' ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(40, 10, 'Weight', 1, 0, 'C');
$pdf->Cell(40, 10, 'Weight Category', 1, 0, 'C');
$pdf->Ln();

$pdf->SetY($y + 5);
$pdf->Cell(10, 5, (isset($row['type']) && $row['type'] == 'cadet' ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(40, 5, 'Cadet', 1, 0, 'C');
$pdf->Ln();

$y = $pdf->GetY();
$pdf->Cell(10, 5, (isset($row['type']) && $row['type'] == 'junior' ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(40, 5, 'Junior', 1, 0, 'C');
$pdf->Cell(40, 10, 'Female', 1, 0, 'C');
$pdf->Cell(10, 10, (isset($row['gender']) && $row['gender'] == 'Female' ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(40, 10, ($row['weight'] ?? ''), 1, 0, 'C');
$pdf->Cell(40, 10, ($row['weight_category'] ?? ''), 1, 0, 'C');
$pdf->Ln();

$pdf->SetY($y + 5);
$pdf->Cell(10, 5, (isset($row['type']) && $row['type'] == 'senior' ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(40, 5, 'Senior', 1, 0, 'C');
$pdf->Ln(); // Line break

// State row
$pdf->Cell(50, 10, 'State / Organization Name', 1, 0, 'C');
$pdf->Cell(0, 10, ($row['state_organization_name'] ?? ''), 1, 1, 'C');
$pdf->Ln(5);

// Name row
$y = $pdf->GetY();
$pdf->Cell(40, 6, 'NAME', 'LTR', 0, 'C');
$pdf->Cell(103, 12, ($row['name'] ?? ''), 1, 0, 'C');
$pdf->MultiCell(0, 36, 'PASTE ONE PASSPORT SIZE PHOTOGRAPH', 'LTR', 'C');
if (!empty($row['photo_path']) && file_exists($row['photo_path'])) {
    // Add the image to the PDF
    $pdf->Image($row['photo_path'], 159, 102, 35, 35);
}

// $pdf->Cell(0, 50, '', 'LR', 0, 'C');
$pdf->SetY($y + 6);
$pdf->Cell(40, 6, '(IN CAPITAL LETTERS)', 'LRB', 1, 'C');

// Date of Birth and Age row
$pdf->Cell(40, 12, 'Date of Birth', 1, 0, 'C');
$pdf->Cell(30, 12, (date("d-m-Y", strtotime($row['date_of_birth'])) ?? ''), 1, 0, 'C');
$pdf->Cell(40, 12, 'Age', 1, 0, 'C');
$pdf->Cell(33, 12, ($row['age'] ?? ''), 1, 1, 'C');

// Parent / Guardian Name row
$pdf->Cell(40, 12, 'Parent / Guardian Name', 1, 0, 'C');
$pdf->Cell(103, 12, ($row['parent_guardian_name'] ?? ''), 1, 1, 'C');

$pdf->MultiCell(0, 10, 'COPY OF CORPORATION / MUNICIPAL BIRTH CERTIFICATE, TFI ID CARD, COLOUR BELT CERTIFICATE & KUKKIWON CERTIFICATE SHOULD BE ENCLOSED COMPULSORILY AND ORIGINALS SHOULD BE SHOWN AT THE TIME OF WEIGH IN.', 'TB', 'C', 0);

// Current Belt Grade and TFI ID row
$pdf->Cell(50, 10, 'Present Belt Grade', 1, 0, 'C');
$pdf->Cell(40, 10, ($row['current_belt_grade'] ?? ''), 1, 0, 'C');
$pdf->Cell(50, 10, 'TFI ID Card No.', 1, 0, 'C');
$pdf->Cell(40, 10, ($row['tfi_id_no'] ?? ''), 1, 1, 'C');

// Academic Qualification Table
$pdf->Cell(50, 10, 'Academic Qualification', 1, 0, 'C');
$pdf->Cell(40, 10, ($row['academic_qualification'] ?? ''), 1, 0, 'C');
$pdf->Cell(50, 10, 'Name of the School', 1, 0, 'C');
$pdf->Cell(40, 10, ($row['name_of_school'] ?? ''), 1, 1, 'C');

$pdf->Cell(50, 10, 'Name of Board/University', 1, 0, 'C');
$pdf->Cell(0, 10, ($row['board_university_name'] ?? ''), 1, 1, 'C');

// Declaration

$pdf->Cell(0, 10, 'DECLARATION', 'B', 1, 'C');
$pdf->MultiCell(0, 10, 'I, the undersigned do hereby solemnly affirm, declare and confirm for myself, my heirs, executors & administrators that I indemnify the Promoters/Organizers/Sponsors & its Members, Officials, Participants etc., holding myself personally responsible for all damages, injuries or accidents, claims, demands etc., waiving all prerogative rights, whatsoever related to the above set forth event.', 'B', 'C', 0);

// Signatures section
// Second row for empty signature lines (with top and bottom borders)
$pdf->Cell(90, 16, '', 'TRB', 0); // Top and bottom border
$pdf->Cell(0, 16, '', 'LTB', 1); // Top and bottom border

// First row with "Signature of Parent/Guardian" and "Signature of Participant"
if (!empty($row['signature_parent_guardian_path']) && file_exists($row['signature_parent_guardian_path'])) {
    $pdf->Image($row['signature_parent_guardian_path'], 35, 210, 50, 15);
}
$pdf->Cell(90, 10, 'Signature of Parent/Guardian', 'TRB', 0, 'C'); // Only top border

if (!empty($row['signature_participant_path']) && file_exists($row['signature_participant_path'])) {
    $pdf->Image($row['signature_participant_path'], 120, 210, 50, 15);
}
$pdf->Cell(0, 10, 'Signature of Participant', 'TB', 1, 'C'); // Only top border
$pdf->Ln(10);
$pdf->Ln(10);

if (!empty($row['signature_president_secretary_path']) && file_exists($row['signature_president_secretary_path'])) {
    $pdf->Image($row['signature_president_secretary_path'], 80, 240, 50, 15);
}

$pdf->Cell(0, 0, 'Signature of President / Secretary – Affiliated State Taekwondo Association', 'TB', 1, 'C');
$pdf->Ln(5);

$pdf->Cell(0, 5, 'FOR CHAMPIONSHIP USE', 'TB', 1, 'C');
$pdf->Cell(30, 10, 'WEIGHT', 1, 0, 'C');
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell(30, 5, '1ST', 1, 0, 'C');
$pdf->Cell(30, 5, '2ND', 1, 0, 'C');
$pdf->Cell(30, 5, '3RD', 1, 0, 'C');
$pdf->Cell(0, 5, 'REMARKS', 1, 1, 'C');
$pdf->SetY($y + 5);
$pdf->SetX($x);
$pdf->Cell(30, 5, '', 1, 0);
$pdf->Cell(30, 5, '', 1, 0);
$pdf->Cell(30, 5, '', 1, 0);
$pdf->Cell(0, 5, '', 1, 1);
$pdf->Ln(5);

// Output the PDF
$pdf->Output("Individual Entry Form"." ".$row['name'].' 2.pdf', 'I');
