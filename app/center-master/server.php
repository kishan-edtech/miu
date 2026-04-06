<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
error_reporting(-1);
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if (isset($_POST['order'])) {
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Users.ID DESC";
}

$center_query = "";

if (in_array($_SESSION['Role'], ['Counsellor'])) {
//   $center_query .= " AND Users.Created_By = " . $_SESSION['ID'];
} 

if ($_SESSION['Role'] !== "Administrator") {
 
  $check_has_unique_center_code = $conn->query("SELECT Center_Suffix FROM Universities WHERE ID = " . $_SESSION['university_id'] . " AND Has_Unique_Center = 1");
  if ($check_has_unique_center_code->num_rows > 0) {
    $center_suffix = mysqli_fetch_assoc($check_has_unique_center_code);
    $center_suffix = $center_suffix['Center_Suffix'];
    $center_query .= " AND Code LIKE '$center_suffix%' AND Users.Is_Unique = 1";
  } else {
    $center_query.= " AND Users.Is_Unique = 0";
  }
}


if ($_SESSION['Role'] == 'Sub-Counsellor' || $_SESSION['Role'] == 'Counsellor') {
  $center_ids = [];
  $user_ids = [];
  $getcenterids = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = " . intval($_SESSION['ID']));
  if ($getcenterids->num_rows > 0) {
      $centerids = $getcenterids->fetch_all(MYSQLI_ASSOC);
      $center_ids = array_column($centerids, 'User_ID');
  }
  $getnotalloteduser = $conn->query("SELECT Users.ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Users.Role = 'Center' AND University_User.User_ID IS NULL");
  if ($getnotalloteduser->num_rows > 0) {
      while ($dataArr = $getnotalloteduser->fetch_assoc()) {
          $user_ids[] = $dataArr['ID'];
      }
  }
  $centers = array_merge($center_ids, $user_ids);
  if (!empty($centers)) {
      $center_ids_str = implode(',', array_map('intval', $centers)); // Secure against SQL injection
      $center_query .= " AND Users.ID IN ($center_ids_str) ";
  }
}

$center_query = '';
$counselloer_query = "";
if ($_SESSION['Role'] == 'University Head') {
  $center_ids = [];
  $getcounselloer = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = " . intval($_SESSION['ID']));
  if ($getcounselloer->num_rows > 0) {
      $centerids = $getcounselloer->fetch_all(MYSQLI_ASSOC);
      $center_ids = array_column($centerids, 'User_ID');
    if (!empty($center_ids)) {
      $center_ids_str = implode(',', array_map('intval', $center_ids)); 
      $counselloer_query = " AND Reporting IN ($center_ids_str) ";
      $getcounselloer = $conn->query("SELECT GROUP_CONCAT(University_User.User_ID) AS sc_ids FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE 1=1 AND Role = 'Center' $counselloer_query");
      if ($getcounselloer->num_rows > 0) {
        $sc_row = $getcounselloer->fetch_assoc()['sc_ids'];
        $center_query.=" AND Users.ID IN(".$sc_row.")";
      } 
    }
  }
}




## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Users.Name like '%" . $searchValue . "%' OR Users.Code like '%" . $searchValue . "%' OR Users.Email like '%" . $searchValue . "%' OR Users.Mobile like '%" . $searchValue . "%' OR CONCAT(Reporting.Name, ' (', Reporting.Code, ')') LIKE '%" . $searchValue . "%')";
}


$verticalQuery = "";
if($_SESSION['Role']!="Administrator"){
    $verticalQuery = " and Users.vertical = ".$_SESSION['vertical']??1;
}
## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Users WHERE Role = 'Center' $center_query $verticalQuery");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Users.ID) as filtered FROM Users LEFT JOIN Users as Reporting on Users.Created_By = Reporting.ID WHERE Users.Role = 'Center' $center_query $verticalQuery $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Users.ID, Users.Name, Users.CanCreateSubCenter, Users.Email, Users.Code, Users.Status, Users.Photo,Users.Internal_ID, CAST(AES_DECRYPT(Users.Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password, CONCAT(Reporting.Name, ' (', Reporting.Code, ')') as Created_By FROM Users LEFT JOIN Users as Reporting on Users.Created_By = Reporting.ID WHERE Users.Role = 'Center' $center_query $verticalQuery $searchQuery $orderby LIMIT " . $row . "," . $rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {


// credit amount
  $credit_query = $conn->query("SELECT SUM(Amount) AS totalamount FROM Wallets WHERE Added_By = '" . $row['ID'] ."' AND Status=1 AND Payment_For = 1 ");
  $creditamount = mysqli_fetch_assoc($credit_query);
  $total_credit_amount = isset($creditamount['totalamount']) ? $creditamount['totalamount'] : 0;
// debit amount
  $debit_query = $conn->query("SELECT SUM(Amount) AS totalamount FROM Wallet_Payments WHERE Added_By = '" . $row['ID'] ."' AND Status=1");
  $debit_amount = mysqli_fetch_assoc($debit_query);
  $total_debit_amount = isset($debit_amount['totalamount']) ? $debit_amount['totalamount'] : 0;
  $current_amount = $total_credit_amount -  $total_debit_amount;
  $data[] = array(
    "Photo" => '/ams'.$row['Photo'],
    "Name" => $row['Name'],
    "Email" => $row['Email'],
    "Code" => $row['Code'],
    "Password" => $row['password'],
    "CanCreateSubCenter" => $row['CanCreateSubCenter'],
    "Status" => $row["Status"],
    "Created_By" => $row['Created_By'],
    "ID" => $row["ID"],
    'totalAmount' => $current_amount,
    "Internal_ID" => isset($row["Internal_ID"]) ? $row["Internal_ID"] : ''
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
