# Attendance System Code Analysis

## Overview

This document provides a comprehensive analysis of the attendance-related code and database structure in the HRM application. The attendance system manages employee attendance tracking, timesheet management, leave management, and resource allocation through a combination of PHP classes, controllers, and database tables.

## PHP Classes and Files Analysis

### 1. AttendanceClass.php

**Location**: `app/AttendanceClass.php`

This file contains multiple model classes that extend the base `Model` class for attendance-related functionality:

#### Classes Defined:

1. **LeaveTypes** - Manages leave type definitions
   - Table: `leave_types`
   - Global instance: `$leaveTypesClass`

2. **EmployeeLeave** - Handles employee leave requests and records
   - Table: `employee_leave`
   - Global instance: `$employeeLeaveClass`

3. **Attendance** - Core attendance tracking
   - Table: `attendance`
   - Global instance: `$attendanceClass`

4. **AttenDetails** - Detailed attendance information
   - Table: `atten_details`
   - Global instance: `$attenDetailsClass`

5. **Timesheet** - Employee timesheet records
   - Table: `timesheet`
   - Global instance: `$timesheetClass`

6. **TimesheetDetails** - Detailed timesheet entries
   - Table: `timesheet_details`
   - Global instance: `$timesheetDetailsClass`

7. **Allocation** - Resource allocation tracking
   - Table: `res_allocation`
   - Global instance: `$allocationClass`
   - **Special Method**: `getTotalTime($jsonData)` - Calculates total time from JSON allocation data

### 2. atten_controller.php

**Location**: `app/atten_controller.php`

This is the main controller handling all attendance-related API endpoints and business logic.

#### Key Endpoints and Functionality:

##### Save Operations (`action=save`):

1. **leave_type** - Creates new leave types
   - Validates leave type name uniqueness
   - Requires `create_leave_types` permission
   - Fields: name, paid_type, added_by

2. **employee_leave** - Creates employee leave requests
   - Calculates leave days automatically using `getDateDifference()`
   - Validates uniqueness by leave_id, emp_id, and date_from
   - Requires `create_leave` permission
   - Auto-populates paid_type from leave type

3. **attendance** - Records attendance for employees
   - Supports bulk attendance for Employee/Department/Location
   - Auto-detects leave status (PL/UL) from employee_leave table
   - Creates attendance record and multiple atten_details records
   - Uses database transactions for data integrity
   - Requires `create_attendance` permission

4. **upload_attendance** - Bulk attendance upload via CSV
   - Validates CSV file format
   - Processes attendance data in batches
   - Creates or updates attendance records
   - Handles employee filtering by reference type
   - Includes comprehensive error handling

5. **timesheet** - Records employee timesheets
   - Creates timesheet record for specific date
   - Handles individual employee timesheet entries
   - Includes time_in and time_out tracking
   - Uses database transactions
   - Requires `create_timesheet` permission

6. **upload_timesheet** - Bulk timesheet upload via CSV
   - Similar to attendance upload but includes time tracking
   - Processes time_in and time_out data
   - Validates timesheet data format

7. **bulkAttendance** - Bulk attendance entry interface
   - Processes multiple employee attendance at once
   - Deletes previous records for the same date
   - Creates attendance and detail records

8. **bulkTimesheet** - Bulk timesheet entry interface
   - Similar to bulk attendance but for timesheets
   - Handles time_in and time_out for multiple employees

9. **allocation** - Resource allocation management
   - Stores JSON allocation data for projects/budgets
   - Links employees with supervisors
   - Manages monthly resource allocation

##### List Operations (`action=list`):

1. **leave_types** - Paginated leave types listing
2. **emp_leaves** - Employee leave records with joins
3. **attendance** - Attendance records with employee counts
4. **timesheet** - Timesheet records with employee counts
5. **allocations** - Resource allocation records

##### Get Operations (`action=get`):

1. **leave_type** - Single leave type details
2. **emp_leave** - Employee leave details with formatting
3. **4editAttendance** - Attendance editing interface with employee status
4. **downloadAttendanceCSV** - CSV template generation
5. **4editTimesheet** - Timesheet editing interface
6. **downloadTimesheetCSV** - Timesheet CSV template

## Database Structure Analysis

### Core Attendance Tables:

#### 1. attendance
- **Primary Key**: id
- **Purpose**: Main attendance records
- **Key Fields**:
  - `ref` - Reference type (Employee/Department/Location)
  - `ref_id` - Reference ID
  - `ref_name` - Reference name
  - `atten_date` - Attendance date
  - `added_by` - User who created record

#### 2. atten_details
- **Primary Key**: id
- **Purpose**: Individual employee attendance details
- **Key Fields**:
  - `atten_id` - Foreign key to attendance table
  - `emp_id` - Employee ID
  - `full_name`, `phone_number`, `email`, `staff_no` - Employee details
  - `status` - Attendance status (P, PL, UL, S, H, NH, N)
  - `atten_date` - Attendance date

#### 3. timesheet
- **Primary Key**: id
- **Purpose**: Timesheet records
- **Key Fields**:
  - `ts_date` - Timesheet date
  - `added_by` - User who created record

#### 4. timesheet_details
- **Primary Key**: id
- **Purpose**: Individual employee timesheet entries
- **Key Fields**:
  - `ts_id` - Foreign key to timesheet table
  - `emp_id` - Employee ID
  - Employee details (full_name, phone_number, email, staff_no)
  - `time_in`, `time_out` - Work hours
  - `ts_date` - Timesheet date
  - `status` - Work status

#### 5. leave_types
- **Primary Key**: id
- **Purpose**: Leave type definitions
- **Key Fields**:
  - `name` - Leave type name
  - `paid_type` - Paid/Unpaid classification
  - `added_by` - Creator user ID

#### 6. employee_leave
- **Primary Key**: id
- **Purpose**: Employee leave requests and records
- **Key Fields**:
  - `emp_id` - Employee ID
  - `leave_id` - Foreign key to leave_types
  - `date_from`, `date_to` - Leave period
  - `days_num` - Number of leave days
  - `paid_type` - Paid/Unpaid status
  - `status` - Leave status (Pending/Approved/Rejected/Cancelled)

#### 7. res_allocation
- **Primary Key**: id
- **Purpose**: Resource allocation tracking
- **Key Fields**:
  - `emp_id` - Employee ID
  - Employee details (full_name, phone_number, email, staff_no)
  - `sup_id` - Supervisor ID
  - Supervisor details (sup_name, sup_phone, sup_email, sup_staff_no)
  - `allocation` - JSON data for project/budget allocations
  - `month` - Allocation month

## Attendance Recording Workflows

### 1. Individual Attendance Recording
1. User selects attendance type (Employee/Department/Location)
2. Selects specific employee or organizational unit
3. Sets attendance date and status
4. System creates attendance record
5. System creates atten_details records for affected employees
6. System auto-detects leave status from employee_leave table

### 2. Bulk Attendance Recording
1. User selects department and location filters
2. System displays all active employees
3. User sets attendance status for multiple employees
4. System processes all records in a single transaction
5. Previous records for the same date are deleted and replaced

### 3. CSV Upload Workflow
1. User downloads CSV template with employee data
2. User fills attendance status and uploads file
3. System validates CSV format and data
4. System processes records in batches
5. System creates or updates attendance records
6. Error handling for invalid data

### 4. Timesheet Recording Workflow
1. Similar to attendance but includes time tracking
2. Records time_in and time_out for each employee
3. Supports both individual and bulk entry
4. Includes CSV upload functionality

### 5. Leave Management Workflow
1. Admin creates leave types (Paid/Unpaid)
2. Employees submit leave requests
3. System calculates leave days automatically
4. Leave status affects attendance recording
5. System auto-applies leave status during attendance

## Key Business Logic

### Attendance Status Codes:
- **P** - Present
- **PL** - Paid Leave
- **UL** - Unpaid Leave
- **S** - Sick
- **H** - Holiday
- **NH** - Not Hired Day
- **N** - Absent

### Leave Integration:
- System automatically checks for approved leave when recording attendance
- Leave type (Paid/Unpaid) determines attendance status (PL/UL)
- Leave dates are validated against attendance dates

### Resource Allocation:
- JSON-based allocation data for projects and budgets
- Links employees with supervisors
- Calculates total allocated time from JSON data
- Monthly allocation tracking

## Security and Permissions

### Required Permissions:
- `create_leave_types` - Create leave types
- `create_leave` - Create employee leave requests
- `create_attendance` - Record attendance
- `create_timesheet` - Record timesheets
- `create_allocation` - Manage resource allocation

### Data Validation:
- Input sanitization using `escapePostData()` and `escapeStr()`
- Uniqueness checks for critical records
- File type validation for uploads
- Database transaction management for data integrity

## Integration Points

### Employee Management:
- Links to employees table for employee data
- Uses employee status filtering (active/inactive)
- Integrates with organizational structure (branches, locations)

### Payroll Integration:
- Attendance data feeds into payroll calculations
- Timesheet data affects payroll processing
- Leave status impacts salary calculations

### Reporting:
- Attendance data used for various reports
- Timesheet data for project tracking
- Resource allocation for capacity planning

## Error Handling

### Database Transactions:
- All multi-record operations use transactions
- Rollback on errors to maintain data integrity
- Comprehensive error logging

### File Upload Validation:
- CSV format validation
- Row-by-row error tracking
- Graceful handling of invalid data

### User Feedback:
- JSON response format for all operations
- Detailed error messages
- Success/failure status indicators

This analysis provides a complete understanding of the attendance system's architecture, functionality, and integration points within the larger HRM application.