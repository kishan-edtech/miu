<?php
ini_set('display_errors', 1);

session_start();
require '../includes/db-config.php';
require('../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

$query = " AND Students.University_ID = ".$_SESSION['university_id'];
$orderby = "ORDER BY Students.ID Desc";
$header = array('Student Name', 'Student ID', 'Enrollment No', 'Course Name',
'Center Name', 'Published At');

## Fetch records
$result_record = "SELECT   CONCAT(Students.First_Name, ' ', Students.Middle_Name,' ',Students.Last_Name) AS student_name , 
Students.Unique_ID, Students.Enrollment_No, Sub_Courses.Name AS subcourse_name,Users.Name as center_name,
DATE_FORMAT(marksheets.created_at, '%d-%m-%Y') as published_on FROM Students JOIN Courses AS c ON 
Students.Course_ID = c.ID JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID JOIN marksheets ON
Students.ID = marksheets.enrollment_no JOIN Users ON
Students.Added_For = Users.ID  WHERE Students.Deleted_At IS NULL $query AND Sub_Courses.Status=1 GROUP BY marksheets.enrollment_no $orderby";

$resultsRecords = mysqli_query($conn, $result_record);

$data[] = $header;

while ($row = mysqli_fetch_assoc($resultsRecords)) {
  $data[] = $row;
}

//echo "<pre>";print_r($header); print_r($data); die;
$xlsx = SimpleXLSXGen::fromArray($data)->downloadAs('Results.xlsx');