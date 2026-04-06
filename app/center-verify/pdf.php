<?php
ob_start(); // 🔥 IMPORTANT: prevent accidental output

ini_set('display_errors', 0);
error_reporting(0);

require '../../includes/db-config.php';
session_start();

use setasign\Fpdi\Fpdi;
require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

if (!isset($_GET['id'])) {
    exit;
}

$id = (int) $_GET['id'];

$q = mysqli_query($conn, "SELECT * FROM center_verfiy1 WHERE id = $id");
if (mysqli_num_rows($q) == 0) {
    exit;
}

$data = mysqli_fetch_assoc($q);

// ---------------- PDF INIT ----------------
$pdf = new FPDI('P', 'mm', 'A4');

$templateFile = __DIR__ . '/MDU_1.pdf';
$pageCount = $pdf->setSourceFile($templateFile);
$tplId = $pdf->importPage(1);

$pdf->AddPage();
$pdf->useTemplate($tplId, 0, 0, 210, 297);

$pdf->SetFont('Arial', '', 10);

// ---------------- DATA MAPPING ----------------

// 1. NAME OF THE CENTER
$pdf->SetXY(55, 70);
$pdf->Cell(120, 6, $data['institution_name'], 0);

// 2. TYPE OF CENTER
$pdf->SetXY(100, 82);
$pdf->Cell(120, 6, $data['infra_dtls_types_ownership'], 0);

// // 3. DATE OF REGISTRATION
$pdf->SetXY(60, 92);
$pdf->Cell(60, 6, $data['declaration_date'], 0);

// // 4. PAN
$pdf->SetXY(150, 92);
$pdf->Cell(60, 6, $data['dir_state'], 0);

$pdf->SetXY(100, 102);
$pdf->Cell(60, 6, $data['dir_aadhar'], 0);

// // 5. DIRECTOR NAME
$pdf->SetXY(55, 114);
$pdf->Cell(120, 6, $data['dir_name'], 0);

// 6. ADDRESS
$pdf->SetXY(30, 124);
$pdf->MultiCell(190, 6, $data['dir_address']);

// 7. DISTRICT
$pdf->SetXY(35, 136);
$pdf->Cell(60, 6, $data['dir_district'], 0);

// // 8. CONTACT / STATE / PIN
$pdf->SetXY(45, 146);
$pdf->Cell(40, 6, $data['dir_mob_number'], 0);

$pdf->SetXY(125, 146);
$pdf->Cell(35, 6, $data['dir_state'], 0);

$pdf->SetXY(175, 146);
$pdf->Cell(30, 6, $data['dir_pincode'], 0);

// // 9. EMAIL
$pdf->SetXY(45, 158);
$pdf->Cell(120, 6, $data['dir_email'], 0);

// // 10. OWNERSHIP TYPE
$pdf->SetXY(90, 168);
$pdf->Cell(120, 6, $data['infra_dtls_types_ownership'], 0);

$pdf->SetXY(80, 178);
$pdf->Cell(120, 6, $data['infra_dtls_types_ownership'], 0);

// // 12. CENTER MANAGER NAME
$pdf->SetXY(90, 190);
$pdf->Cell(120, 6, $data['cmgr_name'], 0);

// // 13. MANAGER EMAIL
$pdf->SetXY(35, 201);
$pdf->Cell(120, 6, $data['cmgr_email'], 0);

// // 14. MANAGER PHONE
$pdf->SetXY(35, 212);
$pdf->Cell(120, 6, $data['cmgr_mob_number'], 0);

// // DECLARATION DATE


// PLACE
// $pdf->SetXY(120, 190);
// $pdf->Cell(60, 6, $data['place'], 0);
$pageCount = $pdf->setSourceFile($templateFile);
$tplId = $pdf->importPage(2);

// $pdf->SetXY(40, 300);
// $pdf->Cell(60, 6, $data['declaration_date'], 0);



// $pdf->AddPage();
// $pdf->useTemplate($tplId, 0, 0, 210, 297);


// ---------------- PAGE 2 ----------------
$tplId2 = $pdf->importPage(2);

$pdf->AddPage();
$pdf->useTemplate($tplId2, 0, 0, 210, 297);
$pdf->SetFont('Arial', '', 10);

// PLACE
$pdf->SetXY(20, 75);
$pdf->Cell(60, 6, $data['place'], 0);

// DECLARATION DATE
$pdf->SetXY(20, 60); // ✅ within page
$pdf->Cell(60, 6, $data['declaration_date'], 0);


$signaturePath = $_SERVER['DOCUMENT_ROOT'] . '/app/center-verify/uploads/' . $data['signature'];

if (!empty($data['signature']) && file_exists($signaturePath)) {
    // X, Y, WIDTH(mm), HEIGHT(mm)
    $pdf->Image($signaturePath, 170, 55, 30, 15);
}



$rubberPath = $_SERVER['DOCUMENT_ROOT'] . '/app/center-verify/uploads/' . $data['rubber_stamp'];

if (!empty($data['rubber_stamp']) && file_exists($rubberPath)) {
    // X, Y, WIDTH(mm), HEIGHT(mm)
    $pdf->Image($rubberPath, 170, 65, 30, 30);
}









// // ---------------- ENCLOSURES ----------------
// $pdf->SetFont('Arial', '', 9);

// /*
// | PARTICULARS (already printed in PDF) | REMARKS |
// */

// // Line 1
// $pdf->SetXY(160, 135);
// $pdf->Cell(30, 6, 'YES', 0);

// // Line 2
// $pdf->SetXY(160, 155);
// $pdf->Cell(30, 6, 'YES', 0);

// // Line 3
// $pdf->SetXY(160, 172);
// $pdf->Cell(30, 6, 'YES', 0);

// // Line 4
// $pdf->SetXY(160, 189);
// $pdf->Cell(30, 6, 'YES', 0);

// // Line 5
// // $pdf->SetXY(160, 210);
// // $pdf->Cell(30, 6, 'YES', 0);

// $pdf->SetXY(150, 210);
// $pdf->Cell(120, 6, $data['cmgr_aadhar'], 0);

// // // Line 6
// // $pdf->SetXY(160, 225);
// // $pdf->Cell(30, 6, 'YES', 0);

// $pdf->SetXY(150, 225);
// $pdf->Cell(120, 6, $data['cmgr_aadhar'], 0);

// // // Line 7
// $pdf->SetXY(160, 240);
// $pdf->Cell(30, 6, 'YES', 0);


// ---------------- ENCLOSURES ----------------
$pdf->SetFont('Arial', '', 9);

// Map of files for each line (update with your DB fields)
$enclosures = [
    1 => $data['approach_road'],    // Line 1
    2 => $data['front_view'],       // Line 2
    3 => $data['back_view'],        // Line 3
    4 => $data['reception_area'],   // Line 4
    5 => $data['domain_lab'],       // Line 5
    6 => $data['classroom'],        // Line 6
    7 => $data['washrooms'],        // Line 7
];

// Coordinates for each line (Y values from your code)
$coordinates = [
    1 => 135,
    2 => 155,
    3 => 172,
    4 => 189,
    5 => 210,
    6 => 225,
    7 => 240,
];

foreach ($enclosures as $line => $fileName) {
    $x = ($line == 5 || $line == 6) ? 160 : 160; // X adjustment like your old code
    $y = $coordinates[$line];

    if (!empty($fileName)) {
        // Add clickable "View" link in PDF
        $url = 'https://wilpvocmdu.edtechinnovate.in/app/center-verify/uploads/' . $fileName;
        $pdf->SetTextColor(0, 0, 255); // blue
        $pdf->SetXY($x, $y);
        $pdf->Cell(30, 6, 'View', 0, 0, '', false, $url);
        $pdf->SetTextColor(0, 0, 0); // reset color
    } else {
        // If no file, just print NO
        $pdf->SetXY($x, $y);
        $pdf->Cell(30, 6, 'NO', 0);
    }
}







$pdf->SetFont('Arial', '', 10);
// ---------------- OUTPUT ----------------
ob_end_clean();
$pdf->Output('I', 'Skill_Center_Application.pdf');
exit;
