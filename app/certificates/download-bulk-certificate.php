<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../includes/db-config.php';
require '../../extras/vendor/autoload.php';

use setasign\Fpdi\Fpdi;

session_start();
ini_set('max_execution_time', 120);

$sqlQuery = '';
$background_type = $_POST['background_type'] ?? 'with_background';
//  echo('<pre>');print_r($background_type);DIE;
if (isset($_POST['course_type_id']) && !empty($_POST['course_type_id'])) {
    $course_id = $_POST['course_type_id'];
    $sqlQuery .= " AND Students.Course_ID = '$course_id'";
}

if (isset($_POST['course_id']) && !empty($_POST['course_id'])) {
    $sub_course_id = $_POST['course_id'];
    $sqlQuery .= " AND Students.Sub_Course_ID = '$sub_course_id'";
}

if (isset($_POST['student_id']) && !empty($_POST['student_id'])) {
    $student_id_array = explode(",", $_POST['student_id']);
    foreach ($student_id_array as &$en_no) {
        $en_no = "'" . trim($en_no) . "'";
    }
    unset($en_no);
    $student_id = implode(",", $student_id_array);
    $sqlQuery .= " AND Students.Enrollment_No IN ($student_id)";
}
// print_r($sqlQuery);die;
if (isset($_POST['category']) && !empty($_POST['category'])) {
    $sub_course_id = $_POST['category'];
    $sqlQuery .= " AND Students.Duration = 1";
}

$pdf_dir = '../../uploads/certificates/';

// Create certificates directory if it doesn't exist
if (!is_dir($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}
// echo("SELECT Students.*, Sub_Courses.Min_Duration as total_duration, Modes.Name as mode, Sub_Courses.Name as course, Courses.Name as program_Type FROM Students LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID LEFT JOIN Modes ON Students.University_ID = Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.Enrollment_No IS NOT NULL $sqlQuery");die;
$students_sql = $conn->query("SELECT Students.*, Sub_Courses.Min_Duration as total_duration,Sub_Courses.Name as course, Courses.Name as program_Type FROM Students LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID  LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.Enrollment_No IS NOT NULL $sqlQuery");

if ($students_sql->num_rows > 0) {
    $generated_files = [];

    while ($row = $students_sql->fetch_assoc()) {
        $students_result = $conn->query("SELECT Students.*, Admission_Sessions.Name as Admission_Session, Sub_Courses.Min_Duration as total_duration, Modes.Name as mode, Sub_Courses.Name as course, Courses.Name as program_Type FROM Students LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID LEFT JOIN Modes ON Students.University_ID = Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.Enrollment_No = '" . trim($row['Enrollment_No']) . "'");

        if ($students_result && $students_result->num_rows > 0) {
            $students_temps = $students_result->fetch_assoc();
            $Enrol_no = $students_temps['Enrollment_No'];
            //  print_r($Enrol_no);die;
            $dateIssuequery = $conn->query("SELECT date_of_issue FROM certificates where enrollment_no= '$Enrol_no'");
            $dateinfo = $dateIssuequery->fetch_assoc();
            //   print_r($dateinfo);die;
            // Calculate academic session
            $adm_session = '2023-24';
            if (!empty($students_temps['Admission_Session'])) {
                list($month, $year) = explode('-', $students_temps['Admission_Session']);
                if ($year > 23) {
                    $adm_session = '2024-25';
                }
            }

            $durMonthYear = "";
            if ($students_temps['mode'] == "Monthly") {
                $durMonthYear = "Months";
            } elseif ($students_temps['mode'] == "Sem") {
                $durMonthYear = "Semesters";
            } else {
                $durMonthYear = "Years";
            }

            // Calculate total duration
            $total_duration = 0;
            if ($students_temps['University_ID'] == 47) {
                if (!empty($students_temps['total_duration']) && str_contains($students_temps['total_duration'], '"')) {
                    $a = str_replace('"', '', $students_temps['total_duration']);
                    $total_duration = (int)$a;
                } else {
                    $total_duration = !empty($students_temps['total_duration']) ? (int)$students_temps['total_duration'] : 0;
                }
            } else {
                if (!empty($students_temps['Duration']) && str_contains($students_temps['Duration'], '/')) {
                    $a = explode("/", $students_temps['Duration']);
                    $total_duration = (int)$a[0];
                } else {
                    $total_duration = !empty($students_temps['Duration']) ? (int)$students_temps['Duration'] : 0;
                }
            }

            // Determine certificate type and hours - FIXED Course_Category issue
            $certificate = "Certified Skill Diploma";
            $courseCategory = isset($students_temps['Course_Category']) ? $students_temps['Course_Category'] : '';

            if (!empty($courseCategory) && str_contains($courseCategory, 'advance_diploma')) {
                $certificate = "Adv. Certification Skill Diploma";
            }

            $hours = 0;
            if ($total_duration == 3 && $durMonthYear == "Months") {
                $certificate = "Certification";
                $hours = 160;
            } elseif ($total_duration == 6 && $durMonthYear == "Months") {
                $certificate = "Certified Skill Diploma";
                $hours = 320;
            } elseif ($total_duration == 11 && $durMonthYear == "Months") {
                $hours = 960;
            }

            $name = $students_temps['First_Name'] . " " . $students_temps['Middle_Name'] . " " . $students_temps['Last_Name'];


            // Format name
            $name = array_filter(explode(" ", $name));
            $formattedName = "";
            foreach ($name as $n) {
                if (!empty($formattedName)) {
                    $formattedName .= ' ';
                }
                $firstLetter = substr($n, 0, 1);
                $restLetters = substr($n, 1);
                $formattedName .= $firstLetter . $restLetters;
            }

            // Extract course name
            $courseFull = $students_temps['course'];
            $durationValue = '';
            $courseName = $courseFull;

            if (!empty($courseFull)) {
                preg_match('/\((.*?)\)/', $courseFull, $matches);
                $durationValue = isset($matches[1]) ? $matches[1] : '';
                $courseName = trim(preg_replace('/\s*\(.*?\)\s*/', '', $courseFull));
            }

            require_once('../../extras/TCPDF/tcpdf.php');
            require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
            require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

            // Create PDF based on background type
            $pdf = new FPDI('L', 'mm', array(299, 210));

            if ($background_type == 'with_background') {
                // WITH BACKGROUND - Use template
                $templateFile = 'mdu_certificate.pdf';

                if (file_exists($templateFile)) {
                    $pdf = new Fpdi();
                    // $templateFile = 'mduMarksheet.pdf';
                    $pageCount = $pdf->setSourceFile($templateFile);
                    $tplIdx = $pdf->importPage(1);
                    $pdf->addPage('L');
                    $pdf->useTemplate($tplIdx, 0, 0, 297, 210);
                    // $pdf->SetTextColor(6, 64, 101);
                } else {
                    $pdf = new Fpdi();
                    $pdf->AddPage('L', 'A4');
                    $pdf->SetTextColor(0, 0, 0);
                }
            } else {
                // NO BACKGROUND - Create blank page
                $pdf->AddPage();
                $pdf->SetTextColor(0, 0, 0);
            }

            // COMMON COORDINATES FOR BOTH VERSIONS - SAME AS WITH_BACKGROUND

            // Student name and enrollment - SAME COORDINATES
            $pdf->SetFont('Times', 'B', 16);
            // ($background_type=='with_background')? $pdf->SetXY(0, 89.8): $pdf->SetXY(0, 89.8);
            $pdf->SetXY(0, 89.8);

            // $formattedName = strtolower(trim($formattedName));
            $formattedName = strtolower(trim($formattedName));

            // $formattedName = implode(' ', array_map(function ($word) {
            //     // remove extra spaces safety
            //     $word = trim($word);

            //     // if word length is exactly 2 → FULL CAPS
            //     if (mb_strlen($word) === 2) {
            //         return strtoupper($word);
            //     }

            //     // otherwise normal ucfirst
            //     return ucfirst($word);

            // }, preg_split('/\s+/', $formattedName)));
            $formattedName = implode(' ', array_map(function ($word) {

                $word = trim($word);

                // Agar word me dot hai (T.s. type case)
                if (strpos($word, '.') !== false) {

                    $parts = explode('.', $word);

                    $parts = array_map(function ($part) {
                        if ($part === '') return '';
                        return ucfirst(strtolower($part));
                    }, $parts);

                    return implode('.', $parts);
                }

                // Agar 2 letter word hai → full caps
                if (mb_strlen($word) === 2) {
                    return strtoupper($word);
                }

                return ucfirst(strtolower($word));
            }, preg_split('/\s+/', $formattedName)));

            $pdf->Cell(0, 0, ucwords($formattedName) . " bearing Enrollment no. " . '(' . $Enrol_no . ')', 0, 1, 'C');
            $pdf->SetFont('Times', 'B', 12);

            ($background_type == 'with_background') ? $pdf->SetXY(110, 106.5) : $pdf->SetXY(110, 106.5);
            // $pdf->SetXY(110, 108.5); 

            // $pdf->Cell(0, 0, $formattedName . " bearing Enrollment No " . $Enrol_no, 0, 1, 'C'); 
            // $pdf->Cell(0, 0, strtoupper(trim($courseName)), 0, 1, 'L');
            // $courseHeight=($background_type=='with_background')? '6': '6';
            $pdf->MultiCell(170, 6, strtoupper(trim($courseName)), 0, 'L');
            //$pdf->SetXY(52, 140.6); 
            ($background_type == 'with_background') ? $pdf->SetXY(52, 147.6) : $pdf->SetXY(52, 140.6);
            $pdf->Cell(0, 0, $students_temps['Admission_Session'], 0, 1, 'C');
            //$pdf->SetXY(96, 140.6); 
            ($background_type == 'with_background') ? $pdf->SetXY(96, 147.6) : $pdf->SetXY(96, 140.6);
            $pdf->Cell(0, 0, $durationValue, 0, 1, 'L');
            // $months = (int) filter_var($durationValue, FILTER_SANITIZE_NUMBER_INT);
            $months = (int) filter_var($durationValue, FILTER_SANITIZE_NUMBER_INT);

            $admissionSession = $students_temps['Admission_Session'];

            // Create date
            $date = DateTime::createFromFormat('M-Y', $admissionSession);
            // echo $admissionSession;die;
            //  Inclusive logic (current month included)
            $date->modify('+' . ($months - 1) . ' months');

            // Final result
            $completeMonthYear = $date->format('M-Y');

            // echo $completeMonthYear;die;
            //$pdf->SetXY(220, 140.6); 
            ($background_type == 'with_background') ? $pdf->SetXY(220, 147.6) : $pdf->SetXY(220, 140.6);
            $pdf->Cell(0, 0, $completeMonthYear, 0, 1, 'L');
            //$pdf->SetXY(58, 175.6);
            // ($background_type == 'with_background') ? $pdf->SetXY(58, 177.6) : $pdf->SetXY(58, 175.6);
            // $pdf->Cell(0, 0, date('d/m/Y'), 0, 1, 'L');

            //jan-25 11 months
            // $pdf->Cell(0, 0, '28/01/2026', 0, 1, 'L');
            //jul-25 6 months
            // $dateOfIssue= date("M-Y", strtotime("+2 months", strtotime($completeMonthYear)));
            if (empty($dateinfo['date_of_issue']) || $dateinfo['date_of_issue'] == "") {
                $date = DateTime::createFromFormat('M-Y', $completeMonthYear);

                /* +2 months */
                $date->modify('+2 months');

                /* second week ka start (8th day) */
                $date->setDate($date->format('Y'), $date->format('m'), 8);

                /* agar weekend hai to agle Monday par le jao */
                if ($date->format('N') == 6) {       // Saturday
                    $date->modify('+2 days');
                } elseif ($date->format('N') == 7) { // Sunday
                    $date->modify('+1 day');
                }
                $dateOfIssue = $date->format('d/m/Y');
                $updatedateofissue = ", date_of_issue = '$dateOfIssue'";
            } else {
                $dateOfIssue =  $dateinfo['date_of_issue'];
                $updatedateofissue = "";
            }



            // echo '<pre>';
            // print_r($dateOfIssue);
            // die;
            // $pdf->Cell(0, 0, $dateOfIssue, 0, 1, 'L');
            //APRIL-25 6 months
            // $pdf->Cell(0, 0, '23/10/2025', 0, 1, 'L');
            //jan-25 6 months
            // $pdf->Cell(0, 0, '10/07/2026', 0, 1, 'L');
            // Course name - SAME COORDINATES
            //             $pdf->SetFont('Arial', 'B', 11); 
            //             // $pdf->SetXY(123, 125); 
            //             // $pdf->Cell(0, 0, substr(strtoupper($courseName), 0, 67), 0, 1, 'L'); 
            // $courseText = strtoupper(trim($courseName));
            // $maxWidth = 80; // width for first line
            // $x = 98;
            // $y = 150.8;
            // $lineHeight = 14.5;

            // // Split words
            // $words = explode(' ', $courseText);
            // $line = '';
            // $lines = [];
            // $lineCount = 0;

            // foreach ($words as $word) {
            //     $testLine = $line . $word . ' ';
            //     $lineWidth = $pdf->GetStringWidth($testLine);

            //     if ($lineWidth > $maxWidth && $line !== '') {
            //         $lines[] = trim($line);
            //         $line = $word . ' ';
            //         $lineCount++;

            //         // Stop wrapping after first line
            //         if ($lineCount == 1) {
            //             break;
            //         }
            //     } else {
            //         $line = $testLine;
            //     }
            // }

            // // Remaining text goes fully into the second line
            // $remainingWords = array_slice($words, count(explode(' ', implode(' ', $lines))));
            // $lines[] = trim(implode(' ', $remainingWords));
            // Remaining text goes fully into the second line
            // if(count($lines)>0){
            //     $remainingWords = array_slice($words, count(explode(' ', implode(' ', $lines))));
            //     $lines[] = trim(implode(' ', $remainingWords));    
            // }else{
            //     $lines[] = $line;
            // }
            // // Print first line (centered)
            // if (!empty($lines[0])) {
            //     $pdf->SetXY($x, $y);
            //     $pdf->Cell($maxWidth, 0, $lines[0], 0, 1, 'L');
            // }

            // // Print second line (left aligned and wider)
            // if (!empty($lines[1])) {
            //     $y += $lineHeight;
            //     $pdf->SetXY($x - 77, $y); // adjust X for better alignment
            //     $pdf->Cell(120, 0, $lines[1], 0, 1, 'L');
            // }

            // Academic year - SAME COORDINATES
            // $pdf->SetXY(44, 181.3); 
            // // $pdf->Cell(0, 0, 'AY ' . $adm_session, 0, 1, 'C'); 
            // $pdf->Cell(0, 0, 'AY 2024 - 25', 0, 1, 'C');
            // Duration - SAME COORDINATES
            // $pdf->SetXY(73, 198); 
            // $pdf->Cell(0, 0, $durationValue, 0, 1, 'L'); 

            // Date - SAME COORDINATES
            // $pdf->SetXY(40, 180); 
            // $pdf->Cell(0, 0, "04-06-2025", 0, 1, 'L');

            // Save individual PDF - FIXED file path issue
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $students_temps['Enrollment_No']) . "_" . time() . "_" . rand(1000, 9999) . ".pdf";
            $file_path = $pdf_dir . $filename;

            // Ensure directory exists and is writable
            if (!is_dir($pdf_dir)) {
                mkdir($pdf_dir, 0777, true);
            }

            // Save PDF file
            $pdf->Output($file_path, "F");
            // exit;
            // Check if file was created successfully
            if (file_exists($file_path)) {
                $generated_files[] = $file_path;
            } else {
                error_log("Failed to create PDF file: " . $file_path);
            }
        } else {
            echo "No student found for enrollment number: " . $row['Enrollment_No'] . "<br>";
        }

        $student_id = $students_temps['ID'];
        // echo('<pre>');print_r($student_id);die;
        $enrollment_no = $students_temps['Enrollment_No'];
        $file_path_db = "uploads/certificates/" . basename($file_path);
        $file_type = "pdf";
        $status = 1;
        $created_by = $_SESSION['ID'] ?? 0;
        $timestamp = date("Y-m-d H:i:s");

        // Check if certificate already exists
        $check_cert = $conn->query("SELECT ID, file_path FROM certificates WHERE student_id = '$student_id' LIMIT 1");

        if ($check_cert && $check_cert->num_rows > 0) {
            // --- Update existing record ---
            $existing = $check_cert->fetch_assoc();
            $old_file = "../../" . $existing['file_path'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }

            $update_cert = $conn->query("
        UPDATE certificates 
        SET 
            file_path = '$file_path_db',
            file_type = '$file_type',
            updated_at = '$timestamp',
            created_by = '$created_by'
            $updatedateofissue
        WHERE student_id = '$student_id'
    ");

            if (!$update_cert) {
                error_log("Failed to update certificate for student_id $student_id: " . $conn->error);
            }
        } else {
            // --- Insert new record ---
            $insert_cert = $conn->query("
        INSERT INTO certificates (student_id, enrollment_no, file_path, file_type, status, created_by, created_at,date_of_issue)
        VALUES ('$student_id', '$enrollment_no', '$file_path_db', '$file_type', '$status', '$created_by', '$timestamp','$dateOfIssue')
    ");

            if (!$insert_cert) {
                error_log("Failed to insert certificate for student_id $student_id: " . $conn->error);
            }
        }
        // Check if any files were generated
        if (empty($generated_files)) {
            echo "No certificates were generated. Please check the errors above.";
            exit;
        }
    }
    // Create ZIP file
    $zip = new ZipArchive();
    $zip_file = $pdf_dir . 'Certificates_' . time() . '.zip';

    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($generated_files as $file) {
            if (file_exists($file)) {
                $zip->addFile($file, basename($file));
            }
        }
        $zip->close();

        // Check if ZIP was created successfully
        if (!file_exists($zip_file)) {
            echo 'Failed to create zip file.';
            exit;
        }
    } else {
        echo 'Failed to create zip file.';
        exit;
    }

    // Clean up individual PDF files
    foreach ($generated_files as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Download ZIP file
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=Certificates_' . time() . '.zip');
    header('Content-Length: ' . filesize($zip_file));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    readfile($zip_file);

    // Clean up ZIP file after download
    unlink($zip_file);
    exit;
} else {
    echo "No records found!";
}
