<?php 
$employee_id = $_GET['employee_id'];
$employee = $GLOBALS['employeeClass']->read($employee_id);
// var_dump($employee);

if(!$employee['avatar']) {
	if(strtolower($employee['gender']) == 'female')  {
		$employee['avatar'] = 'female_avatar.png';
	} else {
		$employee['avatar'] = 'male_avatar.png';
	}
}

$border_color = '#6c757d';
$user = $GLOBALS['employeeClass']->get_user($employee_id);
if($user) {
	if(ucwords($user[0]['is_logged']) == 'Yes') $border_color = '#80ff83';
}


?>

<div class="page content">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="empInfo-tab" data-bs-toggle="tab" data-bs-target="#empInfo" type="button" role="tab" aria-controls="empInfo" aria-selected="true">Employee Info</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#employeeDocuments" type="button" role="tab" aria-controls="employeeDocuments" aria-selected="false">Documents</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#educationTab" type="button" role="tab" aria-controls="educationTab" aria-selected="false">Education</button>
				</li>
			</ul>
		</div>
	</div>
	<hr>
	<div class="tab-content" id="employeePage">
		<div class="tab-pane fade show active" id="empInfo" role="tabpanel" aria-labelledby="home-tab">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
				<h5 class="">Employee info </h5>
				<div class="ms-auto d-sm-flex">
					<div class="btn-group smr-10">
						<a href="<?=baseUri();?>/employees"  class="btn btn-secondary">Go Back</a>
					</div>            
				</div>
			</div>

			<hr>
			<div class="row">
				<div class="col-lg-4 col-md-12">
					<div class="card">
						<div class="card-header bold">
							Employee
						</div>
						<div class="card-body">
							<div class="sflex swrap emp-profile sjcenter">
								<img style="border-color: <?=$border_color;?>;" class="profile-img sflex-basis-100" src="<?=baseUri();?>/assets/images/avatars/<?=$employee['avatar'];?>">
								<label class="profile-img-edit">
									<input type="hidden" id="employee_id" value="<?=$employee_id;?>" name="">
									<input type="file" id="profile-img" class="hidden" name="">
									<i class="fa fa-pencil"></i>
								</label>
							</div>
							<div class="sflex smt-10 swrap sjcenter">
								<h5 class="sflex-basis-100 sflex swrap sjcenter bold"><?=$employee['full_name'];?> </h5>
								<span class="sflex-basis-100 sflex swrap sjcenter "><?=$employee['designation'];?> </span>
							</div>

							<div class="sflex smt-10 swrap">
								<span class="sflex-basis-100 sflex swrap  ">Status</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['status'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  "><?=$GLOBALS['branch_keyword']['sing'];?></span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['branch'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Position</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['position'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">State</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['state'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Office/Duty station</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['location_name'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Projects</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['project'];?> </p>
								
							</div>
							
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-12">
					<div class="card">
						<div class="card-header bold">
							Basic Information
						</div>
						<div class="card-body">
							
							<div class="sflex smt-10 swrap">
								<span class="sflex-basis-100 sflex swrap  ">Full name</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['full_name'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Gender</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['gender'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Email</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['email'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Phone Number</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['phone_number'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Staff Number</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['staff_no'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Identification Number</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['national_id'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Date of birth</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=formatDate($employee['date_of_birth']);?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">City/Adress</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['city'];?>  <?=$employee['address'];?></p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Payment info</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['payment_bank'];?>,  <?=$employee['payment_account'];?></p>
								
							</div>
							
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-12">
					<div class="card">
						<div class="card-header bold">
							Contract Information
						</div>
						<div class="card-body">
							
							<div class="sflex smt-10 swrap">
								<span class="sflex-basis-100 sflex swrap  ">Date first contract</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=formatDate($employee['hire_date']);?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Current Contract</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=formatDate($employee['contract_start']);?> - <?=formatDate($employee['contract_end']);?></p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Contractr Type </span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['contract_type'];?>  </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Budget Code</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['budget_code'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Base Salary</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=formatMoney($employee['salary']);?> </p>
								
							</div>


							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">MoH contract</span>
								<p class="sflex-basis-100 sflex swrap  bold"> <?=$employee['moh_contract'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Working</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['work_days'];?> days per week /  <?=$employee['work_hours'];?> hours per day</p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Grade</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['grade'];?> </p>
								
							</div>
							
							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Seniority/Tax exempt</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$employee['seniority'];?> /<?=$employee['tax_exempt'];?> </p>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="tab-pane fade " id="employeeDocuments" role="tabpanel" aria-labelledby="home-tab">
			<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
				<h5 class="">Employee Documents</h5>
				<div class="ms-auto d-sm-flex">
					<div class="btn-group smr-10">
						<button type="button" data-bs-toggle="modal" data-bs-target="#add_document" class="btn btn-primary">Add Document</button>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<input type="hidden" id="employee_id" value="<?=$employee_id; ?>">
						<table id="employeeDocsDT" class="table table-striped table-bordered" style="width:100%">
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="tab-pane fade " id="educationTab" role="tabpanel" aria-labelledby="home-tab">
			<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
				<h5 class="">Education</h5>
			</div>
			<?php 
			$education = $GLOBALS['employeeClass']->get_education($employee_id);
			if($education) {
			?>
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th>Degree</th>
									<th>Institution</th>
									<th>Started</th>
									<th>Graduated</th>
								</tr>
							</thead>

							<tbody>
								<?php 
								foreach ($education as $row) { ?>
									<tr>
										<td><?=$row['degree'];?></td>
										<td><?=$row['institution'];?></td>
										<td><?=$row['start_year'];?></td>
										<td><?=$row['graduation_year'];?></td>
									</tr>
								<?php  }

								?>
								
							</tbody>
						</table> 
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>






<?php 
require('emp_doc.php');
?>



<style type="text/css">
	
</style>