<?php
session_start();
if ($_POST['amount'] != '' && $_POST['date_of_transaction'] != '' && $_POST['transaction_no'] != '' && $_POST['mode_of_payment'] != '') {
    require '../../includes/db-config.php';
    $paidFeeQuery = "select sum(Fee) as paidAmount from University_Payments where Student_ID=" . $_POST['stu_id'] . " AND Sub_Course_ID = " . $_POST['courseId'] . " AND Duration = " . $_POST['duration'] . "";
    $paidAmount   = $conn->query($paidFeeQuery)->fetch_assoc();

    $totalFeeQuery = "select university_fee from Sub_Courses where ID=" . $_POST['courseId'];

    $totalFee = $conn->query($totalFeeQuery)->fetch_assoc();

    $edit_paid_fee = $_POST['edit_paid_fee'];


    if ($paidAmount['paidAmount'] > 0) {
        if ((intval($paidAmount['paidAmount']) + intval($_POST['amount']) - intval($edit_paid_fee)) > $totalFee['university_fee']) {
            echo json_encode(['status' => 'error', 'message' => 'Amount cannot more than pending amount ', 'code' => '1001']);
            exit;
        }
    } else {
        if ($totalFee['university_fee'] < $_POST['amount']) {
            echo json_encode(['status' => 'error', 'message' => 'Amount cannot more than pending amount', 'code' => '1002']);
            exit;
        }
    }

    $submitQuery = "UPDATE `University_Payments` SET
    `Fee` = '" . $_POST['amount'] . "',
    `Transaction_No` = '" . $_POST['transaction_no'] . "',
    `Transaction_Date` = '" . $_POST['date_of_transaction'] . "',
    `Transaction_Mode` = '" . $_POST['mode_of_payment'] . "',
    `Duration` = '" . $_POST['duration'] . "'
        WHERE `ID` = '" . $_POST['id'] . "'";
    $runQuery = $conn->query($submitQuery);
    if ($runQuery) {
        echo json_encode(['status' => 'success', 'message' => 'University payment added successfully', 'code' => '1003']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error in qury', 'code' => '1004']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'All fields are mandatory', 'code' => '1003']);
    exit;
}
