# File Structure and Organization

## Overview

The HRM application follows a modular file organization structure that separates concerns between different functional areas. This document provides a comprehensive mapping of the application's directory structure and explains the purpose of each component.

## Root Directory Structure

```
hrm-application/
├── .git/                          # Git version control
├── .htaccess                      # Apache configuration
├── .kiro/                         # Kiro IDE configuration
├── 403.php                        # Unauthorized access page
├── 404.php                        # Page not found
├── app/                           # Core application logic
├── assets/                        # Static assets (CSS, JS, images)
├── asset_config.php               # Asset configuration
├── attendance_fol/                # Attendance management pages
├── coming.php                     # Coming soon placeholder
├── customize_table.php            # Table customization utility
├── dashboard.php                  # Main dashboard
├── docs/                          # Documentation
├── download_employees.php         # Employee data export
├── download_payroll.php           # Payroll data export
├── files/                         # Shared template files
├── finance/                       # Financial management pages
├── hrm/                           # Human resource management pages
├── index.php                      # Application entry point
├── login.php                      # Login page
├── logout.php                     # Logout handler
├── management/                    # HR management pages
├── organization/                  # Organization setup pages
├── payments_fol/                  # Payment processing pages
├── payroll_fol/                   # Payroll management pages
├── pdf.php                        # PDF generation utility
├── performance/                   # Performance management pages
├── prints/                        # Print/report templates
├── README.md                      # Project documentation
├── reports_fol/                   # Report generation pages
├── settings_fol/                  # System settings pages
├── setup/                         # System setup pages
├── system/                        # User and role management
└── training/                      # Training management pages
```

## Core Application Directory (`app/`)

The `app/` directory contains the core application logic, models, and controllers:

```
app/
├── AttendanceClass.php            # Attendance model
├── atten_controller.php           # Attendance controller
├── auth.php                       # Authentication system
├── autoload.php                   # Custom autoloader and routing
├── BranchClass.php                # Branch/Department model
├── CompanyClass.php               # Company model
├── config.php                     # Application configuration
├── CountryClass.php               # Country model
├── dashboard_data.php             # Dashboard data provider
├── db.php                         # Database connection
├── EducationClass.php             # Education model
├── EmployeeClass.php              # Employee model
├── financeClass.php               # Finance model
├── finance_controller.php         # Finance controller
├── helpers.php                    # Helper functions
├── hrm_controller.php             # HRM controller
├── init.php                       # Application initialization
├── LocationsClass.php             # Locations model
├── management_classes.php         # Management models
├── management_controller.php      # Management controller
├── MiscClass.php                  # Miscellaneous entities model
├── Model.php                      # Base model class
├── oldsidebar.php                 # Legacy sidebar (unused)
├── org_controller.php             # Organization controller
├── PayrollClass.php               # Payroll model
├── payroll_controller.php         # Payroll controller
├── performanceClass.php           # Performance model
├── performance_controller.php     # Performance controller
├── report_controller.php          # Report controller
├── SalaryClass.php                # Salary model
├── SettingsClass.php              # Settings model
├── settings_controller.php        # Settings controller
├── sql/                           # SQL scripts and database schema
├── StatesClass.php                # States model
├── sys_permissions.php            # System permissions
├── trainingClass.php              # Training model
├── training_controller.php        # Training controller
├── UserClass.php                  # User model
├── users_controller.php           # User controller
└── utilities.php                  # Utility functions
```

### Key Application Files

#### 1. Core System Files

- **`init.php`**: Application bootstrap file that loads all dependencies
- **`db.php`**: Database connection configuration and initialization
- **`config.php`**: Application-wide configuration settings
- **`autoload.php`**: Custom routing system and file loading logic
- **`auth.php`**: Authentication and authorization system

#### 2. Base Classes

- **`Model.php`**: Base model class providing CRUD operations
- **`helpers.php`**: Common helper functions for data processing
- **`utilities.php`**: Utility functions for formatting and calculations

#### 3. Domain Models

Each major entity has its own model class:
- **Employee Management**: `EmployeeClass.php`, `EducationClass.php`
- **Organization**: `CompanyClass.php`, `BranchClass.php`, `LocationsClass.php`
- **Payroll**: `PayrollClass.php`, `SalaryClass.php`
- **Attendance**: `AttendanceClass.php`
- **User Management**: `UserClass.php`

#### 4. Controllers

Controllers handle HTTP requests and business logic:
- **`hrm_controller.php`**: Employee management operations
- **`payroll_controller.php`**: Payroll processing
- **`atten_controller.php`**: Attendance tracking
- **`users_controller.php`**: User and role management

## Assets Directory (`assets/`)

Static assets organized by type:

```
assets/
├── css/                           # Stylesheets
│   ├── bootstrap.min.css          # Bootstrap framework
│   ├── custom.css                 # Custom application styles
│   └── modules/                   # Module-specific styles
├── docs/                          # Document uploads
│   └── employee/                  # Employee document storage
├── fonts/                         # Web fonts
├── images/                        # Static images
│   ├── avatars/                   # User profile pictures
│   ├── logos/                     # Company logos
│   └── icons/                     # Application icons
├── js/                            # JavaScript files
│   ├── jquery.min.js              # jQuery library
│   ├── bootstrap.bundle.min.js    # Bootstrap JavaScript
│   ├── dashboard2.js              # Dashboard functionality
│   └── modules/                   # Module-specific JavaScript
│       ├── hrm.js                 # Employee management
│       ├── payroll.js             # Payroll functionality
│       ├── attendance.js          # Attendance tracking
│       └── [other modules]
├── plugins/                       # Third-party libraries
│   ├── apexchart/                 # Chart library
│   ├── datatables/                # Data table plugin
│   └── [other plugins]
├── sass/                          # SASS source files
└── tcpdf/                         # PDF generation library
```

## Feature Module Directories

Each major feature has its own directory containing related pages:

### 1. Human Resource Management (`hrm/`)

```
hrm/
├── awards.php                     # Employee awards management
├── designations.php               # Job designations
├── docs_add.php                   # Add employee documents
├── docs_edit.php                  # Edit employee documents
├── documents.php                  # Document management
├── employees.php                  # Employee listing
├── employees_upload.php           # Bulk employee upload
├── employee_add.php               # Add new employee
├── employee_edit.php              # Edit employee
├── employee_show.php              # Employee details
├── emp_doc.php                    # Employee document viewer
├── folder_show.php                # Document folder viewer
└── hrm_menu.php                   # HRM navigation menu
```

### 2. Payroll Management (`payroll_fol/`)

```
payroll_fol/
├── payroll.php                    # Payroll listing
├── payroll_add.php                # Create new payroll
├── payroll_show.php               # Payroll details
├── payslip_show.php               # Individual payslip
├── pay_payroll.php                # Process payroll payments
├── timesheet_edit.php             # Edit timesheet
├── transactions.php               # Payroll transactions
└── transaction_add.php            # Add transaction
```

### 3. Attendance Management (`attendance_fol/`)

```
attendance_fol/
├── add_attendance.php             # Add attendance record
├── add_timesheet_bulk.php         # Bulk timesheet entry
├── attendance.php                 # Attendance listing
├── atten_add.php                  # Add attendance
├── atten_edit.php                 # Edit attendance
├── emp_leave_add.php              # Add employee leave
├── emp_leave_edit.php             # Edit employee leave
├── leave_mgt.php                  # Leave management
├── leave_type_add.php             # Add leave type
├── leave_type_edit.php            # Edit leave type
├── manage_allocation.php          # Timesheet allocation
├── res_allocation.php             # Resource allocation
├── timesheet.php                  # Timesheet management
├── timesheet_add.php              # Add timesheet
└── timesheet_edit.php             # Edit timesheet
```

### 4. Organization Setup (`organization/` and `setup/`)

```
organization/
├── banks.php                      # Bank account management
├── bank_add.php                   # Add bank account
├── bank_edit.php                  # Edit bank account
├── branches.php                   # Department management
├── branch_add.php                 # Add department
├── branch_edit.php                # Edit department
├── locations.php                  # Location management
├── location_add.php               # Add location
├── location_edit.php              # Edit location
├── misc.php                       # Miscellaneous settings
├── misc_add.php                   # Add misc item
├── misc_edit.php                  # Edit misc item
├── org.php                        # Organization setup
├── org_add.php                    # Add organization
├── org_chart.php                  # Organization chart
├── org_edit.php                   # Edit organization
├── state_add.php                  # Add state
├── state_edit.php                 # Edit state
└── state_show.php                 # State details
```

### 5. Financial Management (`finance/`)

```
finance/
├── accounts.php                   # Account management
├── add_bank_account.php           # Add bank account
├── add_expense.php                # Add expense
├── add_income.php                 # Add income
├── edit_bank_account.php          # Edit bank account
├── edit_expense.php               # Edit expense
├── edit_income.php                # Edit income
├── expenses.php                   # Expense management
├── income.php                     # Income management
├── payroll_payment.php            # Payroll payments
└── pay_payroll.php                # Process payroll
```

### 6. Performance Management (`performance/`)

```
performance/
├── appraisals.php                 # Performance appraisals
├── appraisals_add.php             # Add appraisal
├── appraisals_edit.php            # Edit appraisal
├── goal_tracking.php              # Goal tracking
├── goal_tracking_add.php          # Add goal
├── goal_tracking_edit.php         # Edit goal
├── indicators.php                 # Performance indicators
├── indicators_add.php             # Add indicator
├── indicators_edit.php            # Edit indicator
└── tracking.php                   # Performance tracking
```

### 7. Training Management (`training/`)

```
training/
├── add_trainer.php                # Add trainer
├── add_training.php               # Add training program
├── edit_trainer.php               # Edit trainer
├── edit_training.php              # Edit training program
├── trainers.php                   # Trainer management
└── training.php                   # Training program management
```

### 8. System Administration (`system/`)

```
system/
├── roles.php                      # Role management
├── users.php                      # User management
├── user_add.php                   # Add user
├── user_edit.php                  # Edit user
└── user_show.php                  # User details
```

## Shared Components (`files/`)

Common template files used across the application:

```
files/
├── app_footer.php                 # Application footer
├── app_header.php                 # Application header
├── app_sidebar.php                # Navigation sidebar
├── app_topbar.php                 # Top navigation bar
├── bread.txt                      # Breadcrumb data
├── login_footer.php               # Login page footer
├── login_header.php               # Login page header
└── to_json.php                    # JSON conversion utility
```

## Report Templates (`prints/`)

Print and PDF templates for various reports:

```
prints/
├── absence.php                    # Absence report
├── allEmployees.php               # All employees report
├── componsation.php               # Compensation report
├── deductions.php                 # Deductions report
├── payroll_report.php             # Payroll report
└── payslip.php                    # Payslip template
```

## Documentation (`docs/`)

Comprehensive documentation organized by feature:

```
docs/
├── 01-System-Overview/            # System architecture and overview
├── 02-Employee-Management/        # Employee management documentation
├── 03-Payroll-System/             # Payroll system documentation
├── 04-Attendance-Tracking/        # Attendance system documentation
├── 05-Organizational-Structure/   # Organization management
├── 06-Financial-Management/       # Financial system documentation
├── 07-Performance-Training/       # Performance and training docs
├── 08-Security-Authentication/    # Security documentation
├── 09-API-Integration/            # API and integration docs
├── 10-Database-Reference/         # Database schema and reference
└── codebase-analysis.md           # Codebase analysis
```

## File Naming Conventions

### 1. PHP Files

- **Models**: `[Entity]Class.php` (e.g., `EmployeeClass.php`)
- **Controllers**: `[module]_controller.php` (e.g., `hrm_controller.php`)
- **Pages**: Descriptive names (e.g., `employee_add.php`, `payroll_show.php`)

### 2. JavaScript Files

- **Module Scripts**: `[module].js` (e.g., `hrm.js`, `payroll.js`)
- **Libraries**: Original library names (e.g., `jquery.min.js`)

### 3. CSS Files

- **Framework**: `bootstrap.min.css`
- **Custom**: `custom.css`
- **Module Styles**: Organized in `modules/` subdirectory

## Directory Access Patterns

### 1. Public Access

- Root directory files (`.php` files)
- `assets/` directory for static resources
- `prints/` directory for reports

### 2. Protected Access

- `app/` directory (core application logic)
- `files/` directory (shared components)
- Database configuration files

### 3. Upload Directories

- `assets/docs/employee/` - Employee document uploads
- `assets/images/avatars/` - User profile pictures
- File upload directories with proper permissions

## Configuration Files

### 1. Web Server Configuration

- **`.htaccess`**: Apache rewrite rules and security settings
- **`asset_config.php`**: Asset path configuration

### 2. Application Configuration

- **`app/config.php`**: Application settings and constants
- **`app/db.php`**: Database connection parameters

## Security Considerations

### 1. File Permissions

- Application files: Read-only for web server
- Upload directories: Write permissions with restrictions
- Configuration files: Protected from direct access

### 2. Directory Protection

- `app/` directory protected from direct web access
- Database files and sensitive configurations secured
- Upload directories with file type restrictions

This file structure provides a clear separation of concerns while maintaining ease of navigation and development. The modular organization allows for independent development of features while sharing common components and utilities.