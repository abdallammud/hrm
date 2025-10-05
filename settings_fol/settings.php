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
		<!-- System settings card (updated to show color swatch and logo row) -->
		<div class="card border-primary mb-3" >
			<div class="card-header bold">System settings</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" style="width:100%">
					<?php
					$systemSettings = getSettingsBySection('system');
					echo $GLOBALS['logoPath'];
					foreach ($systemSettings as $setting) {
						// Determine if this is a color setting (type contains 'color') or a logo
						$isColor = (strpos($setting['type'], 'color') !== false) || ($setting['type'] === 'system_color');
						$isLogo  = ($setting['type'] === 'system_logo' || $setting['type'] === 'logo');
						?>
						<tr>
							<td><?=$setting['details'];?></td>
							<td class="d-flex align-items-center justify-content-between">
								<?php if ($isColor) { ?>
									<div class="d-flex align-items-center">
										<div class="color-swatch mr-2" style="width:28px;height:20px;border-radius:4px;border:1px solid #ccc;background: <?=$setting['value'];?>;"></div>
										<span class="setting-value-text"><?=$setting['value'];?></span>
									</div>
								<?php } else if ($isLogo) {
									// show thumbnail if value contains file name
									$logoPath = trim($setting['value']);
									if ($logoPath) {
										// If the value is only filename, adjust it to assets/images/
										$displayPath = (strpos($logoPath, 'assets/') === 0) ? $logoPath : 'assets/images/' . $logoPath;
										echo '<div class="d-flex align-items-center"><img src="' . $displayPath . '" alt="logo" style="max-height:36px; max-width:120px; object-fit:contain; margin-right:8px; border:1px solid #eee; padding:4px; background:white;"><span class="setting-value-text">' . htmlspecialchars($setting['value']) . '</span></div>';
									} else {
										echo '<span class="text-muted">No logo uploaded</span>';
									}
								} else { ?>
									<span class="setting-value-text"><?=htmlspecialchars($setting['value']);?></span>
								<?php } ?>
								<i data-details="<?=$setting['details'];?>" data-section="<?=$setting['section'];?>" data-remarks="<?=$setting['remarks'];?>" onclick="return change_settings(`<?=$setting['type'];?>`)" class="fa cursor fa-pencil-alt"></i>
							</td>
						</tr>
					<?php } ?>
					<!-- Add a dedicated Logo row if not already present in DB -->
					<?php
					$hasLogo = false;
					foreach ($systemSettings as $s) { if ($s['type'] === 'system_logo' || $s['type'] === 'logo') $hasLogo = true; }
					if (!$hasLogo) { ?>
						<tr>
							<td>System logo</td>
							<td class="d-flex align-items-center justify-content-between">
								<span class="text-muted">No logo uploaded</span>
								<i data-details="<?=$setting['details'];?>" data-section="<?=$setting['section'];?>" data-remarks="<?=$setting['remarks'];?>" onclick="return change_settings(`system_logo`)" class="fa cursor fa-pencil-alt"></i>
							</td>
						</tr>
					<?php } ?>
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
					// var_dump($payrollSettings);
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
					
					
					

						

	<div class="col-md-12 col-lg-6">
		<div class="card border-primary mb-3">
			<div class="card-header bold">System Features</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" style="width:100%">
					<?php
					$featuresSettings = getSettingsBySection('admin');
					// var_dump($featuresSettings);
					foreach ($featuresSettings as $setting) { 
						$disabled_features = get_setting('disabled_features');
						$disabled_features = json_decode($disabled_features['value']);
						
						?>
					    <tr>
					        <td><?=$setting['details'];?></td>
					        <td class="d-flex align-items-center justify-content-between">
					            <span><?=ucwords(implode(', ', json_decode($setting['value'])))?></span>
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