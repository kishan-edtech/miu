<?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-top.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/header-bottom.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/menu.php'; ?>
<?php $id = str_replace('W1Ebt1IhGN3ZOLplom9I', '', base64_decode($_REQUEST['id'])); ?>
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
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
                                if (count($breadcrumbs) == $i): $active = "active";
                                    $crumb                                 = explode("?", $breadcrumbs[$i]);
                                    echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                                endif;
                            }
                            ?>
                            <div>

                            </div>
                        </ol>
                        <!-- END BREADCRUMB -->
                    </div>
                </div>
            </div>
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class="container-fluid">

                <!-- ===== UNIVERSITY FEE SECTION ===== -->
                <div class="card card-default mb-4">
                    <div class="card-body">

                        <?php
                        $query      = "SELECT Students.*,Sub_Courses.* FROM `Students` LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID WHERE Students.ID =  $id";
                        $courseData = $conn->query($query);
                        $record     = $courseData->fetch_assoc();
                        $paidAmount = $conn->query("select sum(Fee) as paidAmount from University_Payments where Student_ID=" . $id)->fetch_assoc();

                        ?>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Student Name</label>
                                <input type="text" class="form-control" value="<?php echo $record['First_Name'] . ' ' . $record['Middle_Name'] . ' ' . $record['Last_Name'] ?>">
                            </div>

                            <div class="col-md-4">
                                <label>Course</label>
                                <input type="text" class="form-control" value="<?php echo $record['Name'] ?>">
                            </div>

                            <div class="col-md-4 text-end">
                                <label class="fw-bold">University Fee: <?php echo $paidAmount['paidAmount'] ?>/<?php echo $record['university_fee'] ?></label>
                            </div>
                        </div>

                        <?php
                        if ($paidAmount['paidAmount'] < $record['university_fee']) {
                        ?>
                            <div class="row pull-right">
                                <div class="col-md-2">
                                    <button class="btn btn-primary"
                                        onclick="addUniversityPayment(<?php echo $id ?>,<?php echo $record['ID'] ?>,<?php echo $record['Duration'] ?>)">Add Payment</button>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SNO</th>
                                        <th>Date of Transaction</th>
                                        <th>Mode of Payment</th>
                                        <th>Transaction No.</th>
                                        <th>Amount</th>
                                        <th>Duration</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $universityPaidAmountQuery = "select * from University_Payments where student_id=" . $id . "";
                                    $paidAmountData            = $conn->query($universityPaidAmountQuery);
                                    $amount                    = 0;
                                    $i                         = 1;
                                    while ($row = $paidAmountData->fetch_assoc()) {

                                        $amount = $amount + $row['Fee'];
                                    ?>
                                        <tr>
                                            <td><?php echo $i ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['Transaction_Date'])); ?></td>
                                            <td><?php echo $row['Transaction_Mode'] ?></td>
                                            <td><?php echo $row['Transaction_No'] ?></td>
                                            <td><?php echo $row['Fee'] ?></td>
                                            <td><?php echo $row['Duration'] ?></td>
                                            <td><a href="javascript:void(0)"
                                                    onclick="editPayment(<?php echo $row['ID'] ?>,<?php echo $record['ID'] ?>,<?php echo $record['Duration'] ?>)"><i class="ti ti-edit-circle add_btn_form h5 cursor-pointer"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>


                <!-- ===== MISCELLANEOUS UNIVERSITY FEE SECTION ===== -->
                <div class="card card-default">
                    <div class="card-body">

                        <h4 class="text-center mb-4">Miscellaneous University Fee</h4>

                        <?php
                        // unpaid university misc fee
                        $subCourseId = (int)$record['ID'];
                        $universityId = (int)$_SESSION['university_id'];

                        $getMisUniFee = $conn->query("
                            SELECT ucfh.*, ufh.Fee_Head AS fee_head_name
                            FROM University_Course_Fee_Head ucfh
                            LEFT JOIN University_Fee_Head ufh 
                                ON ucfh.Fee_Head_ID = ufh.ID
                            WHERE ucfh.Sub_Course_ID = $subCourseId
                            AND ucfh.University_ID = $universityId AND ufh.ID is NOT NULL
                        ");

                        // paid university misc fee
                        $paidAmount = $conn->query("select sum(Fee) as paidAmount from University_Payments_Misc where student_id=" . $id." ")->fetch_assoc();
                        $total_paid_mis_uni_fee = $paidAmount['paidAmount'] ?? 0;
                        ?>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Student Name</label>
                                <input type="text" class="form-control" value="<?php echo $record['First_Name'] . ' ' . $record['Middle_Name'] . ' ' . $record['Last_Name'] ?>">
                            </div>

                            <div class="col-md-4">
                                <label>Course</label>
                                <input type="text" class="form-control" value="<?php echo $record['Name'] ?>">
                            </div>

                            <div class="col-md-4 text-end">
                                <?php 
                                $total_mis_uni_fee = 0;
                                while($fee = $getMisUniFee->fetch_assoc()){

                                 // paid university misc fee
                                $paidAmount = $conn->query("select sum(Fee) as paidAmount from University_Payments_Misc where student_id=" . $id." AND Fee_Head_ID = ".$fee['Fee_Head_ID']." ")->fetch_assoc();
                                $paid_mis_uni_fee = $paidAmount['paidAmount'] ?? 0;

                                $total_mis_uni_fee += $fee['Amount'];

                                echo $fee['fee_head_name'].": ".$paid_mis_uni_fee."/ ".$fee['Amount']."<br>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <?php
                            if ($total_paid_mis_uni_fee  < $total_mis_uni_fee) {
                                ?>
                                <div class="row pull-right">
                                    <div class="col-md-2">
                                        <button class="btn btn-primary"
                                            onclick="addUniversityPaymentMics(<?php echo $id ?>,<?php echo $record['ID'] ?>,<?php echo $record['Duration'] ?>)">Add Payment</button>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SNO</th>
                                        <th>Date of Transaction</th>
                                        <th>Mode of Payment</th>
                                        <th>Transaction No.</th>
                                        <th>Amount</th>
                                        <th>Fee Head</th>
                                        <th>Duration</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $universityPaidAmountQuery = "SELECT up.*, ufh.Fee_Head AS head_name
                                            FROM University_Payments_Misc up
                                            LEFT JOIN University_Fee_Head ufh 
                                                ON up.Fee_Head_ID = ufh.ID
                                            WHERE up.student_id = $id AND ufh.ID is NOT NULL
                                        ";
                                    $paidAmountData            = $conn->query($universityPaidAmountQuery);
                                    $amount                    = 0;
                                    $i                         = 1;
                                    while ($row = $paidAmountData->fetch_assoc()) {

                                        $amount = $amount + $row['Fee'];
                                    ?>
                                        <tr>
                                            <td><?php echo $i ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['Transaction_Date'])); ?></td>
                                            <td><?php echo $row['Transaction_Mode'] ?></td>
                                            <td><?php echo $row['Transaction_No'] ?></td>
                                            <td><?php echo $row['Fee'] ?></td>
                                            <td><?php echo $row['head_name'] ?></td>
                                            <td><?php echo $row['Duration'] ?></td>
                                            <td><a href="javascript:void(0)"
                                                    onclick="editPaymentMics(<?php echo $row['ID'] ?>,<?php echo $record['ID'] ?>,<?php echo $record['Duration'] ?>)"><i class="ti ti-edit-circle add_btn_form h5 cursor-pointer"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- END PAGE CONTENT -->
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-top.php'; ?>

        <script>
            function addUniversityPayment(id, courseId, duration) {
                $.ajax({
                    url: "/ams/university/payment/make",
                    type: "post",
                    data: {
                        id: id,
                        courseId: courseId,
                        duration: duration
                    },
                    success: function(res) {
                        $('#md-modal-content').html(res);
                        $('#mdmodal').modal('show');
                    }
                })
            }

            function editPayment(id, courseId,duration) {
                $.ajax({
                    url: "/ams/university/payment/edit",
                    type: "post",
                    data: {
                        id: id,
                        courseId: courseId,
                        duration: duration,
                    },
                    success: function(res) {
                        $('#md-modal-content').html(res);
                        $('#mdmodal').modal('show');
                    }
                })
            }

            function addUniversityPaymentMics(id, courseId, duration) {
                $.ajax({
                    url: "/ams/university/payment-misc/make",
                    type: "post",
                    data: {
                        id: id,
                        courseId: courseId,
                        duration: duration
                    },
                    success: function(res) {
                        $('#md-modal-content').html(res);
                        $('#mdmodal').modal('show');
                    }
                })
            }

            function editPaymentMics(id, courseId,duration) {
                $.ajax({
                    url: "/ams/university/payment-misc/edit",
                    type: "post",
                    data: {
                        id: id,
                        courseId: courseId,
                        duration: duration,
                    },
                    success: function(res) {
                        $('#md-modal-content').html(res);
                        $('#mdmodal').modal('show');
                    }
                })
            }
        </script>

        <?php include $_SERVER['DOCUMENT_ROOT'] . '/ams/includes/footer-bottom.php'; ?>