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

				    $setting = get_data('sys_settings', array('type' => $post['type']));
				    check_auth('manage_states');

				    if(!$setting) {
				    	$done = $settingsClass->create($data);
				    } else {
				    	$done = $settingsClass->update($post['type'], $data);
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