<?php
require('init.php');

if(isset($_GET['action'])) {
    if(isset($_GET['endpoint'])) {
        // CREATE
        if($_GET['action'] == 'save') {
            if($_GET['endpoint'] == 'promotion') {
                $result = ['error' => false, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['employee_id']) || empty($post['new_designation']) || empty($post['promotion_date'])) {
                        $result['msg'] = 'Employee, new designation and promotion date are required';
                        echo json_encode($result); exit();
                    }
                    
                    check_auth('create_promotions');
                    
                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    if(!$employee) {
                        $result['msg'] = 'Employee not found';
                        echo json_encode($result); exit();
                    }
                    
                    // Get designation details
                    $newDesignation = $post['new_designation'];
                    // Get current designation ID
                    $oldDesignationId = $post['old_designation'];
                    
                    $data = [
                        'employee_id' => $post['employee_id'],
                        'old_designation' => $oldDesignationId,
                        'new_designation' => $post['new_designation'],
                        'promotion_date' => $post['promotion_date'],
                        'new_salary' => $post['new_salary'] ?? null,
                        'reason' => $post['reason'] ?? '',
                        'status' => $post['status'] ?? 'Pending',
                        'added_by' => $_SESSION['user_id'],
                        'added_date' => date('Y-m-d H:i:s')
                    ];
                    
                    $id = $promotionsClass->create($data);
                    if($id) {
                        // If status is approved, update employee table
                        if($post['status'] == 'Approved') {
                            $updateData = [
                                'designation' => $newDesignation,
                                'updated_by' => $_SESSION['user_id'],
                                'updated_date' => date('Y-m-d H:i:s')
                            ];
                            if(!empty($post['new_salary'])) {
                                $updateData['salary'] = $post['new_salary'];
                            }
                            
                            $GLOBALS['conn']->query("UPDATE employees SET designation = '".$newDesignation."', salary = '".$post['new_salary']."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        
                        $result['msg'] = 'Promotion record added successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to add promotion record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            }
            // TRANSFERS CRUD OPERATIONS
            else if($_GET['endpoint'] == 'transfer') {
                $result = ['error' => false, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['employee_id']) || empty($post['new_department_id']) || empty($post['transfer_date'])) {
                        $result['msg'] = 'Employee, new department and transfer date are required';
                        echo json_encode($result); exit();
                    }
                    
                    // Check authorization
                    if(!check_session('create_transfers')) {
                        $result['msg'] = 'You are not authorized to create transfers';
                        echo json_encode($result); exit();
                    }
                    
                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    if(!$employee) {
                        $result['msg'] = 'Employee not found';
                        echo json_encode($result); exit();
                    }
                    
                    // Get department details
                    $newDepartment = get_data('branches', ['id' => $post['new_department_id']])[0];
                    if(!$newDepartment) {
                        $result['msg'] = 'New department not found';
                        echo json_encode($result); exit();
                    }
                    
                    // Get current department details
                    $oldDepartment = null;
                    if(!empty($employee['branch_id'])) {
                        $oldDepartment = get_data('branches', ['id' => $employee['branch_id']])[0];
                    }
                    
                    $data = [
                        'employee_id' => $post['employee_id'],
                        'old_department_id' => $employee['branch_id'] ?? null,
                        'new_department_id' => $post['new_department_id'],
                        'transfer_date' => $post['transfer_date'],
                        'reason' => $post['reason'] ?? '',
                        'status' => $post['status'] ?? 'Pending',
                        'added_by' => $_SESSION['user_id'],
                        'added_date' => date('Y-m-d H:i:s')
                    ];
                    
                    $transfersClass = $GLOBALS['transfersClass'];
                    $saved = $transfersClass->create($data);
                    
                    if($saved) {
                        // If status is approved, update employee table
                        if($post['status'] == 'Approved') {
                            $GLOBALS['conn']->query("UPDATE employees SET branch_id = '".$post['new_department_id']."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        
                        $result['msg'] = 'Transfer record added successfully';
                    } else {
                        $result['error'] = true;
                        $result['msg'] = 'Failed to add transfer record';
                    }
                } catch(Exception $e) {
                    $result['error'] = true;
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // RESIGNATIONS CRUD OPERATIONS - CREATE
            else if($_GET['endpoint'] == 'resignation') {
                $result = ['error' => false, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['employee_id']) || empty($post['resignation_date']) || empty($post['last_working_day'])) {
                        $result['msg'] = 'Employee, resignation date and last working day are required';
                        echo json_encode($result); exit();
                    }
                    
                    // Check authorization
                    if(!check_session('create_resignations')) {
                        $result['msg'] = 'You are not authorized to create resignations';
                        echo json_encode($result); exit();
                    }
                    
                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    if(!$employee) {
                        $result['msg'] = 'Employee not found';
                        echo json_encode($result); exit();
                    }
                    
                    $data = [
                        'employee_id' => $post['employee_id'],
                        'resignation_date' => $post['resignation_date'],
                        'last_working_day' => $post['last_working_day'],
                        'reason' => $post['reason'] ?? '',
                        'status' => $post['status'] ?? 'Pending',
                        'added_by' => $_SESSION['user_id'],
                        'added_date' => date('Y-m-d H:i:s')
                    ];
                    
                    $resignationsClass = $GLOBALS['resignationsClass'];
                    $id = $resignationsClass->create($data);
                    if($id) {
                        // If status is approved, update employee status to inactive
                        if($post['status'] == 'Approved') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Inactive', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        
                        $result['msg'] = 'Resignation record added successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to add resignation record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result);
            }
            // TERMINATIONS CRUD OPERATIONS - CREATE
            else if($_GET['endpoint'] == 'termination') {
                $result = ['error' => false, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['employee_id']) || empty($post['termination_date']) || empty($post['reason'])) {
                        $result['msg'] = 'Employee, termination date and reason are required';
                        echo json_encode($result); exit();
                    }
                    
                    // Check authorization
                    if(!check_session('create_terminations')) {
                        $result['msg'] = 'You are not authorized to create terminations';
                        echo json_encode($result); exit();
                    }
                    
                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    if(!$employee) {
                        $result['msg'] = 'Employee not found';
                        echo json_encode($result); exit();
                    }
                    
                    $data = [
                        'employee_id' => $post['employee_id'],
                        'termination_date' => $post['termination_date'],
                        'reason' => $post['reason'],
                        'termination_type' => $post['termination_type'] ?? 'Involuntary',
                        'status' => $post['status'] ?? 'Pending',
                        'added_by' => $_SESSION['user_id'],
                        'added_date' => date('Y-m-d H:i:s')
                    ];
                    
                    $terminationsClass = $GLOBALS['terminationsClass'];
                    $id = $terminationsClass->create($data);
                    if($id) {
                        // If status is completed, update employee status to inactive
                        if($post['status'] == 'Completed') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Inactive', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        
                        $result['msg'] = 'Termination record added successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to add termination record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result);
            }
            // WARNINGS CRUD OPERATIONS - CREATE
            else if($_GET['endpoint'] == 'warning') {
                $result = ['error' => false, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    // var_dump($post); 
                    if(empty($post['employee_id']) || empty($post['issued_by'])) {
                        $result['msg'] = 'Employee and issuer are required';
                        echo json_encode($result); exit();
                    }

                    check_auth('create_warnings');

                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    if(!$employee) {
                        $result['msg'] = 'Employee not found';
                        echo json_encode($result); exit();
                    }

                    // Get issuer details (assuming 'users' table for issued_by)
                    $issuer = get_data('users', ['user_id' => $post['issued_by']])[0];
                    if(!$issuer) {
                        $result['msg'] = 'Issuer not found';
                        echo json_encode($result); exit();
                    }

                    $data = [
                        'employee_id' => $post['employee_id'],
                        'warning_date' => $post['warning_date'],
                        'reason' => $post['reason'],
                        'issued_by' => $post['issued_by'],
                        'severity' => $post['severity'] ?? 'Low', // Default to 'Low' if not provided
                        'added_by' => $_SESSION['user_id'],
                        'added_date' => date('Y-m-d H:i:s')
                    ];

                    $warningsClass = $GLOBALS['warningsClass']; // Assuming this is initialized in init.php
                    $id = $warningsClass->create($data);
                    if($id) {
                        $result['msg'] = 'Warning record added successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to add warning record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result);
            }
        }
        // UPDATE
        else if($_GET['action'] == 'update') {
            if($_GET['endpoint'] == 'promotion') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['promotion_id']) || empty($post['employee_id']) || empty($post['new_designation']) || empty($post['promotion_date'])) {
                        $result['msg'] = 'Promotion ID, employee, new designation and promotion date are required';
                        echo json_encode($result); exit();
                    }
                    
                    check_auth('edit_promotions');
                    
                    // Get current promotion record
                    $currentPromotion = get_data('promotions', ['promotion_id' => $post['promotion_id']])[0];
                    if(!$currentPromotion) {
                        $result['msg'] = 'Promotion record not found';
                        echo json_encode($result); exit();
                    }
                    
                    // Get employee and designation details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    $newDesignation = $post['new_designation'];
                    
                    $data = [
                        'new_designation' => $newDesignation,
                        'promotion_date' => $post['promotion_date'],
                        'new_salary' => $post['new_salary'] ?? null,
                        'reason' => $post['reason'] ?? '',
                        'status' => $post['status'],
                        'updated_by' => $_SESSION['user_id']
                    ];
                    
                    $updated = $promotionsClass->update($post['promotion_id'], $data);
                    if($updated) {
                        // If status changed to approved, update employee table
                        if($post['status'] == 'Approved' && $currentPromotion['status'] != 'Approved') {
                            $GLOBALS['conn']->query("UPDATE employees SET designation = '".$newDesignation."', salary = '".$post['new_salary']."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        // If status changed from approved to something else, revert employee table
                        else if($currentPromotion['status'] == 'Approved' && $post['status'] != 'Approved') {
                            if($currentPromotion['old_designation']) {
                                $oldDesignation = $currentPromotion['old_designation'];
                                if($oldDesignation) {
                                    $GLOBALS['conn']->query("UPDATE employees SET designation = '".$oldDesignation."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                                }
                            }
                        }
                        
                        $result['msg'] = 'Promotion record updated successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to update promotion record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            }
            // TRANSFERS CRUD OPERATIONS
            else if($_GET['endpoint'] == 'transfer') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['transfer_id']) || empty($post['employee_id']) || empty($post['new_department_id']) || empty($post['transfer_date'])) {
                        $result['msg'] = 'Transfer ID, employee, new department and transfer date are required';
                        echo json_encode($result); exit();
                    }
                    
                    // Check authorization
                    if(!check_session('edit_transfers')) {
                        $result['msg'] = 'You are not authorized to edit transfers';
                        echo json_encode($result); exit();
                    }
                    
                    $transfersClass = $GLOBALS['transfersClass'];
                    
                    // Get current transfer data
                    $currentTransfer = $transfersClass->get($post['transfer_id']);
                    if(!$currentTransfer) {
                        $result['msg'] = 'Transfer record not found';
                        echo json_encode($result); exit();
                    }
                    
                    // Get employee and department details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    $newDepartment = get_data('branches', ['id' => $post['new_department_id']])[0];
                    
                    $data = [
                        'new_department_id' => $post['new_department_id'],
                        'transfer_date' => $post['transfer_date'],
                        'reason' => $post['reason'] ?? '',
                        'status' => $post['status'] ?? 'Pending',
                        'updated_by' => $_SESSION['user_id']
                    ];
                    
                    $updated = $transfersClass->update($post['transfer_id'], $data);
                    
                    if($updated) {
                        // If status changed to approved, update employee table
                        if($post['status'] == 'Approved' && $currentTransfer['status'] != 'Approved') {
                            $GLOBALS['conn']->query("UPDATE employees SET branch_id = '".$post['new_department_id']."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        // If status changed from approved to something else, revert employee table
                        else if($currentTransfer['status'] == 'Approved' && $post['status'] != 'Approved') {
                            if($currentTransfer['old_department_id']) {
                                $GLOBALS['conn']->query("UPDATE employees SET branch_id = '".$currentTransfer['old_department_id']."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                            }
                        }
                        
                        $result['error'] = false;
                        $result['msg'] = 'Transfer record updated successfully';
                    } else {
                        $result['msg'] = 'Failed to update transfer record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // RESIGNATIONS CRUD OPERATIONS - UPDATE
            else if($_GET['endpoint'] == 'resignation') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['resignation_id']) || empty($post['employee_id']) || empty($post['resignation_date']) || empty($post['last_working_day'])) {
                        $result['msg'] = 'Resignation ID, employee, resignation date and last working day are required';
                        echo json_encode($result); exit();
                    }
                    
                    // Check authorization
                    if(!check_session('edit_resignations')) {
                        $result['msg'] = 'You are not authorized to edit resignations';
                        echo json_encode($result); exit();
                    }
                    
                    // Get current resignation data
                    $currentResignation = get_data('resignations', ['resignation_id' => $post['resignation_id']])[0];
                    if(!$currentResignation) {
                        $result['msg'] = 'Resignation record not found';
                        echo json_encode($result); exit();
                    }
                    
                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    
                    $data = [
                        'resignation_date' => $post['resignation_date'],
                        'last_working_day' => $post['last_working_day'],
                        'reason' => $post['reason'] ?? '',
                        'status' => $post['status'] ?? 'Pending',
                        'updated_by' => $_SESSION['user_id']
                    ];
                    
                    $resignationsClass = $GLOBALS['resignationsClass'];
                    $updated = $resignationsClass->update($post['resignation_id'], $data);
                    
                    if($updated) {
                        // If status changed to approved, update employee status to inactive
                        if($post['status'] == 'Approved' && $currentResignation['status'] != 'Approved') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Inactive', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        // If status changed from approved to something else, revert employee status
                        else if($currentResignation['status'] == 'Approved' && $post['status'] != 'Approved') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Active', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        
                        $result['error'] = false;
                        $result['msg'] = 'Resignation record updated successfully';
                    } else {
                        $result['msg'] = 'Failed to update resignation record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // TERMINATIONS CRUD OPERATIONS - UPDATE
            else if($_GET['endpoint'] == 'termination') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['termination_id']) || empty($post['employee_id']) || empty($post['termination_date']) || empty($post['reason'])) {
                        $result['msg'] = 'Termination ID, employee, termination date and reason are required';
                        echo json_encode($result); exit();
                    }
                    
                    // Check authorization
                    if(!check_session('edit_terminations')) {
                        $result['msg'] = 'You are not authorized to edit terminations';
                        echo json_encode($result); exit();
                    }
                    
                    // Get current termination data
                    $currentTermination = get_data('terminations', ['termination_id' => $post['termination_id']])[0];
                    if(!$currentTermination) {
                        $result['msg'] = 'Termination record not found';
                        echo json_encode($result); exit();
                    }
                    
                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    
                    $data = [
                        'termination_date' => $post['termination_date'],
                        'reason' => $post['reason'],
                        'termination_type' => $post['termination_type'] ?? 'Involuntary',
                        'status' => $post['status'] ?? 'Pending',
                        'updated_by' => $_SESSION['user_id']
                    ];
                    
                    $terminationsClass = $GLOBALS['terminationsClass'];
                    $updated = $terminationsClass->update($post['termination_id'], $data);
                    
                    if($updated) {
                        // If status changed to completed, update employee status to inactive
                        if($post['status'] == 'Completed' && $currentTermination['status'] != 'Completed') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Inactive', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        // If status changed from completed to something else, revert employee status
                        else if($currentTermination['status'] == 'Completed' && $post['status'] != 'Completed') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Active', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$post['employee_id']."'");
                        }
                        
                        $result['error'] = false;
                        $result['msg'] = 'Termination record updated successfully';
                    } else {
                        $result['msg'] = 'Failed to update termination record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // WARNINGS CRUD OPERATIONS - UPDATE
            else if($_GET['endpoint'] == 'warning') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['warning_id']) || empty($post['employee_id']) || empty($post['warning_date']) || empty($post['reason']) || empty($post['issued_by'])) {
                        $result['msg'] = 'Warning ID, employee, warning date, reason, and issuer are required';
                        echo json_encode($result); exit();
                    }

                    check_auth('edit_warnings');

                    // Get current warning record
                    $currentWarning = get_data('warnings', ['warning_id' => $post['warning_id']])[0];
                    if(!$currentWarning) {
                        $result['msg'] = 'Warning record not found';
                        echo json_encode($result); exit();
                    }

                    // Get employee details
                    $employee = get_data('employees', ['employee_id' => $post['employee_id']])[0];
                    if(!$employee) {
                        $result['msg'] = 'Employee not found';
                        echo json_encode($result); exit();
                    }

                    // Get issuer details (assuming 'users' table for issued_by)
                    $issuer = get_data('users', ['user_id' => $post['issued_by']])[0];
                    if(!$issuer) {
                        $result['msg'] = 'Issuer not found';
                        echo json_encode($result); exit();
                    }

                    $data = [
                        'employee_id' => $post['employee_id'],
                        'warning_date' => $post['warning_date'],
                        'reason' => $post['reason'],
                        'issued_by' => $post['issued_by'],
                        'severity' => $post['severity'] ?? 'Low',
                        'updated_by' => $_SESSION['user_id']
                    ];

                    $warningsClass = $GLOBALS['warningsClass']; // Assuming this is initialized in init.php
                    $updated = $warningsClass->update($post['warning_id'], $data);
                    if($updated) {
                        $result['msg'] = 'Warning record updated successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to update warning record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            }

            
        }
        // LOAD (DataTable)
        else if($_GET['action'] == 'load') {
            if($_GET['endpoint'] == 'promotions') {
                $result = ['error' => false, 'msg' => '', 'data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
                
                $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
                $length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
                $searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderBy = '';
                
                if(isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
                    $columns = ['promotion_id', 'employee_name', 'old_designation', 'new_designation', 'promotion_date', 'new_salary', 'status', 'added_date'];
                    $orderColumn = $columns[$_POST['order'][0]['column']] ?? 'promotion_id';
                    $orderDir = $_POST['order'][0]['dir'] == 'desc' ? 'DESC' : 'ASC';
                    $orderBy = "ORDER BY $orderColumn $orderDir";
                }
                
                $query = "SELECT p.*, e.full_name as employee_name, e.phone_number, old_designation, new_designation FROM promotions p LEFT JOIN employees e ON p.employee_id = e.employee_id WHERE p.promotion_id IS NOT NULL";
                
                if(!empty($searchParam)) {
                    $query .= " AND (e.full_name LIKE '%$searchParam%' OR e.phone_number LIKE '%$searchParam%' OR old_designation LIKE '%$searchParam%' OR p.status LIKE '%$searchParam%')";
                }
                
                $query .= " $orderBy LIMIT $start, $length";
                
                $promotions = $GLOBALS['conn']->query($query);
                
                $countQuery = "SELECT COUNT(*) as total FROM promotions p LEFT JOIN employees e ON p.employee_id = e.employee_id WHERE p.promotion_id IS NOT NULL";
                
                if(!empty($searchParam)) {
                    $countQuery .= " AND (e.full_name LIKE '%$searchParam%' OR e.phone_number LIKE '%$searchParam%' OR p.status LIKE '%$searchParam%')";
                }
                
                $countResult = $GLOBALS['conn']->query($countQuery);
                $totalRecords = $countResult->fetch_assoc()['total'];
                
                if($promotions->num_rows > 0) {
                    while($row = $promotions->fetch_assoc()) {
                        $actions = '<div class="btn-group">';
                        if(check_session('edit_promotions')) {
                            $actions .= '<span class="btn-sm cursor smr-10  btn-primary" onclick="get_promotion('.$row['promotion_id'].')" title="Edit"><i class="fa fa-pencil"></i></span>';
                        }
                        if(check_session('delete_promotions')) {
                            $actions .= '<span class="btn-sm cursor  btn-danger" onclick="delete_promotion('.$row['promotion_id'].')" title="Delete"><i class="fa fa-trash"></i></span>';
                        }
                        $actions .= '</div>';
                        
                        $statusBadge = '';
                        switch($row['status']) {
                            case 'Approved':
                                $statusBadge = '<span class="badge bg-success">Approved</span>';
                                break;
                            case 'Rejected':
                                $statusBadge = '<span class="badge bg-danger">Rejected</span>';
                                break;
                            default:
                                $statusBadge = '<span class="badge bg-warning">Pending</span>';
                        }
                        
                        $result['data'][] = [
                            // $row['promotion_id'],
                            $row['employee_name'] ?? '',
                            $row['old_designation'] ?? 'N/A',
                            $row['new_designation'] ?? '',
                            date('d M Y', strtotime($row['promotion_date'])),
                            $row['new_salary'] ? formatMoney($row['new_salary']) : 'N/A',
                            $statusBadge,
                            date('d M Y', strtotime($row['added_date'])),
                            $actions
                        ];
                    }
                    $result['msg'] = $promotions->num_rows . " records were found.";
                } else {
                    $result['msg'] = "No records found.";
                }
                
                $result['recordsTotal'] = $totalRecords;
                $result['recordsFiltered'] = $totalRecords;
                
                echo json_encode($result); exit();
            }
            // TRANSFERS CRUD OPERATIONS
            else if($_GET['endpoint'] == 'transfers') {
                $result = ['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []];
                
                try {
                    $draw = $_POST['draw'] ?? 1;
                    $start = $_POST['start'] ?? 0;
                    $length = $_POST['length'] ?? 10;
                    $searchParam = $_POST['search']['value'] ?? '';
                    $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
                    $orderDir = $_POST['order'][0]['dir'] ?? 'asc';
                    
                    $columns = ['transfer_id', 'employee_name', 'old_department_name', 'new_department_name', 'transfer_date', 'status', 'added_date'];
                    $orderColumn = $columns[$orderColumnIndex] ?? 'transfer_id';
                    
                    if($orderColumn == 'employee_name') {
                        $orderBy = "ORDER BY e.full_name $orderDir";
                    } else if($orderColumn == 'old_department_name') {
                        $orderBy = "ORDER BY od.name $orderDir";
                    } else if($orderColumn == 'new_department_name') {
                        $orderBy = "ORDER BY nd.name $orderDir";
                    } else {
                        $orderBy = "ORDER BY $orderColumn $orderDir";
                    }
                    
                    $query = "SELECT t.*, e.full_name as employee_name, e.phone_number, 
                             od.name as old_department_name, nd.name as new_department_name 
                             FROM transfers t 
                             LEFT JOIN employees e ON t.employee_id = e.employee_id 
                             LEFT JOIN branches od ON t.old_department_id = od.id 
                             LEFT JOIN branches nd ON t.new_department_id = nd.id 
                             WHERE t.transfer_id IS NOT NULL";
                    
                    if(!empty($searchParam)) {
                        $query .= " AND (e.full_name LIKE '%$searchParam%' OR e.phone_number LIKE '%$searchParam%' OR od.name LIKE '%$searchParam%' OR nd.name LIKE '%$searchParam%' OR t.status LIKE '%$searchParam%')";
                    }
                    
                    $query .= " $orderBy LIMIT $start, $length";
                    $transfers = $GLOBALS['conn']->query($query);
                    
                    $countQuery = "SELECT COUNT(*) as total FROM transfers t 
                                  LEFT JOIN employees e ON t.employee_id = e.employee_id 
                                  LEFT JOIN branches od ON t.old_department_id = od.id 
                                  LEFT JOIN branches nd ON t.new_department_id = nd.id 
                                  WHERE t.transfer_id IS NOT NULL";
                    
                    if(!empty($searchParam)) {
                        $countQuery .= " AND (e.full_name LIKE '%$searchParam%' OR e.phone_number LIKE '%$searchParam%' OR od.name LIKE '%$searchParam%' OR nd.name LIKE '%$searchParam%' OR t.status LIKE '%$searchParam%')";
                    }
                    
                    $countResult = $GLOBALS['conn']->query($countQuery);
                    $totalRecords = $countResult->fetch_assoc()['total'];
                    
                    if($transfers->num_rows > 0) {
                        while($row = $transfers->fetch_assoc()) {
                            $actions = '<div class="btn-group">';
                            if(check_session('edit_transfers')) {
                                $actions .= '<span class="btn-sm cursor smr-10  btn-primary" onclick="get_transfer('.$row['transfer_id'].')" title="Edit"><i class="fa fa-pencil"></i></span>';
                            }
                            if(check_session('delete_transfers')) {
                                $actions .= '<span class="btn-sm cursor  btn-danger" onclick="delete_transfer('.$row['transfer_id'].')" title="Delete"><i class="fa fa-trash"></i></span>';
                            }
                            $actions .= '</div>';
                            
                            $statusBadge = '<span class="badge bg-' . ($row['status'] == 'Approved' ? 'success' : ($row['status'] == 'Rejected' ? 'danger' : 'warning')) . '">' . $row['status'] . '</span>';
                            
                            $result['data'][] = [
                                // $row['transfer_id'],
                                $row['employee_name'] ?? '',
                                $row['old_department_name'] ?? 'N/A',
                                $row['new_department_name'] ?? '',
                                date('d M Y', strtotime($row['transfer_date'])),
                                $statusBadge,
                                date('d M Y', strtotime($row['added_date'])),
                                $actions
                            ];
                        }
                    }
                    
                    $result['draw'] = $draw;
                    $result['recordsTotal'] = $totalRecords;
                    $result['recordsFiltered'] = $totalRecords;
                    
                } catch(Exception $e) {
                    $result['error'] = true;
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                
                echo json_encode($result);
            }
            // RESIGNATIONS CRUD OPERATIONS - LOAD
            else if($_GET['endpoint'] == 'resignations') {
                $result = ['error' => false, 'msg' => '', 'data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];

                $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
                $length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
                $searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderBy = '';

                if(isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
                    $columns = ['resignation_id', 'employee_name', 'resignation_date', 'last_working_day', 'reason', 'status', 'added_date'];
                    $orderColumn = $columns[$_POST['order'][0]['column']] ?? 'resignation_id';
                    $orderDir = $_POST['order'][0]['dir'] == 'desc' ? 'DESC' : 'ASC';
                    // Handle ordering by joined table columns
                    if ($orderColumn == 'employee_name') {
                        $orderBy = "ORDER BY e.full_name $orderDir";
                    } else {
                        $orderBy = "ORDER BY r.$orderColumn $orderDir";
                    }
                }

                $query = "SELECT r.*, e.full_name as employee_name
                          FROM resignations r
                          LEFT JOIN employees e ON r.employee_id = e.employee_id
                          WHERE r.resignation_id IS NOT NULL";

                if(!empty($searchParam)) {
                    $query .= " AND (e.full_name LIKE '%$searchParam%' OR r.reason LIKE '%$searchParam%' OR r.status LIKE '%$searchParam%')";
                }

                $query .= " $orderBy LIMIT $start, $length";

                $resignations = $GLOBALS['conn']->query($query);

                $countQuery = "SELECT COUNT(*) as total
                               FROM resignations r
                               LEFT JOIN employees e ON r.employee_id = e.employee_id
                               WHERE r.resignation_id IS NOT NULL";

                if(!empty($searchParam)) {
                    $countQuery .= " AND (e.full_name LIKE '%$searchParam%' OR r.reason LIKE '%$searchParam%' OR r.status LIKE '%$searchParam%')";
                }

                $countResult = $GLOBALS['conn']->query($countQuery);
                $totalRecords = $countResult->fetch_assoc()['total'];

                if($resignations->num_rows > 0) {
                    while($row = $resignations->fetch_assoc()) {
                        $actions = '<div class="btn-group">';
                        if(check_session('edit_resignations')) {
                            $actions .= '<span class="btn-sm cursor smr-10  btn-primary" onclick="get_resignation('.$row['resignation_id'].')" title="Edit"><i class="fa fa-pencil"></i></span>';
                        }
                        if(check_session('delete_resignations')) {
                            $actions .= '<span class="btn-sm cursor  btn-danger" onclick="delete_resignation('.$row['resignation_id'].')" title="Delete"><i class="fa fa-trash"></i></span>';
                        }
                        $actions .= '</div>';

                        $statusBadge = '<span class="badge bg-' . ($row['status'] == 'Approved' ? 'success' : ($row['status'] == 'Rejected' ? 'danger' : 'warning')) . '">' . $row['status'] . '</span>';

                        $result['data'][] = [
                            // $row['resignation_id'],
                            $row['employee_name'] ?? '',
                            date('d M Y', strtotime($row['resignation_date'])),
                            date('d M Y', strtotime($row['last_working_day'])),
                            $statusBadge,
                            date('d M Y', strtotime($row['added_date'])),
                            $actions
                        ];
                    }
                    $result['msg'] = $resignations->num_rows . " records were found.";
                } else {
                    $result['msg'] = "No records found.";
                }

                $result['recordsTotal'] = $totalRecords;
                $result['recordsFiltered'] = $totalRecords;

                echo json_encode($result); exit();
            }
            // TERMINATIONS CRUD OPERATIONS - LOAD
            else if($_GET['endpoint'] == 'terminations') {
                $result = ['error' => false, 'msg' => '', 'data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];

                $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
                $length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
                $searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderBy = '';

                if(isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
                    $columns = ['termination_id', 'employee_name', 'termination_date', 'reason', 'termination_type', 'status', 'added_date'];
                    $orderColumn = $columns[$_POST['order'][0]['column']] ?? 'termination_id';
                    $orderDir = $_POST['order'][0]['dir'] == 'desc' ? 'DESC' : 'ASC';
                    // Handle ordering by joined table columns
                    if ($orderColumn == 'employee_name') {
                        $orderBy = "ORDER BY e.full_name $orderDir";
                    } else {
                        $orderBy = "ORDER BY t.$orderColumn $orderDir";
                    }
                }

                $query = "SELECT t.*, e.full_name as employee_name
                          FROM terminations t
                          LEFT JOIN employees e ON t.employee_id = e.employee_id
                          WHERE t.termination_id IS NOT NULL";

                if(!empty($searchParam)) {
                    $query .= " AND (e.full_name LIKE '%$searchParam%' OR t.reason LIKE '%$searchParam%' OR t.status LIKE '%$searchParam%')";
                }

                $query .= " $orderBy LIMIT $start, $length";

                $terminations = $GLOBALS['conn']->query($query);

                $countQuery = "SELECT COUNT(*) as total
                               FROM terminations t
                               LEFT JOIN employees e ON t.employee_id = e.employee_id
                               WHERE t.termination_id IS NOT NULL";

                if(!empty($searchParam)) {
                    $countQuery .= " AND (e.full_name LIKE '%$searchParam%' OR t.reason LIKE '%$searchParam%' OR t.status LIKE '%$searchParam%')";
                }

                $countResult = $GLOBALS['conn']->query($countQuery);
                $totalRecords = $countResult->fetch_assoc()['total'];

                if($terminations->num_rows > 0) {
                    while($row = $terminations->fetch_assoc()) {
                        $actions = '<div class="btn-group">';
                        if(check_session('edit_terminations')) {
                            $actions .= '<span class="btn-sm cursor smr-10  btn-primary" onclick="get_termination('.$row['termination_id'].')" title="Edit"><i class="fa fa-pencil"></i></span>';
                        }
                        if(check_session('delete_terminations')) {
                            $actions .= '<span class="btn-sm cursor  btn-danger" onclick="delete_termination('.$row['termination_id'].')" title="Delete"><i class="fa fa-trash"></i></span>';
                        }
                        $actions .= '</div>';

                        $statusBadge = '<span class="badge bg-' . ($row['status'] == 'Completed' ? 'success' : ($row['status'] == 'Rejected' ? 'danger' : 'warning')) . '">' . $row['status'] . '</span>';

                        $result['data'][] = [
                            // $row['termination_id'],
                            $row['employee_name'] ?? '',
                            date('d M Y', strtotime($row['termination_date'])),
                            $row['reason'] ?? '',
                            $row['termination_type'] ?? '',
                            $statusBadge,
                            date('d M Y', strtotime($row['added_date'])),
                            $actions
                        ];
                    }
                    $result['msg'] = $terminations->num_rows . " records were found.";
                } else {
                    $result['msg'] = "No records found.";
                }

                $result['recordsTotal'] = $totalRecords;
                $result['recordsFiltered'] = $totalRecords;

                echo json_encode($result); exit();
            }
            // WARNINGS CRUD OPERATIONS - LOAD
            else if($_GET['endpoint'] == 'warnings') {
                $result = ['error' => false, 'msg' => '', 'data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];

                $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
                $length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
                $searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderBy = '';

                if(isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
                    $columns = ['warning_id', 'employee_name', 'warning_date', 'reason', 'issued_by_name', 'severity', 'added_date'];
                    $orderColumn = $columns[$_POST['order'][0]['column']] ?? 'warning_id';
                    $orderDir = $_POST['order'][0]['dir'] == 'desc' ? 'DESC' : 'ASC';
                    // Handle ordering by joined table columns
                    if ($orderColumn == 'employee_name') {
                        $orderBy = "ORDER BY e.full_name $orderDir";
                    } elseif ($orderColumn == 'issued_by_name') {
                        $orderBy = "ORDER BY u.full_name $orderDir";
                    } else {
                        $orderBy = "ORDER BY w.$orderColumn $orderDir";
                    }
                }

                $query = "SELECT w.*, e.full_name as employee_name, u.full_name as issued_by_name
                          FROM warnings w
                          LEFT JOIN employees e ON w.employee_id = e.employee_id
                          LEFT JOIN users u ON w.issued_by = u.user_id
                          WHERE w.warning_id IS NOT NULL";

                if(!empty($searchParam)) {
                    $query .= " AND (e.full_name LIKE '%$searchParam%' OR u.full_name LIKE '%$searchParam%' OR w.reason LIKE '%$searchParam%' OR w.severity LIKE '%$searchParam%')";
                }

                $query .= " $orderBy LIMIT $start, $length";

                $warnings = $GLOBALS['conn']->query($query);

                $countQuery = "SELECT COUNT(*) as total
                               FROM warnings w
                               LEFT JOIN employees e ON w.employee_id = e.employee_id
                               LEFT JOIN users u ON w.issued_by = u.user_id
                               WHERE w.warning_id IS NOT NULL";

                if(!empty($searchParam)) {
                    $countQuery .= " AND (e.full_name LIKE '%$searchParam%' OR u.full_name LIKE '%$searchParam%' OR w.reason LIKE '%$searchParam%' OR w.severity LIKE '%$searchParam%')";
                }

                $countResult = $GLOBALS['conn']->query($countQuery);
                $totalRecords = $countResult->fetch_assoc()['total'];

                if($warnings->num_rows > 0) {
                    while($row = $warnings->fetch_assoc()) {
                        $actions = '<div class="btn-group">';
                        if(check_session('edit_warnings')) {
                            $actions .= '<span class="btn-sm cursor smr-10  btn-primary" onclick="get_warning('.$row['warning_id'].')" title="Edit"><i class="fa fa-pencil"></i></span>';
                        }
                        if(check_session('delete_warnings')) {
                            $actions .= '<span class="btn-sm cursor  btn-danger" onclick="delete_warning('.$row['warning_id'].')" title="Delete"><i class="fa fa-trash"></i></span>';
                        }
                        $actions .= '</div>';

                        $severityBadge = '<span class="badge bg-' . ($row['severity'] == 'Low' ? 'success' : ($row['severity'] == 'Medium' ? 'warning' : 'danger')) . '">' . $row['severity'] . '</span>';

                        $result['data'][] = [
                            // $row['warning_id'],
                            $row['employee_name'] ?? '',
                            date('d M Y', strtotime($row['warning_date'])),
                            $row['reason'] ?? '',
                            $row['issued_by_name'] ?? '',
                            $severityBadge,
                            date('d M Y', strtotime($row['added_date'])),
                            $actions
                        ];
                    }
                    $result['msg'] = $warnings->num_rows . " records were found.";
                } else {
                    $result['msg'] = "No records found.";
                }

                $result['recordsTotal'] = $totalRecords;
                $result['recordsFiltered'] = $totalRecords;

                echo json_encode($result); exit();
            }
        }
        // GET (single)
        else if($_GET['action'] == 'get') {
            if($_GET['endpoint'] == 'promotions') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                if($id) {
                    $promotion = get_data('promotions', ['promotion_id' => $id]);
                    if($promotion) {
                        $promotionData = $promotion[0];
                        
                        // Get employee details
                        $employee = get_data('employees', ['employee_id' => $promotionData['employee_id']])[0];
                        $promotionData['employee_name'] = $employee['full_name'] ?? '';
                        $promotionData['current_salary'] = $employee['salary'] ?? '';
                        
                        // Get designation names
                        if($promotionData['old_designation']) {
                            $oldDesignation = get_data('designations', ['id' => $promotionData['old_designation']])[0];
                            $promotionData['old_designation_name'] = $oldDesignation['name'] ?? '';
                        }
                        
                        echo json_encode(['error' => false, 'data' => $promotionData]);
                    } else {
                        echo json_encode(['error' => true, 'msg' => 'Promotion record not found']);
                    }
                } else {
                    echo json_encode(['error' => true, 'msg' => 'Promotion ID is required']);
                }
                exit();
            }
            // TRANSFERS CRUD OPERATIONS
            else if($_GET['endpoint'] == 'transfer') {
                $result = ['error' => true, 'msg' => '', 'data' => []];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['id'])) {
                        $result['msg'] = 'Transfer ID is required';
                        echo json_encode($result); exit();
                    }
                    
                    $transfersClass = $GLOBALS['transfersClass'];
                    $transferData = $transfersClass->get($post['id']);
                    
                    if($transferData) {
                        // Get employee details
                        $employee = get_data('employees', ['employee_id' => $transferData['employee_id']])[0];
                        $transferData['employee_name'] = $employee['full_name'] ?? '';
                        
                        // Get department names
                        if($transferData['old_department_id']) {
                            $oldDepartment = get_data('branches', ['id' => $transferData['old_department_id']])[0];
                            $transferData['old_department_name'] = $oldDepartment['name'] ?? '';
                        }
                        
                        if($transferData['new_department_id']) {
                            $newDepartment = get_data('branches', ['id' => $transferData['new_department_id']])[0];
                            $transferData['new_department_name'] = $newDepartment['name'] ?? '';
                        }
                        
                        $result['error'] = false;
                        $result['data'] = $transferData;
                    } else {
                        $result['msg'] = 'Transfer record not found';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // RESIGNATIONS CRUD OPERATIONS - GET
            else if($_GET['endpoint'] == 'resignation') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                if($id) {
                    $resignation = get_data('resignations', ['resignation_id' => $id]);
                    if($resignation) {
                        $resignationData = $resignation[0];

                        // Get employee details
                        $employee = get_data('employees', ['employee_id' => $resignationData['employee_id']])[0];
                        $resignationData['employee_name'] = $employee['full_name'] ?? '';

                        echo json_encode(['error' => false, 'data' => $resignationData]);
                    } else {
                        echo json_encode(['error' => true, 'msg' => 'Resignation record not found']);
                    }
                } else {
                    echo json_encode(['error' => true, 'msg' => 'Resignation ID is required']);
                }
                exit();
            }
            // TERMINATIONS CRUD OPERATIONS - GET
            else if($_GET['endpoint'] == 'termination') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                if($id) {
                    $termination = get_data('terminations', ['termination_id' => $id]);
                    if($termination) {
                        $terminationData = $termination[0];

                        // Get employee details
                        $employee = get_data('employees', ['employee_id' => $terminationData['employee_id']])[0];
                        $terminationData['employee_name'] = $employee['full_name'] ?? '';

                        echo json_encode(['error' => false, 'data' => $terminationData]);
                    } else {
                        echo json_encode(['error' => true, 'msg' => 'Termination record not found']);
                    }
                } else {
                    echo json_encode(['error' => true, 'msg' => 'Termination ID is required']);
                }
                exit();
            }
            // WARNINGS CRUD OPERATIONS - GET
            else if($_GET['endpoint'] == 'warning') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                if($id) {
                    $warning = get_data('warnings', ['warning_id' => $id]);
                    if($warning) {
                        $warningData = $warning[0];

                        // Get employee details
                        $employee = get_data('employees', ['employee_id' => $warningData['employee_id']])[0];
                        $warningData['employee_name'] = $employee['full_name'] ?? '';

                        // Get issuer details
                        $issuer = get_data('users', ['user_id' => $warningData['issued_by']])[0];
                        $warningData['issued_by_name'] = $issuer['full_name'] ?? '';

                        echo json_encode(['error' => false, 'data' => $warningData]);
                    } else {
                        echo json_encode(['error' => true, 'msg' => 'Warning record not found']);
                    }
                } else {
                    echo json_encode(['error' => true, 'msg' => 'Warning ID is required']);
                }
                exit();
            }
        }
        // DELETE
        else if($_GET['action'] == 'delete') {
            if($_GET['endpoint'] == 'promotions') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['id'])) {
                        $result['msg'] = 'Promotion ID is required';
                        echo json_encode($result); exit();
                    }
                    
                    check_auth('delete_promotions');
                    
                    // Get promotion details before deletion
                    $promotion = get_data('promotions', ['promotion_id' => $post['id']])[0];
                    if(!$promotion) {
                        $result['msg'] = 'Promotion record not found';
                        echo json_encode($result); exit();
                    }
                    
                    $deleted = $promotionsClass->delete($post['id']);
                    if($deleted) {
                        // If the promotion was approved, revert employee designation
                        if($promotion['status'] == 'Approved' && $promotion['old_designation']) {
                            $oldDesignation = get_data('designations', ['id' => $promotion['old_designation']])[0];
                            if($oldDesignation) {
                                $GLOBALS['conn']->query("UPDATE employees SET designation = '".$oldDesignation['name']."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$promotion['employee_id']."'");
                            }
                        }
                        
                        $result['msg'] = 'Promotion record deleted successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to delete promotion record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            }
            // TRANSFERS CRUD OPERATIONS
            else if($_GET['endpoint'] == 'transfer') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['transfer_id'])) {
                        $result['msg'] = 'Transfer ID is required';
                        echo json_encode($result); exit();
                    }
                    
                    // Check authorization
                    if(!check_session('delete_transfers')) {
                        $result['msg'] = 'You are not authorized to delete transfers';
                        echo json_encode($result); exit();
                    }
                    
                    $transfersClass = $GLOBALS['transfersClass'];
                    
                    // Get transfer data before deletion
                    $transfer = $transfersClass->get($post['transfer_id']);
                    if(!$transfer) {
                        $result['msg'] = 'Transfer record not found';
                        echo json_encode($result); exit();
                    }
                    
                    $deleted = $transfersClass->delete($post['transfer_id']);
                    if($deleted) {
                        // If the transfer was approved, revert employee department
                        if($transfer['status'] == 'Approved' && $transfer['old_department_id']) {
                            $GLOBALS['conn']->query("UPDATE employees SET branch_id = '".$transfer['old_department_id']."', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$transfer['employee_id']."'");
                        }
                        
                        $result['error'] = false;
                        $result['msg'] = 'Transfer record deleted successfully';
                    } else {
                        $result['msg'] = 'Failed to delete transfer record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // RESIGNATIONS CRUD OPERATIONS - DELETE
            else if($_GET['endpoint'] == 'resignation') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['resignation_id'])) {
                        $result['msg'] = 'Resignation ID is required';
                        echo json_encode($result); exit();
                    }

                    // Check authorization
                    if(!check_session('delete_resignations')) {
                        $result['msg'] = 'You are not authorized to delete resignations';
                        echo json_encode($result); exit();
                    }

                    // Get resignation details before deletion (optional, but good practice if there were related actions)
                    $resignation = get_data('resignations', ['resignation_id' => $post['resignation_id']])[0];
                    if(!$resignation) {
                        $result['msg'] = 'Resignation record not found';
                        echo json_encode($result); exit();
                    }

                    $resignationsClass = $GLOBALS['resignationsClass'];
                    $deleted = $resignationsClass->delete($post['resignation_id']);
                    if($deleted) {
                        // If the resignation was approved, revert employee status
                        if($resignation['status'] == 'Approved') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Active', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$resignation['employee_id']."'");
                        }
                        
                        $result['msg'] = 'Resignation record deleted successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to delete resignation record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // TERMINATIONS CRUD OPERATIONS - DELETE
            else if($_GET['endpoint'] == 'termination') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['termination_id'])) {
                        $result['msg'] = 'Termination ID is required';
                        echo json_encode($result); exit();
                    }

                    // Check authorization
                    if(!check_session('delete_terminations')) {
                        $result['msg'] = 'You are not authorized to delete terminations';
                        echo json_encode($result); exit();
                    }

                    // Get termination details before deletion (optional, but good practice if there were related actions)
                    $termination = get_data('terminations', ['termination_id' => $post['termination_id']])[0];
                    if(!$termination) {
                        $result['msg'] = 'Termination record not found';
                        echo json_encode($result); exit();
                    }

                    $terminationsClass = $GLOBALS['terminationsClass'];
                    $deleted = $terminationsClass->delete($post['termination_id']);
                    if($deleted) {
                        // If the termination was completed, revert employee status
                        if($termination['status'] == 'Completed') {
                            $GLOBALS['conn']->query("UPDATE employees SET status = 'Active', updated_by = '".$_SESSION['user_id']."', updated_date = '".date('Y-m-d H:i:s')."' WHERE employee_id = '".$termination['employee_id']."'");
                        }
                        
                        $result['msg'] = 'Termination record deleted successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to delete termination record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: ' . $e->getMessage();
                }
                echo json_encode($result);
            }
            // WARNINGS CRUD OPERATIONS - DELETE
            else if($_GET['endpoint'] == 'warning') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['warning_id'])) {
                        $result['msg'] = 'Warning ID is required';
                        echo json_encode($result); exit();
                    }

                    check_auth('delete_warnings');

                    // Get warning details before deletion (optional, but good practice if there were related actions)
                    $warning = get_data('warnings', ['warning_id' => $post['warning_id']])[0];
                    if(!$warning) {
                        $result['msg'] = 'Warning record not found';
                        echo json_encode($result); exit();
                    }

                    $warningsClass = $GLOBALS['warningsClass']; // Assuming this is initialized in init.php
                    $deleted = $warningsClass->delete($post['warning_id']);
                    if($deleted) {
                        $result['msg'] = 'Warning record deleted successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to delete warning record';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            }
        }
    }
}