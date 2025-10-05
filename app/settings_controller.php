<?php
require('init.php');
if(isset($_GET['action'])) {
    if($_GET['action'] == 'update') {
        if($_GET['endpoint'] == 'setting') {
            try {
                // If there's a file upload for logo, handle it separately
                if (isset($_FILES['logoFile']) && !empty($_FILES['logoFile']['name'])) {
                    // Authorization
                    check_auth('edit_settings');

                    $uploadDir = __DIR__ . '/../assets/images/';
                    if (!is_dir($uploadDir)) {
                        // attempt to create it if missing
                        @mkdir($uploadDir, 0755, true);
                    }

                    $file = $_FILES['logoFile'];
                    // Basic validation
                    $allowed = ['image/png','image/jpeg','image/jpg'];
                    if (!in_array($file['type'], $allowed)) {
                        echo json_encode(['error' => true, 'msg' => 'Invalid file type. Only PNG/JPG allowed.']);
                        exit();
                    }
                    if ($file['size'] > 2 * 1024 * 1024) { // 2MB
                        echo json_encode(['error' => true, 'msg' => 'File too large. Max 2MB.']);
                        exit();
                    }

                    // create safe filename: system_logo_TIMESTAMP.ext
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $safeName = 'system_logo_' . time() . '.' . $ext;
                    $target = $uploadDir . $safeName;

                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        // Update DB: store the filename (not full path). In display, we will prepend assets/images/
                        $valueToStore = $safeName;

                        // check if setting exists
                        $type = 'system_logo';
                        $sql = "SELECT * FROM sys_settings WHERE `type` = '".$GLOBALS['conn']->real_escape_string($type)."'";
                        $settingExists = $GLOBALS['conn']->query($sql)->num_rows;

                        if ($settingExists == 0) {
                            $data = [
                                'type' => $type,
                                'details' => 'System logo',
                                'value' => $GLOBALS['conn']->real_escape_string($valueToStore),
                                'section' => 'system',
                                'remarks' => 'required'
                            ];
                            $done = $settingsClass->create($data);
                        } else {
                            $sql = "UPDATE sys_settings SET `value` = '".$GLOBALS['conn']->real_escape_string($valueToStore)."' WHERE `type` = '".$GLOBALS['conn']->real_escape_string($type)."'";
                            $done = $GLOBALS['conn']->query($sql);
                        }

                        if ($done) {
                            $result['msg'] = 'Logo uploaded successfully';
                            $result['error'] = false;
                            $result['value'] = $valueToStore;
                        } else {
                            $result['msg'] = 'Failed to save logo in database';
                            $result['error'] = true;
                        }
                    } else {
                        $result['msg'] = 'Failed to upload file';
                        $result['error'] = true;
                    }

                    echo json_encode($result);
                    exit();
                }

                // Regular settings (non-file)
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

                // For color settings: value expected to be rgb(...) — no special handling required here
                // Check setting already exists
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
                $result['sql_error'] = $e->getMessage();
                $result['error'] = true;
            }

            echo json_encode($result);
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


?>