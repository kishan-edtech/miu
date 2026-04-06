<?php

require '../extras/vendor/autoload.php';
require_once('../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../extras/vendor/setasign/fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

require '../includes/db-config.php'; // 🔥 DB CONNECTION

// ==============================
// DIRECTORY SETUP
// ==============================
$pdf_dir = '../uploads/aadhar_pdfs/';

if (!is_dir($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}

// ==============================
// FETCH AADHAAR DATA
// ==============================
$sql = "
SELECT 
    Student_Documents.Location,
    Students.Unique_ID
FROM wilp_mdu.Student_Documents
LEFT JOIN wilp_mdu.Students 
    ON Student_Documents.Student_ID = Students.ID
WHERE 
    Students.ID IN (298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418)
    AND Student_Documents.Type = 'Aadhar'
    AND Students.Added_For = 1721
";

$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die('No Aadhaar records found');
}

// ==============================
// PDF GENERATION
// ==============================
$generated_files = [];

while ($row = $result->fetch_assoc()) {

    $image_path = '../' . ltrim($row['Location'], '/');
    $unique_id  = $row['Unique_ID'];

    if (!file_exists($image_path)) {
        continue; // skip if image missing
    }

    $pdf = new Fpdi();
    $pdf->AddPage('P', 'A4');

    // Aadhaar image full page
    $pdf->Image(
        $image_path,
        5,
        5,
        200,
        287
    );

    // PDF NAME = UNIQUE_ID.pdf
    $filename = $unique_id . '.pdf';
    $file_path = $pdf_dir . $filename;

    $pdf->Output($file_path, 'F');

    if (file_exists($file_path)) {
        $generated_files[] = $file_path;
    }
}

// ==============================
// ZIP CREATION
// ==============================
if (empty($generated_files)) {
    die('No PDFs created');
}

$zip = new ZipArchive();
$zip_name = $pdf_dir . 'Aadhaar_PDFs_' . time() . '.zip';

if ($zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    foreach ($generated_files as $file) {
        $zip->addFile($file, basename($file));
    }
    $zip->close();
} else {
    die('ZIP creation failed');
}

// ==============================
// CLEANUP INDIVIDUAL PDFs
// ==============================
foreach ($generated_files as $file) {
    unlink($file);
}

// ==============================
// DOWNLOAD ZIP
// ==============================
ob_clean();
flush();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zip_name) . '"');
header('Content-Length: ' . filesize($zip_name));
header('Cache-Control: no-store');

readfile($zip_name);
unlink($zip_name);
exit;
