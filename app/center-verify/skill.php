<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Application form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #toast-container>div {
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

        .form_title {
            font-size: 40px;
            font-weight: 700;
        }

        .sub_title {
            font-size: 25px;
        }

        .btn_w {
            width: 10%;
        }

        .tab-width {
            width: 100%;
        }

        .text_wrap {
            word-wrap: break-word;
        }

        input.is-invalid {
            border: 1px solid red !important;
        }

        @media(max-width:425px) {
            .tab-width {
                width: 561px;
            }

            .btn_w {
                width: 40%;
            }

            .form_title {
                font-size: 27px;
                font-weight: 700;
            }

            .sub_title {
                font-size: 14px;
            }
        }
        
        
        /*###*/
        /* ==============================
   GLOBAL FORM UI FIX
============================== */
table {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

th {
    background: #f8f9fa;
    width: 28%;
    vertical-align: middle;
    font-weight: 600;
}

td {
    vertical-align: middle;
}

/* ==============================
   INPUTS & SELECTS
============================== */
input,
select,
textarea {
    border: 1px solid #ced4da !important;
    padding: 8px 10px;
    border-radius: 4px;
    font-size: 14px;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #0192d0 !important;
    box-shadow: 0 0 0 0.15rem rgba(1, 146, 208, 0.25);
}

/* File input fix */
input[type="file"] {
    padding: 6px;
    background: #fff;
}

/* ==============================
   SECTION HEADINGS
============================== */
.f16 {
    font-size: 18px;
    font-weight: 700;
    padding-bottom: 10px;
    border-bottom: 2px solid #0192d0;
    margin-bottom: 20px;
}

/* ==============================
   ERROR UI
============================== */
.error {
    font-size: 12px;
    margin-top: 4px;
    
}

.is-invalid {
    border-color: red !important;
}

/* ==============================
   BUTTONS
============================== */
.btn_w {
    width: auto;
    padding: 10px 28px;
    font-size: 15px;
}

.btn-primary {
    background-color: #0192d0;
    border: none;
}

.btn-primary:hover {
    background-color: #017bb0;
}

/* ==============================
   DECLARATION AREA
============================== */
.form-control-plaintext {
    border-bottom: 2px dotted #333 !important;
    padding: 6px 4px;
}

/* ==============================
   RESPONSIVE FIX
============================== */
@media (max-width: 768px) {

    th {
        width: 100%;
        display: block;
        background: none;
        padding-top: 12px;
    }

    td {
        display: block;
        width: 100%;
    }

    table,
    tbody,
    tr {
        display: block;
    }

    .tab-width {
        width: 100% !important;
    }

    .btn_w {
        width: 100%;
        margin-bottom: 10px;
    }

    .form_title {
        font-size: 26px;
        text-align: center;
    }

    .sub_title {
        font-size: 14px;
    }
}


input.is-invalid,
select.is-invalid,
textarea.is-invalid,
input[type="file"].is-invalid,
input[type="date"].is-invalid {
    border: 2px solid red !important;
    outline: none;
}
.error {
    color: red;
    font-size: 12px;
    display: block;
    margin-top: 4px;
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
  $leadeQuery=$conn->query("SELECT * FROM web_leads WHERE id= $leadID")->fetch_assoc();
//   echo('<pre>');print_r($leadeQuery);die;
}
?>

    <section>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="col-md-12 col-sm-12 float-md-start float-sm-start d-flex flex-row d-flex justify-content-center align-items-center">
                        <img src="/ams/assets/images/downloadfooter.webp" alt="" class="img-fluid" style="width: 168px;">
                        <h1><span class="form_title ">SKILL</span>
                            <span class="sub_title">DEVLOPMENT DIVISION</span>
                        </h1>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <h1 class="form_title  text-center pt-4">Service Partner Form</h1>
                </div>
                <div class="col-lg-12">
                    <!--<form role="form" id="service_partner" class="dz-form pb-3">-->
                    <form
  role="form"
  id="service_partner"
  class="dz-form pb-3"
  method="POST"
  enctype="multipart/form-data"
>

                       
                        <input type="hidden" name="service_id" id="service_id" value="">
                        <div id="set-show-service">
                            <!-- Service Partner Details -->
                            <div class="row mb-5 mt-5">
                                <h4 class="f16">A. Service Partner Details</h4>
                                <div class="table-responsive tab-width">
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                            <tr>
                                                <th scope="row">Name of Institution<sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="text" onkeypress="return isNotNumberKey(event);" name="skill_name_intstution" value="<?= (!empty($leadeQuery['institute_name']))?$leadeQuery['institute_name']:''?>" id="skill_name_intstution" required>
                                                    <span class="error text-danger skill_name_intstution_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Name of Director<sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="text" onkeypress="return isNotNumberKey(event);" name="dir_name" id="dir_name" value="<?= (!empty($leadeQuery['contact_person']))?$leadeQuery['contact_person']:''?>" required>
                                                    <span class="error text-danger dir_name_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Pin Code<sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" onkeypress="return isNumberKey(event);" name="dir_pincode" id="dir_pincode" required>
                                                    <span class="error text-danger dir_pincode_err" style="display:none;">It is required</span>
                                                </td>
                                                <th>Name of State<sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <select class="form-select" name="dir_state" id="dir_state" readonly>
                                                        <option value="">Select State</option>
                                                    </select>
                                                    <span class="error text-danger dir_state_err"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Name of district<sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" onkeypress="return isNotNumberKey(event);" name="dir_district" id="dir_district" required readonly>
                                                    <span class="error text-danger dir_district_err" style="display:none;">It is required</span>
                                                </td>
                                                <th>Mobile No<sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="tel" onkeypress="return isNumberKey(event);" maxlength="10" minlength="10" name="dir_mob_number" value="<?= (!empty($leadeQuery['mobile_no']))?$leadeQuery['mobile_no']:''?>" id="dir_mob_number" required>
                                                    <span class="error text-danger dir_mob_number_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Address: (full address of the proposed site)<sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="text" name="dir_address" id="dir_address" required>
                                                    <span class="error text-danger dir_address_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Official Contact Details<sup class="text-danger">*</sup>:</th>
                                                <td colspan="4">
                                                    <input type="text" name="dir_contact_details" id="dir_contact_details" value="<?= (!empty($leadeQuery['mobile_no']))?$leadeQuery['mobile_no']:''?>" required>
                                                    <span class="error text-danger dir_contact_details_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email ID<sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="email" name="dir_email" id="dir_email" value="<?= (!empty($leadeQuery['email']))?$leadeQuery['email']:''?>" required>
                                                    <span class="error text-danger dir_email_err" style="display:none;">It is required</span>
                                                </td>
                                                <th>Aadhaar No <sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" maxlength="12" onkeypress="return isNumberKey(event);" name="dir_aadhar" id="dir_aadhar" required>
                                                    <span class="error text-danger dir_aadhar_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Centre Manager Details -->
                            <div class="row mb-5">
                                <h4 class="f16">B. Centre Manager Details</h4>
                                <div class="table-responsive tab-width">
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                            <tr>
                                                <th scope="row">Name of Centre Manager <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="text" onkeypress="return isNotNumberKey(event);" name="cmgr_name" id="cmgr_name" required>
                                                    <span class="error text-danger cmgr_name_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Pin Code<sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" onkeypress="return isNumberKey(event);" name="cmgr_pincode" id="cmgr_pincode" required>
                                                    <span class="error text-danger cmgr_pincode_err" style="display:none;">It is required</span>
                                                </td>
                                                <th>Name of State <sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <select class="form-select" name="cmgr_state" id="cmgr_state" readonly>
                                                        <option value="">Select State</option>
                                                    </select>
                                                    <span class="error text-danger cmgr_state_err"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Name of district <sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" onkeypress="return isNotNumberKey(event);" name="cmgr_district" id="cmgr_district" required readonly>
                                                    <span class="error text-danger cmgr_district_err" style="display:none;">It is required</span>
                                                </td>
                                                <th>Mobile No <sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" maxlength="10" onkeypress="return isNumberKey(event);" name="cmgr_mob_number" id="cmgr_mob_number" value="" required>
                                                    <span class="error text-danger cmgr_mob_number_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Address: (full address of the proposed site) <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="text" name="cmgr_address" id="cmgr_address" required>
                                                    <span class="error text-danger cmgr_address_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Official Contact Details <sup class="text-danger">*</sup>:</th>
                                                <td colspan="4">
                                                    <input type="text" name="cmgr_contact_details" id="cmgr_contact_details" value="<?= (!empty($leadeQuery['mobile_no']))?$leadeQuery['mobile_no']:''?>" required>
                                                    <span class="error text-danger cmgr_contact_details_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email ID <sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" name="cmgr_email" id="cmgr_email" value="<?= (!empty($leadeQuery['email']))?$leadeQuery['email']:''?>" required>
                                                    <span class="error text-danger cmgr_email_err"></span>
                                                </td>
                                                <th>Aadhaar No <sup class="text-danger">*</sup>:</th>
                                                <td>
                                                    <input type="text" maxlength="12" onkeypress="return isNumberKey(event);" name="cmgr_aadhar" id="cmgr_aadhar" required>
                                                    <span class="error text-danger cmgr_aadhar_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Infrastructural details -->
                            <div class="row mb-5">
                                <h4 class="f16">C. Infrastructural details <sup class="text-danger">*</sup>:</h4>
                                <div class="table-responsive tab-width">
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                            <tr>
                                                <th scope="row">If multi-storied building, the floors being proposed for training <sup class="text-danger">*</sup>:</th>
                                                <td colspan="4">
                                                    <input type="text" name="infra_dtls_training" id="infra_dtls_training">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Total built-up area (in Sq Ft) <sup class="text-danger">*</sup>:</th>
                                                <td colspan="4">
                                                    <input type="text" onkeypress="return isNumberKey(event);" name="infra_dtls_builtup" id="infra_dtls_builtup" required>
                                                    <span class="error text-danger infra_dtls_builtup_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Total compound area (in Sq Ft) <sup class="text-danger">*</sup>:</th>
                                                <td colspan="4">
                                                    <input type="text" onkeypress="return isNumberKey(event);" name="infra_dtls_compound" id="infra_dtls_compound" required>
                                                    <span class="error text-danger infra_dtls_compound_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Type of Ownership <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="text" name="infra_dtls_types_ownership" id="infra_dtls_types_ownership" required>
                                                    <span class="error text-danger infra_dtls_types_ownership_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">If Leased/Rented, Lease or rent tenure left <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="text" name="infra_dtls_leased_rented" id="infra_dtls_leased_rented" required>
                                                    <span class="error text-danger infra_dtls_leased_rented_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- File Uploads -->
                            <div class="row mb-5">
                                <div class="table-responsive tab-width">
                                    <table class="table tab-width">
                                        <tbody class="tab-width">
                                            <tr>
                                                <th scope="row">Approach Road <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="approach_road" id="approach_road" required>
                                                    <span class="error text-danger approach_road_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Front View <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="front_view" id="front_view" required>
                                                    <span class="error text-danger front_view_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Back View <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="back_view" id="back_view" required>
                                                    <span class="error text-danger back_view_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Reception Area <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="reception_area" id="reception_area" required>
                                                    <span class="error text-danger reception_area_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Domain Lab <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="domain_lab" id="domain_lab" required>
                                                    <span class="error text-danger domain_lab_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Classroom <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="classroom" id="classroom" required>
                                                    <span class="error text-danger classroom_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Washrooms <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="washrooms" id="washrooms" required>
                                                    <span class="error text-danger washrooms_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">IT Lab <sup class="text-danger">*</sup>:</th>
                                                <td colspan="3">
                                                    <input type="file" name="it_lab" id="it_lab" required>
                                                    <span class="error text-danger it_lab_err" style="display:none;">It is required</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Declaration -->
                            <div class="row mb-5">
                                <h2 class="text-center f16">DECLARATION</h2>
                                <div class="col-md-12">
                                    <p>This to certify that all the above information furnished regarding the Institution/ College is Correct and authentic to the best of my knowledge</p>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6 float-md-start float-sm-start">
                                        <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-2 col-form-label">Date:</label>
                                            <div class="col-sm-10">
                                                <!-- <input type="text" class="form-control-plaintext" name="date" id="date" onkeypress="return isNumberKey(event);" required> -->
                                                 <input
    type="date"
    class="form-control"
    name="date"
    id="date"
    required 
>
<span class="error text-danger date_err" style="display:none;">It is required</span>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 float-md-start float-sm-start">
                                        <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-5 col-form-label">(Signature, Head of the Institution)</label>
                                            <div class="col-sm-7">
                                                <input type="file" class="form-control-plaintext" name="signature" id="signature" required>
                                                <span class="error text-danger signature_err" style="display:none;">It is required</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 float-md-start float-sm-start">
                                        <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-2 col-form-label">Place:</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control-plaintext" name="place" id="place" required>
                                                <span class="error text-danger place_err" style="display:none;">It is required</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 float-md-start float-sm-start">
                                        <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-5 col-form-label">(Name with Rubber Stamp):</label>
                                            <div class="col-sm-7">
                                                <input type="file" class="form-control-plaintext" name="rubber_stamp" id="rubber_stamp" required>
                                                <span class="error text-danger rubber_stamp_err" style="display:none;">It is required</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4 justify-content-between">
                                <div class="col-md-6">
                                    <button class="btn btn-danger btn_w" type="button" onclick="history.back();">Go Back</button>
                                </div>
                                <div class="col-md-6 text-end">
                                    <input type="submit" name="submit" id="submit" value="Apply Now" class="btn btn-primary btn_w">
                                </div>
                            </div>
                        </div>
                        
                        <!-- <div class="row mt-4 justify-content-center">-->
                        <!--    <input type="button" id="download_pdf" value="Download Pdf" onclick="window.print()" class="btn btn-primary d-none btn_w">-->
                        <!--</div>-->
                         <div class="row mt-4 justify-content-center">
                            <input type="button" id="download_pdf" value="Back" onclick="window.location.href='https://wilpvocmdu.edtechinnovate.in/users/appliedcenter';" class="btn btn-primary d-none btn_w">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
   <script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('service_partner');

    /* ================================
       FIELD VALIDATION
    ================================ */
    function validateField(field) {
        let valid = true;
        const errorSpan = field.nextElementSibling;

        if (field.hasAttribute('required')) {

            if (field.type === 'file') {
                if (field.files.length === 0) valid = false;
            } else {
                if (!field.value.trim()) valid = false;
            }

            if (!valid) {
                field.classList.add('is-invalid');
                // field.style.setProperty('border', '1px solid red', 'important');
                if (errorSpan && errorSpan.classList.contains('error')) {
                    errorSpan.style.display = 'block';
                }
            } else {
                field.classList.remove('is-invalid');
                field.style.border = '';
                if (errorSpan && errorSpan.classList.contains('error')) {
                    errorSpan.style.display = 'none';
                }
            }
        }

        return valid;
    }

    function validateForm() {
        let isValid = true;
        let firstError = null;

        const fields = form.querySelectorAll(
            'input[required], select[required], textarea[required]'
        );

        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
                if (!firstError) firstError = field;
            }
        });

        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }

        return isValid;
    }

    // Live validation
    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', () => validateField(field));
        field.addEventListener('change', () => validateField(field));
    });

    /* ================================
       FORM SUBMIT (AJAX)
    ================================ */
    
form.addEventListener('submit', async function (e) {
    e.preventDefault();

    if (!validateForm()) {
        toastr.error('Please fill all required fields');
        return;
    }

    const formData = new FormData(form);

    try {
        const response = await fetch(
            'https://wilpvocmdu.edtechinnovate.in/app/center-verify/store',
            {
                method: 'POST',
                body: formData,
                credentials: 'omit' // important for CORS
            }
        );

        const data = await response.json();
        console.log(data);

        if (data.status === 200) {
            document.getElementById('service_id').value = data.service_id;
            document.getElementById('download_pdf').classList.remove('d-none');
            document.getElementById('submit').style.display = 'none';
            toastr.success('Information saved successfully');
            showServicePartner();
            form.reset();
        } 
        else if (data.status === 'error') {
            printErrorMsg(data.msg);
        } 
        else {
            toastr.error(data.message || 'Something went wrong');
        }

    } catch (error) {
        console.error(error);
        toastr.error('Server error. Please try again.');
    }
});


});

/* ================================
   NUMBER / TEXT RESTRICTIONS
================================ */
function isNumberKey(evt) {
    let charCode = evt.which ? evt.which : evt.keyCode;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}

function isNotNumberKey(evt) {
    let charCode = evt.which ? evt.which : evt.keyCode;
    return (charCode > 31 && (charCode < 48 || charCode > 57));
}

/* ================================
   STATE LIST
================================ */
const states = [
    "Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chandigarh","Chhattisgarh",
    "Delhi","Goa","Gujarat","Haryana","Himachal Pradesh","Jammu & Kashmir","Jharkhand",
    "Karnataka","Kerala","Lakshadweep","Madhya Pradesh","Maharashtra","Manipur","Meghalaya",
    "Mizoram","Nagaland","Orissa","Puducherry","Punjab","Rajasthan","Sikkim","Tamil Nadu",
    "Telangana","Tripura","Uttar Pradesh","Uttarakhand","West Bengal",
    "Andaman & Nicobar Islands","Dadra & Nagar Haveli","Daman & Diu"
];

function populateStates(selectId) {
    const dropdown = document.getElementById(selectId);
    dropdown.innerHTML = '<option value="">Select State</option>';
    states.forEach(state => {
        dropdown.add(new Option(state, state));
    });
}

/* ================================
   PINCODE FETCH (DIR)
================================ */
function fetchPinCodeDetails() {
    const pin = document.getElementById('dir_pincode').value;
    if (pin.length !== 6) return;

    fetch(`https://api.postalpincode.in/pincode/${pin}`)
        .then(res => res.json())
        .then(data => {
            if (data[0].Status === "Success") {
                document.getElementById('dir_district').value = data[0].PostOffice[0].District;
                document.getElementById('dir_state').value = data[0].PostOffice[0].State;
            } else {
                toastr.error('Invalid Pin Code');
            }
        });
}

/* ================================
   PINCODE FETCH (CMGR)
================================ */
function fetchPinCode_Details() {
    const pin = document.getElementById('cmgr_pincode').value;
    if (pin.length !== 6) return;

    fetch(`https://api.postalpincode.in/pincode/${pin}`)
        .then(res => res.json())
        .then(data => {
            if (data[0].Status === "Success") {
                document.getElementById('cmgr_district').value = data[0].PostOffice[0].District;
                document.getElementById('cmgr_state').value = data[0].PostOffice[0].State;
            } else {
                toastr.error('Invalid Pin Code');
            }
        });
}

/* ================================
   INIT
================================ */
document.addEventListener('DOMContentLoaded', function () {
    populateStates('dir_state');
    populateStates('cmgr_state');

    document.getElementById('dir_pincode').addEventListener('input', fetchPinCodeDetails);
    document.getElementById('cmgr_pincode').addEventListener('input', fetchPinCode_Details);
});

/* ================================
   ERROR PRINT (SERVER)
================================ */
function printErrorMsg(msg) {
    $.each(msg, function (key, value) {
        $('.' + key + '_err').text(value).show();
        setTimeout(() => {
            $('.' + key + '_err').fadeOut();
        }, 5000);
    });
}

/* ================================
   SHOW SAVED DATA
================================ */
function showServicePartner() {
    $('#set-show-service').html('');
    $.get("{{ route('show-service-partner') }}", {
        service_id: $('#service_id').val()
    }, function (res) {
        $('#set-show-service').html(res);
    });
}
</script>

<script>
document.getElementById('date').addEventListener('keydown', function (e) {
    e.preventDefault();
});
</script>


</body>

</html>