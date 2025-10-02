<?php 
$payroll_id = $_GET['payroll_id'] ?? 0;
$payrollInfo = get_data('payroll', ['id' => $payroll_id]);

if($payrollInfo) {
	$payrollInfo = $payrollInfo[0];
} else {
	$payrollInfo = [
		'month'      => '',
		'ref'        => '',
		'ref_name'   => '',
		'added_date' => '',
		'status'     => '',
	];
}

if($payrollInfo['status'] == 'Request') {
	$payrollInfo['status'] = 'Pending';
}

// Payroll months
$payrollInfo['month'] = $payrollInfo['month'] ? explode(",", $payrollInfo['month']) : [];

// Workflow
$workflow = json_decode($payrollInfo['workflow'], true) ?: [];
$myWorkflow = array_filter($workflow, fn($item) => $item['user_id'] == $_SESSION['user_id']);
$myWorkflowStatuses = array_map(fn($item) => $item['status'], $myWorkflow);

// Finished / rejected
$finished      = json_decode($payrollInfo['finished'], true) ?: [];
$rejected      = json_decode($payrollInfo['rejected'], true) ?: [];

$finishedUsers    = array_column($finished, 'user_id');
$rejectedUsers    = array_column($rejected, 'user_id');
$usersRejectedTo  = array_column($rejected, 'next_user');

// Flags
$currentUserId = $_SESSION['user_id'];
$isFinished    = in_array($currentUserId, $finishedUsers);
$isRejected    = in_array($currentUserId, $rejectedUsers);
$canReReview   = in_array($currentUserId, $usersRejectedTo); 
$canTakeAction = (!$isFinished && !$isRejected) || $canReReview;

// Column settings
$columns = get_columns('showpayrollDT', 'show_columns');
?>
<div class="page content header">
    <div class="page-breadcrumb d-sm-flex align-items-center">
        <h5 class="spy-10">Payroll details</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <a href="<?=baseUri();?>/payroll" class="btn btn-secondary"> Back</a>
            </div>
            <div class="ms-auto d-none d-md-block">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary">Actions</button>
                    <button type="button" class="btn btn-outline-secondary split-bg-secondary actions dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu actions dropdown-menu-right dropdown-menu-lg-end">
						<a class="dropdown-item cursor  edit-table_customize" data-table="showpayrollDT"> Edit table columns</a>
						<a class="dropdown-item cursor  edit-table_customize" data-table="payroll_pdf"> Edit pdf columns</a>
                        <?php if (!empty($payrollInfo['month'])): ?>
                            <a class="dropdown-item cursor" href="<?=baseUri();?>/pdf.php?print=payroll&id=<?=$payroll_id;?>&month=<?=$payrollInfo['month'][0];?>" target="_blank"> Download PDF</a>
                        <?php endif; ?>
                        <a id="download_payroll" class="dropdown-item cursor"> Download Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page content">
    <div class="card">
        <div class="card-body">
            <div class="row d-md-none d-none d-sm-none d-lg-flex">
                <p class="bold">Payroll details</p>

                <!-- Month -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Month</label>
                        <select id="payrollMonth" readonly class="form-control cursor">
                            <?php foreach ($payrollInfo['month'] as $month): ?>
                                <option value="<?=date('Y-m', strtotime($month))?>"> <?=date('F Y', strtotime($month))?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-error text-danger">This is error</span>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Status</label>
                        <input type="text" readonly class="form-control cursor" value="<?=$payrollInfo['status'];?>">
                        <span class="form-error text-danger">This is error</span>
                    </div>
                </div>

                <!-- Date -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Date added</label>
                        <input type="text" readonly class="form-control cursor" value="<?=date('F d, Y', strtotime($payrollInfo['added_date']));?>">
                        <span class="form-error text-danger">This is error</span>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Bulk actions</label>
                        <select name="bulk_actions" class="form-control cursor" id="bulk_actions">
                            <option value="">Select action</option>
                            <?php if($_SESSION['delete_payroll'] == 'on'): ?>
                                <option value="delete">Delete</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Workflow -->
            <div class="row">
                <p class="bold">Payroll workflow</p>

                <?php if($canTakeAction): ?>
                    <div class="col-md-3 col-lg-2 col-sm-12">
                        <div class="form-group">
                            <label class="label required">Next Action</label>
                            <input type="hidden" class="payroll_id" name="payroll_id" value="<?=$payrollInfo['id'];?>">
                            <select name="next_action" class="form-control cursor" id="next_action">
							<option value="">Select action</option>
							<?php if($_SESSION['review_payroll'] == 'on' && ($canReReview || !in_array('Reviewed', $myWorkflowStatuses))): ?>
								<option value="Reviewed">Review</option>
							<?php endif; ?>

							<?php if($_SESSION['approve_payroll'] == 'on' && ($canReReview || !in_array('Approved', $myWorkflowStatuses))): ?>
								<option value="Approved">Approve</option>
							<?php endif; ?>

							<?php if($_SESSION['reject_payroll'] == 'on' && ($canReReview || !in_array('Rejected', $myWorkflowStatuses))): ?>
								<option value="Rejected">Reject</option>
							<?php endif; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-md-5 col-lg-5 col-sm-12">
                    <div class="sflex">
                        <?php if(count($myWorkflow) > 0 && $canTakeAction): ?>
                            <button style="font-size: 14px;" class="btn smt-23 spy-10 btn-lg btn-primary cursor" data-bs-toggle="modal" data-bs-target="#notifyModal">
                                Finish and Notify Next Person
                            </button>
                        <?php endif; ?>
                        <button style="font-size: 14px;" class="btn sml-10 smt-23 spy-10 btn-lg btn-success cursor" type="button" data-bs-toggle="modal" data-bs-target="#payrollHistoryModal">
                            View Workflow
                        </button>
                    </div>
                </div>
            </div>

            <hr>
            <div class="table-responsive">
                <table id="showpayrollDT" class="table table-striped table-bordered" style="width:100%"></table> 
            </div>
        </div>
    </div>
</div>


<!-- Payroll History Modal -->
<div class="modal fade" id="payrollHistoryModal" tabindex="-1" aria-labelledby="payrollHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:500px;">
        <div class="modal-content" id="generatePayrollForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title" id="payrollHistoryModalLabel">
                    <i class="bi bi-clock-history"></i> Payroll Workflow History
                </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <?php
                // Decode the JSON data
                $workflow = json_decode($payrollInfo['workflow'], true); 

                echo '<ul class="list-group list-group-flush">';

                // Loop through the workflow history in reverse order (most recent first)
                $reversed_workflow = array_reverse($workflow);

                foreach ($reversed_workflow as $step) {
                    $status = $step['status'];
                    $icon_class = 'bi-info-circle-fill'; // Default
                    $text_color = 'text-secondary'; // Default
                    
                    switch ($status) {
                        case 'Created':
                            $icon_class = 'bi-file-earmark-plus-fill';
                            $text_color = 'text-secondary';
                            break;
                        case 'Reviewed':
                            $icon_class = 'bi-eye-fill';
                            $text_color = 'text-info';
                            break;
                        case 'Rejected':
                            $icon_class = 'bi-x-octagon-fill';
                            $text_color = 'text-danger';
                            break;
                        case 'Approved':
                            $icon_class = 'bi-check-circle-fill';
                            $text_color = 'text-success';
                            break;
                    }
                    
                    echo '<li class="list-group-item d-flex justify-content-between align-items-start">';
                    echo '  <div class="d-flex align-items-center me-2">';
                    echo '    <i class="bi ' . $icon_class . ' ' . $text_color . ' fs-5 me-3"></i>';
                    echo '    <div>';
                    echo '      <strong class="' . $text_color . '">' . htmlspecialchars($status) . '</strong>';
                    
                    $action_parts = explode(' by ', $step['action']);
                    $user_name = isset($action_parts[1]) ? htmlspecialchars($action_parts[1]) : '';
                    
                    if ($user_name) {
                        echo '      <div class="text-muted">By: ' . $user_name . '</div>';
                    }
                    echo '    </div>';
                    echo '  </div>';
                    echo '  <span class="text-dark align-self-center">' . date("M d, Y h:i A", strtotime($step['date'])) . '</span>';
                    echo '</li>';
                }

                echo '</ul>';
                ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Notify Next Person Modal -->
<div class="modal fade" id="notifyModal" tabindex="-1" aria-labelledby="notifyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:500px;">
        <form class="modal-content" id="notifyNextPersonForm"  style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
			<div class="modal-header">
				<h5 class="modal-title" id="notifyModalLabel">
                    <i class="bi bi-person-exclamation"></i> Notify Next Person
                </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            

            <div class="modal-body">
                <div class="mb-3 smt-15">
                    <label for="nextUserSelect" class="form-label">Select Person to Notify:</label>
					<input type="hidden" name="payroll_id" class="payroll_id" value="<?=$payroll_id;?>">
					<input type="hidden" name="current_status" class="current_status" value="<?=$payrollInfo['status'];?>">
                    <select class="form-select validate" data-msg="Select a user to notify" name="next_user" id="nextUserSelect" required>
                        <option value="">-- Select User --</option>
                        <?php
                        $users = get_data('users', ['status' => 'active']);
                        foreach ($users as $user) {
							if($user['user_id'] == $_SESSION['user_id']) continue;
							$role = $user['role'];
							$roleName = $GLOBALS['userClass']->get_roleName($user['user_id']);
							$selected = '';
							if(is_array($_SESSION['reports_to'])) {
								$selected = in_array($user['user_id'], $_SESSION['reports_to']) ? 'selected' : '';
							}
							// if($user['user_id'] == $payrollInfo['added_by']) continue;
                            echo '<option '.$selected.' value="' . $user['user_id'] . '">' . $user['full_name'] . ' ('.$roleName.') </option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="notificationMessage" class="form-label">Message (Optional):</label>
                    <textarea class="form-control" id="notificationMessage" rows="3" placeholder="Add a short message about the payroll..."></textarea>
                </div>
            </div> 
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="sendNotificationBtn">
                    <i class="bi bi-send-fill"></i> Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<?php require('payslip_show.php'); ?>
<?php require('pay_payroll.php'); ?>

<?php 
$columns = get_columns('showpayrollDT', 'show_columns');
require('./customize_table.php');
?>

<script type="text/javascript">
var payroll_id = '<?=$payroll_id;?>';
var tableColumns = <?=json_encode($columns);?>;

document.addEventListener("DOMContentLoaded", () => {
    var month = $('#payrollMonth').val();
    if(month) {
        load_showPayroll(payroll_id, month);
        $('#download_payroll').attr('href', `${base_url}/download_payroll.php?id=${payroll_id}&month=${month}`)
    }
    $('#payrollMonth').on('change', () => {
        var month = $('#payrollMonth').val();
        if(month) {
            load_showPayroll(payroll_id, month);
            $('#download_payroll').attr('href', `${base_url}/download_payroll.php?id=${payroll_id}&month=${month}`)
        }
    })
});
</script>

<style>
    .dropdown.bootstrap-select.my-select { width: 100% !important; }
</style>
