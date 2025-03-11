<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'save') {
			if($_GET['endpoint'] == 'leave_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'paid_type' => $post['paid_type'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('leave_types', ['name' => $post['name']]);
				    check_auth('manage_leaves');

				    // Call the create method
				    $result['id'] = $leaveTypesClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Leave type created successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'employee_leave') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $leave_typeInfo = get_data('leave_types', array('id' => $post['leave_id']))[0];
				    $data = array(
				        'emp_id' => $post['emp_id'], 
				        'leave_id' => $post['leave_id'], 
				        'paid_type' => $leave_typeInfo['paid_type'], 
				        'date_from' => $post['date_from'], 
				        'date_to' => $post['date_to'], 
				        'days_num' => getDateDifference($post['date_from'], $post['date_to'])['totalDays'],
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('employee_leave', ['leave_id' => $post['leave_id'], 'emp_id' => $post['emp_id'], 'date_from' => $post['date_from']]);
				    check_auth('manage_leaves');

				    // Call the create method
				    $result['id'] = $employeeLeaveClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Employee leave request created successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'attendance') {
				try {
				    // Prepare data from POST request
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    $data = array(
				        'ref' => $post['ref'], 
				        'ref_id' => $post['ref_id'], 
				        'ref_name' => $post['ref_name'], 
				        'atten_date' => $post['atten_date'], 
				        'added_by' => $_SESSION['user_id']
				    );

				     check_auth('mark_attendance');

				    // Call the create method
				    $result['id'] = $attendanceClass->create($data);
				    if($result['id']) {
				    	$atten_id = $result['id'];
				    	if($post['ref']) {
				    		$ref_id = $post['ref_id'];
				    		$get_employees = "SELECT * FROM `employees` WHERE `status` = 'active'";
				    		if($post['ref'] == 'Employee') {
				    			$get_employees .= " AND `employee_id` = '$ref_id'";
				    		} else if($post['ref'] == 'Department') {
				    			$get_employees .= " AND `branch_id` = '$ref_id'";
				    		} else if($post['ref'] == 'Location') {
				    			$get_employees .= " AND `location_id` = '$ref_id'";
				    		}

				    		$empSet = $GLOBALS['conn']->query($get_employees);
				    		if($empSet->num_rows > 0) {
				    			while($row = $empSet->fetch_assoc()) {
				    				$employee_id = $row['employee_id'];
				    				$full_name = $row['full_name'];
				    				$phone_number = $row['phone_number'];
				    				$email = $row['email'];
				    				$staff_no = $row['staff_no'];

				    				$leaveType = '';
				    				$atten_date = $post['atten_date'];
				    				$check_leave = $GLOBALS['conn']->query("SELECT * FROM `employee_leave` WHERE `emp_id` = '$employee_id' AND `status` <> 'Cancelled' AND '$atten_date' BETWEEN `date_from` AND `date_to`");
				    				if($check_leave->num_rows > 0) {
				    					while($leaveRow = $check_leave->fetch_assoc()) {
				    						$paid_type = $leaveRow['paid_type'];
				    						if($paid_type == 'Unpaid') {
				    							$leaveType = 'UL';
				    						} else {$leaveType = 'PL';}
				    					}
				    				}

				    				if($leaveType) $post['atten_status'] = $leaveType;

				    				$detailData = [
				    					'atten_id' => $atten_id,
				    					'emp_id' => $employee_id,
				    					'full_name' => $full_name,
				    					'phone_number' => $phone_number,
				    					'email' => $email,
				    					'staff_no' => $staff_no,
				    					'status' => $post['atten_status'],
				    					'atten_date' => $post['atten_date'], 
				    					'added_by' => $_SESSION['user_id']
				    				];

				    				$result['id'] = $attenDetailsClass->create($detailData);
				    			}
				    		} else {
				    			throw new Exception("No employees were found.");
				    		}
				    	}
				    } 

				    $GLOBALS['conn']->commit();


				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Attendance recorded successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $GLOBALS['conn']->rollback();
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['msg'] .= ' '. $e->getMessage();;
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'upload_attendance') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();

				    $result = ['error' => false, 'msg' => '', 'errors' => ''];

				    check_auth('mark_attendance'); // Authorization check

				    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
				        $fileTmpPath = $_FILES['file']['tmp_name'];
				        $fileName = $_FILES['file']['name'];
				        $fileType = $_FILES['file']['type'];

				        // Validate file type
				        if ($fileType != 'text/csv') {
				            $result['error'] = true;
				            $result['msg'] = "Invalid file type. Please upload a valid CSV file.";
				            echo json_encode($result);
				            exit();
				        }

				        if (($file = fopen($fileTmpPath, 'r')) !== false) {
				            $row = 0;

				            while (($line = fgetcsv($file, 1000, ',')) !== false) {
				                $row++;
				                if ($row == 1) continue; // Skip header row

				                // Ensure the row has the correct number of columns
				                if (count($line) < 7) {
				                    $result['errors'] .= "Skipping invalid row at line $row. \n";
				                    continue;
				                }

				                list($staff_no, $employee_id, $full_name, $atten_date, $attend_status, $ref, $ref_id, $ref_name) = array_map('escapeStr', $line);

				                // Check for missing required fields
				                if (!$full_name || !$employee_id || !$attend_status || !$ref_id || !$ref_name) {
				                    $result['errors'] .= "Missing required fields at line $row. \n";
				                    continue;
				                }

				                $atten_date = date('Y-m-d', strtotime($atten_date));

				                // Check or create attendance record
				                $attendanceRecord = $GLOBALS['conn']->query("SELECT id FROM `attendance` WHERE `ref` = '$ref' AND `ref_id` = '$ref_id' AND `atten_date` = '$atten_date'");
				                if ($attendanceRecord->num_rows > 0) {
				                    $atten_id = $attendanceRecord->fetch_assoc()['id'];
				                } else {
				                    $data = [
				                        'ref' => $ref,
				                        'ref_id' => $ref_id,
				                        'ref_name' => $ref_name,
				                        'atten_date' => $atten_date,
				                        'added_by' => $_SESSION['user_id']
				                    ];
				                    $atten_id = $attendanceClass->create($data);
				                }

				                // Validate attendance ID
				                if (!$atten_id) {
				                    $result['errors'] .= "Failed to create or retrieve attendance record for line $row. \n";
				                    continue;
				                }

				                // Get employees matching the reference
				                $get_employees = "SELECT * FROM `employees` WHERE `status` = 'active' AND `employee_id` = '$employee_id'";
				                if ($ref == 'Employee') {
				                    $get_employees .= " AND `employee_id` = '$ref_id'";
				                } elseif ($ref == 'Department') {
				                    $get_employees .= " AND `branch_id` = '$ref_id'";
				                } elseif ($ref == 'Location') {
				                    $get_employees .= " AND `location_id` = '$ref_id'";
				                }

				                $empSet = $GLOBALS['conn']->query($get_employees);
				                if ($empSet->num_rows > 0) {
				                    while ($empRow = $empSet->fetch_assoc()) {
				                        $employee_id = $empRow['employee_id'];

				                        // Check if attendance detail already exists
				                        $detailCheck = $GLOBALS['conn']->query("SELECT id FROM `atten_details` WHERE `atten_id` = '$atten_id' AND `emp_id` = '$employee_id'");
				                        if ($detailCheck->num_rows > 0) {
				                            continue; // Skip if already exists
				                        }

				                        /*$leaveType = '';
					    				$check_leave = $GLOBALS['conn']->query("SELECT * FROM `employee_leave` WHERE `emp_id` = '$employee_id' AND `status` <> 'Cancelled' AND '$atten_date' BETWEEN `date_from` AND `date_to`");
					    				if($check_leave->num_rows > 0) {
					    					while($leaveRow = $check_leave->fetch_assoc()) {
					    						$paid_type = $leaveRow['paid_type'];
					    						if($paid_type == 'Unpaid') {
					    							$leaveType = 'UL';
					    						} else {$leaveType = 'PL';}
					    					}
					    				}

					    				if($leaveType) $attend_status = $leaveType;*/

				                        $detailData = [
				                            'atten_id' => $atten_id,
				                            'emp_id' => $employee_id,
				                            'full_name' => $empRow['full_name'],
				                            'phone_number' => $empRow['phone_number'],
				                            'email' => $empRow['email'],
				                            'staff_no' => $empRow['staff_no'],
				                            'status' => $attend_status,
				                            'atten_date' => $atten_date,
				                            'added_by' => $_SESSION['user_id']
				                        ];

				                        // Create attendance detail
				                        $attenDetailsClass->create($detailData);
				                    }
				                } else {
				                    $GLOBALS['conn']->rollback();
				                    throw new Exception("No active employees found for reference at line $row.");
				                }
				            }

				            fclose($file);
				            $GLOBALS['conn']->commit();
				            $result['msg'] = "Attendance uploaded successfully.";
				        } else {
				            throw new Exception("File read error.");
				        }
				    } else {
				        throw new Exception("Please select a file.");
				    }
				} catch (Exception $e) {
				    $GLOBALS['conn']->rollback();
				    $result['error'] = true;
				    $result['msg'] = $e->getMessage();
				    error_log($e->getMessage());
				}

				echo json_encode($result);
			} else if($_GET['endpoint'] == 'timesheet') {
				try {
				    // Prepare data from POST request
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    $data = array(
				        'ts_date' => $post['ts_date'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    $tsDate = date("Y-m-d", strtotime($post['ts_date']));
				    check_auth('manage_timesheets');

				    $check_exists = $GLOBALS['conn']->query("SELECT * FROM `timesheet` WHERE `ts_date` LIKE '$tsDate%'");
				    if($check_exists->num_rows > 0) {
				    	while($foundRow = $check_exists->fetch_assoc()) {
				    		$ts_id = $foundRow['id'];
				    		$result['id'] = $ts_id;
				    	}
				    } else {
				    	$result['id'] = $timesheetClass->create($data);
				    }

				    if($result['id']) {
				    	$ts_id = $result['id'];
				    	if($post['emp_id']) {
				    		$emp_id = $post['emp_id'];
				    		$get_employees = "SELECT * FROM `employees` WHERE `status` = 'active' AND `employee_id` = '$emp_id'";

				    		$empSet = $GLOBALS['conn']->query($get_employees);
				    		if($empSet->num_rows > 0) {
				    			while($row = $empSet->fetch_assoc()) {
				    				$employee_id = $row['employee_id'];
				    				$full_name = $row['full_name'];
				    				$phone_number = $row['phone_number'];
				    				$email = $row['email'];
				    				$staff_no = $row['staff_no'];

				    				$detailData = [
				    					'ts_id' 	=> $ts_id,
				    					'emp_id' 	=> $employee_id,
				    					'full_name' 	=> $full_name,
				    					'phone_number' 	=> $phone_number,
				    					'email' 		=> $email,
				    					'staff_no' 		=> $staff_no,
				    					'ts_date' 		=> $post['ts_date'], 
				    					'time_in' 		=> $post['time_in'], 
				    					'time_out' 		=> $post['time_out'], 
				    					'added_by' 		=> $_SESSION['user_id']
				    				];

				    				$result['id'] = $timesheetDetailsClass->create($detailData);
				    			}
				    		} else {
				    			throw new Exception("No employees were found.");
				    		}
				    	}
				    } 

				    $GLOBALS['conn']->commit();


				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Timesheet recorded successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $GLOBALS['conn']->rollback();
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['msg'] .= ' '. $e->getMessage();;
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'upload_timesheet') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();

				    $result = ['error' => false, 'msg' => '', 'errors' => ''];

				    check_auth('manage_timesheets'); // Authorization check

				    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
				        $fileTmpPath = $_FILES['file']['tmp_name'];
				        $fileName = $_FILES['file']['name'];
				        $fileType = $_FILES['file']['type'];

				        // Validate file type
				        if ($fileType != 'text/csv') {
				            $result['error'] = true;
				            $result['msg'] = "Invalid file type. Please upload a valid CSV file.";
				            echo json_encode($result);
				            exit();
				        }

				        if (($file = fopen($fileTmpPath, 'r')) !== false) {
				            $row = 0;

				            while (($line = fgetcsv($file, 1000, ',')) !== false) {
				                $row++;
				                if ($row == 1) continue; // Skip header row

				                // Ensure the row has the correct number of columns
				                if (count($line) < 6) {
				                    $result['errors'] .= "Skipping invalid row at line $row. \n";
				                    continue;
				                }

				                list($staff_no, $employee_id, $full_name, $tsDate, $attend_status, $time_in, $time_out,$ref, $ref_id, $ref_name) = array_map('escapeStr', $line);

				                // Check for missing required fields
				                if (!$full_name || !$employee_id || !$attend_status) {
				                    $result['errors'] .= "Missing required fields at line $row. \n";
				                    continue;
				                }

				                $tsDate = date('Y-m-d', strtotime($tsDate));

				                $time_in = date('H:i:s', strtotime($time_in));
				                $time_in = date('H:i:s', strtotime($time_in));


				                // Check or create timesheet record
				                $timesheetRecord = $GLOBALS['conn']->query("SELECT id FROM `timesheet` WHERE `ts_date` LIKE '$tsDate%' ");
				                if ($timesheetRecord->num_rows > 0) {
				                    $ts_id = $timesheetRecord->fetch_assoc()['id'];
				                } else {
				                    $data = [
				                        'ts_date' => $tsDate,
				                        'added_by' => $_SESSION['user_id']
				                    ];
				                    $ts_id = $timesheetClass->create($data);
				                }

				                // Validate timesheet ID
				                if (!$ts_id) {
				                    $result['errors'] .= "Failed to create or retrieve timesheet record for line $row. \n";
				                    continue;
				                }

				                // Get employees matching the reference
				                $get_employees = "SELECT * FROM `employees` WHERE `status` = 'active' AND `employee_id` = '$employee_id'";
				                if ($ref == 'Employee') {
				                    $get_employees .= " AND `employee_id` = '$ref_id'";
				                } elseif ($ref == 'Department') {
				                    $get_employees .= " AND `branch_id` = '$ref_id'";
				                } elseif ($ref == 'Location') {
				                    $get_employees .= " AND `location_id` = '$ref_id'";
				                }

				                $empSet = $GLOBALS['conn']->query($get_employees);
				                if ($empSet->num_rows > 0) {
				                    while ($empRow = $empSet->fetch_assoc()) {
				                        $employee_id = $empRow['employee_id'];

				                        // Check if timesheet detail already exists
				                        $detailCheck = $GLOBALS['conn']->query("SELECT id FROM `timesheet_details` WHERE `ts_id` = '$ts_id' AND `emp_id` = '$employee_id' AND `ts_date` = '$tsDate%'");
				                        if ($detailCheck->num_rows > 0) {
				                            continue; // Skip if already exists
				                        }

				                        /*$leaveType = '';
					    				$check_leave = $GLOBALS['conn']->query("SELECT * FROM `employee_leave` WHERE `emp_id` = '$employee_id' AND `status` <> 'Cancelled' AND '$tsDate' BETWEEN `date_from` AND `date_to`");
					    				if($check_leave->num_rows > 0) {
					    					while($leaveRow = $check_leave->fetch_assoc()) {
					    						$paid_type = $leaveRow['paid_type'];
					    						if($paid_type == 'Unpaid') {
					    							$leaveType = 'UL';
					    						} else {$leaveType = 'PL';}
					    					}
					    				}

					    				if($leaveType) $attend_status = $leaveType;*/

				                        $detailData = [
				                            'ts_id' => $ts_id,
				                            'emp_id' => $employee_id,
				                            'full_name' => $empRow['full_name'],
				                            'phone_number' => $empRow['phone_number'],
				                            'email' => $empRow['email'],
				                            'staff_no' => $empRow['staff_no'],
				                            'time_in' => $time_in,
				                            'time_out' => $time_out,
				                            'status' => $attend_status,
				                            'ts_date' => $tsDate,
				                            'added_by' => $_SESSION['user_id']
				                        ];

				                        // Create attendance detail
				                        $timesheetDetailsClass->create($detailData);
				                    }
				                } else {
				                    $GLOBALS['conn']->rollback();
				                    throw new Exception("No active employees found for reference at line $row.");
				                }
				            }

				            fclose($file);
				            $GLOBALS['conn']->commit();
				            $result['msg'] = "Timesheet uploaded successfully.";
				        } else {
				            throw new Exception("File read error.");
				        }
				    } else {
				        throw new Exception("Please select a file.");
				    }
				} catch (Exception $e) {
				    $GLOBALS['conn']->rollback();
				    $result['error'] = true;
				    $result['msg'] = $e->getMessage();
				    error_log($e->getMessage());
				}

				echo json_encode($result);
			}

			exit();
		} 


		// Update data
		else if($_GET['action'] == 'update') {
			$updated_date = date('Y-m-d H:i:s');
			if($_GET['endpoint'] == 'leave_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'paid_type' => isset($post['paid_type']) ? $post['paid_type']: "Active",
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "",
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('leave_types', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_leaves');

				    // Call the create method
				    $result['id'] = $leaveTypesClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Leave type info editted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'emp_leave') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
					$leave_typeInfo = get_data('leave_types', array('id' => $post['leave_id']))[0];
					$data = array(
					    'leave_id' => $post['leave_id'], 
					    'paid_type' => $leave_typeInfo['paid_type'], 
					    'date_from' => $post['date_from'], 
					    'date_to' => $post['date_to'], 
					    'status' => $post['status'], 
					    'days_num' => getDateDifference($post['date_from'], $post['date_to'])['totalDays'],
					    'updated_by' => $_SESSION['user_id'],
					    'updated_date' => $updated_date
					);

					check_auth('manage_leaves');
					if($post['status'] == 'Approved') {
						check_auth('approve_leaves');
					}

					// Call the create method
					$result['id'] = $employeeLeaveClass->update($post['id'], $data);

					// If the branch is editted successfully, return a success message
					if($result['id']) {
					    $result['msg'] = 'Employee leave info editted successfully';
					    $result['error'] = false;
					} else {
					    $result['msg'] = 'Something went wrong, please try again';
					    $result['error'] = true;
					}

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'attendance') {
				try {
				    // Prepare data from POST request
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    $data = array(
				        'atten_date' => $post['atten_date'], 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date 
				    );

				    check_auth('mark_attendance');

				    // Call the create method
				    $attendanceClass->update($post['id'], $data);
				    if($post['id']) {
				    	$atten_id = $post['id'];
				    	$atten_date = $post['atten_date'];
				    	$updated_by = $_SESSION['user_id'];
			    		foreach ($post['emp_id'] as $index => $value) {
			    			$emp_id = $post['emp_id'][$index];
			    			$attend_status = $post['attend_status'][$index];

			    			if($attend_status == 'removeEmp') {
			    				$del_emp = "DELETE FROM `atten_details` WHERE `atten_id` LIKE '$atten_id' AND `emp_id` LIKE '$emp_id'";
								if(!mysqli_query($GLOBALS["conn"], $del_emp)) {
									throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
								}
			    			}

						    $updateEmp = $conn->prepare("UPDATE `atten_details` SET `status`=?, `atten_date` = ?, `updated_by` = ?, `updated_date` = ? WHERE `atten_id` = ? AND `emp_id` = ?");
							$updateEmp->bind_param("ssssss", $attend_status, $atten_date, $updated_by, $updated_date, $atten_id, $emp_id);
							if(!$updateEmp->execute()) {
								throw new Exception('Error details: ' . $updateEmp->error);
							}
			    		}
				    	
				    } 

				    $GLOBALS['conn']->commit();

				    $result['msg'] = 'Attendance updated successfully';
				    $result['error'] = false;				   

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $GLOBALS['conn']->rollback();
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['msg'] .= ' '. $e->getMessage();;
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'timesheet') {
				try {
				    // Prepare data from POST request
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    $data = array(
				        'ts_date' => $post['ts_date'], 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date 
				    );

				    check_auth('manage_timesheets');

				    // Call the create method
				    $timesheetClass->update($post['id'], $data);
				    if($post['id']) {
				    	$ts_id = $post['id'];
				    	$ts_date = $post['ts_date'];
				    	$updated_by = $_SESSION['user_id'];
			    		foreach ($post['emp_id'] as $index => $value) {
			    			$emp_id = $post['emp_id'][$index];
			    			$delete_status = isset($post['delete_status'][$index]) ? $post['delete_status'][$index] : "";
			    			$ts_status = isset($post['ts_status'][$index]) ? $post['ts_status'][$index] : "";

			    			$time_in 	= $post['time_in'][$index];
			    			$time_out 	= $post['time_out'][$index];

			    			if($delete_status == 'removeEmp') {
			    				$del_emp = "DELETE FROM `timesheet_details` WHERE `ts_id` LIKE '$ts_id' AND `emp_id` LIKE '$emp_id'";
								if(!mysqli_query($GLOBALS["conn"], $del_emp)) {
									throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
								}

								// continue;
			    			}

			    			if($ts_status != 'P') {
			    				$time_in = "00:00:00";
			    				$time_out = "00:00:00";
			    			}

						    $updateEmp = $conn->prepare("UPDATE `timesheet_details` SET `status`=?, `ts_date` = ?, `time_in` =?, `time_out` = ?,  `updated_by` = ?, `updated_date` = ? WHERE `ts_id` = ? AND `emp_id` = ?");
							$updateEmp->bind_param("ssssssss", $ts_status, $ts_date, $time_in, $time_out, $updated_by, $updated_date, $ts_id, $emp_id);
							if(!$updateEmp->execute()) {
								throw new Exception('Error details: ' . $updateEmp->error);
							}

							continue;
			    		}
				    	
				    } 

				    $GLOBALS['conn']->commit();

				    $result['msg'] = 'Timesheet updated successfully';
				    $result['error'] = false;				   

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $GLOBALS['conn']->rollback();
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['msg'] .= ' '. $e->getMessage();;
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			}
		}



		// Load data
		else if($_GET['action'] == 'load') {
			$role = '';
			$status = '';
			$length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
			$searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
			$orderBy = ''; // Default sorting
			$order = 'ASC';
			$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
			$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;

			if (isset($_POST['role'])) $role = $_POST['role'];
			if (isset($_POST['status'])) $status = $_POST['status'];

			$result = [
			    'status' => 201,
			    'error' => false,
			    'data' => [],
			    'draw' => $draw,
			    'iTotalRecords' => 0,
			    'iTotalDisplayRecords' => 0,
			    'msg' => ''
			];

			if ($_GET['endpoint'] === 'leave_types') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name', 'paid_type'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `leave_types` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `paid_type` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $leave_types = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `leave_types` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `paid_type` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($leave_types->num_rows > 0) {
			        while ($row = $leave_types->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $leave_types->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'emp_leaves') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['full_name', 'name', 'date_from', 'days_num'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT `full_name`, `name`, L.`paid_type`, L.`status`, `date_from`, `date_to`, `days_num`, `staff_no`, L.`id` FROM `employee_leave` L  INNER JOIN `employees` E ON L.`emp_id` = E.`employee_id` INNER JOIN `leave_types` LT ON LT.`id` = L.`leave_id` WHERE L.`id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $employee_leave = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `employee_leave` L  INNER JOIN `employees` E ON L.`emp_id` = E.`employee_id` INNER JOIN `leave_types` LT ON LT.`id` = L.`leave_id` WHERE L.`id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($employee_leave->num_rows > 0) {
			        while ($row = $employee_leave->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $employee_leave->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'attendance') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['ref', 'atten_date', '', 'added_date'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `attendance` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`ref` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_name` LIKE '%" . escapeStr($searchParam) . "%' OR `added_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $attendance = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `attendance` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`ref` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_name` LIKE '%" . escapeStr($searchParam) . "%' OR `added_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($attendance->num_rows > 0) {
			        while ($row = $attendance->fetch_assoc()) {
			        	$id = $row['id'];
			        	$employee_count = 0;

			        	$query = "SELECT COUNT(emp_id) AS employee_count FROM `atten_details` WHERE `atten_id` = ?";
				        $stmt = $GLOBALS['conn']->prepare($query);
				        $stmt->bind_param("i", $id);
				        $stmt->execute();
				        $countResult = $stmt->get_result();

				        if ($countRow = $countResult->fetch_assoc()) {
				            $employee_count = $countRow['employee_count'];
				        }

			        	$row['employee_count'] = $employee_count;
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $attendance->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'timesheet') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['ts_date', '', 'added_date'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `timesheet` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`ts_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $timesheet = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `timesheet` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`ts_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($timesheet->num_rows > 0) {
			        while ($row = $timesheet->fetch_assoc()) {
			        	$id = $row['id'];
			        	$employee_count = 0;

			        	$query = "SELECT COUNT(emp_id) AS employee_count FROM `timesheet_details` WHERE `ts_id` = ?";
				        $stmt = $GLOBALS['conn']->prepare($query);
				        $stmt->bind_param("i", $id);
				        $stmt->execute();
				        $countResult = $stmt->get_result();

				        if ($countRow = $countResult->fetch_assoc()) {
				            $employee_count = $countRow['employee_count'];
				        }

			        	$row['employee_count'] = $employee_count;
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $timesheet->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			}

			echo json_encode($result);

			exit();

		} 


		// Get data
		else if($_GET['action'] == 'get') {
			if ($_GET['endpoint'] === 'leave_type') {
				json(get_data('leave_types', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'emp_leave') {
				$leaveInfo = get_data('employee_leave', array('id' => $_POST['id']))[0];
				$leaveInfo['full_name'] = get_data('employees', array('employee_id' => $leaveInfo['emp_id']))[0]['full_name'];
				$leaveInfo['date_from'] = date('Y-m-d', strtotime($leaveInfo['date_from']));
				$leaveInfo['date_to'] = date('Y-m-d', strtotime($leaveInfo['date_to']));
				json($leaveInfo);
			} else if ($_GET['endpoint'] === '4editAttendance') {
				$result = [];
				$atten_id = $_POST['id'];
				$attendanceInfo = get_data('attendance', array('id' => $atten_id))[0];
				$result = $attendanceInfo;
				$result['atten_date'] = date('Y-m-d', strtotime($attendanceInfo['atten_date']));

				// Get details
				$employees = '';

				$get_details = "SELECT * FROM `atten_details` WHERE `atten_id` = '$atten_id'";
				$detailsSet = $GLOBALS['conn']->query($get_details);
				while($row = $detailsSet->fetch_assoc()) {
					$id 	= $row['id'];
					$emp_id 	= $row['emp_id'];
					$full_name 	= $row['full_name'];
					$staff_no 	= $row['staff_no'];
					$status 	= $row['status'];

					$PChecked  = $PLChecked = $SChecked = $ULChecked = $HChecked = $NHChecked = $NChecked = '';
					if($status == 'P') $PChecked = 'checked=""';
					if($status == 'PL') $PLChecked = 'checked=""';
					if($status == 'UL') $ULChecked = 'checked=""';
					if($status == 'S') $SChecked = 'checked=""';
					if($status == 'H') $HChecked = 'checked=""';
					if($status == 'NH') $NHChecked = 'checked=""';
					if($status == 'N') $NChecked = 'checked=""';

					$employees .= '<tr>
				        <td style="vertical-align: middle;">' . $staff_no . '</td>
				        <td style="vertical-align: middle;">' . $full_name . '</td>
				        <td style="width: 40%; vertical-align: middle;">
				            <div class="sflex sspace-bw scenter-items">
				            	<input type="hidden" class="employee_id" value="'.$emp_id.'" />
				                <input type="radio" class="btn-check statusBTN" name="statusBTN' . $emp_id . '" ' . $PChecked . ' id="P' . $emp_id . '" value="P" autocomplete="off">
				                <label class="btn swidth-40 statusBTNLabel  btn-outline-primary" for="P' . $emp_id . '">P</label>

				                <input type="radio" class="btn-check statusBTN" name="statusBTN' . $emp_id . '" ' . $PLChecked . ' id="PL' . $emp_id . '" value="PL" autocomplete="off">
				                <label class="btn swidth-40 statusBTNLabel  btn-outline-success" for="PL' . $emp_id . '">PL</label>

				                <input type="radio" class="btn-check statusBTN" name="statusBTN' . $emp_id . '" ' . $SChecked . ' id="S' . $emp_id . '" value="S" autocomplete="off">
				                <label class="btn swidth-40 statusBTNLabel  btn-outline-secondary" for="S' . $emp_id . '">S</label>

				                <input type="radio" class="btn-check statusBTN" name="statusBTN' . $emp_id . '" ' . $ULChecked . ' id="UL' . $emp_id . '" value="UL" autocomplete="off">
				                <label class="btn swidth-40 statusBTNLabel  btn-outline-warning" for="UL' . $emp_id . '">UL</label>

				                <input type="radio" class="btn-check statusBTN" name="statusBTN' . $emp_id . '" ' . $HChecked . ' id="H' . $emp_id . '" value="H" autocomplete="off">
				                <label class="btn swidth-40 statusBTNLabel  btn-outline-primary" for="H' . $emp_id . '">H</label>

				                <input type="radio" class="btn-check statusBTN" name="statusBTN' . $emp_id . '" ' . $NHChecked . ' id="NH' . $emp_id . '" value="NH" autocomplete="off">
				                <label class="btn swidth-40 statusBTNLabel  btn-outline-secondary" for="NH' . $emp_id . '">NH</label>

				                <input type="radio" class="btn-check statusBTN" name="statusBTN' . $emp_id . '" ' . $NChecked . ' id="N' . $emp_id . '" value="N" autocomplete="off">
				                <label class="btn swidth-40 statusBTNLabel  btn-outline-danger" for="N' . $emp_id . '">N</label>

				                <input type="radio" class="btn-check removeEmp statusBTN" name="statusBTN' . $emp_id . '" id="removeEmp' . $emp_id . '" value="removeEmp" autocomplete="off">
				                <label title="Remove employee" class="btn removeEmp swidth-40 statusBTNLabel btn-outline-danger" for="removeEmp' . $emp_id . '"><span class="fa fa-trash"></span></label>
				            </div>
				        </td>
				    </tr>';
				}

				$result['employees'] = $employees;

				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'downloadAttendanceCSV') {
				$post = escapePostData($_POST);
				$ref_id = $post['ref_id'];
	    		$get_employees = "SELECT * FROM `employees` WHERE `status` = 'active'";
	    		if($post['ref'] == 'Department') {
	    			$get_employees .= " AND `branch_id` = '$ref_id'";
	    		} else if($post['ref'] == 'Location') {
	    			$get_employees .= " AND `location_id` = '$ref_id'";
	    		}

	    		$result = [];
				$result['data'] = []; // Initialize as an empty array for storing rows

				// Add header row as the first entry
				$result['data'][] = ['Staff No.', 'Employee ID', 'Full name', 'Date', 'Status', 'Reference', 'Reference #', 'Reference Name'];

				$empSet = $GLOBALS['conn']->query($get_employees);
				if ($empSet->num_rows > 0) {
				    while ($row = $empSet->fetch_assoc()) {
				        $employee_id = $row['employee_id'];
				        $full_name = $row['full_name'];
				        $phone_number = $row['phone_number'];
				        $email = $row['email'];
				        $staff_no = $row['staff_no'];

				        $atten_date = $post['atten_date'];
				        $attend_status = ''; // This seems to be a placeholder
				        $ref_id = $post['ref_id'];
				        $ref = $post['ref'];
				        $ref_name = $post['ref_name'];

				        $attenInfo = get_data('atten_details', array('emp_id' => $employee_id, 'atten_date' => $atten_date));
				        if($attenInfo) {
				        	$attenInfo = $attenInfo[0];
				        	$attend_status = $attenInfo['status'];
				        }

				        $leaveType = '';
	    				$check_leave = $GLOBALS['conn']->query("SELECT * FROM `employee_leave` WHERE `emp_id` = '$employee_id' AND `status` <> 'Cancelled' AND '$atten_date' BETWEEN `date_from` AND `date_to`");
	    				if($check_leave->num_rows > 0) {
	    					while($leaveRow = $check_leave->fetch_assoc()) {
	    						$paid_type = $leaveRow['paid_type'];
	    						if($paid_type == 'Unpaid') {
	    							$leaveType = 'UL';
	    						} else {$leaveType = 'PL';}
	    					}
	    				}

	    				if($leaveType) $attend_status = $leaveType;

				        // Prepare the data for the current employee
				        $data = [$staff_no, $employee_id, $full_name, $atten_date, $attend_status, $ref, $ref_id, $ref_name];

				        // Append the employee data as a row to the result
				        $result['data'][] = $data;
				    }

				    // Add success response
				    $result['error'] = false;
				    $result['msg'] = 'File downloaded successfully.';
				} else {
				    // Add error response if no employees found
				    $result['error'] = true;
				    $result['msg'] = 'No employees found.';
				}

	    		echo json_encode($result);
			} else if ($_GET['endpoint'] === '4editTimesheet') {
				$result = [];
				$ts_id = $_POST['id'];
				$tsInfo = get_data('timesheet', array('id' => $ts_id))[0];
				$result = $tsInfo;
				$result['ts_date'] = date('Y-m-d', strtotime($tsInfo['ts_date']));

				// Get details
				$employees = '';

				$get_details = "SELECT * FROM `timesheet_details` WHERE `ts_id` = '$ts_id'";
				$detailsSet = $GLOBALS['conn']->query($get_details);
				while($row = $detailsSet->fetch_assoc()) {
					$id 	= $row['id'];
					$emp_id 	= $row['emp_id'];
					$full_name 	= $row['full_name'];
					$staff_no 	= $row['staff_no'];
					$status 	= $row['status'];

					$time_in 	= $row['time_in'];
					$time_out 	= $row['time_out'];


					$PChecked  = $PLChecked = $SChecked = $ULChecked = $HChecked = $NHChecked = $NChecked = '';
					if($status == 'P') $PChecked 	= 'selected=""';
					if($status == 'PL') $PLChecked 	= 'selected=""';
					if($status == 'UL') $ULChecked 	= 'selected=""';
					if($status == 'S') $SChecked 	= 'selected=""';
					if($status == 'H') $HChecked 	= 'selected=""';
					if($status == 'NH') $NHChecked 	= 'selected=""';
					if($status == 'N') $NChecked 	= 'selected=""';

					$employees .= '<tr>
                        <td style="vertical-align: middle;">'.$staff_no.'</td>
                        <td style="vertical-align: middle;">'.$full_name.'</td>
                        <td style="width:150px; vertical-align: middle;">
                        	<input type="hidden" class="employee_id" id="employee_id" value="'.$emp_id.'" />
                        	<input type="time" class="form-control timeIn" value="'.date("H:i:s", strtotime($time_in)).'" style="-width: 100%;" name="">
                        </td>
                        <td style="width:150px;">
                        	<input type="time" class="form-control timeOut" value="'.date("H:i:s", strtotime($time_out)).'" style="-width:  100%;" name="">
                        </td>
                        <td class="cursor info-tooltip" style="width:20%;">
                            <div class="sflex scenter-items">
                            	<select class="form-control smr-10 slcTsStatus" id="slcTsStatus">
                                	<option '.$PChecked.' value="P">Present</option>
                                	<option '.$SChecked.' value="S">Sick</option>
                                    <option '.$PLChecked.' value="PL">Paid Leave</option>
                                    <option '.$ULChecked.' value="UL">Unpaid Leave</option>
                                	<option '.$HChecked.' value="H">Holiday</option>
                                	<option '.$NHChecked.' value="NH">Not hired day</option>
                                	<option '.$NChecked.' value="N">Absent</option>
                            	</select>
				                <input type="radio" class="btn-check removeEmp statusBTN" name="statusBTN' . $emp_id . '" id="removeEmp' . $emp_id . '" value="removeEmp" autocomplete="off">
				                <label title="Remove employee" class="btn removeEmp swidth-40 statusBTNLabel btn-outline-danger" for="removeEmp' . $emp_id . '"><span class="fa fa-trash"></span></label>
                            </div>
                        </td>
                    </tr>';
				}

				$result['employees'] = $employees;

				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'downloadTimesheetCSV') {
				$post = escapePostData($_POST);
				$ref_id = $post['ref_id'];
	    		$get_employees = "SELECT * FROM `employees` WHERE `status` = 'active'";
	    		if($post['ref'] == 'Department') {
	    			$get_employees .= " AND `branch_id` = '$ref_id'";
	    		} else if($post['ref'] == 'Location') {
	    			$get_employees .= " AND `location_id` = '$ref_id'";
	    		}

	    		$result = [];
				$result['data'] = []; // Initialize as an empty array for storing rows
				// Add header row as the first entry
				$result['data'][] = ['Staff No.', 'Employee ID', 'Full name', 'Date', 'Status', 'Time in', 'Time out', 'Reference', 'Reference #', 'Reference Name' ];

				$empSet = $GLOBALS['conn']->query($get_employees);
				if ($empSet->num_rows > 0) {
				    while ($row = $empSet->fetch_assoc()) {
				        $employee_id = $row['employee_id'];
				        $full_name = $row['full_name'];
				        $phone_number = $row['phone_number'];
				        $email = $row['email'];
				        $staff_no = $row['staff_no'];

				        $ts_date = $post['ts_date'];
				        $attend_status = 'P'; // This seems to be a placeholder
				        $ref_id = $post['ref_id'];
				        $ref = $post['ref'];
				        $ref_name = $post['ref_name'];

				        $time_in = '';
				        $time_out = '';

				        $tsInfo = get_data('timesheet_details', array('emp_id' => $employee_id, 'ts_date' => $ts_date));
				        if($tsInfo) {
				        	$tsInfo = $tsInfo[0];
				        	$attend_status = $tsInfo['status'];
				        	$time_in = $tsInfo['time_in'];
				        	$time_out = $tsInfo['time_out'];
				        }

				        $leaveType = '';
	    				$check_leave = $GLOBALS['conn']->query("SELECT * FROM `employee_leave` WHERE `emp_id` = '$employee_id' AND `status` <> 'Cancelled' AND '$ts_date' BETWEEN `date_from` AND `date_to`");
	    				if($check_leave->num_rows > 0) {
	    					while($leaveRow = $check_leave->fetch_assoc()) {
	    						$paid_type = $leaveRow['paid_type'];
	    						if($paid_type == 'Unpaid') {
	    							$leaveType = 'UL';
	    						} else {$leaveType = 'PL';}
	    					}
	    				}

	    				if($leaveType) $attend_status = $leaveType;

				        // Prepare the data for the current employee
				        $data = [$staff_no, $employee_id, $full_name, $ts_date, $attend_status, $time_in, $time_out, $ref, $ref_id, $ref_name];

				        // Append the employee data as a row to the result
				        $result['data'][] = $data;
				    }

				    // Add success response
				    $result['error'] = false;
				    $result['msg'] = 'File downloaded successfully.';
				} else {
				    // Add error response if no employees found
				    $result['error'] = true;
				    $result['msg'] = 'No employees found.';
				}

	    		echo json_encode($result);
			}
 
			exit();
		}

		// Search data
		else if($_GET['action'] == 'search') {
			if ($_GET['endpoint'] === 'employee4Select') {
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';
				$search = isset($_POST['search']) ? $_POST['search'] : '';

				$options = '';
				$response = [];
				$response['error'] = true;
				if($search) {
					$query = "SELECT * FROM `employees` WHERE `status` = 'active' AND (`full_name` LIKE '$search%' OR `phone_number` LIKE '$search%' OR `email` LIKE '$search%') ORDER BY `full_name` ASC LIMIT 10";
                    $empSet = $GLOBALS['conn']->query($query);
                    if($empSet->num_rows > 0) {
                    	while($row = $empSet->fetch_assoc()) {
                    		$employee_id = $row['employee_id'];
                    		$full_name = $row['full_name'];
                    		$phone_number = $row['phone_number'];

                    		$options .=  '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                    		$response['error'] = false;
                    	}
                    } 
				} else {
					$query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                    $empSet = $GLOBALS['conn']->query($query);
                    if($empSet->num_rows > 0) {
                    	while($row = $empSet->fetch_assoc()) {
                    		$employee_id = $row['employee_id'];
                    		$full_name = $row['full_name'];
                    		$phone_number = $row['phone_number'];

                    		$options .=  '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                    	}
                    } 
				}

				$response['options'] = $options;
				echo json_encode($response); exit();
			} else if ($_GET['endpoint'] === 'department4Select') {
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';
				$search = isset($_POST['search']) ? $_POST['search'] : '';

				$options = '';
				$response = [];
				$response['error'] = true;
				if($search) {
					$query = "SELECT * FROM `branches` WHERE `status` = 'active' AND (`name` LIKE '$search%' ) ORDER BY `name` ASC LIMIT 10";
                    $branchSet = $GLOBALS['conn']->query($query);
                    if($branchSet->num_rows > 0) {
                    	while($row = $branchSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    		$response['error'] = false;
                    	}
                    } 
				} else {
					$query = "SELECT * FROM `branches` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
                    $branchSet = $GLOBALS['conn']->query($query);
                    if($branchSet->num_rows > 0) {
                    	while($row = $branchSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    	}
                    } 
				}

				$response['options'] = $options;
				echo json_encode($response); exit();
			} else if ($_GET['endpoint'] === 'location4Select') {
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';
				$search = isset($_POST['search']) ? $_POST['search'] : '';

				$options = '';
				$response = [];
				$response['error'] = true;
				if($search) {
					$query = "SELECT * FROM `locations` WHERE `status` = 'active' AND (`name` LIKE '$search%' ) ORDER BY `name` ASC LIMIT 10";
                    $locationSet = $GLOBALS['conn']->query($query);
                    if($locationSet->num_rows > 0) {
                    	while($row = $locationSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    		$response['error'] = false;
                    	}
                    } 
				} else {
					$query = "SELECT * FROM `locations` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
                    $locationSet = $GLOBALS['conn']->query($query);
                    if($locationSet->num_rows > 0) {
                    	while($row = $locationSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    	}
                    } 
				}

				$response['options'] = $options;
				echo json_encode($response); exit();
			} else if ($_GET['endpoint'] === 'atten_for') {
				$data = '';
				if($_POST['attenFor'] == 'Employee') {
					$data = '<label class="label required" for="searchEmployee">Employee</label>
                        <select class="my-select searchEmployee" name="searchEmployee" id="searchEmployee" data-live-search="true" title="Search and select employee">';
                        $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                        $empSet = $GLOBALS['conn']->query($query);
                        if($empSet->num_rows > 0) {
                        	while($row = $empSet->fetch_assoc()) {
                        		$employee_id = $row['employee_id'];
                        		$full_name = $row['full_name'];
                        		$phone_number = $row['phone_number'];

                        		$data .= '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                        	}
                        } 
                        
			       $data .= '</select>';
				} else if($_POST['attenFor'] == 'Department') {
					$data = '<label class="label required" for="searchDepartment">Department</label>
                        <select class="my-select searchDepartment" name="searchDepartment" id="searchDepartment" data-live-search="true" title="Search and select employee">';
                        $query = "SELECT * FROM `branches` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
                        $branchSet = $GLOBALS['conn']->query($query);
                        if($branchSet->num_rows > 0) {
                        	while($row = $branchSet->fetch_assoc()) {
                        		$id = $row['id'];
                        		$name = $row['name'];

                        		$data .= '<option value="'.$id.'">'.$name.'</option>';
                        	}
                        } 
                        
			       $data .= '</select>';
				} else if($_POST['attenFor'] == 'Location') {
					$data = '<label class="label required" for="searchLocation">Location</label>
                        <select class="my-select searchLocation" name="searchLocation" id="searchLocation" data-live-search="true" title="Search and select employee">';
                        $query = "SELECT * FROM `locations` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
                        $locationSet = $GLOBALS['conn']->query($query);
                        if($locationSet->num_rows > 0) {
                        	while($row = $locationSet->fetch_assoc()) {
                        		$id = $row['id'];
                        		$name = $row['name'];

                        		$data .= '<option value="'.$id.'">'.$name.'</option>';
                        	}
                        } 
                        
			       $data .= '</select>';
				}

				if($data) echo json_encode(['data' => $data, 'error' => false]); exit();
				echo json_encode(['error' => true, 'msg' => 'Do data, something went wrong.']);
			}

			exit();
		}


		// Delete data
		else if($_GET['action'] == 'delete') {
			if ($_GET['endpoint'] === 'leave_type') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_leaves');
				    checkForeignKey($post['id'], 'leave_id', ['employee_leave']);
				    $deleted = $leaveTypesClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Leave type has been  deleted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'emp_leave') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_leaves');
				    $leaveInfo = get_data('employee_leave', array('id' => $post['id']))[0];

				    if($leaveInfo['status'] != 'Request' && $leaveInfo['status'] != 'Cancelled') {
				    	$result['msg'] = 'Cannot delete leave that is processed.';
				        $result['error'] = true;
				    	echo json_encode($result); exit();
				    }

				    $deleted = $employeeLeaveClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Employee leave record has been  deleted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'attendance') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    $atten_id = $post['id'];
				    check_auth('mark_attendance');

				    $del_emp = "DELETE FROM `atten_details` WHERE `atten_id` LIKE '$atten_id'";
					if(!mysqli_query($GLOBALS["conn"], $del_emp)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}

				    $deleted = $attendanceClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Attendance record has been  deleted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'timesheet') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    $ts_id = $post['id'];
				    check_auth('manage_timesheets');

				    $del_emp = "DELETE FROM `timesheet_details` WHERE `ts_id` LIKE '$ts_id'";
					if(!mysqli_query($GLOBALS["conn"], $del_emp)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}

				    $deleted = $timesheetClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Timesheet record has been  deleted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			}

			exit();
		}
	}
}

?>