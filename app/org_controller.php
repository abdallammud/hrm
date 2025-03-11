<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'save') {
			if($_GET['endpoint'] == 'company') {
				try {
				    // Prepare data from POST request
				    $data = array(
				        'name' => $_POST['name'], 
				        'address' => $_POST['address'], 
				        'contact_phone' => $_POST['phones'], 
				        'contact_email' => $_POST['emails']
				    );

				    check_exists('company', ['name' => $_POST['name']]);
				    check_auth('manage_company_info');

				    // Call the create method
				    $result['id'] = $companyClass->create($data);

				    // If the company is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Company created successfully';
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
			} else if($_GET['endpoint'] == 'branch') {
				try {
				    // Prepare data from POST request
				    $data = array(
				        'name' => $_POST['name'], 
				        'address' => isset($_POST['address']) ? $_POST['address'] : '', 
				        'contact_phone' => isset($_POST['contact_phone']) ? $_POST['contact_phone'] : '', 
				        'contact_email' => isset($_POST['contact_email']) ? $_POST['contact_email'] : '', 
				    );

				    check_exists('branches', ['name' => $_POST['name']]);
				    check_auth('manage_departments');

				    // Call the create method
				    $result['id'] = $branchClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = $GLOBALS['branch_keyword']['sing'].' created successfully';
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
			} else if($_GET['endpoint'] == 'state') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'country_id' => isset($post['country']) ? $post['country']: "" ,  
				        'country_name' => isset($post['countryName']) ? $post['countryName']: "" , 
				        'tax_grid' => isset($post['tax']) ? json_encode($post['tax']) : "",
				        'stamp_duty' => isset($post['stampDuty']) ? $post['stampDuty']: 0 , 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('states', ['name' => $_POST['name']]);
				    check_auth('manage_states');

				    // Call the create method
				    $result['id'] = $statesClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'State created successfully';
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
			} else if($_GET['endpoint'] == 'location') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'state_id' => isset($post['state']) ? $post['state']: "" ,  
				        'state_name' => isset($post['stateName']) ? $post['stateName']: "" , 
				        'city_name' => isset($post['city']) ? $post['city']: "",
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('locations', ['name' => $post['name']]);
				    check_auth('manage_duty_locations');

				    // Call the create method
				    $result['id'] = $locationsClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Duty location created successfully';
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
			} else if($_GET['endpoint'] == 'bank_account') {
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
				    check_auth('manage_company_banks');

				    // Call the create method
				    $result['id'] = $bankAccountClass->create($data);

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
			} else if($_GET['endpoint'] == 'designation') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('designations', ['name' => $post['name']]);
				    check_auth('manage_designations');

				    // Call the create method
				    $result['id'] = $designationsClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Designation created successfully';
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
			} else if($_GET['endpoint'] == 'project') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'comments' => $post['comments'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('projects', ['name' => $post['name']]);
				    check_auth('manage_projects');

				    // Call the create method
				    $result['id'] = $projectsClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Project created successfully';
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
			} else if($_GET['endpoint'] == 'contract_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('contract_types', ['name' => $post['name']]);
				    check_auth('manage_contract_types');

				    // Call the create method
				    $result['id'] = $contractTypesClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Contract type created successfully';
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
			} else if($_GET['endpoint'] == 'budget_code') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'comments' => $post['comments'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('budget_codes', ['name' => $post['name']]);
				    check_auth('manage_budget_codes');

				    // Call the create method
				    $result['id'] = $budgetCodesClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Budget code created successfully';
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
			} else if($_GET['endpoint'] == 'bank') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('banks', ['name' => $post['name']]);
				    check_auth('manage_company_banks');

				    // Call the create method
				    $result['id'] = $banksClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Bank name created successfully';
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
			} else if($_GET['endpoint'] == 'subtype') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'type' => $post['type'], 
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('trans_subtypes', ['name' => $post['name']]);
				    check_auth('manage_employee_transactions');

				    // Call the create method
				    $result['id'] = $transSubTypesClass->create($data);

				    // If the branch is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Transaction subtype created successfully';
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


		// Update data
		else if($_GET['action'] == 'update') {
			$updated_date = date('Y-m-d H:i:s');
			if($_GET['endpoint'] == 'company') {
				try {
				    // Prepare data from POST request
				    $data = array(
				    	'id' => $_POST['id'], 
				        'name' => $_POST['name'], 
				        'address' => $_POST['address'], 
				        'contact_phone' => $_POST['phones'], 
				        'contact_email' => $_POST['emails']
				    );

				    check_exists('company', ['name' => $_POST['name']], ['id' => $_POST['id']]);
				    check_auth('manage_company_info');

				    // Call the create method
				    $updated = $companyClass->update($_POST['id'], $data);

				    // If the company is created successfully, return a success message
				    if($updated) {
				        $result['msg'] = 'Company editted successfully';
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
			} else if($_GET['endpoint'] == 'branch') {
				try {
				    // Prepare data from POST request
				    $data = array(
				    	'id' => $_POST['id'], 
				        'name' => $_POST['name'], 

				        'address' => isset($_POST['address']) ? $_POST['address'] : '', 
				        'contact_phone' => isset($_POST['phones']) ? $_POST['phones'] : '', 
				        'contact_email' => isset($_POST['emails']) ? $_POST['emails'] : '', 
				    );

				    check_exists('branches', ['name' => $_POST['name']], ['id' => $_POST['id']]);
				    check_auth('manage_departments');

				    // Call the create method
				    $updated = $branchClass->update($_POST['id'], $data);

				    // If the company is created successfully, return a success message
				    if($updated) {
				        $result['msg'] = $GLOBALS['branch_keyword']['sing'].' editted successfully';
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
			} else if($_GET['endpoint'] == 'state') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'country_id' => isset($post['country']) ? $post['country']: "" ,  
				        'country_name' => isset($post['countryName']) ? $post['countryName']: "" , 
				        'tax_grid' => isset($post['tax']) ? json_encode($post['tax']) : "",
				        'status' => isset($post['status']) ? $post['status']: "" , 
				        'stamp_duty' => isset($post['stampDuty']) ? $post['stampDuty']: 0, 
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('states', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_states');

				    // Call the create method
				    $updated = $statesClass->update($post['id'], $data);

				    // If the company is created successfully, return a success message
				    if($updated) {
				        $result['msg'] = 'Satet info editted successfully';
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
			} else if($_GET['endpoint'] == 'location') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'state_id' => isset($post['state']) ? $post['state']: "" ,  
				        'state_name' => isset($post['stateName']) ? $post['stateName']: "" , 
				        'city_name' => isset($post['city']) ? $post['city']: "",
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "Active",
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('locations', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_duty_locations');

				    // Call the create method
				    $result['id'] = $locationsClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Duty location editted successfully';
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
			} else if($_GET['endpoint'] == 'bank_account') {
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
				    check_auth('manage_company_banks');

				    // Call the create method
				    $result['id'] = $bankAccountClass->update($post['id'], $data);

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
			} else if($_GET['endpoint'] == 'designation') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('designations', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_designations');

				    // Call the create method
				    $result['id'] = $designationsClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Designation info editted successfully';
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
			} else if($_GET['endpoint'] == 'project') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'comments' => isset($post['comments']) ? $post['comments']: "Active",
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "",
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('projects', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_projects');

				    // Call the create method
				    $result['id'] = $projectsClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Project info editted successfully';
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
			} else if($_GET['endpoint'] == 'contract_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('contract_types', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_contract_types');

				    // Call the create method
				    $result['id'] = $contractTypesClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Contract type info editted successfully';
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
			} else if($_GET['endpoint'] == 'budget_code') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'comments' => isset($post['comments']) ? $post['comments']: "Active",
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "",
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('budget_codes', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_budget_codes');

				    // Call the create method
				    $result['id'] = $budgetCodesClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Budget code info editted successfully';
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
			} else if($_GET['endpoint'] == 'bank') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('banks', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_company_banks');

				    // Call the create method
				    $result['id'] = $banksClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Bank info editted successfully';
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
			} else if($_GET['endpoint'] == 'subtype') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'type' => $post['type'], 
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('trans_subtypes', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('manage_employee_transactions');

				    // Call the create method
				    $result['id'] = $transSubTypesClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Subtype info editted successfully';
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

			if ($_GET['endpoint'] === 'company') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name', 'contact_phone', 'contact_email', 'address'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `company` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%'  OR `contact_phone` LIKE '%" . escapeStr($searchParam) . "%'  OR `contact_email` LIKE '%" . escapeStr($searchParam) . "%'  OR `address` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $company = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `company` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `contact_phone` LIKE '%" . escapeStr($searchParam) . "%' OR `contact_email` LIKE '%" . escapeStr($searchParam) . "%' OR `address` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($company->num_rows > 0) {
			        while ($row = $company->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $company->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'branches') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name', 'contact_phone', 'contact_email', 'address'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `branches` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%'  OR `contact_phone` LIKE '%" . escapeStr($searchParam) . "%'  OR `contact_email` LIKE '%" . escapeStr($searchParam) . "%'  OR `address` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $branches = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `branches` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `contact_phone` LIKE '%" . escapeStr($searchParam) . "%' OR `contact_email` LIKE '%" . escapeStr($searchParam) . "%' OR `address` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($branches->num_rows > 0) {
			        while ($row = $branches->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $branches->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'states') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name', 'country_name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `states` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%'  OR `country_name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $states = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `states` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%'  OR `country_name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($states->num_rows > 0) {
			        while ($row = $states->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $states->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'locations') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name', 'city_name', 'state_name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `locations` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%'  OR `city_name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $locations = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `locations` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%'  OR `city_name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($locations->num_rows > 0) {
			        while ($row = $locations->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $locations->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'bank_accounts') {
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
			} else if ($_GET['endpoint'] === 'designations') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `designations` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $designations = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `designations` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($designations->num_rows > 0) {
			        while ($row = $designations->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $designations->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'projects') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `projects` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `comments` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $projects = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `projects` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `comments` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($projects->num_rows > 0) {
			        while ($row = $projects->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $projects->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'contract_types') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `contract_types` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $contract_types = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `contract_types` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($contract_types->num_rows > 0) {
			        while ($row = $contract_types->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $contract_types->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'budget_codes') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `budget_codes` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `comments` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $budget_codes = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `budget_codes` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `comments` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($budget_codes->num_rows > 0) {
			        while ($row = $budget_codes->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $budget_codes->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'all_banks') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `banks` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $banks = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `banks` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($banks->num_rows > 0) {
			        while ($row = $banks->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $banks->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'subtypes') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `trans_subtypes` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `type` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $trans_subtypes = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `trans_subtypes` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($trans_subtypes->num_rows > 0) {
			        while ($row = $trans_subtypes->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $trans_subtypes->num_rows . " records were found.";
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
			} else if ($_GET['endpoint'] === 'state') {
				$stateInfo = get_data('states', array('id' => $_POST['id']));
				// var_dump($_POST['show']);
				if(isset($_POST['show']) && $_POST['show'] == "true") {
					$stateInfo = $stateInfo[0];
					$details 	= '';
					$tax 		= '';
					$name 			= $stateInfo['name'];
					$country_name 	= $stateInfo['country_name'];
					$status 		= $stateInfo['status'];

					$details .= '<tr>
						<td>'.$name.'</td>
						<td>'.$country_name.'</td>
						<td>'.$status.'</td>
					</tr>';

					$taxGrid = json_decode($stateInfo['tax_grid']);

					if ($taxGrid && (is_object($taxGrid) || is_array($taxGrid))) {
					    if (!empty($taxGrid)) {
					        foreach ($taxGrid as $grid) {
					           $tax .= '<tr>
									<td>'.formatMoney($grid->min).'</td>
									<td>'.formatMoney($grid->max).'</td>
									<td>'.$grid->rate.'%</td>
								</tr>';
					        }
					    } 
					} 

					echo json_encode(array('details' => $details, 'tax' => $tax));
				} else {
					json($stateInfo);
				}
			} else if ($_GET['endpoint'] === 'location') {
				json(get_data('locations', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'bank_account') {
				json(get_data('bank_accounts', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'designation') {
				json(get_data('designations', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'project') {
				json(get_data('projects', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'contract_type') {
				json(get_data('contract_types', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'budget_code') {
				json(get_data('budget_codes', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'bank') {
				json(get_data('banks', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'subtype') {
				json(get_data('trans_subtypes', array('id' => $_POST['id'])));
			}

			exit();
		}


		// Delete data
		else if($_GET['action'] == 'delete') {
			if ($_GET['endpoint'] === 'company') {
				try {
				    // Delete company
				    check_auth('manage_company_info');
				    $deleted = $companyClass->delete($_POST['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Company record has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'branch') {
				try {
				    // Delete branchClass
				    check_auth('manage_departments');
				    checkForeignKey($_POST['id'], 'branch_id', ['employees']);
				    $deleted = $branchClass->delete($_POST['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = $GLOBALS['branch_keyword']['sing'].' record has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'state') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    checkForeignKey($post['id'], 'state_id', ['employees']);
				    check_auth('manage_states');
				    $deleted = $statesClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'State record has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'location') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    // checkForeignKey($post['id'], 'state_id', ['employees']);
				    check_auth('manage_duty_locations');
				    $deleted = $locationsClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Duty location has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'bank_account') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_company_banks');
				    $deleted = $bankAccountClass->delete($post['id']);

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
			} else if ($_GET['endpoint'] === 'designation') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    // checkForeignKey($post['id'], 'designation', ['employees']);
				    check_auth('manage_designations');
				    $deleted = $designationsClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Designation has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'project') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    checkForeignKey($post['id'], 'project_id', ['employees']);
				    check_auth('manage_projects');
				    $deleted = $projectsClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Project has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'contract_type') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_contract_types');
				    $deleted = $contractTypesClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Contract type has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'bank') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_company_banks');
				    $deleted = $banksClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Bank name has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'budget_code') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_budget_codes');
				    $deleted = $budgetCodesClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Budget code has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'subtype') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('manage_employee_transactions');
				    $deleted = $transSubTypesClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Subtype has been  deleted successfully';
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