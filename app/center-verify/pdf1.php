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

// Template PDF
$templateFile = __DIR__ . '/MDU_Vocational.pdf';

// Load template
$pageCount = $pdf->setSourceFile($templateFile);

/*
|--------------------------------------------------------------------------
| PAGE 1 (BACKGROUND ONLY)
|--------------------------------------------------------------------------
*/
$tpl1 = $pdf->importPage(1);
$pdf->AddPage();
$pdf->useTemplate($tpl1, 0, 0, 210, 297);


$pdf->SetFont('Arial', '', 10);

// ---------------- DATA MAPPING ----------------

// 1. NAME OF THE CENTER
$pdf->SetXY(40, 108);
$pdf->Cell(120, 6, $data['institution_name'], 0);


$pdf->SetXY(22, 209);
$pdf->Cell(120, 6, date('d-m-Y', strtotime($data['created_at'])), 0);

// place


$pdf->SetXY(23, 219);
$pdf->Cell(120, 6, $data['place'], 0);


$pdf->SetXY(15, 235);
$pdf->Cell(120, 6, strtoupper($data['institution_name']), 0);




$signaturePath = $_SERVER['DOCUMENT_ROOT'] . '/app/center-verify/uploads/' . $data['signature'];

if (!empty($data['signature']) && file_exists($signaturePath)) {
    // X, Y, WIDTH(mm), HEIGHT(mm)
    $pdf->Image($signaturePath, 138, 213, 30, 15);
}



/*
|--------------------------------------------------------------------------
| PAGE 2 (BACKGROUND ONLY)
|--------------------------------------------------------------------------
*/
$tpl2 = $pdf->importPage(2);
$pdf->AddPage();
$pdf->useTemplate($tpl2, 0, 0, 210, 297);


$pdf->SetFont('Arial', '', 10);

// ---------------- DATA MAPPING ----------------

// 1. NAME OF THE CENTER
$pdf->SetXY(55, 82);
$pdf->Cell(120, 6, $data['institution_name'], 0);

// // 2. TYPE OF CENTER
$pdf->SetXY(48, 91);
$pdf->Cell(120, 6, $data['postal_address'], 0);

// // // 3. DATE OF REGISTRATION
$pdf->SetXY(50, 100);
$pdf->Cell(60, 6, $data['city_place'], 0);

// // // 4. PAN
$pdf->SetXY(130, 100);
$pdf->Cell(60, 6, $data['block_tehsil'], 0);

$pdf->SetXY(40, 110);
$pdf->Cell(60, 6, $data['dir_district'], 0);

$pdf->SetXY(105, 110);
$pdf->Cell(120, 6, $data['dir_pincode'], 0);

$pdf->SetXY(160, 110);
$pdf->MultiCell(190, 6, $data['dir_state']);

// // 7. DISTRICT
$pdf->SetXY(80, 120);
$pdf->Cell(60, 6, $data['dir_mob_number'], 0);

// // // 8. CONTACT / STATE / PIN
$pdf->SetXY(160, 120);
$pdf->Cell(40, 6, $data['dir_mob_number'], 0);

$pdf->SetXY(40, 130);
$pdf->Cell(35, 6, $data['dir_email'], 0);

$pdf->SetXY(80, 138);
$pdf->Cell(30, 6, $data['dir_name'], 0);

// // // 9. EMAIL
$pdf->SetXY(80, 148);
$pdf->Cell(120, 6, $data['qualification_principal'], 0);

// // // 10. OWNERSHIP TYPE
$pdf->SetXY(90, 156);
$pdf->Cell(120, 6, $data['administrative_exp'], 0);

$pdf->SetXY(185, 156);
$pdf->Cell(120, 6, $data['teaching_exp'], 0);

// // // 12. CENTER MANAGER NAME
$pdf->SetXY(100, 166);
$pdf->Cell(120, 6, $data['name_address'], 0);

// $pdf->SetXY(128, 176);
// $pdf->Cell(120, 6, $data['registered'], 0);



// // Position of YES/NO checkboxes in your PDF
// $x_yes = 128;  // X position of YES box
// $x_no  = 140;  // X position of NO box
// $y     = 178;  // Y position of both boxes
// $boxSize = 4;  // size of the tick mark

// // Tick YES if registered is 'yes'
// if (strtolower($data['registered']) == 'yes') {
//     $pdf->SetFont('ZapfDingbats', '', 12);
//     $pdf->SetXY($x_yes, $y - 1); // adjust vertical alignment
//     $pdf->Cell($boxSize, $boxSize, chr(51)); // tick mark
// }

// // Tick NO if registered is 'no'
// if (strtolower($data['registered']) == 'no') {
//     $pdf->SetFont('ZapfDingbats', '', 12);
//     $pdf->SetXY($x_no, $y - 1);
//     $pdf->Cell($boxSize, $boxSize, chr(51)); // tick mark
// }


// ---------- YES / NO tick for Registered (ONLY THIS ROW) ----------
$x_yes = 128;   // YES position (adjust once)
$x_no  = 140;   // NO position
$y     = 178;   // Same row Y position

$pdf->SetFont('ZapfDingbats', '', 12);

if (strtolower(trim($data['registered'])) === 'yes') {
    $pdf->SetXY($x_yes, $y);
    $pdf->Cell(5, 5, chr(51), 0); // ✔ YES
} elseif (strtolower(trim($data['registered'])) === 'no') {
    $pdf->SetXY($x_no, $y);
    $pdf->Cell(5, 5, chr(51), 0); // ✔ NO
}

$pdf->SetFont('Arial', '', 10); // reset font
// ---------------------------------------------------------------



// // // 14. MANAGER PHONE
$pdf->SetXY(68, 183);
$pdf->Cell(120, 6, $data['registration_year'], 0);


$pdf->SetXY(120, 183);
$pdf->Cell(120, 6, $data['registration_no'], 0);


//Name & official address of the Manager/President/Chairman of the centre
$pdf->SetXY(40, 235);
$pdf->Cell(120, 6, $data['cmgr_name'], 0);

$pdf->SetXY(45, 244);
$pdf->Cell(120, 6, $data['manager_designation'], 0);


$pdf->SetXY(40, 252);
$pdf->Cell(120, 6, $data['cmgr_address'], 0);




$pdf->SetXY(70, 260);
$pdf->Cell(120, 6, $data['cmgr_mob_number'], 0);






/*
|--------------------------------------------------------------------------
| PAGE 3 (BACKGROUND ONLY)
|--------------------------------------------------------------------------
*/
$tpl3 = $pdf->importPage(3);
$pdf->AddPage();
// ✅ ADD THIS LINE
$pdf->SetAutoPageBreak(false);
$pdf->useTemplate($tpl3, 0, 0, 210, 297);

$pdf->SetFont('Arial', '', 10);


// $pdf->SetXY(70, 29);
// $pdf->Cell(120, 6, $data['area_sq'], 0);

// Convert acres to square meters (clean value)
$area_acres = $data['area_acres']; // decimal(10,2)
$area_sq_m  = $area_acres * 4046.85642;

// Round to 2 decimal places and remove unnecessary decimals if whole number
if (floor($area_sq_m) == $area_sq_m) {
    $area_sq_m = number_format($area_sq_m, 0); // no decimals
} else {
    $area_sq_m = number_format($area_sq_m, 2); // 2 decimals
}

// Print Total Area in sq. Mtrs at position
$pdf->SetXY(70, 22);
$pdf->Cell(120, 6, $area_sq_m, 0);



$pdf->SetXY(70, 29);
$pdf->Cell(120, 6, $data['built_area'], 0);








// Process Staff Details from JSON
$staff_details = [];
if (!empty($data['staff_details_json'])) {
    $staff_json = json_decode($data['staff_details_json'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($staff_json)) {
        $staff_details = $staff_json;
    }
}

// STAFF TABLE DATA
// Starting positions for the table (adjust these based on your PDF template)
$start_x = 22;  // Starting X position
$start_y = 53;  // Starting Y position (below the staff details heading)
$row_height = 8; // Height of each row
$col1_width = 80; // Width for Staff Name column
$col2_width = 100; // Width for Qualification column

// Counter for serial numbers
$counter = 1;

// Add staff data to the table
foreach ($staff_details as $staff) {
    $staff_name = isset($staff['name']) ? $staff['name'] : '';
    $qualification = isset($staff['qualification']) ? $staff['qualification'] : '';
    
    // Serial Number
    $pdf->SetXY($start_x, $start_y);
    $pdf->Cell(10, $row_height, $counter . '.', 0, 0, 'L');
    
    // Staff Name
    $pdf->SetXY($start_x + 10, $start_y);
    $pdf->Cell($col1_width, $row_height, $staff_name, 0, 0, 'L');
    
    // Qualification
    $pdf->SetXY($start_x + 10 + $col1_width, $start_y);
    $pdf->Cell($col2_width, $row_height, $qualification, 0, 0, 'L');
    
    // Move to next row
    $start_y += $row_height;
    $counter++;
    
    // Break if too many rows (avoid going off page)
    if ($start_y > 250) {
        break;
    }
}

// If no staff details, show a message
if (empty($staff_details)) {
    $pdf->SetXY($start_x, $start_y);
    $pdf->Cell(180, $row_height, 'No staff details provided.', 0, 0, 'C');
}










// $pdf->SetXY(30, 155);
// $pdf->Cell(120, 6, $data['built_area'], 0);

$pdf->SetXY(25, 156);
$pdf->Cell(120, 6, date('d-m-Y', strtotime($data['declaration_date'])), 0);





$pdf->SetXY(25, 166);
$pdf->Cell(120, 6, $data['place'], 0);


// // i want here Signature with view button 
// $pdf->SetXY(150, 166);
// $pdf->Cell(120, 6, $data['place'], 0);

// ---------- SIGNATURE : VIEW ONLY ----------
if (!empty($data['signature'])) {
    $signatureUrl = '/app/center-verify/uploads/' . $data['signature'];

    $pdf->SetXY(150, 166);   // adjust position if needed
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 255);

    $pdf->Cell(30, 6, 'View', 0, 0, 'L', false, $signatureUrl);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);
}

// ---------- RUBBER STAMP : VIEW ONLY ----------
if (!empty($data['rubber_stamp'])) {
    $rubberUrl = '/app/center-verify/uploads/' . $data['rubber_stamp'];

    $pdf->SetXY(165, 156);   // adjust position if needed
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 255);

    $pdf->Cell(30, 6, 'View', 0, 0, 'L', false, $rubberUrl);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);
}


$pdf->SetXY(25, 275);
$pdf->Cell(120, 6, date('d-m-Y', strtotime($data['declaration_date'])), 0);





/*
|--------------------------------------------------------------------------
| PAGE 4 (BACKGROUND ONLY)
|--------------------------------------------------------------------------
*/
// $tpl4 = $pdf->importPage(4);
// $pdf->AddPage();
// $pdf->useTemplate($tpl4, 0, 0, 210, 297);


// $pdf->SetXY(30, 155);
// $pdf->Cell(120, 6, $data['enclosures_json'], 0);



/*
|--------------------------------------------------------------------------
| PAGE 4 - ENCLOSURES CHECKLIST (Yes/No Text Only)
|--------------------------------------------------------------------------
*/
// $tpl4 = $pdf->importPage(4);
// $pdf->AddPage();
// $pdf->useTemplate($tpl4, 0, 0, 210, 297);

// $pdf->SetFont('Arial', '', 10);

// // Parse the enclosures data from JSON
// $enclosures = json_decode($data['enclosures_json'], true);

// // Define positions for each row with both X and Y coordinates
// // Format: [x_position, y_position]
// $positions = [
//     1 => ['x' => 130, 'y' => 56],   // Row 1
//     2 => ['x' => 130, 'y' => 72],   // Row 2
//     3 => ['x' => 130, 'y' => 88],   // Row 3
//     4 => ['x' => 130, 'y' => 105],  // Row 4
//     5 => ['x' => 130, 'y' => 120],  // Row 5
//     6 => ['x' => 130, 'y' => 140],  // Row 6
//     7 => ['x' => 130, 'y' => 160],  // Row 7
//     8 => ['x' => 130, 'y' => 175],  // Row 8
//     9 => ['x' => 130, 'y' => 183],  // Row 9
//     // Add more if needed: 10 => ['x' => 40, 'y' => 173], etc.
// ];

// $counter = 1;
// foreach ($enclosures as $enclosure) {
//     if (!isset($positions[$counter])) {
//         break; // No more positions defined
//     }
    
//     $pos = $positions[$counter];
//     $status = isset($enclosure['status']) ? strtoupper($enclosure['status']) : 'NO';
    
//     // Write "Yes" or "No" at the specified X,Y position
//     $pdf->SetXY($pos['x'], $pos['y']);
//     $pdf->Cell(20, 6, $status, 0, 0, 'C');
    
//     $counter++;
// }

// // ---------------- OUTPUT ----------------
// ob_end_clean();
// $pdf->Output('I', 'Vocational_Application_Complete.pdf');
// exit;


/*
|--------------------------------------------------------------------------
| PAGE 4 - ENCLOSURES CHECKLIST (Yes/No with File Links - No Underline)
|--------------------------------------------------------------------------
*/
$tpl4 = $pdf->importPage(4);
$pdf->AddPage();
$pdf->useTemplate($tpl4, 0, 0, 210, 297);

$pdf->SetFont('Arial', '', 10);

// Parse the enclosures data from JSON
$enclosures = json_decode($data['enclosures_json'], true);

// Define positions for Yes/No column
$yesno_positions = [
    1 => ['x' => 130, 'y' => 56],   // Row 1
    2 => ['x' => 130, 'y' => 72],   // Row 2
    3 => ['x' => 130, 'y' => 88],   // Row 3
    4 => ['x' => 130, 'y' => 105],  // Row 4
    5 => ['x' => 130, 'y' => 120],  // Row 5
    6 => ['x' => 130, 'y' => 140],  // Row 6
    7 => ['x' => 130, 'y' => 160],  // Row 7
    8 => ['x' => 130, 'y' => 175],  // Row 8
    9 => ['x' => 130, 'y' => 183],  // Row 9
];

// Define positions for Remarks/File links column
$remarks_positions = [
    1 => ['x' => 175, 'y' => 56],   // Row 1 - Remarks column
    2 => ['x' => 175, 'y' => 72],   // Row 2
    3 => ['x' => 175, 'y' => 88],   // Row 3
    4 => ['x' => 175, 'y' => 105],  // Row 4
    5 => ['x' => 175, 'y' => 120],  // Row 5
    6 => ['x' => 175, 'y' => 140],  // Row 6
    7 => ['x' => 175, 'y' => 160],  // Row 7
    8 => ['x' => 175, 'y' => 175],  // Row 8
    9 => ['x' => 175, 'y' => 183],  // Row 9
];

$counter = 1;
foreach ($enclosures as $enclosure) {
    if (!isset($yesno_positions[$counter])) {
        break; // No more positions defined
    }
    
    $pos = $yesno_positions[$counter];
    $status = isset($enclosure['status']) ? strtoupper($enclosure['status']) : 'NO';
    
    // Write "Yes" or "No" at the specified X,Y position
    $pdf->SetXY($pos['x'], $pos['y']);
    $pdf->Cell(20, 6, $status, 0, 0, 'C');
    
    // Add file link in remarks column if file exists
    $file_info = isset($enclosure['file']) ? $enclosure['file'] : null;
    
    if (!empty($file_info) && $status === 'YES') {
        if (isset($remarks_positions[$counter])) {
            $rem_pos = $remarks_positions[$counter];
            
            if (is_array($file_info) && !empty($file_info)) {
                // Multiple files (like enclosure_7)
                $file_count = count($file_info);
                $pdf->SetXY($rem_pos['x'], $rem_pos['y']);
                $pdf->SetTextColor(0, 0, 255); // Blue color for links
                $pdf->SetFont('Arial', '', 8); // Removed 'U' parameter (no underline)
                $pdf->Cell(40, 6, "View ($file_count files)", 0, 0, 'L', false, '');
                $pdf->SetTextColor(0, 0, 0); // Reset to black
                $pdf->SetFont('Arial', '', 10); // Reset font
            } elseif (!empty($file_info)) {
                // Single file - create clickable link
                $file_name = basename($file_info);
                $file_url = '/app/center-verify/uploads/' . $file_info; // Adjust URL path
                
                $pdf->SetXY($rem_pos['x'], $rem_pos['y']);
                $pdf->SetTextColor(0, 0, 255); // Blue color for links
                $pdf->SetFont('Arial', '', 8); // Removed 'U' parameter (no underline)
                $pdf->Cell(40, 6, "View", 0, 0, 'L', false, $file_url);
                $pdf->SetTextColor(0, 0, 0); // Reset to black
                $pdf->SetFont('Arial', '', 10); // Reset font
            }
        }
    } elseif (isset($remarks_positions[$counter])) {
        // Show dash for no file
        $rem_pos = $remarks_positions[$counter];
        $pdf->SetXY($rem_pos['x'], $rem_pos['y']);
        $pdf->Cell(40, 6, "-", 0, 0, 'L');
    }
    
    $counter++;
}

// ---------------- OUTPUT ----------------
ob_end_clean();
$pdf->Output('I', 'Vocational_Application_Complete.pdf');
exit;

// // ---------------- OUTPUT ----------------
// ob_end_clean();
// $pdf->Output('I', 'Vocational_Background_Only.pdf');
// exit;
