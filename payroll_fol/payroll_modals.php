<!-- Payroll History Modal -->
<div class="modal fade" id="payrollHistoryModal" tabindex="-1" aria-labelledby="payrollHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:500px;">
        <div class="modal-content" id="generatePayrollForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px; width: 700px;">
            <div class="modal-header">
                <h5 class="modal-title" id="payrollHistoryModalLabel">
                    <i class="bi bi-clock-history"></i> Payroll Workflow History
                </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert smt-10 alert-info">
                    <i class="fa fa-info-circle"></i> Last User notified: <strong><?= $lastUserNotifiedName ?></strong> (<?= $lastUserNotifiedRole ?>)
                </div>
                <?php
                // Show workflow history (most recent first)
                $reversed_workflow = array_reverse($workflow);
                if (empty($reversed_workflow)) {
                    echo '<div class="text-muted">No workflow history available.</div>';
                } else {
                    echo '<ul class="list-group list-group-flush">';
                    foreach ($reversed_workflow as $step) {
                        $status = $step['status'] ?? 'Unknown';
                        $icon_class = 'bi-info-circle-fill';
                        $text_color = 'text-secondary';
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

                        $action_text = $step['action'] ?? '';
                        // If action contains " by X", try to get the user name
                        $action_parts = explode(' by ', $action_text, 2);
                        $roleName = $GLOBALS['userClass']->get_roleName($step['user_id']);
                        $user_name = isset($action_parts[1]) ? htmlspecialchars($action_parts[1]) : '';
                        if ($user_name) {
                            echo '      <div class="text-muted">By: ' . $user_name . ' (' . $roleName . ')</div>';
                        }
                        echo '    </div>';
                        echo '  </div>';
                        echo '  <span class="text-dark align-self-center">' . (isset($step['date']) ? date("M d, Y h:i A", strtotime($step['date'])) : '') . '</span>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }
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
                    <input type="hidden" name="payroll_id" class="payroll_id" value="<?= htmlspecialchars($payroll_id); ?>">
                    <!-- IMPORTANT: we will set this hidden input's value when opening the modal -->
                    <input type="hidden" name="status" class="selected_status" value="">
                    <select class="form-select validate" data-msg="Select a user to notify" name="next_user" id="nextUserSelect" required>
                        <option value="">-- Select User --</option>
                        <?php
                        $users = get_data('users', ['status' => 'active']);
                        foreach ($users as $user) {
                            if ((string)$user['user_id'] === $currentUserId) continue; // do not include self
                            $roleName = $GLOBALS['userClass']->get_roleName($user['user_id']);
                            $selected = '';
                            if (is_array($_SESSION['reports_to'])) {
                                $selected = in_array($user['user_id'], $_SESSION['reports_to']) ? 'selected' : '';
                            }
                            echo '<option ' . $selected . ' value="' . htmlspecialchars($user['user_id']) . '">' . htmlspecialchars($user['full_name']) . ' (' . htmlspecialchars($roleName) . ')</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="notificationMessage" class="form-label">Message (Optional):</label>
                    <textarea class="form-control" id="notificationMessage" name="message" rows="3" placeholder="Add a short message about the payroll..."></textarea>
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

<!-- Add Employee to Payroll Modal -->
<div class="modal fade" id="addEmployeeToPayrollModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:500px;">
        <form class="modal-content" id="addEmployeeToPayrollForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">
                    <i class="bi bi-person-plus-fill"></i> Add Employee to Payroll
                </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="payroll_id" class="payroll_id" value="<?= htmlspecialchars($payroll_id); ?>">
                <div class="form-group">
                    <input type="hidden" name="payroll_id_for_add" id="payroll_id_for_add" value="<?=$payroll_id;?>">
                    <label class="label required" for="searchEmployeePayroll">Select Employees</label>
                    <select class="my-select searchEmployeePayroll" name="employees[]" id="searchEmployeePayroll" multiple data-live-search="true" title="Search and select employees..." multiple>
                        <!-- Options will be loaded dynamically by JavaScript -->
                    </select>
                    <small class="form-text text-muted">Only employees matching the payroll's criteria (Department/Location) who are not already in this payroll will be shown.</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="addEmployeesBtn">
                    <i class="bi bi-plus-circle"></i> Add Selected Employees
                </button>
            </div>
        </form>
    </div>
</div>