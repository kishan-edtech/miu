<?php if(isset($_GET['id'])){
  require '../../../includes/db-config.php';
  $id = intval($_GET['id']);
  $University_Fee_Head = $conn->query("SELECT ID, Fee_Head, University_ID FROM University_Fee_Head WHERE ID = $id");
  if($University_Fee_Head->num_rows>0){
    $University_Fee_Head = $University_Fee_Head->fetch_assoc();
    
    $Fee_Head = $University_Fee_Head['Fee_Head'] ?? ''; 
    
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6 class="font-weight-bold text-black">Edit <span class="semi-bold">Fee Haed</span></h6>
  </div>
  <form role="form" id="form-edit-university-payments" action="/ams/app/components/university-payments/update" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Fee Head</label>
            <input type="text" name="Fee_Head" class="form-control" value="<?php echo $Fee_Head;?>" placeholder="ex: Exam">
          </div>
        </div>
      </div>
      
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-12 m-t-10 sm-m-t-10">
        <button aria-label="" type="button" data-dismiss="modal" class=" btn btn-secondary mr-2">
        <i class="ti ti-circle-dashed-x mr-2"></i> Close</button>
        <button aria-label="" type="submit" class="btn btn-primary ">
        <i class="ti ti-circle-check mr-2"></i> Update</button>
      </div>
    </div>
  </form>
  <script>
    $(function(){
      $('#form-add-university-payments').validate({
        rules: {
          fee: {required:true},
        },
        highlight: function (element) {
          $(element).addClass('error');
          $(element).closest('.form-control').addClass('has-error');
        },
        unhighlight: function (element) {
          $(element).removeClass('error');
          $(element).closest('.form-control').removeClass('has-error');
        }
      });
    })


    $("#form-edit-university-payments").on("submit", function(e){
      if($('#form-edit-university-payments').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?=$University_Fee_Head['ID']?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if(data.status){
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#tableUniversityPayments').DataTable().ajax.reload(null, false);
            }else{
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
        e.preventDefault();
      }
    });
  </script>
<?php }} ?>
