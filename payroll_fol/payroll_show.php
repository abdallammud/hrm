<?php
// ----- payroll_manage.php (view) -----
$payroll_id = (int)($_GET['payroll_id'] ?? 0);
$payrollInfoRaw = get_data('payroll', ['id' => $payroll_id]);

if ($payrollInfoRaw && count($payrollInfoRaw) > 0) {
    $payrollInfo = $payrollInfoRaw[0];
} else {
    $payrollInfo = [
        'id' => 0,
        'month' => '',
        'ref' => '',
        'ref_name' => '',
        'added_date' => '',
        'status' => '',
        'workflow' => '[]',
        'finished' => '[]',
        'rejected' => '[]',
        'added_by' => null,
    ];
}

if ($payrollInfo['status'] === 'Request') {
    $payrollInfo['status'] = 'Pending';
}

// Payroll months
$payrollInfo['month'] = $payrollInfo['month'] ? explode(",", $payrollInfo['month']) : [];

// Workflow and arrays
$workflow = json_decode($payrollInfo['workflow'] ?? '[]', true) ?: [];
$myWorkflow = array_filter($workflow, fn($item) => (string)$item['user_id'] === (string)$_SESSION['user_id']);
$myWorkflowStatuses = array_map(fn($item) => $item['status'], $myWorkflow);

// Get last workflow obj and get a reason
$lastWorkflowObj = end($workflow);
$reason = $lastWorkflowObj['reason'] ?? '';

$finished = json_decode($payrollInfo['finished'] ?? '[]', true) ?: [];
$rejected = json_decode($payrollInfo['rejected'] ?? '[]', true) ?: [];

$finishedUsers = array_map('strval', array_column($finished, 'user_id'));
$rejectedUsers = array_map('strval', array_column($rejected, 'user_id'));
$usersRejectedTo = array_map('strval', array_column($rejected, 'next_user'));

// var_dump($myWorkflow);
// var_dump($workflow);
// var_dump($finishedUsers);
// var_dump($rejectedUsers);
// var_dump($usersRejectedTo);




// Flags
$currentUserId = (string)$_SESSION['user_id'];
$isFinished = in_array($currentUserId, $finishedUsers, true);
$isRejected = in_array($currentUserId, $rejectedUsers, true);
$canReReview = in_array($currentUserId, $usersRejectedTo, true);

// Permission flags (session flags)
$hasActionPermission = ($_SESSION['review_payroll'] === 'on' || $_SESSION['approve_payroll'] === 'on' || $_SESSION['reject_payroll'] === 'on');

// User can take action if they have permission AND they are not already finished/rejected (unless they are allowed to re-review)
$canTakeAction = $hasActionPermission && ((!$isFinished && !$isRejected) || $canReReview);

// Payroll creator can't reject their own payroll
$isCreator = ((string)($payrollInfo['added_by'] ?? '') === $currentUserId);



// Last user-specific status (if any)
$lastStatus = $myWorkflowStatuses ? end($myWorkflowStatuses) : '';

// Decide which options to show in select
$canShowReview = ($_SESSION['review_payroll'] === 'on') && ($canReReview || !in_array('Reviewed', $myWorkflowStatuses, true)) && ($payrollInfo['status'] !== 'Rejected' || $canReReview);
$canShowValidate = ($_SESSION['validate_payroll'] === 'on') && ($canReReview || !in_array('Validated', $myWorkflowStatuses, true)) && ($payrollInfo['status'] !== 'Rejected' || $canReReview);
$canShowApprove = ($_SESSION['approve_payroll'] === 'on') && ($canReReview || !in_array('Approved', $myWorkflowStatuses, true));
$canShowReject = ($_SESSION['reject_payroll'] === 'on') && ($canReReview || !in_array('Rejected', $myWorkflowStatuses, true)) && !$isCreator;

$columns = get_columns('showpayrollDT', 'show_columns');

// Get last user notified from finished
$lastUserNotified = end($finished);
$lastUserNotifiedName = $lastUserNotified ? $GLOBALS['userClass']->get($lastUserNotified['next_user']) : null;
$lastUserNotifiedName = $lastUserNotifiedName ? $lastUserNotifiedName['full_name'] : '';
$lastUserNotifiedRole = $lastUserNotified ? $GLOBALS['userClass']->get_roleName($lastUserNotified['next_user']) : null;
?>
<div class="page content header">
    <div class="page-breadcrumb d-sm-flex align-items-center">
        <h5 class="spy-10">Payroll details</h5>
        <div class="ms-auto d-sm-flex">
            <div class=" smr-10">
                <a href="<?= baseUri(); ?>/payroll" class="btn smr-10 btn-secondary"> Back</a>
                <?php if ($payrollInfo['status'] !== 'Approved'): ?>
                    <button type="button" class="btn btn-primary" id="openAddEmployeeModalBtn" style="font-size: 14px;" data-bs-toggle="modal" data-bs-target="#addEmployeeToPayrollModal">
                        <i class="bi bi-plus-circle"></i> Add Employee
                    </button>
                <?php endif; ?>
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
                            <a class="dropdown-item cursor" href="<?= baseUri(); ?>/pdf.php?print=payroll&id=<?= $payroll_id; ?>&month=<?= htmlspecialchars($payrollInfo['month'][0]); ?>" target="_blank"> Download PDF</a>
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
                                <option value="<?= date('Y-m', strtotime($month)) ?>"> <?= date('F Y', strtotime($month)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-error text-danger">This is error</span>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Status</label>
                        <input type="text" readonly class="form-control cursor" value="<?= htmlspecialchars($payrollInfo['status']); ?>">
                        <span class="form-error text-danger">This is error</span>
                    </div>
                </div>

                <!-- Date -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Date added</label>
                        <input type="text" readonly class="form-control cursor" value="<?= $payrollInfo['added_date'] ? date('F d, Y', strtotime($payrollInfo['added_date'])) : ''; ?>">
                        <span class="form-error text-danger">This is error</span>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Bulk actions</label>
                        <select name="bulk_actions" class="form-control cursor" id="bulk_actions">
                            <option value="">Select action</option>
                            <?php if ($_SESSION['delete_payroll'] == 'on'): ?>
                                <option value="delete">Delete</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                
                
            </div>

            <!-- Filters -->
            <div class="row d-md-none d-none d-sm-none d-lg-flex">
                <p class="bold">Payroll filters</p>

                <!-- Department -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Department</label>
                        <select id="slcDepartment" readonly class="form-control cursor">
                            <option value="">All</option>
                            <!-- Select all branches id as value and names as text -->
                            <?php
                                $sql = "SELECT * FROM `branches` WHERE `status` = 'Active'";
                                $result = $GLOBALS['conn']->query($sql);
                                if($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Duty Location -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">Duty Location</label>
                        <select id="slcDutyLocation" readonly class="form-control cursor">
                            <option value="">All</option>
                            <!-- Select all branches id as value and names as text -->
                            <?php
                                $sql = "SELECT * FROM `locations` WHERE `status` = 'Active'";
                                $result = $GLOBALS['conn']->query($sql);
                                if($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- State -->
                <div class="col-ms-12 col-md-6 col-lg-2">
                    <div class="form-group">
                        <label class="label required">State</label>
                        <select id="slcState" readonly class="form-control cursor">
                            <option value="">All</option>
                            <!-- Select all branches id as value and names as text -->
                            <?php
                                $sql = "SELECT * FROM `states` WHERE `status` = 'Active'";
                                $result = $GLOBALS['conn']->query($sql);
                                if($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Budget Code -->
                <div class="col-ms-12 col-md-6 col-lg-3">
                    <div class="form-group">
                        <label class="label required">Budget Code</label>
                        <select id="slcBudgetCode" readonly class="form-control cursor">
                            <option value="">All</option>
                            <!-- Select all branches id as value and names as text -->
                            <?php
                                $sql = "SELECT * FROM `budget_codes` WHERE `status` = 'Active'";
                                $result = $GLOBALS['conn']->query($sql);
                                if($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $grant_code_id  = $row['grant_code_id'];
                                        $grant_code = get_data('grant_codes', ['id' => $grant_code_id]);
                                        $grant_code = $grant_code[0]['name'];
                                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . " - " . $grant_code . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
            </div>

            <!-- Workflow -->
            <div class="row">
                <p class="bold">Payroll workflow</p>

                <?php if ($canTakeAction && $payrollInfo['status'] !== 'Approved'): ?>
                    <div class="col-md-3 col-lg-2 col-sm-12">
                        <div class="form-group">
                            <label class="label required">Next Action</label>
                            <input type="hidden" class="payroll_id" name="payroll_id" value="<?= $payrollInfo['id']; ?>">
                            <select name="next_action" class="form-control cursor" id="next_action">
                                <option value="">Select action</option>
                                <?php if ($payrollInfo['status'] !== 'Approved'): ?>
                                    <?php if ($canShowReview): ?>
                                        <option value="Reviewed">Review</option>
                                    <?php endif; ?>
                                    <?php if ($canShowValidate): ?>
                                        <option value="Validated">Validate</option>
                                    <?php endif; ?>
                                    <?php if ($canShowApprove): ?>
                                        <option value="Approved">Approve</option>
                                    <?php endif; ?>
                                    <?php if ($canShowReject): ?>
                                        <option value="Rejected">Reject</option>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!-- <option value="Disapproved">Disapprove</option> -->
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-md-5 col-lg-5 col-sm-12">
                    <div class="sflex">
                        <?php if (count($myWorkflow) > 0 && $canTakeAction && $payrollInfo['status'] !== 'Approved'): ?>
                            <!-- We will intercept this button opening the modal and ensure an action is selected -->
                            <button style="font-size: 14px;" id="openNotifyModal" class="btn smt-23 spy-10 btn-lg btn-primary cursor">
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

<?php require('payroll_modals.php'); ?>

<?php require('payslip_show.php'); ?>
<?php require('pay_payroll.php'); ?>

<?php
$columns = get_columns('showpayrollDT', 'show_columns');
require('./customize_table.php');
?>

<script type="text/javascript">
var payroll_id = '<?= $payroll_id; ?>';
var tableColumns = <?= json_encode($columns); ?>;

var current_status = '<?= $payrollInfo['status']; ?>';

document.addEventListener("DOMContentLoaded", () => {
    var month = $('#payrollMonth').val();
    if (month) {
        load_showPayroll(payroll_id, month);
        $('#download_payroll').attr('href', `${base_url}/download_payroll.php?id=${payroll_id}&month=${month}`)
    }
    $('#payrollMonth').on('change', () => {
        var month = $('#payrollMonth').val();
        if (month) {
            load_showPayroll(payroll_id, month);
            $('#download_payroll').attr('href', `${base_url}/download_payroll.php?id=${payroll_id}&month=${month}`)
        }
    });

    // Intercept "Finish and Notify Next Person" to ensure an action is chosen
    $('#openNotifyModal').on('click', function (e) {
        var selectedAction = current_status;//$('#next_action').val();
        if (!selectedAction) {
            // show user-friendly message (you may replace with swal or toast in your app)
            alert('Please select an action (Review / Approve / Reject) before notifying the next person.');
            return;
        }

        // Set the hidden status input in the notify modal
        $('#notifyModal').find('input[name="status"]').val(selectedAction);
        // Open modal programmatically
        var notifyModal = new bootstrap.Modal(document.getElementById('notifyModal'));
        notifyModal.show();
    });

});
</script>

<style>
    .dropdown.bootstrap-select.my-select { width: 100% !important; }
    .swal-text {
        text-align: center;
    }
</style>
