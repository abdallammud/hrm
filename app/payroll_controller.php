<?php
require('init.php');

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
				    	check_auth('manage_employee_transactions');

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

				    check_auth('manage_employee_transactions'); // Authorization check

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

				    if($post['ref'] = 'All') {
				    	$post['ref_id'] = '';
				    	$post['ref_name'] = 'All employees';
				    }

				    $month = date('Y-m');
				    $data = array(
				        'ref' 		=> $post['ref'], 
				        'ref_id' 	=> $post['ref_id'], 
				        'month' 	=> $month,
				        'ref_name' 	=> $post['ref_name'], 
				        'added_by' 	=> $_SESSION['user_id']
				    );

				    check_auth('generate_payroll');
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

			    	check_auth('manage_employee_transactions');

			    	if($post['status'] == 'Approved') {
			    		check_auth('approve_employee_transactions');
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
					$status = 'Approved';
					$payrollId = $post['id']; 
					$emp_id = isset($post['emp_id']) ? $post['emp_id'] : ''; 
				   	$data = array(
				        'status' => $status, 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

			    	check_auth('approve_payroll');

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
				        $result['msg'] = 'Payroll approved successfully successfully';
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

				    check_auth('pay_payroll');

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
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['staff_no', 'full_name', 'transaction_type', 'transaction_subtype', 'amount', 'status', 'added_date'];
				    // var_dump($_POST['order']);
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `employee_transactions` WHERE `transaction_id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `transaction_type` LIKE '%" . escapeStr($searchParam) . "%' OR `transaction_subtype` LIKE '%" . escapeStr($searchParam) . "%' OR `amount` LIKE '%" . escapeStr($searchParam) . "%' OR `description` LIKE '%" . escapeStr($searchParam) . "%'  OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $employee_transactions = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `employee_transactions` WHERE `transaction_id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `transaction_type` LIKE '%" . escapeStr($searchParam) . "%' OR `transaction_subtype` LIKE '%" . escapeStr($searchParam) . "%' OR `amount` LIKE '%" . escapeStr($searchParam) . "%' OR `description` LIKE '%" . escapeStr($searchParam) . "%'  OR `added_by` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($employee_transactions->num_rows > 0) {
			        while ($row = $employee_transactions->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $employee_transactions->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
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
				$payroll_id = isset($_POST['payroll_id']) ? $_POST['payroll_id'] : 0;
				$month = isset($_POST['month']) ? $_POST['month'] : '';
				if(isset($_POST['payroll_id'])) $payroll_id = $_POST['payroll_id'];
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['staff_no', 'full_name', 'base_salary', 'earnings', 'total_deductions', 'tax', 'net_salary'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT  `id`, `payroll_id`, `emp_id`, `staff_no`, `full_name`, `email`, `contract_type`, `job_title`, `month`, `required_days`, `days_worked`, `unpaid_days`, `unpaid_hours`, `bank_name`, `bank_number`, `pay_date`, `paid_by`,  `status`, `base_salary`, (`allowance` + `bonus` + `commission`) AS earnings, (`loan` + `advance` + `deductions`) AS `total_deductions`, `tax`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `payroll_id`  = $payroll_id AND `month` LIKE '$month'";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `staff_no` LIKE '%" . escapeStr($searchParam) . "%'  )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $payroll_details = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `payroll_details`  WHERE `payroll_id`  = $payroll_id ";
			    if ($searchParam) {
			        $countQuery .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `staff_no` LIKE '%" . escapeStr($searchParam) . "%'  )";
			    }




			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($payroll_details->num_rows > 0) {
			        while ($row = $payroll_details->fetch_assoc()) {
			        	$emp_id = $row['emp_id'];
			        	$paid_by = $row['paid_by'];
			        	$month 	= $row['month'];
			        	$month 	= date('F Y', strtotime($month));
			        	$net_salary = $row['net_salary'];
			        	$employeeInfo = get_data('employees', ['employee_id' => $emp_id]);
			        	$taxPercentage = '';
			        	if($employeeInfo) {
			        		$employeeInfo = $employeeInfo[0];
			        		$state_id = $employeeInfo['state_id'];
			        		$taxPercentage = getTaxPercentage($net_salary, $state_id);
			        	}

			        	$paid_by = $userClass->get_emp($paid_by);
			        	if(count($paid_by) > 0) $paid_by = $paid_by['full_name'];


			        	$row['taxRate'] = $taxPercentage;
			        	$row['paid_by'] = $paid_by;
			        	$row['month'] = $month;
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $payroll_details->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			}


			echo json_encode($result);

			exit();

		} 


		// Get data
		else if($_GET['action'] == 'get') {
			if ($_GET['endpoint'] === 'transaction') {
				json(get_data('employee_transactions', array('transaction_id' => $_POST['id']))[0]);
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
				$payroll_id = $_POST['id'];
				$data = '';

				$query = $GLOBALS['conn']->query("SELECT * FROM `payroll_details` WHERE `id` = '$payroll_id'");
				if($query) {
					while($row = $query->fetch_assoc()) {
						$full_name = $row['full_name'];
						$emp_id = $row['emp_id'];
						$month = $row['month'];
						$base_salary = $row['base_salary'];
						$added_date = $row['added_date'];
						$month = date('F Y', strtotime($month));
						$added_date = date('F d, Y', strtotime($added_date));



						$employee = $GLOBALS['employeeClass']->read($emp_id);
						$state_id = $employee['state_id'];
		        		$taxPercentage = getTaxPercentage($base_salary, $state_id);
						// var_dump($employee);
						if(!$employee['avatar']) {
							if(strtolower($employee['gender']) == 'female')  {
								$employee['avatar'] = 'female_avatar.png';
							} else {
								$employee['avatar'] = 'male_avatar.png';
							}
						}

						$avatar = $employee['avatar'];


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
				                        			<span class="bold sflex-basis-100">'.$emp_id.', '.$row['staff_no'].'</span>
				                        		</div>
				                        		<div class="border-bottom p-1 sflex swrap  ">
				                        			<span class=" sflex-basis-100">Job title</span>
				                        			<span class="bold sflex-basis-100">'.$employee['position'].'</span>
				                        		</div>
				                        		<div class="border-bottom p-1 sflex swrap  ">
				                        			<span class=" sflex-basis-100">Department</span>
				                        			<span class="bold sflex-basis-100">'.$employee['branch'].'</span>
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
				                        			<span class="bold sflex-basis-100">'.$employee['payment_bank'].', '.$employee['payment_account'].'</span>
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
				                   				<td>'.formatMoney($row['base_salary']+$row['allowance'] + $row['bonus'] + $row['extra_hours'] + $row['commission']).'</td>
				                   				<td>Tax</td>
				                   				<td>'.formatMoney($row['tax']).' ('.$taxPercentage.'%)</td>
				                   			</tr>
				                   			<tr>
				                   				<td></td>
				                   				<td></td>
				                   				<td>Total Deductions</td>
				                   				<td>'.formatMoney($row['advance']+$row['loan'] + $row['deductions'] + $row['unpaid_days'] + $row['unpaid_hours']+$row['tax']).'</td>
				                   			</tr>
				                   			<tr>
				                   				<td></td>
				                   				<td>Net Salary</td>
				                   				<td>'.formatMoney(($row['base_salary']+$row['allowance'] + $row['bonus'] + $row['extra_hours'] + $row['commission']) - ($row['advance']+$row['loan'] + $row['deductions'] + $row['unpaid_days'] + $row['unpaid_hours']+$row['tax'])).'</td>
				                   				<td></td>
				                   			</tr>
				                   		</tbody>
				                   	</table>
				                </div>
				            </div>
				        </form>';
					}
				}

				echo $data;		
			} else if ($_GET['endpoint'] === 'allColumns4CustomizeTable') {
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
				} else if($_POST['transFor'] == 'Location') {
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
			if ($_GET['endpoint'] === 'transaction') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_employee_transactions');
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
				    check_auth('generate_payroll');

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
				    check_auth('generate_payroll');

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