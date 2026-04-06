<?php
session_start();
if ($_POST['amount'] != '' && $_POST['date_of_transaction'] != '' && $_POST['transaction_no'] != '' && $_POST['mode_of_payment'] != '' && $_POST['fee_head'] != '') {
    require '../../includes/db-config.php';
    $paidFeeQuery = "select sum(Fee) as paidAmount from University_Payments_Misc where Student_ID=" . $_POST['stu_id'] . " AND Sub_Course_ID = " . $_POST['courseId'] . " AND Duration = " . $_POST['duration'] . " AND Fee_Head_ID = ".$_POST['fee_head']."";
    $paidAmount   = $conn->query($paidFeeQuery)->fetch_assoc();

   $totalFeeQuery = "select Amount from University_Course_Fee_Head where Sub_Course_ID =" . $_POST['courseId']." AND University_ID = ".$_SESSION['university_id']." AND Fee_Head_ID = ".$_POST['fee_head']."";

    $totalFee = $conn->query($totalFeeQuery)->fetch_assoc();
    
    if($_POST['edit_fee_head_id'] == $_POST['fee_head']){
      $edit_paid_fee = $_POST['edit_paid_fee'];
    } else {
       $edit_paid_fee = 0;
    }

    if ($paidAmount['paidAmount'] > 0) {
        if ((intval($paidAmount['paidAmount']) + intval($_POST['amount']) - intval($edit_paid_fee)) > $totalFee['Amount']) {
            echo json_encode(['status' => 'error', 'message' => 'Amount cannot more than pending amount ', 'code' => '1001']);
            exit;
        }
    } else { 
        if ($totalFee['Amount'] < $_POST['amount']) {
            echo json_encode(['status' => 'error', 'message' => 'Amount cannot more than pending amount', 'code' => '1002']);
            exit;
        }
    }

    $submitQuery = "UPDATE `University_Payments_Misc` SET
    `Fee` = '" . $_POST['amount'] . "',
    `Transaction_No` = '" . $_POST['transaction_no'] . "',
    `Transaction_Date` = '" . $_POST['date_of_transaction'] . "',
    `Transaction_Mode` = '" . $_POST['mode_of_payment'] . "',
    `Duration` = '" . (int)$_POST['duration'] . "',
    `Fee_Head_ID` = '" . (int)$_POST['fee_head'] . "'
        WHERE `ID` = '" . (int)$_POST['id'] . "'";
    $runQuery = $conn->query($submitQuery);
    if ($runQuery) {
        echo json_encode(['status' => 'success', 'message' => 'University miscellaneous payment added successfully', 'code' => '1003']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error in qury', 'code' => '1004']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'All fields are mandatory', 'code' => '1003']);
    exit;
}
