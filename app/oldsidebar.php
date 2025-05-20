<?php 
$menu 	= $_GET['menu'];
$action = $_GET['action'] ?? null;
$tab 	= $_GET['tab'] ?? null;

?>
<aside class="sidebar-wrapper" data-simplebar="true">
	<div class="sidebar-header">
		<div class="logo-icon">
			<img src="<?=baseUri();?>/assets/images/logo-icon.png" class="logo-img" alt="">
		</div>
		<div class="logo-name flex-grow-1">
			<h5 class="mb-0">Asheeri</h5>
		</div>
		<div class="sidebar-close">
			<span class="material-icons-outlined">close</span>
		</div>
	</div>
	<div class="sidebar-nav">
		<!--navigation-->
		<ul class="metismenu" id="sidenav">
			<?php if(check_session('view_dashboard')) { ?>
				<li>
					<a href="<?=baseUri();?>/dashboard">
						<div class="parent-icon">
							<i class="material-icons-outlined">home</i>
						</div>
						<div class="menu-title">Dashboard</div>
					</a>
				</li>
			<?php } if(check_session('manage_company_info') || check_session('manage_departments') || check_session('manage_duty_locations') || check_session('manage_states') || check_session('manage_company_banks')) { ?>
				
				<li class="<?php if($menu == 'org') echo 'mm-active' ;?>">
					<a href="javascript:;" class="has-arrow">
						<div class="parent-icon">
							<i class="material-icons-outlined">sort</i>
						</div>
						<div class="menu-title">Organization</div>
					</a>
					<ul class="<?php if($menu == 'org') echo 'mm-show' ;?>">
						<?php if(check_session('manage_company_info')) { ?>
							<li>
								<a href="<?=baseUri();?>/org">
									<i class="material-icons-outlined">arrow_right</i>
									Setup
								</a>
							</li>
						<?php } if(check_session('manage_departments')) { ?>
							<li>
								<a href="<?=baseUri();?>/<?=strtolower($GLOBALS['branch_keyword']['plu']);?>">
									<i class="material-icons-outlined">arrow_right</i>
									<?=$GLOBALS['branch_keyword']['plu'];?>
								</a>
							</li>
						<?php } if(check_session('manage_duty_locations') ) { ?>
							<li>
								<a href="<?=baseUri();?>/locations">
									<i class="material-icons-outlined">arrow_right</i>
									Duty Locations
								</a>
							</li>
						<?php } if($GLOBALS['auth']->can('manage_duty_locations')) { ?>
							<!-- <li>
								<a href="<?=baseUri();?>/currency">
									<i class="material-icons-outlined">arrow_right</i>
									Currencies & exchange
								</a>
							</li> -->
						<?php } if(check_session('manage_company_banks')) { ?>
							<li>
								<a href="<?=baseUri();?>/banks">
									<i class="material-icons-outlined">arrow_right</i>
									Bank accounts
								</a>
							</li>
						<?php } ?>
					</ul>
				</li>
			<?php } if(check_session('view_employees') || check_session('manage_designations')) {  ?>

				<li class="<?php if($menu == 'hrm') echo 'mm-active' ;?>">
					<a href="javascript:;" class="has-arrow">
						<div class="parent-icon">
							<i class="material-icons-outlined">people</i>
						</div>
						<div class="menu-title">HRM</div>
					</a>
					<ul class="<?php if($menu == 'hrm') echo 'mm-show' ;?>">
						<?php if(check_session('view_employees')) { ?>
							<li class="<?php if($tab == 'employees') echo 'mm-active' ;?>">
								<a href="<?=baseUri();?>/employees">
									<i class="material-icons-outlined">arrow_right</i>
									Employees
								</a>
							</li>
						<?php } if(check_session('manage_designations')) { ?>
							<li class="<?php if($tab == 'designations') echo 'mm-active' ;?>">
								<a href="<?=baseUri();?>/designations">
									<i class="material-icons-outlined">arrow_right</i>
									Designations
								</a>
							</li>
						<?php } ?>
						
					</ul>
				</li>
			<?php } if(check_session('view_payroll') || check_session('manage_bonus_allowances')) { ?>

				<li class="<?php if($menu == 'payroll') echo 'mm-active' ;?>">
					<a href="javascript:;" class="has-arrow">
						<div class="parent-icon">
							<i class="material-icons-outlined">calculate</i>
						</div>
						<div class="menu-title">Payroll</div>
					</a>
					<ul class="<?php if($menu == 'payroll') echo 'mm-show' ;?>">
						<?php if(check_session('view_payroll')) { ?>
							<li class="<?php if($tab == 'payroll') echo 'mm-active' ;?>">
								<a href="<?=baseUri();?>/payroll">
									<i class="material-icons-outlined">arrow_right</i>
									Payroll
								</a>
							</li>
						<?php } if(check_session('manage_bonus_allowances')) { ?>
							<li class="<?php if($tab == 'employees') echo 'mm-active' ;?>">
								<a href="<?=baseUri();?>/employees">
									<i class="material-icons-outlined">arrow_right</i>
									Bonus and Deductions
								</a>
							</li>
						<?php } ?>
						
					</ul>
				</li>
			<?php } ?>

			<li class="<?php if($menu == 'attendance') echo 'mm-active' ;?>">
				<a href="javascript:;" class="has-arrow">
					<div class="parent-icon">
						<i class="material-icons-outlined">list_alt</i>
					</div>
					<div class="menu-title">Attendance</div>
				</a>
				<ul class="<?php if($menu == 'attendance') echo 'mm-show' ;?>">
					<li class="<?php if($tab == 'employees') echo 'mm-active' ;?>">
						<a href="<?=baseUri();?>/employees">
							<i class="material-icons-outlined">arrow_right</i>
							Attendance
						</a>
					</li>
					<li class="<?php if($tab == 'timesheet') echo 'mm-active' ;?>">
						<a href="<?=baseUri();?>/employees">
							<i class="material-icons-outlined">arrow_right</i>
							Timesheet
						</a>
					</li>

					<li class="<?php if($tab == 'leave') echo 'mm-active' ;?>">
						<a href="<?=baseUri();?>/employees">
							<i class="material-icons-outlined">arrow_right</i>
							Leave Mgt
						</a>
					</li>
					
				</ul>
			</li>

			<li>
				<a href="<?=baseUri();?>/employees">
					<div class="parent-icon">
						<i class="material-icons-outlined">payments</i>
					</div>
					<div class="menu-title">Payments</div>
				</a>
			</li>

			<li class="<?php if($menu == 'users') echo 'mm-active' ;?>">
				<a href="<?=baseUri();?>/user">
					<div class="parent-icon">
						<i class="material-icons-outlined">engineering</i>
					</div>
					<div class="menu-title">System users</div>
				</a>
			</li>

			<li>
				<a href="cards.html">
					<div class="parent-icon">
						<i class="material-icons-outlined">bar_chart</i>
					</div>
					<div class="menu-title">Reports</div>
				</a>
			</li>

			<li>
				<a href="cards.html">
					<div class="parent-icon">
						<i class="material-icons-outlined">settings</i>
					</div>
					<div class="menu-title">Settings</div>
				</a>
			</li>
			
			
		</ul>
		<!--end navigation-->
	</div>
</aside>