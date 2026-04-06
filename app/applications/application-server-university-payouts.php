<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
ini_set('display_errors', 1);
## Read value
$draw       = $_POST['draw'];
$row        = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if (isset($_POST['order'])) {
    $columnIndex     = $_POST['order'][0]['column'];            // Column index
    $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir'];               // asc or desc
}

$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value

## Search
$searchQuery = " ";
if ($searchValue != '') {
    $searchQuery = "AND Payment_ID like '%" . $searchValue . "%' OR Amount like '%" . $searchValue . "%'";
}

//print_r($searchQuery);die;

if (isset($columnSortOrder)) {
    $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
    $orderby = "ORDER BY University_Stu_Payments.ID DESC";
}

$all_count = $conn->query("SELECT COUNT(University_Stu_Payments.ID) as allcount FROM University_Stu_Payments  WHERE 1=1 $searchQuery $orderby");

$records      = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count          = $conn->query("SELECT COUNT(University_Stu_Payments.ID) as filtered FROM University_Stu_Payments  WHERE 1=1 $searchQuery $orderby");
$records               = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT * from University_Stu_Payments WHERE 1=1 $searchQuery $orderby";

//print_r($result_record);die;

$empRecords = mysqli_query($conn, $result_record);
$data       = [];
// echo $result_record ; die;
while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = [
        "Student_ID_Count" => count(explode(',', $row['Student_ID'])),
        "Student_ID_Count" => count(explode(',', $row['Student_ID'])),
        "Payment_ID"       => $row['Payment_ID'],
        "Amount"           => $row['Amount'],
        "UTR_No"           => $row['Transaction_No'],
        "Transaction_Date" => date('d-m-y', strtotime($row['Transaction_Date'])),
        "ID"               => base64_encode($row['ID'] . 'W1Ebt1IhGN3ZOLplom9I'),
    ];
}

## Response
$response = [
    "draw"                 => intval($draw),
    "iTotalRecords"        => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData"               => $data,
];

echo json_encode($response);
