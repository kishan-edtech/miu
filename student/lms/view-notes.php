<?php include($_SERVER['DOCUMENT_ROOT'].'/ams/includes/header-top.php'); ?>
<style>
  .copyright {
    background: #fff;
    padding: 15px 0;
    border-top: 1px solid #e0e0e0;
}
</style>

<?php include($_SERVER['DOCUMENT_ROOT'].'/ams/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/ams/includes/menu.php'); ?>
    <!-- START PAGE-CONTAINER -->

<?php 
  $id = $_GET['id'];
  $sub_id = $_GET['sub_id'];
  $base_url="https://".$_SERVER['HTTP_HOST']."/";
  $course_id=$_SESSION['Sub_Course_ID'];
  $student_id=$_SESSION['ID'];

  $query="SELECT notes.`id`, notes.`file_type`, notes.`file_path`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, notes.`status` FROM notes LEFT JOIN Sub_Courses ON Sub_Courses.ID = notes.course_id LEFT JOIN Syllabi ON Syllabi.ID = notes.subject_id WHERE notes.id=$id ";

$results = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($results);
  
?>

    <div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'].'/ams/includes/topbar.php'); ?>  
    <div class="page-content-wrapper ">
    <div class="content pb-0" style="padding-top: 25px;">

      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
        </div>
      </div>
     
      <div class=" container-fluid">
        <div class="card card-transparent">
          <div class="card-header">
            <b><?=$row['subject_name']?></b>  Notes  
            
            <div class="pull-right">
              <div class="col-xs-7 " style="margin-right: 10px;">
              <a class="btn btn-danger p-2 " href="/ams/student/lms/subjects?id=<?= $sub_id; ?>" data-toggle="tooltip" data-original-title="Back" > <i class="uil uil-arrow-circle-left"></i>Back</a>
              </div>
            </div>
            <div class="clearfix"></div>
            </div>
            <div class="card-body">
            <embed src="<?=$base_url?>/ams/<?=$row['file_path']?>#toolbar=0&scrollbar=1&&navpanes=0&controls=0" type="application/pdf" width="100%" height="560px" />
            </div>
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/ams/includes/footer-top.php'); ?>
<script type="text/javascript">

</script>
<?php include($_SERVER['DOCUMENT_ROOT'].'/ams/includes/footer-bottom.php'); ?>
