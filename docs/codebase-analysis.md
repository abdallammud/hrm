# HRM Application Codebase Analysis

This document provides a comprehensive analysis of the PHP classes, controllers, and database-related files in the HRM application.

## PHP Model Classes

### Core Infrastructure
- **Model.php** - Base model class with CRUD operations and database connectivity
- **db.php** - MySQL database connection configuration
- **auth.php** - Authentication and session management
- **helpers.php** - Utility functions and batch processing helpers

### Employee Management Models
- **EmployeeClass.php** - Employee data management and operations
- **SalaryClass.php** - Employee salary management (table: employee_salaries)
- **EducationClass.php** - Employee education records (table: employee_education)

### Organizational Structure Models
- **CompanyClass.php** - Company information management (table: company)
- **BranchClass.php** - Branch/Department management (table: branches)
- **LocationsClass.php** - Duty locations management (table: locations)
- **StatesClass.php** - Geographic states (table: states)
- **CountryClass.php** - Country data (table: countries)

### Attendance & Time Tracking Models
- **AttendanceClass.php** - Contains multiple classes:
  - `LeaveTypes` (table: leave_types)
  - `EmployeeLeave` (table: employee_leave)
  - `Attendance` (table: attendance)
  - `AttenDetails` (table: atten_details)
  - `Timesheet` (table: timesheet)

### Payroll Management Models
- **PayrollClass.php** - Contains multiple classes:
  - `EmployeeTransactions` (table: employee_transactions)
  - `Payroll` (table: payroll)
  - `PayrollDetailsClass` (table: payroll_details)

### Performance & Training Models
- **performanceClass.php** - Performance management (table: performance)
- **trainingClass.php** - Contains multiple classes:
  - `Trainers` (table: trainers)
  - `TrainingList` (table: training_list)

### Financial Management Models
- **financeClass.php** - Contains multiple classes:
  - `accountsClass` (table: bank_accounts)
  - `TransactionsClass` (table: fn_transactions)

### HR Management Models
- **management_classes.php** - Contains multiple classes:
  - `PromotionsClass` (table: promotions)
  - `TransfersClass` (table: transfers)
  - `ResignationsClass` (table: resignations)
  - `TerminationsClass` (table: terminations)
  - `WarningsClass` (table: warnings)

### System Management Models
- **UserClass.php** - Contains multiple classes:
  - `Users` (table: users)
  - `Permissios` (table: permissions)
  - `UserPermissions` (table: user_permissions)
- **sys_permissions.php** - Contains multiple classes:
  - `Sys_Permissions` (table: sys_permissions)
  - `Sys_roles` (table: sys_roles)
  - `Sys_role_permissions` (table: sys_role_permissions)
- **SettingsClass.php** - System settings (table: sys_settings)

### Miscellaneous Models
- **MiscClass.php** - Contains multiple classes:
  - `Designations` (table: designations)
  - `Projects` (table: projects)
  - `ContractTypes` (table: contract_types)
  - `BudgetCodes` (table: budget_codes)
  - `BanksClass` (table: banks)
  - `TransSubTypesClass` (table: trans_subtypes)
  - `GoalTypesClass` (table: goal_types)
  - `AwardTypesClass` (table: award_types)
  - `FinancialAccountsClass` (table: financial_accounts)
  - `TrainingOptionsClass` (table: training_options)
  - `TrainingTypesClass` (table: training_types)

## Controller Files

### Primary Feature Controllers
- **hrm_controller.php** - Employee management operations and workflows
- **payroll_controller.php** - Payroll processing and calculations
- **atten_controller.php** - Attendance tracking and timesheet management
- **finance_controller.php** - Financial operations and transaction management
- **performance_controller.php** - Performance management and appraisals
- **training_controller.php** - Training program and trainer management
- **management_controller.php** - HR management actions (promotions, transfers, etc.)
- **org_controller.php** - Organizational structure management
- **users_controller.php** - User and role management
- **settings_controller.php** - System configuration and settings
- **report_controller.php** - Report generation and data export

## Database-Related Files

### Schema Files
- **app/sql/changes.sql** - Database schema modifications
- **app/sql/folders.sql** - Empty file (possibly for future use)

### Configuration Files
- **app/db.php** - Database connection parameters
- **app/config.php** - Application configuration including terminology settings

## Application Structure Analysis

### Routing System
The application uses a sophisticated menu-driven routing system defined in `autoload.php`:
- Hierarchical menu structure with main menus and submenus
- Permission-based access control for each route
- Dynamic file loading based on menu configuration
- Support for actions (add, edit, show, delete)

### Menu Structure
Main application modules:
1. **Dashboard** - Main application dashboard
2. **HRM** - Employee management (employees, documents, awards)
3. **Payroll** - Payroll processing (payroll, transactions)
4. **Attendance** - Time tracking (timesheet, attendance, allocation, leave)
5. **Performance** - Performance management (appraisals, indicators, tracking)
6. **Finance** - Financial management (accounts, payments, expenses, income)
7. **Reports** - Report generation and viewing
8. **Training** - Training management (training, trainers)
9. **Management** - HR actions (promotions, transfers, warnings, resignations, terminations)
10. **System Setup** - Organizational setup (organization, branches, locations, misc)
11. **Settings** - System configuration
12. **Users** - User and role management

### Security Implementation
- Session-based authentication
- Role-based access control with granular permissions
- Prepared statements for SQL injection prevention
- Input sanitization and validation
- Permission checking at menu and action levels

### Database Design Patterns
- Consistent table naming conventions
- Primary key patterns (mostly 'id', some with specific naming like 'user_id', 'salary_id')
- Foreign key relationships between related entities
- Audit trail capabilities in some tables
- Flexible schema design supporting various HR workflows

## Key Findings

1. **Modular Architecture**: The application is well-organized with clear separation between models, controllers, and views.

2. **Comprehensive HR Coverage**: The system covers all major HR functions including employee management, payroll, attendance, performance, training, and financial management.

3. **Security-Conscious Design**: Implements proper security measures including prepared statements, permission-based access control, and input validation.

4. **Flexible Configuration**: Supports configurable terminology and organizational structures.

5. **Extensible Design**: The base Model class and menu system make it easy to add new features and modules.

6. **Database Integration**: Strong integration with MySQL using prepared statements and proper error handling.

This analysis provides the foundation for creating detailed documentation of each system component and workflow.