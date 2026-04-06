<?php

if (isset($_POST['table']) && isset($_POST['id'])) {

    require '../../includes/db-config.php';
    session_start();

    $table = mysqli_real_escape_string($conn, $_POST['table']);
    $table = str_replace('-', '_', $table);

    $column = "Status";
    if (isset($_POST['column']) && ! empty($_POST['column'])) {
        $column = $_POST['column'];
    }

    if ($table == 'Students') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $id = base64_decode($id);
        $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
    } else {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
    }
    
    if (empty($table) || empty($id)) {
        echo json_encode(['status' => 403, 'message' => 'Forbidden']);
        exit();
    }

    // 1️⃣ Get old status
    if($table=="marksheets"){
        $id = $_POST['id'];
        // print_r("SELECT status FROM marksheets WHERE enrollment_no = "."'$id'");die;
        $get_status = $conn->query("SELECT status FROM marksheets WHERE enrollment_no = "."'$id'");
    }else{
        $get_status = $conn->query("SELECT $column FROM $table WHERE ID = $id");
    }
    

    if ($get_status->num_rows > 0) {

        $status     = mysqli_fetch_assoc($get_status);
        $old_status = $status[$column];

        // 2️⃣ Toggle value
        if ($old_status == 1) {
            $new_status = 0;
        } else {
            $new_status = 1;
        }

        // 3️⃣ Update
        if($table=="marksheets"){
        $update = $conn->query("UPDATE $table SET $column = $new_status WHERE enrollment_no = "."'$id'");
        }else{
           $update = $conn->query("UPDATE $table SET $column = $new_status WHERE ID = $id");
        }
        

        if ($update) {

            // 4️⃣ Prepare log data
            $changes_old = [];
            $changes_new = [];

            $changes_old[$column] = $old_status;
            $changes_new[$column] = $new_status;

            // 5️⃣ Save activity log
            addLog($conn,$_SESSION['university_id'], $_SESSION['ID'], 'update', $table, $id, $column . ' status changed', json_encode($changes_old), json_encode($changes_new)
            );

            echo json_encode([
                'status'  => 200,
                'message' => $column . ' changed successfully!',
            ]);

        } else {

            echo json_encode([
                'status'  => 302,
                'message' => 'Something went wrong!',
            ]);

        }

    } else {

        echo json_encode([
            'status'  => 404,
            'message' => 'No record found!',
        ]);

    }

} else {

    echo json_encode([
        'status'  => 403,
        'message' => 'Forbidden',
    ]);
    exit();

}
