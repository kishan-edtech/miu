<?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-top.php'; ?>

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
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-bottom.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/menu.php';
    unset($_SESSION['current_session']);
?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/topbar.php'; ?>
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
                          $crumb  = explode("?", $breadcrumbs[$i]);
                          echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                      endif;
                  }
              ?>
              <div>
                <?php if ($_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'University Head') {?>
                  <button class="btn btn-link add_btn_form" aria-label="" title="" data-toggle="tooltip"
                    data-original-title="Upload University Payments" onclick="uploadUniversityPayments()"><i class="ti ti-upload" style="font-size:24px;"></i></button>

                    <button class="btn btn-link add_btn_form" aria-label="" title="" data-toggle="tooltip"
                    data-original-title="Upload Certificate and Marksheet" onclick="uploadCertificateMarksheet()"><i class="ti ti-upload" style="font-size:24px;"></i></button>
                <?php }?>

              </div>
            </ol>
          </div>
        </div>
      </div>
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <?php if (isset($_SESSION['university_id'])) {?>

          <div class="card-body">
            <div class="card card-transparent">

              <div class="tab-content">
                <div class="tab-pane active" id="applications">
                  <div class="table-bordered">
                    <table class="table table-hover nowrap table-responsive" style="margin-top:0px !important" id="application-table">
                      <thead>
                        <tr>
                          <th>Payment ID</th>
                          <th>Student</th>
                           <th>Amount</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php }?>

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

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-top.php'; ?>

  <?php if ($_SESSION['Role'] == 'Administrator' && ! isset($_SESSION['university_id'])) {?>
    <script type="text/javascript">
      changeUniversity();
    </script>
  <?php }?>

  <script type="text/javascript">
    $(function () {
      var role = '<?php echo $_SESSION['Role']; ?>';
      var showInhouse = role != 'Center' && role != 'Sub-Center' ? true : false;
      var hasStudentLogin = '<?php echo isset($_SESSION['has_lms']) && $_SESSION['has_lms'] == 1 ? true : false; ?>';
      var showStatus = false;
      var applicationTable = $('#application-table');
      var is_university = "<?php echo $_SESSION['Designation'] == "University" ? true : false; ?>";

      var applicationSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/ams/app/applications/application-server-university-payouts',
          'type': 'POST',
          complete: function (xhr, responseText) {
            $('#application_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [
        {
          data: "Payment_ID",width:"15%",
        },
        {
            data: "Student_ID_Count",width:"15%",
            "render": function(data, type, row) {
                return '<a href="javascript:void(0);" onclick="openStudentPayment(\'' + row.ID + '\')">'+data+'</a>';
            }
        },
        {
          data: "Amount",width:"1%",
        },
        {
          data: "Transaction_Date",width:"15%",
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
    function uploadUniversityPayments() {
      $.ajax({
        url: '/ams/app/applications/uploads/create_university_payouts',
        type: 'GET',
        success: function (data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }
    function openStudentPayment(id) {
      $.ajax({
        url: '/ams/app/applications/uploads/showStudentPayments?id=' + id,
        type: 'GET',
        success: function (data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }

    function uploadCertificateMarksheet() {
      $.ajax({
        url: '/ams/app/applications/uploads/create_certificate_marksheet',
        type: 'GET',
        success: function (data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }
  </script>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-bottom.php'; ?>