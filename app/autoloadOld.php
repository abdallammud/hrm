<?php 
function load_files() {
    if (!isset($_GET['menu'])) {
        require 'dashboard.php';
        return;
    }

    $menu = $_GET['menu'];
    $action = $_GET['action'] ?? null;
    $tab = $_GET['tab'] ?? null;

    // Define menu permissions and file paths
    $menus = [
        'org' => [
            'folder' => 'organization',
            'default' => 'org',
            'name' => 'org',
            'auth' => 'manage_company_info',
            'actions' => [
                'show' => ['file' => 'org_show', 'auth' => 'view_company']
            ],
            'sub' => [
                'branches' => [
                    'default' => 'branches',
                    'name' => 'branches',
                    'auth' => 'manage_departments',
                    'actions' => [
                        'show' => ['file' => 'branch_show', 'auth' => 'view_branch']
                    ],
                ],
                'locations' => [
                    'default' => 'locations',
                    'name' => 'locations',
                    'auth' => 'manage_duty_locations',
                    'actions' => [
                        'show' => ['file' => 'chart_show', 'auth' => 'view_chart']
                    ],
                ],
                'currency' => [
                    'default' => 'currency',
                    'name' => 'currency',
                    'auth' => 'manage_company_info',
                    'actions' => [
                        'show' => ['file' => 'chart_show', 'auth' => 'view_chart']
                    ],
                ],
                'banks' => [
                    'default' => 'banks',
                    'name' => 'banks',
                    'auth' => 'manage_company_banks',
                    'actions' => [
                        'show' => 'chart_show'
                    ],
                ],
            ]
        ],
        'hrm' => [
            'folder' => 'hrm',
            'default' => 'employees',
            'name' => 'employees',
            'auth' => 'view_employees',
            'actions' => [
                
            ],
            'sub' => [
                'employees' => [
                    'default' => 'employees',
                    'name' => 'employees',
                    'auth' => 'view_employees',
                    'actions' => [
                        'add' => ['file' => 'employee_add', 'auth' => 'add_employee']
                        'show' => ['file' => 'employee_show', 'auth' => 'view_employees']
                    ],
                ]
            ]
        ],
        'users' => [
            'folder' => 'hrm',
            'default' => 'users',
            'name' => 'users',
            'auth' => 'manage_users',
            'actions' => [
                    'add' => ['file' => 'user_add', 'auth' => 'add_user'],
                    'edit' => ['file' => 'user_edit', 'auth' => 'edit_user'],
                    'edit' => ['file' => 'user_show', 'auth' => 'manage_users'],
                ],
            'actions' => [
                'add' => 'user_add',
                'show' => 'user_show',
                'edit' => 'user_edit'
            ]
        ],
    ];

    // Check if menu exists
    if (!array_key_exists($menu, $menus)) {
        require '404.php';
        return;
    }

    $folder = $menus[$menu]['folder'] . '/';

    // Check if tab exists and if action matches a sub-tab
    if ($tab && isset($menus[$menu]['sub'][$tab])) {
        $subMenu = $menus[$menu]['sub'][$tab];
        if ($action && isset($subMenu['actions'][$action])) {
            if(check_session($subMenu['actions'][$action]['auth'])) {
                require $folder . $subMenu['actions'][$action] . '.php';
            } else {
                require '404.php';
                return;
            }
        } else {
            if(check_session($subMenu['auth'])) {
                require $folder . $subMenu['default'] . '.php';
            } else {
                require '404.php';
                return;
            }
        }

    // If action matches the top-level menu
    } else if ($action && isset($menus[$menu]['actions'][$action])) {
        if(check_session($menus[$menu]['actions'][$action]['auth'])) {
            require $folder . $menus[$menu]['actions'][$action]['file'] . '.php';
        } else {
            require '404.php';
            return;
        }
    // Load the default file if no action or sub-tab is specified
    } else {
        if(check_session($menus[$menu]['auth'])) {
            require $folder . $menus[$menu]['default'] . '.php';
        } else {
            require '404.php';
            return;
        }
        
    }
}

function check_session($session) {
    if(is_array($session)) {
        $result = false;
        foreach ($session as $sessionVar) {
            $result isset($_SESSION[$sessionVar]) && $_SESSION[$sessionVar] == 'on';
        }
        return $result;
    } else {
        return isset($_SESSION[$session]) && $_SESSION[$session] == 'on';
    }
    
}


?>
