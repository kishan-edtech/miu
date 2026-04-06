<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require '../../../includes/db-config.php';

    $uniStuPayId = mysqli_real_escape_string($conn, $_GET['id']);
    $uniStuPayId = str_replace('W1Ebt1IhGN3ZOLplom9I', '', base64_decode($uniStuPayId));
    // echo "<pre>";
    // print_r($uniStuPayId);die;
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
    <i class="pg-icon">close</i>
  </button>
  <h5>University Payments</h5>
</div>

<div class="modal-body">

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Payment ID</th>
        <th>Student ID</th>
        <th>Amount</th>
        <th>UTR No</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>

      <?php
          //$query = "SELECT * FROM University_Stu_Payments WHERE ID = $uniStuPayId";

          $uniStuPayId = (int) $uniStuPayId;

          $query = "SELECT usp.*,
    up.Fee,
    up.Student_ID
FROM university_stu_payments usp
LEFT JOIN university_payments up
    ON usp.Transaction_No = up.Transaction_No
WHERE usp.ID = $uniStuPayId";

          $result = mysqli_query($conn, $query);

          while ($row = mysqli_fetch_assoc($result)) {

              $students = explode(',', $row['Student_ID']);

              foreach ($students as $student) {

                  // Fetch student name
                  $stuQuery  = "SELECT First_Name ,Middle_Name,Last_Name FROM Students WHERE ID = '$student'";
                  $stuResult = mysqli_query($conn, $stuQuery);
                  $stuData   = mysqli_fetch_assoc($stuResult);

                  $studentName = trim(
                      $stuData['First_Name'] . ' ' .
                      $stuData['Middle_Name'] . ' ' .
                      $stuData['Last_Name']
                  );
              ?>
        <tr>
          <td><?php echo $row['Payment_ID']; ?></td>
          <td><?php echo trim($studentName); ?></td>
          <td><?php echo $row['Fee']; ?></td>
          <td><?php echo $row['Transaction_No']; ?></td>
          <td><?php echo date('d-m-Y', strtotime($row['Transaction_Date'])); ?></td>
        </tr>
<?php
    }
    }
?>

    </tbody>
  </table>

</div>

<div class="modal - footer text - end">
  <button type="button" class="btn btn - secondary" data-dismiss="modal">
    Close
  </button>
</div>
