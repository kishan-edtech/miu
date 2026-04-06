<?php
if (isset($_POST['id']) && $_POST['enrollment_no']) {
    require '../../../includes/db-config.php';

    session_start();

    $id            = intval($_POST['id']);
    $enrollment_no = mysqli_real_escape_string($conn, $_POST['enrollment_no']);

    if (empty($enrollment_no)) {
        echo json_encode(['status' => 400, 'message' => 'Enrollment No is required.']);
        exit();
    }

    // 1️⃣ Get OLD data
    $old_query      = $conn->query("SELECT Enrollment_No FROM Students WHERE ID = $id");
    $old_data       = $old_query->fetch_assoc();
    $old_enrollment = $old_data['Enrollment_No'];

    $check = $conn->query("SELECT ID FROM Students WHERE Enrollment_No = '$enrollment_no'");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 400, 'message' => 'Enrollment No. already exists.']);
    }

    $column = "";
    if ($_SESSION['has_lms']) {
        $column = ", Status = 1, Admit_Card = 1, ID_Card = 1, Exam = 1";
    }

    $update = $conn->query("UPDATE Students SET Enrollment_No = '$enrollment_no' $column WHERE ID = $id");
    if ($update) {

        $changes_old = [];
        $changes_new = [];

        // 3️⃣ Detect change
        if ($old_enrollment != $enrollment_no) {
            $changes_old['Enrollment_No'] = $old_enrollment;
            $changes_new['Enrollment_No'] = $enrollment_no;
        }

        // 4️⃣ Add log
        if (! empty($changes_new)) {
            addLog($conn,
                $_SESSION['university_id'], $_SESSION['ID'], 'update', 'Students', $id, 'Enrollment number updated', json_encode($changes_old), json_encode($changes_new));

        }
        echo json_encode(['status' => 200, 'message' => 'Enrollment No. updated successfully!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
}
