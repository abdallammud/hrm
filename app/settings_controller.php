<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Update data
		if($_GET['action'] == 'update') {
			if($_GET['endpoint'] == 'setting') {
				try {
					$post = escapePostData($_POST);
				    $data = array(
				        'type' => isset($post['type']) ? $post['type']: "" , 
				        'details' => isset($post['details']) ? $post['details']: "" , 
				        'value' => isset($post['value']) ? $post['value']: "" , 
				        'section' => isset($post['section']) ? $post['section']: "" , 
				        'remarks' => isset($post['remarks']) ? $post['remarks']: "" , 
				    );

					if($post['type'] == 'disabled_features') {
						if(isset($post['value'])) {
							$data['value'] = json_encode($post['value']);
						} else {
							$data['value'] = '[]';
						}
						$data['section'] = 'admin';
						$data['details'] = 'Disabled features';
						$data['remarks'] = 'required';
						
					}
					
					// Check setting already exis
					$sql = "SELECT * FROM sys_settings WHERE `type` = '".$post['type']."'";
					$setting = $GLOBALS['conn']->query($sql)->num_rows;
				    check_auth('edit_settings');


				    if($setting == 0) {
				    	$done = $settingsClass->create($data);
				    } else {
				    	// Update settings
						$sql = "UPDATE sys_settings SET `value` = '".$data['value']."' WHERE `type` = '".$data['type']."'";
						$done = $GLOBALS['conn']->query($sql);
				    }

				    if($done) {
				        $result['msg'] = 'Changed successfully';
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

			}
		}

		// Get data
		else if($_GET['action'] == 'get') {
			if ($_GET['endpoint'] === 'setting') {
				$post = escapePostData($_POST);
				json(get_setting($post['type']));
			} else if ($_GET['endpoint'] === 'branch') {
				json(get_data('branches', array('id' => $_POST['id'])));
			}

			exit();
		}
		
	}
}

?>