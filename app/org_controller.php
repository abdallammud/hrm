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
				    check_auth('create_organization');

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
				    check_auth('create_departments');

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
				    check_auth('create_states');

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
				    check_auth('create_duty_locations');

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
				    check_auth('create_bank_accounts');

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
				    check_auth('create_designations');

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
				    check_auth('create_projects');

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
				    check_auth('create_contract_types');

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
				        'grant_code_id' => $post['grant_code_id'],	
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('budget_codes', ['name' => $post['name']]);
				    check_auth('create_budget_codes');

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
				    check_auth('create_bank_accounts');

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
				    check_auth('create_transaction_subtypes');

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
			} else if($_GET['endpoint'] == 'goal_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    check_auth('create_goal_types');
				    $data = array(
				        'name' => $post['name'],
				        'added_by' => $_SESSION['user_id']
				    );

				    // Create the goal type
				    $result['id'] = $goalTypesClass->create($data);

				    // Goal type created
				    if($result['id']) {
				        $result['msg'] = 'Goal type has been added successfully';
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
			} else if($_GET['endpoint'] == 'award_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    check_auth('create_award_types');
				    $data = array(
				        'name' => $post['awardTypeName'],
				    );

				    // Create the award type
				    $result['id'] = $awardTypesClass->create($data);

				    // Award type created
				    if($result['id']) {
				        $result['msg'] = 'Award type has been added successfully';
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
			} else if($_GET['endpoint'] == 'financial_account') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    check_auth('create_financial_accounts');
				    $data = array(
				        'name' => $post['financialAccountName'],
				        'type' => $post['accountType'],
				        'status' => 'Active',
				        'added_by' => $_SESSION['user_id'],
				        'added_date' => date('Y-m-d H:i:s')
				    );

				    check_exists('financial_accounts', ['name' => $post['financialAccountName']]);

				    // Call the create method
				    $result['id'] = $financialAccountsClass->create($data);

				    // If the financial account is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Financial account created successfully';
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
			} else if($_GET['endpoint'] == 'training_options') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    check_auth('create_training_options');
				    $data = array(
				        'name' => $post['name'],
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('training_options', ['name' => $post['name']]);

				    // Call the create method
				    $result['id'] = $trainingOptionsClass->create($data);

				    // If the training option is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Training option created successfully';
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
			} else if($_GET['endpoint'] == 'training_types') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    check_auth('create_training_types');
				    $data = array(
				        'name' => $post['name'],
				        'added_by' => $_SESSION['user_id']
				    );

				    check_exists('training_types', ['name' => $post['name']]);

				    // Call the create method
				    $result['id'] = $trainingTypesClass->create($data);

				    // If the training type is created successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Training type created successfully';
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
			} else if($_GET['endpoint'] == 'grant_code') {
				try {
					$post = escapePostData($_POST);
					$data = array(
						'name' => $post['name'],
						'added_by' => $_SESSION['user_id']
					);

					check_exists('grant_codes', ['name' => $post['name']]);
					check_auth('create_budget_codes');

					$result['id'] = $grantCodesClass->create($data);

					if ($result['id']) {
						$result['msg'] = 'Grant code created successfully';
						$result['error'] = false;
					} else {
						$result['msg'] = 'Something went wrong, please try again';
						$result['error'] = true;
					}

				} catch (Exception $e) {
					$result['msg'] = 'Error: Something went wrong';
					$result['sql_error'] = $e->getMessage();
					$result['error'] = true;
				}
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
				    check_auth('edit_organization');

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
				    check_auth('edit_departments');

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
				    check_auth('edit_states');

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
				    check_auth('edit_duty_locations');

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
				    check_auth('edit_bank_accounts');

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
				    check_auth('edit_designations');

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
				    check_auth('edit_projects');

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
				    check_auth('edit_contract_types');

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
						'grant_code_id' => $post['grant_code_id'],
				        'comments' => isset($post['comments']) ? $post['comments']: "Active",
				        'status' => isset($post['slcStatus']) ? $post['slcStatus']: "",
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('budget_codes', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('edit_budget_codes');

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
				    check_auth('edit_bank_accounts');

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
				    check_auth('edit_transaction_subtypes');

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
			} else if($_GET['endpoint'] == 'goal_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'status' => isset($post['status']) ? $post['status']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('goal_types', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('edit_goal_types');

				    // Call the create method
				    $result['id'] = $goalTypesClass->update($post['id'], $data);

				    // If the branch is editted successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Goal type info editted successfully';
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
			} else if($_GET['endpoint'] == 'award_type') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'status' => isset($post['status']) ? $post['status']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('award_types', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('edit_award_types');

				    // Call the create method
				    $result['id'] = $awardTypesClass->update($post['id'], $data);

				    // If the award type is edited successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Award type info edited successfully';
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
			} else if($_GET['endpoint'] == 'financial_account') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['financialAccountName4Edit'], 
				        'type' => $post['accountType4Edit'],
				        'status' => isset($post['slcStatus4Edit']) ? $post['slcStatus4Edit']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('financial_accounts', ['name' => $post['financialAccountName4Edit']], ['id' => $post['financialAccount_id']]);
				    check_auth('edit_financial_accounts');

				    // Call the update method
				    $result['id'] = $financialAccountsClass->update($post['financialAccount_id'], $data);

				    // If the financial account is edited successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Financial account info edited successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the update method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'training_options') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'status' => isset($post['status']) ? $post['status']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('training_options', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('edit_training_options');

				    // Call the update method
				    $result['id'] = $trainingOptionsClass->update($post['id'], $data);

				    // If the training option is edited successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Training option info edited successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the update method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if($_GET['endpoint'] == 'training_types') {
				try {
				    // Prepare data from POST request
				    $post = escapePostData($_POST);
				    $data = array(
				        'name' => $post['name'], 
				        'status' => isset($post['status']) ? $post['status']: "Active" ,
				        'updated_by' => $_SESSION['user_id'],
				        'updated_date' => $updated_date
				    );

				    check_exists('training_types', ['name' => $post['name']], ['id' => $post['id']]);
				    check_auth('edit_training_types');

				    // Call the update method
				    $result['id'] = $trainingTypesClass->update($post['id'], $data);

				    // If the training type is edited successfully, return a success message
				    if($result['id']) {
				        $result['msg'] = 'Training type info edited successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the update method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if ($_GET['endpoint'] == 'grant_code') {
				try {
					$post = escapePostData($_POST);
					$data = array(
						'name' => $post['name'],
						'status' => $post['slcStatus'] ?? 'Active',
						'updated_by' => $_SESSION['user_id'],
						'updated_date' => $updated_date
					);

					check_exists('grant_codes', ['name' => $post['name']], ['id' => $post['id']]);
					check_auth('edit_budget_codes');

					$result['id'] = $grantCodesClass->update($post['id'], $data);

					if ($result['id']) {
						$result['msg'] = 'Grant code updated successfully';
						$result['error'] = false;
					} else {
						$result['msg'] = 'Something went wrong, please try again';
						$result['error'] = true;
					}

				} catch (Exception $e) {
					$result['msg'] = 'Error: Something went wrong';
					$result['sql_error'] = $e->getMessage();
					$result['error'] = true;
				}
				echo json_encode($result);
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
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `contact_phone` LIKE '%" . escapeStr($searchParam) . "%' OR `contact_email` LIKE '%" . escapeStr($searchParam) . "%' OR `address` LIKE '%" . escapeStr($searchParam) . "%')";
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
			    $query = "SELECT b.name, b.comments, b.id, b.status, g.name as grant_code FROM `budget_codes` b INNER JOIN `grant_codes` g ON b.grant_code_id = g.id WHERE b.`id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (b.`name` LIKE '%" . escapeStr($searchParam) . "%' OR b.`comments` LIKE '%" . escapeStr($searchParam) . "%' OR g.`name` LIKE '%" . escapeStr($searchParam) . "%' )";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $budget_codes = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `budget_codes` b INNER JOIN `grant_codes` g ON b.grant_code_id = g.id WHERE b.`id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (b.`name` LIKE '%" . escapeStr($searchParam) . "%' OR b.`comments` LIKE '%" . escapeStr($searchParam) . "%' OR g.`name` LIKE '%" . escapeStr($searchParam) . "%' )";
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
			} else if ($_GET['endpoint'] === 'goal_types') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `goal_types` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $goal_types = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `goal_types` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($goal_types->num_rows > 0) {
			        while ($row = $goal_types->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $goal_types->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'award_types') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `award_types` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $award_types = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `award_types` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($award_types->num_rows > 0) {
			        while ($row = $award_types->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $award_types->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'financial_accounts') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name', 'type', 'status'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `financial_accounts` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `type` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $financial_accounts = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `financial_accounts` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $countQuery .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%' OR `type` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($financial_accounts->num_rows > 0) {
			        while ($row = $financial_accounts->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $financial_accounts->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'training_options') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `training_options` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $training_options = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `training_options` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($training_options->num_rows > 0) {
			        while ($row = $training_options->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $training_options->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'training_types') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['name'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `training_types` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $training_types = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `training_types` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($training_types->num_rows > 0) {
			        while ($row = $training_types->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $training_types->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'grant_codes') {
				$orderBy = 'name';
				$order = 'ASC';
				$searchParam = $_POST['search']['value'] ?? '';
				$start = $_POST['start'] ?? 0;
				$length = $_POST['length'] ?? 10;

				if (isset($_POST['order']) && isset($_POST['order'][0])) {
					$orderColumnMap = ['name'];
					$orderByIndex = (int)$_POST['order'][0]['column'];
					$orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
					$order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}

				$query = "SELECT * FROM `grant_codes` WHERE `id` IS NOT NULL";
				if ($searchParam) {
					$query .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
				}

				$query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

				$grant_codes = $GLOBALS['conn']->query($query);

				$countQuery = "SELECT COUNT(*) as total FROM `grant_codes` WHERE `id` IS NOT NULL";
				if ($searchParam) {
					$countQuery .= " AND (`name` LIKE '%" . escapeStr($searchParam) . "%')";
				}

				$totalRecordsResult = $GLOBALS['conn']->query($countQuery);
				$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

				if ($grant_codes->num_rows > 0) {
					while ($row = $grant_codes->fetch_assoc()) {
						$result['data'][] = $row;
					}
					$result['iTotalRecords'] = $totalRecords;
					$result['iTotalDisplayRecords'] = $totalRecords;
					$result['msg'] = $grant_codes->num_rows . " records were found.";
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
					$stateInfo1 = $stateInfo[0];
					$taxGrid = json_decode($stateInfo1['tax_grid']);
					$stateInfo[0]['tax_grid'] = $taxGrid;
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
			} else if ($_GET['endpoint'] === 'goal_type') {
				json(get_data('goal_types', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'award_type') {
				json(get_data('award_types', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'financial_account') {
				json(get_data('financial_accounts', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'training_options') {
				json(get_data('training_options', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'training_types') {
				json(get_data('training_types', array('id' => $_POST['id'])));
			} else if ($_GET['endpoint'] === 'grant_code' && $_GET['action'] == 'get') {
				json(get_data('grant_codes', array('id' => $_POST['id'])));
			}

			exit();
		}


		// Delete data
		else if($_GET['action'] == 'delete') {
			if ($_GET['endpoint'] === 'company') {
				try {
				    // Delete company
				    check_auth('delete_organization');
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
				    check_auth('delete_departments');
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
				    check_auth('delete_states');
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
				    check_auth('delete_duty_locations');
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
				    check_auth('delete_bank_accounts');
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
				    check_auth('delete_designations');
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
				    check_auth('delete_projects');
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
				    check_auth('delete_contract_types');
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
				    check_auth('delete_bank_accounts');
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
				    check_auth('delete_budget_codes');
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
				    check_auth('delete_transaction_subtypes');
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
			} else if ($_GET['endpoint'] === 'goal_type') {
				try {
				    // Delete branchClass
				    $post = escapePostData($_POST);
				    check_auth('delete_goal_types');
				    $deleted = $goalTypesClass->delete($post['id']);

				    // Company deleted
				    if($deleted) {
				        $result['msg'] = 'Goal type has been  deleted successfully';
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
			} else if ($_GET['endpoint'] === 'award_type') {
				try {
				    // Delete award type
				    $post = escapePostData($_POST);
				    check_auth('delete_award_types');
				    $deleted = $awardTypesClass->delete($post['id']);

				    // Award type deleted
				    if($deleted) {
				        $result['msg'] = 'Award type has been deleted successfully';
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
			} else if ($_GET['endpoint'] === 'financial_account') {
				try {
				    // Delete financial account
				    $post = escapePostData($_POST);
				    check_auth('delete_financial_accounts');
				    $deleted = $financialAccountsClass->delete($post['id']);

				    // Financial account deleted
				    if($deleted) {
				        $result['msg'] = 'Financial account has been deleted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the delete method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'training_options') {
				try {
				    // Delete training option
				    $post = escapePostData($_POST);
				    check_auth('delete_training_options');
				    $deleted = $trainingOptionsClass->delete($post['id']);

				    // Training option deleted
				    if($deleted) {
				        $result['msg'] = 'Training option has been deleted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the delete method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'training_types') {
				try {
				    // Delete training type
				    $post = escapePostData($_POST);
				    check_auth('delete_training_types');
				    $deleted = $trainingTypesClass->delete($post['id']);

				    // Training type deleted
				    if($deleted) {
				        $result['msg'] = 'Training type has been deleted successfully';
				        $result['error'] = false;
				    } else {
				        $result['msg'] = 'Something went wrong, please try again';
				        $result['error'] = true;
				    }

				} catch (Exception $e) {
				    // Catch any exceptions from the delete method and return an error message
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response (for example in an API)
				echo json_encode($result);
			} else if ($_GET['endpoint'] === 'grant_code' && $_GET['action'] == 'delete') {
				try {
					$post = escapePostData($_POST);
					check_auth('delete_budget_codes');
					$deleted = $grantCodesClass->delete($post['id']);

					// Check if this grant code id is found in table budget_codes column grant_code_id
					$found = get_data('budget_codes', array('grant_code_id' => $post['id']));
					if ($found) {
						$result['msg'] = 'Grant code is being used in budget codes, please remove it first';
						$result['error'] = true;
						return json_encode($result);
					}
					if ($deleted) {
						$result['msg'] = 'Grant code deleted successfully';
						$result['error'] = false;
					} else {
						$result['msg'] = 'Something went wrong, please try again';
						$result['error'] = true;
					}

				} catch (Exception $e) {
					$result['msg'] = 'Error: Something went wrong';
					$result['sql_error'] = $e->getMessage();
					$result['error'] = true;
				}

				echo json_encode($result);
			}

			exit();
		}
	}
}

?>