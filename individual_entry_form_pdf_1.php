<?php
include_once('connection.php');
require_once('tcpdf/tcpdf.php');

// Get the full request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Parse the URI to get the path elements
$uri_segments = explode('/', trim($request_uri, '/'));

// Check if an ID is passed in the URL for editing
if (isset($uri_segments[1])) {
    $id = intval($uri_segments[1]);

    // Fetch data from the database if editing
    $query = $conn->prepare("SELECT * FROM individual_entry_form WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    // If no data is found for the given ID, redirect or show an error
    if (!$row) {
        $_SESSION['error'] = "Record not found!";
        header("Location: /individual_entry_form");
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
// Set font for the title
$pdf->SetFont('helvetica', 'B', 16); // Bold font for the title
// Create the cell with fill color
$pdf->Cell(0, 10, 'INDIVIDUAL ENTRY FORM', 1, 1, 'C', 1); // '1' at the end fills the cell with color
// Reset fill color if needed for subsequent cells
$pdf->SetFillColor(255, 255, 255); // Set fill color back to white for the next cells


// Add a line break
$pdf->Ln(5);

// Set font for the table
$pdf->SetFont('helvetica', 'B', 10);

// Entry form fields
$pdf->Cell(0, 10, 'Mark (X) on the appropriate boxes.', 'LTR', 1, 'C');

$pdf->Cell(35, 10, 'Sub-Junior', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(10, 10, (isset($row['type']) && $row['type'] == 'sub_junior' ? 'X' : ''), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 10, 'Cadet', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(10, 10, (isset($row['type']) && $row['type'] == 'cadet' ? 'X' : ''), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 10, 'Junior', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(10, 10, (isset($row['type']) && $row['type'] == 'junior' ? 'X' : ''), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 10, 'Senior', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(10, 10, (isset($row['type']) && $row['type'] == 'senior' ? 'X' : ''), 1, 0, 'C');
$pdf->Ln(); // Line break

// Category row
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(24, 10, 'Category', 1, 0, 'C');
$pdf->Cell(40, 10, 'Individual', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(12, 10, (isset($row['category']) && $row['category'] == 'Individual' ? 'X' : ''), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 10, 'Pair', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(12, 10, (isset($row['category']) && $row['category'] == 'Pair' ? 'X' : ''), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 10, 'Group', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(12, 10, (isset($row['category']) && $row['category'] == 'Group' ? 'X' : ''), 1, 0, 'C');
$pdf->Ln();

// Gender row
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 10, 'Gender', 1, 0, 'C');
$pdf->Cell(40, 10, 'Male', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(10, 10, (isset($row['gender']) && $row['gender'] == 'Male' ? 'X' : ''), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 10, 'Female', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(11, 10, (isset($row['gender']) && $row['gender'] == 'Female' ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(0, 10, '', 'LTR', 1, 'C');
$pdf->Ln(5);

$y = $pdf->GetY();
// Create a cell that spans 5 rows for the passport photo
if (!empty($row['photo_path']) && file_exists($row['photo_path'])) {
    // Add the image to the PDF
    $pdf->Image($row['photo_path'], 158, 98, 35, 42);
}

$pdf->Cell(0, 30, '', 'R', 1, 'C'); // Empty cell to create space

// Set the position for the photo cell (manually control y position)
//$y_before_photo = $pdf->GetY(); // Save the current Y position
//$pdf->SetY($y_before_photo - 40); // Move Y position up for the photo cell
// $pdf->Cell(0, 80, 'Attach One Passport Size Photo', 1, 1, 'C', 0, '', 1, true, 'C', 'M'); // Centered text with vertical alignment
//$pdf->SetY($y_before_photo); // Reset to original position for next rows
$pdf->SetY($y);

// Name row
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 10, 'Name', 1, 0, 'C');
$pdf->Cell(101, 10, ($row['name'] ?? ''), 1, 1, 'C'); // Full width

// State row
$pdf->Cell(40, 10, 'State', 1, 0, 'C');
$pdf->Cell(101, 10, ($row['state_organization_name'] ?? ''), 1, 1, 'C'); // Full width

// Date of Birth and Age row
$pdf->Cell(40, 10, 'Date of Birth', 1, 0, 'C');
$pdf->Cell(40, 10, ($row['date_of_birth'] ?? ''), 1, 0, 'C');
$pdf->Cell(40, 10, 'Age', 1, 0, 'C');
$pdf->Cell(21, 10, ($row['age'] ?? ''), 1, 1, 'C');

// Parent / Guardian Name row
$pdf->Cell(50, 10, 'Parent / Guardian Name', 1, 0, 'C');
$pdf->Cell(0, 10, ($row['parent_guardian_name'] ?? ''), 1, 1, 'C'); // Full width

// Current Belt Grade and TFI ID row
$pdf->Cell(40, 10, 'Current Belt Grade', 1, 0, 'C');
$pdf->Cell(20, 10, ($row['current_belt_grade'] ?? ''), 1, 0, 'C');
$pdf->Cell(40, 10, 'TFI ID No.', 1, 0, 'C');
$pdf->Cell(20, 10, ($row['tfi_id_no'] ?? ''), 1, 0, 'C');
$pdf->Cell(40, 10, 'Belt Certificate No.', 1, 0, 'C');
$pdf->Cell(0, 10, ($row['belt_certificate_no'] ?? ''), 1, 1, 'C'); // Full width

// Additional note
$pdf->Cell(0, 5, 'Xerox copy of TFI ID Card, Belt Grade Certificate, Birth Certificate should be enclosed compulsorily.', 0, 1, 'C');

// Academic Qualification Table
$pdf->Cell(50, 10, 'Academic Qualification', 1, 0, 'C');
$pdf->Cell(40, 10, ($row['academic_qualification'] ?? ''), 1, 0, 'C');
$pdf->Cell(50, 10, 'Name of School', 1, 0, 'C');
$pdf->Cell(0, 10, ($row['name_of_school'] ?? ''), 1, 1, 'C'); // Full width

$pdf->Cell(50, 10, 'Name of Board/University', 1, 0, 'C');
$pdf->Cell(0, 10, ($row['board_university_name'] ?? ''), 1, 1, 'C'); // Full width

// Declaration
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 10, 'DECLARATION', 'B', 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 10, 'I, the undersigned do hereby solemnly affirm, declare and confirm for myself, my heirs, executors & administrators that I indemnify the Promoters/Organizers/Sponsors & its Members, Officials, Participants etc., holding myself personally responsible for all damages, injuries or accidents, claims, demands etc., waiving all prerogative rights, whatsoever related to the above set forth event.', 'B', 'C', 0);

// Signatures section
// Add some spacing above the signatures section
$pdf->Ln(10);
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 10);

// First row with "Signature of Parent/Guardian" and "Signature of Participant"
if (!empty($row['signature_parent_guardian_path']) && file_exists($row['signature_parent_guardian_path'])) {
    $pdf->Image($row['signature_parent_guardian_path'], 35, 215, 50, 15);
}
$pdf->Cell(90, 10, 'Signature of Parent/Guardian', 'TR', 0, 'C'); // Only top border

if (!empty($row['signature_participant_path']) && file_exists($row['signature_participant_path'])) {
    $pdf->Image($row['signature_participant_path'], 120, 215, 50, 15);
}
$pdf->Cell(0, 10, 'Signature of Participant', 'T', 1, 'C'); // Only top border

// Second row for empty signature lines (with top and bottom borders)
$pdf->Cell(90, 10, '', 'TRB', 0); // Top and bottom border
$pdf->Cell(0, 10, '', 'LTB', 1); // Top and bottom border

// Third row with "Signature of President/Secretary" - centered, top and bottom border
$pdf->Cell(0, 0, 'Signature of President/Secretary', 'T', 1, 'C'); // Top and bottom border
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 0, 'State Association with stamp', 'B', 1, 'C'); // Top and bottom border

if (!empty($row['signature_president_secretary_path']) && file_exists($row['signature_president_secretary_path'])) {
    $pdf->Image($row['signature_president_secretary_path'], 80, 260, 50, 15);
}
if (!empty($row['state_association_stamp_path']) && file_exists($row['state_association_stamp_path'])) {
    $pdf->Image($row['state_association_stamp_path'], 80, 270, 50, 15);
}

// Output the PDF
// $pdf->Output('taekwondo_entry_form.pdf', 'I');
$pdf->Output("Individual Entry Form"." ".$row['name'].'_1.pdf', 'I');

/*$content = '';
$content .= '
<style>
    .header {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 20px;
    }
    .sub-header {
        text-align: center;
        margin-bottom: 10px;
    }
    .blue-header {
        background-color: #e0e0e0;
        text-align: center;
        color: #007bff;
        font-weight: bold;
        padding: 10px;
        margin: 0;
    }


</style>';

$content .= '
<div class="header">2023 NATIONAL OPEN KYORUGI & POOMSAE TAEKWONDO CHAMPIONSHIPS</div>
<div class="sub-header">5th to 8th October 2023<br>
    Noida Indoor Stadium, Sector 21A, Noida, Uttar Pradesh-201301<br>
    Organizer: Uttar Pradesh Taekwondo Association<br>
    Promoter: Taekwondo Federation of India
</div>';

$content .= '<br>';

$pdf->writeHTML($content);*/

/*$content .= '<table border="1" cellpadding="5">';

// Entry form fields

$content .= '<tr><th class="blue-header" colspan="8"><strong>INDIVIDUAL ENTRY FORM</strong></th></tr>';
$content .= '</table><br><br><table border="1" cellpadding="5">';

$content .= '<tr><th colspan="8"><strong>Mark (X) on the appropriate boxes.</strong></th></tr>';

$content .= '<tr>
	<td><strong>Sub-Junior</strong></td>
	<td>' . (isset($row['type']) && $row['type'] == 'sub_junior' ? 'X' : '') . '</td>
	<td><strong>Cadet</strong></td>
	<td>' . (isset($row['type']) && $row['type'] == 'cadet' ? 'X' : '') . '</td>
	<td><strong>Junior</strong></td>
	<td>' . (isset($row['type']) && $row['type'] == 'junior' ? 'X' : '') . '</td>
	<td><strong>Senior</strong></td>
	<td>' . (isset($row['type']) && $row['type'] == 'senior' ? 'X' : '') . '</td>
</tr>';

$content .= '<tr>
	<td colspan="2"><strong>Category</strong></td>
	<td><strong>Individual</strong></td>
	<td>' . (isset($row['category']) && $row['category'] == 'Individual' ? 'X' : '') . '</td>
	<td><strong>Pair</strong></td>
	<td>' . (isset($row['category']) && $row['category'] == 'Pair' ? 'X' : '') . '</td>
	<td><strong>Group</strong></td>
	<td>' . (isset($row['category']) && $row['category'] == 'Group' ? 'X' : '') . '</td>
</tr>';

$content .= '<tr>
	<td colspan="2"><strong>Gender</strong></td>
	<td><strong>Male</strong></td>
	<td>' . (isset($row['gender']) && $row['gender'] == 'Male' ? 'X' : '') . '</td>
	<td><strong>Female</strong></td>
	<td>' . (isset($row['gender']) && $row['gender'] == 'Female' ? 'X' : '') . '</td>
	<td rowspan="5" colspan="2" style="text-align: center; vertical-align: middle;"><br><br><br><br><strong>Attach One Passpport Size Photo</strong></td>
</tr>';

$content .= '<tr><td colspan="6"></td></tr>';

$content .= '<tr>
	<td colspan="2">Name</td>
	<td colspan="4">' . ($row['name'] ?? '') . '</td>
</tr>';

$content .= '<tr>
	<td colspan="2">State</td>
	<td colspan="4">' . ($row['state_organization_name'] ?? '') . '</td>
</tr>';

$content .= '<tr>
	<td colspan="2">Date of Birth</td>
	<td colspan="2">' . ($row['date_of_birth'] ?? '') . '</td>
	<td>Age</td>
	<td>' . ($row['age'] ?? '') . '</td>
</tr>';

$content .= '<tr>
	<td colspan="3">Parent / Guardian Name</td>
	<td colspan="5">' . ($row['parent_guardian_name'] ?? '') . '</td>
</tr>';

$content .= '<tr>
	<td>Current Belt Grade</td>
	<td>' . ($row['current_belt_grade'] ?? '') . '</td>
	<td>TFI ID No.</td>
	<td>' . ($row['tfi_id_no'] ?? '') . '</td>
	<td colspan="2">Belt Certificate No.</td>
	<td colspan="2">' . ($row['belt_certificate_no'] ?? '') . '</td>
</tr>';

$content .= '</table>
<div style="text-align: center;">Xerox copy of TFI ID Card, Belt Grade Certificate, Birth Certificate should be enclosed compulsorily.</div>
<table border="1" cellpadding="5">';

$content .= '<tr>
	<td>Academic Qualification</td>
	<td>' . ($row['academic_qualification'] ?? '') . '</td>
	<td>Name of School</td>
	<td>' . ($row['name_of_school'] ?? '') . '</td>
</tr>';

$content .= '<tr><td>Name of Board/University</td>
	<td colspan="3">' . ($row['board_university_name'] ?? '') . '</td>
</tr>';

$content .= '</table>';*/

/*$content .= '<table border="1" cellpadding="5">';
$content .= '<tr>
	<td style="border-left: none;">Signature of Parent/Guardian</td>
	<td class="no-right-border">Signature of Participant</td>
</tr>';

$content .= '<tr><td></td><td></td></tr>';

$content .= '<tr>
	<td colspan="2" class="no-left-border no-right-border">Signature of President/Secretary<br><strong>State Association with stamp</strong></td>
</tr>';

$content .= '</table>';*/