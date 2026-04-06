<?php 
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';

    $id = intval($_GET['id']);
    $user = $conn->query("SELECT Name, vertical FROM Users WHERE ID = $id");
    $user = mysqli_fetch_assoc($user);
  }
?>

<?php
session_start();
$role = $_SESSION['Role'];
$disabled ="";
$vertical = 1;
if($role!='Administrator')
{
    $disabled = "disabled";
    $vertical = $_SESSION['vertical'];
}

?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5 class="font-weight-bold text-black">Edit <span class="semi-bold"></span>Sub-Center</h5>
</div>
<form role="form" id="form-edit-sub-centers" action="/ams/app/sub-centers/update" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
        <div class="col-md-12 pr-0">
          <div class="form-group form-group-default required">
            <label>Vertical</label>
            <select class="full-width" style="border: transparent;" name="vertical" id="vertical" <?=$disabled?>>
              <option value="1" <?php if($user['vertical']==1){ echo "selected";}else{echo "";}  ?>>Edtech</option>
              <option value="2" <?php if($user['vertical']==2){ echo "selected";}else{echo "";}  ?>>IITS</option>
              <option value="3" <?php if($user['vertical']==3){ echo "selected";}else{echo "";}  ?>>Rudra</option>
            </select>
          </div>
        </div>
      <div class="col-md-12 pl-0">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Jhon Doe" value="<?=$user['Name']?>" required>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-12 m-t-10 sm-m-t-10">
      <!--<button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">-->
      <!--  <span>Update</span>-->
      <!--  <span class="hidden-block">-->
      <!--    <i class="pg-icon">tick</i>-->
      <!--  </span>-->
      <!--</button>-->
            <button aria-label="" type="button" data-dismiss="modal" class=" btn btn-secondary mr-2">
        <i class="ti ti-circle-dashed-x mr-2"></i> Close</button>
      <button aria-label="" type="submit" class="btn btn-primary ">
        <i class="ti ti-circle-check mr-2"></i> Update</button>
    </div>
  </div>
</form>

<script>
  $(function(){
    $('#form-edit-sub-centers').validate({
      rules: {
        name: {required:true},
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

  $("#form-edit-sub-centers").on("submit", function(e){
    if($('#form-edit-sub-centers').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      formData.append('id', '<?=$id?>');
      $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if(data.status==200){
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#users-table').DataTable().ajax.reload(null, false);
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
