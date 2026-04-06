<?php
include '../../includes/db-config.php';
session_start();
$id = $_POST['id'];

$duration = $_POST['duration'];
$subCourseId = $_POST['courseId'];
$universityId = (int)$_SESSION['university_id'];

$getMisUniFee = $conn->query("
    SELECT ucfh.*, ufh.Fee_Head AS fee_head_name
    FROM University_Course_Fee_Head ucfh
    LEFT JOIN University_Fee_Head ufh 
        ON ucfh.Fee_Head_ID = ufh.ID
    WHERE ucfh.Sub_Course_ID = $subCourseId
    AND ucfh.University_ID = $universityId AND ufh.ID is NOT NULL
");
?>
<div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5 class="fs-4 text-black fw-bold">Allot University Miscellaneous Fee</h5>
</div>
<form action="/ams/university/payment-misc/store" method="post" id="unversity_payment_form">
    <div class="modal-body">
        <div class="row">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="courseId" value="<?= $subCourseId ?>">
            <input type="hidden" name="duration" value="<?= $duration ?>">

            <div class="col-md-6">
                <label for="amount">Amount</label>
                <input type="number" name="amount" id="amount" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="date">Date of Transaction</label>
                <input type="date" name="date_of_transaction" id="date_of_transaction" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="transaction_no">Transaction Number</label>
                <input type="text" name="transaction_no" id="transaction_no" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="mode_of_payment">Mode of Transaction</label>
                <select name="mode_of_payment" id="mode_of_payment" class="form-control">
                    <option value="Cash">Cash</option>
                    <option value="Debit">Debit Card</option>
                    <option value="Credit">Credit Card</option>
                    <option value="UPI">UPI</option>
                    <option value="DD">DD</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="mode_of_payment">University Fee Head</label>
                <select name="fee_head" id="fee_head" class="form-control">
                    <?php  while($fee = $getMisUniFee->fetch_assoc()) { ?>
                    <option value="<?php echo $fee['Fee_Head_ID'];?>"><?php echo $fee['fee_head_name'];?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-12 m-t-10 sm-m-t-10">
              <button aria-label="" type="button" data-dismiss="modal" class=" btn btn-secondary mr-2">
        <i class="ti ti-circle-dashed-x mr-2"></i> Close</button>
      <button aria-label="" type="submit" class="btn btn-primary ">
        <i class="ti ti-circle-check mr-2"></i> Save</button>
      </div>
    </div>
</form>

<script>
    $('#unversity_payment_form').submit(function (e) {
        e.preventDefault();
        var formdata = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (res) {
                if (res.status == 'success') {
                    $('.modal').modal('hide');
                    notification('success', res.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    notification('error', res.message);
                }
            },
            error: function (data) {
                console.log('An error occurred.');
                console.log(data);
            },
        })
    })
</script>