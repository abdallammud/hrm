<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'save') {
			if($_GET['endpoint'] == 'bank_account') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'bank_name' => $post['name'], 
				        'account' => isset($post['account']) ? $post['account']: "" ,  
				        'balance' => isset($post['balance']) ? $post['balance']: "" , 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('bank_accounts', ['bank_name' => $post['name']]);
				    check_auth('create_bank_accounts');

				    // Call the create method
				    $result['id'] = $accountsClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Bank account created successfully';
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
			} else if($_GET['endpoint'] == 'expense') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    // Get bank and financial account info
				    $bankInfo = get_data('bank_accounts', ['id' => $post['bank_id']])[0];
				    $fnAccountInfo = get_data('financial_accounts', ['id' => $post['fn_account_id']])[0];
				    // Check if bank has sufficient balance
				    if($bankInfo['balance'] < $post['amount']) {
				        $result['msg'] = 'Insufficient bank balance';
				        $result['error'] = true;
				        echo json_encode($result);
				        exit();
				    }
				    $data = array(
				        'type' => 'Expense',
				        'bank_id' => $post['bank_id'],
				        'bank_name' => $bankInfo['bank_name'],
				        'bank_account' => $bankInfo['account'],
				        'amount' => $post['amount'],
				        'fn_account_id' => $post['fn_account_id'],
				        'fn_account_name' => $fnAccountInfo['name'],
				        'payee_payer' => $post['payee_payer'],
				        'description' => isset($post['description']) ? $post['description'] : "",
				        'ref_number' => isset($post['refNumber']) ? $post['refNumber'] : "",
				        'added_by' => $_SESSION['user_id'],
				        'added_date' => $post['paid_date'] . ' ' . date('H:i:s')
				    );

				    check_auth('create_expenses');

				    // Start transaction
				    $GLOBALS['conn']->begin_transaction();
				    try {
				        // Create expense record
				        $result['id'] = $transactionsClass->create($data);
				        // Update bank balance
				        $newBalance = $bankInfo['balance'] - $post['amount'];
				        $accountsClass->update($post['bank_id'], ['balance' => $newBalance]);
				        // $transactionsClass->commit();
						$GLOBALS['conn']->commit();
				        if($result['id']) {
				            $result['msg'] = 'Expense recorded successfully';
				            $result['error'] = false;
				        } else {
				            $result['msg'] = 'Something went wrong, please try again';
				            $result['error'] = true;
				        }
				    } catch (Exception $e) {
				        $GLOBALS['conn']->rollback();
				        throw $e;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the create method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'income') {
                try {
                    $post = escapePostData($_POST);
                    $bankInfo = get_data('bank_accounts', ['id' => $post['bank_id']])[0];
                    $fnAccountInfo = get_data('financial_accounts', ['id' => $post['fn_account_id']])[0];
                    $data = array(
                        'type' => 'Income',
                        'bank_id' => $post['bank_id'],
                        'bank_name' => $bankInfo['bank_name'],
                        'bank_account' => $bankInfo['account'],
                        'amount' => $post['amount'],
                        'fn_account_id' => $post['fn_account_id'],
                        'fn_account_name' => $fnAccountInfo['name'],
                        'payee_payer' => $post['payee_payer'],
                        'description' => isset($post['description']) ? $post['description'] : "",
                        'ref_number' => isset($post['refNumber']) ? $post['refNumber'] : "",
                        'added_by' => $_SESSION['user_id'],
                        'added_date' => $post['paid_date'] . ' ' . date('H:i:s')
                    );
                    check_auth('create_income');
                    $GLOBALS['conn']->begin_transaction();
                    try {
                        $result['id'] = $transactionsClass->create($data);
                        $newBalance = $bankInfo['balance'] + $post['amount'];
                        $accountsClass->update(['balance' => $newBalance], ['id' => $post['bank_id']]);
                        $GLOBALS['conn']->commit();
                        $result['msg'] = 'Income added successfully';
                        $result['error'] = false;
                    } catch (Exception $e) {
                        $GLOBALS['conn']->rollback();
                        $result['msg'] = 'Error: Something went wrong';
                        $result['sql_error'] = $e->getMessage();
                        $result['error'] = true;
                    }
                } catch (Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
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
			if($_GET['endpoint'] == 'bank_account') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'bank_name' => $post['name'], 
				        'account' => isset($post['account']) ? $post['account']: "" ,  
				        'balance' => isset($post['balance']) ? $post['balance']: "" , 
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "Active" , 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('bank_accounts', ['bank_name' => $post['name']], ['id' => $post['id']]);
				    check_auth('edit_bank_accounts');

				    // Call the create method
				    $result['id'] = $accountsClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Bank account info editted successfully';
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
			} else if($_GET['endpoint'] == 'expense') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    // Get current expense data
				    $currentExpense = get_data('fn_transactions', ['id' => $post['id']]);
				    if(!$currentExpense) {
				        $result['msg'] = 'Expense not found';
				        $result['error'] = true;
				        echo json_encode($result);
				        exit();
				    }
				    $currentExpense = $currentExpense[0];
				    // Get bank and financial account info
				    $bankInfo = get_data('bank_accounts', ['id' => $post['slcBank']]);
				    $fnAccountInfo = get_data('financial_accounts', ['id' => $post['slcFinancialAccount']]);
				    // Calculate balance change
				    $oldAmount = $currentExpense['amount'];
				    $newAmount = $post['amount'];
				    $amountDifference = $newAmount - $oldAmount;
				    // Check if bank has sufficient balance for increase
				    if($amountDifference > 0 && $bankInfo['balance'] < $amountDifference) {
				        $result['msg'] = 'Insufficient bank balance for the increase';
				        $result['error'] = true;
				        echo json_encode($result);
				        exit();
				    }
				    $data = array(
				        'bank_id' => $post['slcBank'],
				        'bank_name' => $bankInfo['bank_name'],
				        'bank_account' => $bankInfo['account'],
				        'amount' => $post['amount'],
				        'fn_account_id' => $post['slcFinancialAccount'],
				        'fn_account_name' => $fnAccountInfo['name'],
				        'payee_payer' => $post['paidTo'],
				        'description' => isset($post['description']) ? $post['description'] : "",
				        'ref_number' => isset($post['refNumber']) ? $post['refNumber'] : "",
				        'status' => isset($post['slcStatus']) ? $post['slcStatus'] : "Active",
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date,
				        'added_date' => $post['paidDate'] . ' ' . date('H:i:s')
				    );

				    check_auth('edit_expenses');

				    // Start transaction
				    $transactionsClass->beginTransaction();
				    try {
				        // Update expense record
				        $result['id'] = $transactionsClass->update($post['id'], $data);
				        // Update bank balance based on difference
				        if($amountDifference != 0) {
				            $newBalance = $bankInfo['balance'] - $amountDifference;
				            $accountsClass->update($post['slcBank'], ['balance' => $newBalance]);
				        }
				        $transactionsClass->commit();
				        if($result['id']) {
				            $result['msg'] = 'Expense updated successfully';
				            $result['error'] = false;
				        } else {
				            $result['msg'] = 'Something went wrong, please try again';
				            $result['error'] = true;
				        }
				    } catch (Exception $e) {
				        $transactionsClass->rollback();
				        throw $e;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the update method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'income') {
				try {
					$post = escapePostData($_POST);
					$currentIncome = get_data('fn_transactions', ['id' => $post['id']]);
					if(!$currentIncome) {
						$result['msg'] = 'Income not found';
						$result['error'] = true;
						echo json_encode($result);
						exit();
					}
					$currentIncome = $currentIncome[0];
					$bankInfo = get_data('bank_accounts', ['id' => $post['slcBankIncome']]);
					$fnAccountInfo = get_data('financial_accounts', ['id' => $post['slcFinancialAccountIncome']]);
					$oldAmount = $currentIncome['amount'];
					$newAmount = $post['amountIncome'];
					$amountDifference = $newAmount - $oldAmount;
					$data = array(
						'bank_id' => $post['slcBankIncome'],
						'bank_name' => $bankInfo['bank_name'],
						'bank_account' => $bankInfo['account'],
						'amount' => $post['amountIncome'],
						'fn_account_id' => $post['slcFinancialAccountIncome'],
						'fn_account_name' => $fnAccountInfo['name'],
						'payee_payer' => $post['receivedFrom'],
						'description' => isset($post['descriptionIncome']) ? $post['descriptionIncome'] : "",
						'ref_number' => isset($post['refNumberIncome']) ? $post['refNumberIncome'] : "",
						'status' => isset($post['slcStatusIncome']) ? $post['slcStatusIncome'] : "Active",
						'updated_by' => $_SESSION['user_id'],
						'updated_date' => $updated_date,
						'added_date' => $post['receivedDate'] . ' ' . date('H:i:s')
					);
					check_auth('edit_income');
					$transactionsClass->beginTransaction();
					try {
						$result['id'] = $transactionsClass->update($post['id'], $data);
						if($amountDifference != 0) {
							$newBalance = $bankInfo['balance'] + $amountDifference;
							$accountsClass->update($post['slcBankIncome'], ['balance' => $newBalance]);
						}
						$transactionsClass->commit();
						if($result['id']) {
							$result['msg'] = 'Income updated successfully';
							$result['error'] = false;
						} else {
							$result['msg'] = 'Something went wrong, please try again';
							$result['error'] = true;
						}
					} catch (Exception $e) {
						$transactionsClass->rollback();
						throw $e;
					}
				} catch (Exception $e) {
					$result['msg'] = 'Error: Something went wrong';
					$result['sql_error'] = $e->getMessage();
					$result['error'] = true;
				}
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'payPayroll') {
				try {
				    $post = escapePostData($_POST);
				    $status = 'Paid';

				    $payrollId = $post['payroll_id'];
				    $payroll_detId = isset($post['payroll_detId']) ? $post['payroll_detId'] : null;
				    $payroll_detIds = isset($post['payroll_detIds']) ? $post['payroll_detIds'] : null;
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

				    $paid_through = $bank_name . ", " . $account;
				    $paid_by = $_SESSION['user_id'];

				    // Get the sum of all approved salaries for the payroll
				    $query = "SELECT `id`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `payroll_id` = ? AND `status` = 'Approved'";
				    // Handle single or bulk payment
				    if ($payroll_detId) {
				        $query .= " AND `id` = ?";
				    } else if ($payroll_detIds) {
				        $ids = implode(',', array_map('intval', explode(',', $payroll_detIds)));
				        $query .= " AND `id` IN ($ids)";
				    }

				    $net_salary = 0;
				    $salaryStmt = $conn->prepare($query);
				    if ($payroll_detId) {
				        $salaryStmt->bind_param("ii", $payrollId, $payroll_detId);
				    } else {
				        $salaryStmt->bind_param("i", $payrollId);
				    }
				    $salaryStmt->execute();
				    $salaryResult = $salaryStmt->get_result();

				    while ($row = $salaryResult->fetch_assoc()) {
				        $net_salary += $row['net_salary'];
				    }

				    // Check if bank has sufficient balance
				    if ($balance < $net_salary) {
				        $result['msg'] = 'Insufficient bank balance. Required: ' . number_format($net_salary, 2) . ', Available: ' . number_format($balance, 2);
				        $result['error'] = true;
				        echo json_encode($result);
				        exit();
				    }

				    // Start MySQL transaction
				    $conn->begin_transaction();

				    // Update payroll details based on payroll_detId or bulk IDs
				    $detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `pay_date` = ?, `paid_by` = ?, `paid_through` = ?, `bank_id` = ? WHERE `payroll_id` = ? AND `status` = 'Approved'";

				    if ($payroll_detId) {
				        $detailsQuery .= " AND `id` = ?";
				        $detailsStmt = $conn->prepare($detailsQuery);
				        $detailsStmt->bind_param("sssssii", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId, $payroll_detId);
				    } else if ($payroll_detIds) {
				        $ids = implode(',', array_map('intval', explode(',', $payroll_detIds)));
				        $detailsQuery .= " AND `id` IN ($ids)";
				        $detailsStmt = $conn->prepare($detailsQuery);
				        $detailsStmt->bind_param("sssssi", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId);
				    } else {
				        $detailsStmt = $conn->prepare($detailsQuery);
				        $detailsStmt->bind_param("sssssi", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId);
				    }

				    if (!$detailsStmt->execute()) {
				        throw new Exception("Failed to update payroll details: " . $detailsStmt->error);
				    }

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

				    $result['msg'] = 'Payroll paid successfully. Amount: ' . number_format($net_salary, 2);
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
			} else if($_GET['endpoint'] == 'rejectPayroll') {
				try {
				    $post = escapePostData($_POST);
				    $status = 'Rejected';
				    $payroll_detId = isset($post['payroll_detId']) ? $post['payroll_detId'] : null;
				    $payroll_detIds = isset($post['payroll_detIds']) ? $post['payroll_detIds'] : null;

				    check_auth('manage_payroll');

				    // Start MySQL transaction
				    $conn->begin_transaction();

				    // Update payroll details status to rejected
				    if ($payroll_detId) {
				        $detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` = ? AND `status` = 'Approved'";
				        $detailsStmt = $conn->prepare($detailsQuery);
				        $detailsStmt->bind_param("sssi", $status, $_SESSION['user_id'], $updated_date, $payroll_detId);
				    } else if ($payroll_detIds) {
				        $ids = implode(',', array_map('intval', explode(',', $payroll_detIds)));
				        $detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` IN ($ids) AND `status` = 'Approved'";
				        $detailsStmt = $conn->prepare($detailsQuery);
				        $detailsStmt->bind_param("sss", $status, $_SESSION['user_id'], $updated_date);
				    }

				    if (!$detailsStmt->execute()) {
				        throw new Exception("Failed to reject payroll details: " . $detailsStmt->error);
				    }

				    // Commit transaction
				    $conn->commit();

				    $result['msg'] = 'Payroll rejected successfully';
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

            if ($_GET['endpoint'] === 'bank_accounts') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['bank_name', 'account', 'balance', 'status'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `bank_accounts` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`bank_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `account` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $bank_accounts = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `bank_accounts` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`bank_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `account` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($bank_accounts->num_rows > 0) {
			        while ($row = $bank_accounts->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $bank_accounts->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'expenses') {
                if (isset($_POST['order']) && isset($_POST['order'][0])) {
                    $orderColumnMap = ['added_date', 'fn_account_name', 'amount', 'payee_payer', 'bank_name', 'ref_number', 'status'];
                    $orderByIndex = (int)$_POST['order'][0]['column'];
                    $orderBy = $orderColumnMap[$orderByIndex] ?? 'added_date';
                    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
                } else {
                    $orderBy = 'added_date';
                    $order = 'DESC';
                }
                // Base query
                $query = "SELECT * FROM `fn_transactions` WHERE `type` = 'Expense'";
                // Add search functionality
                if ($searchParam) {
                    $query .= " AND (`fn_account_name` LIKE '%" . escapeStr($searchParam) . "%' OR `payee_payer` LIKE '%" . escapeStr($searchParam) . "%' OR `bank_name` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_number` LIKE '%" . escapeStr($searchParam) . "%')";
                }
                // Add ordering
                $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";
                // Execute query
                $expenses = $GLOBALS['conn']->query($query);
                // Count total records for pagination
                $countQuery = "SELECT COUNT(*) as total FROM `fn_transactions` WHERE `type` = 'Expense'";
                if ($searchParam) {
                    $countQuery .= " AND (`fn_account_name` LIKE '%" . escapeStr($searchParam) . "%' OR `payee_payer` LIKE '%" . escapeStr($searchParam) . "%' OR `bank_name` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_number` LIKE '%" . escapeStr($searchParam) . "%')";
                }
                $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
                $totalRecords = $totalRecordsResult->fetch_assoc()['total'];
                if ($expenses->num_rows > 0) {
                    while ($row = $expenses->fetch_assoc()) {
                        $result['data'][] = $row;
                    }
                    $result['iTotalRecords'] = $totalRecords;
                    $result['iTotalDisplayRecords'] = $totalRecords;
                    $result['msg'] = $expenses->num_rows . " records were found.";
                } else {
                    $result['msg'] = "No records found";
                }
            } else if ($_GET['endpoint'] === 'income') {
                if (isset($_POST['order']) && isset($_POST['order'][0])) {
                    $orderColumnMap = ['added_date', 'fn_account_name', 'amount', 'payee_payer', 'bank_name', 'ref_number', 'status'];
                    $orderByIndex = (int)$_POST['order'][0]['column'];
                    $orderBy = $orderColumnMap[$orderByIndex] ?? 'added_date';
                    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
                } else {
                    $orderBy = 'added_date';
                    $order = 'DESC';
                }
                // Base query
                $query = "SELECT * FROM `fn_transactions` WHERE `type` = 'Income'";
                // Add search functionality
                if ($searchParam) {
                    $query .= " AND (`fn_account_name` LIKE '%" . escapeStr($searchParam) . "%' OR `payee_payer` LIKE '%" . escapeStr($searchParam) . "%' OR `bank_name` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_number` LIKE '%" . escapeStr($searchParam) . "%')";
                }
                // Add ordering
                $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";
                // Execute query
                $income = $GLOBALS['conn']->query($query);
                // Count total records for pagination
                $countQuery = "SELECT COUNT(*) as total FROM `fn_transactions` WHERE `type` = 'Income'";
                if ($searchParam) {
                    $countQuery .= " AND (`fn_account_name` LIKE '%" . escapeStr($searchParam) . "%' OR `payee_payer` LIKE '%" . escapeStr($searchParam) . "%' OR `bank_name` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_number` LIKE '%" . escapeStr($searchParam) . "%')";
                }
                $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
                $totalRecords = $totalRecordsResult->fetch_assoc()['total'];
                if ($income->num_rows > 0) {
                    while ($row = $income->fetch_assoc()) {
                        $result['data'][] = $row;
                    }
                    $result['iTotalRecords'] = $totalRecords;
                    $result['iTotalDisplayRecords'] = $totalRecords;
                    $result['msg'] = $income->num_rows . " records were found.";
                } else {
                    $result['msg'] = "No records found";
                }
            
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['added_date', 'fn_account_name', 'amount', 'payee_payer', 'bank_name', 'ref_number', 'status'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? 'added_date';
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				} else {
				    $orderBy = 'added_date';
				    $order = 'DESC';
				}
			    
			    // Base query
			    $query = "SELECT * FROM `fn_transactions` WHERE `type` = 'Expense'";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`fn_account_name` LIKE '%" . escapeStr($searchParam) . "%' OR `payee_payer` LIKE '%" . escapeStr($searchParam) . "%' OR `bank_name` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_number` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $expenses = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `fn_transactions` WHERE `type` = 'Expense'";
			    if ($searchParam) {
			        $countQuery .= " AND (`fn_account_name` LIKE '%" . escapeStr($searchParam) . "%' OR `payee_payer` LIKE '%" . escapeStr($searchParam) . "%' OR `bank_name` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_number` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($expenses->num_rows > 0) {
			        while ($row = $expenses->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $expenses->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'approved_payrolls') {
				$month = isset($_POST['month']) ? $_POST['month'] : '';
				
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['staff_no', 'full_name', 'month', 'base_salary', 'net_salary', 'status'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? 'full_name';
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				} else {
				    $orderBy = 'full_name';
				}

			    // Base query for approved payrolls
			    $query = "SELECT `id`, `payroll_id`, `emp_id`, `staff_no`, `full_name`, `email`, `job_title`, `month`, `status`, `base_salary`, (`allowance` + `bonus` + `commission`) AS earnings, (`loan` + `advance` + `deductions`) AS `total_deductions`, `tax`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary, `pay_date`, `paid_by`, `paid_through` FROM `payroll_details` WHERE `status` = 'Approved'";

			    // Add month filter if provided
			    if ($month) {
			        $query .= " AND `month` LIKE '%" . escapeStr($month) . "%'";
			    }

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `job_title` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $payroll_details = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `payroll_details` WHERE `status` = 'Approved'";
			    if ($month) {
			        $countQuery .= " AND `month` LIKE '%" . escapeStr($month) . "%'";
			    }
			    if ($searchParam) {
			        $countQuery .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `job_title` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($payroll_details->num_rows > 0) {
			        while ($row = $payroll_details->fetch_assoc()) {
			        	$month = $row['month'];
			        	$month = date('F Y', strtotime($month));
			        	$row['month'] = $month;
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $payroll_details->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No approved payrolls found";
			    }
			}

			// Return the result as a JSON response
			echo json_encode($result);

			exit();

		} 


		// Get data
		else if($_GET['action'] == 'get') {
			if($_GET['endpoint'] == 'income') {
				$id = isset($_POST['id']) ? $_POST['id'] : null;
				$result = [ 'error' => true, 'msg' => '', 'data' => null ];
				if(!$id) {
					$result['msg'] = 'Missing income ID';
					echo json_encode($result);
					exit();
				}
				$income = get_data('fn_transactions', ['id' => $id, 'type' => 'Income']);
				if($income) {
					$result['data'] = $income[0];
					$result['error'] = false;
				} else {
					$result['msg'] = 'Income not found';
				}
				echo json_encode($result);
				exit();
			}

			if ($_GET['endpoint'] === 'bank_account') {
				json(get_data('bank_accounts', array('id' => $_POST['id']))[0]);
			} else if ($_GET['endpoint'] === 'expense') {
				json(get_data('fn_transactions', array('id' => $_POST['id']))[0]);
			} else if ($_GET['endpoint'] === 'financial_accounts_expense') {
				$data = '<option value="">Select Financial Account</option>';
				$accounts = get_data('financial_accounts', ['type' => 'Expense', 'status' => 'Active']);
				if($accounts) {
					foreach($accounts as $account) {
						$data .= '<option value="'.$account['id'].'">'.$account['name'].'</option>';
					}
				}
				echo $data;
			} else if ($_GET['endpoint'] === 'bank_accounts_for_payment') {
				$data = '<option value="">Select bank account</option>';
				$banks = get_data('bank_accounts', ['status' => 'Active']);
				if($banks) {
					foreach($banks as $bank) {
						$data .= '<option value="'.$bank['id'].'">'.$bank['bank_name'].' - '.$bank['account'].' (Balance: '.number_format($bank['balance'], 2).')</option>';
					}
				}
				echo $data;
			}
		}

		// Search data
		else if($_GET['action'] == 'search') {
			

			exit();
		}


		// Delete data
		else if($_GET['action'] == 'delete') {
			if ($_GET['endpoint'] === 'bank_account') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('delete_bank_accounts');
				    $deleted = $accountsClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Bank account has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'expense') {
				try {
				    // Get expense data before deletion
				    $post = escapePostData($_POST);
				    $expense = get_data('fn_transactions', ['id' => $post['id']]);
				    
				    if(!$expense) {
				        $result['msg'] = 'Expense not found';
				        $result['error'] = true;
				        echo json_encode($result);
				        exit();
				    }
				    
				    $expense = $expense[0];
				    check_auth('delete_expenses');
				    
				    // Start transaction
				    $GLOBALS['conn']->begin_transaction();
				    
				    try {
				        // Delete expense record
				        $deleted = $transactionsClass->delete($post['id']);
				        
				        // Restore bank balance
				        $bankInfo = get_data('bank_accounts', ['id' => $expense['bank_id']]);
				        if($bankInfo) {
				            $newBalance = $bankInfo[0]['balance'] + $expense['amount'];
				            $accountsClass->update($expense['bank_id'], ['balance' => $newBalance]);
				        }
				        
				        $GLOBALS['conn']->commit();
				        
				        if($deleted) {
				            $result['msg'] = 'Expense has been deleted successfully';
				            $result['error'] = false;
				        } else {
				            $result['msg'] = 'Something went wrong, please try again';
				            $result['error'] = true;
				        }
				    } catch (Exception $e) {
				        $GLOBALS['conn']->rollback();
				        throw $e;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the delete method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'income') {
				$id = isset($_POST['id']) ? $_POST['id'] : null;
				$result = [ 'error' => true, 'msg' => '' ];
				if(!$id) {
					$result['msg'] = 'Missing income ID';
					echo json_encode($result);
					exit();
				}
				check_auth('delete_income');
				$income = get_data('fn_transactions', ['id' => $id, 'type' => 'Income']);
				if(!$income) {
					$result['msg'] = 'Income not found';
					echo json_encode($result);
					exit();
				}
				$income = $income[0];
				$bankInfo = get_data('bank_accounts', ['id' => $income['bank_id']]);
				if(!$bankInfo) {
					$result['msg'] = 'Bank not found';
					echo json_encode($result);
					exit();
				}
				$bankInfo = $bankInfo[0];
				$GLOBALS['conn']->begin_transaction();
				try {
					// Delete income record
					$deleted = $transactionsClass->delete($id);
					// Restore bank balance (subtract income amount)
					$newBalance = $bankInfo['balance'] - $income['amount'];
					$accountsClass->update(['balance' => $newBalance], ['id' => $income['bank_id']]);
					$GLOBALS['conn']->commit();
					if($deleted) {
						$result['msg'] = 'Income deleted successfully';
						$result['error'] = false;
					} else {
						$result['msg'] = 'Failed to delete income';
					}
				} catch(Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: Something went wrong';
					$result['sql_error'] = $e->getMessage();
				}
				echo json_encode($result);
				exit();
			}

			exit();
		}
	}
}

?>