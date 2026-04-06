<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/header-top.php'); 
include('includes/header-bottom.php');
include('includes/menu.php'); ?>
 <?php
//  echo '<pre>';
//  print_r($_SESSION);
//  die;
 ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include('includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <?php
        if ($_SESSION['Role'] == 'Student') {
          include('dashboards/student.php');
        }
        elseif($_SESSION['Role'] == 'Center'){ 
            include('dashboards/authorization.php');
        }
        ?>
        
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php 
    include('includes/footer-top.php');
    include('includes/footer-bottom.php'); 
    ?>