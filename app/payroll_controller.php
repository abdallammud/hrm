<?php
require('init.php');
use App\myEmail;

$mailer = new myEmail();
if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'save') {
			if($_GET['endpoint'] == 'transaction') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $emp_id = $post['emp_id'];
				    $employeeInfo = get_data('employees', ['employee_id' => $emp_id]);
				    if(isset($employeeInfo)) {
				    	$employeeInfo = $employeeInfo[0];
				    	$full_name = $employeeInfo['full_name'];
				    	$staff_no = $employeeInfo['staff_no'];
				    	$phone_number = $employeeInfo['phone_number'];
				    	$email = $employeeInfo['email'];
				    	
				    	$data = array(
					        'emp_id' => $emp_id, 
					        'full_name' => $full_name, 
					        'phone_number' => $phone_number, 
					        'email' => $email, 
					        'staff_no' => $staff_no, 
					        'transaction_type' => $post['transaction_type'], 
					        'transaction_subtype' => $post['transaction_subtype'],
					        'amount' => $post['amount'], 
					        'date' => $post['date'], 
					        'description' => $post['description'], 
					        'status' => $post['status'], 
					        'added_by' => $_SESSION['user_id']
					    );

					    check_exists('employee_transactions', ['emp_id' => $post['emp_id'], 'transaction_type' => $post['transaction_type'], 'date' => $post['date']]);
				    	check_auth('create_payroll_transactions');

				    	if($post['status'] == 'Approved') {
				    		check_auth('approve_employee_transactions');
				    	}

				    	$result['id'] = $employeeTransactionsClass->create($data);

					    // If the branch is created successfully, return a success message
					    if($result['id']) {
					        $result['msg'] = 'Employee transaction created successfully';
					        $result['error'] = false;
					    } else {
					        $result['msg'] = 'Something went wrong, please try again';
					        $result['error'] = true;
					    }
				    } else {
				    	$result['msg'] = 'No employees found';
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
			} else if($_GET['endpoint'] == 'upload_transaction') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();

				    $result = ['error' => false, 'msg' => '', 'errors' => ''];

				    check_auth('create_payroll_transactions'); // Authorization check

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

				                list($staff_no, $employee_id, $full_name, $transDate, $transaction_type, $transaction_subtype, $amount, $status, $comments) = array_map('escapeStr', $line);

				                // Check for missing required fields
				                if (!$full_name || !$employee_id || !$amount || !$transaction_type) {
				                    $result['errors'] .= "Missing required fields at line $row. \n";
				                    continue;
				                }

				                $transDate = date('Y-m-d', strtotime($transDate));

				                // Get employees matching the reference
				                $get_employees = "SELECT * FROM `employees` WHERE `status` = 'active' AND `employee_id` = '$employee_id'";
				                $empSet = $GLOBALS['conn']->query($get_employees);
				                if ($empSet->num_rows > 0) {
				                    while ($empRow = $empSet->fetch_assoc()) {
				                        $employee_id = $empRow['employee_id'];
				                        $full_name = $empRow['full_name'];
				                        $phone_number = $empRow['phone_number'];
				                        $email = $empRow['email'];
				                        $staff_no = $empRow['staff_no'];

				                      	$data = array(
									        'emp_id' => $employee_id, 
									        'full_name' => $full_name, 
									        'phone_number' => $phone_number, 
									        'email' => $email, 
									        'staff_no' => $staff_no, 
									        'transaction_type' => $transaction_type, 
									        'transaction_subtype' => $transaction_subtype,
									        'amount' => $amount, 
									        'date' => $transDate, 
									        'description' => $comments, 
									        'status' => $status, 
									        'added_by' => $_SESSION['user_id']
									    );

									    $check_exists = $GLOBALS['conn']->query("SELECT transaction_id FROM `employee_transactions` WHERE `date` LIKE '$transDate%' AND `emp_id` = '$employee_id' AND `transaction_type` = '$transaction_type'");
				                        if ($check_exists->num_rows > 0) {
				                            continue; // Skip if already exists
				                        }

								    	if($status == 'Approved') {
								    		check_auth('approve_employee_transactions');
								    	}

								    	$result['id'] = $employeeTransactionsClass->create($data);
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
			} else if($_GET['endpoint'] == 'payroll') {
				// Payroll generate
				try {
					// Prepare data from POST request
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);

				    if($post['ref'] == 'All') {
				    	$post['ref_id'] = '';
				    	$post['ref_name'] = 'All employees';
				    }
					$userInfo = $userClass->read($_SESSION['user_id']);
					$user_fullName = $userInfo['full_name'];
					// Initialize workflow as an array with the first action
				    $workflow = [
				        [
				            'action' => 'Created by '. $user_fullName, 
				            'date' => date('Y-m-d H:i:s'),
				            'status' => 'Created',
							'user_id' => $_SESSION['user_id']
				        ]
				    ];

				    $month = date('Y-m');
				    $data = array(
				        'ref' 		=> $post['ref'], 
				        'ref_id' 	=> $post['ref_id'], 
				        'month' 	=> $month,
				        'ref_name' 	=> $post['ref_name'], 
						'workflow' 	=> json_encode($workflow),
						'added_by' 	=> $_SESSION['user_id']
				    );

					// var_dump($data); exit;

				    check_auth('create_payroll');
				    $payroll_id = $payrollClass->create($data);

				    if($payroll_id) {
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

				    		$months = '';
				    		if($empSet->num_rows > 0) {
				    			while ($row = $empSet->fetch_assoc()) {
								    $employeeId 	= $row['employee_id'];
								    $fullName 		= $row['full_name'];
								    $phoneNumber 	= $row['phone_number'];
								    $email 			= $row['email'];
								    $staffNo 		= $row['staff_no'];
								    $contractType 	= $row['contract_type'];
								    $paymentBank 	= $row['payment_bank'];
								    $paymentAccount = $row['payment_account'];
								    $state_id 		= $row['state_id'];
								    $workDays 	= $row['work_days'];
								    $workHours 	= $row['work_hours'];
								    $position 	= $row['position'];
								    $salary 	= $row['salary'];

								    

								    foreach ($post['month'] as $month) {
								    	$month = date('Y-m', strtotime($month));
									    // Attendance information
									    $attendanceInfo = calculateAttendanceStats($employeeId, $month);

									    $months .= $month .", ";
									    // Calculate required work days in the month
									    $requiredDays = getWorkdaysInMonth($month, $workDays);
									    $requiredDays -= $attendanceInfo['not_hired_days'] - $attendanceInfo['holidays'];

									    if ($requiredDays <= 0) {
									        continue; // Skip to the next employee if no required days
									    }

									    // Calculate salary per day and per hour
									    $salaryPerDay = $salary / max($requiredDays, 1); // Avoid division by zero
									    $salaryPerHour = $salaryPerDay / max($workHours, 1);

									    // Calculate over and under hours if overtime is enabled
									    $extraHours = $underHours = 0;
									    if (return_setting('overtime') === 'Yes') {
									        $timeSheetInfo = calculateTimeSheetHours($employeeId, $month, $workHours);
									        $netHours = $timeSheetInfo['net_hours'];

									        if ($netHours > 0) {
									            $extraHours = $netHours * $salaryPerHour;
									        } elseif ($netHours < 0) {
									            $underHours = abs($netHours) * $salaryPerHour;
									        }
									    }

									    // Calculate earnings
									    $earnings = calculateEmployeeEarnings($employeeId, $month);
									    $allowance = $earnings['allowance'] ?? 0;
									    $bonus = $earnings['bonus'] ?? 0;
									    $commission = $earnings['commission'] ?? 0;

									    // Calculate deductions
									    $deductions = calculateEmployeeDeductions($employeeId, $month);
									    $loan = $deductions['loan'] ?? 0;
									    $advance = $deductions['advance'] ?? 0;
									    $deduction = $deductions['deduction'] ?? 0;

									    // Calculate unpaid days and effective days worked
									    $unpaidDaysCost = ($attendanceInfo['unpaid_leave_days'] + $attendanceInfo['no_show_days']) * $salaryPerDay;
									    $daysWorked = $requiredDays - $attendanceInfo['unpaid_leave_days'] - $attendanceInfo['no_show_days']- $attendanceInfo['paid_leave_days'] - $attendanceInfo['sick_days'];

									    // Calculate tax and then net salary
									    $total_earnings = $salary + $allowance + $bonus + $commission + $extraHours - $loan - $advance - $deduction - $underHours - $unpaidDaysCost;

									    // Get state tax
									    $taxRate = getTaxRate($total_earnings, $state_id);
									    $total_earnings -= $taxRate;

									    // Insert to details table
									    $detailsData = [
									    	'payroll_id' => $payroll_id,
									    	'emp_id' => $employeeId,
									    	'full_name' => $fullName,
									    	'staff_no' => $staffNo,
									    	'email' => $email, 
									    	'contract_type' => $contractType,
									    	'job_title' => $position,
									    	'month' => $month,
									    	'required_days' => $requiredDays,
									    	'days_worked' => $daysWorked,
									    	'base_salary' => $salary,
									    	'allowance' => $allowance,
									    	'bonus' => $bonus,
									    	'extra_hours' => $extraHours,
									    	'commission' => $commission,
									    	'tax' => $taxRate,
									    	'advance' => $advance,
									    	'loan' => $loan,
									    	'deductions' => $deduction,
									    	'unpaid_days' => $unpaidDaysCost,
									    	'unpaid_hours' => $underHours,
									    	'bank_name' => $paymentBank,
									    	'bank_number' => $paymentAccount,
									    	'added_by' => $_SESSION['user_id']
									    ];

									    $payrollDetailsClass->create($detailsData);
									}

								}

								$months = rtrim($months, ", "); 
								$array = explode(", ", $months);
								$unique_months = array_unique($array);
								$months = implode(", ", $unique_months);
								$months = rtrim($months, ", "); 
								$updatemonths = array(
							        'month' => $months, 
							    );

								$payrollClass->update($payroll_id, $updatemonths);

				    		} else {
				    			throw new Exception("No employees were found.");
				    		}
				    	}
				    }

				    $GLOBALS['conn']->commit();

				    // If the payroll is created successfully, return a success message
				    if($payroll_id) {
				        $result['msg'] = 'Payroll recorded successfully';
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
			} else if($_GET['endpoint'] == 'add_employees_to_payroll') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					$payroll_id = (int)$post['payroll_id'];
					$employee_ids = isset($post['employee_ids']) ? $post['employee_ids'] : [];
			
					if (empty($payroll_id) || empty($employee_ids)) {
						throw new Exception("Payroll ID and Employee IDs are required.");
					}
			
					// Fetch existing payroll
					$payrollInfo = $payrollClass->read($payroll_id);
					if (!$payrollInfo) {
						throw new Exception("Payroll not found.");
					}

					$status = $payrollInfo['status'];
			
					// Get the comma-separated months string and convert to array
					$months_str = $payrollInfo['month'];
					$months_arr = array_map('trim', explode(',', $months_str));
			
					foreach ($employee_ids as $employeeId) {
						$employeeId = (int)$employeeId;
						$empSet = get_data('employees', ['employee_id' => $employeeId]);
			
						if (!$empSet || count($empSet) === 0) {
							continue; // Skip if employee not found
						}
			
						$row = $empSet[0];
						$fullName       = $row['full_name'];
						$staffNo        = $row['staff_no'];
						$email          = $row['email'];
						$contractType   = $row['contract_type'];
						$position       = $row['position'];
						$paymentBank    = $row['payment_bank'];
						$paymentAccount = $row['payment_account'];
						$salary         = $row['salary'];
						$state_id       = $row['state_id'];
						$workDays       = $row['work_days'];
						$workHours      = $row['work_hours'];
			
						foreach ($months_arr as $month) {
							$month = date('Y-m', strtotime($month));
			
							// Attendance information
							$attendanceInfo = calculateAttendanceStats($employeeId, $month);
			
							// Required work days
							$requiredDays = getWorkdaysInMonth($month, $workDays);
							$requiredDays -= $attendanceInfo['not_hired_days'] - $attendanceInfo['holidays'];
			
							if ($requiredDays <= 0) {
								continue; // Skip if no required days
							}
			
							// Per-day and per-hour salary
							$salaryPerDay = $salary / max($requiredDays, 1);
							$salaryPerHour = $salaryPerDay / max($workHours, 1);
			
							// Extra and under hours
							$extraHours = $underHours = 0;
							if (return_setting('overtime') === 'Yes') {
								$timeSheetInfo = calculateTimeSheetHours($employeeId, $month, $workHours);
								$netHours = $timeSheetInfo['net_hours'];
			
								if ($netHours > 0) {
									$extraHours = $netHours * $salaryPerHour;
								} elseif ($netHours < 0) {
									$underHours = abs($netHours) * $salaryPerHour;
								}
							}
			
							// Earnings
							$earnings = calculateEmployeeEarnings($employeeId, $month);
							$allowance = $earnings['allowance'] ?? 0;
							$bonus = $earnings['bonus'] ?? 0;
							$commission = $earnings['commission'] ?? 0;
			
							// Deductions
							$deductions = calculateEmployeeDeductions($employeeId, $month);
							$loan = $deductions['loan'] ?? 0;
							$advance = $deductions['advance'] ?? 0;
							$deduction = $deductions['deduction'] ?? 0;
			
							// Unpaid leave/no-show deductions
							$unpaidDaysCost = ($attendanceInfo['unpaid_leave_days'] + $attendanceInfo['no_show_days']) * $salaryPerDay;
			
							$daysWorked = $requiredDays - $attendanceInfo['unpaid_leave_days'] - $attendanceInfo['no_show_days'] - $attendanceInfo['paid_leave_days'] - $attendanceInfo['sick_days'];
			
							// Net salary calculation
							$total_earnings = $salary + $allowance + $bonus + $commission + $extraHours - $loan - $advance - $deduction - $underHours - $unpaidDaysCost;
			
							// Apply tax
							$taxRate = getTaxRate($total_earnings, $state_id);
							$total_earnings -= $taxRate;
			
							// Insert into payroll_details
							$detailsData = [
								'payroll_id' => $payroll_id,
								'emp_id' => $employeeId,
								'full_name' => $fullName,
								'staff_no' => $staffNo,
								'email' => $email,
								'contract_type' => $contractType,
								'job_title' => $position,
								'month' => $month,
								'required_days' => $requiredDays,
								'days_worked' => $daysWorked,
								'base_salary' => $salary,
								'allowance' => $allowance,
								'bonus' => $bonus,
								'extra_hours' => $extraHours,
								'commission' => $commission,
								'tax' => $taxRate,
								'advance' => $advance,
								'loan' => $loan,
								'deductions' => $deduction,
								'unpaid_days' => $unpaidDaysCost,
								'unpaid_hours' => $underHours,
								'bank_name' => $paymentBank,
								'bank_number' => $paymentAccount,
								'status' => $status,
								'added_by' => $_SESSION['user_id']
							];
			
							$payrollDetailsClass->create($detailsData);
						}
					}
			
					$GLOBALS['conn']->commit();
					$result['msg'] = 'Employees added to payroll successfully';
					$result['error'] = false;
			
				} catch (Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: ' . $e->getMessage();
					$result['error'] = true;
				}
			
				echo json_encode($result);
				exit();
			}
			

			exit();
		} 


		// Update data
		else if($_GET['action'] == 'update') {
			$updated_date = date('Y-m-d H:i:s');
			if($_GET['endpoint'] == 'transaction') {
				try {
					$post = escapePostData($_POST);
					$transaction_id = $post['transaction_id'];
				   	$data = array(
				        'transaction_type' => $post['transaction_type'], 
				        'transaction_subtype' => $post['transaction_subtype'],
				        'amount' => $post['amount'], 
				        'date' => $post['date'], 
				        'description' => $post['description'], 
				        'status' => $post['status'], 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

			    	check_auth('edit_payroll_transactions');

			    	if($post['status'] == 'Approved') {
			    		check_auth('manage_payroll_transactions');
			    	}

			    	$result['id'] = $employeeTransactionsClass->update($transaction_id, $data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Employee transaction updated successfully';
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
			} else if($_GET['endpoint'] == 'approvePayroll') {
				try {
					$post = escapePostData($_POST);
					$status = $post['status'];
					$payrollId = $post['id']; 
					$emp_id = isset($post['emp_id']) ? $post['emp_id'] : ''; 
				   	$data = array(
				        'status' => $status, 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

			    	check_auth('manage_payroll');

			    	if(isset($emp_id) && $emp_id) {
			    		$details = $conn->prepare("UPDATE `payroll_details` SET `status`=? WHERE `payroll_id` = '$payrollId' AND `emp_id` = '$emp_id'");
				        $details->bind_param("s", $status);
				        $details->execute();
				        $result['id'] = $payrollId;
			    	} else {
				    	$result['id'] = $payrollClass->update($payrollId, $data);
				    	$details = $conn->prepare("UPDATE `payroll_details` SET `status`=? WHERE `payroll_id` = '$payrollId'");
				        $details->bind_param("s", $status);
				        $details->execute();
				    }

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Payroll status changed successfully';
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
			} else if($_GET['endpoint'] == 'payPayroll') {
				try {
				    $post = escapePostData($_POST);
				    $status = 'Paid';

				    $payrollId = $post['payroll_id'];
				    $payroll_detId = $post['payroll_detId'];
				    $slcBank = $post['slcBank'];
				    $payDate = $post['payDate'] . date(" H:i:s");

				    // Fetch bank information
				    $bankInfo = get_data('bank_accounts', ['id' => $slcBank]);
				    if (!$bankInfo) {
				        $result['msg'] = 'Something went wrong with the bank.';
				        $result['error'] = true;
				        echo json_encode($result);
				        exit();
				    }

				    $bank_name = $bankInfo[0]['bank_name'];
				    $account = $bankInfo[0]['account'];
				    $balance = $bankInfo[0]['balance'];

				    check_auth('manage_payroll');

				    $data = array(
				        'status' => $status, 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    $paid_through = $bank_name . ", " . $account;
				    $paid_by = $_SESSION['user_id'];

				    // Get the sum of all approved salaries for the payroll
				    $query = "SELECT `id`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `payroll_id` = ? AND `status` = 'Approved'";
				    if (isset($payroll_detId) && $payroll_detId) {
				    	$query .= " AND `id` = $payroll_detId";
				    }

				    $net_salary = 0;
				    $salaryStmt = $conn->prepare($query);
				    $salaryStmt->bind_param("i", $payrollId);
				    $salaryStmt->execute();
				    $salaryResult = $salaryStmt->get_result();

				    while ($row = $salaryResult->fetch_assoc()) {
				        $net_salary += $row['net_salary'];
				    }

				    // Start MySQL transaction
				    $conn->begin_transaction();

				    // Update payroll details based on payroll_detId
				    $detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `pay_date` = ?, `paid_by` = ?, `paid_through` = ?, `bank_id` =? WHERE `payroll_id` = ? AND `status` = 'Approved'";

				    if (isset($payroll_detId) && $payroll_detId) {
				        $detailsQuery .= " AND `id` = ?";
				        $detailsStmt = $conn->prepare($detailsQuery);
				        $detailsStmt->bind_param("ssssssi", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId, $payroll_detId);
				    } else {
				        $detailsStmt = $conn->prepare($detailsQuery);
				        $detailsStmt->bind_param("ssssss", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId);
				    }

				    if (!$detailsStmt->execute()) {
				        throw new Exception("Failed to update payroll details: " . $detailsStmt->error);
				    }

				    // Calculate the net salary


				    // Update bank balance
				    $new_balance = $balance - $net_salary;
				    $updateBankQuery = "UPDATE `bank_accounts` SET `balance` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` = ?";
				    $bankStmt = $conn->prepare($updateBankQuery);
				    $updated_date = date("Y-m-d H:i:s");
				    $bankStmt->bind_param("dsis", $new_balance, $paid_by, $updated_date, $slcBank);

				    if (!$bankStmt->execute()) {
				        throw new Exception("Failed to update bank balance: " . $bankStmt->error);
				    }

				    // Commit transaction
				    $conn->commit();

				    $result['msg'] = 'Payroll paid successfully';
				    $result['error'] = false;

				} catch (Exception $e) {
				    // Roll back transaction in case of error
				    $conn->rollback();
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage();
				    $result['error'] = true;
				}

				// Return the result as a JSON response
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'columns4CustomizeTable') {
				$table = isset($_POST['table']) ? $_POST['table'] : '';
				$columns = isset($_POST['columns']) ? $_POST['columns'] : '';

				$columns = implode(",", $columns);

				$update = "UPDATE `table_customize` SET `show_columns` = ? WHERE `dt_table` = ?";
			   	$updateStmt = $conn->prepare($update);
			    $updateStmt->bind_param("ss", $columns, $table);
			    $updateStmt->execute();

			    echo 'updated'; exit();

			} else if ($_GET['endpoint'] == 'payroll_status') {
				try {
					$post = escapePostData($_POST);
					$status = $post['status'] ?? null;
					$payrollId = $post['id'] ?? null;
					$emp_id = $post['emp_id'] ?? '';
			
					if (!$status || !$payrollId) {
						throw new Exception("Missing required data (status or id).");
					}
			
					// Map status to permission name
					$check = null;
					if ($status === 'Approved') $check = 'approve_payroll';
					else if ($status === 'Rejected') $check = 'reject_payroll';
					else if ($status === 'Reviewed') $check = 'review_payroll';
			
					if ($check) {
						check_auth($check);
					}
			
					$updated_date = date('Y-m-d H:i:s');
			
					$userInfo = $userClass->read($_SESSION['user_id']);
					$user_fullName = $userInfo['full_name'] ?? 'Unknown User';
			
					$payrollInfo = $payrollClass->read($payrollId);
					if (!$payrollInfo) {
						throw new Exception("Payroll not found.");
					}
			
					// Prevent creator from rejecting own payroll
					if ($status === 'Rejected' && (string)($payrollInfo['added_by'] ?? '') === (string)$_SESSION['user_id']) {
						throw new Exception("Payroll creator cannot reject their own payroll.");
					}
			
					// Safely decode JSON (fallback to empty arrays)
					$workflow = json_decode($payrollInfo['workflow'] ?? '[]', true);
					$rejected = json_decode($payrollInfo['rejected'] ?? '[]', true);
					$finished = json_decode($payrollInfo['finished'] ?? '[]', true);
			
					$workflow = is_array($workflow) ? $workflow : [];
					$rejected = is_array($rejected) ? $rejected : [];
					$finished = is_array($finished) ? $finished : [];
			
					$currentUser = (string)$_SESSION['user_id'];
			
					// Remove any rejected/finished entries that targeted the current user
					$rejected = array_values(array_filter($rejected, function ($record) use ($currentUser) {
						return !isset($record['next_user']) || (string)$record['next_user'] !== $currentUser;
					}));
					if($status == "Rejected") {
						$finished = array_values(array_filter($finished, function ($record) use ($currentUser) {
							return !isset($record['next_user']) || (string)$record['next_user'] !== $currentUser;
						}));
						
						$workflow = [];
					}
			
					// Append to workflow
					$newWorkflow = [
						"action" => $status . " by " . $user_fullName,
						"date" => $updated_date,
						"status" => $status,
						"user_id" => $_SESSION['user_id']
					];
					$workflow[] = $newWorkflow;
			
					$data = [
						'status' => $status,
						'workflow' => json_encode($workflow),
						'rejected' => json_encode($rejected),
						'finished' => json_encode($finished),
						'updated_by' => $_SESSION['user_id'],
						'updated_date' => $updated_date
					];
			
					$result = [];
					$result['id'] = $payrollClass->update($payrollId, $data);
			
					// Update all payroll details to match new status
					$details = $conn->prepare("UPDATE `payroll_details` SET `status`=? WHERE `payroll_id` = ?");
					$details->bind_param("si", $status, $payrollId);
					$details->execute();
			
					if ($result['id']) {
						$result['msg'] = 'Payroll status changed successfully';
						$result['error'] = false;
					} else {
						$result['msg'] = 'Something went wrong, please try again';
						$result['error'] = true;
					}
				} catch (Exception $e) {
					$result = [
						'msg' => 'Error: ' . $e->getMessage(),
						'error' => true,
					];
				}
			
				echo json_encode($result);
				exit;
			} else if ($_GET['endpoint'] == 'notify_next_person') {
				try {
					$post = escapePostData($_POST);
			
					$payroll_id = $post['payroll_id'] ?? null;
					$status = $post['status'] ?? '';
					$next_user = $post['next_user'] ?? null;
					$message = $post['message'] ?? '';
			
					if (!$payroll_id || !$next_user || !$status) {
						throw new Exception("Missing required data (payroll_id, status or next_user).");
					}
			
					// Prevent forwarding to self
					if ((string)$next_user === (string)$_SESSION['user_id']) {
						throw new Exception("You cannot forward to yourself.");
					}
			
					// Validate next_user exists & active
					$nextUserInfo = $userClass->read($next_user);
					if (!$nextUserInfo || ($nextUserInfo['status'] ?? '') !== 'Active') {
						throw new Exception("Next user not found or is not active.");
					}
			
					// Permission check depending on status
					$check = null;
					if ($status === 'Approved') $check = 'approve_payroll';
					else if ($status === 'Rejected') $check = 'reject_payroll';
					else if ($status === 'Reviewed') $check = 'review_payroll';
			
					if ($check) check_auth($check);
			
					$payrollInfo = $payrollClass->read($payroll_id);
					if (!$payrollInfo) throw new Exception("Payroll not found.");
			
					// Creator cannot reject their own payroll
					if ($status === 'Rejected' && (string)($payrollInfo['added_by'] ?? '') === (string)$_SESSION['user_id']) {
						throw new Exception("Payroll creator cannot reject their own payroll.");
					}
			
					// Prepare updated records
					$updated_date = date('Y-m-d H:i:s');
					$userInfo = $userClass->read($_SESSION['user_id']);
					$user_fullName = $userInfo['full_name'] ?? 'Unknown User';
			
					$updateColumn = ($status === 'Rejected') ? 'rejected' : 'finished';
					$action = ($status === 'Rejected' ? "Rejected by " : "Finished by ") . $user_fullName;
			
					// Safely decode existing arrays
					$workflow = json_decode($payrollInfo['workflow'] ?? '[]', true) ?: [];
					$rejected = json_decode($payrollInfo['rejected'] ?? '[]', true) ?: [];
					$finished = json_decode($payrollInfo['finished'] ?? '[]', true) ?: [];
			
					// Remove entries that targeted the current user (they are now acting)
					// $currentUser = (string)$_SESSION['user_id'];
					// $rejected = array_values(array_filter($rejected, function ($r) use ($currentUser) {
					// 	return !isset($r['next_user']) || (string)$r['next_user'] !== $currentUser;
					// }));
					// $finished = array_values(array_filter($finished, function ($r) use ($currentUser) {
					// 	return !isset($r['next_user']) || (string)$r['next_user'] !== $currentUser;
					// }));
			
					// Build new entry for rejected/finished
					$newData = [
						'action' => $action,
						'date' => $updated_date,
						'status' => $status,
						'user_id' => $_SESSION['user_id'],
						'next_user' => $next_user,
						'message' => $message
					];
			
					// Append to the proper list
					if ($updateColumn === 'rejected') {
						$rejected[] = $newData;
					} else {
						$finished[] = $newData;
					}
			
					// Also append to workflow
					$workflow[] = [
						'action' => $status . ' by ' . $user_fullName,
						'date' => $updated_date,
						'status' => $status,
						'user_id' => $_SESSION['user_id']
					];
			
					// Prepare update payload (also set the payroll status to the selected status)
					$updateData = [
						// 'workflow' => json_encode($workflow),
						'rejected' => json_encode($rejected),
						'finished' => json_encode($finished),
						'status' => $status,
						'updated_by' => $_SESSION['user_id'],
						'updated_date' => $updated_date
					];
			
					$result = [];
					$result['id'] = $payrollClass->update($payroll_id, $updateData);
			
					// Update payroll details statuses
					$details = $conn->prepare("UPDATE `payroll_details` SET `status`=? WHERE `payroll_id` = ?");
					$details->bind_param("si", $status, $payroll_id);
					$details->execute();

					$link = baseUri();
					// remove /app from last 
					$link = substr($link, 0, -4);
					$link .= '/payroll/'.$payroll_id;
			
					// Create notification for the next person
					$notificationData = [
						'recipient_id' => $next_user,
						'channel_type' => 'both',
						'notification_type' => 'Payroll',
						'priority' => 'high',
						'subject' => 'Payroll Notification',
						'details' => 'Payroll action required: ' . $status,
						'message' => $message,
						'added_by' => $_SESSION['user_id'],
						'link' => $link
					];
					$notificationsClass->create($notificationData);

					// Send email
					$email = [
						'to' => $userInfo['email'],
						'fullname' => $userInfo['full_name'],
						'subject' => 'Payroll Notification',
						'body' => 'You have a new payroll notification. Click the button below to view. '.$message,
						'buttonText' => 'View Payroll',
						'buttonLink' => $link,
						
					];
					
					try {
						$mailer->send($email);
						// echo "Email sent successfully!";
					} catch (Exception $e) {
						// echo "Failed: " . $e->getMessage();
					}
			
					if ($result['id']) {
						$result['msg'] = 'Notification sent successfully';
						$result['error'] = false;
					} else {
						$result['msg'] = 'Something went wrong, please try again';
						$result['error'] = true;
					}
				} catch (Exception $e) {
					$result = [
						'msg' => 'Error: ' . $e->getMessage(),
						'sql_error' => $e->getMessage(),
						'error' => true,
					];
				}
			
				echo json_encode($result);
				exit;
			} else if($_GET['endpoint'] == 'markAsRead') {
				try {
					$post = escapePostData($_POST);
					$type = $post['type'];
					if($type == 'all') {
						$sql = $GLOBALS['conn']->prepare("UPDATE `notifications` SET `is_read`='1' WHERE  recipient_id=?");
						$sql->bind_param("i", $_SESSION['user_id']);
						$sql->execute();
					} else {
						$sql = $GLOBALS['conn']->prepare("UPDATE `notifications` SET `is_read`='1' WHERE  recipient_id=? AND id=?");
						$sql->bind_param("ii", $_SESSION['user_id'], $post['type']);
						$sql->execute();
					}
					$result['msg'] = 'Notification marked as read successfully';
					$result['error'] = false;
				} catch (Exception $e) {
					$result = [
						'msg' => 'Error: ' . $e->getMessage(),
						'sql_error' => $e->getMessage(),
						'error' => true,
					];
				}
				echo json_encode($result);
				exit;
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

			if ($_GET['endpoint'] === 'transactions') {
				$result = ['data' => []];
			
				// Ordering
				$order = "ASC";
				$orderBy = "et.added_date";
				if (!empty($_POST['order'][0])) {
					$orderColumnMap = [
						0 => 'e.staff_no',
						1 => 'e.full_name',
						2 => 'et.transaction_type',
						3 => 'et.transaction_subtype',
						4 => 'et.amount',
						5 => 'et.status',
						6 => 'et.added_date'
					];
					$orderByIndex = (int)$_POST['order'][0]['column'];
					if (isset($orderColumnMap[$orderByIndex])) {
						$orderBy = $orderColumnMap[$orderByIndex];
					}
					$order = (strtoupper($_POST['order'][0]['dir']) === 'DESC') ? 'DESC' : 'ASC';
				}
			
				// Base query: join employee info
				$query = "
					SELECT et.*, 
						   e.staff_no, 
						   e.full_name, 
						   e.phone_number, 
						   e.email
					FROM employee_transactions et
					INNER JOIN employees e ON e.employee_id = et.emp_id
					WHERE et.transaction_id IS NOT NULL
				";
			
				// Search
				if (!empty($searchParam)) {
					$esc = escapeStr($searchParam);
					$query .= " AND (
						e.staff_no LIKE '%$esc%' OR 
						e.full_name LIKE '%$esc%' OR 
						e.phone_number LIKE '%$esc%' OR 
						e.email LIKE '%$esc%' OR 
						et.transaction_type LIKE '%$esc%' OR 
						et.transaction_subtype LIKE '%$esc%' OR 
						et.amount LIKE '%$esc%' OR 
						et.description LIKE '%$esc%' OR 
						et.added_by LIKE '%$esc%'
					)";
				}
			
				// Ordering + pagination
				$query .= " ORDER BY $orderBy $order LIMIT $start, $length";
			
				// Run query
				$employee_transactions = $GLOBALS['conn']->query($query);
			
				// Count total
				$countQuery = "
					SELECT COUNT(*) as total
					FROM employee_transactions et
					INNER JOIN employees e ON e.employee_id = et.emp_id
					WHERE et.transaction_id IS NOT NULL
				";
				if (!empty($searchParam)) {
					$esc = escapeStr($searchParam);
					$countQuery .= " AND (
						e.staff_no LIKE '%$esc%' OR 
						e.full_name LIKE '%$esc%' OR 
						e.phone_number LIKE '%$esc%' OR 
						e.email LIKE '%$esc%' OR 
						et.transaction_type LIKE '%$esc%' OR 
						et.transaction_subtype LIKE '%$esc%' OR 
						et.amount LIKE '%$esc%' OR 
						et.description LIKE '%$esc%' OR 
						et.added_by LIKE '%$esc%'
					)";
				}
				$totalRecordsResult = $GLOBALS['conn']->query($countQuery);
				$totalRecords = $totalRecordsResult->fetch_assoc()['total'] ?? 0;
			
				// Build response
				if ($employee_transactions && $employee_transactions->num_rows > 0) {
					while ($row = $employee_transactions->fetch_assoc()) {
						$result['data'][] = $row;
					}
					$result['iTotalRecords'] = $totalRecords;
					$result['iTotalDisplayRecords'] = $totalRecords;
					$result['msg'] = $employee_transactions->num_rows . " records were found.";
				} else {
					$result['msg'] = "No records found";
					$result['iTotalRecords'] = 0;
					$result['iTotalDisplayRecords'] = 0;
				}
			} else if ($_GET['endpoint'] === 'payroll') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['ref', 'month', '', 'added_date'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `payroll` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`ref` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_name` LIKE '%" . escapeStr($searchParam) . "%' OR `added_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' OR `status` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $payroll = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `payroll` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`ref` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_name` LIKE '%" . escapeStr($searchParam) . "%' OR `added_date` LIKE '%" . escapeStr($searchParam) . "%' OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' OR `status` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($payroll->num_rows > 0) {
			        while ($row = $payroll->fetch_assoc()) {
			        	$id = $row['id'];
			        	$month = $row['month'];

			        	$month = formatYearMonths($month);
			        	$row['month'] = $month;
			        	$employee_count = 0;

			        	// $query = "SELECT COUNT(emp_id) AS employee_count FROM `payroll_details` WHERE `payroll_id` = ? GROUP BY `emp_id`";
			        	$query = "SELECT COUNT(DISTINCT emp_id) AS employee_count FROM `payroll_details` WHERE `payroll_id` = ?";
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
			        $result['msg'] = $payroll->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'payroll_details') {
				// DataTables server side params
				$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
				$start = isset($_POST['start']) ? max(0, intval($_POST['start'])) : 0;
				$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
				// limit page size to avoid heavy queries
				if ($length <= 0 || $length > 200) $length = 25;
			
				$payroll_id = isset($_POST['payroll_id']) ? intval($_POST['payroll_id']) : 0;
				$month_raw = isset($_POST['month']) ? trim($_POST['month']) : '';
				// keep backward-compatible logic: if month provided use exact match, otherwise wildcard
				$monthLike = ($month_raw !== '') ? $month_raw : '%';
			
				$searchParam = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
			
				// Map datatable column index -> safe DB expression
				$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 2;
				$orderDir = (isset($_POST['order'][0]['dir']) && strtoupper($_POST['order'][0]['dir']) === 'DESC') ? 'DESC' : 'ASC';
			
				$orderColumnMap = [
					0 => 'pd.id',
					1 => 'COALESCE(e.staff_no, pd.staff_no)',
					2 => 'COALESCE(e.full_name, pd.full_name)',
					3 => 'COALESCE(e.email, pd.email)',
					4 => 'COALESCE(e.contract_type, pd.contract_type)',
					5 => 'COALESCE(e.designation, e.position, pd.job_title)',
					6 => 'pd.month',
					7 => 'pd.required_days',
					8 => 'pd.days_worked',
					9 => 'pd.unpaid_days',
					10 => 'pd.unpaid_hours',
					11 => 'pd.base_salary',
					12 => '(pd.allowance + pd.bonus + pd.commission)',
					13 => '(pd.loan + pd.advance + pd.deductions)',
					14 => 'pd.tax',
					15 => '(pd.base_salary + (pd.allowance + pd.bonus + pd.commission) - (pd.loan + pd.advance + pd.deductions) - pd.tax)',
					16 => 'pd.status',
					17 => 'COALESCE(e.payment_bank, pd.bank_name)',
					18 => 'COALESCE(e.payment_account, pd.bank_number)',
					19 => 'pd.id'
				];
			
				$orderBy = isset($orderColumnMap[$orderColumnIndex]) ? $orderColumnMap[$orderColumnIndex] : 'COALESCE(e.full_name, pd.full_name)';
			
				// Base WHERE (use prepared params)
				$whereClauses = ['pd.payroll_id = ?', 'pd.month LIKE ?'];
				$params = [$payroll_id, $monthLike];
				$paramTypes = 'is';
			
				// Add search
				if ($searchParam !== '') {
					$whereClauses[] = '(' .
						'COALESCE(e.full_name, pd.full_name) LIKE ? OR ' .
						'COALESCE(e.staff_no, pd.staff_no) LIKE ? OR ' .
						'COALESCE(e.email, pd.email) LIKE ?' .
						')';
					$like = '%' . $searchParam . '%';
					$params[] = $like; $params[] = $like; $params[] = $like;
					$paramTypes .= 'sss';
				}
			
				$whereSql = implode(' AND ', $whereClauses);
			
				// Columns to select (only what's needed)
				$select = "pd.id, pd.payroll_id, pd.emp_id,
					COALESCE(e.staff_no, pd.staff_no) AS staff_no,
					COALESCE(e.full_name, pd.full_name) AS full_name,
					COALESCE(e.email, pd.email) AS email,
					COALESCE(e.contract_type, pd.contract_type) AS contract_type,
					COALESCE(e.designation, e.position, pd.job_title) AS job_title,
					pd.month, pd.required_days, pd.days_worked, pd.unpaid_days, pd.unpaid_hours,
					pd.base_salary,
					(pd.allowance + pd.bonus + pd.commission) AS earnings,
					(pd.loan + pd.advance + pd.deductions) AS total_deductions,
					pd.tax,
					(pd.base_salary + (pd.allowance + pd.bonus + pd.commission) - (pd.loan + pd.advance + pd.deductions) - pd.tax) AS net_salary,
					COALESCE(e.payment_bank, pd.bank_name) AS bank_name,
					COALESCE(e.payment_account, pd.bank_number) AS bank_number,
					pd.status, pd.paid_by, COALESCE(e.state_id, NULL) AS state_id";
			
				// Main query with ordering and limit
				$sql = "SELECT $select
						FROM payroll_details pd
						LEFT JOIN employees e ON pd.emp_id = e.employee_id
						WHERE $whereSql
						ORDER BY $orderBy $order
						LIMIT ?, ?";
			
				// Append start/length params (integers)
				$params[] = $start; $params[] = $length;
				$paramTypes .= 'ii';
			
				// Prepare & execute
				$stmt = $GLOBALS['conn']->prepare($sql);
				if (!$stmt) {
					$result = ['draw' => $draw, 'data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'error' => $GLOBALS['conn']->error];
					echo json_encode($result);
					exit;
				}
			
				// bind params (using argument unpacking)
				$stmt->bind_param($paramTypes, ...$params);
				$stmt->execute();
				$res = $stmt->get_result();
			
				// Count total matching (filtered) rows
				$countSql = "SELECT COUNT(*) AS total
							 FROM payroll_details pd
							 LEFT JOIN employees e ON pd.emp_id = e.employee_id
							 WHERE $whereSql";
				$countStmt = $GLOBALS['conn']->prepare($countSql);
				if (!$countStmt) {
					$totalFiltered = 0;
				} else {
					// bind the same params except the LIMITs (so copy first N params)
					// Determine how many params were for WHERE: it's total params minus 2 (start, length)
					$whereParamCount = count($params) - 2;
					$whereParams = array_slice($params, 0, $whereParamCount);
					$whereTypes = substr($paramTypes, 0, $whereParamCount);
					$countStmt->bind_param($whereTypes, ...$whereParams);
					$countStmt->execute();
					$cntRes = $countStmt->get_result();
					$totalFiltered = ($cntRes && $cntRes->num_rows) ? intval($cntRes->fetch_assoc()['total']) : 0;
					$countStmt->close();
				}
			
				// Count total records for this payroll_id (unfiltered by search) for pagination
				$totalSql = "SELECT COUNT(*) as total FROM payroll_details WHERE payroll_id = ?";
				$totalStmt = $GLOBALS['conn']->prepare($totalSql);
				$totalRecords = 0;
				if ($totalStmt) {
					$totalStmt->bind_param('i', $payroll_id);
					$totalStmt->execute();
					$totalRes = $totalStmt->get_result();
					if ($totalRes && $totalRes->num_rows) $totalRecords = intval($totalRes->fetch_assoc()['total']);
					$totalStmt->close();
				}
			
				// Build response
				$result = [
					'draw' => $draw,
					'data' => [],
					'recordsTotal' => $totalRecords,
					'recordsFiltered' => $totalFiltered,
					// for compatibility with older DataTables
					'iTotalRecords' => $totalRecords,
					'iTotalDisplayRecords' => $totalFiltered
				];
			
				if ($res && $res->num_rows > 0) {
					while ($row = $res->fetch_assoc()) {
						$emp_id = $row['emp_id'];
						$paid_by = $row['paid_by'];
						$monthFormatted = $row['month'];
						// If month is stored as date-like, format; otherwise keep
						if (strtotime($row['month']) !== false) {
							$monthFormatted = date('F Y', strtotime($row['month']));
						}
						$net_salary = $row['net_salary'];
			
						// Use joined state_id (from employees)
						$state_id = isset($row['state_id']) ? $row['state_id'] : null;
						$taxPercentage = ($state_id !== null) ? getTaxPercentage($net_salary, $state_id) : '';
			
						// Resolve paid_by name (existing helper)
						$paid_by_name = $userClass->get_emp($paid_by);
						if (is_array($paid_by_name) && isset($paid_by_name['full_name'])) $paid_by_name = $paid_by_name['full_name'];
			
						$row['txtStatus'] = $row['status'];
						$row['taxRate'] = $taxPercentage;
						$row['paid_by'] = $paid_by_name;
						$row['month'] = $monthFormatted;
			
						// optional: cast numeric fields to float for JSON consumers
						$row['base_salary'] = floatval($row['base_salary']);
						$row['earnings'] = floatval($row['earnings']);
						$row['total_deductions'] = floatval($row['total_deductions']);
						$row['tax'] = floatval($row['tax']);
						$row['net_salary'] = floatval($row['net_salary']);
			
						$result['data'][] = $row;
					}
					$result['msg'] = $res->num_rows . " records were found.";
				} else {
					$result['msg'] = "No records found";
				}
			
				echo json_encode($result);
				exit;
			}
			


			echo json_encode($result);

			exit();

		} 


		// Get data
		else if($_GET['action'] == 'get') {
			if ($_GET['endpoint'] === 'transaction') {
				// json(get_data('employee_transactions', array('transaction_id' => $_POST['id']))[0]);
				$query = "
					SELECT et.*, 
						   e.staff_no, 
						   e.full_name, 
						   e.phone_number, 
						   e.email
					FROM employee_transactions et
					INNER JOIN employees e ON e.employee_id = et.emp_id
					WHERE et.transaction_id = ?
				";
				
				$stmt = $conn->prepare($query);
				$stmt->bind_param("i", $_POST['id']);
				$stmt->execute();
				$result = $stmt->get_result();
				
				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					json($row);
				} else {
					json([]);
				}
			} else if ($_GET['endpoint'] === 'transSubTypes') {
				$data = '<option value="">None</option>';
				$type = $_POST['type'];
				$sql = $GLOBALS['conn']->query("SELECT * FROM `trans_subtypes` WHERE `type` = '$type'");
				if($sql->num_rows > 0) {
					while($row = $sql->fetch_assoc()) {
						$data .= '<option value="'.$row['name'].'">'.$row['name'].'</option>';
					}
				}
				echo $data;
			} else if ($_GET['endpoint'] === 'downloadTransactionsCSV') {
				$post = escapePostData($_POST);
				$ref_id = isset($post['ref_id']) ? $post['ref_id'] : '';
	    		$get_employees = "SELECT * FROM `employees` WHERE `status` = 'active'";
	    		if($post['ref'] == 'Department') {
	    			$get_employees .= " AND `branch_id` = '$ref_id'";
	    		} else if($post['ref'] == 'Location') {
	    			$get_employees .= " AND `location_id` = '$ref_id'";
	    		}

	    		$result = [];
				$result['data'] = []; // Initialize as an empty array for storing rows
				// Add header row as the first entry
				$result['data'][] = ['Staff No.', 'Employee ID', 'Full name', 'Date', 'Transaction type', 'Transaction subtype', 'Amount', 'Status', 'Comments'];

				$empSet = $GLOBALS['conn']->query($get_employees);
				if ($empSet->num_rows > 0) {
				    while ($row = $empSet->fetch_assoc()) {
				        $employee_id 	= $row['employee_id'];
				        $full_name 		= $row['full_name'];
				        $phone_number 	= $row['phone_number'];
				        $email 			= $row['email'];
				        $staff_no 		= $row['staff_no'];

				        $date 			= $post['date'];
				        $transaction_type 	= '';  
				        $amount 	= '';
				        $status 	= 'Request';  
				        $transaction_subtype 	= ''; 
				        $description = '';
				    

	    				$check_exists = $GLOBALS['conn']->query("SELECT * FROM `employee_transactions` WHERE `emp_id` = '$employee_id' AND `status` <> 'Cancelled' AND `date` LIKE '$date%'");
	    				if($check_exists->num_rows > 0) {
	    					while($existRow = $check_exists->fetch_assoc()) {
	    						$transaction_type = $existRow['transaction_type'];
	    						$amount = $existRow['amount'];
	    						$status = $existRow['status'];
	    						$transaction_subtype = $existRow['transaction_subtype'];
	    						$description = $existRow['description'];
	    					}
	    				}


				        // Prepare the data for the current employee
				        $data = [$staff_no, $employee_id, $full_name, $date, $transaction_type, $transaction_subtype, $amount, $status, $description];

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
			} else if ($_GET['endpoint'] === '4payslipShow') {
				$payroll_id = (int)$_POST['id'];
				$data = '';
			
				// Single query: join payroll_details with employees
				$sql = "
					SELECT pd.*, 
						   e.full_name AS emp_full_name, e.staff_no AS emp_staff_no, e.position,
						   e.branch, e.payment_bank, e.payment_account,
						   e.state_id, e.gender, e.avatar
					FROM payroll_details pd
					LEFT JOIN employees e ON pd.emp_id = e.employee_id
					WHERE pd.id = ?
					LIMIT 1
				";
				$stmt = $GLOBALS['conn']->prepare($sql);
				$stmt->bind_param("i", $payroll_id);
				$stmt->execute();
				$result = $stmt->get_result();
			
				if ($result && $row = $result->fetch_assoc()) {
					// Payroll fields
					$month      = date('F Y', strtotime($row['month']));
					$added_date = date('F d, Y', strtotime($row['added_date']));
					$base_salary = (float)$row['base_salary'];
			
					// Employee fields (fallback if payroll_details is outdated)
					$full_name  = $row['emp_full_name'] ?: $row['full_name'];
					$staff_no   = $row['emp_staff_no'] ?: $row['staff_no'];
					$emp_id     = $row['emp_id'];
					$position   = $row['position'] ?? '';
					$branch     = $row['branch'] ?? '';
					$payment    = ($row['payment_bank'] ?? '') . ', ' . ($row['payment_account'] ?? '');
			
					// Avatar logic
					$avatar = $row['avatar'];
					if (!$avatar) {
						$avatar = strtolower($row['gender']) === 'female' ? 'female_avatar.png' : 'male_avatar.png';
					}
			
					// Tax percentage from employees.state_id
					$taxPercentage = getTaxPercentage($base_salary, (int)$row['state_id']);
			
					// Totals
					$totalEarnings = $row['base_salary'] + $row['allowance'] + $row['bonus'] + $row['extra_hours'] + $row['commission'];
					$totalDeductions = $row['advance'] + $row['loan'] + $row['deductions'] + $row['unpaid_days'] + $row['unpaid_hours'] + $row['tax'];
					$netSalary = $totalEarnings - $totalDeductions;
			
					// Build HTML (kept same structure)
					$data = '<form class="modal-content" id="PayslipForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
						<div class="modal-header">
							<h5 class="modal-title">Payslip for the month of <span class="paySlipMonth">'.$month.'</span></h5>
							<button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body" >
							<div>
								<div class="row">
									<div class="col col-md-3 col-sm-12">
										<img class="w-100 " style="max-height: 200px;" src="../assets/images/avatars/'.$avatar.'">
									</div>
			
									<div class="col  col-md-5 col-sm-12">
										<div class="border ">
											<div class="border-bottom p-1 sflex swrap  ">
												<span class=" sflex-basis-100">Employee name</span>
												<span class="bold sflex-basis-100">'.$full_name.'</span>
											</div>
											<div class="border-bottom p-1 sflex swrap  ">
												<span class=" sflex-basis-100">Employee ID/Staff No.</span>
												<span class="bold sflex-basis-100">'.$emp_id.', '.$staff_no.'</span>
											</div>
											<div class="border-bottom p-1 sflex swrap  ">
												<span class=" sflex-basis-100">Job title</span>
												<span class="bold sflex-basis-100">'.$position.'</span>
											</div>
											<div class="border-bottom p-1 sflex swrap  ">
												<span class=" sflex-basis-100">Department</span>
												<span class="bold sflex-basis-100">'.$branch.'</span>
											</div>
										</div>
									</div>
			
									<div class="col  col-md-4 col-sm-12">
										<div class="sflex smb-15 p-3 sjend">
											<a  class="fa cursor print-payslip fa-print"></a>
										</div>
										<div class="border ">
											<div class="border-bottom p-1 sflex swrap  ">
												<span class=" sflex-basis-100">Payment method</span>
												<span class="bold sflex-basis-100">'.$payment.'</span>
											</div>
											<div class="border-bottom p-1 sflex swrap  ">
												<span class=" sflex-basis-100">Days worked</span>
												<span class="bold sflex-basis-100">'.$row['days_worked'].'/'.$row['required_days'].' </span>
											</div>
											
											<div class="border-bottom p-1 sflex swrap  ">
												<span class=" sflex-basis-100">Pay date</span>
												<span class="bold sflex-basis-100">'.$added_date.'</span>
											</div>
										</div>
									</div>
								</div>
								<div class="m-4"></div>
								<h5 class="">Payroll details</h5>
								<table id="payrollDetails" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Earnings</th>
											<th>Amount</th>
											<th>Deductions</th>
											<th>Amount</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Basic salary</td>
											<td>'.formatMoney($row['base_salary']).'</td>
											<td>Un-paid days</td>
											<td>'.formatMoney($row['unpaid_days']).'</td>
										</tr>
										<tr>
											<td>Allowance</td>
											<td>'.formatMoney($row['allowance']).'</td>
											<td>Un-paid hours</td>
											<td>'.formatMoney($row['unpaid_hours']).'</td>
										</tr>
										<tr>
											<td>Commissions</td>
											<td>'.formatMoney($row['commission']).'</td>
											<td>Advance</td>
											<td>'.formatMoney($row['advance']).'</td>
										</tr>
										<tr>
											<td>Extra Hours</td>
											<td>'.formatMoney($row['extra_hours']).'</td>
											<td>Loan</td>
											<td>'.formatMoney($row['loan']).'</td>
										</tr>
										<tr>
											<td>Bonus</td>
											<td>'.formatMoney($row['bonus']).'</td>
											<td>Other Deductions</td>
											<td>'.formatMoney($row['deductions']).'</td>
										</tr>
										<tr>
											<td>Total Earnings</td>
											<td>'.formatMoney($totalEarnings).'</td>
											<td>Tax</td>
											<td>'.formatMoney($row['tax']).' ('.$taxPercentage.'%)</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td>Total Deductions</td>
											<td>'.formatMoney($totalDeductions).'</td>
										</tr>
										<tr>
											<td></td>
											<td>Net Salary</td>
											<td>'.formatMoney($netSalary).'</td>
											<td></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</form>';
				}
			
				echo $data;        
			}else if ($_GET['endpoint'] === 'allColumns4CustomizeTable') {
				$table = isset($_POST['table']) ? $_POST['table'] : '';
				$allColumns = get_columns($table, 'all_columns');
				$showColumns = get_columns($table, 'show_columns');
				$data = '';
				foreach ($allColumns as $col) {
					$checked = '';
					if(in_array($col, $showColumns)) $checked = 'checked=""';

					$colTxt = ucwords(strtolower(str_replace("_", " ", $col)));
					$data .= '<div class="col-sm-6">
                		<div class="form-check cursor">
							<input class="form-check-input cursor custom-col" '.$checked.' value="'.$col.'" type="checkbox" value="" id="custom-col-'.$col.'">
							<label class="form-check-label cursor" for="custom-col-'.$col.'">
							'.$colTxt.'
							</label>
						</div>
                	</div>';
				}

				echo $data;
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
			} else if ($_GET['endpoint'] === 'trans_for') {
				$data = '';
				if($_POST['transFor'] == 'Employee') {
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
				} else if($_POST['transFor'] == 'Department') {
					$data = '<label class="label required" for="searchDepartment">Department</label>
                        <select class="my-select searchDepartment" name="searchDepartment" id="searchDepartment" data-live-search="true" title="Search and select department">';
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
				} else if($_POST['transFor'] == 'Location') {
					$data = '<label class="label required" for="searchLocation">Location</label>
                        <select class="my-select searchLocation" name="searchLocation" id="searchLocation" data-live-search="true" title="Search and select location">';
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
			} else if ($_GET['endpoint'] === 'search_employees_for_payroll') {
				$payroll_id = isset($_POST['payroll_id']) ? (int)$_POST['payroll_id'] : 0;
				$search = isset($_POST['search']) ? trim($_POST['search']) : '';
				$options = '';
				$response = ['error' => true, 'options' => ''];
			
				if ($payroll_id > 0) {
					// Get payroll details
					$payrollInfo = $payrollClass->read($payroll_id);
			
					if ($payrollInfo) {
						$ref_type = $payrollInfo['ref']; // 'Department' or 'Location'
						$ref_id   = (int)$payrollInfo['ref_id'];
			
						// Base query with LEFT JOIN to exclude already assigned employees
						$query = "
							SELECT e.employee_id, e.full_name, e.phone_number
							FROM employees e
							LEFT JOIN payroll_details pd 
								   ON e.employee_id = pd.emp_id AND pd.payroll_id = ?
							WHERE e.status = 'Active' AND pd.emp_id IS NULL
						";
			
						$types = "i"; // For payroll_id
						$params = [$payroll_id];
			
						// Add filter by department or location
						if ($ref_type === 'Department' && $ref_id > 0) {
							$query .= " AND e.branch_id = ? ";
							$types .= "i";
							$params[] = $ref_id;
						} elseif ($ref_type === 'Location' && $ref_id > 0) {
							$query .= " AND e.location_id = ? ";
							$types .= "i";
							$params[] = $ref_id;
						}
			
						// Add search filter
						if ($search) {
							$query .= " AND (e.full_name LIKE ? OR e.phone_number LIKE ? OR e.email LIKE ?)";
							$types .= "sss";
							$like = "%{$search}%";
							$params[] = $like;
							$params[] = $like;
							$params[] = $like;
						}
			
						$query .= " ORDER BY e.full_name ASC LIMIT 20";
			
						// Prepare & bind
						$stmt = $GLOBALS['conn']->prepare($query);
						if ($stmt) {
							$stmt->bind_param($types, ...$params);
							$stmt->execute();
							$result = $stmt->get_result();
			
							if ($result && $result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									$options .= '<option value="' . $row['employee_id'] . '">'
											  . htmlspecialchars($row['full_name'])
											  . ', ' . htmlspecialchars($row['phone_number'] ?? '') 
											  . '</option>';
								}
								$response['error'] = false;
							}
							$stmt->close();
						}
					}
				}
			
				$response['options'] = $options;
				echo json_encode($response);
				exit();
			}
			

			exit();
		}


		// Delete data
		else if($_GET['action'] == 'delete') {
			if ($_GET['endpoint'] === 'transaction') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('delete_payroll_transactions');
				    // checkForeignKey($post['id'], 'trans_ref', ['payroll_details']);
				    $deleted = $employeeTransactionsClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Employee transaction has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'payroll') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    $payrollId = $post['id'];
				    check_auth('delete_payroll');

				    $payrollClass->update_bankAccount($payrollId);

				    $month = '';
				    $payrollInfo = get_data('payroll', ['id' => $payrollId]);
				    if($payrollInfo) {
				    	$payrollInfo = $payrollInfo[0];
				    	$month = $payrollInfo['month'];
				    }

				    $del_emp = "DELETE FROM `payroll_details` WHERE `payroll_id` = '$payrollId'";
					if(!mysqli_query($GLOBALS["conn"], $del_emp)) {
						// throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}

				    $deleted = $payrollClass->delete($post['id']);

				    $payrollClass->update_payrollRelatedTables($month, $payrollId, true);
				    

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Payroll record has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'payrollDetail') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    $detailId = $post['id'];
				    check_auth('delete_payroll');

				    $payroll_id = 0;
				    $detailInfo = get_data('payroll_details', ['id' => $detailId]);
				    if($detailInfo) {
				    	$detailInfo = $detailInfo[0];
				    	$payroll_id = $detailInfo['payroll_id'];
				    }

				    $payrollClass->update_bankAccount($payroll_id, $detailId);

				    

				    $deleted = $payrollDetailsClass->delete($post['id']);
				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Payroll detail record has been  deleted successfully';
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