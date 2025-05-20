<div class="row mt-4" >
	<div class="col-md-12 col-lg-6">
		<div class="card border-primary mb-3" >
			<div class="card-header bold">Employee settings</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" style="width:100%">
					<?php
					$employeesSettings = getSettingsBySection('employees');
					foreach ($employeesSettings as $setting) { ?>
					    <tr>
					        <td><?=$setting['details'];?></td>
					        <td class="d-flex align-items-center justify-content-between">
					            <span><?=$setting['value'];?></span>
					            <i onclick="return change_settings(`<?=$setting['type'];?>`)" class="fa cursor fa-pencil-alt"></i>
					        </td>
					    </tr>
					<?php } 
					?>
					
					
					
				</table>
			</div>
		</div>
		<div class="card border-primary mb-3" >
			<div class="card-header bold">System settings</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" style="width:100%">
					<?php
					$employeesSettings = getSettingsBySection('system');
					foreach ($employeesSettings as $setting) { ?>
					    <tr>
					        <td><?=$setting['details'];?></td>
					        <td class="d-flex align-items-center justify-content-between">
					            <span><?=$setting['value'];?></span>
					            <i onclick="return change_settings(`<?=$setting['type'];?>`)" class="fa cursor fa-pencil-alt"></i>
					        </td>
					    </tr>
					<?php } 
					?>
					
					
					
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-12 col-lg-6">
		<div class="card border-primary mb-3" >
			<div class="card-header bold">Payroll settings</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" style="width:100%">
					<?php
					$payrollSettings = getSettingsBySection('payroll');
					foreach ($payrollSettings as $setting) { ?>
					    <tr>
					        <td><?=$setting['details'];?></td>
					        <td class="d-flex align-items-center justify-content-between">
					            <span><?=$setting['value'];?></span>
					            <i onclick="return change_settings(`<?=$setting['type'];?>`)" class="fa cursor fa-pencil-alt"></i>
					        </td>
					    </tr>
					<?php }  
					?>
					
					
					
				</table>
			</div>
		</div>
	</div>
</div>

<?php require('settings_edit.php'); ?>