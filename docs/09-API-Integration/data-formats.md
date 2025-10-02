# Data Formats Documentation

## Overview

This document describes the data formats used in the HRM application's API endpoints, including JSON request/response structures, CSV file formats for bulk operations, and data validation rules.

## JSON Request/Response Structures

### Standard Response Format

All API endpoints return JSON responses following these standard formats:

#### Success Response
```json
{
    "error": false,
    "msg": "Operation completed successfully",
    "id": 123,
    "data": {
        // Response data object
    }
}
```

#### Error Response
```json
{
    "error": true,
    "msg": "Error description",
    "sql_error": "Database error details (optional)"
}
```

#### DataTable Response
```json
{
    "status": 201,
    "error": false,
    "data": [
        // Array of data objects
    ],
    "draw": 1,
    "iTotalRecords": 100,
    "iTotalDisplayRecords": 100,
    "msg": "Records found message"
}
```

### Employee Data Structures

#### Employee Creation Request
```json
{
    "full_name": "John Doe",
    "phone_number": "+1234567890",
    "email": "john.doe@example.com",
    "gender": "Male",
    "national_id": "123456789",
    "date_of_birth": "1990-01-15",
    "city": "New York",
    "address": "123 Main Street",
    "branch_id": 1,
    "location_id": 1,
    "position": "Software Developer",
    "hire_date": "2023-01-01",
    "contract_start": "2023-01-01",
    "contract_end": "2024-12-31",
    "salary": 75000.00,
    "degree": ["Bachelor of Science", "Master of Science"],
    "institution": ["University A", "University B"],
    "startYear": ["2008", "2012"],
    "endYear": ["2012", "2014"],
    "project_id": [1, 2, 3],
    "budget_code": ["BC001", "BC002"]
}
```

#### Employee Response
```json
{
    "error": false,
    "msg": "Employee created successfully",
    "id": 123,
    "data": {
        "employee_id": 123,
        "staff_no": "EMP123",
        "full_name": "John Doe",
        "phone_number": "+1234567890",
        "email": "john.doe@example.com",
        "gender": "Male",
        "national_id": "123456789",
        "date_of_birth": "1990-01-15",
        "age": 33,
        "city": "New York",
        "address": "123 Main Street",
        "branch_id": 1,
        "branch": "IT Department",
        "location_id": 1,
        "location_name": "Headquarters",
        "position": "Software Developer",
        "designation": "Senior Developer",
        "hire_date": "2023-01-01",
        "contract_start": "2023-01-01",
        "contract_end": "2024-12-31",
        "contract_type": "Full-time",
        "salary": 75000.00,
        "work_days": 5,
        "work_hours": 8,
        "status": "Active",
        "avatar": "avatar_123.jpg",
        "added_by": 1,
        "added_date": "2023-01-01 10:00:00",
        "updated_by": null,
        "updated_date": null
    }
}
```

### Attendance Data Structures

#### Attendance Record Request
```json
{
    "ref": "Employee",
    "ref_id": 123,
    "ref_name": "John Doe",
    "atten_date": "2023-12-01",
    "atten_status": "P"
}
```

#### Attendance Response
```json
{
    "error": false,
    "msg": "Attendance recorded successfully",
    "id": 456,
    "data": {
        "id": 456,
        "ref": "Employee",
        "ref_id": 123,
        "ref_name": "John Doe",
        "atten_date": "2023-12-01",
        "added_by": 1,
        "added_date": "2023-12-01 09:00:00",
        "details": [
            {
                "id": 789,
                "atten_id": 456,
                "emp_id": 123,
                "full_name": "John Doe",
                "staff_no": "EMP123",
                "status": "P",
                "atten_date": "2023-12-01"
            }
        ]
    }
}
```

#### Bulk Attendance Request
```json
{
    "date": "2023-12-01",
    "employees": [123, 124, 125],
    "statuses": ["P", "A", "L"]
}
```

### Timesheet Data Structures

#### Timesheet Record Request
```json
{
    "ts_date": "2023-12-01",
    "emp_id": 123,
    "time_in": "09:00:00",
    "time_out": "17:00:00"
}
```

#### Timesheet Response
```json
{
    "error": false,
    "msg": "Timesheet recorded successfully",
    "id": 789,
    "data": {
        "id": 789,
        "ts_date": "2023-12-01",
        "added_by": 1,
        "added_date": "2023-12-01 17:30:00",
        "details": [
            {
                "id": 101,
                "ts_id": 789,
                "emp_id": 123,
                "full_name": "John Doe",
                "staff_no": "EMP123",
                "time_in": "09:00:00",
                "time_out": "17:00:00",
                "total_hours": 8.0,
                "ts_date": "2023-12-01"
            }
        ]
    }
}
```

### Payroll Data Structures

#### Payroll Generation Request
```json
{
    "ref": "Department",
    "ref_id": 1,
    "ref_name": "IT Department",
    "month": ["2023-12"]
}
```

#### Payroll Response
```json
{
    "error": false,
    "msg": "Payroll recorded successfully",
    "id": 201,
    "data": {
        "id": 201,
        "ref": "Department",
        "ref_id": 1,
        "ref_name": "IT Department",
        "month": "2023-12",
        "status": "Created",
        "workflow": [
            {
                "action": "Created by Admin User",
                "date": "2023-12-01 10:00:00",
                "status": "Created",
                "user_id": 1
            }
        ],
        "employee_count": 25,
        "total_amount": 187500.00,
        "added_by": 1,
        "added_date": "2023-12-01 10:00:00"
    }
}
```

#### Payroll Detail Structure
```json
{
    "id": 301,
    "payroll_id": 201,
    "emp_id": 123,
    "staff_no": "EMP123",
    "full_name": "John Doe",
    "email": "john.doe@example.com",
    "contract_type": "Full-time",
    "job_title": "Software Developer",
    "month": "2023-12",
    "required_days": 22,
    "days_worked": 20,
    "base_salary": 7500.00,
    "allowance": 500.00,
    "bonus": 1000.00,
    "commission": 0.00,
    "extra_hours": 200.00,
    "tax": 1200.00,
    "advance": 0.00,
    "loan": 500.00,
    "deductions": 100.00,
    "unpaid_days": 0.00,
    "unpaid_hours": 0.00,
    "net_salary": 7400.00,
    "bank_name": "Bank ABC",
    "bank_number": "1234567890",
    "status": "Approved",
    "pay_date": null,
    "paid_by": null,
    "paid_through": null,
    "bank_id": null
}
```

### Financial Transaction Data Structures

#### Expense Record Request
```json
{
    "bank_id": 1,
    "fn_account_id": 2,
    "amount": 1500.00,
    "payee_payer": "Office Supplies Inc",
    "description": "Monthly office supplies",
    "refNumber": "INV-2023-001",
    "paid_date": "2023-12-01"
}
```

#### Income Record Request
```json
{
    "bank_id": 1,
    "fn_account_id": 3,
    "amount": 25000.00,
    "payee_payer": "Client ABC Corp",
    "description": "Project milestone payment",
    "refNumber": "PAY-2023-001",
    "paid_date": "2023-12-01"
}
```

#### Financial Transaction Response
```json
{
    "error": false,
    "msg": "Expense recorded successfully",
    "id": 401,
    "data": {
        "id": 401,
        "type": "Expense",
        "bank_id": 1,
        "bank_name": "Main Account",
        "bank_account": "1234567890",
        "amount": 1500.00,
        "fn_account_id": 2,
        "fn_account_name": "Office Expenses",
        "payee_payer": "Office Supplies Inc",
        "description": "Monthly office supplies",
        "ref_number": "INV-2023-001",
        "status": "Active",
        "added_by": 1,
        "added_date": "2023-12-01 14:30:00"
    }
}
```

### Performance Management Data Structures

#### Performance Indicator Request
```json
{
    "department_id": 1,
    "designation_id": 2,
    "department": "IT Department",
    "designation": "Senior Developer",
    "business_pro": 4,
    "oral_com": 5,
    "leadership": 3,
    "project_mgt": 4,
    "res_allocating": 4
}
```

#### Performance Indicator Response
```json
{
    "error": false,
    "msg": "Indicator added successfully",
    "id": 501,
    "data": {
        "id": 501,
        "department_id": 1,
        "designation_id": 2,
        "department": "IT Department",
        "designation": "Senior Developer",
        "attributes": {
            "Behavioural Competencies": [
                {"name": "Business Process", "rating": 4},
                {"name": "Oral Communication", "rating": 5}
            ],
            "Organizational Competencies": [
                {"name": "Leadership", "rating": 3},
                {"name": "Project Management", "rating": 4}
            ],
            "Technical Competencies": [
                {"name": "Allocating Resources", "rating": 4}
            ]
        },
        "overall_rating": 4.0,
        "added_by": 1,
        "added_date": "2023-12-01 11:00:00"
    }
}
```

#### Employee Appraisal Request
```json
{
    "emp_id": 123,
    "department_id": 1,
    "designation_id": 2,
    "department": "IT Department",
    "designation": "Senior Developer",
    "indicator_rating": 4.0,
    "appraisal_rating": 4.2,
    "month": "2023-12",
    "remarks": "Excellent performance this month"
}
```

### Training Data Structures

#### Trainer Request
```json
{
    "full_name": "Dr. Jane Smith",
    "phone": "+1234567890",
    "email": "jane.smith@training.com",
    "status": "Active"
}
```

#### Training Program Request
```json
{
    "type_id": 1,
    "option_id": 2,
    "trainer_id": 3,
    "employee_id": [123, 124, 125],
    "cost": 2500.00,
    "start_date": "2023-12-15",
    "end_date": "2023-12-20",
    "description": "Advanced software development training"
}
```

#### Training Program Response
```json
{
    "error": false,
    "msg": "3 training record(s) added successfully",
    "data": {
        "training_records": [
            {
                "id": 601,
                "type_id": 1,
                "type_name": "Technical Training",
                "option_id": 2,
                "option_name": "Software Development",
                "trainer_id": 3,
                "trainer_name": "Dr. Jane Smith",
                "trainer_phone": "+1234567890",
                "trainer_email": "jane.smith@training.com",
                "emp_id": 123,
                "full_name": "John Doe",
                "staff_no": "EMP123",
                "cost": 2500.00,
                "start_date": "2023-12-15",
                "end_date": "2023-12-20",
                "description": "Advanced software development training",
                "status": "Active"
            }
        ]
    }
}
```

### User Management Data Structures

#### User Creation Request
```json
{
    "full_name": "Admin User",
    "phone": "+1234567890",
    "email": "admin@company.com",
    "username": "admin",
    "password": "securepassword123",
    "sysRole": "1"
}
```

#### Role Creation Request
```json
{
    "name": "HR Manager",
    "actions": [
        "create_employees",
        "edit_employees",
        "view_employees",
        "create_attendance",
        "manage_payroll"
    ]
}
```

## CSV File Formats

### Employee Bulk Upload CSV Format

**File**: `employees.csv`
**Headers**: staff_no, full_name, phone_number, email, gender, national_id, date_of_birth, city, address, payment_bank, payment_account, branch, designation, state, location, hire_date, contract_start, contract_end, contract_type, salary, tax_exempt, budget_codes, projects, moh_contract, work_days, work_hours, grade, seniority

**Example**:
```csv
staff_no,full_name,phone_number,email,gender,national_id,date_of_birth,city,address,payment_bank,payment_account,branch,designation,state,location,hire_date,contract_start,contract_end,contract_type,salary,tax_exempt,budget_codes,projects,moh_contract,work_days,work_hours,grade,seniority
EMP001,John Doe,+1234567890,john.doe@company.com,Male,123456789,1990-01-15,New York,123 Main St,Bank ABC,1234567890,IT Department,Senior Developer,New York,Headquarters,2023-01-01,2023-01-01,2024-12-31,Full-time,75000,No,"BC001,BC002","Project A,Project B",No,5,8,Senior,5
EMP002,Jane Smith,+1234567891,jane.smith@company.com,Female,123456790,1985-05-20,Los Angeles,456 Oak Ave,Bank XYZ,0987654321,HR Department,HR Manager,California,West Coast,2022-03-15,2022-03-15,2025-03-14,Full-time,85000,No,BC003,Project C,No,5,8,Manager,8
```

### Attendance Bulk Upload CSV Format

**File**: `attendance.csv`
**Headers**: staff_no, employee_id, full_name, atten_date, attend_status, ref, ref_id, ref_name

**Example**:
```csv
staff_no,employee_id,full_name,atten_date,attend_status,ref,ref_id,ref_name
EMP001,123,John Doe,2023-12-01,P,Employee,123,John Doe
EMP002,124,Jane Smith,2023-12-01,A,Employee,124,Jane Smith
EMP003,125,Bob Johnson,2023-12-01,L,Employee,125,Bob Johnson
```

**Attendance Status Codes**:
- `P`: Present
- `A`: Absent
- `L`: Leave
- `PL`: Paid Leave
- `UL`: Unpaid Leave
- `H`: Holiday
- `N`: No Show
- `NH`: Not Hired

### Timesheet Bulk Upload CSV Format

**File**: `timesheet.csv`
**Headers**: staff_no, employee_id, full_name, ts_date, attend_status, time_in, time_out, ref, ref_id, ref_name

**Example**:
```csv
staff_no,employee_id,full_name,ts_date,attend_status,time_in,time_out,ref,ref_id,ref_name
EMP001,123,John Doe,2023-12-01,P,09:00:00,17:00:00,Employee,123,John Doe
EMP002,124,Jane Smith,2023-12-01,P,08:30:00,16:30:00,Employee,124,Jane Smith
EMP003,125,Bob Johnson,2023-12-01,P,09:15:00,17:15:00,Employee,125,Bob Johnson
```

### Employee Transactions Bulk Upload CSV Format

**File**: `transactions.csv`
**Headers**: staff_no, employee_id, full_name, trans_date, transaction_type, transaction_subtype, amount, status, comments

**Example**:
```csv
staff_no,employee_id,full_name,trans_date,transaction_type,transaction_subtype,amount,status,comments
EMP001,123,John Doe,2023-12-01,Allowance,Transport,500.00,Approved,Monthly transport allowance
EMP002,124,Jane Smith,2023-12-01,Bonus,Performance,2000.00,Approved,Q4 performance bonus
EMP003,125,Bob Johnson,2023-12-01,Deduction,Loan,300.00,Approved,Monthly loan deduction
```

**Transaction Types**:
- `Allowance`: Additional payments
- `Bonus`: Performance or special bonuses
- `Commission`: Sales commissions
- `Deduction`: Salary deductions
- `Loan`: Loan deductions
- `Advance`: Salary advances

## Data Validation Rules

### Field Validation

#### Required Fields
- **Employee**: full_name, phone_number, email, hire_date, branch_id, location_id, salary
- **Attendance**: ref, ref_id, atten_date, atten_status
- **Timesheet**: ts_date, emp_id, time_in, time_out
- **Financial Transaction**: bank_id, fn_account_id, amount, payee_payer, paid_date
- **User**: full_name, phone, email, username, password, sysRole

#### Data Type Validation
- **Dates**: YYYY-MM-DD format
- **Times**: HH:MM:SS format
- **Decimals**: Up to 2 decimal places for monetary values
- **Integers**: Positive integers for IDs
- **Email**: Valid email format
- **Phone**: International phone number format

#### Length Constraints
- **Names**: 1-255 characters
- **Email**: 1-255 characters
- **Phone**: 1-20 characters
- **Descriptions**: Up to 1000 characters
- **Comments**: Up to 2000 characters

#### Value Constraints
- **Status Fields**: Predefined values only
- **Ratings**: 0-5 scale for performance indicators
- **Progress**: 0-100 percentage for goals
- **Amounts**: Positive values for salaries and transactions

### File Upload Validation

#### Supported File Types
- **CSV Files**: text/csv MIME type
- **Images**: jpg, jpeg, png, gif, webp
- **Documents**: pdf, doc, docx, txt

#### File Size Limits
- **Images**: Maximum 5MB
- **CSV Files**: Maximum 10MB
- **Documents**: Maximum 20MB

#### CSV Validation Rules
- **Headers**: Must match expected column names exactly
- **Row Count**: Minimum 1 data row (excluding header)
- **Column Count**: Must match expected number of columns
- **Data Integrity**: All required fields must have values
- **Duplicate Prevention**: System checks for existing records

## Error Response Formats

### Validation Error Response
```json
{
    "error": true,
    "msg": "Validation failed",
    "validation_errors": {
        "full_name": "Full name is required",
        "email": "Invalid email format",
        "salary": "Salary must be a positive number"
    }
}
```

### File Upload Error Response
```json
{
    "error": true,
    "msg": "File upload failed",
    "file_errors": {
        "type": "Invalid file type. Only CSV files are allowed",
        "size": "File size exceeds 10MB limit",
        "format": "CSV file must contain required headers"
    }
}
```

### Bulk Operation Response
```json
{
    "error": false,
    "msg": "Bulk operation completed",
    "total": 100,
    "processed": 95,
    "success_count": 90,
    "error_count": 5,
    "errors": "Row 15: Missing required field 'email'. Row 23: Invalid date format. Row 45: Duplicate employee ID. Row 67: Invalid department. Row 89: Salary must be numeric.",
    "progress": 100
}
```

## Integration Considerations

### Content-Type Headers
- **JSON Requests**: `application/json`
- **Form Data**: `application/x-www-form-urlencoded`
- **File Uploads**: `multipart/form-data`

### Character Encoding
- **Default**: UTF-8
- **Database**: UTF-8 collation
- **File Uploads**: UTF-8 encoding required

### Date/Time Handling
- **Storage**: UTC timezone in database
- **Display**: Local timezone based on user settings
- **API**: ISO 8601 format (YYYY-MM-DD HH:MM:SS)

### Pagination
- **Default Page Size**: 20 records
- **Maximum Page Size**: 100 records
- **Offset-based**: Using `start` and `length` parameters

### Search and Filtering
- **Global Search**: Searches across multiple fields
- **Column Search**: Field-specific filtering
- **Date Range**: From/to date filtering
- **Status Filtering**: Active/inactive record filtering