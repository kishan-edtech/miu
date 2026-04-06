<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Application form</title>

    <!-- Toastr CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
          integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #toast-container > div {
            opacity: 0.9 !important;
            -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=90) !important;
            filter: alpha(opacity=90) !important;
            max-width: none !important;
        }

        .toast {
            background-color: #5a8dee !important;
        }

        .toast-success {
            background-color: #058243 !important;
        }

        .toast-error {
            background-color: red !important;
        }

        .toast-info {
            background-color: #00cfdd !important;
        }

        .toast-warning {
            background-color: #fdac41 !important;
        }

        .btn-primary {
            background-color: #0192d0;
        }

        .sec {
            padding: 40px 0px 100px;
        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 1px !important;
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            width: 100%;
            border: none !important;
            outline: none;
        }

        .form-control-plaintext {
            border-bottom: 2px dotted #111 !important;
        }

        .f16 {
            font-size: 16px;
            font-weight: 600;
        }

        .btn_w {
            width: 10%;
        }

        .tab-width {
            width: 100%;
        }

        .form_title {
            font-size: 40px;
            font-weight: 700;
        }

        @media (max-width: 425px) {
            .tab-width {
                width: 1150px;
            }

            .btn_w {
                width: 40%;
            }

            .form_title {
                font-size: 27px;
                font-weight: 700;
            }

            .sub_title {
                font-size: 18px;
            }
        }

        .sub_title {
            font-size: 25px;
        }

        .text_wrap {
            word-wrap: break-word;
        }

        .loading {
            display: none;
            color: blue;
            font-weight: bold;
        }

        .error {
            font-size: 13px;
        }

        .is-invalid {
            border: 1px solid red !important;
        }
    </style>
</head>

<body>
      <?php
   ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_GET['centerForm']) && $_GET['centerForm'] !== '') {
     require '../admin/includes/db-config.php';
    $leadID = base64_decode($_GET['centerForm']);
    // echo('<pre>');print_r($leadID);die;
  $leadeQuery=$conn->query("SELECT * FROM web_leads WHERE id= $leadID")->fetch_assoc();
//   echo('<pre>');print_r($leadeQuery);die;
}
?>
<section class="sec">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 col-sm-12 d-flex justify-content-center align-items-center">
                <img src="/ams/assets/images/downloadfooter.webp" alt="" class="img-fluid" style="width: 168px;">
                <h1 class="ms-3">
                    <span class="form_title">VOCATIONAL</span>
                    <span class="sub_title">TRAINING PARTNER</span>
                </h1>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-12 col-sm-12">
                <h1 class="form_title text-center pt-4">
                    Application for Vocational Training Partner
                </h1>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-lg-12">
                <form role="form" id="voc_form">
                    <div id="set-vocform-data">
                        <!-- If you use Laravel, keep csrf otherwise remove -->
                        <input type="hidden" name="voc_form_id" id="voc_form_id" value="" />

                        <!-- A. General Information About the Institute -->
                        <div class="mb-5 mt-5">
                            <div class="row">
                                <h4 class="f16 text_wrap">A. General Information About the Institute</h4>
                                <div class="table-responsive tab-width">
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                        <tr>
                                            <th scope="row">1. Name of Institution<sup class="text-danger">*</sup>:</th>
                                            <td colspan="8">
                                                <input type="text" onkeypress="return isNotNumberKey(event);" name="institution_name" value="<?= (!empty($leadeQuery['institute_name']))?$leadeQuery['institute_name']:''?>" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">2. Postal Address<sup class="text-danger">*</sup>:</th>
                                            <td colspan="8">
                                                <input type="text" name="postal_address" id="postal_address" onkeypress="return isNotNumberKey(event);" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">City/Place <sup class="text-danger">*</sup>:</th>
                                            <td>
                                                <input type="text" name="city_place" id="city_place" onkeypress="return isNotNumberKey(event);" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th>Block/Tehsil<sup class="text-danger">*</sup> :</th>
                                            <td colspan="4">
                                                <input type="text" name="block_tehsil" id="block_tehsil" onkeypress="return isNotNumberKey(event);" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Pin Code<sup class="text-danger">*</sup>:</th>
                                            <td>
                                                <input type="text" onkeypress="return isNumberKey(event);" name="pin_code" id="pin_code" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th scope="row">State <sup class="text-danger">*</sup>:</th>
                                            <td colspan="4">
                                                <select class="form-select" name="state" id="state" onkeypress="return isNotNumberKey(event);" readonly required>
                                                    <option value="">Select State</option>
                                                </select>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">District<sup class="text-danger">*</sup>:</th>
                                            <td colspan="4">
                                                <input type="text" name="district" id="district" onkeypress="return isNotNumberKey(event);" readonly required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">3. Phone No. with STD Code/ Mobile No.<sup class="text-danger">*</sup>:</th>
                                            <td colspan="2">
                                                <input type="tel" maxlength="10" value="<?= (!empty($leadeQuery['mobile_no']))?$leadeQuery['mobile_no']:''?>" onkeypress="return isNumberKey(event);" name="mobile_no_stdcode" id="mobile_no_stdcode" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th>Email <sup class="text-danger">*</sup>:</th>
                                            <td colspan="2">
                                                <input type="email" name="email" id="email" value="<?= (!empty($leadeQuery['email']))?$leadeQuery['email']:''?>" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">4. Name of the Principal<sup class="text-danger">*</sup>:</th>
                                            <td colspan="2">
                                                <input type="text" onkeypress="return isNotNumberKey(event);" name="principal_name" id="principal_name" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th>5. Qualifications of the Principal <sup class="text-danger">*</sup>:</th>
                                            <td colspan="2">
                                                <input type="text" name="qualification_principal" id="qualification_principal" onkeypress="return isNotNumberKey(event);" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Administrative Experience (in Years)<sup class="text-danger">*</sup>:</th>
                                            <td colspan="2">
                                                <input type="number" onkeypress="return isNumberKey(event);" name="administrative_exp" id="administrative_exp" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th>Teaching Experience (in Years) <sup class="text-danger">*</sup>:</th>
                                            <td colspan="2">
                                                <input type="number" onkeypress="return isNumberKey(event);" name="teaching_exp" id="teaching_exp" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- B. Information About the Society/Trust/etc -->
                            <div class="row">
                                <h4 class="f16 text_wrap mt-4 mt-md-0">
                                    B. Information About the Society/Trust/Soleproprietorship/Partnership/Company Running the Institute
                                </h4>
                                <div class="table-responsive tab-width">
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                        <tr>
                                            <th scope="row">1. Name and address of Trust/Society/Etc <sup class="text-danger">*</sup>:</th>
                                            <td colspan="3">
                                                <input type="text" name="name_address" id="name_address" onkeypress="return isNotNumberKey(event);" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">2. Is the Trust/Society/Etc Registered? <sup class="text-danger">*</sup>:</th>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="registered" value="Yes"
                                                           style="border: 1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="registered" value="No"
                                                           style="border: 1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </td>
                                            <th>3. If Yes, under which Act? <sup class="text-danger">*</sup>:</th>
                                            <td>
                                                <input type="text" name="registered_under_act" id="registered_under_act" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                4. Year of Registration<sup class="text-danger">*</sup>:
                                                <p class="mb-0" style="font-size: 14px; font-weight:400;">
                                                    (Certified copy of the Certificate of Registration and Memorandum of the Society is to be enclosed. Enclosure-I)
                                                </p>
                                            </th>
                                            <td>
                                                <input type="text" onkeypress="return isNumberKey(event);" name="registration_year" id="registration_year" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th>Registration No.<sup class="text-danger">*</sup>:</th>
                                            <td>
                                                <input type="text" name="registration_no" id="registration_no" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                5. Whether the Trust/Society/Etc/Management is of non-proprietary character <sup class="text-danger">*</sup>:
                                                <p class="mb-0" style="font-size: 14px; font-weight:400;">
                                                    (List of members with their addresses stating how the members are related to each other to be enclosed. Enclosure-II)
                                                </p>
                                            </th>
                                            <td colspan="4" class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="whether_non_proprietary" value="Yes"
                                                           style="border: 1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="whether_non_proprietary" value="No"
                                                           style="border: 1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">No</label>
                                                </div>
                                                <span class="error text-danger whether_non_proprietary_err"></span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h4 class="f16">6. Name and official address of the Manager/President/Chairman of the Centre <sup class="text-danger">*</sup>:</h4>
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                        <tr>
                                            <th scope="row">Name :</th>
                                            <td colspan="3">
                                                <input type="text" onkeypress="return isNotNumberKey(event);" name="name" id="name" value="<?= (!empty($leadeQuery['contact_person']))?$leadeQuery['contact_person']:''?>" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th scope="row">Designation:</th>
                                            <td colspan="3">
                                                <input type="text" name="designation" id="designation" onkeypress="return isNotNumberKey(event);" required>
                                                <span class="error text-danger designation_err"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Address :</th>
                                            <td colspan="3">
                                                <input type="text" name="address" id="address" onkeypress="return isNotNumberKey(event);" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <th>Phone No. with STD Code:</th>
                                            <td colspan="3">
                                                <input type="tel" maxlength="10" onkeypress="return isNumberKey(event);" name="phone" id="mobile_no" value="<?= (!empty($leadeQuery['mobile_no']))?$leadeQuery['mobile_no']:''?>" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- C. Infrastructural and Academic Facilities -->
                        <div class="mb-5 mt-5">
                            <div class="row">
                                <h4 class="f16">C. Infrastructural and Academic Facilities <sup class="text-danger">*</sup>:</h4>
                                <div class="table-responsive tab-width">
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                        <tr>
                                            <th scope="row">1. Is the Institution/ college located in a rented building or own building?</th>
                                            <td colspan="4">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="institution_located" value="Rented"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Rented</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="institution_located" value="Owned"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Owned</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">a). Area of school/campus<sup class="text-danger">*</sup></th>
                                            <td>
                                                <div class="d-flex justify-content-around">
                                                    <input style="width:70%" onkeypress="return isNumberKey(event);" type="text" name="area_acres" id="area_acres" required>
                                                    <span class="error text-danger" style="display:none;">It is required</span>
                                                    <span>(in Acres)</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <input style="width:70%" onkeypress="return isNumberKey(event);" type="text" name="area_sq" id="area_sq" required>
                                                    <span class="error text-danger" style="display:none;">It is required</span>
                                                    <span>(in sq. Mtrs.)</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">b). Built up area (in sq. Mtrs.) <sup class="text-danger">*</sup></th>
                                            <td colspan="4">
                                                <input type="text" onkeypress="return isNumberKey(event);" name="built_area" id="built_area" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h4 class="f16">2. Infrastructure Details <sup class="text-danger">*</sup>:</h4>
                                    <h4 class="f16">a). Rooms, Libraries and Laboratories</h4>
                                    <table class="table tab-width">
                                        <thead class="tab-width">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Item</th>
                                            <th>No. of Rooms</th>
                                            <th>Size in sq. ft. (l*b)</th>
                                            <th>Area in sq. ft.</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tab-width">
                                        <tr>
                                            <th>1.</th>
                                            <th>Classrooms
                                                <p class="mb-0" style="font-size:14px;font-weight:400;">(min. 300 sq. ft. each)</p>
                                            </th>
                                            <td>
                                                <input type="text" name="classrooms_total_rooms" onkeypress="return isNumberKey(event);" id="classrooms_total_rooms" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="classrooms_size_sqft" onkeypress="return isNumberKey(event);" id="classrooms_size_sqft" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="classrooms_area_sqft" onkeypress="return isNumberKey(event);" id="classrooms_area_sqft" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>2.</th>
                                            <th>Composite Science Lab</th>
                                            <td><input type="text" name="science_lab_total_rooms" onkeypress="return isNumberKey(event);" id="science_lab_total_rooms"></td>
                                            <td><input type="text" name="science_lab_size_sqft" onkeypress="return isNumberKey(event);" id="science_lab_size_sqft"></td>
                                            <td><input type="text" name="science_lab_area_sqft" onkeypress="return isNumberKey(event);" id="science_lab_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>3.</th>
                                            <th>Physics Lab</th>
                                            <td><input type="text" name="physics_lab_total_rooms" onkeypress="return isNumberKey(event);" id="physics_lab_total_rooms"></td>
                                            <td><input type="text" name="physics_lab_size_sqft" onkeypress="return isNumberKey(event);" id="physics_lab_size_sqft"></td>
                                            <td><input type="text" name="physics_lab_area_sqft" onkeypress="return isNumberKey(event);" id="physics_lab_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>4.</th>
                                            <th>Chemistry Lab</th>
                                            <td><input type="text" name="chemistry_lab_total_rooms" onkeypress="return isNumberKey(event);" id="chemistry_lab_total_rooms"></td>
                                            <td><input type="text" name="chemistry_lab_size_sqft" onkeypress="return isNumberKey(event);" id="chemistry_lab_size_sqft"></td>
                                            <td><input type="text" name="chemistry_lab_area_sqft" onkeypress="return isNumberKey(event);" id="chemistry_lab_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>5.</th>
                                            <th>Biology Lab</th>
                                            <td><input type="text" name="biology_lab_total_rooms" onkeypress="return isNumberKey(event);" id="biology_lab_total_rooms"></td>
                                            <td><input type="text" name="biology_lab_size_sqft" onkeypress="return isNumberKey(event);" id="biology_lab_size_sqft"></td>
                                            <td><input type="text" name="biology_lab_area_sqft" onkeypress="return isNumberKey(event);" id="biology_lab_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>6.</th>
                                            <th>Maths Lab</th>
                                            <td><input type="text" name="maths_lab_total_rooms" onkeypress="return isNumberKey(event);" id="maths_lab_total_rooms"></td>
                                            <td><input type="text" name="maths_lab_size_sqft" onkeypress="return isNumberKey(event);" id="maths_lab_size_sqft"></td>
                                            <td><input type="text" name="maths_lab_area_sqft" onkeypress="return isNumberKey(event);" id="maths_lab_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>7.</th>
                                            <th>Computer Lab</th>
                                            <td><input type="text" name="computer_lab_total_rooms" onkeypress="return isNumberKey(event);" id="computer_lab_total_rooms"></td>
                                            <td><input type="text" name="computer_lab_size_sqft" onkeypress="return isNumberKey(event);" id="computer_lab_size_sqft"></td>
                                            <td><input type="text" name="computer_lab_area_sqft" onkeypress="return isNumberKey(event);" id="computer_lab_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>8.</th>
                                            <th>Library</th>
                                            <td><input type="text" name="library_total_rooms" onkeypress="return isNumberKey(event);" id="library_total_rooms"></td>
                                            <td><input type="text" name="library_size_sqft" onkeypress="return isNumberKey(event);" id="library_size_sqft"></td>
                                            <td><input type="text" name="library_area_sqft" onkeypress="return isNumberKey(event);" id="library_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>9.</th>
                                            <th>Other Rooms/Hall</th>
                                            <td><input type="text" name="other_total_rooms" onkeypress="return isNumberKey(event);" id="other_total_rooms"></td>
                                            <td><input type="text" name="other_size_sqft" onkeypress="return isNumberKey(event);" id="other_size_sqft"></td>
                                            <td><input type="text" name="other_area_sqft" onkeypress="return isNumberKey(event);" id="other_area_sqft"></td>
                                        </tr>
                                        <tr>
                                            <th>10.</th>
                                            <th>Special Needs Workshop</th>
                                            <td><input type="text" name="special_workshop_total_rooms" onkeypress="return isNumberKey(event);" id="special_workshop_total_rooms"></td>
                                            <td><input type="text" name="special_workshop_size_sqft" onkeypress="return isNumberKey(event);" id="special_workshop_size_sqft"></td>
                                            <td><input type="text" name="special_workshop_area_sqft" onkeypress="return isNumberKey(event);" id="special_workshop_area_sqft"></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h4 class="f16">
                                        3. Teaching Staff (List of staff indicating qualifications, subject(s) taught & experience etc. to be enclosed. Enclosure-VII) <sup class="text-danger">*</sup>:
                                    </h4>
                                    <table class="table tab-width">
                                        <thead class="tab-width">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Staff</th>
                                            <th>No. of Permanent Teachers</th>
                                            <th>No. of part time Teachers</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tab-width">
                                        <tr>
                                            <th>1.</th>
                                            <th>TGTs (Trained Graduate Teachers)</th>
                                            <td>
                                                <input type="text" name="tgts_permanent" onkeypress="return isNumberKey(event);" id="tgts_permanent" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="tgts_part_time" onkeypress="return isNumberKey(event);" id="tgts_part_time" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="total_tgts" onkeypress="return isNumberKey(event);" id="total_tgts" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>2.</th>
                                            <th>PGTs (Post Graduate Teachers/Lecturers)</th>
                                            <td>
                                                <input type="text" name="pgts_permanent" onkeypress="return isNumberKey(event);" id="pgts_permanent" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="pgts_part_time" onkeypress="return isNumberKey(event);" id="pgts_part_time" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="total_pgts" onkeypress="return isNumberKey(event);" id="total_pgts" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>3.</th>
                                            <th>Librarian</th>
                                            <td>
                                                <input type="text" name="librarian_permanent" onkeypress="return isNumberKey(event);" id="librarian_permanent" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="librarian_part_time" onkeypress="return isNumberKey(event);" id="librarian_part_time" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </td>
                                            <td>
                                                <input type="text" name="total_librarian" onkeypress="return isNumberKey(event);" id="total_librarian" required>
                                                <span class="error text-danger built_area_err"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>4.</th>
                                            <th>Vice Principal/Head Master/Head Mistress</th>
                                            <td>
                                                <input type="text" name="vice_principal_permanent" onkeypress="return isNumberKey(event);" id="vice_principal_permanent" required>
                                                <span class="error text-danger built_area_err"></span>
                                            </td>
                                            <td>
                                                <input type="text" name="vice_principal_part_time" onkeypress="return isNumberKey(event);" id="vice_principal_part_time" required>
                                                <span class="error text-danger built_area_err"></span>
                                            </td>
                                            <td>
                                                <input type="text" name="total_vice_principal" onkeypress="return isNumberKey(event);" id="total_vice_principal" required>
                                                <span class="error text-danger built_area_err"></span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Administrative support staff & Other facilities -->
                        <div class="mb-5 mt-5">
                            <div class="row">
                                <div class="table-responsive tab-width">
                                    <h4 class="f16">4. Administrative support staff:</h4>
                                    <table class="table tab-width">
                                        <thead class="tab-width">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Staff</th>
                                            <th>Permanent</th>
                                            <th>Not Permanent</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tab-width">
                                        <tr>
                                            <th>1.</th>
                                            <th>Clerks</th>
                                            <td><input type="text" name="clerks_permanent" onkeypress="return isNumberKey(event);" id="clerks_permanent"></td>
                                            <td><input type="text" name="clerks_nt_permanent" onkeypress="return isNumberKey(event);" id="clerks_nt_permanent"></td>
                                            <td><input type="text" name="total_clerks" onkeypress="return isNumberKey(event);" id="total_clerks"></td>
                                        </tr>
                                        <tr>
                                            <th>2.</th>
                                            <th>Lab Attendants</th>
                                            <td><input type="text" name="lab_attendants_permanent" onkeypress="return isNumberKey(event);" id="lab_attendants_permanent"></td>
                                            <td><input type="text" name="lab_attendants_nt_permanent" onkeypress="return isNumberKey(event);" id="lab_attendants_nt_permanent"></td>
                                            <td><input type="text" name="total_lab_attendants" onkeypress="return isNumberKey(event);" id="total_lab_attendants"></td>
                                        </tr>
                                        <tr>
                                            <th>3.</th>
                                            <th>Accountants</th>
                                            <td><input type="text" name="accountants_permanent" onkeypress="return isNumberKey(event);" id="accountants_permanent"></td>
                                            <td><input type="text" name="accountants_nt_permanent" onkeypress="return isNumberKey(event);" id="accountants_nt_permanent"></td>
                                            <td><input type="text" name="total_accountants" onkeypress="return isNumberKey(event);" id="total_accountants"></td>
                                        </tr>
                                        <tr>
                                            <th>4.</th>
                                            <th>Peons</th>
                                            <td><input type="text" name="peons_permanent" onkeypress="return isNumberKey(event);" id="peons_permanent"></td>
                                            <td><input type="text" name="peons_nt_permanent" onkeypress="return isNumberKey(event);" id="peons_nt_permanent"></td>
                                            <td><input type="text" name="total_peons" onkeypress="return isNumberKey(event);" id="total_peons"></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h4 class="f16">Other Facilities:</h4>
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                        <tr>
                                            <th>a.</th>
                                            <th>Facility of Toilets</th>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_facilities" value="Available for Boys"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Available for Boys</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_facilities" value="Available for Girls"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Available for Girls</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_facilities" value="Both"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Both</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_facilities" value="Not available"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Not available</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>b.</th>
                                            <th>Facility of Drinking Water</th>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_facilities_drinking_water" value="Yes"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Available</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_facilities_drinking_water" value="No"
                                                           style="border:1px solid rgba(0,0,0,.25) !important;">
                                                    <label class="form-check-label">Not available</label>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h4 class="f16">5. Library Facilities:</h4>
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                        <tr>
                                            <th>Total No. of Books</th>
                                            <td><input type="text" name="library_facilities_ttl_books" onkeypress="return isNumberKey(event);" id="library_facilities_ttl_books"></td>
                                            <th>No. of Dailies (Newspapers)</th>
                                            <td><input type="text" name="library_facilities_newspapers" onkeypress="return isNumberKey(event);" id="library_facilities_newspapers"></td>
                                            <th>No. of Magazines</th>
                                            <td><input type="text" name="library_facilities_magazines" onkeypress="return isNumberKey(event);" id="library_facilities_magazines"></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h4 class="f16">6. Other Facilities available in the school:</h4>
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                        <tr>
                                            <th>
                                                Sports & Games
                                                <input class="form-check-input ms-2" style="border:1px solid rgba(0,0,0,.25) !important;" type="checkbox" value="yes" id="sports_games" name="sports_games">
                                            </th>
                                            <th>
                                                Dance Room
                                                <input class="form-check-input ms-2" style="border:1px solid rgba(0,0,0,.25) !important;" type="checkbox" value="yes" id="dance_room" name="dance_room">
                                            </th>
                                            <th>
                                                Gymnasium
                                                <input class="form-check-input ms-2" style="border:1px solid rgba(0,0,0,.25) !important;" type="checkbox" value="yes" id="gymnasium" name="gymnasium">
                                            </th>
                                            <th>
                                                Music Room
                                                <input class="form-check-input ms-2" style="border:1px solid rgba(0,0,0,.25) !important;" type="checkbox" value="yes" id="music_room" name="music_room">
                                            </th>
                                            <th>
                                                Hostel
                                                <input class="form-check-input ms-2" style="border:1px solid rgba(0,0,0,.25) !important;" type="checkbox" value="yes" id="hostel" name="hostel">
                                            </th>
                                            <th>
                                                Health and Medical Check up
                                                <input class="form-check-input ms-2" style="border:1px solid rgba(0,0,0,.25) !important;" type="checkbox" value="yes" id="medical_checkup" name="medical_checkup">
                                            </th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Other Relevant Information -->
                        <div class="mb-5 mt-5">
                            <h2 class="f16">Other Relevant Information:</h2>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-sm-7 col-form-label">
                                                1. What are the working hours of the applying institution?
                                            </label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control-plaintext"
                                                       onkeypress="return isNumberKey(event);" name="relevant_info1" id="relevant_info1" required>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">
                                                2. Express in a few lines- why does the applying institution want to be associated with Us:
                                            </label>
                                            <div class="col-sm-12">
                                                <textarea class="form-control" name="relevant_info2" id="relevant_info2" rows="5"
                                                          style="border:1px solid rgba(0,0,0,.25) !important;" required></textarea>
                                                <span class="error text-danger" style="display:none;">It is required</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <label class="col-sm-2 col-form-label">3. Place</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control-plaintext"
                                                   onkeypress="return isNotNumberKey(event);" name="place" id="place" required>
                                            <span class="error text-danger" style="display:none;">It is required</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4 d-flex justify-content-between">
                                <input type="submit" name="submit" id="submit" value="Apply Now" class="btn btn-primary btn_w">
                                <div id="set-voc-url" class="d-flex justify-content-end"></div>
                            </div>
                        </div>
                    </div>

                    <div id="loading" class="loading">Loading...</div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        return !(charCode > 31 && (charCode < 48 || charCode > 57));
    }

    function isNotNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        return (charCode > 31 && (charCode < 48 || charCode > 57));
    }

    function fetchStates() {
        var states = [
            "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chandigarh", "Chhattisgarh",
            "Delhi", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jammu & Kashmir", "Jharkhand",
            "Karnataka", "Kerala", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya",
            "Mizoram", "Nagaland", "Orissa", "Puducherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu",
            "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal", "Andaman & Nicobar Islands",
            "Dadra & Nagar Haveli", "Daman & Diu"
        ];

        var stateDropdown = document.getElementById('state');
        stateDropdown.innerHTML = '<option value="">Select State</option>';

        states.forEach(function (state) {
            var option = document.createElement('option');
            option.value = state;
            option.text = state;
            stateDropdown.add(option);
        });
    }

    function fetchPinCodeDetails() {
        var pincode = document.getElementById('pin_code').value;
        if (pincode.length === 6) {
            var url = 'https://api.postalpincode.in/pincode/' + pincode;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data[0].Status === "Success") {
                        var district = data[0].PostOffice[0].District;
                        var state = data[0].PostOffice[0].State;

                        document.getElementById('district').value = district;

                        var stateDropdown = document.getElementById('state');
                        for (var i = 0; i < stateDropdown.options.length; i++) {
                            if (stateDropdown.options[i].value === state) {
                                stateDropdown.selectedIndex = i;
                                break;
                            }
                        }
                    } else {
                        document.getElementById('district').value = "";
                        document.getElementById('state').selectedIndex = 0;
                    }
                })
                .catch(error => {
                    console.error('Error fetching pin code details:', error);
                });
        } else {
            document.getElementById('district').value = "";
            document.getElementById('state').selectedIndex = 0;
        }
    }

    // Validate all required fields (single-step form)
    function validateForm() {
        var form = document.getElementById('voc_form');
        var requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        var isValid = true;

        requiredFields.forEach(function (field) {
            var errorSpan = field.parentElement.querySelector('.error') || field.nextElementSibling;

            if (!field.value) {
                field.classList.add('is-invalid');
                if (errorSpan && errorSpan.classList.contains('error')) {
                    errorSpan.style.display = 'block';
                }
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                if (errorSpan && errorSpan.classList.contains('error')) {
                    errorSpan.style.display = 'none';
                }
            }
        });

        return isValid;
    }

    document.addEventListener("DOMContentLoaded", function () {
        fetchStates();
        document.getElementById('pin_code').addEventListener('input', fetchPinCodeDetails);
    });

    $(document).ready(function () {
        var formSelector = '#voc_form';

        $(formSelector).on('submit', function (event) {
            event.preventDefault();

            if (!validateForm()) {
                toastr.error("Please fill all required fields.");
                return;
            }

            var url = 'https://wilpvocmdu.edtechinnovate.in/app/center-verify/store1'; // backend URL

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.status == 200) {
                        // $('#set-voc-url').html(
                        //     '<a href="/print-voc-form/' + response.voc_form_id +
                        //     '" id="download_pdf" class="btn btn-primary d-none">Print</a>'
                        // );
            //              $('#set-voc-url').html(`
            //     <div class="row mt-4 justify-content-center">
            //         <input type="button"
            //             id="download_pdf"
            //             value="Back"
            //             onclick="window.location.href='https://s-voc.maya.edu.in/';"
            //             class="btn btn-primary btn_w">
            //     </div>
            // `);
            
            $('#set-voc-url').html(
    '<a href="https://wilpvocmdu.edtechinnovate.in/users/appliedcenter" ' +
    'id="download_pdf" ' +
    'class="btn btn-primary d-none">Back</a>'
);
                        $('#download_pdf').removeClass('d-none');
                        $('#submit').hide();

                        toastr.success("Information saved successfully");
                    } else if (response.status == "error") {
                        printErrorMsg(response.msg);
                    } else if (response.status == 400) {
                        // custom handling if needed
                    } else {
                        toastr.error(response.message || "Something went wrong");
                    }
                },
                error: function () {
                    toastr.error("Server error");
                }
            });
        });

        function printErrorMsg(msg) {
            $.each(msg, function (key, value) {
                $('.' + key + '_err').text(value);
                $('.' + key + '_err').addClass('adOn_errorMsg');
                setTimeout(function () {
                    $('.' + key + '_err').text("");
                    $('.' + key + '_err').removeClass('adOn_errorMsg');
                }, 5000);
            });
        }
    });
</script>

</body>
</html>

