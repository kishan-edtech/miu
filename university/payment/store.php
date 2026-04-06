<?php
session_start();
if ($_POST['amount'] != '' && $_POST['date_of_transaction'] != '' && $_POST['transaction_no'] != '' && $_POST['mode_of_payment'] != '') {
    require '../../includes/db-config.php';
    $paidFeeQuery = "select sum(Fee) as paidAmount from University_Payments where Student_ID=" . $_POST['id']." AND Sub_Course_ID = " . $_POST['courseId']." AND Duration = " . $_POST['duration']."";
    $paidAmount = $conn->query($paidFeeQuery)->fetch_assoc();

    $totalFeeQuery = "select university_fee from Sub_Courses where ID=" . $_POST['courseId'];

    $totalFee = $conn->query($totalFeeQuery)->fetch_assoc();

    if ($paidAmount['paidAmount'] > 0) {
        if ((intval($paidAmount['paidAmount']) + intval($_POST['amount'])) > $totalFee['university_fee']) {
            echo json_encode(array('status' => 'error', 'message' => 'Amount cannot more than pending amount', 'code' => '1001'));
            exit;
        }
    } else {
        if ($totalFee['university_fee'] < $_POST['amount']) {
            echo json_encode(array('status' => 'error', 'message' => 'Amount cannot more than pending amount', 'code' => '1002'));
            exit;
        }
    }
    $submitQuery = "INSERT INTO `University_Payments` (`Student_ID`, `University_ID`, `Sub_Course_ID`, `Fee`, `Source`, `Transaction_No`, `Transaction_Date`, `Transaction_Mode`, `Duration`) 
    VALUES ('" . $_POST['id'] . "', '" . $_SESSION['university_id'] . "','" . $_POST['courseId'] . "','" . $_POST['amount'] . "','offline','" . $_POST['transaction_no'] . "', '" . $_POST['date_of_transaction'] . "', '" . $_POST['mode_of_payment'] . "', '" . $_POST['duration'] . "')";
    $runQuery = $conn->query($submitQuery);
    if ($runQuery) {
        echo json_encode(array('status' => 'success', 'message' => 'University payment added successfully', 'code' => '1003'));
        exit;
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error in qury', 'code' => '1004'));
        exit;
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'All fields are mandatory', 'code' => '1003'));
    exit;
}
