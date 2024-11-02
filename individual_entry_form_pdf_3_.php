<?php
require_once('tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle("2023 National Open Kyorugi & Poomsae Taekwondo Championships");
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Title Section
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, '2023 NATIONAL OPEN KYORUGI & POOMSAE TAEKWONDO CHAMPIONSHIPS', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, '5th to 8th October 2023', 0, 1, 'C');
$pdf->Cell(0, 8, 'Noida Indoor Stadium, Sector 21A, Noida, Uttar Pradesh-201301', 0, 1, 'C');
$pdf->Cell(0, 8, 'Organizer : Uttar Pradesh TAEKWONDO ASSOCIATION', 0, 1, 'C');
$pdf->Cell(0, 8, 'Promoter : TAEKWONDO FEDERATION OF INDIA', 0, 1, 'C');
$pdf->Cell(0, 8, 'will be done as per WTF Rules.', 0, 1, 'C');
$pdf->Ln(5);

// Awards Section
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, '12. Awards', 0, 1);
$pdf->Cell(20);
$pdf->Cell(0, 6, 'I   Place : Gold Medal', 0, 1);
$pdf->Cell(20);
$pdf->Cell(0, 6, 'II  Place : Silver Medal', 0, 1);
$pdf->Cell(20);
$pdf->Cell(0, 6, 'III Place : Two Bronze Medals', 0, 1);
$pdf->Ln(5);

// Medical Control Section
$pdf->Cell(0, 6, '13. Medical Control', 0, 1);
$pdf->Cell(20);
$pdf->MultiCell(0, 6, "(A) Use Of Drugs Or Doping By Any Chemical Substances Prescribed In The WT Regulations For Doping Control Are Strongly Prohibited.", 0, 'L');
$pdf->Ln(5);

// Table Header for Sub-Junior Division
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(90, 7, 'Wt. Category', 1, 0, 'C');
$pdf->Cell(90, 7, 'Sub-Junior Division', 1, 1, 'C');

// Sub-Junior Boys and Girls Header Row
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(45, 7, 'Sub-Junior Boys', 1, 0, 'C');
$pdf->Cell(45, 7, 'Weight Category', 1, 0, 'C');
$pdf->Cell(45, 7, 'Sub-Junior Girls', 1, 0, 'C');
$pdf->Cell(45, 7, 'Weight Category', 1, 1, 'C');

// Sub-Junior Boys and Girls Weight Categories
$subJuniorRows = [
    ['Under 16 Kg', 'Not exceeding 16 Kg', 'Under 14 Kg', 'Not exceeding 14 Kg'],
    ['Under 18 Kg', 'Over 16 Kgs & Not Exceeding 18 Kgs', 'Under 16 Kg', 'Over 14 Kgs & Not Exceeding 16 Kgs'],
    ['Under 21 Kg', 'Over 18 Kgs & Not Exceeding 21 Kgs', 'Under 18 Kg', 'Over 16 Kgs & Not Exceeding 18 Kgs'],
    ['Under 23 Kg', 'Over 21 Kgs & Not Exceeding 23 Kgs', 'Under 20 Kg', 'Over 18 Kgs & Not Exceeding 20 Kgs'],
    ['Under 25 Kg', 'Over 23 Kgs & Not Exceeding 25 Kgs', 'Under 22 Kg', 'Over 20 Kgs & Not Exceeding 22 Kgs'],
    ['Under 27 Kg', 'Over 25 Kgs & Not Exceeding 27 Kgs', 'Under 24 Kg', 'Over 22 Kgs & Not Exceeding 24 Kgs'],
    ['Under 29 Kg', 'Over 27 Kgs & Not Exceeding 29 Kgs', 'Under 26 Kg', 'Over 24 Kgs & Not Exceeding 26 Kgs'],
    ['Under 32 Kg', 'Over 29 Kgs & Not Exceeding 32 Kgs', 'Under 29 Kg', 'Over 26 Kgs & Not Exceeding 29 Kgs'],
    ['Under 35 Kg', 'Over 32 Kgs & Not Exceeding 35 Kgs', 'Under 32 Kg', 'Over 29 Kgs & Not Exceeding 32 Kgs'],
    ['Under 38 Kg', 'Over 35 Kgs & Not Exceeding 38 Kgs', 'Under 35 Kg', 'Over 32 Kgs & Not Exceeding 35 Kgs'],
    ['Under 41 Kg', 'Over 38 Kgs & Not Exceeding 41 Kgs', 'Under 38 Kg', 'Over 35 Kgs & Not Exceeding 38 Kgs'],
    ['Under 44 Kg', 'Over 41 Kgs & Not Exceeding 44 Kgs', 'Under 41 Kg', 'Over 38 Kgs & Not Exceeding 41 Kgs'],
    ['Under 50 Kg', 'Over 44 Kgs & Not Exceeding 50 Kgs', 'Under 47 Kg', 'Over 41 Kgs & Not Exceeding 47 Kgs'],
    ['Under 60 Kg', 'Over 50 Kgs & Not Exceeding 60 Kgs', 'Under 57 Kg', 'Over 47 Kgs & Not Exceeding 57 Kgs'],
];

foreach ($subJuniorRows as $row) {
    foreach ($row as $cell) {
        $pdf->Cell(45, 7, $cell, 1, 0, 'C');
    }
    $pdf->Ln();
}

$pdf->Ln(5);

// Junior Division Header
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(90, 7, 'Junior Division', 1, 1, 'C');

// Junior Boys and Girls Header Row
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(45, 7, 'Junior Boys', 1, 0, 'C');
$pdf->Cell(45, 7, 'Weight Category', 1, 0, 'C');
$pdf->Cell(45, 7, 'Junior Girls', 1, 0, 'C');
$pdf->Cell(45, 7, 'Weight Category', 1, 1, 'C');

// Junior Boys and Girls Weight Categories
$juniorRows = [
    ['Under 45 Kg', 'Not exceeding 45 Kg', 'Under 42 Kg', 'Not exceeding 42 Kg'],
    ['Under 48 Kg', 'Over 45 Kg & Not exceeding 48 Kg', 'Under 44 Kg', 'Over 42 Kg & Not exceeding 44 Kg'],
];

foreach ($juniorRows as $row) {
    foreach ($row as $cell) {
        $pdf->Cell(45, 7, $cell, 1, 0, 'C');
    }
    $pdf->Ln();
}

// Output the PDF
$pdf->Output("Individual Entry Form"." ".$row['name'].' 3.pdf', 'I');
?>
