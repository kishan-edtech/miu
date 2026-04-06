<?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-top.php'); ?>
<style>
      table thead{
    background: #c5cfca ;
  }
  table thead tr th {
    color: #4b4b4b !important;
    font-weight: bold !important;
  }
  #sub-courses-search-table{
      border-radius:10px !important;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                  <?php
                    if($_SESSION['Role']=="Administrator")
                    { ?>
                        <button class="btn btn-link " aria-label="" title="" data-toggle="tooltip" data-original-title="Add Specialization" onclick="add('sub-courses','lg')">
                        <i class="ti ti-copy-plus add_btn_form  add_btn_form" style="font-size:24px"></i>
                        </button>
             
                    <?php }
                  ?>
                <button class="btn btn-link add_btn_form" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="ti ti-square-rounded-arrow-down" style="font-size:24px !important;"></i></button>
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

        <div class="card card-transparent">
          <div class="card-header">
            <div class="pull-right">
              <div class="col-xs-12">
                <input type="text" id="sub-courses-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-bordered">
              <table class="table table-hover nowrap table-responsive " style="margin-top:0px !important;" id="sub-courses-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Program</th>
                    <th>Department</th>
                    <th>Type</th>
                    <th>Mode</th>
                    <th data-orderable="false">Vertical</th>
                    <th data-orderable="false">Status</th>
                    <th data-orderable="false">Internal ID</th>
                    <th data-orderable="false">Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-top.php'); ?>
    <script type="text/javascript">
      $(function() {
        window.BASE_URL = "<?= $base_url ?>";
        var role = '<?= $_SESSION['Role'] ?>';
        var university = '<?= $_SESSION['university_id'] ?>';
        var show = role == 'Administrator' ? true : false;
        var showinternal = (role=="Administrator" && university==41) ? true:false;
        // var showEdit =    ? true : false; 
        var table = $('#sub-courses-table');

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': BASE_URL + '/app/sub-courses/server'
          },
          'columns': [{
              data: "Name",width:"10%"
            },
            {
              data: "Course_ID",width:"30%"
            },
            {
              data: "Department_ID",width:"10%"
            },
            {
              data: "Course_Type",width:"10%"
            },
            {
              data: "Mode_ID",width:"10%"
            },
            {
              data: "University_ID",width:"10%",
              visible: show
            },
            {
              data: "Status",width:"10%",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                var checked = data == 1 ? 'checked' : '';
                return '<div class="form-check form-check-inline switch switch-lg success">\
                  <input onclick="changeStatus(&#39;Sub-Courses&#39;, &#39;' + row.ID + '&#39;)" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
                  <label for="status-switch-' + row.ID + '">' + active + '</label>\
                </div>';
              },
              visible:show
            },
            {
              data: "internal_id",
              "render": function (data, type, row) {
                  var edit = "";
                  var edit = '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Internal ID" onclick="addInternalId(&#39;' + row.ID + '&#39;)">';
                return data + edit;
              },
              visible: showinternal
            },
            {
              data: "ID",width:"10%",
              "render": function(data, type, row) {
                 
                     return '<div class="button-list text-end">\
                <i class="ti ti-square-rounded-plus add_btn_form h5 cursor-pointer" onclick="allotScheme(&#39;' + data + '&#39, &#39;lg&#39;)" data-toggle="tooltip" title="Allot Scheme & Fees"></i>\
                <i class="ti ti-edit-circle add_btn_form h5 cursor-pointer" onclick="edit(&#39;sub-courses&#39;, &#39;' + data + '&#39, &#39;lg&#39;)" data-toggle="tooltip" title="Edit Specializations"></i>\
                <i class="ti ti-trash-x cursor-pointer add_btn_form h5" onclick="destroy(&#39;sub-courses&#39;, &#39;' + data + '&#39)" data-toggle="tooltip" title="Delete Specializations"></i>\
              </div>'   
                 
              },
              visible:show
            },
          ],
          "sDom": "<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          "aaSorting": [],
          "iDisplayLength": 10
        };

        table.dataTable(settings);

        // search box for table
        $('#sub-courses-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });
         $('#sub-courses-table').on('draw.dt', function () {
          $('[data-toggle="tooltip"]').tooltip();
          });
      })
    </script>

    <script>
      function allotScheme(id, modal){
        $.ajax({
          url: BASE_URL + '/app/sub-courses/schemes?id='+id,
          type: 'GET',
          success: function(data){
            $("#"+modal+"-modal-content").html(data);
            $("#"+modal+"modal").modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function changeColumnStatus(id, column) {
        $.ajax({
          url: BASE_URL + '/app/sub-courses/status',
          type: 'post',
          data: {
            id: id,
            column: column
          },
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              notification('success', data.message);
              $('#sub-courses-table').DataTable().ajax.reload(null, false);
            } else {
              notification('danger', data.message);
              $('#sub-courses-table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }
      function addInternalId(id) {
        $.ajax({
          url: BASE_URL + '/app/sub-courses/internal-id/create?id=' + id,
          type: 'POST',
          data: {
            id: id
          },
          success: function (data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function exportData() {
        var search = $('#sub-courses-search-table').val();
        var url = search.length > 0 ? "?search=" + search : "";
        window.open('/app/sub-courses/export' + url);
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-bottom.php'); ?>
