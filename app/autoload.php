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
    // var_dump($menus[$menu]['sub']);
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
            'icon' => 'dashboard',
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'menu' => 'dashboard',
            'auth' => 'manage_dashboard',
        ],
        'org' => [
            'folder' => 'organization',
            'default' => 'org',
            'name' => 'Organization',
            'icon' => 'home',
            'menu' => 'org',
            'route' => 'org',
            'auth' => ['manage_organization', 'manage_departments', 'manage_duty_locations', 'manage_states', 'manage_bank_accounts', 'manage_designations', 'manage_projects', 'manage_budget_codes', 'manage_contract_types', 'manage_transaction_subtypes', 'manage_gaol_types'],
            'actions' => [
                'show' => ['file' => 'org_show', 'auth' => 'view_company']
            ],
            'sub' => [
               'setup' => [
                    'default' => 'org',
                    'name' => 'Set up',
                    'route' => 'org',
                    'auth' => 'manage_organization',
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
                    'auth' => 'manage_bank_accounts',
                    'actions' => [
                        'show' => 'chart_show'
                    ],
                ],

                'misc' => [
                    'default' => 'misc',
                    'name' => 'Miscellaneous',
                    'route' => 'misc',
                    'auth' => ['manage_designations', 'manage_projects', 'manage_budget_codes', 'manage_contract_types', 'manage_transaction_subtypes', 'manage_gaol_types'],
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
            'auth' => ['manage_employees', 'manage_employee_docs'],
            'sub' => [
                'employees' => [
                    'default' => 'employees',
                    'auth' => 'manage_employees',
                    'route' => 'employees',
                    'name' => 'Employees',
                    'actions' => [
                        'add' => ['file' => 'employee_add', 'auth' => 'create_employees'],
                        'show' => ['file' => 'employee_show', 'auth' => 'manage_employees'],
                        'edit' => ['file' => 'employee_edit', 'auth' => 'edit_employees'],
                    ],
                ],
                'documents' => [
                    'default' => 'documents',
                    'auth' => 'manage_employee_docs',
                    'route' => 'documents',
                    'name' => 'Documents',
                    'js' => ['docs'],
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
            'auth' => ['manage_payroll', 'manage_payroll_transactions'],
            'sub' => [
                'payroll' => [
                    'default' => 'payroll',
                    'auth' => 'manage_payroll',
                    'route' => 'payroll',
                    'name' => 'Payroll',
                    'actions' => [
                        // 'add' => ['file' => 'employee_add', 'auth' => 'add_employee'],
                        'show' => ['file' => 'payroll_show', 'auth' => 'view_payroll'],
                    ],
                ],
                'transactions' => [
                    'default' => 'transactions',
                    'auth' => 'manage_payroll_transactions',
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
            'auth' => ['manage_attendance', 'manage_timesheet', 'manage_leave', 'manage_leave_types'],
            'sub' => [
                'attendance' => [
                    'default' => 'attendance',
                    'auth' => 'manage_attendance',
                    'route' => 'attendance',
                    'name' => 'Attendance',
                    'actions' => [
                        'add' => ['file' => 'add_attendance', 'auth' => 'create_attendance'],
                        // 'show' => ['file' => 'employee_show', 'auth' => 'view_employees'],
                    ],
                ],
                'timesheet' => [
                    'default' => 'timesheet',
                    'auth' => 'manage_timesheet',
                    'route' => 'timesheet',
                    'name' => 'Timesheets',
                    'actions' => [
                        'add' => ['file' => 'add_timesheet_bulk', 'auth' => 'create_timesheet'],
                        // 'show' => ['file' => 'employee_show', 'auth' => 'view_employees'],
                    ],
                ],

                'allocation' => [
                    'default' => 'res_allocation',
                    'auth' => 'manage_allocation',
                    'route' => 'allocation',
                    'name' => 'Timesheet Allocation',
                    'actions' => [
                        'add' => ['file' => 'manage_allocation', 'auth' => 'create_allocation'],
                        // 'show' => ['file' => 'employee_show', 'auth' => 'view_employees'],
                    ],
                ],

                'leave' => [
                    'default' => 'leave_mgt',
                    'auth' => 'manage_leave',
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

        /* 'users' => [
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
            
        ], */

        'users' => [
            'folder' => 'system',
            'default' => 'users',
            'name' => 'Users',
            'icon' => 'engineering',
            'route' => 'user',
            'menu' => 'users',
            'auth' => 'manage_users',
            'sub' => [
                'users' => [
                    'default' => 'users',
                    'auth' => 'manage_users',
                    'route' => 'user',
                    'name' => 'Users',
                    'actions' => [
                        'add' => ['file' => 'user_add', 'auth' => 'create_users'],
                        'edit' => ['file' => 'user_edit', 'auth' => 'edit_users'],
                        'show' => ['file' => 'user_show', 'auth' => 'manage_users'],
                    ],
                ],
                'roles' => [
                    'default' => 'roles',
                    'auth' => 'manage_roles',
                    'route' => 'roles',
                    'name' => 'Roles',
                    'menu' => 'users',
                ],
            ],
        ],

        'reports' => [
            'folder' => 'reports_fol',
            'default' => 'reports',
            'name' => 'Reports',
            'icon' => 'bar_chart',
            'route' => 'reports',
            'menu' => 'reports',
            'auth' => 'manage_reports',
            'actions' => [
                // 'add' => ['file' => 'user_add', 'auth' => 'add_user'],
                // 'edit' => ['file' => 'user_edit', 'auth' => 'edit_user'],
                'show' => ['file' => 'report_show', 'auth' => 'manage_reports'],
            ],
            
        ],
        

        'settings' => [
            'folder' => 'settings_fol',
            'default' => 'settings',
            'name' => 'Settings',
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
