<?php
if (isset($_POST['center'])) {
    require '../../includes/db-config.php';
    session_start();

    $id = array_key_exists('inserted_id', $_POST) ? intval($_POST['inserted_id']) : 0;

    // Required
    $center            = intval($_POST['center']);
    $admission_session = intval($_POST['admission_session']);
    $admission_type    = intval($_POST['admission_type']);
    $course            = intval($_POST['course']);
    $sub_course        = intval($_POST['sub_course']);

    $duration = intval($_POST['duration']);
    $abcid    = mysqli_real_escape_string($conn, $_POST['abc_id']);
    if (empty($center) || empty($admission_session) || empty($admission_type) || empty($course) || empty($sub_course) || empty($duration)) {
        exit(json_encode(['status' => 400, 'message' => 'All fields are required']));
    }

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $full_name = str_replace('  ', ' ', $full_name);
    $full_name = explode(' ', $full_name, 3);
    $count     = count($full_name);

    if ($count == 2) {
        $first_name  = trim($full_name[0]);
        $first_name  = strtoupper(strtolower($first_name));
        $middle_name = null;
        $last_name   = trim($full_name[1]);
        $last_name   = strtoupper(strtolower($last_name));
    } elseif ($count > 2) {
        $first_name  = trim($full_name[0]);
        $first_name  = strtoupper(strtolower($first_name));
        $middle_name = trim($full_name[1]);
        $middle_name = strtoupper(strtolower($middle_name));
        $last_name   = trim($full_name[2]);
        $last_name   = strtoupper(strtolower($last_name));
    } else {
        $first_name  = trim($full_name[0]);
        $first_name  = strtoupper(strtolower($first_name));
        $middle_name = null;
        $last_name   = null;
    }

    if ($_SESSION['university_id'] == 20 && $admission_session == 70) {
        $duration = 6;
    }
    if ($admission_session == 73) {
        $duration = 2;
    }

    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $father_name = strtoupper(strtolower($father_name));
    $mother_name = mysqli_real_escape_string($conn, $_POST['mother_name']);
    $mother_name = strtoupper(strtolower($mother_name));

    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $dob = date('Y-m-d', strtotime($dob));

    $gender            = mysqli_real_escape_string($conn, $_POST['gender']);
    $category          = mysqli_real_escape_string($conn, $_POST['category']);
    $employment_status = mysqli_real_escape_string($conn, $_POST['employment_status']);
    $marital_status    = mysqli_real_escape_string($conn, $_POST['marital_status']);
    $religion          = mysqli_real_escape_string($conn, $_POST['religion']);
    $aadhar            = mysqli_real_escape_string($conn, $_POST['aadhar']);
    $nationality       = mysqli_real_escape_string($conn, $_POST['nationality']);

    if (! empty($id)) {

        // 1️⃣ Get old student data
        $old_query = $conn->query("SELECT * FROM Students WHERE ID = $id");
        $old_data  = $old_query->fetch_assoc();

        // 2️⃣ Prepare new data
        $new_data = [
            'abc_id'               => $abcid,
            'Admission_Type_ID'    => $admission_type,
            'Admission_Session_ID' => $admission_session,
            'Course_ID'            => $course,
            'Sub_Course_ID'        => $sub_course,
            'Duration'             => $duration,
            'First_Name'           => $first_name,
            'Middle_Name'          => $middle_name,
            'Last_Name'            => $last_name,
            'Father_Name'          => $father_name,
            'Mother_Name'          => $mother_name,
            'DOB'                  => $dob,
            'Aadhar_Number'        => $aadhar,
            'Category'             => $category,
            'Gender'               => $gender,
            'Nationality'          => $nationality,
            'Employement_Status'   => $employment_status,
            'Marital_Status'       => $marital_status,
            'Religion'             => $religion,
        ];

        // 3️⃣ Detect changed fields
        $changes_old = [];
        $changes_new = [];

        foreach ($new_data as $field => $value) {

            if ($old_data[$field] != $value) {
                $changes_old[$field] = $old_data[$field];
                $changes_new[$field] = $value;
            }

        }

        // 4️⃣ Run update query
        $add_student = $conn->query("UPDATE Students SET
        abc_id = '$abcid',
        Admission_Type_ID = $admission_type,
        Admission_Session_ID = $admission_session,
        Course_ID = $course,
        Sub_Course_ID = $sub_course,
        Duration = $duration,
        First_Name = '$first_name',
        Middle_Name = '$middle_name',
        Last_Name = '$last_name',
        Father_Name = '$father_name',
        Mother_Name = '$mother_name',
        DOB = '$dob',
        Aadhar_Number = '$aadhar',
        Category = '$category',
        Gender = '$gender',
        Nationality = '$nationality',
        Employement_Status = '$employment_status',
        Marital_Status = '$marital_status',
        Religion = '$religion'
        WHERE ID = $id");

        if ($add_student) {

            generateStudentLedger($conn, $id);

            // 5️⃣ Log only if fields changed
            if (! empty($changes_new)) {

                addLog($conn, $_SESSION['university_id'], $_SESSION['ID'], 'update', 'Students', $id, 'Student updated', json_encode($changes_old), json_encode($changes_new));
            }

            echo json_encode([
                'status'  => 200,
                'message' => 'Step 1 details saved successfully!',
                'id'      => $id,
            ]);

        } else {
            echo json_encode([
                'status'  => 400,
                'message' => 'Something went wrong!',
            ]);
        }
    } else {

        $added_by = $_SESSION['Role'] == 'Student' ? $center : $_SESSION['ID'];

        if (empty($lead_id)) {
            $lead_id = 'NULL';
        }

        $add_student = $conn->query("INSERT INTO Students (Added_By, Added_For, Lead_Status_ID, University_ID, Admission_Type_ID, Admission_Session_ID, Course_ID, Sub_Course_ID, Duration, Admission_Duration, First_Name, Middle_Name, Last_Name, Father_Name, Mother_Name, DOB, Aadhar_Number, Category, Gender, Nationality, Employement_Status, Marital_Status, Religion, Step) VALUES(" . $added_by . ", $center, $lead_id, " . $_SESSION['university_id'] . ", $admission_type, $admission_session, $course, $sub_course, $duration, $duration, '$first_name', '$middle_name', '$last_name', '$father_name', '$mother_name', '$dob', '$aadhar', '$category', '$gender', '$nationality', '$employment_status', '$marital_status', '$religion', 1)");
        if ($add_student) {
            $student_id = $conn->insert_id;
            generateStudentLedger($conn, $student_id);

            if ($lead_id == 'NULL' || $lead_id == null) {
                $has_unique_student_id = $conn->query("SELECT ID_Suffix, Max_Character FROM Universities WHERE ID = " . $_SESSION['university_id'] . " AND Has_Unique_StudentID = 1");
                if ($has_unique_student_id->num_rows > 0) {
                    $has_unique_student_id = $has_unique_student_id->fetch_assoc();
                    $suffix                = $has_unique_student_id['ID_Suffix'];
                    $characters            = $has_unique_student_id['Max_Character'];
                    $unique_id             = generateStudentID($conn, $suffix, $characters, $_SESSION['university_id']);
                    $conn->query("UPDATE Students SET Unique_ID = '$unique_id' WHERE ID = $student_id");
                    if (isset($_POST['abc_id'])) {
                        $abcid = mysqli_real_escape_string($conn, $_POST['abc_id']);
                        $conn->query("UPDATE Students SET abc_id = '$abcid' WHERE ID = $student_id");
                    }
                }
            } else {
                $unique_id = $conn->query("SELECT Unique_ID FROM Lead_Status WHERE ID = $lead_id");
                $unique_id = $unique_id->fetch_assoc();
                $conn->query("UPDATE Students SET Unique_ID = '" . $unique_id['Unique_ID'] . "' WHERE ID = $student_id");

                $final_stage = $conn->query("SELECT ID FROM Stages WHERE Name = 'Step 1 Completed'");
                if ($final_stage->num_rows > 0) {
                    $final_stage = $final_stage->fetch_assoc();
                    $final_stage = $final_stage['ID'];
                } else {
                    $final_stage = $conn->query("INSERT INTO Stages (`Name`, Is_Last) VALUES ('Step 1 Completed', 1)");
                    $final_stage = $conn->insert_id;
                }

                $conn->query("UPDATE Lead_Status SET Admission = 1, Stage_ID = $final_stage, Reason_ID = NULL WHERE ID = $lead_id");
            }

            $new_data = [
                'Added_By'             => $added_by,
                'Added_For'            => $center,
                'Lead_Status_ID'       => $lead_id,
                'University_ID'        => $_SESSION['university_id'],
                'Admission_Type_ID'    => $admission_type,
                'Admission_Session_ID' => $admission_session,
                'Course_ID'            => $course,
                'Sub_Course_ID'        => $sub_course,
                'Duration'             => $duration,
                'First_Name'           => $first_name,
                'Middle_Name'          => $middle_name,
                'Last_Name'            => $last_name,
                'Father_Name'          => $father_name,
                'Mother_Name'          => $mother_name,
                'DOB'                  => $dob,
                'Aadhar_Number'        => $aadhar,
                'Category'             => $category,
                'Gender'               => $gender,
                'Nationality'          => $nationality,
                'Employement_Status'   => $employment_status,
                'Marital_Status'       => $marital_status,
                'Religion'             => $religion,
            ];

            addLog($conn, $_SESSION['university_id'], $_SESSION['ID'], 'add', 'Students', $student_id, 'Student added', null, json_encode($new_data));

            echo json_encode(['status' => 200, 'message' => 'Step 1 details saved successfully!', 'id' => $student_id]);
        } else {
            echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
        }
    }
}
