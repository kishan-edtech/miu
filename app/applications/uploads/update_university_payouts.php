<?php
if (isset($_FILES['file'])) {
    require '../../../includes/db-config.php';

    require '../../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php';
    require '../../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php';

    session_start();

    $export_data = [];

    // Header
    $header = ['UTR No', 'Amount', 'Date', 'Student ID'];

    $export_data[] = $header;

    $mimes = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (in_array($_FILES["file"]["type"], $mimes)) {
        // Upload File
        $uploadFilePath = basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

        // Read File
        $reader = new SpreadsheetReader($uploadFilePath);

        // Sheet Count
        $totalSheet = count($reader->sheets());

        /* For Loop for all sheets */
        for ($i = 0; $i < $totalSheet; $i++) {
            $reader->ChangeSheet($i);

            foreach ($reader as $row) {
                $totalAmount      = 0;
                $totalSheetAmount = 0;

                // Data
                $remark    = [];
                $utrNumber = mysqli_real_escape_string($conn, $row[0]);
                $amount    = mysqli_real_escape_string($conn, $row[1]);
                $date      = mysqli_real_escape_string($conn, $row[2]);
                $studentId = mysqli_real_escape_string($conn, $row[3]);

                if ($studentId == 'Student ID') {
                    continue;
                }

                $utrNumberCheck = $conn->query("SELECT * FROM University_Stu_Payments WHERE Transaction_No = '$utrNumber' AND University_ID = " . $_SESSION['university_id'] . "");
                if ($utrNumberCheck->num_rows == 0) {

                    $totalSheetAmount += $amount;

                    $studentIds = explode(',', $studentId);

                    foreach ($studentIds as $id) {
                        $student_sub_course = $conn->query("SELECT Sub_Course_ID FROM Students WHERE Unique_ID = '$id' AND University_ID = " . $_SESSION['university_id'] . "");
                        if ($student_sub_course->num_rows == 0) {
                            continue;
                        }
                        $subCourseRow = $student_sub_course->fetch_assoc();
                        $subCourseId  = $subCourseRow['Sub_Course_ID'];

                        if (! empty($subCourseId)) {
                            $subCourseUniFee           = $conn->query("SELECT university_fee FROM Sub_Courses WHERE ID = $subCourseId AND University_ID = " . $_SESSION['university_id'] . "");
                            $subCourseUniFeeRow        = $subCourseUniFee->fetch_assoc();
                            $subCourseUniFeeRowAmount  = $subCourseUniFeeRow['university_fee'];
                            $totalAmount              += isset($subCourseUniFeeRowAmount) ? $subCourseUniFeeRowAmount : 0;
                        }
                    }

                    if ($totalAmount == $totalSheetAmount) {

                        $paymentId     = "EDMDU" . rand(1000, 9999);
                        $dateObj       = DateTime::createFromFormat('m-d-y', $date);
                        $formattedDate = $dateObj->format('Y-m-d H:i:s');

                        $submitQuery = "INSERT INTO `University_Stu_Payments` (`Student_ID`, `University_ID`, `Payment_ID`, `Amount`, `Transaction_No`,`Transaction_Date`) VALUES ('" . $studentId . "', '" . $_SESSION['university_id'] . "','" . $paymentId . "','" . $amount . "','" . $utrNumber . "', '" . $formattedDate . "')";

                        $runQuery = $conn->query($submitQuery);

                        foreach ($studentIds as $id) {

                            $student_sub_course = $conn->query("SELECT ID,Sub_Course_ID,Admission_Duration FROM Students WHERE Unique_ID = '$id' AND University_ID = " . $_SESSION['university_id'] . "");
                            $subCourseRow       = $student_sub_course->fetch_assoc();
                            $subCourseId        = $subCourseRow['Sub_Course_ID'];
                            $stuId              = $subCourseRow['ID'];
                            $duration           = $subCourseRow['Admission_Duration'];

                            if (! empty($subCourseId)) {
                                $subCourseUniFee          = $conn->query("SELECT university_fee FROM Sub_Courses WHERE ID = $subCourseId AND University_ID = " . $_SESSION['university_id'] . "");
                                $subCourseUniFeeRow       = $subCourseUniFee->fetch_assoc();
                                $subCourseUniFeeRowAmount = $subCourseUniFeeRow['university_fee'];

                                $submitQuery = "INSERT INTO `University_Payments` (`Student_ID`, `University_ID`, `Sub_Course_ID`, `Fee`, `Source`, `Transaction_No`, `Transaction_Date`, `Transaction_Mode`, `Duration`) VALUES ('" . $stuId . "', '" . $_SESSION['university_id'] . "','" . $subCourseId . "','" . $subCourseUniFeeRowAmount . "','offline','" . $utrNumber . "', '" . $formattedDate . "', '" . "offline" . "', '" . $duration . "')";
                                $runQuery    = $conn->query($submitQuery);
                            }

                        }
                        $remark[]      = "University Payments successfull!";
                        $export_data[] = [$utrNumber, $amount, $date, $studentId, empty($remark) ? 'Values are missing!' : implode(", ", $remark)];

                    } else {
                        $remark[]      = "University Payments not match!";
                        $export_data[] = [$utrNumber, $amount, $date, $studentId, empty($remark) ? 'Values are missing!' : implode(", ", $remark)];
                    }
                } else {
                    $remark[]      = "UTR Number already exist!";
                    $export_data[] = [$utrNumber, $amount, $date, $studentId, empty($remark) ? 'Values are missing!' : implode(", ", $remark)];
                }
            }
        }
        unlink($uploadFilePath);
        $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('University Payouts.xlsx');
    }
}
