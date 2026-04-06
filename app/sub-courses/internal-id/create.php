<?php
if (isset($_GET['id'])) {
    require '../../../includes/db-config.php';
    session_start();
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $center = $conn->query("SELECT internal_id FROM Sub_Courses WHERE ID = $id");
    $center = mysqli_fetch_assoc($center);
    $internal_id = $center['internal_id'];
}
?>
<!-- Modal -->
<div class="modal-header clearfix text-left mb-1">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Internal ID</h5>
</div>
<form role="form" id="form-Internal-id" action="/ams/app/sub-courses/internal-id/store" method="POST">
    <div class="modal-body">
      <div class="row clearfix">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Internal ID</label>
            <input type="text" name="internal_id" id="internal_id" value="<?php print !empty($internal_id) ? $internal_id : '' ?>" class="form-control" placeholder="">
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer flex justify-content-between">
      <div class="sm-m-t-5">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span><?php print !empty($abc_id) ? 'Update' : 'Add' ?></span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
</form>

<script type="text/javascript">

    $(function(){
        $("#form-Internal-id").validate({
            rules : {
                abc_id : {
                    required : true
                },
            },
            highlight: function(element) {
                $(element).addClass('error');
                $(element).closest('.form-control').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).removeClass('error');
                $(element).closest('.form-control').removeClass('has-error');
            }
        })
    })

    $("#form-Internal-id").on('submit', function(e){
        e.preventDefault();
        if ($('#form-Internal-id').valid()) {
            $(':input[type="submit"]').prop('disabled', true);
            var formData = new FormData(this);
            formData.append('id', '<?= $id ?>');
            $.ajax({
                url: this.action,
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(data) {
                    if (data.status == 200) {
                    $('.modal').modal('hide');
                    notification('success', data.message);
                    $('.table').DataTable().ajax.reload(null, false);
                    } else {
                    $(':input[type="submit"]').prop('disabled', false);
                    notification('danger', data.message);
                    }
                }
            });
        }
    }); 
</script>