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

				    foreach ($employeeData as $index => $value) {
				    	$data[$index] = isset($employeeData[$index]) ? $employeeData[$index]: "";
				    }

				    $data['added_by'] = $_SESSION['user_id'];

				    check_exists('employees', ['full_name' => $post['full_name'], 'email' => $post['email']]);
				    check_auth('add_employee');

				    // Call the create method for employee
				    $result['id'] = $employeeClass->create($data);

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
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();

				    $result = ['error' => false, 'msg' => '', 'errors' => ''];

				    check_auth('add_employee'); // Authorization check

				    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
				        $fileTmpPath = $_FILES['file']['tmp_name'];
				        $fileName = $_FILES['file']['name'];
				        $fileSize = $_FILES['file']['size'];
				        $fileType = $_FILES['file']['type'];

				        // Validate file type and size
				        if ($fileType != 'text/csv' ) { // File size limit: 5MB
				        	// || $fileSize > 5 * 1024 * 1024
				            $result['error'] = true;
				            $result['msg'] = "Invalid file type or size. Please upload a valid CSV file.";
				            echo json_encode($result);
				            exit();
				        }

				        if (($file = fopen($fileTmpPath, 'r')) !== false) {
				            $row = 0;

				            while (($line = fgetcsv($file, 1000, ',')) !== false) {
				                $row++;
				                if ($row == 1) continue; // Skip header row

				                // Ensure the row has the correct number of columns
				                if (count($line) < 27) {
				                    $result['errors'] .= "Skipping invalid row at line $row: ";
				                    continue;
				                }

				                list(
				                    $staff_no, $full_name, $phone_number, $email, $gender,
				                    $national_id, $date_of_birth, $city, $address,
				                    $payment_bank, $payment_account, $branch, 
				                    $designation, $state, $location, $hire_date,
				                    $contract_start, $contract_end, $contract_type, $salary,
				                    $tax_exempt, $budget_code, $moh_contract, $work_days,
				                    $work_hours, $grade, $seniority
				                ) = array_map('escapeStr', $line);

				                $position = $designation;

				                // Check for missing required fields
				                if (!$full_name || !$phone_number || !$gender || !$email || !$branch || !$state || !$hire_date) {
				                    $result['errors'] .= " Missing required fields at line $row.";
				                    continue;
				                }

				                $date_of_birth 	= date('Y-m-d', strtotime($date_of_birth));
				                $hire_date 		= date('Y-m-d', strtotime($hire_date));
				                $contract_start = date('Y-m-d', strtotime($contract_start));
				                $contract_end 	= date('Y-m-d', strtotime($contract_end));

				                $check_sql = "SELECT * FROM `employees` WHERE `full_name` = '$full_name' AND `phone_number` = '$phone_number'";
				                $check_exists = $GLOBALS['conn']->query($check_sql);
				                if($check_exists->num_rows > 0) {
				                	$result['errors'] .= " Record already exits at line $row.";
				                	continue;
				                }

				                // Process each entity and handle creation or retrieval
				                $branch_id = checkAndCreateEntity('branches', $branch, $myUserId, $branchClass);
				                $state_id = checkAndCreateEntity('states', $state, $myUserId, $statesClass);
				                $location_id = checkAndCreateEntity('locations', $location, $myUserId, $locationsClass);
				                $designation_id = checkAndCreateEntity('designations', $designation, $myUserId, $designationsClass);
				                $contract_type_id = checkAndCreateEntity('contract_types', $contract_type, $myUserId, $contractTypesClass);
				                $budget_code_id = checkAndCreateEntity('budget_codes', $budget_code, $myUserId, $budgetCodesClass);

				                // Prepare employee data
				                $employeeData = [
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
				                    'budget_code' => $budget_code,
				                    'moh_contract' => $moh_contract,
				                    'payment_bank' => $payment_bank,
				                    'payment_account' => $payment_account,
				                    'grade' => $grade,
				                    'tax_exempt' => $tax_exempt,
				                    'seniority' => $seniority,
				                ];

				                $result['id'] = $employeeClass->create($employeeData);

				                if ($result['id']) {
				                    // Handle staff number
				                    if (!isset($staff_no) || $staff_no == return_setting('staff_prefix')) {
				                        $staff_no = return_setting('staff_prefix') . $result['id'];
				                        $employeeClass->update($result['id'], ['staff_no' => $staff_no]);
				                    }

				                    // Create user
				                    $password = password_hash($phone_number, PASSWORD_DEFAULT);
				                    $userData = [
				                        'full_name' => $full_name,
				                        'phone' => $phone_number,
				                        'email' => $email,
				                        'emp_id' => $result['id'],
				                        'branch_id' => $branch_id,
				                        'username' => usernameFromEmail($email),
				                        'password' => $password,
				                        'role' => 'employee',
				                        'added_by' => $_SESSION['user_id']
				                    ];
				                    // $userClass->create($userData);
				                } else {
				                    $GLOBALS['conn']->rollback();
				                    throw new Exception("Failed to create employee at line $row.");
				                }
				            }

				            fclose($file);
				            $GLOBALS['conn']->commit();
				            $result['msg'] = "Employees uploaded successfully.";
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
			} else if($_GET['endpoint'] == 'folder') {
				try {
					$GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					
					check_auth('add_employee'); // Same auth as adding employee
					
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
			} 

			exit();
		} 


		// Update data
		else if($_GET['action'] == 'update') {
			$updated_date = date('Y-m-d H:i:s');
			if($_GET['endpoint'] == 'employee') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();
				    $post = escapePostData($_POST);
				    $data = array();

				    $employeeData = $post;
				    unset($employeeData['employee_id']);
				    unset($employeeData['degree']);
				    unset($employeeData['institution']);
				    unset($employeeData['startYear']);
				    unset($employeeData['endYear']);

				    foreach ($employeeData as $index => $value) {
				    	$data[$index] = isset($employeeData[$index]) ? $employeeData[$index]: "";
				    }

				    $data['updated_by'] = $_SESSION['user_id'];
				    $data['updated_date'] = $updated_date;

				    check_exists('employees', ['full_name' => $post['full_name'], 'email' => $post['email']], ['employee_id' => $post['employee_id'], 'staff_no' => $post['staff_no']]);
				    check_auth('edit_employee');

				    // Call the create method for employee
				    $result['id'] = $employeeClass->update($post['employee_id'], $data);

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

				        $deleted = $educationClass->delete($post['employee_id']);

				        if (is_array($degree) && count($degree) > 0) {
				            foreach ($degree as $index => $value) {
				                $degree         = escapeStr($degree[$index]);
				                $institution    = escapeStr($institution[$index]);
				                $startYear      = escapeStr($startYear[$index]);
				                $endYear        = escapeStr($endYear[$index]);

				                $educationData = array(
				                    'employee_id'      => $post['employee_id'],
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
					        'updated_by' 	=> $_SESSION['user_id'],
					        'updated_date' 	=> $updated_date
					    );

					    $user = $employeeClass->get_user($post['employee_id']);
					    if($user) {
					    	$id = $user[0]['emp_id'];
					    	// $user_id = $userClass->update($id, $userData);
					    } else {
					    	// $user_id = $userClass->create($userData);
					    }

				        // Commit the transaction if everything is successful
				        $GLOBALS['conn']->commit();

				        // Return success response
				        $result['msg'] = 'Employee info updated  successfully';
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
			} else if ($_GET['endpoint'] == 'employee_avatar') {
			    // Ensure user has the correct permissions
			    check_auth('edit_employee');
			    
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
					
					check_auth('add_employee'); // Same auth as adding employee
					
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
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['full_name', 'phone_number', 'email', 'position', 'hire_date', 'salary', 'status'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}

				$department = $state = $location = $status = '';	
				if(isset($_POST['department'])) $department = $_POST['department'];
				if(isset($_POST['state'])) $state = $_POST['state'];
				if(isset($_POST['location'])) $location = $_POST['location'];
				if(isset($_POST['status'])) $status = $_POST['status'];


			    // Base query
			    $query = "SELECT * FROM `employees` WHERE `employee_id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%'  OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `address` LIKE '%" . escapeStr($searchParam) . "%' OR `designation` LIKE '%" . escapeStr($searchParam) . "%' OR `position` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    if($department) {
			    	$query .= " AND `branch_id` LIKE '$department'";
			    }

			    if($state) {
			    	$query .= " AND `state_id` LIKE '$state'";
			    }

			    if($location) {
			    	$query .= " AND `location_id` LIKE '$location'";
			    }

			    if($status) {
			    	$query .= " AND `status` LIKE '$status'";
			    } else if (!$searchParam) {
			    	$query .= " AND `status` LIKE 'Active'";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";



			    // Execute query
			    $employees = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `employees` WHERE `employee_id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' OR `email` LIKE '%" . escapeStr($searchParam) . "%' OR `address` LIKE '%" . escapeStr($searchParam) . "%' OR `designation` LIKE '%" . escapeStr($searchParam) . "%' OR `position` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($employees->num_rows > 0) {
			        while ($row = $employees->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $employees->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'folders') {
				$result = [];
				try {
					check_auth('view_employees');
					
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
				$folder_id = isset($_POST['folder_id']) ? escapeStr($_POST['folder_id']) : '';
				$employee_id = isset($_POST['employee_id']) ? escapeStr($_POST['employee_id']) : '';
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name', 'full_name', 'phone','type_name', 'expiration_date', 'created_at'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `employee_docs` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

				if($folder_id) {
					$query .= " AND `folder_id` = '$folder_id'";
				}

				if($employee_id) {
					$query .= " AND `emp_id` = '$employee_id'";
				}

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $empDocs = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `employee_docs` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    if($folder_id) {
			        $countQuery .= " AND `folder_id` = '$folder_id'";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($empDocs->num_rows > 0) {
			        while ($row = $empDocs->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $empDocs->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} 

			echo json_encode($result);

			exit();

		} 


		// Get data
		else if($_GET['action'] == 'get') {
			if ($_GET['endpoint'] === 'company') {
				json(get_data('company', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'branch') {
				json(get_data('branches', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'doc_types') {
				$id = $_POST['id'];
				$sql = "SELECT * FROM document_types WHERE id = '$id'";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
			} else if ($_GET['endpoint'] === 'folder_docs') {
				$id = $_POST['id'];
				$sql = "SELECT * FROM documents WHERE id = '$id'";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
				exit();
			} else if($_GET['endpoint'] == 'doc_types_list') {
				$sql = "SELECT id, name FROM document_types ORDER BY name ASC";
				$result = $GLOBALS['conn']->query($sql);
				$data = array();
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				echo json_encode($data);
				exit();
			} else if($_GET['endpoint'] == 'employees_list') {
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
			}
		} 


		// Delete data
		else if($_GET['action'] == 'delete') {
			if ($_GET['endpoint'] === 'employee') {
				try {
				    // Delete company
				    check_auth('delete_employee');
				    $post = escapePostData($_POST);
				    $employeeId = $post['id'];

				    // Delete payroll info
				    $deleted = "DELETE FROM `payroll_details` WHERE `emp_id` LIKE '$employeeId'";
					if(!mysqli_query($GLOBALS["conn"], $deleted)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}
				    // Delete leave info
				    $deleted = "DELETE FROM `employee_leave` WHERE `emp_id` LIKE '$employeeId'";
					if(!mysqli_query($GLOBALS["conn"], $deleted)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}
				    // Delete attendance info
				    $deleted = "DELETE FROM `atten_details` WHERE `emp_id` LIKE '$employeeId'";
					if(!mysqli_query($GLOBALS["conn"], $deleted)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}
				    // Delete timesheet info
				    $deleted = "DELETE FROM `timesheet_details` WHERE `emp_id` LIKE '$employeeId'";
					if(!mysqli_query($GLOBALS["conn"], $deleted)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}
				    // Delete trans info
				    $deleted = "DELETE FROM `employee_transactions` WHERE `emp_id` LIKE '$employeeId'";
					if(!mysqli_query($GLOBALS["conn"], $deleted)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}

					// Delete employee
					$deleted = "DELETE FROM `employees` WHERE `employee_id` LIKE '$employeeId'";
					if(!mysqli_query($GLOBALS["conn"], $deleted)) {
						throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
					}

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
					check_auth('add_employee'); // Same auth as adding employee
					
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
				    check_auth('delete_employee');
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