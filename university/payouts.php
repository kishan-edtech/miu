<?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-top.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    .tooltip-inner {
    white-space: pre-wrap;
    max-width: 100% !important;
    text-align: left !important;
     }
    .dropdown-toggle::after {
     content: none !important
    }
   .btn-secondary:not(:disabled):not(.disabled).active, .btn-secondary:not(:disabled):not(.disabled):active, .show>.btn-secondary.dropdown-toggle {
    color: black;
    background-color: white;
    border-color: white;
    border: none;
     }
     [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled){
     border: none !important;
     }
    .dropdown-menu > a.dropdown-item:hover, .dropdown-menu > a.dropdown-item:focus, .dropdown-menu > a.dropdown-item:active {
    color: #212121;
    text-decoration: none;
    background-color: #c5cfca;
    border-radius: 0px !important;
    }
   .select2-container .select2-selection {
    border-radius: 7px !important;
    }
   #startDateFilter{
    border-right: none !important;
    border-top-left-radius: 7px !important;
    border-bottom-left-radius: 7px !important;
    padding-right: 0px !important;
    }
    #endDateFilter{
    border-left:none !important;
     border-top-right-radius: 7px !important;
    border-bottom-right-radius: 7px !important;
    padding-right:0px !important;
     }
    .input-group-addon{
     border-top: solid 1px #d0d0d0 !important;
    border-bottom: solid 1px #d0d0d0 !important;
    color: #d0d0d0 !important;
    padding-top: 5px;
    }
  .label-success {
    background-color: #407260;
    color: #fff;
   }
  .label-important, .label-danger {
    background-color: #be534b;
    color: #fff;
   }
     table thead{
    background: white !important ;
  }
  table thead tr th {
    color: black !important;
    font-weight: bold !important;
  }
  #application-table thead, #not-processed-table thead, #ready-for-verification-table thead, #verified-table thead, #enrolled-table thead{
       background: #c5cfca  !important;
  }
  #application-table thead tr th,
  #not-processed-table thead tr th,
  #ready-for-verification-table thead tr th,
  #verified-table thead tr th,
  #enrolled-table thead tr th{
      color: #4b4b4b !important;
    font-weight: bold !important;
  }
  #application-table_filter label input, #not-processed-table_filter label input, #ready-for-verification-table_filter  label input, #verified-table_filter label input, #enrolled-table_filter label input{
      border-radius: 10px !important;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/menu.php');
unset($_SESSION['current_session']);
unset($_SESSION['filterByDepartment']);
unset($_SESSION['filterBySubCourses']);
unset($_SESSION['filterByDate']);
unset($_SESSION['filterByStatus']);
unset($_SESSION['filterByUser']);
unset($_SESSION['filterByExamStatus']); //sf
unset($_SESSION['filterByVerticalType']);//sf
unset($_SESSION['filterByDuration']);
?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i):
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                <?php if ($_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'University Head') { ?>
                  <button class="btn btn-link add_btn_form" aria-label="" title="" data-toggle="tooltip"
                    data-original-title="Upload OA, Enrollment AND Roll No." onclick="uploadOAEnrollRoll()"><i class="ti ti-upload" style="font-size:24px;"></i></button>
                <?php } ?>
                <button class="btn btn-link add_btn_form" aria-label="" title="" data-toggle="tooltip" data-original-title="Download"
                  onclick="exportData()"> <i class="ti ti-download" style="font-size:24px;"></i></button>
                <button class="btn btn-link add_btn_form" aria-label="" title="" data-toggle="tooltip"
                  data-original-title="Add Student" onclick="window.open('/admissions/application-form');"><i class="ti ti-library-plus-filled" style="font-size:24px"></i></button>
              </div>
            </ol>
            <!-- END BREADCRUMB -->

          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <?php if (isset($_SESSION['university_id'])) { ?>
          <div class="card card-transparent">
            <div class="card-header">
              <div class="d-flex justify-content-start">

                <div class="col-md-2">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sessions"
                      onchange="changeSession(this.value)">
                        <option>Select Session</option>
                      <option value="All">All</option>
                      <?php
                      $role_query = "";
                      if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
                        $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
                        $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
                      }
                      $sessions = $conn->query("SELECT Admission_Sessions.ID,Admission_Sessions.Name,Admission_Sessions.Current_Status FROM Admission_Sessions LEFT JOIN Students ON Admission_Sessions.ID = Students.Admission_Session_ID WHERE Admission_Sessions.University_ID = '" . $_SESSION['university_id'] . "' $role_query GROUP BY Name ORDER BY Admission_Sessions.ID ASC");
                      while ($session = mysqli_fetch_assoc($sessions)) { ?>
                        <option value="<?= $session['Name'] ?>" <?php print $session['Current_Status'] == 1 ? 'selected' : '' ?>><?= $session['Name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <!-- add filter -->
                <?php if($_SESSION['university_id']=="41"){ ?>
                <div class="col-md-2 ">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="duration"
                      onchange="addFilter(this.value, 'duration');">
                      <option value="">Choose Duration</option>
                      <option value="3">3 Months</option>
                      <option value="6">6 Months</option>
                      <option value="1">11 Months Adv Certification</option>
                      <option value="2">11 Months Certified</option>
                    </select>
                  </div>
                </div>
                <?php }else{ ?>
                <div class="col-md-2 ">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="departments"
                      onchange="addFilter(this.value, 'departments');">
                      <option value="">Choose Types</option>
                      <?php $departments = $conn->query("SELECT ID, Name FROM Course_Types WHERE University_ID = " . $_SESSION['university_id']);
                      while ($department = $departments->fetch_assoc()) {
                        echo '<option value="' . $department['ID'] . '">' . $department['Name'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <?php } ?>
                <div class="col-md-2 ">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_courses"
                      onchange="addFilter(this.value, 'sub_courses')" data-placeholder="Choose Sub-Course">
                      <option value="">Choose Sub-Course</option>
                      <?php $programs = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Name FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " $role_query GROUP BY Students.Sub_Course_ID");
                      while ($program = $programs->fetch_assoc()) {
                        echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4 m-b-10">
                  <div class="input-daterange input-group" id="datepicker-range">
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="startDateFilter"
                      name="start" />
                    <div class="input-group-addon">to</div>
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="endDateFilter"
                      onchange="addDateFilter()" name="end" />
                  </div>
                </div>
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="application_status"
                      onchange="addFilter(this.value, 'application_status')" data-placeholder="Choose App. Status">
                      <option value="">Application Status</option>
                      <option value="1">Document Verified</option>
                      <option value="2">Payment Verified</option>
                      <option value="3">Both Verified</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="d-flex justify-content-start">
                  
                <?php if ($_SESSION['Role'] != 'Sub-Center' && $_SESSION['Role'] != 'Center') { ?>
                  <!-- vertical type filter -->
                  <div class="col-md-2 m-b-10">
                    <div class="form-group">
                      <select class="full-width" style="width:40px" data-init-plugin="select2" id="vartical_type"
                        onchange="addFilterVerticalType(this.value,'users', 'vartical_type')"
                        data-placeholder="Choose Vertical Type">
                        <option value="">Vertical Type</option>
                        <option value="1">Edtech</option>
                        <option value="2">IITS</option>
                        <option value="3">Rudra</option>
                      </select>
                    </div>
                  </div>
                  
                <?php } ?>
                <?php
                $allowedRoles = ["Administrator", "Academic Head", "Counsellor", "University Head", "Operations", "Accountant", "Sub-Counsellor"];
                if (in_array($_SESSION['Role'], $allowedRoles)) {
                  ?>
                  <div class="col-md-2 m-b-10">
                    <div class="form-group">
                      <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="center"
                        id="center" onchange="addFilter(this.value, 'users')">
                        <option value="">Select Center</option>
                      </select>
                    </div>
                  </div>
                <?php } ?>
                <?php
                $userArr = array("Academic Head", "Accountant", "Administrator", "Counsellor", "Operations", "Sub-Counsellor", "University Head");
                if ($_SESSION['CanCreateSubCenter'] == 1 || in_array($_SESSION['Role'], $userArr)) { ?>
                  <div class="col-md-2 m-b-10">
                    <div class="form-group">
                      <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_center"
                        onchange="addSubCenterFilter(this.value, 'users')" data-placeholder="Choose Sub Center">

                      </select>
                    </div>
                  </div>
                <?php } ?>
              </div>

              <!-- end filter -->
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="card card-transparent">
              
              <div class="tab-content">
                <div class="tab-pane active" id="applications">
                  <div class="table-bordered">
                    <table class="table table-hover nowrap table-responsive" style="margin-top:0px !important" id="application-table">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Name</th>
                          <th>Enrollment No</th>
                          <th>Course</th>
                          <th>Duration</th>
                          <th>Status</th>
                          <th>Owner Name</th>
                          <th>RM Name</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

      <!-- END PLACE PAGE CONTENT HERE -->
    </div>
    <!-- END CONTAINER FLUID -->
  </div>
  <!-- END PAGE CONTENT -->

  <div class="modal fade slide-up" id="reportmodal" style="z-index:9999" tabindex="-1" role="dialog"
    data-keyboard="false" data-backdrop="static" aria-hidden="false">
    <div class="modal-dialog modal-md">
      <div class="modal-content-wrapper">
        <div class="modal-content" id="report-modal-content">
        </div>
      </div>
    </div>
  </div>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-top.php'); ?>
  <script src="/ams/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="/ams/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
  <script>
    $('#datepicker-range').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      endDate: '0d'
    });
  </script>

  <?php if ($_SESSION['Role'] == 'Administrator' && !isset($_SESSION['university_id'])) { ?>
    <script type="text/javascript">
      changeUniversity();
    </script>
  <?php } ?>

  <script type="text/javascript">
    $(function () {
      var role = '<?php echo $_SESSION['Role']; ?>';
      var showInhouse = role != 'Center' && role != 'Sub-Center' ? true : false;
      var is_accountant = role == 'Accountant' ? true : false;
      var is_operations = ['Operations', 'Administrator', 'University Head'].includes(role) ? true : false;
      var hasStudentLogin = '<?php echo isset($_SESSION['has_lms']) && $_SESSION['has_lms'] == 1 ? true : false; ?>';
      var showStatus = false;
      var applicationTable = $('#application-table');
      var notProcessedTable = $('#not-processed-table');
      var readyForVerificationTable = $('#ready-for-verification-table');
      var verifiedTable = $('#verified-table');
      var processedToUniversityTable = $('#proccessed-to-university-table');
      var enrolledTable = $('#enrolled-table');
      var is_university = "<?php echo $_SESSION['Designation']=="University"?true:false; ?>";

      var applicationSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/ams/app/applications/application-server-payouts',
          'type': 'POST',
          complete: function (xhr, responseText) {
            $('#application_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [
        {
            data: "ID",
            "render": function(data, type, row) {
                var addPayment = '<a href="/ams/university/payment/create?id='+row.ID+'"><i class="fa-solid fa-square-plus"></i></a>'
                return addPayment;
            }
        },
        {
          data: "First_Name",width:"15%",
        },
        {
          data: "Enrollment_No",width:"15%",
        },
        {
          data: "Short_Name",width:"15%",
        },
        {
          data: "Duration",width:"15%",
        },
        {
          data: "Enrolled_Status",width:"15%",
          render: function(data, type, row) {
             if (row.Enrollment_No && row.Enrollment_No.trim() !== '') {
                return "Enrolled";
             }
            var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Processed" : "Processed to University";
            var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : proccessedToUniversity;

            return paymentVerified;
            }
          ,
        },
        {
          data: "Center_Name",width:"15%",
        },
        {
          data: "RM",width:"15%",
        }
        ],
       "sDom": "<'row pt-3 px-3 w-100 p-0 m-0'<'col-sm-6 pr-0 pl-0 custon_text_start'l><'col-sm-6 pr-0 pl-0'f>><t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
              "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
        drawCallback: function (settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

     
      applicationTable.dataTable(applicationSettings);
      

      // search box for table
      $('#application-search-table').keyup(function () {
        applicationTable.fnFilter($(this).val());
      });

     

    })
  </script>

  <script type="text/javascript">
    function changeSession(value) {
      $('input[type=search]').val('');
      updateSession();
    }

    function updateSession() {
      var session_id = $('#sessions').val();
      $.ajax({
        url: '/ams/app/applications/change-session',
        data: {
          session_id: session_id
        },
        type: 'POST',
        success: function (data) {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }
  </script>

  <script type="text/javascript">
    function addEnrollment(id) {
      $.ajax({
        url: '/ams/app/applications/enrollment/create?id=' + id,
        type: 'GET',
        success: function (data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }

    function addOANumber(id) {
      $.ajax({
        url: '/ams/app/applications/oa-number/create?id=' + id,
        type: 'GET',
        success: function (data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }
  </script>

  <script type="text/javascript">
    function exportData() {
    //   var search = $('#application-table').val();
    //   var url = search.length > 0 ? "?search=" + search : "";
    //   console.log(url);return false;
      
      
        //var search = $('#application-search-table').val();
        var search = $('#application-table').val();
        //console.log(search, "sandip");
        var steps_found = $('.nav-tabs').find('li a.active').attr('data-target');
        var steps_found = steps_found.substring(1, steps_found.length);
        var url = search.length > 0 ? "?steps_found=" + steps_found + "&search=" + search : "?steps_found=" + steps_found;
        //var url = search.length > 0 ? "?search=" + search : "";
        window.open('/ams/app/applications/export' + url);
        // window.open('/app/applications/kp' + url);
    }

    function exportDocuments(id) {
      $.ajax({
        url: '/ams/app/applications/document?id=' + id,
        type: 'GET',
        success: function (data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }

    function exportZip(id) {
      window.open('/ams/app/applications/zip?id=' + id);
    }

    function exportPdf(id) {
      window.open('/ams/app/applications/pdf?id=' + id);
    }
  </script>

  <script type="text/javascript">
    function uploadOAEnrollRoll() {
      $.ajax({
        url: '/ams/app/applications/uploads/create_oa_enroll_roll',
        type: 'GET',
        success: function (data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }
  </script>
  <script type="text/javascript">
    function printForm(id) {
      window.open('/forms/<?= $_SESSION['university_id'] ?>/index?student_id=' + id);
    }
  </script>
  <script type="text/javascript">
    function processByCenter(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Process'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/ams/app/applications/process-by-center",
            type: 'POST',
            dataType: 'json',
            data: {
              id: id
            },
            success: function (data) {
              if (data.status == 200) {
                notification('success', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              } else {
                notification('danger', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              }
            }
          });
        } else {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }

    function processedToUniversity(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Process.'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/ams/app/applications/processed-to-university",
            type: 'POST',
            dataType: 'json',
            data: {
              id: id
            },
            success: function (data) {
              if (data.status == 200) {
                notification('success', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              } else {
                notification('danger', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              }
            }
          });
        } else {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }

    function verifyPayment(id) {
      $.ajax({
        url: '/ams/app/applications/review-payment?id=' + id,
        type: 'GET',
        success: function (data) {
          $("#lg-modal-content").html(data);
          $("#lgmodal").modal('show');
        }
      })
    }

    function verifyDocument(id) {
      $.ajax({
        url: '/app/applications/review-documents?id=' + id,
        type: 'GET',
        success: function (data) {
          $('#full-modal-content').html(data);
          $('#fullmodal').modal('show');
        }
      })
    }

    function reportPendency(id) {
      $.ajax({
        url: '/ams/app/pendencies/create?id=' + id,
        type: 'GET',
        success: function (data) {
          $('#report-modal-content').html(data);
          $('#reportmodal').modal('show');
        }
      })
    }

    function uploadPendency(id) {
      $(".modal").modal('hide');
      $.ajax({
        url: '/ams/app/pendencies/edit?id=' + id,
        type: 'GET',
        success: function (data) {
          $("#lg-modal-content").html(data);
          $("#lgmodal").modal('show');
        }
      })
    }
  </script>
  <script type="text/javascript">
    $(document).ready(function () {
        var universit_id= <?= $_SESSION['university_id'] ?>;
        if(universit_id == "41"){
            $("#departments").select2({
        placeholder: "Choose Duration",
      }) 
        }else{
      $("#departments").select2({
        placeholder: "Choose Course Type",
      })
        }
      $("#center").select2({
        placeholder: "Choose Center",
      })
      $("#sub_courses").select2({
        placeholder: "Choose Sub-Courses",
      })
      getCenter('<?php $_SESSION['university_id'] ?>');
      <?php if ($_SESSION['Role'] == "Center" && $_SESSION['CanCreateSubCenter'] == 1) { ?>
        addFilter('<?= $_SESSION["ID"]; ?>', 'users');
      <?php } ?>
    })

    function getCenter() {
      let university_id = '<?= $_SESSION['university_id'] ?>';
      $.ajax({
        url: '/ams/app/application-form/center-form?university_id=' + university_id,
        type: 'GET',
        success: function (data) {
          $('#center').html(data);
          $('#center').val(<?php echo !empty($id) ? $student['Added_For'] : (isset($_GET['lead_id']) ? $lead['User_ID'] : (isset($_SESSION['lead_id']) ? $lead['Added_For'] : '')) ?>);
        }
      })
    }

    function addFilter(id, by) {
      $.ajax({
        url: '/ams/app/applications/filter',
        type: 'POST',
        data: {
          id,
          by
        },
        dataType: 'json',
        success: function (data) {
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
            $("#sub_center").html(data.subCenterName);
            if (by == "users") {
              var subcourse_id = $("#sub_center").val();
              addSubCenterFilter(subcourse_id, by);
            }

            if ('<?= $_SESSION['Role'] ?>' == 'Administrator') {
              // $(".sub_center").html(data.subCenterName);

            }
          }
        }
      })
    }
    function addSubCenterFilter(id, by) {
      $.ajax({
        url: '/ams/app/applications/filter',
        type: 'POST',
        data: {
          id,
          by
        },
        dataType: 'json',
        success: function (data) {

          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
            // $  ("#sub_center").html(data.subCenterName);
          }

        }
      })
    }

    function addDateFilter() {
      var startDate = $("#startDateFilter").val();
      var endDate = $("#endDateFilter").val();
      if (startDate.length == 0 || endDate == 0) {
        return
      }
      var id = 0;
      var by = 'date';
      $.ajax({
        url: '/ams/app/applications/filter',
        type: 'POST',
        data: {
          id,
          by,
          startDate,
          endDate
        },
        dataType: 'json',
        success: function (data) {
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
          }
        }
      })
    }
    
    function getCenterListVerticalType(id, by, vertical_type) {
      $.ajax({
        url: '/ams/app/students/vertical-list?vertical_type=' + vertical_type + '&type=' + by + '&id=' + id,
        type: 'GET',
        success: function(data) {
          // inject into center dropdown
          $("#center").html(data);
        }
      })
    }

    function addFilterVerticalType(id, by, vertical_type) {
      addFilter(id, 'vartical_type');
      getCenterListVerticalType(id, by, vertical_type);
    }

  </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-bottom.php'); ?>