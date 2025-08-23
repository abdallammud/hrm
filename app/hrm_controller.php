<?php
require('init.php');

$myUserId = $_SESSION['user_id'];
if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'save') {
			if($_GET['endpoint'] == 'employee') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    $data = array();

				    $employeeData = $post;
				    unset($employeeData['degree']);
				    unset($employeeData['institution']);
				    unset($employeeData['startYear']);
				    unset($employeeData['endYear']);

					// Unset project and budget codes 
					unset($employeeData['project']);
					unset($employeeData['project_id']);
					unset($employeeData['budget_code']);

				    foreach ($employeeData as $index => $value) {
				    	$data[$index] = isset($employeeData[$index]) ? $employeeData[$index]: "";
				    }

				    $data['added_by'] = $_SESSION['user_id'];

				    check_exists('employees', ['full_name' => $post['full_name'], 'email' => $post['email']]);
				    check_auth('create_employees');

					// var_dump($data);

				    // Call the create method for employee
				    $result['id'] = $employeeClass->create($data);
					$emp_id = $result['id'];
					if(isset($post['project_id']) && is_array($post['project_id'])) {
						$employeeClass->assignProjects($emp_id, $post['project_id']);
					}

					if(isset($post['budget_code']) && is_array($post['budget_code'])) {
						$employeeClass->assignBudgetCodes($emp_id, $post['budget_code']);
					}

				    // If the employee was created successfully, handle salary and education
				    if ($result['id']) {
				    	// Handle staff number
				    	if(!isset($post['staff_no']) || $post['staff_no'] == return_setting('staff_prefix')) {
				    		$staff_no = return_setting('staff_prefix').$result['id'];
				    		$staffNo = array('staff_no' => $staff_no);
				    		$employeeClass->update($result['id'], $staffNo);
				    	}
				        // Education data
				        $degree 		= isset($post['degree']) ? $post['degree'] : [];
				        $institution 	= isset($post['institution']) ? $post['institution'] : [];
				        $startYear 		= isset($post['startYear']) ? $post['startYear'] : [];
				        $endYear 		= isset($post['endYear']) ? $post['endYear'] : [];
				        if (is_array($degree) && count($degree) > 0) {
				            foreach ($degree as $index => $value) {
				                $degree         = escapeStr($degree[$index]);
				                $institution    = escapeStr($institution[$index]);
				                $startYear      = escapeStr($startYear[$index]);
				                $endYear        = escapeStr($endYear[$index]);

				                $educationData = array(
				                    'employee_id'      => $result['id'],
				                    'degree'           => $degree,
				                    'institution'      => $institution,
				                    'start_year'       => $startYear,
				                    'graduation_year'  => $endYear,
				                );

				                // Create education records
				                $educationClass->create($educationData);
				            }
				        }

				        $password = password_hash($post['phone_number'], PASSWORD_DEFAULT);

				        // Create user
				        $userData = array(
					        'full_name' => $post['full_name'],
					        'phone'   	=> $post['phone_number'],
					        'email'     => $post['email'],
					        'emp_id'    => $result['id'],
					        'branch_id'         => $post['branch_id'],
					        'username'  	=> usernameFromEmail($post['email']),
					        'password'      => $password,
					        'role'     		=> 'employee',
					        'added_by' 		=> $_SESSION['user_id']
					    );

					    // $user_id = $userClass->create($userData);

				        // Commit the transaction if everything is successful
				        $GLOBALS['conn']->commit();

				        // Return success response
				        $result['msg'] = 'Employee created successfully';
				        $result['error'] = false;
				    } else {
				        // If employee creation failed, roll back the transaction
				        $GLOBALS['conn']->rollback();
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }
				} catch (Exception $e) {
				    // If any exception occurs, rollback the transaction
				    $GLOBALS['conn']->rollback();

				    // Return error response
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'upload_employees') {
				try {
				    $result = ['error' => false, 'msg' => '', 'errors' => '', 'progress' => 0, 'total' => 0, 'processed' => 0];

				    check_auth('create_employees'); // Authorization check

				    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
				        $fileTmpPath = $_FILES['file']['tmp_name'];
				        $fileName = $_FILES['file']['name'];
				        $fileSize = $_FILES['file']['size'];
				        $fileType = $_FILES['file']['type'];

				        // Validate file type and size
				        if ($fileType != 'text/csv') {
				            $result['error'] = true;
				            $result['msg'] = "Invalid file type. Please upload a valid CSV file.";
				            echo json_encode($result);
				            exit();
				        }

				        // First pass: Count total rows for progress tracking
				        $totalRows = 0;
				        if (($file = fopen($fileTmpPath, 'r')) !== false) {
				            while (fgetcsv($file, 1000, ',') !== false) {
				                $totalRows++;
				            }
				            fclose($file);
				            $totalRows--; // Subtract header row
				        }

				        $result['total'] = $totalRows;

				        if (($file = fopen($fileTmpPath, 'r')) !== false) {
				            $row = 0;
				            $processedCount = 0;
				            $successCount = 0;
				            $errorCount = 0;
				            $batchSize = 50; // Process in batches for better performance
				            $batchData = [];
				            
				            // Prepare entity caches to reduce database queries
				            $entityCache = [
				                'branches' => [],
				                'states' => [],
				                'locations' => [],
				                'designations' => [],
				                'contract_types' => [],
				                'budget_codes' => []
				            ];

				            while (($line = fgetcsv($file, 1000, ',')) !== false) {
				                $row++;
				                if ($row == 1) continue; // Skip header row

				                $processedCount++;

				                // Ensure the row has the correct number of columns
				                if (count($line) < 27) {
				                    $result['errors'] .= "Skipping invalid row at line $row: ";
				                    $errorCount++;
				                    continue;
				                }

				                list(
				                    $staff_no, $full_name, $phone_number, $email, $gender,
				                    $national_id, $date_of_birth, $city, $address,
				                    $payment_bank, $payment_account, $branch, 
				                    $designation, $state, $location, $hire_date,
				                    $contract_start, $contract_end, $contract_type, $salary,
				                    $tax_exempt, $budget_codes, $projects, $moh_contract, $work_days,
				                    $work_hours, $grade, $seniority
				                ) = array_map('escapeStr', $line);

				                $position = $designation;

				                // Check for missing required fields
				                if (!$full_name) {
				                    $result['errors'] .= " Missing required fields at line $row.";
				                    $errorCount++;
				                    continue;
				                }

				                // Validate and format dates
				                try {
				                    $date_of_birth = date('Y-m-d', strtotime($date_of_birth));
				                    $hire_date = date('Y-m-d', strtotime($hire_date));
				                    $contract_start = date('Y-m-d', strtotime($contract_start));
				                    $contract_end = date('Y-m-d', strtotime($contract_end));
				                } catch (Exception $dateEx) {
				                    $result['errors'] .= " Invalid date format at line $row.";
				                    $errorCount++;
				                    continue;
				                }

								// Check for duplicate employees using prepared statement for better performance
				                $check_stmt = $GLOBALS['conn']->query("SELECT employee_id FROM employees WHERE full_name = '$full_name' AND phone_number = '$phone_number'");
				                if($check_stmt && $check_stmt->num_rows > 0) {
				                    $result['errors'] .= " Record already exists at line $row.";
				                    $errorCount++;
				                    continue;
				                }

								$budget_codes_array = explode(",", trim($budget_codes));
								$projects_array = explode(",", trim($projects));

				                // Use cached entities or create new ones
				                $branch_id = getCachedOrCreateEntity('branches', $branch, $myUserId, $branchClass, $entityCache);
				                $state_id = getCachedOrCreateEntity('states', $state, $myUserId, $statesClass, $entityCache);
				                $location_id = getCachedOrCreateEntity('locations', $location, $myUserId, $locationsClass, $entityCache);
				                $designation_id = getCachedOrCreateEntity('designations', $designation, $myUserId, $designationsClass, $entityCache);
				                $contract_type_id = getCachedOrCreateEntity('contract_types', $contract_type, $myUserId, $contractTypesClass, $entityCache);
				                // $budget_code_id = getCachedOrCreateEntity('budget_codes', $budget_codes, $myUserId, $budgetCodesClass, $entityCache);

				                // Add to batch
				                $batchData[] = [
				                    'full_name' => $full_name,
				                    'phone_number' => $phone_number,
				                    'email' => $email,
				                    'gender' => $gender,
				                    'staff_no' => $staff_no,
				                    'national_id' => $national_id,
				                    'date_of_birth' => $date_of_birth,
				                    'state_id' => $state_id,
				                    'state' => $state,
				                    'city' => $city,
				                    'address' => $address,
				                    'branch_id' => $branch_id,
				                    'branch' => $branch,
				                    'location_id' => $location_id,
				                    'location_name' => $location,
				                    'position' => $position,
				                    'designation' => $designation,
				                    'hire_date' => $hire_date,
				                    'contract_start' => $contract_start,
				                    'contract_end' => $contract_end,
				                    'work_days' => $work_days,
				                    'work_hours' => $work_hours,
				                    'contract_type' => $contract_type,
				                    'salary' => $salary,
				                    // 'budget_code' => $budget_codes,
									// 'project' => $projects,
				                    'moh_contract' => $moh_contract,
				                    'payment_bank' => $payment_bank,
				                    'payment_account' => $payment_account,
				                    'grade' => $grade,
				                    'tax_exempt' => $tax_exempt,
				                    'seniority' => $seniority,
									'budget_codes_array' => $budget_codes_array,
									'projects_array' => $projects_array,
				                    'row_number' => $row
				                ];

								// var_dump($batchData);
								// exit;

				                // Process batch when it reaches the batch size
				                if (count($batchData) >= $batchSize) {
				                    $batchResult = processBatchEmployees($batchData, $employeeClass, $myUserId);
				                    $successCount += $batchResult['success'];
				                    $errorCount += $batchResult['errors'];
				                    if (!empty($batchResult['error_messages'])) {
				                        $result['errors'] .= $batchResult['error_messages'];
				                    }
				                    $batchData = [];
				                }
				            }

				            // Process remaining batch
				            if (!empty($batchData)) {
				                $batchResult = processBatchEmployees($batchData, $employeeClass, $myUserId);
				                $successCount += $batchResult['success'];
				                $errorCount += $batchResult['errors'];
				                if (!empty($batchResult['error_messages'])) {
				                    $result['errors'] .= $batchResult['error_messages'];
				                }
				            }

				            fclose($file);
				            
				            $result['processed'] = $processedCount;
				            $result['success_count'] = $successCount;
				            $result['error_count'] = $errorCount;
				            $result['progress'] = 100;
				            
				            if ($successCount > 0) {
				                $result['msg'] = "Upload completed. Successfully processed $successCount employees.";
				                if ($errorCount > 0) {
				                    $result['msg'] .= " $errorCount records had errors.";
				                }
				            } else {
				                $result['error'] = true;
				                $result['msg'] = "No employees were successfully processed.";
				            }
				        } else {
				            throw new Exception("File read error.");
				        }
				    } else {
				        throw new Exception("Please select a file.");
				    }
				} catch (Exception $e) {
				    $result['error'] = true;
				    $result['msg'] = $e->getMessage();
				    error_log($e->getMessage());
				}

				echo json_encode($result);
			} else if($_GET['endpoint'] == 'folder') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					
					check_auth('manage_employee_docs'); // Same auth as adding employee
					
					$data = array(
						'name' => $post['name'],
						'created_by' => $_SESSION['user_id'],
						'created_at' => date('Y-m-d H:i:s')
					);
					
					// Insert into folders table
					$sql = "INSERT INTO folders (name, created_by, created_at) VALUES (?, ?, ?)";
					$stmt = $GLOBALS['conn']->prepare($sql);
					$stmt->bind_param("sis", $data['name'], $data['created_by'], $data['created_at']);
					
					if ($stmt->execute()) {
						$GLOBALS['conn']->commit();
						$result['msg'] = 'Folder created successfully';
						$result['error'] = false;
					} else {
						throw new Exception("Error creating folder");
					}
					
				} catch (Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: ' . $e->getMessage();
					$result['error'] = true;
				}
				
				echo json_encode($result);
				exit();
			} else if($_GET['endpoint'] == 'doc_types') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);

					check_auth('manage_employee_docs');
					
					// Check if type already exists
					$check_sql = "SELECT * FROM document_types WHERE name = '".$post['name']."'";
					$check_exists = $GLOBALS['conn']->query($check_sql);
					if($check_exists->num_rows > 0) {
						$result['msg'] = 'Document type already exists';
						$result['error'] = true;
					} else {
						$data = array(
							'name' => $post['name'],
							'created_by' => $_SESSION['user_id'],
							'updated_by' => $_SESSION['user_id']
						);
						
						$columns = implode(", ", array_keys($data));
						$values = "'" . implode("', '", array_values($data)) . "'";
						$sql = "INSERT INTO document_types ($columns) VALUES ($values)";
						
						if($GLOBALS['conn']->query($sql)) {
							$GLOBALS['conn']->commit();
							$result['msg'] = 'Document type added successfully';
							$result['error'] = false;
						} else {
							throw new Exception("Error adding document type");
						}
					}
				} catch(Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: Something went wrong';
					$result['error'] = true;
				}
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'emp_docs') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					
					// Handle file upload
					$target_dir = '../assets/docs/employee/';
					if (!file_exists($target_dir)) {
						mkdir($target_dir, 0777, true);
					}

					check_auth('create_employee_docs');

					$file = $_FILES['docFile'];
					$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
					$file_name = uniqid() . '.' . $file_ext;
					$target_file = $target_dir . $file_name;

					if (move_uploaded_file($file['tmp_name'], $target_file)) {
						$employeeInfo = get_data('employees', ['employee_id' => $post['employee_id']])[0];
						if (!$employeeInfo) {
							throw new Exception("Employee not found");
						}
						
						$data = array(
							'name' => $post['docName'],
							'folder_id' => $post['docFolder'],
							'folder_name' => $post['docFolderName'],
							'type_id' => $post['docType'],
							'type_name' => $post['docTypeName'],
							'emp_id' => $post['employee_id'],
							'full_name' => $employeeInfo['full_name'],
							'phone' => '45444',//$employeeInfo['phone'],
							'email' => $employeeInfo['email'],
							'expiration_date' => $post['expirationDate'],
							'document' => $file_name,
							'created_by' => $_SESSION['user_id'],
							'updated_by' => $_SESSION['user_id']
						);
						
						$result['id'] = $empDocClass->create($data);
						$GLOBALS['conn']->commit();
						// If the branch is created successfully, return a success message
						if($result['id']) {
							$result['msg'] = 'Document added successfully';
							$result['error'] = false;
						} else {
							$result['msg'] = 'Something went wrong, please try again';
							$result['error'] = true;
						}
					} else {
						throw new Exception("Error uploading file");
					}
					
				} catch(Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: ' . $e->getMessage();
					$result['error'] = true;
				}
				echo json_encode($result);
				exit();
			} else if($_GET['endpoint'] == 'award') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    
				    check_auth('manage_awards');
				    
				    // Get employee details
				    $emp_query = "SELECT * FROM employees WHERE employee_id = '{$post['employee_id']}'";
				    $emp_result = $GLOBALS['conn']->query($emp_query);
				    
				    if ($emp_result->num_rows === 0) {
				        throw new Exception('Invalid employee selected');
				    }
				    
				    $employee = $emp_result->fetch_assoc();
				    
				    // Get award type name
				    $type_query = "SELECT name FROM award_types WHERE id = '{$post['award_type']}'";
				    $type_result = $GLOBALS['conn']->query($type_query);
				    
				    if ($type_result->num_rows === 0) {
				        throw new Exception('Invalid award type selected');
				    }
				    
				    $award_type_name = $type_result->fetch_assoc()['name'];
				    
				    // Prepare data
				    $data = array(
				        'award_type' => $award_type_name,
				        'type_id' => $post['award_type'],
				        'emp_id' => $post['employee_id'],
				        'full_name' => $employee['full_name'],
				        'phone_number' => $employee['phone_number'],
				        'staff_no' => $employee['staff_no'],
				        'email' => $employee['email'],
				        'gift' => $post['gift'],
				        'award_date' => $post['award_date'],
				        'added_by' => $_SESSION['user_id']
				    );
				    
				    // Insert into database
				    $columns = implode(", ", array_keys($data));
				    $values = "'" . implode("', '", array_values($data)) . "'";
				    $sql = "INSERT INTO employee_awards ($columns) VALUES ($values)";
				    
				    if ($GLOBALS['conn']->query($sql)) {
				        $result['id'] = $GLOBALS['conn']->insert_id;
				        $GLOBALS['conn']->commit();
				        $result['msg'] = 'Award added successfully';
				        $result['error'] = false;
				    } else {
				        throw new Exception("Error adding award: " . $GLOBALS['conn']->error);
				    }
				    
				} catch (Exception $e) {
				    // If any exception occurs, rollback the transaction
				    $GLOBALS['conn']->rollback();
				    
				    // Return error response
				    $result['msg'] = 'Error: ' . $e->getMessage();
				    $result['error'] = true;
				}
				
				// Return the result as a JSON response
				echo json_encode($result);
				exit();
			}

			exit();
		} 


		// Update data
		else if($_GET['action'] == 'update') {
			$updated_date = date('Y-m-d H:i:s');
			if ($_GET['endpoint'] == 'employee') {
				try {
					/* ---------- 1. Start transaction ---------- */
					$GLOBALS['conn']->begin_transaction();

					/* ---------- 2. Sanitize incoming data ---------- */
					$post  = escapePostData($_POST);
					$empId = (int)($post['employee_id'] ?? 0);

					if (!$empId) {
						throw new Exception('Employee id missing.');
					}

					/* ---------- 3. Build the employees table payload ---------- */
					$employeeData = $post;

					// Remove everything that does NOT belong to the employees table
					unset(
						$employeeData['employee_id'],
						$employeeData['degree'],
						$employeeData['institution'],
						$employeeData['startYear'],
						$employeeData['endYear'],
						$employeeData['project'],
						$employeeData['project_id'],
						$employeeData['budget_code']
					);

					// Optional meta
					$employeeData['updated_by']   = $_SESSION['user_id'];
					$employeeData['updated_date'] = date('Y-m-d H:i:s');   // or $updated_date if you already have it

					/* ---------- 4. Check constraints ---------- */
					check_exists(
						'employees',
						['full_name' => $post['full_name'], 'email' => $post['email']],
						['employee_id' => $empId, 'staff_no' => $post['staff_no'] ?? null]
					);
					check_auth('edit_employees');

					/* ---------- 5. Update the employee row ---------- */
					$updated = $employeeClass->update($empId, $employeeData);
					if (!$updated) {
						throw new Exception('Unable to update employee record.');
					}

					/* ---------- 6. Handle staff_no if empty ---------- */
					if (empty($post['staff_no']) || $post['staff_no'] == return_setting('staff_prefix')) {
						$staffNo = return_setting('staff_prefix') . $empId;
						$employeeClass->update($empId, ['staff_no' => $staffNo]);
					}

					/* ---------- 7. Sync projects ---------- */
					$projectIds = isset($post['project_id']) && is_array($post['project_id'])
						? array_map('intval', $post['project_id'])
						: [];

					$employeeClass->assignProjects($empId, $projectIds);

					/* ---------- 8. Sync budget codes ---------- */
					$budgetCodes = isset($post['budget_code']) && is_array($post['budget_code'])
						? $post['budget_code']
						: [];

					$employeeClass->assignBudgetCodes($empId, $budgetCodes);

					/* ---------- 9. Sync education ---------- */
					$educationClass->delete($empId);   // Remove all existing rows

					$degrees      = $post['degree']      ?? [];
					$institutions = $post['institution'] ?? [];
					$startYears   = $post['startYear']   ?? [];
					$endYears     = $post['endYear']     ?? [];

					if (is_array($degrees) && count($degrees)) {
						foreach ($degrees as $idx => $deg) {
							$educationClass->create([
								'employee_id'     => $empId,
								'degree'          => escapeStr($deg),
								'institution'     => escapeStr($institutions[$idx] ?? ''),
								'start_year'      => escapeStr($startYears[$idx]  ?? ''),
								'graduation_year' => escapeStr($endYears[$idx]    ?? ''),
							]);
						}
					}

					/* ---------- 10. Update / create user account ---------- */
					$password = password_hash($post['phone_number'], PASSWORD_DEFAULT);

					$userData = [
						'full_name'    => $post['full_name'],
						'phone'        => $post['phone_number'],
						'email'        => $post['email'],
						'emp_id'       => $empId,
						'branch_id'    => $post['branch_id'],
						'username'     => usernameFromEmail($post['email']),
						'password'     => $password,
						'role'         => 'employee',
						'updated_by'   => $_SESSION['user_id'],
						'updated_date' => date('Y-m-d H:i:s'),
					];

					$user = $employeeClass->getUser($empId);
					if ($user) {
						// $userClass->update($user[0]['id'], $userData);
					} else {
						// $userClass->create($userData);
					}

					/* ---------- 11. Finish ---------- */
					$GLOBALS['conn']->commit();

					$result = [
						'msg'   => 'Employee updated successfully',
						'error' => false,
					];
				} catch (Throwable $e) {
					$GLOBALS['conn']->rollback();

					$result = [
						'msg'       => 'Error: ' . $e->getMessage(),
						'sql_error' => $e->getMessage(),
						'error'     => true,
					];
				}

				echo json_encode($result);
			} else if ($_GET['endpoint'] == 'employee_avatar') {
			    // Ensure user has the correct permissions
			    check_auth('edit_employees');
			    
			    $image = '';
			    $uploadOk = false;
			    $employee_id = $_POST['employee_id'];

			    // Check if a file is uploaded
			    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
			        // Get file information
			        $target_dir = "../assets/images/avatars/";
			        $file_name = basename($_FILES["avatar"]["name"]);

			        // Generate a unique file name to prevent overwriting
			        $temp = explode(".", $_FILES["avatar"]["name"]);
			        $newfilename = round(microtime(true)) . '.' . end($temp);

			        $target_file = $target_dir . $newfilename;
			        $uploadOk = true;

			        // Check if the uploaded file is a valid image
			        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
			        if ($check === false) {
			            $result['error'] = true;
			            $result['msg'] = "Please select a valid image.";
			            echo json_encode($result);
			            exit();
			        }

			        // Check file size (max 5MB)
			        if ($_FILES["avatar"]["size"] > 5000000) {  // 5MB limit
			            $uploadOk = false;
			            $result['error'] = true;
			            $result['msg'] = "File is too large. Maximum size is 5MB.";
			            echo json_encode($result);
			            exit();
			        }

			        // Allow certain file formats
			        $allowed_extensions = array("jpg", "jpeg", "png", "gif", "webp");
			        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			        if (!in_array($file_extension, $allowed_extensions)) {
			            $uploadOk = false;
			            $result['error'] = true;
			            $result['msg'] = "Invalid file type. Please upload an image (jpg, jpeg, png, gif, webp).";
			            echo json_encode($result);
			            exit();
			        }

			        // Proceed with uploading the image if everything is ok
			        if ($uploadOk) {
			            $image = $newfilename;
			            if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
			                $result['error'] = true;
			                $result['msg'] = "Something went wrong while uploading the image. Please try again.";
			                echo json_encode($result);
			                exit();
			            }
			        } else {
			            $result['error'] = true;
			            $result['msg'] = "Could not upload the file. Please try again.";
			            echo json_encode($result);
			            exit();
			        }
			    } else {
			        $result['error'] = true;
			        $result['msg'] = "No file uploaded or there was an upload error.";
			        echo json_encode($result);
			        exit();
			    }
			    $data = ['avatar' => $image];
			    $result['id'] = $employeeClass->update($_POST['employee_id'], $data);
			 	
			    // Return success response
			    $result['msg'] = 'Employee avatar updated successfully';
			    $result['error'] = false;
			    echo json_encode($result);
			} else if($_GET['endpoint'] == 'folder') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					
					check_auth('manage_employee_docs'); // Same auth as adding employee
					
					$data = array(
						'name' => $post['name'],
						'updated_at' => date('Y-m-d H:i:s')
					);
					
					// Update folder
					$sql = "UPDATE folders SET name = ?, updated_at = ? WHERE id = ? AND deleted_at IS NULL";
					$stmt = $GLOBALS['conn']->prepare($sql);
					$stmt->bind_param("ssi", $data['name'], $data['updated_at'], $post['id']);
					
					if ($stmt->execute()) {
						$GLOBALS['conn']->commit();
						$result['msg'] = 'Folder updated successfully';
						$result['error'] = false;
					} else {
						throw new Exception("Error updating folder");
					}
					
				} catch (Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: ' . $e->getMessage();
					$result['error'] = true;
				}
				
				echo json_encode($result);
				exit();
			} else if($_GET['endpoint'] == 'doc_types') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					
					check_auth('manage_employee_docs');
					// Check if type already exists
					$check_sql = "SELECT * FROM document_types WHERE name = '".$post['name']."' AND id != '".$post['id']."'";
					$check_exists = $GLOBALS['conn']->query($check_sql);
					if($check_exists->num_rows > 0) {
						$result['msg'] = 'Document type already exists';
						$result['error'] = true;
					} else {
						$data = array(
							'name' => $post['name'],
							'updated_by' => $_SESSION['user_id']
						);
						
						$updates = array();
						foreach($data as $key => $value) {
							$updates[] = "$key = '$value'";
						}
						$sql = "UPDATE document_types SET " . implode(", ", $updates) . " WHERE id = '".$post['id']."'";
						
						if($GLOBALS['conn']->query($sql)) {
							$GLOBALS['conn']->commit();
							$result['msg'] = 'Document type updated successfully';
							$result['error'] = false;
						} else {
							throw new Exception("Error updating document type");
						}
					}
				} catch(Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: Something went wrong';
					$result['error'] = true;
				}
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'folder_docs') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);

					check_auth('manage_employee_docs');
					
					$data = array(
						'name' => $post['docName4Edit'],
						'doc_type_id' => $post['docType4Edit'],
						'employee_id' => $post['employee4Edit'],
						'expiration_date' => $post['expirationDate4Edit'],
						'updated_by' => $_SESSION['user_id']
					);
					
					$updates = array();
					foreach($data as $key => $value) {
						$updates[] = "$key = '$value'";
					}
					$sql = "UPDATE documents SET " . implode(", ", $updates) . " WHERE id = '".$post['id']."'";
					
					if($GLOBALS['conn']->query($sql)) {
						$GLOBALS['conn']->commit();
						$result['msg'] = 'Document updated successfully';
						$result['error'] = false;
					} else {
						throw new Exception("Error updating document");
					}
				} catch(Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: Something went wrong';
					$result['error'] = true;
				}
				echo json_encode($result);
				exit();
			} else if($_GET['endpoint'] == 'award') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    
				    check_auth('manage_awards');
				    
				    // Validate award ID
				    if (!isset($post['id']) || empty($post['id'])) {
				        throw new Exception('Invalid award ID');
				    }
				    
				    // Get award type name
				    $type_query = "SELECT name FROM award_types WHERE id = '{$post['award_type']}'";
				    $type_result = $GLOBALS['conn']->query($type_query);
				    
				    if ($type_result->num_rows === 0) {
				        throw new Exception('Invalid award type selected');
				    }
				    
				    $award_type_name = $type_result->fetch_assoc()['name'];
				    
				    // Prepare data
				    $data = array(
				        'award_type' => $award_type_name,
				        'type_id' => $post['award_type'],
				        'gift' => $post['gift'],
				        'award_date' => $post['award_date'],
				        'status' => $post['status'],
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );
				    
				    // Update database
				    $updates = array();
				    foreach ($data as $key => $value) {
				        $updates[] = "`$key` = '$value'";
				    }
				    
				    $sql = "UPDATE employee_awards SET " . implode(", ", $updates) . " WHERE id = '{$post['id']}'";
				    
				    if ($GLOBALS['conn']->query($sql)) {
				        $GLOBALS['conn']->commit();
				        $result['msg'] = 'Award updated successfully';
				        $result['error'] = false;
				    } else {
				        throw new Exception("Error updating award: " . $GLOBALS['conn']->error);
				    }
				    
				} catch (Exception $e) {
				    // If any exception occurs, rollback the transaction
				    $GLOBALS['conn']->rollback();
				    
				    // Return error response
				    $result['msg'] = 'Error: ' . $e->getMessage();
				    $result['error'] = true;
				}
				
				// Return the result as a JSON response
				echo json_encode($result);
				exit();
			} 

			exit();
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

			if ($_GET['endpoint'] === 'employees') {
				$conn = $GLOBALS['conn'];

				// DataTables params
				$draw   = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
				$start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
				$length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
				$searchValue = $_POST['search']['value'] ?? '';

				// Custom filters
				$department = $_POST['department'] ?? '';
				$state      = $_POST['state'] ?? '';
				$location   = $_POST['location'] ?? '';
				$status     = $_POST['status'] ?? '';

				// Increase GROUP_CONCAT capacity (optional but recommended)
				$conn->query("SET SESSION group_concat_max_len = 8192");

				// Whitelist for order-by (DataTables sends columns[i][data]/[name])
				// Keys here MUST match the "data" keys you use in JS columns config.
				$orderable = [
					'staff_no'       => 'e.staff_no',
					'full_name'      => 'e.full_name',
					'phone_number'   => 'e.phone_number',
					'email'          => 'e.email',
					'gender'         => 'e.gender',
					'date_of_birth'  => 'e.date_of_birth',
					'state'          => 's.name',
					'city'           => 'e.city',              // you store city as text on employees
					'address'        => 'e.address',
					'branch'         => 'b.name',
					'location_name'  => 'l.name',
					'position'       => 'e.position',
					'project'        => 'project',             // alias in select
					'designation'    => 'e.designation',
					'hire_date'      => 'e.hire_date',
					'contract_start' => 'e.contract_start',
					'contract_end'   => 'e.contract_end',
					'work_days'      => 'e.work_days',
					'work_hours'     => 'e.work_hours',
					'budget_code'    => 'budget_code',         // alias in select
					'salary'         => 'e.salary',
					'moh_contract'   => 'e.moh_contract',
					'payment_bank'   => 'e.payment_bank',
					'payment_account'=> 'e.payment_account',
					'grade'          => 'e.grade',
					'tax_exempt'     => 'e.tax_exempt',
					'seniority'      => 'e.seniority',
					'status'         => 'e.status'
				];

				// Parse ordering from DataTables
				$orderSql = ' ORDER BY e.employee_id DESC ';
				if (!empty($_POST['order'][0])) {
					$colIdx = (int)$_POST['order'][0]['column'];
					$dir    = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
					// DataTables sends the column meta in columns[]
					$colDataKey = $_POST['columns'][$colIdx]['data'] ?? '';
					if (isset($orderable[$colDataKey])) {
						$orderSql = " ORDER BY {$orderable[$colDataKey]} {$dir} ";
					}
				}

				// Base FROM + JOINs
				$from = "
					FROM employees e
					LEFT JOIN states s        ON s.id = e.state_id
					LEFT JOIN locations l     ON l.id = e.location_id
					LEFT JOIN branches b      ON b.id = e.branch_id
					LEFT JOIN employee_projects ep  ON ep.emp_id = e.employee_id
					LEFT JOIN projects p             ON p.id = ep.project_id
					LEFT JOIN employee_budget_codes ebc ON ebc.emp_id = e.employee_id
					LEFT JOIN budget_codes bc           ON bc.id = ebc.code_id
					WHERE e.employee_id IS NOT NULL
				";

				// Dynamic WHERE (prepared)
				$where = '';
				$params = [];
				$types  = '';

				if ($department !== '') { $where .= " AND e.branch_id = ? ";   $params[] = $department; $types .= 'i'; }
				if ($state      !== '') { $where .= " AND e.state_id = ? ";    $params[] = $state;      $types .= 'i'; }
				if ($location   !== '') { $where .= " AND e.location_id = ? "; $params[] = $location;   $types .= 'i'; }
				if ($status     !== '') { $where .= " AND e.status = ? ";      $params[] = $status;     $types .= 's'; }
				// Default to Active when no status and no search (keep your original behavior)
				if ($status === '' && $searchValue === '') {
					$where .= " AND e.status = 'Active' ";
				}

				// Global search across multiple fields (including joined)
				if ($searchValue !== '') {
					$where .= " AND ( 
						e.staff_no      LIKE ? OR
						e.full_name     LIKE ? OR
						e.phone_number  LIKE ? OR
						e.email         LIKE ? OR
						e.address       LIKE ? OR
						e.designation   LIKE ? OR
						e.position      LIKE ? OR
						s.name          LIKE ? OR
						e.city          LIKE ? OR
						b.name          LIKE ? OR
						l.name          LIKE ? OR
						p.name          LIKE ? OR
						bc.name         LIKE ?
					)";
					for ($i=0; $i<13; $i++) { $params[] = "%{$searchValue}%"; $types .= 's'; }
				}

				// Main SELECT (NOTE: project and budget_code are aggregated here)
				$select = "
					SELECT
						e.employee_id,
						e.staff_no,
						e.full_name,
						e.phone_number,
						e.email,
						e.gender,
						e.date_of_birth,
						s.name           AS state,
						e.city           AS city,
						e.address,
						b.name           AS branch,
						l.name           AS location_name,
						e.position,
						GROUP_CONCAT(DISTINCT p.name  ORDER BY p.name  SEPARATOR ', ') AS project,
						e.designation,
						e.hire_date,
						e.contract_start,
						e.contract_end,
						e.work_days,
						e.work_hours,
						GROUP_CONCAT(DISTINCT bc.name ORDER BY bc.name SEPARATOR ', ') AS budget_code,
						e.salary,
						e.moh_contract,
						e.payment_bank,
						e.payment_account,
						e.grade,
						e.tax_exempt,
						e.seniority,
						e.status
					{$from}
					{$where}
					GROUP BY e.employee_id
					{$orderSql}
					LIMIT ?, ?
				";

				// recordsTotal (no filters, no search)
				$recordsTotalSql = "SELECT COUNT(*) AS cnt FROM employees e WHERE e.employee_id IS NOT NULL";
				$recordsTotalRes = $conn->query($recordsTotalSql);
				$recordsTotal = (int)($recordsTotalRes->fetch_assoc()['cnt'] ?? 0);

				// recordsFiltered (with filters + search BUT without limit; group by because of joins)
				$recordsFilteredSql = "
					SELECT COUNT(*) AS cnt FROM (
						SELECT e.employee_id
						{$from}
						{$where}
						GROUP BY e.employee_id
					) x
				";

				// Prepare filtered count
				$stmtCount = $conn->prepare($recordsFilteredSql);
				if ($types !== '') $stmtCount->bind_param($types, ...$params);
				$stmtCount->execute();
				$resCount = $stmtCount->get_result();
				$recordsFiltered = (int)($resCount->fetch_assoc()['cnt'] ?? 0);
				$stmtCount->close();

				// Prepare main data query
				$stmt = $conn->prepare($select);
				// Add limit params
				$paramsWithLimit = $params;
				$typesWithLimit  = $types . 'ii';
				$paramsWithLimit[] = $start;
				$paramsWithLimit[] = $length;

				$stmt->bind_param($typesWithLimit, ...$paramsWithLimit);
				$stmt->execute();
				$resultSet = $stmt->get_result();

				$data = [];
				while ($row = $resultSet->fetch_assoc()) {
					// Keep keys exactly as JS expects
					$data[] = $row;
				}
				$stmt->close();

				// DataTables standard response
				$out = [
					'draw'            => $draw,
					'recordsTotal'    => $recordsTotal,
					'recordsFiltered' => $recordsFiltered,
					'data'            => $data,

					// keep your legacy keys too (optional; remove if not needed)
					'status'               => 201,
					'error'                => false,
					'iTotalRecords'        => $recordsTotal,
					'iTotalDisplayRecords' => $recordsFiltered,
					'msg'                  => count($data) . " records returned."
				];

				header('Content-Type: application/json');
				echo json_encode($out);
				exit();
			} else if ($_GET['endpoint'] === 'folders') {
				$result = [];
				try {
					check_auth('manage_employee_docs');
					
					$search = isset($_POST['search']) ? escapeStr($_POST['search']) : '';
					$where = "";
					
					if($search != '') {
						$where = " AND name LIKE '%$search%'";
					}
					
					$sql = "SELECT * FROM folders  WHERE deleted_at IS NULL $where ORDER BY created_at DESC";
					
					$query = $GLOBALS['conn']->query($sql);
					$folders = array();
					
					if($query->num_rows > 0) {
						while($row = $query->fetch_assoc()) {
							$folders[] = $row;
						}
					}
					
					$result['data'] = $folders;
					$result['error'] = false;
					
				} catch(Exception $e) {
					$result['msg'] = $e->getMessage();
					$result['error'] = true;
				}
				
				echo json_encode($result);
				exit();
			} else if ($_GET['endpoint'] === 'doc_types') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `document_types` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $document_types = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `document_types` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($document_types->num_rows > 0) {
			        while ($row = $document_types->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $document_types->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'documents') {
				/* --------------------------
				* 1) Incoming parameters
				* -------------------------- */
				$folder_id   = isset($_POST['folder_id'])   ? (int)$_POST['folder_id']   : null;
				$employee_id = isset($_POST['employee_id']) ? (int)$_POST['employee_id'] : null;
				$searchParam = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
				$start       = isset($_POST['start'])  ? (int)$_POST['start']  : 0;
				$length      = isset($_POST['length']) ? (int)$_POST['length'] : 10;

				/* --------------------------
				* 2) Column mapping for ordering
				* -------------------------- */
				$orderColumnMap = [
					0 => 'ed.name',
					1 => 'e.full_name',
					2 => 'e.phone_number',
					3 => 'dt.name',
					4 => 'f.name',
					5 => 'ed.expiration_date',
					6 => 'ed.created_at'
				];
				$orderByIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 6;
				$orderBy      = $orderColumnMap[$orderByIndex] ?? 'ed.created_at';
				$orderDir     = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';

				/* --------------------------
				* 3) Base query components
				* -------------------------- */
				$baseJoin = "
					FROM employee_docs      ed
					JOIN employees          e  ON e.employee_id = ed.emp_id
					LEFT JOIN folders       f  ON f.id          = ed.folder_id
					LEFT JOIN document_types dt ON dt.id         = ed.type_id
				";

				$where  = ' WHERE 1=1 ';
				$params = [];
				$types  = '';

				if ($searchParam !== '') {
					$where .= " AND (
									ed.name     LIKE ?
								OR e.full_name LIKE ?
								OR e.phone_number LIKE ?
								)";
					$like   = "%$searchParam%";
					$params = array_merge($params, [$like, $like, $like]);
					$types  .= 'sss';
				}

				if ($folder_id) {
					$where  .= ' AND ed.folder_id = ?';
					$params[] = $folder_id;
					$types   .= 'i';
				}

				if ($employee_id) {
					$where  .= ' AND ed.emp_id = ?';
					$params[] = $employee_id;
					$types   .= 'i';
				}

				/* --------------------------
				* 4) Total (un-filtered) row count
				* -------------------------- */
				$totalQuery = "SELECT COUNT(*) AS total {$baseJoin}";
				$stmt = $GLOBALS['conn']->prepare($totalQuery);
				$stmt->execute();
				$totalRecords = (int)$stmt->get_result()->fetch_assoc()['total'];
				$stmt->close();

				/* --------------------------
				* 5) Filtered row count
				* -------------------------- */
				$filteredQuery = "SELECT COUNT(*) AS filtered {$baseJoin} {$where}";
				$stmt = $GLOBALS['conn']->prepare($filteredQuery);
				if ($params) {
					$stmt->bind_param($types, ...$params);
				}
				$stmt->execute();
				$filteredRecords = (int)$stmt->get_result()->fetch_assoc()['filtered'];
				$stmt->close();

				/* --------------------------
				* 6) Main data query
				* -------------------------- */
				$dataQuery = "
					SELECT
						ed.id,
						ed.name AS doc_name,
						ed.document,
						ed.expiration_date,
						ed.tags,
						ed.created_at,
						ed.updated_at,
						ed.created_by,
						ed.updated_by,

						e.employee_id,
						e.full_name,
						e.phone_number,
						e.email,

						f.id        AS folder_id,
						f.name      AS folder_name,
						f.status    AS folder_status,

						dt.id       AS type_id,
						dt.name     AS type_name,
						dt.status   AS type_status
					{$baseJoin}
					{$where}
					ORDER BY {$orderBy} {$orderDir}
					LIMIT ?, ?
				";

				$params[] = $start;
				$params[] = $length;
				$types   .= 'ii';

				$stmt = $GLOBALS['conn']->prepare($dataQuery);
				$stmt->bind_param($types, ...$params);
				$stmt->execute();
				$res = $stmt->get_result();

				$data = [];
				while ($row = $res->fetch_assoc()) {
					$data[] = $row;
				}
				$stmt->close();

				/* --------------------------
				* 7) Output
				* -------------------------- */
				echo json_encode([
					'draw'            => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
					'recordsTotal'    => $totalRecords,
					'recordsFiltered' => $filteredRecords,
					'data'            => $data
				]);
				exit;
			} else if ($_GET['endpoint'] === 'awards') {
				/* ---------- 1) Incoming parameters ---------- */
				$start       = isset($_POST['start'])  ? (int)$_POST['start']  : 0;
				$length      = isset($_POST['length']) ? (int)$_POST['length'] : 10;
				$searchParam = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

				/* ---------- 2) Column mapping for ordering ---------- */
				$orderColumnMap = [
					0 => 'e.full_name',
					1 => 'at.name',      // award type
					2 => 'ea.gift',
					3 => 'ea.award_date',
					4 => 'ea.status'
				];
				$orderByIndex = (int)($_POST['order'][0]['column'] ?? 3);
				$orderBy      = $orderColumnMap[$orderByIndex] ?? 'ea.award_date';
				$orderDir     = strtoupper($_POST['order'][0]['dir'] ?? 'DESC') === 'DESC' ? 'DESC' : 'ASC';

				/* ---------- 3) WHERE clause (search) ---------- */
				$where  = ' WHERE 1=1 ';
				$params = [];
				$types  = '';

				if ($searchParam !== '') {
					$where .= " AND (
									e.full_name LIKE ?
								OR at.name   LIKE ?
								OR ea.gift   LIKE ?
								)";
					$like   = "%$searchParam%";
					$params = [$like, $like, $like];
					$types  = 'sss';
				}

				/* ---------- 4) Count queries ---------- */
				// 4-a) total rows (before search)
				$totalQuery = "SELECT COUNT(*) AS total FROM employee_awards ea";
				$stmt = $GLOBALS['conn']->prepare($totalQuery);
				$stmt->execute();
				$totalRecords = (int)$stmt->get_result()->fetch_assoc()['total'];
				$stmt->close();

				// 4-b) filtered rows (after search)
				$filteredQuery = "
					SELECT COUNT(*) AS filtered
					FROM employee_awards ea
					JOIN employees   e  ON e.employee_id = ea.emp_id
					JOIN award_types t ON t.id         = ea.type_id
					{$where}
				";

				// echo $filteredQuery;
				$stmt = $GLOBALS['conn']->prepare($filteredQuery);
				if ($params) {
					$stmt->bind_param($types, ...$params);
				}
				$stmt->execute();
				$filteredRecords = (int)$stmt->get_result()->fetch_assoc()['filtered'];
				$stmt->close();

				/* ---------- 5) Main data query ---------- */
				$dataQuery = "
					SELECT
						ea.id,
						ea.gift,
						ea.award_date,
						ea.status,

						e.employee_id,
						e.full_name,
						e.staff_no,
						e.phone_number,
						e.email,

						t.id   AS type_id,
						t.name AS award_type
					FROM employee_awards ea
					JOIN employees   e  ON e.employee_id = ea.emp_id
					JOIN award_types t ON t.id         = ea.type_id
					{$where}
					ORDER BY {$orderBy} {$orderDir}
					LIMIT ?, ?
				";

				$params[] = $start;
				$params[] = $length;
				$types   .= 'ss';

				$stmt = $GLOBALS['conn']->prepare($dataQuery);
				$stmt->bind_param($types, ...$params);
				$stmt->execute();
				$res = $stmt->get_result();

				$data = [];
				while ($row = $res->fetch_assoc()) {
					$data[] = $row;
				}
				$stmt->close();

				/* ---------- 6) Output for DataTables ---------- */
				echo json_encode([
					'draw'            => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
					'recordsTotal'    => $totalRecords,
					'recordsFiltered' => $filteredRecords,
					'data'            => $data
				]);
				exit;
			}

			echo json_encode($result);

			exit();

		} 


		// Get data
		else if($_GET['action'] == 'get') {
			if ($_GET['endpoint'] === 'award') {
				check_auth('manage_awards');
				$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
				
				if (empty($id)) {
					$result['error'] = true;
					$result['msg'] = 'Invalid award ID';
					echo json_encode($result);
					exit();
				}
				
				$sql = "SELECT * FROM employee_awards WHERE id = '$id'";
				$query = $GLOBALS['conn']->query($sql);
				
				if ($query->num_rows > 0) {
					$result['error'] = false;
					$result['data'] = $query->fetch_assoc();
				} else {
					$result['error'] = true;
					$result['msg'] = 'Award not found';
				}
				
				echo json_encode($result);
				exit();
			} else if ($_GET['endpoint'] == 'company') {
				json(get_data('company', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] == 'branch') {
				json(get_data('branches', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] == 'doc_types') {
				$id = $_POST['id'];
				$sql = "SELECT * FROM document_types WHERE id = '$id'";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
			} else if ($_GET['endpoint'] == 'folder_docs') {
				$id = $_POST['id'];
				$sql = "SELECT * FROM documents WHERE id = '$id'";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
				exit();
			} else if ($_GET['endpoint'] == 'doc_types_list') {
				$sql = "SELECT id, name FROM document_types ORDER BY name ASC";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
				exit();
			} else if ($_GET['endpoint'] == 'employees_list') {
				$sql = "SELECT id, full_name FROM employees ORDER BY full_name ASC";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
				exit();
			}

			exit();
		}

		// Search data
		else if($_GET['action'] == 'search') {
			if($_GET['endpoint'] == 'employees') {
				$search = $_POST['search'];
				$sql = "SELECT id, full_name, phone_number FROM employees 
					   WHERE status = 'active' AND 
					   (full_name LIKE '%$search%' OR phone_number LIKE '%$search%' OR employee_id LIKE '%$search%')
					   ORDER BY full_name ASC LIMIT 10";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
				exit();
			} else if ($_GET['endpoint'] === 'employee4Select') {
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

							$text = $full_name;

							if($phone_number) {
								$text .= ", ".$phone_number;
							}

                    		$options .=  '<option value="'.$employee_id.'">'.$text.'</option>';
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
			} else if ($_GET['endpoint'] === 'awards') {
                // Load awards for DataTable
                if ($_GET['action'] === 'load') {
                    // DataTable server-side processing
                    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
                    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
                    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                    $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 3;
                    $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
                    
                    // Map DataTable column index to database column name
                    $columns = array(
                        0 => 'full_name',
                        1 => 'award_type',
                        2 => 'gift',
                        3 => 'award_date',
                        4 => 'status'
                    );
                    
                    $order_column_name = isset($columns[$order_column]) ? $columns[$order_column] : 'award_date';
                    
                    // Build query
                    $query = "SELECT ea.*, at.name as award_type FROM employee_awards ea 
                              LEFT JOIN award_types at ON ea.type_id = at.id ";
                    
                    // Add search condition if search term provided
                    if (!empty($search)) {
                        $query .= " WHERE ea.full_name LIKE '%$search%' OR at.name LIKE '%$search%' OR ea.gift LIKE '%$search%' ";
                    }
                    
                    // Count total records
                    $total_query = "SELECT COUNT(*) as total FROM employee_awards";
                    $total_result = $GLOBALS['conn']->query($total_query);
                    $total_records = $total_result->fetch_assoc()['total'];
                    
                    // Count filtered records
                    $filtered_query = $query;
                    $filtered_query = "SELECT COUNT(*) as total FROM ($filtered_query) as filtered";
                    $filtered_result = $GLOBALS['conn']->query($filtered_query);
                    $filtered_records = $filtered_result->fetch_assoc()['total'];
                    
                    // Add order and limit
                    $query .= " ORDER BY $order_column_name $order_dir LIMIT $start, $length";
                    
                    // Execute query
                    $result = $GLOBALS['conn']->query($query);
                    $data = array();
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $data[] = $row;
                        }
                    }
                    
                    // Prepare response
                    $response = array(
                        "draw" => $draw,
                        "recordsTotal" => $total_records,
                        "recordsFiltered" => $filtered_records,
                        "data" => $data
                    );
                    
                    echo json_encode($response);
                    exit();
                }
                // Save new award
                else if ($_GET['action'] === 'save') {
                    check_auth('manage_awards');
                    $response = array('error' => true, 'msg' => 'Failed to save award');
                    
                    // Get POST data
                    $employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
                    $award_type = isset($_POST['award_type']) ? intval($_POST['award_type']) : 0;
                    $gift = isset($_POST['gift']) ? $_POST['gift'] : '';
                    $award_date = isset($_POST['award_date']) ? $_POST['award_date'] : date('Y-m-d');
                    $description = isset($_POST['description']) ? $_POST['description'] : '';
                    
                    // Validate required fields
                    if (empty($employee_id) || empty($award_type)) {
                        $response['msg'] = 'Employee and Award Type are required fields';
                        echo json_encode($response);
                        exit();
                    }
                    
                    // Get employee details
                    $emp_query = "SELECT * FROM employees WHERE employee_id = $employee_id";
                    $emp_result = $GLOBALS['conn']->query($emp_query);
                    
                    if ($emp_result->num_rows === 0) {
                        $response['msg'] = 'Invalid employee selected';
                        echo json_encode($response);
                        exit();
                    }
                    
                    $employee = $emp_result->fetch_assoc();
                    
                    // Get award type name
                    $type_query = "SELECT name FROM award_types WHERE id = $award_type";
                    $type_result = $GLOBALS['conn']->query($type_query);
                    
                    if ($type_result->num_rows === 0) {
                        $response['msg'] = 'Invalid award type selected';
                        echo json_encode($response);
                        exit();
                    }
                    
                    $award_type_name = $type_result->fetch_assoc()['name'];
                    
                    // Current user ID
                    $added_by = $_SESSION['user_id'];
                    
                    // Insert award
                    $insert_query = "INSERT INTO employee_awards (award_type, type_id, emp_id, full_name, phone_number, staff_no, email, gift, award_date, added_by) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $GLOBALS['conn']->prepare($insert_query);
                    $stmt->bind_param('siisssssi', 
                        $award_type_name,
                        $award_type,
                        $employee_id,
                        $employee['full_name'],
                        $employee['phone_number'],
                        $employee['staff_no'],
                        $employee['email'],
                        $gift,
                        $award_date,
                        $added_by
                    );
                    
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['msg'] = 'Award added successfully';
                    }
                    
                    echo json_encode($response);
                    exit();
                }
                // Get award details for editing
                else if ($_GET['action'] === 'get') {
                    check_auth('manage_awards');
                    $response = array('error' => true, 'msg' => 'Failed to get award details');
                    
                    $award_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    
                    if (empty($award_id)) {
                        $response['msg'] = 'Invalid award ID';
                        echo json_encode($response);
                        exit();
                    }
                    
                    $query = "SELECT * FROM employee_awards WHERE id = $award_id";
                    $result = $GLOBALS['conn']->query($query);
                    
                    if ($result->num_rows > 0) {
                        $response['error'] = false;
                        $response['data'] = $result->fetch_assoc();
                    }
                    
                    echo json_encode($response);
                    exit();
                }
                // Update award
                else if ($_GET['action'] === 'update') {
                    check_auth('manage_awards');
                    $response = array('error' => true, 'msg' => 'Failed to update award');
                    
                    // Get POST data
                    $award_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    $award_type = isset($_POST['award_type']) ? intval($_POST['award_type']) : 0;
                    $gift = isset($_POST['gift']) ? $_POST['gift'] : '';
                    $award_date = isset($_POST['award_date']) ? $_POST['award_date'] : date('Y-m-d');
                    $status = isset($_POST['status']) ? $_POST['status'] : 'Active';
                    
                    // Validate required fields
                    if (empty($award_id) || empty($award_type)) {
                        $response['msg'] = 'Award ID and Award Type are required fields';
                        echo json_encode($response);
                        exit();
                    }
                    
                    // Get award type name
                    $type_query = "SELECT name FROM award_types WHERE id = $award_type";
                    $type_result = $GLOBALS['conn']->query($type_query);
                    
                    if ($type_result->num_rows === 0) {
                        $response['msg'] = 'Invalid award type selected';
                        echo json_encode($response);
                        exit();
                    }
                    
                    $award_type_name = $type_result->fetch_assoc()['name'];
                    
                    // Current user ID
                    $updated_by = $_SESSION['user_id'];
                    $updated_date = date('Y-m-d H:i:s');
                    
                    // Update award
                    $update_query = "UPDATE employee_awards SET 
                                     award_type = ?, 
                                     type_id = ?, 
                                     gift = ?, 
                                     award_date = ?, 
                                     status = ?, 
                                     updated_by = ?, 
                                     updated_date = ? 
                                     WHERE id = ?";
                    
                    $stmt = $GLOBALS['conn']->prepare($update_query);
                    $stmt->bind_param('sisssis', 
                        $award_type_name,
                        $award_type,
                        $gift,
                        $award_date,
                        $status,
                        $updated_by,
                        $updated_date,
                        $award_id
                    );
                    
                    if ($stmt->execute()) {
                        $response['error'] = false;
                        $response['msg'] = 'Award updated successfully';
                    }
                    
                    echo json_encode($response);
                    exit();
                }
                // Delete award
                else if ($_GET['action'] === 'delete') {
                    check_auth('manage_awards');
                    $response = array('error' => true, 'msg' => 'Failed to delete award');
                    
                    $award_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    
                    if (empty($award_id)) {
                        $response['msg'] = 'Invalid award ID';
                        echo json_encode($response);
                        exit();
                    }
                    
                    $query = "DELETE FROM employee_awards WHERE id = $award_id";
                    
                    if ($GLOBALS['conn']->query($query)) {
                        $response['error'] = false;
                        $response['msg'] = 'Award deleted successfully';
                    }
                    
                    echo json_encode($response);
                    exit();
                }
            }
		} 


		// Delete data
		else if($_GET['action'] == 'delete') {
			if ($_GET['endpoint'] === 'award') {
				check_auth('manage_awards');
				$response = array('error' => true, 'msg' => 'Failed to delete award');
				
				$award_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
				
				if (empty($award_id)) {
					$response['msg'] = 'Invalid award ID';
					echo json_encode($response);
					exit();
				}
				
				$query = "DELETE FROM employee_awards WHERE id = $award_id";
				
				if ($GLOBALS['conn']->query($query)) {
					$response['error'] = false;
					$response['msg'] = 'Award deleted successfully';
				}
				
				echo json_encode($response);
				exit();
			} else if ($_GET['endpoint'] === 'employee') {
				try {
				    // Delete company
				    check_auth('delete_employees');
				    $post = escapePostData($_POST);
				    $employeeId = $post['id'];
					
					$deleted = $employeeClass->deleteEmployee($employeeId);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Employee info has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'folder') {
				try {
					check_auth('manage_employee_docs'); // Same auth as adding employee
					
					$post = escapePostData($_POST);
					$deleted_at = date('Y-m-d H:i:s');
					
					// Soft delete the folder
					$sql = "UPDATE folders SET deleted_at = ? WHERE id = ?";
					$stmt = $GLOBALS['conn']->prepare($sql);
					$stmt->bind_param("si", $deleted_at, $post['id']);
					
					if ($stmt->execute()) {
						$result['msg'] = 'Folder deleted successfully';
						$result['error'] = false;
					} else {
						throw new Exception("Error deleting folder");
					}
					
				} catch (Exception $e) {
					$result['msg'] = 'Error: ' . $e->getMessage();
					$result['error'] = true;
				}
				
				echo json_encode($result);
				exit();
			} else if ($_GET['endpoint'] === 'doc_types') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$id = $_POST['id'];

					check_auth('manage_employee_docs');
					
					$sql = "DELETE FROM document_types WHERE id = '$id'";
					if($GLOBALS['conn']->query($sql)) {
						$GLOBALS['conn']->commit();
						$result['msg'] = 'Document type deleted successfully';
						$result['error'] = false;
					} else {
						throw new Exception("Error deleting document type");
					}
				} catch(Exception $e) {
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: Something went wrong';
					$result['error'] = true;
				}
				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'document') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('delete_employee_docs');
				    $deleted = $empDocClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Document has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'employeeBulk') {
				check_auth('delete_employees');

				$ids = $_POST['ids'] ?? [];
				$action = $_POST['action'] ?? '';

				if (!is_array($ids) || empty($ids)) {
					echo json_encode(['error' => true, 'msg' => 'No employees selected or invalid data']);
					exit;
				}

				if ($action === 'delete') {
					try {
						$deleted_count = 0;
						foreach ($ids as $id) {
							if ($employeeClass->deleteEmployee($id)) {
								$deleted_count++;
							} 
						}

						echo json_encode(['error' => false, 'msg' => "$deleted_count employees deleted successfully"]);

					} catch (Exception $e) {
						echo json_encode(['error' => true, 'msg' => 'An error occurred: ' . $e->getMessage()]);
					}
				} else {
					echo json_encode(['error' => true, 'msg' => 'Invalid action']);
				}
				exit;
			}

			exit();
		} else if($_GET['action'] == 'download') {
			if($_GET['endpoint'] == 'folder_docs') {
				$id = $_GET['id'];
				$sql = "SELECT * FROM documents WHERE id = '$id'";
				$result = $GLOBALS['conn']->query($sql);
				$doc = $result->fetch_assoc();
				
				if($doc && file_exists($doc['file_path'])) {
					$file_name = basename($doc['file_path']);
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$doc['name'].'"');
					header('Content-Length: ' . filesize($doc['file_path']));
					readfile($doc['file_path']);
					exit();
				}
				
				header('HTTP/1.0 404 Not Found');
				echo "File not found.";
				exit();
			}
		} else if($_GET['action'] == 'view') {
			if($_GET['endpoint'] == 'folder_docs') {
				$id = $_GET['id'];
				$sql = "SELECT * FROM documents WHERE id = '$id'";
				$result = $GLOBALS['conn']->query($sql);
				$doc = $result->fetch_assoc();
				
				if($doc && file_exists($doc['file_path'])) {
					$file_ext = strtolower(pathinfo($doc['file_path'], PATHINFO_EXTENSION));
					switch($file_ext) {
						case 'pdf':
							header('Content-Type: application/pdf');
							break;
						case 'jpg':
						case 'jpeg':
							header('Content-Type: image/jpeg');
							break;
						case 'png':
							header('Content-Type: image/png');
							break;
						default:
							header('Content-Type: application/octet-stream');
					}
					readfile($doc['file_path']);
					exit();
				}
				
				header('HTTP/1.0 404 Not Found');
				echo "File not found.";
				exit();
			}
		}

	}
}



?>