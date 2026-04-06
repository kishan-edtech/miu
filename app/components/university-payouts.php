<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingUniversityPayments">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseUniversityPayments" aria-expanded="false" aria-controls="collapsePageAccess">
          University Payments Fee Head
        </a>
    </div>
  </div>
  <div id="collapseUniversityPayments" class="collapse" role="tabcard" aria-labelledby="headingUniversityPayments">
    <div class="card-body">

    <div class="row p-b-20">
      <div class="col-lg-12 text-end">
        <button type="button" class="btn border-0 shadow-none" onclick="addComponents('university-payments', 'md', <?=$university_id?>)"><i class="ti ti-copy-plus add_btn_form" style="font-size:24px"></i></button>
      </div>
    </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-bordered px-3 py-2">
            <table class="table table-hover nowrap table-responsive" style="margin-top:0px !important;" id="tableUniversityPayments">
              <thead>
                <tr>
                  <th>Fee Head</th>
                  <th data-orderable="false">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 
  $lms = false;
  $has_lms = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Has_LMS = 1");
  if($has_lms->num_rows>0){
    $lms = true;
  }
?>

<script type="text/javascript">
  var table = $('#tableUniversityPayments');
  // var settings = {
  //   'processing': true,
  //   'serverSide': true,
  //   'serverMethod': 'post',
  //   'ajax': {
  //     'url':'/ams/app/components/university-payments/server',
  //     type: 'POST',
  //     "data":function(data) {
  //       data.university_id = '<?=$university_id?>';
  //     },
  //   },
  //   'columns': [  
  //     { data: "Fee_Head",width:"1200px"},
  //     { data: "ID",width:"100px",
  //       "render": function(data, type, row){
  //         return '<div class="text-end">\
  //           <i class="ti ti-edit-circle add_btn_form h5 cursor-pointer" onclick="editComponents(\'university-payments\', \''+data+'\', \'md\');"></i>\
  //           <i class="ti ti-trash-x cursor-pointer add_btn_form h5" onclick="destroyComponents(\'university-payments\', \'UniversityPayments\', \''+data+'\');"></i>\
  //         </div>'
  //       }
  //     },
  //   ],
  //   "sDom": "<t><'row'<p i>>",
  //   "destroy": true,
  //   "scrollCollapse": true,
  //   "oLanguage": {
  //       "sLengthMenu": "_MENU_ ",
  //       "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
  //   },
  //   "aaSorting": [],
  //   "iDisplayLength": 100
  // };

  table.dataTable(settings);
</script>
