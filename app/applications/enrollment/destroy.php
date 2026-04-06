<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
    require '../../../includes/db-config.php';

    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

    // 1️⃣ Get old data
    $old_query      = $conn->query("SELECT Enrollment_No FROM Students WHERE ID = $id");
    $old_data       = $old_query->fetch_assoc();
    $old_enrollment = $old_data['Enrollment_No'];

    $column = "";
    if ($_SESSION['has_lms']) {
        $column = ", Status = 0, Admit_Card = 0, ID_Card = 0, Exam = 0";
    }

    $update = $conn->query("UPDATE Students SET Enrollment_No = NULL $column WHERE ID = $id");
    if ($update) {

        $changes_old = [];
        $changes_new = [];

        // 3️⃣ Detect change
        if (! empty($old_enrollment)) {
            $changes_old['Enrollment_No'] = $old_enrollment;
            $changes_new['Enrollment_No'] = null;
        }

        // 4️⃣ Add log
        if (! empty($changes_old)) {
            addLog($conn,$_SESSION['university_id'], $_SESSION['ID'], 'delete', 'Students', $id, 'Enrollment number deleted', json_encode($changes_old), json_encode($changes_new));
        }

        echo json_encode(['status' => 200, 'message' => 'Enrollment No deleted successfully!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }

}
