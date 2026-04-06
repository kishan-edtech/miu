<?php
if (isset($_POST['id']) && isset($_POST['internal_id'])) {
    require '../../../includes/db-config.php';
    session_start();
    $id          = intval($_POST['id']);
    $internal_id = trim($_POST['internal_id']);
    if (empty($internal_id)) {
        echo json_encode(['status' => 400, 'message' => 'Internal ID is required.']);
        exit();
    }
    $internal_id = mysqli_real_escape_string($conn, $internal_id);
    $check = $conn->query("
        SELECT ID
        FROM Users
        WHERE Internal_ID = '$internal_id'
        AND ID != $id
        LIMIT 1
    ");

    if ($check->num_rows > 0) {
        echo json_encode([
            'status'  => 409,
            'message' => 'Internal ID already exists.',
        ]);
        exit();
    }
    $update = $conn->query("
        UPDATE Users
        SET Internal_ID = '$internal_id'
        WHERE ID = $id
    ");

    if ($update) {
        echo json_encode([
            'status'  => 200,
            'message' => 'Internal ID updated successfully!',
        ]);
    } else {
        echo json_encode([
            'status'  => 400,
            'message' => 'Something went wrong!',
        ]);
    }
}
