<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
    require '../../includes/db-config.php';
    require '../../includes/helpers.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $check = $conn->query("SELECT ID FROM Payments WHERE ID = $id");
    if ($check->num_rows > 0) {
        $old_data = $check->fetch_assoc();
        $delete   = $conn->query("DELETE FROM Payments WHERE ID = $id");
        if ($delete) {
            addLog($conn,$_SESSION['university_id'], $_SESSION['ID'], 'delete', 'Payments', $id, 'Payment deleted', json_encode($old_data), null);
            echo json_encode(['status' => 200, 'message' => 'Payment deleted successfully!']);
        } else {
            echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
        }
    } else {
        echo json_encode(['status' => 302, 'message' => 'Payment not exists!']);
    }
}
