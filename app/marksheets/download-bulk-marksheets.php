<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require '../../includes/db-config.php';
require '../../includes/helpers.php';
session_start();

$url = "https://erpglocal.iitseducation.org";
$passFail = "PASS";

use setasign\Fpdi\PdfReader;
use setasign\Fpdi\Fpdi;

ob_end_clean();
require_once('../../extras/TCPDF/tcpdf.php');
require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');
require '../../extras/vendor/autoload.php';
require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
require('../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');

// echo "<pre>"; print_r($_POST['semester']); exit;
$check = '../../assets/img/forms/checked.png';
$sqlQuery = '';
if (isset($_POST['course_type_id']) && !empty($_POST['course_type_id'])) {
    $course_id = $_POST['course_type_id'];
    $sqlQuery .= "AND Students.Course_ID = '$course_id'";
}

if (isset($_POST['course_id']) && !empty($_POST['course_id'])) {
    $sub_course_id = $_POST['course_id'];
    $sqlQuery .= " AND Students.Sub_Course_ID = '$sub_course_id'";
}
$x_cor=16;
if (isset($_POST['student_id']) && !empty($_POST['student_id'])) {
    $student_id_array = explode(",", $_POST['student_id']);
    foreach ($student_id_array as &$en_no) {
        $en_no = "'" . trim($en_no) . "'";
    }
    unset($en_no);
    $student_id = implode(",", $student_id_array);
    $sqlQuery .= " AND Students.Enrollment_No IN ($student_id)";
}

if (isset($_POST['category']) && !empty($_POST['category'])) {
    $sub_course_id = $_POST['category'];
    $sqlQuery .= " AND Students.Duration = '1'";
}


$pdf_dir = '../../uploads/marksheet/';
$export_data = [];
if ($_SESSION['university_id'] == 41) {
    $header = array('Enrollment_No', 'Course', 'Sub-Course', 'Duration', 'Remark');
} else {
    $header = array('Enrollment_No', 'Course', 'Sub-Course', 'Semester', 'Remark');
}
$export_data[] = $header;
$student = $conn->query("SELECT Students.*, Sub_Courses.Min_Duration as total_duration,Sub_Courses.Name as course, Courses.Name as program_Type FROM Students LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID  WHERE Students.Enrollment_No IS NOT NULL $sqlQuery");
// echo($student->num_rows);die;
if ($student->num_rows > 0) {
    while ($row = $student->fetch_assoc()) {
        // echo "<pre>"; print_r($row);die;
        $students_result = $conn->query("SELECT Students.*, Sub_Courses.Min_Duration as total_duration, Modes.Name as mode, Sub_Courses.Name as course, Courses.Name as program_Type, Admission_Sessions.Name as Admission_Session,Admission_Sessions.Exam_Session, Admission_Types.Name as Admission_Type FROM Students LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID LEFT JOIN Modes ON Students.University_ID = Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID  LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.ID = '" . trim($row['ID']) . "'");
        $data = [];
        // $data['remarks'] = "Pass";
        $data = $students_result->fetch_assoc();
        // echo "<pre>"; print_r($data);
        $typoArr = ["th", "st", "nd", "rd", "th", "th", "th", "th", "th"];
        $total_obt = 0;
        $total_max = 0;
        $durations_query = "";
        $min_val = 0;
        $temp_subjects = "";
        $sem_sql = '';
        $scheme_id = NULL;
        $semester = NULL;
        $due_val = '';
        list($month, $year) = explode('-', $data['Admission_Session']);
        $year = (strlen($year) > 2) ? date('y', strtotime("$year-01-01")) : $year;
        $adm_session = '20' . $year . '-' . '20' . $year + 1;
        //  echo('<pre>');print_r($adm_session);die;
        //   echo('<pre>');print_r($data['University_ID'] == '20');
        // if (isset($_POST['semester']) && !empty($_POST['semester'])) {
        //     // echo('<pre>');print_r($_POST);die;
        //     list($scheme_id, $semester) = explode('|', $_POST['semester']);
        //     $data['Duration'] = $semester;
        //     $sem_sql = " AND Syllabi.Semester = '$semester' AND Syllabi.Scheme_ID = $scheme_id";

        //     $durations_query = " AND Syllabi.Semester = " . $semester;
        //      echo('<pre>');
        //      print_r($_POST);
        //     print_r($sem_sql.'<br>');
        //     print_r($durations_query);die;
        // } else
        if ($data['University_ID'] == '20') {
            // echo('hello');die;
            // echo('<pre>');print_r($data['Duration']);die;
            $bovc_sem = $_POST['semester'];
            $sem_sql = " AND Syllabi.Semester = " . $bovc_sem;
            $durations_query = " AND Syllabi.Semester = " . $bovc_sem;
            //  echo('<pre>');
            //  print_r($_POST);
            // print_r($sem_sql.'<br>');
            // print_r($durations_query);die;
        } else if ($data['University_ID'] == '41') {
            $category = strtolower($data['Course_Category']);
            $duration = $data['Duration'];
            // echo('<pre>');print_r($category);die;
            if ($category == 'certified' && ($duration == 6 || $duration == 11)) {
                $due_val = $duration . '/certified';
            } elseif ($category == 'certification') {
                $due_val = $duration . '/certification';
            } elseif ($category == 'advance_diploma' || $duration == '11/advance-diploma') {
                $due_val = '11/advanced';
            } elseif ($category == 'post_graduate') {
                $due_val = '24/post-graduate';
            } else {
                $due_val = $duration;
            }
            $sem_sql = " AND Semester = '" . $due_val . "'";
            $durations_query = " AND Syllabi.Semester = '" . $due_val . "'";
        }


        // $exam_date = $conn->query("SELECT m.exam_month,m.exam_year FROM marksheets AS m LEFT JOIN Syllabi  ON m.subject_id = Syllabi.ID WHERE m.enrollment_no = '" . $data['Enrollment_No'] . "' AND Syllabi.Course_ID = " . $data['Course_ID'] . "  AND  Syllabi.Sub_Course_ID = " . $data['Sub_Course_ID'] . " $sem_sql GROUP BY m.enrollment_no");
        // if ($exam_date->num_rows > 0) {
        //     $examArr = $exam_date->fetch_assoc();
        //     if (!empty($examArr['exam_month']) || !empty($examArr['exam_year'])) {
        //         $exam_month = ucwords($examArr['exam_month']) . '-' . $examArr['exam_year'];
        //         list($date_of_issue) = selectExamSessionAndDateOfIssue($data['Admission_Session'],$semester,'date');
        //     } else {

        //         //$exam_month = ucwords($data['Exam_Session']);
        //     }
        // }
        list($date_of_issue, $exam_month) = selectExamSessionAndDateOfIssue($data['Admission_Session'], $semester, 'date&exam');
        $exam_month = "April-2025";
        // echo "<pre>";
        // print_r(selectExamSessionAndDateOfIssue($data['Admission_Session'],$semester,'date&exam')); die;

        $temp_subjects = $conn->query("SELECT Paper_Type,marksheets.id as markID,marksheets.date_of_issue,marksheets.exam_month,marksheets.exam_year, marksheets.obt_marks_ext, marksheets.obt_marks_int,marksheets.obt_marks,marksheets.status,marksheets.remarks,marksheets.Created_At,Syllabi.Code,Syllabi.Name as subject_name,Syllabi.Min_Marks, Syllabi.Max_Marks, Syllabi.Credit FROM marksheets LEFT JOIN Syllabi ON marksheets.subject_id = Syllabi.ID WHERE enrollment_no = '" . $data['ID'] . "' AND Sub_Course_ID = '" . $row['Sub_Course_ID'] . "'  $sem_sql ORDER BY Syllabi.Code ASC");
        $data['marks'] = array();
        $temp_subject = [];
        $total_obt = 0;
        $total_max = 0;
        $resultPublishDay = "";
        // $o_int_marks = [];
        // IF here the data is came that means this sem result is came if not then this sem result not present
        if ($temp_subjects->num_rows > 0) {

            while ($temp_subject = $temp_subjects->fetch_assoc()) {
                  $markID[]=$temp_subject['markID'];
                  if(!empty($temp_subject['date_of_issue']) && $temp_subject['date_of_issue']!=null){
                     $date_issue_exst[]=$temp_subject['date_of_issue']; 
                  }
                  
                //   echo('<pre>');print_r($markID);exit;
                if ($temp_subject != null) {
                    $resultPublishDay = date("d/m/Y", strtotime($temp_subject['Created_At']));

                    $temp_subject['not_empty_obt_ext_marks'] = "";
                    if (!empty($temp_subject['obt_marks_ext'])) {
                        $temp_subject['not_empty_obt_ext_marks'] = $temp_subject['obt_marks_ext'];
                    }

                    $obt_marks_ext = isset($temp_subject['obt_marks_ext']) ? $temp_subject['obt_marks_ext'] : 0;
                    $obt_marks_int = isset($temp_subject['obt_marks_int']) ? $temp_subject['obt_marks_int'] : 0;

                    $obt_marks_ext = ($temp_subject['obt_marks_ext'] == 'AB') ? 'AB' : $temp_subject['obt_marks_ext'];
                    $obt_marks_int = ($temp_subject['obt_marks_int'] == 'AB') ? 'AB' : $temp_subject['obt_marks_int'];

                    if ($obt_marks_ext != 'AB' && $obt_marks_int != 'AB') {
                        $total_obt = $total_obt + intval($obt_marks_ext) + intval($obt_marks_int);
                        // echo('<pre>');print_r($total_obt);
                    } else {
                        $total_obt = (int) $total_obt + (int) $obt_marks_ext + (int) $obt_marks_int;
                    }
                    $temp_subject['remarks_status'] = "Pass";

                    if ($data['University_ID'] == 41) {
                        if ($total_obt < $min_val || $temp_subject['obt_marks_ext'] == 0 || $temp_subject['obt_marks_ext'] == 'AB') {
                            $temp_subject['remarks_status'] = "FAIL";
                        }
                        $total_max = $total_max + $temp_subject['Max_Marks'];

                        if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_ext'] == 'AB') {
                            $temp_subject['obt_marks'] = 'AB';
                        } else if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_int'] != 'AB') {
                            $obtintmarks = isset($temp_subject['obt_marks_int']) ? intval($temp_subject['obt_marks_int']) : 0;
                            $temp_subject['obt_marks'] = $obtintmarks + 0;
                        } else if ($temp_subject['obt_marks_ext'] != 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                            $temp_subject['obt_marks'] = intval($temp_subject['obt_marks_ext']) + intval($temp_subject['obt_marks_int']);
                        }
                    } else {
                        if ($total_obt <= $min_val || $temp_subject['obt_marks_ext'] == 0 || $temp_subject['obt_marks_ext'] == 'AB') {
                            $temp_subject['remarks_status'] = "FAIL";
                        }
                        $tempTotalMarks = $temp_subject['Max_Marks'] + $temp_subject['Min_Marks'];
                        $total_max = $total_max + $tempTotalMarks;

                        if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_ext'] == 'AB') {
                            $temp_subject['obt_marks'] = 'AB';
                        } else if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_int'] != 'AB') {
                            $obtintmarks = isset($temp_subject['obt_marks_int']) ? intval($temp_subject['obt_marks_int']) : 0;
                            $temp_subject['obt_marks'] = $obtintmarks + 0;
                        } else if ($temp_subject['obt_marks_int'] == 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                            $obtextmarks = isset($temp_subject['obt_marks_ext']) ? intval($temp_subject['obt_marks_ext']) : 0;
                            $temp_subject['obt_marks'] = $obtextmarks + 0;
                        } else if ($temp_subject['obt_marks_ext'] != 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                            $temp_subject['obt_marks'] = intval($temp_subject['obt_marks_ext']) + intval($temp_subject['obt_marks_int']);
                        }
                    }
                    // end remarks_status

                    if ($temp_subject['Min_Marks'] == 40 && $temp_subject['Max_Marks'] == 60) {
                        $min_val = ($temp_subject['Min_Marks'] + $temp_subject['Max_Marks']) * 40 / 100;
                    } else if ($temp_subject['Max_Marks'] == 100 && $temp_subject['Min_Marks'] == 40) {
                        $min_val = $temp_subject['Min_Marks'];
                    } else if ($temp_subject['Max_Marks'] == 100 && $temp_subject['Min_Marks'] == 100) {
                        $min_val = ($temp_subject['Max_Marks']) * 40 / 100;
                    }
                    if ($temp_subject['Paper_Type'] == 'Practical' && $temp_subject['obt_marks_int'] == 0 && $temp_subject['Min_Marks'] == 0) {
                        $temp_subject['Min_Marks'] = '-';
                        $temp_subject['obt_marks_int'] = "-";
                        $temp_subject['remarks_status'] = "Pass";
                    }

                    $temp_subject['remarks'] = $temp_subject['obt_marks_ext'] < $temp_subject['Min_Marks'] ? "Fail" : "Pass";
                    $data['marks'][] = $temp_subject;
                    $data['allremarks'][] = $temp_subject['remarks_status'];
                    $obt_int[] = $temp_subject['obt_marks_int'];
                    $not_empty_ext[] = $temp_subject['not_empty_obt_ext_marks'];
                }
            }

            $percentage = '';
            if (in_array('FAIL', $data['allremarks'])) {
                $data['remarks'] = 'Fail';
                $percentage = '';
            } else {
                $data['remarks'] = 'Pass';
                if ($total_max !== 0) {
                    // echo('<pre>');print_r($total_max);die;
                    $percentage = ($total_obt / $total_max) * 100;
                    $percentage = number_format($percentage, 2) . "%";
                }
            }
            // print_r( $data['remarks']);die;
            $total_subject = totalUloadedSubjectsFunc($conn, $data['University_ID'], $data['ID'], $data['Duration'], "");
            $total_subject_count = count($total_subject);

            if (($total_subject_count !== count($obt_int) || $total_subject_count !== count(array_filter($not_empty_ext))) && $data['University_ID'] == 20) {
                //   echo "<pre>"; print_r($data);
                makeStatus($data, "Result Not Found");
            } else {
                makeStatus($data, "Result Found");
            }


            $data['total_max'] = $total_max;
            $data['total_obt'] = $total_obt;

            $marksWords = ucwords(strtolower(numberToWordFunc($total_obt)));
            $count = $temp_subjects->num_rows;
            $hours = '';
            $total_duration = '';

            if ($data['University_ID'] == 41) {
                $data['mode_type'] = "Duration";
                $data['university_name'] = "School of Vocational And Skill";
                $total_duration = $data['Duration'];
                $data['Durations'] = $data['Duration'];

                $fullCourse = trim($data['course']);
                // Example: "Advanced Certified Skill Diploma in Psychosexual Counselling (11 Months)"

                // Default values
                $course_name = $fullCourse;
                $skillduration = '';
                $duration_text = '';

                // 1️⃣ Extract course name and duration text (if parentheses exist)
                if (preg_match('/^(.*?)\s*\((.*?)\)$/', $fullCourse, $matches)) {
                    $course_name = trim($matches[1]);
                    $skilldurtion = trim($matches[2]); // e.g. "11 Months"
                }

                // // 2️ Determine skill duration based on course name keywords
                // if (stripos($course_name, '11') !== false && stripos($course_name, 'adv') !== false) {
                //     $skillduration = '2'; // 11 + Adv
                // } elseif (stripos($course_name, '11') !== false) {
                //     $skillduration = '1'; // only 11
                // } elseif (stripos($course_name, '6') !== false) {
                //     $skillduration = '6';
                // } elseif (stripos($course_name, '3') !== false) {
                //     $skillduration = '3';
                // } else {
                //     // fallback if nothing matched
                //     $skillduration = $duration_text;
                // }

                // // 3️ Map skill duration to program details
                // if ($skillduration == "3") {
                //     $durations = "Certification Course";
                //     $hours = 160;
                //     $data['skillDurations'] = "3 Months";
                // } elseif ($skillduration == "6") {
                //     $durations = "Certified Skill Diploma";
                //     $hours = 320;
                //     $data['skillDurations'] = "6 Months";
                // } elseif ($skillduration == "2") {
                //     $durations = "Advanced Certification Skill Diploma";
                //     $hours = 960;
                //     $data['skillDurations'] = "11 Months";
                // } elseif ($skillduration == "1") {
                //     $durations = "Certified Skill Diploma";
                //     $hours = 960;
                //     $data['skillDurations'] = "11 Months";
                // } else {
                //     // fallback if duration doesn't match any known pattern
                //     $durations = "Unknown Duration";
                //     $hours = 'NA';
                //     $data['skillDurations'] = $duration_text ?: 'NA';
                // }
                //   echo('<pre>');print_r($data['skillDurations']);die;
            } else {
                $data['university_name'] = "School of Vocational And Skill";
                $data['mode_type'] = "Semester";
                $durations = "";
            }

            $data['duration_val'] = $durations;

            $durMonthYear = "";
            if ($data['mode'] == "Monthly") {
                $durMonthYear = " Months";
            } elseif ($data['mode'] == "Sem") {
                $durMonthYear = " Semester";
            } else {
                $durMonthYear = " Years";
            }

            // if ($data['University_ID'] == 41) {
            //     $data['durMonthYear'] = $data['Durations'] . $durMonthYear . '/ ' . $hours . "Hours";
            // } else {
            //     $data['durMonthYear'] = $data['Duration'] . $typoArr[$data['Duration']];
            // }
            if ($data['University_ID'] == 20) {
                //  $data['durMonthYear'] = $data['Duration'] . $typoArr[$data['Duration']];
                $data['durMonthYear'] = $bovc_sem . $typoArr[$bovc_sem];
                // echo('<pre>');print_r($data['durMonthYear'] );die;
            }
            $student_doc_query = "SELECT Location FROM Student_Documents WHERE Student_ID = '" . $data['ID'] . "' AND Type = 'Photo'";
            $student_doc = $conn->query($student_doc_query);
            $student_doc = $student_doc->fetch_assoc();
            $photo = $student_doc['Location'];
            $data['Photo'] = $url . $photo;
            // with background start
            // $pdf = new Fpdi();
            // $templateFile = 'mdu-result.pdf';
            // $pageCount = $pdf->setSourceFile($templateFile);
            // $tplIdx = $pdf->importPage(1);
            // $pdf->addPage('P'); 
            // $pdf->useTemplate($tplIdx, 0, 0, 210, 297);
            // end with background
            $pdf = new Fpdi();
            $pdf->addPage('P');
            $fullCourse = $data['course']; // "Hospital Administration (11 Months)"
            //   echo('<pre>');print_r($data['skillDurations']);die;
                if (empty($date_issue_exst) || $date_issue_exst == "" || count($date_issue_exst)!=count($markID)) {
    // echo('<pre>');print_r('dasdsa');die;
    $date = DateTime::createFromFormat('M-Y', $data['Admission_Session']);
    $date->modify('+' . ($skilldurtion - 1) . ' months');
    $completeMonthYear = $date->format('M-Y');
    $date = DateTime::createFromFormat('M-Y', $completeMonthYear);
    $date->modify('+1 months');
    $date->setDate($date->format('Y'), $date->format('m'), 8);
    if ($date->format('N') == 6) {
        $date->modify('+2 days');
    } elseif ($date->format('N') == 7) {
        $date->modify('+1 day');
    }
    $dateOfIssue = $date->format('d/m/Y');
   
    if (!empty($markID)) {
        foreach($markID as $IDmark)
      $update_cert = $conn->query("UPDATE marksheets 
                             SET date_of_issue = '$dateOfIssue' 
                             WHERE id = '$IDmark'");
    }
    
} else {
    $dateOfIssue = $date_issue_exst[0];  // database se aayegi
}

            $admissionSession = $data['Admission_Session'];
            // echo('<pre>');print_r($data['Admission_Session']);die;
            // Create date
            $date = DateTime::createFromFormat('M-Y', $admissionSession);
            // echo $admissionSession;die;
            //  Inclusive logic (current month included)
            $date->modify('+' . ($skilldurtion - 1) . ' months');

            // Final result
            $completeMonthYear = $date->format('M-Y');
            // echo('<pre>');print_r($completeMonthYear);die;
            if ($data['University_ID'] == 41) {
                $pdf->SetFont("times", 'B', 9);
                // $pdf->SetXY(15, 45);
                // $pdf->Cell(0, 0, 'Statement of Marks', 0, 0, 'C', 0);
                $pdf->SetXY(30, 43);
                $pdf->SetFont("times", 'B', 9.5);
                // echo('<pre>');print_r($skilldurtion);die;
                $pdf->Cell(0, 0,  $course_name, 0, 0, 'C', 0);
                $pdf->SetFont("times", 'B', 9);
                $pdf->SetXY(25, 49);
                $pdf->Cell(0, 0, 'SESSION : ' . strtoupper($adm_session) . '', 0, 0, 'C', 0);
                $pdf->SetXY(16.1, 55);
                $pdf->Cell(107, 5, strtoupper('Name : ') . strtoupper($data['First_Name']) . ' ' . strtoupper($data['Middle_Name']) . ' ' . strtoupper($data['Last_Name']), 0, 0, 'L', 0);
                $pdf->SetXY(123, 55);
                $pdf->Cell(70, 5, strtoupper('Enrollment No : ') . $data['Enrollment_No'], 0, 0, 'L', 0);
                $pdf->SetXY(16.1, 60);
                $pdf->Cell(107, 5, strtoupper('School : ') . ucwords($data['university_name']), 0, 0, 'L', 0);
                $pdf->SetXY(123, 60);
                $pdf->Cell(70, 5, strtoupper($data['mode_type'] . ' ' . ':' . ' ' . $skilldurtion), 0, 0, 'L', 0);

                // skill
                $pdf->SetFont('times', 'B', 9);
                $cellWidth = 20;
                $cellHeight = 10;
                $pdf->SetXY(16.1, 67.5);
                $pdf->MultiCell(27, 10, 'Subject Code', 'TLB', 'C');
                $pdf->SetXY(43, 67.5);
                $pdf->MultiCell(70.3, 10, 'Subject Name ', 'TLB', 'C');
                $pdf->SetXY(113.3, 67.5);
                $pdf->MultiCell(18.3, 5, 'Obtained Marks', 'TLB', 'C');
                $pdf->SetXY(131, 67.5);
                $pdf->MultiCell(20, 10, 'Min. Marks', 'TLB', 'C');
                $pdf->SetXY(151, 67.5);
                $pdf->MultiCell(19.8, 10, 'Max. Marks', 'TLB', 'C');
                $pdf->SetXY(171, 67.5);
                $pdf->MultiCell(22, 10, 'Remarks', 1, 'C');
                $pdf->SetXY(10, 67.5);
                $pdf->Ln();
                $pdf->SetFont('times', '', 10);
                $x_cor = 16;
                $pdf->SetX($x_cor);
                $remark_status = [];
                $total_get_marks = 0;
                // foreach ($data['marks'] as $mark) {
                //     $pdf->SetX($x_cor);
                //     if (strlen(trim($mark['subject_name'])) > 30) {
                //         $cellHeight = 20;
                //     } else {
                //         $cellHeight = 10;
                //     }


                //     $mark['subject_name'] = str_replace("\xC2\xA0", ' ', $mark['subject_name']);
                //     $mark['subject_name'] = preg_replace('/\s+/', ' ', $mark['subject_name']);
                //     $mark['subject_name'] = strtoupper(utf8_encode($mark['subject_name']));
                //     if (strlen($mark['subject_name']) > 80) {
                //         $pdf->Cell(27, 8, $mark['Code'], 'LR', 0, 'L');
                //         $nameParts = explode("\n", wordwrap(trim($mark['subject_name']), 80));
                //         $x = $pdf->GetX();
                //         $y = $pdf->GetY();

                //         //  ONLY FIRST SUBJECT
                //         if ($isFirstSubject) {
                //             $pdf->SetXY($x, $y + 1.2);
                //             $isFirstSubject = false; //  important
                //         }
                //         $pdf->MultiCell(169.3, 3.5, $nameParts[0] . chr(10) . $nameParts[1], 0, 0, 0, 'L');
                //         $x = $pdf->GetX();
                //         $y = $pdf->GetY();
                //         $pdf->SetXY($x + 202.3, $y - 8);
                //         $pdf->Cell($cellWidth - 2.3, 8, $mark['obt_marks'], 'LR', 0, 'C');
                //         $pdf->Cell($cellWidth - 5, 8, $mark['Min_Marks'], 'R', 0, 'C');
                //         $pdf->Cell($cellWidth - 5, 8, $mark['Max_Marks'], 'R', 0, 'C');
                //         $pdf->Cell(22, 8, $mark['remarks'], 'R', 0, 'C');
                //     } else {
                //         // echo "<pre>";
                //         // echo $mark['subject_name'];
                //         $pdf->Cell(27, 6.5, $mark['Code'], 'LR', 0, 'L');
                //         $pdf->Cell(169.3, 6.5, trim($mark['subject_name']), 'R', 0, 'L');

                //         $pdf->Cell($cellWidth - 2.3, 6.5, $mark['obt_marks'], 'R', 0, 'C');
                //         $pdf->Cell($cellWidth - 5, 6.5, $mark['Min_Marks'], 'R', 0, 'C');
                //         $pdf->Cell($cellWidth - 5, 6.5, $mark['Max_Marks'], 'R', 0, 'C');
                //         $pdf->Cell(22, 6.5, $mark['remarks'], 'R', 0, 'C');
                //     }
                //     $pdf->Ln();
                // }
                    foreach ($data['marks'] as $mark) {
                    $pdf->SetX($x_cor);
                    if (strlen(trim($mark['subject_name'])) > 30) {
                        $cellHeight = 20;
                    } else {
                        $cellHeight = 10;
                    }

 $totalMinMarsk +=$mark['Min_Marks'];
                    $mark['subject_name'] = str_replace("\xC2\xA0", ' ', $mark['subject_name']);
                    $mark['subject_name'] = preg_replace('/\s+/', ' ', $mark['subject_name']);
                    $mark['subject_name'] = strtoupper(utf8_encode($mark['subject_name']));
                    // ECHO strlen($mark['subject_name'])."<br>".$mark['subject_name']."<br>";
                    if (strlen($mark['subject_name']) > 30) {
                        $pdf->Cell(27, $cellHeight - 10, $mark['Code'], 'LB', 0, 'L');
                        $nameParts = explode("\n", wordwrap(trim($mark['subject_name']), 30));

                        $pdf->MultiCell(70.3, 5, $nameParts[0] . chr(10) . $nameParts[1], 'LB', 0, 0, 'L');
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->SetXY($x + 103.3, $y - 10);
                        $pdf->Cell($cellWidth - 2.3, $cellHeight - 10, $mark['obt_marks'], 'LB', 0, 'C');
                        $pdf->Cell($cellWidth, $cellHeight - 10, $mark['Min_Marks'], 'LB', 0, 'C');
                        $pdf->Cell($cellWidth, $cellHeight - 10, $mark['Max_Marks'], 'LB', 0, 'C');
                        $pdf->Cell(22, $cellHeight - 10, $mark['remarks'], 'LBR', 0, 'C');
                    } else {
                        // echo "<pre>";
                        // echo $mark['subject_name'];
                        $pdf->Cell(27, $cellHeight, $mark['Code'], 'LB', 0, 'L');
                        $pdf->Cell(70.3, $cellHeight, trim($mark['subject_name']), 'LB', 0, 'L');
                        $pdf->Cell($cellWidth - 2.3, $cellHeight, $mark['obt_marks'], 'LB', 0, 'C');
                        $pdf->Cell($cellWidth, $cellHeight, $mark['Min_Marks'], 'LB', 0, 'C');
                        $pdf->Cell($cellWidth, $cellHeight, $mark['Max_Marks'], 'LB', 0, 'C');
                        $pdf->Cell(22, $cellHeight, $mark['remarks'], 'LBR', 0, 'C');
                    }
                    $pdf->Ln();
                }
                    //   $pdf->SetX($x_cor);
                    //     $pdf->Cell(27, $cellHeight, 'HA-103', 'LB', 0, 'L');
                    //     $pdf->MultiCell(70.3, 5, 'COMPUTERIZED HOSPITAL MANAGEMENT', 'LB', 0,0, 'L');
                    //      $x = $pdf->GetX();
                    //     $y = $pdf->GetY();
                    //     $pdf->SetXY($x + 103.3, $y - 10);
                    //     $pdf->Cell($cellWidth - 2.3, $cellHeight, '90', 'LB', 0, 'C');
                    //     $pdf->Cell($cellWidth, $cellHeight, '80', 'LB', 0, 'C');
                    //     $pdf->Cell($cellWidth, $cellHeight, '100', 'LB', 0, 'C');
                    //     $pdf->Cell(22, $cellHeight, 'pass', 'LBR', 0, 'C');
                    //     $pdf->Ln();
                    //     $pdf->SetX($x_cor);
                    //     $pdf->Cell(27, $cellHeight, 'HA-103', 'LB', 0, 'L');
                    //     $pdf->MultiCell(70.3, 5, 'COMPUTERIZED HOSPITAL MANAGEMENT', 'LB', 0,0, 'L');
                    //      $x = $pdf->GetX();
                    //     $y = $pdf->GetY();
                    //     $pdf->SetXY($x + 103.3, $y - 10);
                    //     $pdf->Cell($cellWidth - 2.3, $cellHeight, '90', 'LB', 0, 'C');
                    //     $pdf->Cell($cellWidth, $cellHeight, '80', 'LB', 0, 'C');
                    //     $pdf->Cell($cellWidth, $cellHeight, '100', 'LB', 0, 'C');
                    //     $pdf->Cell(22, $cellHeight, 'pass', 'LBR', 0, 'C');
                    //     $pdf->Ln();
                    //     $pdf->SetX($x_cor);
                    //     $pdf->Cell(27, $cellHeight, 'HA-103', 'LB', 0, 'L');
                    //     $pdf->MultiCell(70.3, 5, 'COMPUTERIZED HOSPITAL MANAGEMENT', 'LB', 0,0, 'L');
                    //      $x = $pdf->GetX();
                    //     $y = $pdf->GetY();
                    //     $pdf->SetXY($x + 103.3, $y - 10);
                    //     $pdf->Cell($cellWidth - 2.3, $cellHeight, '90', 'LB', 0, 'C');
                    //     $pdf->Cell($cellWidth, $cellHeight, '80', 'LB', 0, 'C');
                    //     $pdf->Cell($cellWidth, $cellHeight, '100', 'LB', 0, 'C');
                    //     $pdf->Cell(22, $cellHeight, 'pass', 'LBR', 0, 'C');
                // $pdf->Cell(27,  $cellHeight - 10, 'HA-103', 'LB', 0, 'L');
                // $pdf->Cell(169.3, 5, 'COMPUTERIZED HOSPITAL MANAGEMENT', 'LB', 0, 'L');
                // $pdf->Cell($cellWidth - 2.3, 8, '90', 'LB', 0, 'C');
                // $pdf->Cell($cellWidth - 5, 8, '80', 'LB', 0, 'C');
                // $pdf->Cell($cellWidth - 5, 8, '100', 'LB', 0, 'C');
                // $pdf->Cell(22, 8, 'pass', 'LBR', 0, 'C');



               
                // $pdf->Ln();
                 $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->SetXY($x , $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetX($x_cor);
                $pdf->Cell(27, 8, '', 'LTBR', 0, 'L');
                $pdf->Cell(70.3, 8, 'Total (if Passed)', 'TBR', 0, 'L');
                $pdf->Cell(17.7, 8, $data['total_obt'] , 'TBR', 0, 'C');
                  $pdf->Cell($cellWidth, 8, $totalMinMarsk, 'TBR', 0, 'C');
                  $pdf->Cell($cellWidth, 8,  $data['total_max'], 'TBR', 0, 'C');
                // $pdf->Cell(42, 8, $data['remarks'] . ' (' . $percentage . ')', 'TBR', 0, 'C');
                  $pdf->Cell(22, 8, $data['remarks'] , 'TBR', 0, 'C');

                //   $pdf->SetFont('Arial', '', 10);
                //   if($data['remarks'] =='Pass'){
                //       $pdf->Image($check, 15, 237, 3, 3);
                //       $pdf->SetFont('times', 'B', 12);
                //       $pdf->SetXY(40, 228.6);
                //       $pdf->MultiCell(40, 10, $data['remarks'].' ('.$percentage.')', 0, 'C');
                //   }
                //   else{
                //   $pdf->Image($check, 25, 237, 3, 3);}
                //   echo('<pre>');print_r($data['marks']);
                // Filter subjects where remarks_status = 'Fail'
                // $failedSubjects = array_filter($data['marks'], function($mark) {
                //     return isset($mark['remarks']) && strtolower(trim($mark['remarks'])) === 'fail';
                // });

                // $failedcode = '';

                // if (!empty($failedSubjects)) {
                //     // Combine all failed codes into a single comma-separated string
                //     $failedCodes = array_column($failedSubjects, 'Code');
                //     $failedcode = implode(', ', $failedCodes);
                // }
                // // echo('<pre>');print_r($failedcode);
                //                           $pdf->SetXY(40, 228.6);
                //                           $pdf->MultiCell(40, 10, '('.$failedcode.')', 0, 'C');
                //                       }




                //   echo('<pre>');print_r($data);
                // if ($count > 8) {
                //     // echo "<pre>";
                //     // print_r($remark_status);

                //     //more thwn 9 subject
                //     $pdf->SetXY(16, 200.4);
                //     $pdf->SetFont('times', 'B', 9);
                //     $pdf->Cell(0, 0, 'Aggregate Marks', 0, 0, 'C', 0);
                //     $pdf->SetXY(16, 235.4);
                //     $pdf->Cell(65, 8, 'Marks', 'TL', 1, 'C', 0);
                //     $pdf->SetXY(81, 235.4);
                //     $pdf->Cell(35, 8, 'Grand Total', 'TL', 1, 'C', 0);
                //     $pdf->SetXY(116, 235.4);
                //     $pdf->Cell(35, 8, 'Result', 'LTB', 1, 'C', 0);
                //     $pdf->SetXY(151, 235.4);
                //     $pdf->Cell(42, 8, 'Percentage', 1, 1, 'C', 0);
                //     $pdf->SetFont('times', '', 9);
                //     $pdf->SetXY(16, 243.4);
                //     $pdf->Cell(65, 8, ' Obtained Mark', 'TL', 1, 'C', 0);
                //     $pdf->SetXY(81, 243.4);
                //     $pdf->Cell(35, 8, $data['total_obt'], 'TL', 1, 'C', 0);
                //     $pdf->SetXY(116, 243.6);
                //     $pdf->Cell(35, 8, $data['remarks'], 'LR', 1, 'C', 0);
                //     $pdf->SetXY(151, 243.6);
                //     $pdf->Cell(42, 8, $percentage, 'R', 1, 'C', 0);
                //     $pdf->SetXY(16, 251.3);
                //     $pdf->Cell(65, 8, 'Maximum Mark', 'TLB', 1, 'C', 0);
                //     $pdf->SetXY(81, 251.3);
                //     $pdf->Cell(35, 8, $data['total_max'], 'LBT', 1, 'C', 0);
                //     $pdf->SetXY(116, 251.3);
                //     $pdf->Cell(35, 8, '', 'LRB', 'LB', 'C', 0);
                //     $pdf->SetXY(151, 251.3);
                //     $pdf->Cell(42, 8, '', 'RB', 'RB', 'C', 0);
                // } else {

                //     //less then 9 subject
                //     $pdf->SetXY(16, 182);
                //     $pdf->SetFont('times', 'B', 9);
                //     $pdf->Cell(0, 0, 'Aggregate Marks', 0, 0, 'C', 0);
                //     $pdf->SetXY(16, 185);
                //     $pdf->Cell(65, 8, 'Marks', 'TL', 1, 'C', 0);
                //     $pdf->SetXY(81, 185);
                //     $pdf->Cell(35, 8, 'Grand Total', 'TL', 1, 'C', 0);
                //     $pdf->SetXY(116, 185);
                //     $pdf->Cell(35, 8, 'Result', 'LTB', 1, 'C', 0);
                //     $pdf->SetXY(151, 185);
                //     $pdf->Cell(42, 8, 'Percentage', 1, 1, 'C', 0);
                //     $pdf->SetFont('times', '', 9);
                //     $pdf->SetXY(16, 193);
                //     $pdf->Cell(65, 8, ' Obtained Mark', 'TL', 1, 'C', 0);
                //     $pdf->SetXY(81, 193);
                //     $pdf->Cell(35, 8, $data['total_obt'], 'TL', 1, 'C', 0);
                //     $pdf->SetXY(116, 193);
                //     $pdf->Cell(35, 8, $data['remarks'], 'TLR', 1, 'C', 0);
                //     $pdf->SetXY(151, 193);
                //     $pdf->Cell(42, 8, $percentage, 'TR', 1, 'C', 0);
                //     $pdf->SetXY(16, 201.4);
                //     $pdf->Cell(65, 8, 'Maximum Mark', 'TLB', 1, 'C', 0);
                //     $pdf->SetXY(81, 201.4);
                //     $pdf->Cell(35, 8, $data['total_max'], 'LBT', 1, 'C', 0);
                //     $pdf->SetXY(116, 201.4);
                //     $pdf->Cell(35, 8, '', 'LRB', 'LB', 'C', 0);
                //     $pdf->SetXY(151, 201.4);
                //     $pdf->Cell(42, 8, '', 'RB', 'RB', 'C', 0);
                // }
            

 
// echo('<pre>');
// print_r($data['Admission_Session']);
// print_r($dateOfIssue);die;

               $pdf->SetXY(37.5, 224);
               $pdf->MultiCell(27, 10, $dateOfIssue, 0, 'L');
               $pdf->SetAutoPageBreak(true);
            } else {
                // Set the Grade and grade value
                $total_obt_grade_value = 0;
                $total_credit = 0;
                // echo "<pre>";
                // print_r($data['marks']);die;
                foreach ($data['marks'] as $key => $value) {
                    if ($value['remarks_status'] == 'FAIL') {
                        $data['marks'][$key]['grade'] = ($value['obt_marks'] == 'AB') ? 'S' : 'F';
                        $data['marks'][$key]['grade_value'] = '0';
                    } else {
                        $grandTotal = (int)$value['Min_Marks'] + (int)$value['Max_Marks'];
                        $student_obt_mark = $value['obt_marks'];
                        $student_obt_per = round(($student_obt_mark / $grandTotal) * 100, 2);
                        if ($student_obt_per > 90) {
                            $data['marks'][$key]['grade'] = 'O';
                            $data['marks'][$key]['grade_value'] = '10';
                        } elseif ($student_obt_per > 80 && $student_obt_per <= 90) {
                            $data['marks'][$key]['grade'] = 'A+';
                            $data['marks'][$key]['grade_value'] = '9';
                        } elseif ($student_obt_per > 70 && $student_obt_per <= 80) {
                            $data['marks'][$key]['grade'] = 'A';
                            $data['marks'][$key]['grade_value'] = '8';
                        } elseif ($student_obt_per > 60 && $student_obt_per <= 70) {
                            $data['marks'][$key]['grade'] = 'B+';
                            $data['marks'][$key]['grade_value'] = '7';
                        } elseif ($student_obt_per > 55 && $student_obt_per <= 60) {
                            $data['marks'][$key]['grade'] = 'B';
                            $data['marks'][$key]['grade_value'] = '6';
                        } elseif ($student_obt_per > 50 && $student_obt_per <= 55) {
                            $data['marks'][$key]['grade'] = 'C';
                            $data['marks'][$key]['grade_value'] = '5';
                        } elseif ($student_obt_per >= 40 && $student_obt_per <= 50) {
                            $data['marks'][$key]['grade'] = 'P';
                            $data['marks'][$key]['grade_value'] = '4';
                        } else {
                            $data['marks'][$key]['grade'] = 'F';
                            $data['marks'][$key]['grade_value'] = '0';
                        }
                    }
                    $total_obt_grade_value += intval($data['marks'][$key]['grade_value'] * $data['marks'][$key]['Credit']);
                    $total_credit += intval($data['marks'][$key]['Credit']);
                }
                $data['SGPA'] = number_format($total_obt_grade_value / $total_credit, 2);
                // $total_course_dur = (json_decode($data['total_duration'],true))[0];
                $decoded_duration = json_decode($data['total_duration'], true);
                // echo('<pre>');print_r($data['total_duration']);die;
                if (is_array($decoded_duration)) {
                    $total_course_dur = $decoded_duration[0] ?? null; // or provide default
                } else {
                    $total_course_dur = $decoded_duration;
                }
                // echo('<pre>');
                // print_r($data['Duration'].'<br>');die;
                // print_r($data['Duration']);die;
                // 
                $checkLastSem = ($total_course_dur == $bovc_sem) ? true : false;
                $sgpa_record = [];
                if ($checkLastSem) {
                    $sgpa_record = calculateCGPA($data['ID'], $row['Sub_Course_ID'], $bovc_sem);
                    // echo('<pre>');print_r($data['ID']);die;
                    $sgpa_record[] = array(
                        'semester' =>  $bovc_sem,
                        'sgpa' => $data['SGPA'],
                        'total_credit' => $total_credit
                    );
                    // print_r($data['Duration']);die;
                }

                //echo "<pre>"; print_r($sgpa_record); exit;
                // echo "<pre>"; print_r($data); exit;
                if (isset($_REQUEST['marksheet_in_grade'])) {
                    setHeaderGrade($data, $exam_month);
                    $cellHeight = 10;
                    $pdf->SetXY(16, 67.5);
                    $pdf->SetFont('times', 'B', 9);
                    $pdf->MultiCell(35.3, 6, 'Course Code', 'TLB', 'C');
                    $pdf->SetXY(51, 67.5);
                    $pdf->MultiCell(100.5, 6, 'Course Title ', 'TLB', 'C');
                    $pdf->SetXY(151.7, 67.5);
                    $pdf->MultiCell(20, 6, 'Credit', 'TLB', 'C');
                    $pdf->SetXY(171.7, 67.5);
                    $pdf->MultiCell(20.1, 6, 'Grade', 'TLRB', 'C');
                    $pdf->Ln();
                    $pdf->SetFont('times', '', 9);
                    $x_cor = 16;
                    $y_cor = 73.5;
                    $pdf->SetX($x_cor);
                    $pdf->SetY($y_cor);
                    makeGradeMarksheet($data['marks'], $data['SGPA'], $x_cor, $sgpa_record, $data['remarks'],$bovc_sem);
                    $pdf->SetFont('times', '', 9);
                   $pdf->SetXY(37.5, 224);
                    $pdf->MultiCell(27, 10, $dateOfIssue, 0, 'L');
                } else {
                    setHeaderPercentage($data, $exam_month);
                    // Header Part
                     $pdf->SetFont('times', 'B', 10);
                    $cellWidth = 25;
                    $cellHeight = 10;
                    $pdf->SetXY(16.1, 70.5);
                    $pdf->MultiCell(25, 10, 'Subject Code', 'TL', 'C');
                    $pdf->SetXY(41, 70.5);
                    $pdf->MultiCell(76, 10, 'Subject Name ', 'TL', 'C');
                    $pdf->SetXY(117, 70.5);
                    $pdf->MultiCell(25, 10, 'Internal', 'TL', 'C');
                    $pdf->SetXY(142, 70.5);
                    $pdf->MultiCell(25, 10, 'External', 'TL', 'C');
                    $pdf->SetXY(167, 70.5);
                    $pdf->MultiCell(26, 10, 'Total', 'TLR', 'C');
                    $pdf->SetXY(16.1, 80.8);
                    $pdf->MultiCell(25, 10, '', 'LB', 'C');
                    $pdf->SetXY(41, 80.8);
                    $pdf->MultiCell(76, 10, ' ', 'LB', 'C');
                    $pdf->SetXY(117, 80.8);
                    $pdf->MultiCell(12.6, 10, 'Obt', 'TBL', 'C');
                    $pdf->SetXY(129.8, 80.8);
                    $pdf->MultiCell(12, 10, 'Max', 'TBL', 'C');
                    $pdf->SetXY(142, 80.8);
                    $pdf->MultiCell(12.5, 10, 'Obt', 'TBL', 'C');
                    $pdf->SetXY(154.8, 80.8);
                    $pdf->MultiCell(12, 10, 'Max', 'TBL', 'C');
                    $pdf->SetXY(167, 80.8);
                    $pdf->MultiCell(14, 10, 'Obt', 'TBL', 'C');
                    $pdf->SetXY(181, 80.8);
                    $pdf->MultiCell(12, 10, 'Max', 'TLBR', 'C');
                    $pdf->SetXY(10, 80.8);
                    $pdf->Ln();
                    $pdf->SetFont('times', '', 10);
                    $x_cor = 16;
                    $pdf->SetX($x_cor);
                    $remark_statuss = [];

                    makePercentageMarksheet($data['marks'], $x_cor);

                    // Footer Part
                    // $pdf->SetXY(16, 189);
                    // $pdf->SetFont('times', 'B', 9);
                    // $pdf->Cell(0, 0, 'Aggregate Marks', 0, 0, 'C', 0);

                    // $pdf->SetXY(16, 192.2);
                    $pdf->Ln();
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->SetXY($x + 6, $y - 10);
                    $pdf->Cell(81.7, 8, 'Marks', 'TL', 0, 'C', 0);
                    $pdf->Cell(32, 8, 'Grand Total', 'TL', 0, 'C', 0);
                    $pdf->Cell(32, 8, 'Result', 'TL', 0, 'C', 0);
                    $pdf->Cell(31.3, 8, 'Percentage', 'LTR', 1, 'C', 0);
                    // $pdf->Ln();
                    // $x = $pdf->GetX();
                    // $y = $pdf->GetY();
                    $pdf->SetXY($x + 6, $y - 1.7);
                    // $pdf->SetFont('times', '', 9);
                    // $pdf->Cell(65, 8, ' Obtained Mark', 'TL', 1, 'C', 0);
                    // $pdf->Cell(35, 8, $data['total_obt'], 'TL', 1, 'C', 0);
                    // $pdf->Cell(35, 8, $data['remarks'], 'TLR', 1, 'C', 0);
                    // $pdf->Cell(42, 8, $percentage, 'TR', 1, 'C', 0);
                    $pdf->Cell(81.7, 6, ' Obtained Mark', 'TL', 0, 'C', 0);
                    $pdf->Cell(32, 6, $data['total_obt'], 'TL', 0, 'C', 0);
                    $pdf->Cell(32, 6, $data['remarks'], 'TL', 0, 'C', 0);
                    $pdf->Cell(31.3, 6, $percentage, 'LTR', 1, 'C', 0);
                    $pdf->Ln();
                    $pdf->SetXY($x + 6, $y + 4.5);
                    $pdf->Cell(81.7, 6, 'Maximum Mark', 'BL', 0, 'C', 0);
                    $pdf->Cell(32, 6, $data['total_max'], 'BL', 0, 'C', 0);
                    $pdf->Cell(32, 6, '', 'BL', 0, 'C', 0);
                    $pdf->Cell(31.3, 6, '', 'LBR', 1, 'C', 0);

                    $pdf->SetXY(37.5, 224);
                    $pdf->MultiCell(27, 10, $dateOfIssue, 0, 'L');

// $pdf->SetAutoPageBreak(true);
                }
            }
            $pdf->SetXY(20, 212.5);

          
            $filename = $data['Unique_ID'] . "_" . time() . ".pdf";
            // print_r($pdf_dir . $filename);die;
            // $templateFile = 'mduMarksheet.pdf';
            // $pageCount = $pdf->setSourceFile($templateFile);
            // $tplIdx = $pdf->importPage(2);
            // $pdf->addPage('L');
            // // $pdf->useTemplate($tplIdx, 0, 0, 210, 297); 
            // $pdf->useTemplate($tplIdx, 0, 0, 297, 210);
            // echo('<pre>');print_r($markID);die;
            $pdf->Output($pdf_dir . $filename, "I");
            
        } else {
            makeStatus($data, "Result Not Found");
        }
    }

    $filename = 'marksheet_download_status' . date('h m s');
    SimpleXLSXGen::fromArray($export_data)->saveAs($pdf_dir . $filename . '.xlsx');

    $zip = new ZipArchive();
    $zip_file = $pdf_dir . 'Marksheets_' . time() . '.zip';
    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = glob($pdf_dir . '*.{pdf,xlsx}', GLOB_BRACE);
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . basename($zip_file));
        header('Content-Length: ' . filesize($zip_file));
        readfile($zip_file);

        foreach ($files as $file) {
            unlink($file);
        }
        unlink($zip_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create ZIP file.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No record found!']);
}


function makeGradeMarksheet($marks_arr, $sgpa, $x_cor, $sgpa_record, $remark, $bovc_sem)
{

    global $pdf;

    $isFirstSubject = true;
    $cellHeight = 10;

    $totalSubjects = count($marks_arr);
    $currentIndex  = 0;

    foreach ($marks_arr as $mark) {

        $currentIndex++;
        $isLastRow = ($currentIndex === $totalSubjects);

        $pdf->SetX($x_cor);

        $mark['subject_name'] = str_replace("\xC2\xA0", ' ', $mark['subject_name']);
        $mark['subject_name'] = preg_replace('/\s+/', ' ', $mark['subject_name']);
        // $subject=$mark['subject_name'].$mark['subject_name'];
        /* ===================== LONG SUBJECT NAME ===================== */
    //     if (strlen($subject) > 50) {

    //         // $pdf->SetX($x_cor);
    //         $pdf->Cell(35, $cellHeight-2, $mark['Code'], 'L', 0, 'C');
    //         $nameParts = explode("\n", wordwrap($subject, 50));
    //         $pdf->MultiCell(100.7, 4, " " . $nameParts[0] . chr(10) . " " . $nameParts[1], 'L', 0, 0, 'L');
    //         $x = $pdf->GetX();
    //         $y = $pdf->GetY();
    //         $pdf->SetXY(151.8, $y - 10);
    //         $pdf->Cell(20, $cellHeight, $mark['Credit'], 'L', 0, 'C');
    //         $pdf->Cell(20.1, $cellHeight, $mark['grade'], 'LR', 0, 'C');
    //     }
    //     /* ===================== SINGLE LINE SUBJECT ===================== */ 
    //     else {

    //          $pdf->Cell(35,  6, $mark['Code'], $isLastRow ? 'LR' : 'LR', 0, 'C');
    // $pdf->Cell(100.7, 6, " " . $subject, $isLastRow ? 'L' : 'L', 0, 'L');
    // $pdf->Cell(20,  6, $mark['Credit'], $isLastRow ? 'LR' : 'LR', 0, 'C');
    // $pdf->Cell(20.1,  6, $mark['grade'],  $isLastRow ? 'R'  : 'R',  0, 'C');
    //     }
    if (strlen($mark['subject_name']) > 50) {

    $pdf->Cell(35, $cellHeight-1, $mark['Code'], $isLastRow ? 'LBR' : 'LR', 0, 'C');
$x = $pdf->GetX();
    $y = $pdf->GetY();
    $nameParts = explode("\n", wordwrap($mark['subject_name'], 50));
     $topPadding = 1.5; // change this value anytime
    $pdf->SetXY($x, $y + $topPadding);
    $pdf->MultiCell(
        100.7,
        3.5,
        " " . $nameParts[0] . chr(10) . " " . ($nameParts[1] ?? ''),
        $isLastRow ? 'B' : '',
        ''
    );

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->SetXY(151.8, $y - 10);

    $pdf->Cell(20, $cellHeight, $mark['Credit'], $isLastRow ? 'LB' : 'L', 0, 'C');
    $pdf->Cell(20.1, $cellHeight, $mark['grade'], $isLastRow ? 'LRB' : 'LR', 0, 'C');
}
/* ===================== SINGLE LINE SUBJECT ===================== */ 
else {

    $pdf->Cell(35,  6, $mark['Code'], $isLastRow ? 'LB' : 'L', 0, 'C');
    $pdf->Cell(100.7, 6, " " . $mark['subject_name'], $isLastRow ? 'LB' : 'L', 0, 'L');
    $pdf->Cell(20,  6, $mark['Credit'], $isLastRow ? 'LB' : 'L', 0, 'C');
    $pdf->Cell(20.1,  6, $mark['grade'], $isLastRow ? 'LRB' : 'LR', 0, 'C');
}

        $pdf->Ln();
    }
     // Static Data

    /* ===================== SGPA ===================== */
    $pdf->SetFont('times', 'B', 9);
    $y_cor = $pdf->GetY();
    // echo('<pre>');print_r($bovc_sem);die;
    if($bovc_sem !=='6'){
    $pdf->SetX(173, $y_cor);
    $pdf->Cell(20, $cellHeight, 'SGPA : '.$sgpa, 0, 0, 'C');
    // $pdf->SetXY(266.5, $y_cor);
    // $pdf->Cell(20, $cellHeight, $sgpa, 0, 0, 'C');
    }

    /* ===================== SGPA RECORD & RESULT ===================== */
    // echo('<pre>');print_r($sgpa_record);die;
    if (!empty($sgpa_record)) {

        $pdf->SetFont('times', '', 9);
        $y_cor += 0;
        $pdf->SetXY(16, $y_cor);

        $a = 1;
        $total_sgpa_credit = 0;
        $grand_total_credit = 0;
// echo('<pre>');print_r($sgpa_record);die;
        foreach ($sgpa_record as $sgpa_details) {
            //  echo('<pre>');print_r($sgpa_details);exit;
            $total_sgpa_credit += $sgpa_details['sgpa'] * $sgpa_details['total_credit'];
            $grand_total_credit += (int)$sgpa_details['total_credit'];

            $sgpaTotal = number_format($sgpa_details['sgpa'], 2, '.', '');
            $content = " SGPA of Semester " . intToRoman($sgpa_details['semester']) . ":";

            $width = $pdf->GetStringWidth($content);
            $pdf->Cell($width, $cellHeight-3, $content, 0, 0, 'L');

            $pdf->SetFont('times', 'B', 9);
            $pdf->Cell(42 - $width, $cellHeight-3, " $sgpaTotal", 0, 0, 'L');
            $pdf->SetFont('times', '', 9);

            if ($a == 3) {
                $pdf->Ln();
                $pdf->SetX(16);
                $a = 0;
            }
            $a++;
        }
// Move slightly down from SGPA
$pdf->Ln(3);
 $y = $pdf->GetY();

 $pdf->SetY( $y - 18.5);
// Page width minus right margin
$pageWidth = $pdf->GetPageWidth();
$rightMargin = 16;

// RESULT (right aligned)
$pdf->SetFont('times', 'B', 9);
$pdf->SetX($pageWidth - $rightMargin - 60);
$pdf->Cell(60, $cellHeight, "RESULT: " . strtoupper($remark), 0, 0, 'R');


// Next line
$pdf->Ln(4);
// CGPA (right aligned)
$cgpa = round(($total_sgpa_credit / $grand_total_credit), 2);
$pdf->SetX($pageWidth - $rightMargin - 66);
$pdf->Cell(60, $cellHeight, "CGPA: $cgpa", 0, 0, 'R');

        // $pdf->SetFont('times', 'B', 9);
        // $pdf->SetXY(252,128.7);
        // $pdf->Cell(30, $cellHeight, "RESULT: " . strtoupper($remark), 0, 0, 'R');

        // $cgpa = round(($total_sgpa_credit / $grand_total_credit), 2);
        // $pdf->SetXY(231.8, 133);
        // $pdf->Cell(39, $cellHeight, "CGPA: ", 0, 0, 'R');
        // $pdf->SetXY(260.3, 133);
        // $pdf->Cell(20, $cellHeight, $cgpa, 0, 0, 'R');
    }
}


function intToRoman($num)
{
    $mapping = [10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'];
    $result = '';
    foreach ($mapping as $value => $roman) {
        while ($num >= $value) {
            $result .= $roman;
            $num -= $value;
        }
    }
    return $result;
}


function makePercentageMarksheet($marks_arr, $x_cor)
{
    global $pdf;
    $isFirstSubject = true;
    foreach ($marks_arr as $mark) {
        $pdf->SetX($x_cor);
        $cellHeight = (strlen($mark['subject_name']) > 50) ? 10 : 10;
        $remark_statuss[] = $mark['remarks'];
        $mark['subject_name'] = str_replace("\xC2\xA0", ' ', $mark['subject_name']);
        $mark['subject_name'] = preg_replace('/\s+/', ' ', $mark['subject_name']);
        $mark['subject_name'] = utf8_encode($mark['subject_name']);
        $subject=$mark['subject_name'].$mark['subject_name'];
        // if (strlen($mark['subject_name']) > 50) {
        //     $pdf->SetFont('times', '', 9);
        //     $pdf->Cell(25, 7.7, $mark['Code'], 'LR', 0, 'L');
        //     $nameParts = explode("\n", wordwrap($mark['subject_name'], 50));
        //     $x = $pdf->GetX();
        //     $y = $pdf->GetY();


        //     // ✅ ONLY FIRST SUBJECT (run once)
        //     $heightFirst = 0;
        //     if ($isFirstSubject === true) {
        //         $pdf->SetXY($x, $y + 1);
        //         $heightFirst = 1;
        //         $isFirstSubject = false;
        //     }

        //     $pdf->MultiCell(76, ($heightFirst === 1) ? 3 : 3.5, $nameParts[0] . chr(10) . $nameParts[1], 0, 0, 0, 'L');
        //     $x = $pdf->GetX();
        //     $y = $pdf->GetY();
        //     $pdf->SetXY($x + 194.8, $y - 8);
        //     $pdf->Cell(12.8, 8.9, $mark['obt_marks_int'], 'LR', 0, 'C');
        //     $pdf->Cell(12.2, 8.9, $mark['Min_Marks'], 'R', 0, 'C');
        //     $pdf->Cell(12.8, 8.9, $mark['obt_marks_ext'], 'R', 0, 'C');
        //     $pdf->Cell(12.2, 8.9, $mark['Max_Marks'], 'R', 0, 'C');
        //     $pdf->Cell(14, 8.9, $mark['obt_marks_int'] + $mark['obt_marks_ext'], 'R', 0, 'C');
        //     $pdf->Cell(12.3, 9, $mark['Min_Marks'] + $mark['Max_Marks'], 'R', 0, 'C');
        //     // $remark = $mark['obt_marks_ext']<$mark['Min_Marks']?"Fail":"Pass";
        //     // $pdf->Cell(19, 8, $remark, 'R', 0, 'C');
        // } else {

        //     $pdf->Cell(25, 8, $mark['Code'], 'LR', 0, 'L');
        //     $pdf->Cell(76, 8, $mark['subject_name'], 'L', 0, 'L');
        //     $pdf->Cell(12.8, 8, $mark['obt_marks_int'], 'LR', 0, 'C');
        //     $pdf->Cell(12.2, 8, $mark['Min_Marks'], 'R', 0, 'C');
        //     $pdf->Cell(12.8, 8, $mark['obt_marks_ext'], 'R', 0, 'C');
        //     $pdf->Cell(12.2, 8, $mark['Max_Marks'], 'R', 0, 'C');
        //     $pdf->Cell(14, 8, $mark['obt_marks_int'] + $mark['obt_marks_ext'], 'R', 0, 'C');
        //     $pdf->Cell(12.3, 8, $mark['Min_Marks'] + $mark['Max_Marks'], 'R', 0, 'C');
        //     // $pdf->Cell(181, $cellHeight, $mark['subject_name'], 0, 0, 'L');
        //     // $pdf->Cell(12.8, 10, $mark['obt_marks_int'], 'LB', 0, 'C');
        //     // $pdf->Cell(12.2, 10, $mark['Min_Marks'], 'LB', 0, 'C');
        //     // $pdf->Cell(12, 8, $mark['obt_marks_ext'], 'LR', 0, 'C');
        //     // $pdf->Cell(12, 8, $mark['Max_Marks'], 'R', 0, 'C');
        //     // $pdf->Cell(14.3, 8, $mark['Min_Marks'], 'R', 0, 'C');
        //     // $remark = $mark['obt_marks_ext']<$mark['Min_Marks']?"Fail":"Pass";
        //     // $pdf->Cell(19, 8, $remark, 'R', 0, 'C');
        // }
        // $pdf->Ln();
        // $subject=$mark['subject_name'].$mark['subject_name'];
        if (strlen($mark['subject_name']) > 50) {

    $pdf->SetFont('times', '', 9);

    // CODE COLUMN
    $pdf->Cell(25, $cellHeight-1.5, $mark['Code'], 'LR', 0, 'L');

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $nameParts = explode("\n", wordwrap($mark['subject_name'], 50));

    /* ================= TOP SPACE CONTROL ================= */
    $topPadding = 1.5; //  control spacing here

    $pdf->SetXY($x, $y + $topPadding);

    /* ================= SUBJECT ================= */
    $pdf->MultiCell(
        76,
        3.5,
        $nameParts[0] . chr(10) . ($nameParts[1] ?? ''),
        '',   // ❌ no bottom border
        ''
    );

    /* ================= RESET POSITION ================= */
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // same alignment like your first code
    $pdf->SetXY($x + 107, $y - 10);

    /* ================= MARKS COLUMNS ================= */
    $pdf->Cell(12.8, 10, $mark['obt_marks_int'], 'L', 0, 'C');
    $pdf->Cell(12.2, 10, $mark['Min_Marks'], 'L', 0, 'C');
    $pdf->Cell(12.8, 10, $mark['obt_marks_ext'], 'L', 0, 'C');
    $pdf->Cell(12.2, 10, $mark['Max_Marks'], 'L', 0, 'C');
    $pdf->Cell(14, 10, $mark['obt_marks'], 'L', 0, 'C');
    $pdf->Cell(12, 10, $mark['Min_Marks'] + $mark['Max_Marks'], 'LR', 0, 'C');
} else {
            $pdf->Cell(25, $cellHeight, $mark['Code'], 'L', 0, 'L');
            // $pdf->Cell(76, $cellHeight, strtoupper($mark['subject_name']), 'LB', 0, 'L');
            $pdf->Cell(76, $cellHeight, $mark['subject_name'], 'L', 0, 'L');
            $pdf->Cell(12.8, 10, $mark['obt_marks_int'], 'L', 0, 'C');
            $pdf->Cell(12.2, 10, $mark['Min_Marks'], 'L', 0, 'C');
            $pdf->Cell(12.8, 10, $mark['obt_marks_ext'], 'L', 0, 'C');
            $pdf->Cell(12.2, 10, $mark['Max_Marks'], 'L', 0, 'C');
            $pdf->Cell(14, 10, $mark['obt_marks'], 'L', 0, 'C');
            $pdf->Cell(12, 10, (int)$mark['Min_Marks'] + (int)$mark['Max_Marks'], 'LR', 0, 'C');
        }
        $pdf->Ln();
    }
}

function makeStatus($student_data, $message)
{

    global $export_data;
    if ($student_data['University_ID'] == '41') {
        $export_data[] = array($student_data['Enrollment_No'], $student_data['program_Type'], $student_data['course'], $student_data['Duration'], $message);
    } else {
        // list($scheme_id, $semester) = explode('|', $_POST['semester']);
        $semester=$_POST['semester'];
        $export_data[] = array($student_data['Enrollment_No'], $student_data['program_Type'], $student_data['course'], $semester, $message);
    }
}

function calculateCGPA($enrol, $sub_course_id, $bovc_sem)
{
    // echo($enrol.'<br>');
    //  echo($sub_course_id.'<br>'); echo($total_sem_dur.'<br>');
    // echo('<pre>');print_r($bovc_sem);die;
    global $conn;
    $sgpa_record = [];
    // list($scheme_id, $semester) = explode('|', $_POST['semester']);
    $semester=$_POST['semester'];
    $i = 1;
    while ($i < $bovc_sem) {
        $subject_record = [];
        $sem_sql = " AND Syllabi.Semester = '$i'";
        $temp_subjects = $conn->query("SELECT Paper_Type,marksheets.date_of_issue,marksheets.obt_marks_ext, marksheets.obt_marks_int,marksheets.obt_marks,marksheets.status,marksheets.remarks,Syllabi.Name as subject_name,Syllabi.Min_Marks, Syllabi.Max_Marks, Syllabi.Credit FROM marksheets LEFT JOIN Syllabi ON marksheets.subject_id = Syllabi.ID WHERE enrollment_no = '$enrol' AND Sub_Course_ID = '$sub_course_id' $sem_sql ORDER BY Syllabi.Code ASC");
        // print_r("SELECT Paper_Type,marksheets.obt_marks_ext, marksheets.obt_marks_int,marksheets.obt_marks,marksheets.status,marksheets.remarks,Syllabi.Name as subject_name,Syllabi.Min_Marks, Syllabi.Max_Marks, Syllabi.Credit FROM marksheets LEFT JOIN Syllabi ON marksheets.subject_id = Syllabi.ID WHERE enrollment_no = '$enrol' AND Sub_Course_ID = '$sub_course_id' $sem_sql ORDER BY Syllabi.Code ASC");die;
        //  echo('<pre>')    ; print_r('$subject_record');die;
        if ($temp_subjects->num_rows > 0) {
        //   echo('<pre>')    ; print_r('$subject_record');die;
            $j = 0;
            while ($temp_subject = $temp_subjects->fetch_assoc()) {
                $subject_record[$j]['Min_Marks'] = $temp_subject['Min_Marks'];
                $subject_record[$j]['Max_Marks'] = $temp_subject['Max_Marks'];
                $subject_record[$j]['Credit'] = $temp_subject['Credit'];
                if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_ext'] == 'AB') {
                    $subject_record[$j]['obt_marks'] = 'AB';
                    $subject_record[$j]['remarks_status'] = 'FAIL';
                } else if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_int'] != 'AB') {
                    $subject_record[$j]['obt_marks'] = $temp_subject['obt_marks_int'];
                    $subject_record[$j]['remarks_status'] = 'FAIL';
                } else if ($temp_subject['obt_marks_int'] == 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                    $subject_record[$j]['obt_marks'] = $temp_subject['obt_marks_int'];
                    $subject_record[$j]['remarks_status'] = 'FAIL';
                } else if ($temp_subject['obt_marks_ext'] != 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                    $subject_record[$j]['obt_marks'] = intval($temp_subject['obt_marks_ext']) + intval($temp_subject['obt_marks_int']);
                    $subject_record[$j]['remarks_status'] = 'PASS';
                }
                $j++;
            }
            
            list($sgpa, $total_credit) = calculateSGPA($subject_record);
       
            $sgpa_record[] = array(
                'semester' => $i,
                'sgpa' => $sgpa,
                'total_credit' => $total_credit
            );
        }
        $i++;
    }
    return $sgpa_record;
}

function calculateSGPA($temp_subject)
{

    $total_obt_grade_value = 0;
    $total_credit = 0;
    $grade_value = '';
    foreach ($temp_subject as $key => $value) {
        if ($value['remarks_status'] == 'FAIL') {
            $grade_value = '0';
        } else {
            $grandTotal = $value['Min_Marks'] + $value['Max_Marks'];
            $student_obt_mark = $value['obt_marks'];
            $student_obt_per = round(($student_obt_mark / $grandTotal) * 100, 2);
            // $grade_value = match(true) {
            //     $student_obt_per > 90 => '10',
            //     $student_obt_per > 80 && $student_obt_per <= 90 => '9',
            //     $student_obt_per > 70 && $student_obt_per <= 80 => '8',
            //     $student_obt_per > 60 && $student_obt_per <= 70 => '7',
            //     $student_obt_per > 55 && $student_obt_per <= 60 => '6',
            //     $student_obt_per > 50 && $student_obt_per <= 55 => '5',
            //     $student_obt_per >= 40 && $student_obt_per <= 50 => '4',
            //     default => '0',
            // };
            if ($student_obt_per > 90) {
    $grade_value = '10';
} elseif ($student_obt_per > 80 && $student_obt_per <= 90) {
    $grade_value = '9';
} elseif ($student_obt_per > 70 && $student_obt_per <= 80) {
    $grade_value = '8';
} elseif ($student_obt_per > 60 && $student_obt_per <= 70) {
    $grade_value = '7';
} elseif ($student_obt_per > 55 && $student_obt_per <= 60) {
    $grade_value = '6';
} elseif ($student_obt_per > 50 && $student_obt_per <= 55) {
    $grade_value = '5';
} elseif ($student_obt_per >= 40 && $student_obt_per <= 50) {
    $grade_value = '4';
} else {
    $grade_value = '0';
}
        }
        $total_obt_grade_value += intval($grade_value) * intval($value['Credit']);
        $total_credit += intval($value['Credit']);
    }

    return [number_format($total_obt_grade_value / $total_credit, 2), $total_credit];
}


function setHeaderGrade($data, $exam_month)
{

    global $pdf;
    $pdf->SetFont("times", '', 9);
    // $pdf->SetXY(45, 47);
    // $stat = (isset($_REQUEST['marksheet_in_grade']) ? "Statement of Grades" : "Statement of Marks");
    // $pdf->Cell(0, 0, strtoupper($stat), 0, 0, 'C', 0);
    $pdf->SetFont("times", 'B', 9);
    $pdf->SetXY(23, 42.4);
    $course_name =  (isset($_REQUEST['marksheet_in_grade']) ? strtoupper('B. VOC. IN ' . $data['course']) : 'B. VOC. IN' . $data['course']);
    $pdf->Cell(0, 0, $course_name, 0, 0, 'C', 0);
    $pdf->SetXY(22, 47);
    $pdf->SetFont("times", '', 9);
    $addmissionSession = explode('-', $data['Admission_Session']);
    $addmissionYearFrom = 2000 + (int)$addmissionSession[1];
    $addmissionYearTo = 3 + (int)$addmissionYearFrom;
    $pdf->Cell(0, 0, strtoupper('Session: ' . $addmissionYearFrom . '-' . $addmissionYearTo  . ''), 0, 0, 'C', 0);
    $pdf->SetFont("times", 'B', 9);
     $pdf->SetXY(16.1, 55);
    $pdf->Cell(196, 5, 'Name : ', 0, 0, 'L', 0);
    $full_name = $data['First_Name'] . ' ' . $data['Middle_Name'] . ' ' . $data['Last_Name'];
    $pdf->SetXY(27.1, 55);
    checkWarpText(strtoupper($full_name), 34, 94);
   $pdf->SetXY(123, 55);
    $pdf->Cell(70, 5, 'Enrollment No : ' . $data['Enrollment_No'], 0, 0, 'L', 0);
    //   $pdf->SetXY(16.1, 56);
    // $pdf->Cell(196, 5, 'Father Name : ', 0, 0, 'L', 0);
    // $pdf->SetXY(37.1, 56);
    // checkWarpText(strtoupper($data['Father_Name']),33,94);
      $pdf->SetXY(123, 60);
    $pdf->Cell(70, 5, $data['mode_type'] . ' ' . ':' . ' ' . $data['durMonthYear'], 0, 0, 'L', 0);
     $pdf->SetXY(16.1, 60);
    $pdf->Cell(107, 5, 'School: ' .  $data['university_name'], 0, 0, 'L', 0);
    //     $pdf->SetXY(212, 56);
    //     $semInRoman = intToRoman((int)$data['durMonthYear']);
    //   	$examSession = explode('-',$exam_month);
    //   	$examMonth = date('F',strtotime($examSession[0]));
    //   	$examYear = 2000 + (int)$examSession[1];
    //     $pdf->Cell(70, 5, 'Semester: ' .$data['Admission_Session'] . ')', 0, 0, 'L', 0);
    $pdf->SetFont('times', 'B', 9);
}

function setHeaderPercentage($data, $exam_month)
{

    global $pdf;
    $pdf->SetFont("times", '', 9);
    // $pdf->SetXY(15, 55);
    // $pdf->SetXY(45, 47);
    // $stat = (isset($_REQUEST['marksheet_in_grade']) ? "Statement of Grades" : "Statement of Marks");
    $pdf->Cell(0, 0, strtoupper($stat), 0, 0, 'C', 0);
    $pdf->SetXY(23, 42.4);
     $pdf->SetFont('Times', 'B', 9);
    $pdf->Cell(0, 0, 'B.VOC. IN ' . '' . '' . '' . strtoupper($data['course']), 0, 'B', 'C', 0);
      $pdf->SetFont("times", '', 9);
    $pdf->SetXY(22, 47);
    $addmissionSession = explode('-', $data['Admission_Session']);
    //  echo('<pre>');print_r($addmissionSession);die;
    $addmissionYearFrom =  (int)$addmissionSession[1];
    $addmissionYearTo = 3 + (int)$addmissionYearFrom;
    $pdf->Cell(0, 0, strtoupper('Admission Session :' . $addmissionYearFrom . '-' . $addmissionYearTo  . ''), 0, 0, 'C', 0);
    $pdf->SetFont("times", 'B', 9);
    $pdf->SetXY(16.1, 54);
    $pdf->Cell(107, 5, 'Name : ', 0, 0, 'L', 0);
    $full_name = $data['First_Name'] . ' ' . $data['Middle_Name'] . ' ' . $data['Last_Name'];
    $pdf->SetXY(27.1, 54);
    checkWarpText(strtoupper($full_name), 33, 196);
    $pdf->SetXY(140, 54);
    $pdf->Cell(70, 5, 'Enrollment No : ' . $data['Enrollment_No'], 0, 0, 'L', 0);
    $pdf->SetXY(16.1, 59);
    $pdf->Cell(107, 5, 'Father Name : ', 0, 0, 'L', 0);
    $pdf->SetXY(37.1, 59);
    checkWarpText(strtoupper($data['Father_Name']), 33, 94);
    $pdf->SetXY(140, 59);
    $pdf->Cell(70, 5,  $data['mode_type'] . ' ' . ':' . ' ' . $data['durMonthYear'], 0, 0, 'L', 0);
    $pdf->SetXY(16.1, 64);
    $pdf->Cell(107, 5, 'School : ' . ' ' . $data['university_name'], 0, 0, 'L', 0);
    $pdf->SetXY(140, 64);
    $examSession = explode('-', $exam_month);
    $examMonth = date('F', strtotime($examSession[0]));
    $examYear = 2000 + (int)$examSession[1];
    $pdf->Cell(70, 5, 'Exam Session : ' . ' ' . ucwords("APRIL 2025"), 0, 0, 'L', 0);
    $pdf->SetFont('times', 'B', 9);
}

function checkWarpText($content, $content_length, $width)
{

    global $pdf;
    if (strlen($content) > $content_length) {
        $nameParts = explode("\n", wordwrap($content, $content_length));
        $pdf->SetFont("times", '', 9);

        $pdf->MultiCell(196, 5, $nameParts[0] . chr(10) . $nameParts[1], 0, 0, 0, 'L');
        $pdf->SetFont("times", '', 9);
    } else {

        $pdf->Cell(196, 5, $content, 0, 0, 'L', 0);
    }
}

function selectExamSessionAndDateOfIssue($admission_session, $semester, $type)
{
    list($month, $year) = explode('-', $admission_session);
    $month = ucwords(strtolower(substr($month, 0, 3)));
    $year = intval($year);
    $year = (strlen($year) > 2) ? date('y', strtotime("$year-01-01")) : $year;
    $updated_adm_session = $month . '-' . $year;
    $list_details = array(
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Dec-21',
            'semester' => '1',
            'date_of_issue' => '10-02-2022'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Jun-22',
            'semester' => '2',
            'date_of_issue' => '08-08-2022'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Dec-22',
            'semester' => '3',
            'date_of_issue' => '15-02-2023'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Jun-23',
            'semester' => '4',
            'date_of_issue' => '22-07-2023'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Dec-23',
            'semester' => '5',
            'date_of_issue' => '26-02-2024'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Jun-24',
            'semester' => '6',
            'date_of_issue' => '19-10-2024'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Dec-22',
            'semester' => '1',
            'date_of_issue' => '15-02-2023'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Jun-23',
            'semester' => '2',
            'date_of_issue' => '22-07-2023'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Dec-23',
            'semester' => '3',
            'date_of_issue' => '20-02-2024'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Jun-24',
            'semester' => '4',
            'date_of_issue' => '19-10-2024'
        ],
        [
            'admission_session' => 'Jul-23',
            'exam_session' => 'Dec-23',
            'semester' => '1',
            'date_of_issue' => '26-02-2024'
        ],
        [
            'admission_session' => 'Jul-23',
            'exam_session' => 'Jun-24',
            'semester' => '2',
            'date_of_issue' => '19-10-2024'
        ],
        [
            'admission_session' => 'Jul-23',
            'exam_session' => 'Dec-24',
            'semester' => '3',
            'date_of_issue' => date('d-m-Y')
        ],
        [
            'admission_session' => 'Jul-23',
            'exam_session' => 'Jun-24',
            'semester' => '4',
            'date_of_issue' => date('d-m-Y'),
        ],
        [
            'admission_session' => 'Jul-24',
            'exam_session' => 'Dec-24',
            'semester' => '1',
            'date_of_issue' => "15-05-2025",
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Dec-24',
            'semester' => '5',
            'date_of_issue' => date('d-m-Y'),
        ],
        [
            'admission_session' => 'Jul-24',
            'exam_session' => 'Dec-24',
            'semester' => '5',
            'date_of_issue' => "15-05-2025",
        ],
        [
            'admission_session' => 'Jul-24',
            'exam_session' => 'Dec-25',
            'semester' => '5',
            'date_of_issue' => date('d-m-Y'),
        ]
    );

    $exam_session = '';
    $date_of_issue = '';
    foreach ($list_details as $key => $value) {

        if ($value['admission_session'] == $updated_adm_session && $value['semester'] == $semester) {
            $exam_session = $value['exam_session'];
            $date_of_issue = $value['date_of_issue'];
            break;
        }
    }

    return ($type == 'date&exam') ?  [$date_of_issue, $exam_session] : [$date_of_issue];
}
