# API Endpoints Documentation

## Overview

This document provides comprehensive documentation for all API endpoints available in the HRM application. The application follows a RESTful-like API structure with controller-based routing using GET parameters for actions and endpoints.

## Base URL Structure

All API endpoints follow this pattern:
```
/app/{controller_name}.php?action={action}&endpoint={endpoint}
```

## Authentication

All API endpoints require user authentication through PHP sessions. The `check_auth()` function validates user permissions for specific operations.

## Response Format

All endpoints return JSON responses with the following structure:

### Success Response
```json
{
    "error": false,
    "msg": "Operation completed successfully",
    "id": 123,
    "data": {...}
}
```

### Error Response
```json
{
    "error": true,
    "msg": "Error message",
    "sql_error": "Database error details (if applicable)"
}
```

### DataTable Response (for load operations)
```json
{
    "status": 201,
    "error": false,
    "data": [...],
    "draw": 1,
    "iTotalRecords": 100,
    "iTotalDisplayRecords": 100,
    "msg": "Records found"
}
```

## Controller Endpoints

### 1. Attendance Controller (`atten_controller.php`)

#### Save Operations

##### Create Leave Type
- **URL**: `/app/atten_controller.php?action=save&endpoint=leave_type`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Leave type name
  - `paid_type` (string, required): "Paid" or "Unpaid"
- **Permissions**: `create_leave_types`
- **Response**: Success/error message with created ID

##### Create Employee Leave
- **URL**: `/app/atten_controller.php?action=save&endpoint=employee_leave`
- **Method**: POST
- **Parameters**:
  - `emp_id` (int, required): Employee ID
  - `leave_id` (int, required): Leave type ID
  - `date_from` (date, required): Start date (YYYY-MM-DD)
  - `date_to` (date, required): End date (YYYY-MM-DD)
- **Permissions**: `create_leave`
- **Response**: Success/error message with created ID

##### Record Attendance
- **URL**: `/app/atten_controller.php?action=save&endpoint=attendance`
- **Method**: POST
- **Parameters**:
  - `ref` (string, required): Reference type ("Employee", "Department", "Location")
  - `ref_id` (int, required): Reference ID
  - `ref_name` (string, required): Reference name
  - `atten_date` (date, required): Attendance date
  - `atten_status` (string, required): Attendance status ("P", "A", "L", etc.)
- **Permissions**: `create_attendance`
- **Response**: Success/error message with attendance ID

##### Upload Attendance (Bulk)
- **URL**: `/app/atten_controller.php?action=save&endpoint=upload_attendance`
- **Method**: POST (multipart/form-data)
- **Parameters**:
  - `file` (file, required): CSV file with attendance data
- **CSV Format**: staff_no, employee_id, full_name, atten_date, attend_status, ref, ref_id, ref_name
- **Permissions**: `create_attendance`
- **Response**: Success/error message with processing details

##### Record Timesheet
- **URL**: `/app/atten_controller.php?action=save&endpoint=timesheet`
- **Method**: POST
- **Parameters**:
  - `ts_date` (date, required): Timesheet date
  - `emp_id` (int, required): Employee ID
  - `time_in` (time, required): Clock in time (HH:MM:SS)
  - `time_out` (time, required): Clock out time (HH:MM:SS)
- **Permissions**: `create_timesheet`
- **Response**: Success/error message with timesheet ID

##### Upload Timesheet (Bulk)
- **URL**: `/app/atten_controller.php?action=save&endpoint=upload_timesheet`
- **Method**: POST (multipart/form-data)
- **Parameters**:
  - `file` (file, required): CSV file with timesheet data
- **CSV Format**: staff_no, employee_id, full_name, ts_date, attend_status, time_in, time_out, ref, ref_id, ref_name
- **Permissions**: `create_timesheet`
- **Response**: Success/error message with processing details

##### Bulk Attendance Entry
- **URL**: `/app/atten_controller.php?action=save&endpoint=bulkAttendance`
- **Method**: POST
- **Parameters**:
  - `date` (date, required): Attendance date
  - `employees[]` (array, required): Array of employee IDs
  - `statuses[]` (array, required): Array of attendance statuses
- **Permissions**: `create_attendance`
- **Response**: Success/error message

##### Bulk Timesheet Entry
- **URL**: `/app/atten_controller.php?action=save&endpoint=bulkTimesheet`
- **Method**: POST
- **Parameters**:
  - `date` (date, required): Timesheet date
  - `employees[]` (array, required): Array of employee IDs
  - `time_in[]` (array, required): Array of clock in times
  - `time_out[]` (array, required): Array of clock out times
- **Permissions**: `create_timesheet`
- **Response**: Success/error message

##### Resource Allocation
- **URL**: `/app/atten_controller.php?action=save&endpoint=allocation`
- **Method**: POST
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `supervisor_id` (int, required): Supervisor ID
  - `month` (string, required): Allocation month
  - `prevMonth` (string, required): Previous month for cleanup
  - `allocation` (array, required): Allocation data
- **Permissions**: `create_allocation`
- **Response**: Success/error message

### 2. Finance Controller (`finance_controller.php`)

#### Save Operations

##### Create Bank Account
- **URL**: `/app/finance_controller.php?action=save&endpoint=bank_account`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Bank name
  - `account` (string, optional): Account number
  - `balance` (decimal, optional): Initial balance
- **Permissions**: `create_bank_accounts`
- **Response**: Success/error message with account ID

##### Record Expense
- **URL**: `/app/finance_controller.php?action=save&endpoint=expense`
- **Method**: POST
- **Parameters**:
  - `bank_id` (int, required): Bank account ID
  - `fn_account_id` (int, required): Financial account ID
  - `amount` (decimal, required): Expense amount
  - `payee_payer` (string, required): Payee name
  - `description` (string, optional): Expense description
  - `refNumber` (string, optional): Reference number
  - `paid_date` (date, required): Payment date
- **Permissions**: `create_expenses`
- **Response**: Success/error message with transaction ID

##### Record Income
- **URL**: `/app/finance_controller.php?action=save&endpoint=income`
- **Method**: POST
- **Parameters**:
  - `bank_id` (int, required): Bank account ID
  - `fn_account_id` (int, required): Financial account ID
  - `amount` (decimal, required): Income amount
  - `payee_payer` (string, required): Payer name
  - `description` (string, optional): Income description
  - `refNumber` (string, optional): Reference number
  - `paid_date` (date, required): Receipt date
- **Permissions**: `create_income`
- **Response**: Success/error message with transaction ID

#### Update Operations

##### Update Bank Account
- **URL**: `/app/finance_controller.php?action=update&endpoint=bank_account`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Bank account ID
  - `name` (string, required): Bank name
  - `account` (string, optional): Account number
  - `balance` (decimal, optional): Account balance
  - `slcStatus` (string, optional): Account status
- **Permissions**: `edit_bank_accounts`
- **Response**: Success/error message

##### Update Expense
- **URL**: `/app/finance_controller.php?action=update&endpoint=expense`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Expense ID
  - `slcBank` (int, required): Bank account ID
  - `slcFinancialAccount` (int, required): Financial account ID
  - `amount` (decimal, required): Expense amount
  - `paidTo` (string, required): Payee name
  - `description` (string, optional): Expense description
  - `refNumber` (string, optional): Reference number
  - `paidDate` (date, required): Payment date
  - `slcStatus` (string, optional): Status
- **Permissions**: `edit_expenses`
- **Response**: Success/error message

##### Update Income
- **URL**: `/app/finance_controller.php?action=update&endpoint=income`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Income ID
  - `slcBankIncome` (int, required): Bank account ID
  - `slcFinancialAccountIncome` (int, required): Financial account ID
  - `amountIncome` (decimal, required): Income amount
  - `receivedFrom` (string, required): Payer name
  - `descriptionIncome` (string, optional): Income description
  - `refNumberIncome` (string, optional): Reference number
  - `receivedDate` (date, required): Receipt date
  - `slcStatusIncome` (string, optional): Status
- **Permissions**: `edit_income`
- **Response**: Success/error message

##### Pay Payroll
- **URL**: `/app/finance_controller.php?action=update&endpoint=payPayroll`
- **Method**: POST
- **Parameters**:
  - `payroll_id` (int, required): Payroll ID
  - `payroll_detId` (int, optional): Specific payroll detail ID
  - `payroll_detIds` (string, optional): Comma-separated payroll detail IDs
  - `slcBank` (int, required): Bank account ID for payment
  - `payDate` (date, required): Payment date
- **Permissions**: `manage_payroll`
- **Response**: Success/error message with payment details

##### Reject Payroll
- **URL**: `/app/finance_controller.php?action=update&endpoint=rejectPayroll`
- **Method**: POST
- **Parameters**:
  - `payroll_detId` (int, optional): Specific payroll detail ID
  - `payroll_detIds` (string, optional): Comma-separated payroll detail IDs
- **Permissions**: `manage_payroll`
- **Response**: Success/error message

#### Load Operations

##### Load Bank Accounts
- **URL**: `/app/finance_controller.php?action=load&endpoint=bank_accounts`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with bank account records

##### Load Expenses
- **URL**: `/app/finance_controller.php?action=load&endpoint=expenses`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with expense records

##### Load Income
- **URL**: `/app/finance_controller.php?action=load&endpoint=income`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with income records

### 3. HRM Controller (`hrm_controller.php`)

#### Save Operations

##### Create Employee
- **URL**: `/app/hrm_controller.php?action=save&endpoint=employee`
- **Method**: POST
- **Parameters**:
  - `full_name` (string, required): Employee full name
  - `phone_number` (string, required): Phone number
  - `email` (string, required): Email address
  - `gender` (string, required): Gender
  - `national_id` (string, optional): National ID
  - `date_of_birth` (date, required): Birth date
  - `city` (string, optional): City
  - `address` (string, optional): Address
  - `branch_id` (int, required): Department/Branch ID
  - `location_id` (int, required): Location ID
  - `position` (string, required): Job position
  - `hire_date` (date, required): Hire date
  - `contract_start` (date, required): Contract start date
  - `contract_end` (date, required): Contract end date
  - `salary` (decimal, required): Base salary
  - `degree[]` (array, optional): Education degrees
  - `institution[]` (array, optional): Educational institutions
  - `startYear[]` (array, optional): Education start years
  - `endYear[]` (array, optional): Education end years
  - `project_id[]` (array, optional): Project assignments
  - `budget_code[]` (array, optional): Budget code assignments
- **Permissions**: `create_employees`
- **Response**: Success/error message with employee ID

##### Upload Employees (Bulk)
- **URL**: `/app/hrm_controller.php?action=save&endpoint=upload_employees`
- **Method**: POST (multipart/form-data)
- **Parameters**:
  - `file` (file, required): CSV file with employee data
- **CSV Format**: staff_no, full_name, phone_number, email, gender, national_id, date_of_birth, city, address, payment_bank, payment_account, branch, designation, state, location, hire_date, contract_start, contract_end, contract_type, salary, tax_exempt, budget_codes, projects, moh_contract, work_days, work_hours, grade, seniority
- **Permissions**: `create_employees`
- **Response**: Success/error message with processing statistics

##### Create Folder
- **URL**: `/app/hrm_controller.php?action=save&endpoint=folder`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Folder name
- **Permissions**: `manage_employee_docs`
- **Response**: Success/error message

##### Create Document Type
- **URL**: `/app/hrm_controller.php?action=save&endpoint=doc_types`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Document type name
- **Permissions**: `manage_employee_docs`
- **Response**: Success/error message

##### Upload Employee Document
- **URL**: `/app/hrm_controller.php?action=save&endpoint=emp_docs`
- **Method**: POST (multipart/form-data)
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `docName` (string, required): Document name
  - `docFolder` (int, required): Folder ID
  - `docFolderName` (string, required): Folder name
  - `docType` (int, required): Document type ID
  - `docTypeName` (string, required): Document type name
  - `expirationDate` (date, optional): Document expiration date
  - `docFile` (file, required): Document file
- **Permissions**: `create_employee_docs`
- **Response**: Success/error message with document ID

##### Create Employee Award
- **URL**: `/app/hrm_controller.php?action=save&endpoint=award`
- **Method**: POST
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `award_type` (int, required): Award type ID
  - `gift` (string, required): Award gift/description
  - `award_date` (date, required): Award date
- **Permissions**: `manage_awards`
- **Response**: Success/error message with award ID

#### Update Operations

##### Update Employee
- **URL**: `/app/hrm_controller.php?action=update&endpoint=employee`
- **Method**: POST
- **Parameters**: Same as create employee, plus:
  - `employee_id` (int, required): Employee ID to update
- **Permissions**: `edit_employees`
- **Response**: Success/error message

##### Update Employee Avatar
- **URL**: `/app/hrm_controller.php?action=update&endpoint=employee_avatar`
- **Method**: POST (multipart/form-data)
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `avatar` (file, required): Avatar image file (max 5MB, jpg/jpeg/png/gif/webp)
- **Permissions**: `edit_employees`
- **Response**: Success/error message

##### Update Folder
- **URL**: `/app/hrm_controller.php?action=update&endpoint=folder`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Folder ID
  - `name` (string, required): Folder name
- **Permissions**: `manage_employee_docs`
- **Response**: Success/error message

### 4. Management Controller (`management_controller.php`)

#### Save Operations

##### Create Promotion
- **URL**: `/app/management_controller.php?action=save&endpoint=promotion`
- **Method**: POST
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `old_designation` (int, required): Current designation ID
  - `new_designation` (string, required): New designation
  - `promotion_date` (date, required): Promotion date
  - `new_salary` (decimal, optional): New salary amount
  - `reason` (string, optional): Promotion reason
  - `status` (string, optional): Status ("Pending", "Approved", "Rejected")
- **Permissions**: `create_promotions`
- **Response**: Success/error message with promotion ID

##### Create Transfer
- **URL**: `/app/management_controller.php?action=save&endpoint=transfer`
- **Method**: POST
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `new_department_id` (int, required): New department ID
  - `transfer_date` (date, required): Transfer date
  - `reason` (string, optional): Transfer reason
  - `status` (string, optional): Status ("Pending", "Approved", "Rejected")
- **Permissions**: `create_transfers`
- **Response**: Success/error message with transfer ID

##### Create Resignation
- **URL**: `/app/management_controller.php?action=save&endpoint=resignation`
- **Method**: POST
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `resignation_date` (date, required): Resignation date
  - `last_working_day` (date, required): Last working day
  - `reason` (string, optional): Resignation reason
  - `status` (string, optional): Status ("Pending", "Approved", "Rejected")
- **Permissions**: `create_resignations`
- **Response**: Success/error message with resignation ID

##### Create Termination
- **URL**: `/app/management_controller.php?action=save&endpoint=termination`
- **Method**: POST
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `termination_date` (date, required): Termination date
  - `reason` (string, required): Termination reason
  - `termination_type` (string, optional): Type ("Voluntary", "Involuntary")
  - `status` (string, optional): Status ("Pending", "Completed")
- **Permissions**: `create_terminations`
- **Response**: Success/error message with termination ID

##### Create Warning
- **URL**: `/app/management_controller.php?action=save&endpoint=warning`
- **Method**: POST
- **Parameters**:
  - `employee_id` (int, required): Employee ID
  - `issued_by` (int, required): Issuer user ID
  - `warning_date` (date, required): Warning date
  - `reason` (string, required): Warning reason
  - `severity` (string, optional): Severity level ("Low", "Medium", "High")
- **Permissions**: `create_warnings`
- **Response**: Success/error message with warning ID

#### Update Operations

##### Update Promotion
- **URL**: `/app/management_controller.php?action=update&endpoint=promotion`
- **Method**: POST
- **Parameters**: Same as create promotion, plus:
  - `promotion_id` (int, required): Promotion ID to update
- **Permissions**: `edit_promotions`
- **Response**: Success/error message

##### Update Transfer
- **URL**: `/app/management_controller.php?action=update&endpoint=transfer`
- **Method**: POST
- **Parameters**: Same as create transfer, plus:
  - `transfer_id` (int, required): Transfer ID to update
- **Permissions**: `edit_transfers`
- **Response**: Success/error message

##### Update Resignation
- **URL**: `/app/management_controller.php?action=update&endpoint=resignation`
- **Method**: POST
- **Parameters**: Same as create resignation, plus:
  - `resignation_id` (int, required): Resignation ID to update
- **Permissions**: `edit_resignations`
- **Response**: Success/error message

##### Update Termination
- **URL**: `/app/management_controller.php?action=update&endpoint=termination`
- **Method**: POST
- **Parameters**: Same as create termination, plus:
  - `termination_id` (int, required): Termination ID to update
- **Permissions**: `edit_terminations`
- **Response**: Success/error message

### 5. Organization Controller (`org_controller.php`)

#### Save Operations

##### Create Company
- **URL**: `/app/org_controller.php?action=save&endpoint=company`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Company name
  - `address` (string, required): Company address
  - `phones` (string, required): Contact phone numbers
  - `emails` (string, required): Contact email addresses
- **Permissions**: `create_organization`
- **Response**: Success/error message with company ID

##### Create Branch/Department
- **URL**: `/app/org_controller.php?action=save&endpoint=branch`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Branch/Department name
  - `address` (string, optional): Branch address
  - `contact_phone` (string, optional): Contact phone
  - `contact_email` (string, optional): Contact email
- **Permissions**: `create_departments`
- **Response**: Success/error message with branch ID

##### Create State
- **URL**: `/app/org_controller.php?action=save&endpoint=state`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): State name
  - `country` (int, optional): Country ID
  - `countryName` (string, optional): Country name
  - `tax` (array, optional): Tax grid data
  - `stampDuty` (decimal, optional): Stamp duty amount
- **Permissions**: `create_states`
- **Response**: Success/error message with state ID

##### Create Location
- **URL**: `/app/org_controller.php?action=save&endpoint=location`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Location name
  - `state` (int, optional): State ID
  - `stateName` (string, optional): State name
  - `city` (string, optional): City name
- **Permissions**: `create_duty_locations`
- **Response**: Success/error message with location ID

##### Create Designation
- **URL**: `/app/org_controller.php?action=save&endpoint=designation`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Designation name
- **Permissions**: `create_designations`
- **Response**: Success/error message with designation ID

##### Create Project
- **URL**: `/app/org_controller.php?action=save&endpoint=project`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Project name
  - `comments` (string, optional): Project comments
- **Permissions**: `create_projects`
- **Response**: Success/error message with project ID

##### Create Contract Type
- **URL**: `/app/org_controller.php?action=save&endpoint=contract_type`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Contract type name
- **Permissions**: `create_contract_types`
- **Response**: Success/error message with contract type ID

##### Create Budget Code
- **URL**: `/app/org_controller.php?action=save&endpoint=budget_code`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Budget code name
  - `comments` (string, optional): Budget code comments
- **Permissions**: `create_budget_codes`
- **Response**: Success/error message with budget code ID

##### Create Bank
- **URL**: `/app/org_controller.php?action=save&endpoint=bank`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Bank name
- **Permissions**: `create_bank_accounts`
- **Response**: Success/error message with bank ID

##### Create Transaction Subtype
- **URL**: `/app/org_controller.php?action=save&endpoint=subtype`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Subtype name
  - `type` (string, required): Transaction type
- **Permissions**: `create_transaction_subtypes`
- **Response**: Success/error message with subtype ID

##### Create Goal Type
- **URL**: `/app/org_controller.php?action=save&endpoint=goal_type`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Goal type name
- **Permissions**: `create_goal_types`
- **Response**: Success/error message with goal type ID

##### Create Award Type
- **URL**: `/app/org_controller.php?action=save&endpoint=award_type`
- **Method**: POST
- **Parameters**:
  - `awardTypeName` (string, required): Award type name
- **Permissions**: `create_award_types`
- **Response**: Success/error message with award type ID

##### Create Financial Account
- **URL**: `/app/org_controller.php?action=save&endpoint=financial_account`
- **Method**: POST
- **Parameters**:
  - `financialAccountName` (string, required): Account name
  - `accountType` (string, required): Account type
- **Permissions**: `create_financial_accounts`
- **Response**: Success/error message with account ID

##### Create Training Option
- **URL**: `/app/org_controller.php?action=save&endpoint=training_options`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Training option name
- **Permissions**: `create_training_options`
- **Response**: Success/error message with option ID

##### Create Training Type
- **URL**: `/app/org_controller.php?action=save&endpoint=training_types`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Training type name
- **Permissions**: `create_training_types`
- **Response**: Success/error message with type ID

#### Update Operations

All organization entities support update operations with similar parameters to their create operations, plus an `id` parameter to identify the record to update. The URL pattern is:
```
/app/org_controller.php?action=update&endpoint={entity_name}
```

### 6. Payroll Controller (`payroll_controller.php`)

#### Save Operations

##### Create Employee Transaction
- **URL**: `/app/payroll_controller.php?action=save&endpoint=transaction`
- **Method**: POST
- **Parameters**:
  - `emp_id` (int, required): Employee ID
  - `transaction_type` (string, required): Transaction type ("Allowance", "Deduction", etc.)
  - `transaction_subtype` (string, required): Transaction subtype
  - `amount` (decimal, required): Transaction amount
  - `date` (date, required): Transaction date
  - `description` (string, optional): Transaction description
  - `status` (string, required): Status ("Pending", "Approved")
- **Permissions**: `create_payroll_transactions`, `approve_employee_transactions` (for approved status)
- **Response**: Success/error message with transaction ID

##### Upload Employee Transactions (Bulk)
- **URL**: `/app/payroll_controller.php?action=save&endpoint=upload_transaction`
- **Method**: POST (multipart/form-data)
- **Parameters**:
  - `file` (file, required): CSV file with transaction data
- **CSV Format**: staff_no, employee_id, full_name, transDate, transaction_type, transaction_subtype, amount, status, comments
- **Permissions**: `create_payroll_transactions`, `approve_employee_transactions` (for approved transactions)
- **Response**: Success/error message with processing details

##### Generate Payroll
- **URL**: `/app/payroll_controller.php?action=save&endpoint=payroll`
- **Method**: POST
- **Parameters**:
  - `ref` (string, required): Reference type ("Employee", "Department", "Location", "All")
  - `ref_id` (int, optional): Reference ID (not needed for "All")
  - `ref_name` (string, required): Reference name
  - `month[]` (array, required): Array of months to process
- **Permissions**: `create_payroll`
- **Response**: Success/error message with payroll ID

#### Update Operations

##### Update Employee Transaction
- **URL**: `/app/payroll_controller.php?action=update&endpoint=transaction`
- **Method**: POST
- **Parameters**: Same as create transaction, plus:
  - `transaction_id` (int, required): Transaction ID to update
- **Permissions**: `edit_payroll_transactions`, `manage_payroll_transactions` (for approval)
- **Response**: Success/error message

##### Approve/Reject Payroll
- **URL**: `/app/payroll_controller.php?action=update&endpoint=approvePayroll`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Payroll ID
  - `status` (string, required): New status ("Approved", "Rejected")
  - `emp_id` (int, optional): Specific employee ID (for individual approval)
- **Permissions**: `manage_payroll`
- **Response**: Success/error message

##### Pay Payroll
- **URL**: `/app/payroll_controller.php?action=update&endpoint=payPayroll`
- **Method**: POST
- **Parameters**:
  - `payroll_id` (int, required): Payroll ID
  - `payroll_detId` (int, optional): Specific payroll detail ID
  - `slcBank` (int, required): Bank account ID for payment
  - `payDate` (date, required): Payment date
- **Permissions**: `manage_payroll`
- **Response**: Success/error message with payment details

##### Customize Table Columns
- **URL**: `/app/payroll_controller.php?action=update&endpoint=columns4CustomizeTable`
- **Method**: POST
- **Parameters**:
  - `table` (string, required): Table name
  - `columns[]` (array, required): Array of column names to show
- **Response**: "updated" string

#### Load Operations

##### Load Employee Transactions
- **URL**: `/app/payroll_controller.php?action=load&endpoint=transactions`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with transaction records

##### Load Payroll Records
- **URL**: `/app/payroll_controller.php?action=load&endpoint=payroll`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with payroll records including employee counts

##### Load Payroll Details
- **URL**: `/app/payroll_controller.php?action=load&endpoint=payroll_details`
- **Method**: POST
- **Parameters**: 
  - DataTable parameters (length, start, search, order)
  - `payroll_id` (int, required): Payroll ID
  - `month` (string, optional): Month filter
- **Response**: DataTable format with payroll detail records

### 7. Performance Controller (`performance_controller.php`)

#### Save Operations

##### Create Performance Indicator
- **URL**: `/app/performance_controller.php?action=save&endpoint=indicators`
- **Method**: POST
- **Parameters**:
  - `department_id` (int, required): Department ID
  - `designation_id` (int, required): Designation ID
  - `department` (string, required): Department name
  - `designation` (string, required): Designation name
  - `business_pro` (int, optional): Business process rating (0-5)
  - `oral_com` (int, optional): Oral communication rating (0-5)
  - `leadership` (int, optional): Leadership rating (0-5)
  - `project_mgt` (int, optional): Project management rating (0-5)
  - `res_allocating` (int, optional): Resource allocation rating (0-5)
- **Response**: Success/error message with indicator ID

##### Create Employee Appraisal
- **URL**: `/app/performance_controller.php?action=save&endpoint=appraisal`
- **Method**: POST
- **Parameters**:
  - `emp_id` (int, required): Employee ID
  - `department_id` (int, required): Department ID
  - `designation_id` (int, required): Designation ID
  - `department` (string, required): Department name
  - `designation` (string, required): Designation name
  - `indicator_rating` (decimal, required): Indicator rating
  - `appraisal_rating` (decimal, required): Appraisal rating
  - `month` (string, required): Appraisal month
  - `remarks` (string, optional): Appraisal remarks
- **Response**: Success/error message with appraisal ID

##### Create Goal Tracking
- **URL**: `/app/performance_controller.php?action=save&endpoint=goal_tracking`
- **Method**: POST
- **Parameters**:
  - `department_id` (int, required): Department ID
  - `type_id` (int, required): Goal type ID
  - `department` (string, required): Department name
  - `type` (string, required): Goal type name
  - `subject` (string, required): Goal subject
  - `target` (string, required): Goal target
  - `description` (string, optional): Goal description
  - `start_date` (date, required): Start date
  - `end_date` (date, required): End date
  - `progress` (int, required): Progress percentage (0-100)
  - `status` (string, required): Goal status
- **Response**: Success/error message with goal ID

#### Update Operations

##### Update Performance Indicator
- **URL**: `/app/performance_controller.php?action=update&endpoint=indicators`
- **Method**: POST
- **Parameters**: Same as create indicator, plus:
  - `id` (int, required): Indicator ID to update
- **Response**: Success/error message

##### Update Employee Appraisal
- **URL**: `/app/performance_controller.php?action=update&endpoint=appraisal`
- **Method**: POST
- **Parameters**: Same as create appraisal, plus:
  - `id` (int, required): Appraisal ID to update
- **Response**: Success/error message

##### Update Goal Tracking
- **URL**: `/app/performance_controller.php?action=update&endpoint=goal_tracking`
- **Method**: POST
- **Parameters**: Same as create goal tracking, plus:
  - `id` (int, required): Goal ID to update
- **Response**: Success/error message

#### Load Operations

##### Load Performance Indicators
- **URL**: `/app/performance_controller.php?action=load&endpoint=indicators`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with indicator records including overall ratings

##### Load Employee Appraisals
- **URL**: `/app/performance_controller.php?action=load&endpoint=appraisals`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with appraisal records

##### Load Goal Tracking
- **URL**: `/app/performance_controller.php?action=load&endpoint=goal_tracking`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with goal tracking records

#### Get Operations

##### Get Performance Indicator
- **URL**: `/app/performance_controller.php?action=get&endpoint=indicator`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Indicator ID
- **Response**: Indicator data object

##### Get Indicator for Appraisals
- **URL**: `/app/performance_controller.php?action=get&endpoint=indicator4Appraisals`
- **Method**: POST
- **Parameters**:
  - `department_id` (int, required): Department ID
  - `designation_id` (int, required): Designation ID
- **Response**: Simplified indicator attributes for appraisal form

##### Get Employee Appraisal
- **URL**: `/app/performance_controller.php?action=get&endpoint=appraisal`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Appraisal ID
- **Response**: Appraisal data object

##### Get Goal Tracking
- **URL**: `/app/performance_controller.php?action=get&endpoint=goal_tracking`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Goal ID
- **Response**: Goal tracking data object with formatted dates

#### Search Operations

##### Search Employees for Performance
- **URL**: `/app/performance_controller.php?action=search&endpoint=employee4Select`
- **Method**: POST
- **Parameters**:
  - `search` (string, optional): Search term
  - `searchFor` (string, optional): Search context
- **Response**: HTML options for employee selection

#### Delete Operations

##### Delete Performance Indicator
- **URL**: `/app/performance_controller.php?action=delete&endpoint=indicator`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Indicator ID
- **Response**: Success/error message

##### Delete Employee Appraisal
- **URL**: `/app/performance_controller.php?action=delete&endpoint=appraisal`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Appraisal ID
- **Response**: Success/error message

##### Delete Goal Tracking
- **URL**: `/app/performance_controller.php?action=delete&endpoint=goal_tracking`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Goal ID
- **Response**: Success/error message

### 8. Report Controller (`report_controller.php`)

#### Search Operations

##### Search Employees for Reports
- **URL**: `/app/report_controller.php?action=search&endpoint=employee4Select`
- **Method**: POST
- **Parameters**:
  - `search` (string, optional): Search term
  - `searchFor` (string, optional): Search context
- **Response**: HTML options for employee selection

##### Search Departments for Reports
- **URL**: `/app/report_controller.php?action=search&endpoint=department4Select`
- **Method**: POST
- **Parameters**:
  - `search` (string, optional): Search term
  - `searchFor` (string, optional): Search context
- **Response**: HTML options for department selection

##### Search Locations for Reports
- **URL**: `/app/report_controller.php?action=search&endpoint=location4Select`
- **Method**: POST
- **Parameters**:
  - `search` (string, optional): Search term
  - `searchFor` (string, optional): Search context
- **Response**: HTML options for location selection

#### Report Generation

##### Generate All Employees Report
- **URL**: `/app/report_controller.php?action=report`
- **Method**: POST
- **Parameters**:
  - `report` (string, required): "allEmployees"
  - `gender` (string, optional): Gender filter
  - `state` (int, optional): State ID filter
  - `department` (int, optional): Department ID filter
  - `location` (int, optional): Location ID filter
  - `salary` (decimal, optional): Minimum salary filter
  - `salary_up` (decimal, optional): Maximum salary filter
  - `age` (string, optional): Age range filter (e.g., "18-24", "65+")
  - DataTable parameters (length, start, search, order)
- **Response**: DataTable format with filtered employee records

##### Generate Absence Report
- **URL**: `/app/report_controller.php?action=report`
- **Method**: POST
- **Parameters**:
  - `report` (string, required): "absence"
  - `month` (string, optional): Month filter (YYYY-MM format)
  - DataTable parameters (length, start, search, order)
- **Response**: DataTable format with attendance statistics including present, paid leave, unpaid leave, not hired, holiday, and no-show counts

##### Generate Compensation Report
- **URL**: `/app/report_controller.php?action=report`
- **Method**: POST
- **Parameters**:
  - `report` (string, required): "componsation"
  - `month` (string, optional): Month filter (YYYY-MM format)
  - DataTable parameters (length, start, search, order)
- **Response**: DataTable format with payroll details including base salary, earnings, deductions, tax, and net salary

##### Generate Deductions Report
- **URL**: `/app/report_controller.php?action=report`
- **Method**: POST
- **Parameters**:
  - `report` (string, required): "deductions"
  - `month` (string, optional): Month filter (YYYY-MM format)
  - DataTable parameters (length, start, search, order)
- **Response**: DataTable format with employees who have deductions, showing earnings, total deductions, and net salary

### 9. Settings Controller (`settings_controller.php`)

#### Update Operations

##### Update System Setting
- **URL**: `/app/settings_controller.php?action=update&endpoint=setting`
- **Method**: POST
- **Parameters**:
  - `type` (string, required): Setting type/key
  - `details` (string, optional): Setting details
  - `value` (string, optional): Setting value
  - `section` (string, optional): Setting section
  - `remarks` (string, optional): Setting remarks
- **Permissions**: `edit_settings`
- **Response**: Success/error message

#### Get Operations

##### Get System Setting
- **URL**: `/app/settings_controller.php?action=get&endpoint=setting`
- **Method**: POST
- **Parameters**:
  - `type` (string, required): Setting type/key
- **Response**: Setting data object

##### Get Branch Data
- **URL**: `/app/settings_controller.php?action=get&endpoint=branch`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Branch ID
- **Response**: Branch data object

### 10. Training Controller (`training_controller.php`)

#### Save Operations

##### Create Trainer
- **URL**: `/app/training_controller.php?action=save&endpoint=trainers`
- **Method**: POST
- **Parameters**:
  - `full_name` (string, required): Trainer full name
  - `phone` (string, optional): Trainer phone number
  - `email` (string, required): Trainer email address
  - `status` (string, optional): Trainer status ("Active", "Inactive")
- **Permissions**: `create_trainers`
- **Response**: Success/error message with trainer ID

##### Create Training Program
- **URL**: `/app/training_controller.php?action=save&endpoint=training`
- **Method**: POST
- **Parameters**:
  - `type_id` (int, required): Training type ID
  - `option_id` (int, required): Training option ID
  - `trainer_id` (int, required): Trainer ID
  - `employee_id` (int|array, required): Employee ID(s) - can be single ID or array
  - `cost` (decimal, optional): Training cost
  - `start_date` (date, required): Training start date
  - `end_date` (date, required): Training end date
  - `description` (string, optional): Training description
- **Permissions**: `create_training`
- **Response**: Success/error message with count of training records created

#### Update Operations

##### Update Trainer
- **URL**: `/app/training_controller.php?action=update&endpoint=trainers`
- **Method**: POST
- **Parameters**: Same as create trainer, plus:
  - `id` (int, required): Trainer ID to update
- **Permissions**: `edit_trainers`
- **Response**: Success/error message

##### Update Training Program
- **URL**: `/app/training_controller.php?action=update&endpoint=training`
- **Method**: POST
- **Parameters**: Same as create training (single employee only), plus:
  - `id` (int, required): Training ID to update
  - `status` (string, optional): Training status
- **Permissions**: `edit_training`
- **Response**: Success/error message

#### Load Operations

##### Load Trainers
- **URL**: `/app/training_controller.php?action=load&endpoint=trainers`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with trainer records

##### Load Training Programs
- **URL**: `/app/training_controller.php?action=load&endpoint=training`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with training program records

#### Get Operations

##### Get Trainer
- **URL**: `/app/training_controller.php?action=get&endpoint=trainers`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Trainer ID
- **Response**: Trainer data object

##### Get Training Program
- **URL**: `/app/training_controller.php?action=get&endpoint=training`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Training ID
- **Response**: Training program data object

#### Search Operations

##### Search Employees for Training
- **URL**: `/app/training_controller.php?action=search&endpoint=employee4Training`
- **Method**: POST
- **Parameters**:
  - `search` (string, optional): Search term
  - `searchFor` (string, optional): Search context
- **Response**: HTML options for employee selection

#### Delete Operations

##### Delete Trainer
- **URL**: `/app/training_controller.php?action=delete&endpoint=trainers`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Trainer ID
- **Permissions**: `delete_trainers`
- **Response**: Success/error message

##### Delete Training Program
- **URL**: `/app/training_controller.php?action=delete&endpoint=training`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Training ID
- **Permissions**: `delete_training`
- **Response**: Success/error message

### 11. Users Controller (`users_controller.php`)

#### Save Operations

##### Create User
- **URL**: `/app/users_controller.php?action=save&endpoint=user`
- **Method**: POST
- **Parameters**:
  - `full_name` (string, required): User full name
  - `phone` (string, required): User phone number
  - `email` (string, required): User email address
  - `username` (string, required): Username for login
  - `password` (string, required): User password (will be hashed)
  - `sysRole` (string, required): System role
- **Permissions**: `create_users`
- **Response**: Success/error message with user ID

##### Create Role
- **URL**: `/app/users_controller.php?action=save&endpoint=role`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Role name
  - `actions[]` (array, required): Array of permission codes
- **Permissions**: `create_roles`
- **Response**: Success/error message with role ID

#### Update Operations

##### Update User
- **URL**: `/app/users_controller.php?action=update&endpoint=user`
- **Method**: POST
- **Parameters**:
  - `user_id` (int, required): User ID to update
  - `full_name` (string, required): User full name
  - `phone` (string, required): User phone number
  - `email` (string, required): User email address
  - `username` (string, required): Username for login
  - `sysRole` (string, required): System role
  - `slcStatus` (string, optional): User status ("Active", "Inactive")
- **Permissions**: `edit_users`
- **Response**: Success/error message

##### Update Role
- **URL**: `/app/users_controller.php?action=update&endpoint=role`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Role ID to update
  - `name` (string, required): Role name
  - `actions[]` (array, required): Array of permission codes
- **Permissions**: `edit_roles`
- **Response**: Success/error message

##### Update User Password
- **URL**: `/app/users_controller.php?action=update&endpoint=user_password`
- **Method**: POST
- **Parameters**:
  - `user_id` (int, required): User ID
  - `newPassword` (string, required): New password (will be hashed)
- **Permissions**: `edit_users`
- **Response**: Success/error message

#### Load Operations

##### Load Users
- **URL**: `/app/users_controller.php?action=load&endpoint=users`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with user records including role names

#### Search Operations

##### Search Employees for User Creation
- **URL**: `/app/users_controller.php?action=search&endpoint=employee4UserCreate`
- **Method**: POST
- **Parameters**:
  - `search` (string, required): Search term
  - `searchFor` (string, optional): Search context
- **Response**: HTML formatted employee data for selection (employees without existing user accounts)

#### Get Operations

##### Get Role for Editing
- **URL**: `/app/users_controller.php?action=get&endpoint=role4Edit`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Role ID
- **Response**: HTML formatted role editing form with permissions checkboxes

##### Get User
- **URL**: `/app/users_controller.php?action=get&endpoint=user`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): User ID
- **Response**: User data object

#### Delete Operations

##### Delete Role
- **URL**: `/app/users_controller.php?action=delete&endpoint=role`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Role ID
- **Permissions**: `delete_roles`
- **Response**: Success/error message
- **Note**: Cannot delete roles that are assigned to users

## Common Parameters

### DataTable Parameters
Most load operations accept these standard DataTable parameters:
- `length` (int): Number of records per page
- `start` (int): Starting record index
- `draw` (int): Draw counter for DataTable
- `search[value]` (string): Global search term
- `order[0][column]` (int): Column index to sort by
- `order[0][dir]` (string): Sort direction ("asc" or "desc")

### File Upload Parameters
File upload endpoints accept:
- `file` (file): The uploaded file
- File types supported: CSV for bulk operations, images for avatars, documents for employee files
- Maximum file size: 5MB for images, varies for other file types

## Error Handling

### Common Error Responses
- **Authentication Error**: `{"error": true, "msg": "You are not authorized to perform this action"}`
- **Validation Error**: `{"error": true, "msg": "Missing required fields"}`
- **Database Error**: `{"error": true, "msg": "Error: Something went wrong", "sql_error": "Database error details"}`
- **File Upload Error**: `{"error": true, "msg": "Invalid file type. Please upload a valid CSV file."}`

### HTTP Status Codes
- **200**: Success
- **400**: Bad Request (validation errors)
- **401**: Unauthorized (authentication required)
- **403**: Forbidden (insufficient permissions)
- **500**: Internal Server Error (database or system errors)

## Rate Limiting

The application does not implement explicit rate limiting, but relies on PHP session management and database connection limits for resource control.

## Security Considerations

1. **Authentication**: All endpoints require valid PHP session
2. **Authorization**: Permission-based access control using `check_auth()` function
3. **Input Validation**: All inputs are escaped using `escapePostData()` and `escapeStr()` functions
4. **SQL Injection Prevention**: Prepared statements used where applicable
5. **File Upload Security**: File type and size validation for uploads
6. **Password Security**: Passwords are hashed using PHP's `password_hash()` function

### 8. Settings Controller (`settings_controller.php`)

#### Update Operations

##### Update System Setting
- **URL**: `/app/settings_controller.php?action=update&endpoint=setting`
- **Method**: POST
- **Parameters**:
  - `company_name` (string, optional): Company name
  - `company_address` (string, optional): Company address
  - `company_phones` (string, optional): Company phone numbers
  - `company_emails` (string, optional): Company email addresses
  - `company_logo` (file, optional): Company logo image
- **Permissions**: `manage_settings`
- **Response**: Success/error message

#### Get Operations

##### Get System Setting
- **URL**: `/app/settings_controller.php?action=get&endpoint=setting`
- **Method**: POST
- **Response**: System settings data

### 9. Report Controller (`report_controller.php`)

#### Search Operations

##### Search Employees for Reports
- **URL**: `/app/report_controller.php?action=search&endpoint=employee4Select`
- **Method**: POST
- **Parameters**:
  - `searchFor` (string, required): Search term
- **Response**: Array of matching employees

#### Report Generation

##### Generate Report
- **URL**: `/app/report_controller.php?action=report`
- **Method**: POST
- **Parameters**:
  - `report` (string, required): Report type
  - Additional parameters based on report type
- **Response**: Report data or file download

### 10. Users Controller (`users_controller.php`)

#### Save Operations

##### Create User
- **URL**: `/app/users_controller.php?action=save&endpoint=user`
- **Method**: POST
- **Parameters**:
  - `full_name` (string, required): User full name
  - `phone` (string, required): Phone number
  - `email` (string, required): Email address
  - `username` (string, required): Username
  - `password` (string, required): Password
  - `sysRole` (int, required): System role ID
- **Permissions**: `create_users`
- **Response**: Success/error message with user ID

##### Create Role
- **URL**: `/app/users_controller.php?action=save&endpoint=role`
- **Method**: POST
- **Parameters**:
  - `name` (string, required): Role name
  - `actions[]` (array, required): Array of permission actions
- **Permissions**: `create_roles`
- **Response**: Success/error message with role ID

#### Update Operations

##### Update User
- **URL**: `/app/users_controller.php?action=update&endpoint=user`
- **Method**: POST
- **Parameters**: Same as create user, plus:
  - `id` (int, required): User ID to update
- **Permissions**: `edit_users`
- **Response**: Success/error message

##### Update Role
- **URL**: `/app/users_controller.php?action=update&endpoint=role`
- **Method**: POST
- **Parameters**: Same as create role, plus:
  - `id` (int, required): Role ID to update
- **Permissions**: `edit_roles`
- **Response**: Success/error message

#### Load Operations

##### Load Users
- **URL**: `/app/users_controller.php?action=load&endpoint=users`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with user records

##### Load Roles
- **URL**: `/app/users_controller.php?action=load&endpoint=roles`
- **Method**: POST
- **Parameters**: DataTable parameters (length, start, search, order)
- **Response**: DataTable format with role records

#### Search Operations

##### Search Employees for User Creation
- **URL**: `/app/users_controller.php?action=search&endpoint=employee4UserCreate`
- **Method**: POST
- **Parameters**:
  - `search` (string, required): Search term
- **Response**: Array of matching employees

#### Get Operations

##### Get Role for Editing
- **URL**: `/app/users_controller.php?action=get&endpoint=role4Edit`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Role ID
- **Response**: Role data with permissions

##### Get User Details
- **URL**: `/app/users_controller.php?action=get&endpoint=user`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): User ID
- **Response**: User data

#### Delete Operations

##### Delete Role
- **URL**: `/app/users_controller.php?action=delete&endpoint=role`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): Role ID
- **Permissions**: `delete_roles`
- **Response**: Success/error message

##### Delete User
- **URL**: `/app/users_controller.php?action=delete&endpoint=user`
- **Method**: POST
- **Parameters**:
  - `id` (int, required): User ID
- **Permissions**: `delete_users`
- **Response**: Success/error message

## Additional Endpoints

### File Operations

#### Download Employee Documents
- **URL**: `/app/hrm_controller.php?action=download&endpoint=folder_docs`
- **Method**: GET
- **Parameters**:
  - `id` (int, required): Document ID
- **Response**: File download

#### View Employee Documents
- **URL**: `/app/hrm_controller.php?action=view&endpoint=folder_docs`
- **Method**: GET
- **Parameters**:
  - `id` (int, required): Document ID
- **Response**: File content for viewing

### Bulk Operations

#### Bulk Employee Actions
- **URL**: `/app/hrm_controller.php?action=bulk`
- **Method**: POST
- **Parameters**:
  - `ids[]` (array, required): Array of employee IDs
  - `action` (string, required): Action to perform ("activate", "deactivate", "delete")
- **Permissions**: Based on action type
- **Response**: Success/error message with operation results

## Rate Limiting

The application does not implement explicit rate limiting, but relies on PHP session management and database connection limits for resource control.

## Security Considerations

1. **Authentication**: All endpoints require valid PHP session
2. **Authorization**: Permission-based access control using `check_auth()` function
3. **Input Validation**: All inputs are escaped using `escapePostData()` and `escapeStr()` functions
4. **SQL Injection Prevention**: Prepared statements used where applicable
5. **File Upload Security**: File type and size validation for uploads
6. **Password Security**: Passwords are hashed using PHP's `password_hash()` function

## Integration Examples

See the `integration-examples.md` file for detailed cURL examples and code snippets for common API operations.