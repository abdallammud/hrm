<?php 
function load_files() {
    // Load menu configuration
    $menus = get_menu_config();

    // Extract parameters
    $menu = $_GET['menu'] ?? null;
    $action = $_GET['action'] ?? null;
    $tab = $_GET['tab'] ?? null;

    // Load dashboard by default if no menu is specified
    if (!$menu || $menu == 'dashboard' || !array_key_exists($menu, $menus)) {
        load_file('dashboard.php');
        return;
    }

    $folder = $menus[$menu]['folder'] . '/';
    $defaultFile = $menus[$menu]['default'];
    $authKey = $menus[$menu]['auth'];

    // var_dump($authKey);

    // Handle submenus and their actions
    // var_dump($menus[$menu]['sub'][$tab]);
    if ($tab && isset($menus[$menu]['sub'][$tab])) {
        handle_sub_menu($menus[$menu]['sub'][$tab], $folder, $action);
    } 
    // Handle top-level menu actions
    else if ($action && isset($menus[$menu]['actions'][$action])) {
        handle_action($menus[$menu]['actions'][$action], $folder);
    } 
    // Load the default file for the menu
    else {
        if (check_session($authKey)) {
            load_file($folder . $defaultFile . '.php');
        } else {
            load_unauthorized();
        }
    }
}

function handle_sub_menu($subMenu, $folder, $action) {
    if ($action && isset($subMenu['actions'][$action])) {
        handle_action($subMenu['actions'][$action], $folder);
    } else {
        if (check_session($subMenu['auth'])) {
            // var_dump($subMenu['default']);
            load_file($folder . $subMenu['default'] . '.php');
        } else {
            load_unauthorized();
        }
    }
}

function handle_action($actionConfig, $folder) {
    $file = $actionConfig['file'] ?? null;
    $authKey = $actionConfig['auth'] ?? null;

    if ($file && check_session($authKey)) {
        load_file($folder . $file . '.php');
    } else {
        load_unauthorized();
    }
}

function load_file($filePath) {
    if (file_exists($filePath)) {
        require $filePath;
    } else {
        load_not_found();
    }
}

function load_unauthorized() {
    require '403.php'; // Unauthorized access page
    // exit;
}

function load_not_found() {
    require '404.php'; // Page not found
    // exit;
}

function get_menu_config() {
    return [
        'dashboard' => [
            // 'folder' => 'hrm',
            'default' => 'dashboard',
            'icon' => 'home',
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'menu' => 'dashboard',
            'auth' => 'view_dashboard',
        ],
        'org' => [
            'folder' => 'organization',
            'default' => 'org',
            'name' => 'Organization',
            'icon' => 'home',
            'menu' => 'org',
            'route' => 'org',
            'auth' => ['manage_company_info', 'manage_departments', 'manage_duty_locations', 'manage_company_banks'],
            'actions' => [
                'show' => ['file' => 'org_show', 'auth' => 'view_company']
            ],
            'sub' => [
               'setup' => [
                    'default' => 'org',
                    'name' => 'Set up',
                    'route' => 'org',
                    'auth' => 'manage_company_info',
                    'actions' => [
                        // 'show' => ['file' => 'chart_show', 'auth' => 'view_chart']
                    ],
                ],
                'branches' => [
                    'default' => 'branches',
                    'auth' => 'manage_departments',
                    'route' => $GLOBALS['branch_keyword']['plu'],
                    'name' => $GLOBALS['branch_keyword']['plu'],
                    'actions' => [
                        'show' => ['file' => 'branch_show', 'auth' => 'view_branch']
                    ],
                ],
                'locations' => [
                    'default' => 'locations',
                    'name' => 'Duty Locations',
                    'route' => 'locations',
                    'auth' => 'manage_duty_locations',
                    'actions' => [
                        'show' => ['file' => 'chart_show', 'auth' => 'view_chart']
                    ],
                ],
               
                'banks' => [
                    'default' => 'banks',
                    'name' => 'Bank accounts',
                    'route' => 'banks',
                    'auth' => 'manage_company_banks',
                    'actions' => [
                        'show' => 'chart_show'
                    ],
                ],

                'misc' => [
                    'default' => 'misc',
                    'name' => 'Miscellaneous',
                    'route' => 'misc',
                    'auth' => 'manage_departments',
                ],
                // Add other submenus here
            ],
        ],

        'hrm' => [
            'folder' => 'hrm',
            'default' => 'employees',
            'name' => 'HRM',
            'icon' => 'people',
            'route' => 'employees',
            'menu' => 'hrm',
            'auth' => ['view_employees', 'manage_designations'],
            'sub' => [
                'employees' => [
                    'default' => 'employees',
                    'auth' => 'view_employees',
                    'route' => 'employees',
                    'name' => 'Employees',
                    'actions' => [
                        'add' => ['file' => 'employee_add', 'auth' => 'add_employee'],
                        'show' => ['file' => 'employee_show', 'auth' => 'view_employees'],
                        'edit' => ['file' => 'employee_edit', 'auth' => 'edit_employee'],
                    ],
                ],
                
            ],
        ],

        'payroll' => [
            'folder' => 'payroll_fol',
            'default' => 'payroll',
            'name' => 'Payroll',
            'icon' => 'calculate',
            'menu' => 'payroll',
            'route' => 'payroll',
            'auth' => ['view_payroll', 'manage_employee_transactions'],
            'sub' => [
                'payroll' => [
                    'default' => 'payroll',
                    'auth' => 'view_payroll',
                    'route' => 'payroll',
                    'name' => 'Payroll',
                    'actions' => [
                        // 'add' => ['file' => 'employee_add', 'auth' => 'add_employee'],
                        'show' => ['file' => 'payroll_show', 'auth' => 'view_payroll'],
                    ],
                ],
                'transactions' => [
                    'default' => 'transactions',
                    'auth' => 'manage_employee_transactions',
                    'route' => 'transactions',
                    'name' => 'Transactions',
                ],
            ],
        ],

        'attendance' => [
            'folder' => 'attendance_fol',
            'default' => 'attendance',
            'name' => 'Attendance',
            'icon' => 'list_alt',
            'route' => 'attendance',
            'menu' => 'attendance',
            'auth' => ['view_attendance', 'manage_timesheets', 'manage_leaves'],
            'sub' => [
                'attendance' => [
                    'default' => 'attendance',
                    'auth' => 'view_attendance',
                    'route' => 'attendance',
                    'name' => 'Attendance',
                    'actions' => [
                        // 'add' => ['file' => 'employee_add', 'auth' => 'add_employee'],
                        // 'show' => ['file' => 'employee_show', 'auth' => 'view_employees'],
                    ],
                ],
                'timesheet' => [
                    'default' => 'timesheet',
                    'auth' => 'manage_timesheets',
                    'route' => 'timesheet',
                    'name' => 'Timesheets',
                ],

                'leave' => [
                    'default' => 'leave_mgt',
                    'auth' => 'manage_leaves',
                    'route' => 'leave',
                    'name' => 'Leave Mgt',
                ],
            ],
        ],

        /*'payments' => [
            'folder' => 'payments_fol',
            'default' => 'payments',
            'name' => 'Payments',
            'icon' => 'payments',
            'route' => 'payments',
            'menu' => 'payments',
            'auth' => 'process_payments',
            'actions' => [
                // 'add' => ['file' => 'user_add', 'auth' => 'add_user'],
                // 'edit' => ['file' => 'user_edit', 'auth' => 'edit_user'],
                // 'show' => ['file' => 'user_show', 'auth' => 'manage_users'],
            ],
            
        ],*/

        'users' => [
            'folder' => 'system',
            'default' => 'users',
            'name' => 'Users',
            'icon' => 'engineering',
            'route' => 'user',
            'menu' => 'users',
            'auth' => 'manage_users',
            'actions' => [
                'add' => ['file' => 'user_add', 'auth' => 'add_user'],
                'edit' => ['file' => 'user_edit', 'auth' => 'edit_user'],
                'show' => ['file' => 'user_show', 'auth' => 'manage_users'],
            ],
            
        ],

        'reports' => [
            'folder' => 'reports_fol',
            'default' => 'reports',
            'name' => 'Reports',
            'icon' => 'bar_chart',
            'route' => 'reports',
            'menu' => 'reports',
            'auth' => 'view_reports',
            'actions' => [
                // 'add' => ['file' => 'user_add', 'auth' => 'add_user'],
                // 'edit' => ['file' => 'user_edit', 'auth' => 'edit_user'],
                // 'show' => ['file' => 'user_show', 'auth' => 'manage_users'],
            ],
            
        ],
        

        'settings' => [
            'folder' => 'settings_fol',
            'default' => 'settings',
            'name' => 'settings',
            'icon' => 'settings',
            'route' => 'settings',
            'menu' => 'settings',
            'auth' => 'manage_settings',
            'actions' => [
                // 'add' => ['file' => 'user_add', 'auth' => 'add_user'],
                // 'edit' => ['file' => 'user_edit', 'auth' => 'edit_user'],
                // 'show' => ['file' => 'user_show', 'auth' => 'manage_users'],
            ],
            
        ],
        // Add more menus here
    ];
}


