<?php $this->load->view('includes/header'); 
$profile_pic = 'male_user.png';
if(!empty($empData->emp_profile)){$profile_pic = $empData->emp_profile;}
else
{
	if(!empty($empData->emp_gender) and $empData->emp_gender=="Female"):
		$profile_pic = 'female_user.png';
	else:
		$profile_pic = 'male_user.png';
	endif;
}
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Employee Profile</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <!-- Column -->
                                <div class="col-lg-4 col-xlg-3 col-md-5">
                                    <div class="card">
                                        <div class="card-body">
                                            <center class="m-t-30"> <img src="<?= base_url() ?>assets/images/users/<?=$profile_pic?>" class="rounded-circle" width="150" />
                                                <h4 class="card-title m-t-10"><?= (!empty($empData->emp_name)) ? $empData->emp_name : "-"; ?></h4>
                                                <h6 class="card-subtitle"><?= (!empty($empData->title)) ? $empData->title : "-"; ?> (<?= (!empty($empData->name)) ? $empData->name : ""; ?>)</h6>
                                            </center>
                                        </div>
                                        <div>
                                            <hr>
                                        </div>
                                        <div class="card-body">
                                            <strong>Father Name </strong>
                                            <p class="text-muted"><?= (!empty($empData->father_name)) ? $empData->father_name : "-"; ?></p>
                                            <strong>Husband Name </strong>
                                            <p class="text-muted"><?= (!empty($empData->husband_name)) ? $empData->husband_name : "-"; ?></p>
                                            <strong>Phone</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_contact)) ? $empData->emp_contact : "-"; ?></p>
                                            <strong>Marital Status</strong>
                                            <p class="text-muted"><?= (!empty($empData->marital_status)) ? $empData->marital_status : "-" ?></p>
                                            <strong>Gender</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_gender)) ? $empData->emp_gender : "-" ?></p>
                                            <strong>Date Of Birth</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_birthdate)) ? date("d-m-Y", strtotime($empData->emp_birthdate)) : "-"; ?></p>
                                            <strong>Joining Date</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_joining_date)) ? date("d-m-Y", strtotime($empData->emp_joining_date)) : "-"; ?></p>
                                            <strong>Address</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_address)) ? $empData->emp_address : "-"; ?></p>
                                            <strong>Pemenant Address</strong>
                                            <p class="text-muted"><?= (!empty($empData->pemenant_address)) ? $empData->pemenant_address : "-"; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Column -->
                                <!-- Column -->
                                <div class="col-lg-8 col-xlg-9 col-md-7">
                                    <div class="card">
                                        <!-- Tabs -->
                                        <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-salary-tab" data-toggle="pill" href="#salary" role="tab" aria-controls="pills-salary" aria-selected="true">Salary</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-documents-tab" data-toggle="pill" href="#documents" role="tab" aria-controls="pills-documents" aria-selected="false">Documents</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-nomination-tab" data-toggle="pill" href="#nomination" role="tab" aria-controls="pills-nomination" aria-selected="false">Nomination</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-education-tab" data-toggle="pill" href="#education" role="tab" aria-controls="pills-education" aria-selected="false">Education</a>
                                            </li>
                                        </ul>
                                        <!-- Tabs -->
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="salary" role="tabpanel" aria-labelledby="pills-salary-tab">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-6 b-r">
                                                            <strong>Bank Name</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->bank_name)) ? $empSalary->bank_name : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6 b-r">
                                                            <strong>Account No</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->account_no)) ? $empSalary->account_no : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6 b-r">
                                                            <strong>Ifsc Code</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->ifsc_code)) ? $empSalary->ifsc_code : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Salary Basis</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->salary_basis) and $empSalary->salary_basis == "M") ? "Monthly" : "Hourly" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Pf No</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->pf_no)) ? $empSalary->pf_no : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Basic Salary</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->basic_salary)) ? $empSalary->basic_salary : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>HRA</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->hra)) ? $empSalary->hra : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Travelling Allowance</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->ta)) ? $empSalary->ta : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Dearness Allowance</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->da)) ? $empSalary->da : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Other Allowance</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->oa)) ? $empSalary->oa : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Pf Amount</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->pf_amount)) ? $empSalary->pf_amount : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Professional Tax</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->prof_tax)) ? $empSalary->prof_tax : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6">
                                                            <strong>Other Deduction</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empSalary->other_deduction)) ? $empSalary->other_deduction : "-" ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="pills-documents-tab">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-6 b-r">
                                                            <strong>Old Uan No</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->old_uan_no)) ? $empDocs->old_uan_no : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-6 col-xs-6 b-r">
                                                            <strong>Old Pf No</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->old_pf_no)) ? $empDocs->old_pf_no : "-" ?></p>
                                                        </div>
                                                        <hr style="width:100%;">
                                                        <div class="col-md-12 col-xs-6 b-r">
                                                            <h5>Aadhar Details : </h5>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6 b-r">
                                                            <strong>Aadhar Name</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->aadhar_name)) ? $empDocs->aadhar_name : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6">
                                                            <strong>Aadhar No</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->aadhar_no)) ? $empDocs->aadhar_no : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6">
                                                            <strong>Date of Birth <small>(aadhar)</small></strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->aadhar_dob)) ? date("d-m-Y", strtotime($empDocs->aadhar_dob)) : "-" ?></p>
                                                        </div>
                                                        <hr style="width:100%;">
                                                        <div class="col-md-12 col-xs-6 b-r">
                                                            <h5>Pan Details : </h5>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6 b-r">
                                                            <strong>Pan Name</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->pan_name)) ? $empDocs->pan_name : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6">
                                                            <strong>Pan No</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->pan_no)) ? $empDocs->pan_no : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6">
                                                            <strong>Date of Birth <small>(pan)</small></strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->pan_dob)) ? date("d-m-Y", strtotime($empDocs->pan_dob)) : "-" ?></p>
                                                        </div>
                                                        <hr style="width:100%;">
                                                        <div class="col-md-12 col-xs-6 b-r">
                                                            <h5>KYC Details : </h5>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6 b-r">
                                                            <strong>Kyc Name</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->kyc_name)) ? $empDocs->kyc_name : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6">
                                                            <strong>Kyc No</strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->kyc_no)) ? $empDocs->kyc_no : "-" ?></p>
                                                        </div>
                                                        <div class="col-md-4 col-xs-6">
                                                            <strong>Date of Birth <small>(kyc)</small></strong>
                                                            <br>
                                                            <p class="text-muted"><?= (!empty($empDocs->kyc_dob)) ? date("d-m-Y", strtotime($empDocs->kyc_dob)) : "-" ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="nomination" role="tabpanel" aria-labelledby="pills-nomination-tab">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="empNomtbl" class="table table-bordered align-items-center">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th>Name</th>
                                                                    <th>Gender</th>
                                                                    <th>Relation</th>
                                                                    <th>Date of birth</th>
                                                                    <th>Proportion</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="empNomBody">
                                                                <?php
                                                                if (!empty($empNom)) :
                                                                    $i = 1;
                                                                    foreach ($empNom as $row) :
                                                                        echo '<tr>
                                                                                <td>' . $i++ . '</td>
                                                                                <td>
                                                                                    ' . $row->nom_name . '
                                                                                 </td>
                                                                                <td>
                                                                                    ' . $row->nom_gender . '
                                                                                </td>
                                                                                <td>
                                                                                    ' . $row->nom_relation . '
                                                                                </td>
                                                                                <td>
                                                                                    ' . $row->nom_dob . '
                                                                                </td>
                                                                                <td>
                                                                                    ' . $row->nom_proportion . '
                                                                                </td>
                                                                            </tr>';
                                                                    endforeach;
                                                                endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="pills-education-tab">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="empEdutbl" class="table table-bordered align-items-center">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th>Course</th>
                                                                    <th>University/Board</th>
                                                                    <th>Passing Year</th>
                                                                    <th>Per./Grade</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="empEduBody">
                                                                <?php
                                                                if (!empty($empEdu)) :
                                                                    $i = 1;
                                                                    foreach ($empEdu as $row) :
                                                                        echo '<tr>
                                                                            <td>' . $i++ . '</td>
                                                                            <td>
                                                                                ' . $row->course . '
                                                                             </td>
                                                                            <td>
                                                                                ' . $row->university . '
                                                                            </td>
                                                                            <td>
                                                                                ' . $row->passing_year . '
                                                                            </td>
                                                                            <td>
                                                                                ' . $row->grade . '
                                                                            </td>
                                                                        </tr>';
                                                                    endforeach;
                                                                endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- Column -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>