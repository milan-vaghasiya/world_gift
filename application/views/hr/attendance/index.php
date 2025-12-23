<?php 
	$this->load->view('includes/header'); 
	$today = new DateTime();
	$today->modify('first day of this month');$first_day = date('Y-m-d');
	$today->modify('last day of this month');$last_day = date("t",strtotime($today->format('Y-m-d')));
	$monthArr = ['April'=>'04','May'=>'05','June'=>'06','July'=>'07','August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03'];
	
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8"><h4 class="card-title">Attendance</h4>
								<small class="text-success font-bold">Last Synced at <span class="lastSyncedAt"><?=$lastSyncedAt?></span></small></div>
                            <div class="col-md-4">
								<div class="input-group mb-3">
									<button href="#" class="btn btn-light-green pulse syncDeviceData"><i class="fas fa-sync"></i> Sync</button>
									<input type="date" id="report_date" name="report_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> >
									<div class="input-group-append">
										<button class="btn btn-info getDailyAttendance" type="button">Go!</button>
									</div>
								</div>
                            </div>                       
                        </div>                                         
                    </div>
                    <div class="card-body">
						<div class="row">
							<div class="col-sm-12 col-md-3">
								<div class="card bg-info">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="align-self-center display-6"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Total</h4>
												<span>Employee</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 totalEmpStat"></h2>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="card bg-success">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="display-6 align-self-center"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Total</h4>
												<span>Present</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 presentStat"></h2>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="card bg-warning">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="display-6 align-self-center"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Late</h4>
												<span>Arrived</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 lateStat"></h2>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="card bg-danger">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="display-6 align-self-center"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Total</h4>
												<span>Absent</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 absentStat"></h2>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Column -->
						</div>
                        <div class="row">
                            <div class="col-md-12">
								<div class="table-responsive">
									<table id='attendanceSummaryTable' class="table table-striped table-bordered jdt">
										<thead class="thead-info">
											<tr class="clonTR">
												<th>Code</th>
												<th>Emp Name</th>
												<th>Department</th>
												<th>Shift</th>
												<th>Designation</th>
												<th>Category</th>
												<th>Status</th>
												<th>Punch Time</th>
											</tr>
										</thead>
										<tbody class="attendance-summary"></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/attendance.js?v=<?=time()?>"></script>