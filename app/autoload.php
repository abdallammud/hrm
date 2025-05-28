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
    // var_dump($menus);
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
    // var_dump($subMenu);
    if ($action && isset($subMenu['actions'][$action])) {
        handle_action($subMenu['actions'][$action], $folder);
    } else {
        if (check_session($subMenu['auth'])) {
            // var_dump($subMenu['default']);
            if(isset($subMenu['coming'])) {
                load_file($folder . $subMenu['default'] . '.php', true);
            } else {
                load_file($folder . $subMenu['default'] . '.php');
            }
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

function load_file($filePath, $coming = false) {
    if($coming) {
        require 'coming.php';
    } else {
        if (file_exists($filePath)) {
            require $filePath;
        } else {
            load_not_found();
        }
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

function get_menu_config2() {
    return [
        'dashboard' => [
            // 'folder' => 'hrm',
            'default' => 'dashboard',
            'icon' => 'columns-gap',
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'menu' => 'dashboard',
            'auth' => 'manage_dashboard',
        ],
        'org' => [
            'folder' => 'organization',
            'default' => 'org',
            'name' => 'Organization',
            'icon' => 'house-door',
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
            'icon' => 'calculator',
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
            'icon' => 'list-check',
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
            'icon' => 'person-gear',
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
            'icon' => 'graph-up',
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
            'icon' => 'gear',
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

function get_menu_config() {
    return [
        'dashboard' => [
            'default' => 'dashboard',
            'folder' => 'dashboard',
            'icon' => 'columns-gap',
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'menu' => 'dashboard',
            'auth' => 'manage_dashboard',
        ],

        'hrm' => [
            'default' => 'employees',
            'folder' => 'hrm',
            'name' => 'Employees',
            'icon' => 'people',
            'route' => 'employees',
            'menu' => 'employees',
            'auth' => 'manage_employees',
            'sub' => [
                'employees' => [
                    'default' => 'employees',
                    'name' => 'All Employees',
                    'route' => 'employees',
                    'auth' => 'manage_employees',
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
                'awards' => [
                    'default' => 'awards',
                    'folder' => 'awards',
                    'name' => 'Awards',
                    'route' => 'awards',
                    'auth' => 'manage_awards',
                    'coming' => true,
                ],
                'contracts' => [
                    'default' => 'contracts',
                    'folder' => 'contracts',
                    'name' => 'Contracts',
                    'route' => 'contracts',
                    'auth' => 'manage_contracts',
                    'coming' => true,
                ],
            ],
        ],

        'payroll' => [
            'default' => 'payroll',
            'folder' => 'payroll_fol',
            'name' => 'Payroll',
            'icon' => 'calculator',
            'menu' => 'payroll',
            'route' => 'payroll',
            'auth' => 'manage_payroll',
            'sub' => [
                'manage_payroll' => [
                    'default' => 'payroll',
                    'folder' => 'payroll',
                    'name' => 'Manage Payroll',
                    'route' => 'payroll',
                    'auth' => 'manage_payroll',
                ],
                'transactions' => [
                    'default' => 'transactions',
                    'folder' => 'transactions',
                    'name' => 'Transactions',
                    'route' => 'transactions',
                    'auth' => 'manage_payroll_transactions',
                ],
            ],
        ],

        'attendance' => [
            'default' => 'timesheet',
            'folder' => 'attendance_fol',
            'name' => 'Timesheet',
            'icon' => 'clock-history',
            'route' => 'timesheet',
            'menu' => 'timesheet',
            'auth' => 'manage_timesheet',
            'sub' => [
                'timesheet' => [
                    'default' => 'timesheet',
                    'name' => 'Timesheet',
                    'route' => 'timesheet',
                    'auth' => 'manage_timesheet',
                ],
                'attendance' => [
                    'default' => 'attendance',
                    'folder' => 'attendance',
                    'name' => 'Attendance',
                    'route' => 'attendance',
                    'auth' => 'manage_attendance',
                ],
                'manage_leave' => [
                    'default' => 'leave',
                    'folder' => 'leave',
                    'name' => 'Manage Leave',
                    'route' => 'leave',
                    'auth' => 'manage_leave',
                ],
                'allocation' => [
                    'default' => 'allocation',
                    'folder' => 'allocation',
                    'name' => 'Timesheet Allocation',
                    'route' => 'allocation',
                    'auth' => 'manage_allocation',
                ],
            ],
        ],

        'performance' => [
            'default' => 'performance',
            'folder' => 'performance',
            'name' => 'Performance',
            'icon' => 'trophy',
            'route' => 'performance',
            'menu' => 'performance',
            'auth' => 'manage_performance',
            'sub' => [
                'appraisals' => [
                    'default' => 'appraisals',
                    'folder' => 'appraisals',
                    'name' => 'Appraisals',
                    'route' => 'appraisals',
                    'auth' => 'manage_appraisals',
                ],
                'indicators' => [
                    'default' => 'indicators',
                    'folder' => 'indicators',
                    'name' => 'Indicators',
                    'route' => 'indicators',
                    'auth' => 'manage_indicators',
                ],
                'goal_tracking' => [
                    'default' => 'goals',
                    'folder' => 'goals',
                    'name' => 'Goal Tracking',
                    'route' => 'goals',
                    'auth' => 'manage_goals',
                ],
            ],
        ],

        'finance' => [
            'default' => 'finance',
            'folder' => 'finance',
            'name' => 'Finance',
            'icon' => 'currency-dollar',
            'route' => 'finance',
            'menu' => 'finance',
            'auth' => 'manage_finance',
            'sub' => [
                'accounts' => [
                    'default' => 'accounts',
                    'folder' => 'accounts',
                    'name' => 'Manage Accounts',
                    'route' => 'accounts',
                    'auth' => 'manage_accounts',
                ],
                'payroll_payment' => [
                    'default' => 'payroll_payment',
                    'folder' => 'payroll_payment',
                    'name' => 'Payroll Payment',
                    'route' => 'payroll_payment',
                    'auth' => 'manage_payroll_payment',
                ],
                'expenses' => [
                    'default' => 'expenses',
                    'folder' => 'expenses',
                    'name' => 'Other Expenses',
                    'route' => 'expenses',
                    'auth' => 'manage_expenses',
                ],
            ],
        ],

        'reports' => [
            'default' => 'reports',
            'folder' => 'reports',
            'name' => 'Reports',
            'icon' => 'graph-up',
            'route' => 'reports',
            'menu' => 'reports',
            'auth' => 'manage_reports',
        ],

        'training' => [
            'default' => 'training',
            'folder' => 'training',
            'name' => 'Training',
            'icon' => 'book',
            'route' => 'training',
            'menu' => 'training',
            'auth' => 'manage_training',
            'sub' => [
                'list' => [
                    'default' => 'training_list',
                    'folder' => 'training_list',
                    'name' => 'Training List',
                    'route' => 'training_list',
                    'auth' => 'manage_training',
                ],
                'trainers' => [
                    'default' => 'trainers',
                    'folder' => 'trainers',
                    'name' => 'Trainers',
                    'route' => 'trainers',
                    'auth' => 'manage_trainers',
                ],
            ],
        ],

        'management' => [
            'default' => 'management',
            'folder' => 'management',
            'name' => 'Management',
            'icon' => 'briefcase',
            'route' => 'management',
            'menu' => 'management',
            'auth' => 'manage_hrm',
            'sub' => [
                'resignations' => [
                    'default' => 'resignations',
                    'folder' => 'resignations',
                    'name' => 'Resignations',
                    'route' => 'resignations',
                    'auth' => 'manage_resignations',
                ],
                'transfers' => [
                    'default' => 'transfers',
                    'folder' => 'transfers',
                    'name' => 'Transfers',
                    'route' => 'transfers',
                    'auth' => 'manage_transfers',
                ],
                'promotion' => [
                    'default' => 'promotion',
                    'folder' => 'promotion',
                    'name' => 'Promotion',
                    'route' => 'promotion',
                    'auth' => 'manage_promotion',
                ],
                'complaints' => [
                    'default' => 'complaints',
                    'folder' => 'complaints',
                    'name' => 'Complaints',
                    'route' => 'complaints',
                    'auth' => 'manage_complaints',
                ],
                'warning' => [
                    'default' => 'warning',
                    'folder' => 'warning',
                    'name' => 'Warning',
                    'route' => 'warning',
                    'auth' => 'manage_warnings',
                ],
                'termination' => [
                    'default' => 'termination',
                    'folder' => 'termination',
                    'name' => 'Termination',
                    'route' => 'termination',
                    'auth' => 'manage_termination',
                ],
            ],
        ],

        'recruitment' => [
            'default' => 'recruitment',
            'folder' => 'recruitment',
            'name' => 'Recruitment',
            'icon' => 'person-add',
            'route' => 'recruitment',
            'menu' => 'recruitment',
            'auth' => 'manage_recruitment',
        ],

        'system_setup' => [
            'default' => 'system_setup',
            'folder' => 'system_setup',
            'name' => 'System Setup',
            'icon' => 'tools',
            'route' => 'system_setup',
            'menu' => 'system_setup',
            'auth' => 'manage_system_setup',
        ],

        'settings' => [
            'default' => 'settings',
            'folder' => 'settings',
            'name' => 'Settings',
            'icon' => 'gear',
            'route' => 'settings',
            'menu' => 'settings',
            'auth' => 'manage_settings',
        ],

        'users' => [
            'default' => 'users',
            'folder' => 'system',
            'name' => 'Users',
            'icon' => 'person-gear',
            'route' => 'user',
            'menu' => 'users',
            'auth' => 'manage_users',
            'sub' => [
                'users' => [
                    'default' => 'users',
                    'folder' => 'users',
                    'name' => 'Users',
                    'route' => 'user',
                    'auth' => 'manage_users',
                ],
                'roles' => [
                    'default' => 'roles',
                    'folder' => 'roles',
                    'name' => 'Roles',
                    'route' => 'roles',
                    'auth' => 'manage_roles',
                ],
            ],
        ],
    ];
}