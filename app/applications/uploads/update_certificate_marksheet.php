<?php
if (isset($_FILES['file'])) {
    require '../../../includes/db-config.php';

    require '../../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php';
    require '../../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php';

    session_start();

    $export_data = [];

    // Header
    $header        = ['Student ID', 'Enrollment No', 'Certificate', 'Marksheet'];
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
                // Data
                $remark      = [];
                $id          = mysqli_real_escape_string($conn, $row[0]);
                $enrollment  = mysqli_real_escape_string($conn, $row[1]);
                $certificate = mysqli_real_escape_string($conn, $row[2]);
                $marksheet   = mysqli_real_escape_string($conn, $row[3]);

                if ($id == 'Student ID') {
                    continue;
                }

                $student_id = $conn->query("SELECT ID FROM Students WHERE (ID = '$id' OR Unique_ID = '$id') AND University_ID = " . $_SESSION['university_id'] . "");
                if ($student_id->num_rows == 0) {
                    continue;
                }

                $student_id = $student_id->fetch_assoc();
                $id         = $student_id['ID'];

                if (! empty($id)) {

                    if (! empty($enrollment)) {
                        $update = $conn->query("UPDATE Students SET Enrollment_No = '$enrollment' WHERE ID = $id AND University_ID = " . $_SESSION['university_id'] . "");
                        if ($update) {
                            $remark[] = "Enrollment Number updated successfully!";
                        } else {
                            $remark[] = "Can't update Enrollment No!";
                        }
                    }

                    if (! empty($certificate)) {
                        $update = $conn->query("UPDATE Students SET Is_Certificate = '$certificate' WHERE ID = $id AND University_ID = " . $_SESSION['university_id'] . "");
                        if ($update) {
                            $remark[] = "Certificate updated successfully!";
                        } else {
                            $remark[] = "Can't update Certificate!";
                        }
                    }

                    if (! empty($marksheet)) {
                        $update = $conn->query("UPDATE Students SET Is_Marksheet = '$marksheet' WHERE ID = $id AND University_ID = " . $_SESSION['university_id'] . "");
                        if ($update) {
                            $remark[] = "Marksheet updated successfully!";
                        } else {
                            $remark[] = "Can't update Marksheet!";
                        }
                    }

                    $export_data[] = [$id, $enrollment, $certificate, $marksheet, empty($remark) ? 'Values are missing!' : implode(", ", $remark)];
                }
            }
        }
        unlink($uploadFilePath);
        $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('Certificate Marksheet.xlsx');
    }
}
