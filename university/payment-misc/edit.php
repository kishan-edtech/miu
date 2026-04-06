<?php
require '../../includes/db-config.php';
session_start();
$id = $_POST['id'];
$courseId = $_POST['courseId'];
$duration = $_POST['duration'];
$universityId = (int)$_SESSION['university_id'];

$getMisUniFee = $conn->query("
    SELECT ucfh.*, ufh.Fee_Head AS fee_head_name
    FROM University_Course_Fee_Head ucfh
    LEFT JOIN University_Fee_Head ufh 
        ON ucfh.Fee_Head_ID = ufh.ID
    WHERE ucfh.Sub_Course_ID = $courseId
    AND ucfh.University_ID = $universityId AND ufh.ID is NOT NULL
");


$paymentData = $conn->query("select * from University_Payments_Misc where id=".$id)->fetch_assoc();
?>

<div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5 class="fs-4 text-black fw-bold">Update University Miscellaneous Fee</h5>
</div>
  <form action="/ams/university/payment-misc/update" method="post" id="unversity_payment_form">
  <div class="modal-body">
    <div class="row">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="courseId" value="<?=$courseId?>">
        <input type="hidden" name="stu_id" value="<?=isset($paymentData['Student_ID'])? $paymentData['Student_ID']:"" ?>">
        <input type="hidden" name="duration" value="<?= $duration ?>">

        <div class="col-md-6">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" value="<?=$paymentData['Fee']?>">
            <input type="hidden" name="edit_paid_fee" id="edit_paid_fee" class="form-control" value="<?=$paymentData['Fee']?>">
            <input type="hidden" name="edit_fee_head_id" id="edit_fee_head_id" class="form-control" value="<?=$paymentData['Fee_Head_ID']?>">
        </div>
        <div class="col-md-6">
            <label for="date">Date of Transaction</label>
            <input type="date" name="date_of_transaction" id="date_of_transaction" class="form-control" value="<?= !empty($paymentData['Transaction_Date']) 
                ? date('Y-m-d', strtotime($paymentData['Transaction_Date'])) 
                : '' ?>">
        </div>
        <div class="col-md-6">
            <label for="transaction_no">Transaction_no Number</label>
            <input type="text" name="transaction_no" id="transaction_no" class="form-control" value="<?=$paymentData['Transaction_No']?>">
        </div>
        <div class="col-md-6">
            <label for="mode_of_payment">Mode of Transaction</label>
            <select name="mode_of_payment" id="mode_of_payment" class="form-control">
                <option value="Cash" <?=$paymentData['Transaction_Mode']=='Cash'?"selected":""?>>Cash</option>
                <option value="Debit" <?=$paymentData['Transaction_Mode']=='Debit'?"selected":""?>>Debit Card</option>
                <option value="Credit" <?=$paymentData['Transaction_Mode']=='Credit'?"selected":""?>>Credit Card</option>
                <option value="UPI" <?=$paymentData['Transaction_Mode']=='UPI'?"selected":""?>>UPI</option>
                <option value="DD" <?=$paymentData['Transaction_Mode']=='DD'?"selected":""?>>DD</option>
                <option value="Bank Transfer" <?=$paymentData['Transaction_Mode']=='Bank Transfer'?"selected":""?>>Bank Transfer</option>
            </select>
        </div>
         <div class="col-md-6">
            <label for="mode_of_payment">University Fee Head</label>
            <select name="fee_head" id="fee_head" class="form-control">
                <?php  while($fee = $getMisUniFee->fetch_assoc()) { ?>
                <option value="<?php echo $fee['Fee_Head_ID'];?>"  <?=$paymentData['Fee_Head_ID']==$fee['Fee_Head_ID'] ? "selected" : ""?>><?php echo $fee['fee_head_name'];?></option>
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
    $('#unversity_payment_form').submit(function(e){
        e.preventDefault();
        var formdata = new FormData(this);
        $.ajax({
            url:$(this).attr('action'),
            type:$(this).attr('method'),
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success:function(res)
            {
                if(res.status=='success'){
                    $('.modal').modal('hide');
                    notification('success', res.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
              }else{
                notification('error', res.message);
              }              
            }
        })
    })
  </script>