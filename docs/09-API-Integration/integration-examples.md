# Integration Examples

## Overview

This document provides practical examples of integrating with the HRM application API, including cURL commands, code snippets in various programming languages, and complete request-response cycle documentation. These examples demonstrate how to interact with the HRM system programmatically for common operations.

## Authentication Setup

Before making API calls, ensure you have a valid session. The HRM application uses PHP session-based authentication.

### Login Example

```bash
# Login to establish session
curl -X POST "http://your-domain.com/login.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=admin&password=your_password" \
  -c cookies.txt

# Use the session cookie for subsequent requests
curl -X POST "http://your-domain.com/app/hrm_controller.php?action=load&endpoint=employees" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "length=10&start=0"
```

## Employee Management Examples

### Create Employee

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/hrm_controller.php?action=save&endpoint=employee" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "full_name=John Doe" \
  -d "phone_number=+1234567890" \
  -d "email=john.doe@company.com" \
  -d "gender=Male" \
  -d "national_id=123456789" \
  -d "date_of_birth=1990-01-15" \
  -d "city=New York" \
  -d "address=123 Main Street" \
  -d "branch_id=1" \
  -d "location_id=1" \
  -d "position=Software Developer" \
  -d "hire_date=2023-01-01" \
  -d "contract_start=2023-01-01" \
  -d "contract_end=2024-12-31" \
  -d "salary=75000"
```

#### PHP Example
```php
<?php
// Start session and authenticate
session_start();
// Assume authentication is already done

$employeeData = [
    'full_name' => 'John Doe',
    'phone_number' => '+1234567890',
    'email' => 'john.doe@company.com',
    'gender' => 'Male',
    'national_id' => '123456789',
    'date_of_birth' => '1990-01-15',
    'city' => 'New York',
    'address' => '123 Main Street',
    'branch_id' => 1,
    'location_id' => 1,
    'position' => 'Software Developer',
    'hire_date' => '2023-01-01',
    'contract_start' => '2023-01-01',
    'contract_end' => '2024-12-31',
    'salary' => 75000
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://your-domain.com/app/hrm_controller.php?action=save&endpoint=employee');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($employeeData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
if ($result['error'] === false) {
    echo "Employee created successfully with ID: " . $result['id'];
} else {
    echo "Error: " . $result['msg'];
}
?>
```

#### Python Example
```python
import requests
import json

# Login and get session
session = requests.Session()
login_data = {
    'username': 'admin',
    'password': 'your_password'
}
login_response = session.post('http://your-domain.com/login.php', data=login_data)

# Create employee
employee_data = {
    'full_name': 'John Doe',
    'phone_number': '+1234567890',
    'email': 'john.doe@company.com',
    'gender': 'Male',
    'national_id': '123456789',
    'date_of_birth': '1990-01-15',
    'city': 'New York',
    'address': '123 Main Street',
    'branch_id': 1,
    'location_id': 1,
    'position': 'Software Developer',
    'hire_date': '2023-01-01',
    'contract_start': '2023-01-01',
    'contract_end': '2024-12-31',
    'salary': 75000
}

response = session.post(
    'http://your-domain.com/app/hrm_controller.php?action=save&endpoint=employee',
    data=employee_data
)

result = response.json()
if not result['error']:
    print(f"Employee created successfully with ID: {result['id']}")
else:
    print(f"Error: {result['msg']}")
```

#### JavaScript/Node.js Example
```javascript
const axios = require('axios');
const FormData = require('form-data');

// Create axios instance with cookie jar
const api = axios.create({
  baseURL: 'http://your-domain.com',
  withCredentials: true
});

// Login function
async function login(username, password) {
  try {
    const response = await api.post('/login.php', {
      username: username,
      password: password
    });
    return response.data;
  } catch (error) {
    console.error('Login failed:', error.response.data);
    throw error;
  }
}

// Create employee function
async function createEmployee(employeeData) {
  try {
    const response = await api.post('/app/hrm_controller.php?action=save&endpoint=employee', employeeData);
    return response.data;
  } catch (error) {
    console.error('Employee creation failed:', error.response.data);
    throw error;
  }
}

// Usage example
async function main() {
  try {
    // Login first
    await login('admin', 'your_password');
    
    // Create employee
    const employeeData = {
      full_name: 'John Doe',
      phone_number: '+1234567890',
      email: 'john.doe@company.com',
      gender: 'Male',
      national_id: '123456789',
      date_of_birth: '1990-01-15',
      city: 'New York',
      address: '123 Main Street',
      branch_id: 1,
      location_id: 1,
      position: 'Software Developer',
      hire_date: '2023-01-01',
      contract_start: '2023-01-01',
      contract_end: '2024-12-31',
      salary: 75000
    };
    
    const result = await createEmployee(employeeData);
    if (!result.error) {
      console.log(`Employee created successfully with ID: ${result.id}`);
    } else {
      console.log(`Error: ${result.msg}`);
    }
  } catch (error) {
    console.error('Operation failed:', error);
  }
}

main();
```

### Bulk Employee Upload

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/hrm_controller.php?action=save&endpoint=upload_employees" \
  -b cookies.txt \
  -H "Content-Type: multipart/form-data" \
  -F "file=@employees.csv"
```

#### PHP Example
```php
<?php
$csvFile = '/path/to/employees.csv';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://your-domain.com/app/hrm_controller.php?action=save&endpoint=upload_employees');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'file' => new CURLFile($csvFile, 'text/csv', 'employees.csv')
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
echo "Upload result: " . $result['msg'];
?>
```

### Load Employees with Pagination

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/hrm_controller.php?action=load&endpoint=employees" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "length=25" \
  -d "start=0" \
  -d "search[value]=john" \
  -d "order[0][column]=1" \
  -d "order[0][dir]=asc"
```

#### Python Example with Pagination
```python
def get_employees(session, page=0, page_size=25, search="", sort_column=1, sort_direction="asc"):
    data = {
        'length': page_size,
        'start': page * page_size,
        'search[value]': search,
        'order[0][column]': sort_column,
        'order[0][dir]': sort_direction
    }
    
    response = session.post(
        'http://your-domain.com/app/hrm_controller.php?action=load&endpoint=employees',
        data=data
    )
    
    return response.json()

# Usage
employees_data = get_employees(session, page=0, page_size=50, search="developer")
if not employees_data['error']:
    print(f"Found {employees_data['iTotalRecords']} total employees")
    for employee in employees_data['data']:
        print(f"- {employee['full_name']} ({employee['email']})")
```

## Attendance Management Examples

### Record Single Attendance

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/atten_controller.php?action=save&endpoint=attendance" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "ref=Employee" \
  -d "ref_id=123" \
  -d "ref_name=John Doe" \
  -d "atten_date=2023-12-01" \
  -d "atten_status=P"
```

#### JavaScript Example
```javascript
async function recordAttendance(employeeId, employeeName, date, status) {
  const attendanceData = {
    ref: 'Employee',
    ref_id: employeeId,
    ref_name: employeeName,
    atten_date: date,
    atten_status: status
  };
  
  try {
    const response = await api.post('/app/atten_controller.php?action=save&endpoint=attendance', attendanceData);
    return response.data;
  } catch (error) {
    console.error('Attendance recording failed:', error.response.data);
    throw error;
  }
}

// Usage
recordAttendance(123, 'John Doe', '2023-12-01', 'P')
  .then(result => {
    if (!result.error) {
      console.log('Attendance recorded successfully');
    } else {
      console.log('Error:', result.msg);
    }
  });
```

### Bulk Attendance Entry

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/atten_controller.php?action=save&endpoint=bulkAttendance" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "date=2023-12-01" \
  -d "employees[]=123" \
  -d "employees[]=124" \
  -d "employees[]=125" \
  -d "statuses[]=P" \
  -d "statuses[]=A" \
  -d "statuses[]=L"
```

#### PHP Example
```php
<?php
function recordBulkAttendance($employees, $statuses, $date) {
    $data = [
        'date' => $date,
        'employees' => $employees,
        'statuses' => $statuses
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://your-domain.com/app/atten_controller.php?action=save&endpoint=bulkAttendance');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$employees = [123, 124, 125];
$statuses = ['P', 'A', 'L'];
$result = recordBulkAttendance($employees, $statuses, '2023-12-01');
echo $result['msg'];
?>
```

### Upload Attendance CSV

#### Python Example
```python
def upload_attendance_csv(session, csv_file_path):
    with open(csv_file_path, 'rb') as f:
        files = {'file': ('attendance.csv', f, 'text/csv')}
        response = session.post(
            'http://your-domain.com/app/atten_controller.php?action=save&endpoint=upload_attendance',
            files=files
        )
    return response.json()

# Usage
result = upload_attendance_csv(session, '/path/to/attendance.csv')
if not result['error']:
    print(f"Attendance upload successful: {result['msg']}")
else:
    print(f"Upload failed: {result['msg']}")
```

## Payroll Management Examples

### Generate Payroll

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/payroll_controller.php?action=save&endpoint=payroll" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "ref=Department" \
  -d "ref_id=1" \
  -d "ref_name=IT Department" \
  -d "month[]=2023-12"
```

#### Python Example
```python
def generate_payroll(session, ref_type, ref_id, ref_name, months):
    data = {
        'ref': ref_type,
        'ref_id': ref_id,
        'ref_name': ref_name,
        'month[]': months
    }
    
    response = session.post(
        'http://your-domain.com/app/payroll_controller.php?action=save&endpoint=payroll',
        data=data
    )
    
    return response.json()

# Generate payroll for IT Department for December 2023
result = generate_payroll(session, 'Department', 1, 'IT Department', ['2023-12'])
if not result['error']:
    print(f"Payroll generated successfully with ID: {result['id']}")
else:
    print(f"Payroll generation failed: {result['msg']}")
```

### Add Employee Transaction

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/payroll_controller.php?action=save&endpoint=transaction" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "emp_id=123" \
  -d "transaction_type=Allowance" \
  -d "transaction_subtype=Transport" \
  -d "amount=500.00" \
  -d "date=2023-12-01" \
  -d "description=Monthly transport allowance" \
  -d "status=Approved"
```

#### JavaScript Example
```javascript
async function addEmployeeTransaction(empId, type, subtype, amount, date, description, status = 'Pending') {
  const transactionData = {
    emp_id: empId,
    transaction_type: type,
    transaction_subtype: subtype,
    amount: amount,
    date: date,
    description: description,
    status: status
  };
  
  try {
    const response = await api.post('/app/payroll_controller.php?action=save&endpoint=transaction', transactionData);
    return response.data;
  } catch (error) {
    console.error('Transaction creation failed:', error.response.data);
    throw error;
  }
}

// Usage
addEmployeeTransaction(123, 'Allowance', 'Transport', 500.00, '2023-12-01', 'Monthly transport allowance', 'Approved')
  .then(result => {
    if (!result.error) {
      console.log(`Transaction created with ID: ${result.id}`);
    } else {
      console.log(`Error: ${result.msg}`);
    }
  });
```

### Approve Payroll

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/payroll_controller.php?action=update&endpoint=approvePayroll" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "id=201" \
  -d "status=Approved"
```

### Pay Payroll

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/finance_controller.php?action=update&endpoint=payPayroll" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "payroll_id=201" \
  -d "slcBank=1" \
  -d "payDate=2023-12-15"
```

## Financial Management Examples

### Record Expense

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/finance_controller.php?action=save&endpoint=expense" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "bank_id=1" \
  -d "fn_account_id=2" \
  -d "amount=1500.00" \
  -d "payee_payer=Office Supplies Inc" \
  -d "description=Monthly office supplies" \
  -d "refNumber=INV-2023-001" \
  -d "paid_date=2023-12-01"
```

#### PHP Example
```php
<?php
function recordExpense($bankId, $accountId, $amount, $payee, $description, $refNumber, $date) {
    $data = [
        'bank_id' => $bankId,
        'fn_account_id' => $accountId,
        'amount' => $amount,
        'payee_payer' => $payee,
        'description' => $description,
        'refNumber' => $refNumber,
        'paid_date' => $date
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://your-domain.com/app/finance_controller.php?action=save&endpoint=expense');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$result = recordExpense(1, 2, 1500.00, 'Office Supplies Inc', 'Monthly office supplies', 'INV-2023-001', '2023-12-01');
if (!$result['error']) {
    echo "Expense recorded with ID: " . $result['id'];
} else {
    echo "Error: " . $result['msg'];
}
?>
```

### Record Income

#### Python Example
```python
def record_income(session, bank_id, account_id, amount, payer, description, ref_number, date):
    data = {
        'bank_id': bank_id,
        'fn_account_id': account_id,
        'amount': amount,
        'payee_payer': payer,
        'description': description,
        'refNumber': ref_number,
        'paid_date': date
    }
    
    response = session.post(
        'http://your-domain.com/app/finance_controller.php?action=save&endpoint=income',
        data=data
    )
    
    return response.json()

# Usage
result = record_income(session, 1, 3, 25000.00, 'Client ABC Corp', 'Project milestone payment', 'PAY-2023-001', '2023-12-01')
if not result['error']:
    print(f"Income recorded with ID: {result['id']}")
else:
    print(f"Error: {result['msg']}")
```

## Performance Management Examples

### Create Performance Indicator

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/performance_controller.php?action=save&endpoint=indicators" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "department_id=1" \
  -d "designation_id=2" \
  -d "department=IT Department" \
  -d "designation=Senior Developer" \
  -d "business_pro=4" \
  -d "oral_com=5" \
  -d "leadership=3" \
  -d "project_mgt=4" \
  -d "res_allocating=4"
```

#### JavaScript Example
```javascript
async function createPerformanceIndicator(deptId, desigId, deptName, desigName, ratings) {
  const indicatorData = {
    department_id: deptId,
    designation_id: desigId,
    department: deptName,
    designation: desigName,
    ...ratings
  };
  
  try {
    const response = await api.post('/app/performance_controller.php?action=save&endpoint=indicators', indicatorData);
    return response.data;
  } catch (error) {
    console.error('Performance indicator creation failed:', error.response.data);
    throw error;
  }
}

// Usage
const ratings = {
  business_pro: 4,
  oral_com: 5,
  leadership: 3,
  project_mgt: 4,
  res_allocating: 4
};

createPerformanceIndicator(1, 2, 'IT Department', 'Senior Developer', ratings)
  .then(result => {
    if (!result.error) {
      console.log(`Performance indicator created with ID: ${result.id}`);
    } else {
      console.log(`Error: ${result.msg}`);
    }
  });
```

### Create Employee Appraisal

#### Python Example
```python
def create_employee_appraisal(session, emp_id, dept_id, desig_id, dept_name, desig_name, indicator_rating, appraisal_rating, month, remarks):
    data = {
        'emp_id': emp_id,
        'department_id': dept_id,
        'designation_id': desig_id,
        'department': dept_name,
        'designation': desig_name,
        'indicator_rating': indicator_rating,
        'appraisal_rating': appraisal_rating,
        'month': month,
        'remarks': remarks
    }
    
    response = session.post(
        'http://your-domain.com/app/performance_controller.php?action=save&endpoint=appraisals',
        data=data
    )
    
    return response.json()

# Usage
result = create_employee_appraisal(
    session, 123, 1, 2, 'IT Department', 'Senior Developer', 
    4.0, 4.2, '2023-12', 'Excellent performance this month'
)
if not result['error']:
    print(f"Appraisal created with ID: {result['id']}")
else:
    print(f"Error: {result['msg']}")
```

## Training Management Examples

### Create Training Program

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/training_controller.php?action=save&endpoint=training" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "type_id=1" \
  -d "option_id=2" \
  -d "trainer_id=3" \
  -d "employee_id[]=123" \
  -d "employee_id[]=124" \
  -d "employee_id[]=125" \
  -d "cost=2500.00" \
  -d "start_date=2023-12-15" \
  -d "end_date=2023-12-20" \
  -d "description=Advanced software development training"
```

#### PHP Example
```php
<?php
function createTrainingProgram($typeId, $optionId, $trainerId, $employeeIds, $cost, $startDate, $endDate, $description) {
    $data = [
        'type_id' => $typeId,
        'option_id' => $optionId,
        'trainer_id' => $trainerId,
        'employee_id' => $employeeIds,
        'cost' => $cost,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'description' => $description
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://your-domain.com/app/training_controller.php?action=save&endpoint=training');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$employeeIds = [123, 124, 125];
$result = createTrainingProgram(1, 2, 3, $employeeIds, 2500.00, '2023-12-15', '2023-12-20', 'Advanced software development training');
echo $result['msg'];
?>
```

## User Management Examples

### Create User

#### cURL Example
```bash
curl -X POST "http://your-domain.com/app/users_controller.php?action=save&endpoint=user" \
  -b cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "full_name=Admin User" \
  -d "phone=+1234567890" \
  -d "email=admin@company.com" \
  -d "username=admin" \
  -d "password=securepassword123" \
  -d "sysRole=1"
```

#### JavaScript Example
```javascript
async function createUser(userData) {
  try {
    const response = await api.post('/app/users_controller.php?action=save&endpoint=user', userData);
    return response.data;
  } catch (error) {
    console.error('User creation failed:', error.response.data);
    throw error;
  }
}

// Usage
const userData = {
  full_name: 'Admin User',
  phone: '+1234567890',
  email: 'admin@company.com',
  username: 'admin',
  password: 'securepassword123',
  sysRole: 1
};

createUser(userData)
  .then(result => {
    if (!result.error) {
      console.log(`User created with ID: ${result.id}`);
    } else {
      console.log(`Error: ${result.msg}`);
    }
  });
```

## Error Handling Examples

### Comprehensive Error Handling

#### Python Example
```python
import requests
import json
import logging

class HRMAPIClient:
    def __init__(self, base_url, username, password):
        self.base_url = base_url
        self.session = requests.Session()
        self.login(username, password)
    
    def login(self, username, password):
        try:
            response = self.session.post(f"{self.base_url}/login.php", {
                'username': username,
                'password': password
            })
            response.raise_for_status()
            logging.info("Successfully logged in")
        except requests.exceptions.RequestException as e:
            logging.error(f"Login failed: {e}")
            raise
    
    def make_request(self, endpoint, data=None, files=None):
        try:
            if files:
                response = self.session.post(f"{self.base_url}/{endpoint}", data=data, files=files)
            else:
                response = self.session.post(f"{self.base_url}/{endpoint}", data=data)
            
            response.raise_for_status()
            result = response.json()
            
            if result.get('error', False):
                raise Exception(f"API Error: {result.get('msg', 'Unknown error')}")
            
            return result
            
        except requests.exceptions.RequestException as e:
            logging.error(f"Request failed: {e}")
            raise
        except json.JSONDecodeError as e:
            logging.error(f"Invalid JSON response: {e}")
            raise
        except Exception as e:
            logging.error(f"API operation failed: {e}")
            raise
    
    def create_employee(self, employee_data):
        return self.make_request('app/hrm_controller.php?action=save&endpoint=employee', employee_data)
    
    def upload_employees(self, csv_file_path):
        with open(csv_file_path, 'rb') as f:
            files = {'file': ('employees.csv', f, 'text/csv')}
            return self.make_request('app/hrm_controller.php?action=save&endpoint=upload_employees', files=files)

# Usage with error handling
try:
    client = HRMAPIClient('http://your-domain.com', 'admin', 'password')
    
    employee_data = {
        'full_name': 'John Doe',
        'phone_number': '+1234567890',
        'email': 'john.doe@company.com',
        # ... other fields
    }
    
    result = client.create_employee(employee_data)
    print(f"Employee created successfully with ID: {result['id']}")
    
except Exception as e:
    print(f"Operation failed: {e}")
```

### Retry Logic Example

#### JavaScript Example
```javascript
class HRMAPIClient {
  constructor(baseUrl, maxRetries = 3) {
    this.baseUrl = baseUrl;
    this.maxRetries = maxRetries;
    this.session = axios.create({
      baseURL: baseUrl,
      withCredentials: true,
      timeout: 30000
    });
  }
  
  async login(username, password) {
    try {
      const response = await this.session.post('/login.php', {
        username: username,
        password: password
      });
      console.log('Successfully logged in');
      return response.data;
    } catch (error) {
      console.error('Login failed:', error.response?.data || error.message);
      throw error;
    }
  }
  
  async makeRequestWithRetry(endpoint, data, retries = 0) {
    try {
      const response = await this.session.post(endpoint, data);
      const result = response.data;
      
      if (result.error) {
        throw new Error(`API Error: ${result.msg}`);
      }
      
      return result;
    } catch (error) {
      if (retries < this.maxRetries && this.isRetryableError(error)) {
        console.log(`Request failed, retrying... (${retries + 1}/${this.maxRetries})`);
        await this.delay(1000 * Math.pow(2, retries)); // Exponential backoff
        return this.makeRequestWithRetry(endpoint, data, retries + 1);
      }
      throw error;
    }
  }
  
  isRetryableError(error) {
    return error.code === 'ECONNRESET' || 
           error.code === 'ETIMEDOUT' || 
           (error.response && error.response.status >= 500);
  }
  
  delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }
  
  async createEmployee(employeeData) {
    return this.makeRequestWithRetry('/app/hrm_controller.php?action=save&endpoint=employee', employeeData);
  }
}

// Usage
const client = new HRMAPIClient('http://your-domain.com');

async function main() {
  try {
    await client.login('admin', 'password');
    
    const result = await client.createEmployee({
      full_name: 'John Doe',
      phone_number: '+1234567890',
      email: 'john.doe@company.com',
      // ... other fields
    });
    
    console.log(`Employee created successfully with ID: ${result.id}`);
  } catch (error) {
    console.error('Operation failed after retries:', error.message);
  }
}

main();
```

## Batch Processing Examples

### Batch Employee Creation

#### Python Example
```python
import concurrent.futures
import time

def create_employees_batch(client, employees_data, batch_size=10):
    """Create employees in batches to avoid overwhelming the server"""
    results = []
    
    for i in range(0, len(employees_data), batch_size):
        batch = employees_data[i:i + batch_size]
        batch_results = []
        
        with concurrent.futures.ThreadPoolExecutor(max_workers=5) as executor:
            future_to_employee = {
                executor.submit(client.create_employee, emp_data): emp_data 
                for emp_data in batch
            }
            
            for future in concurrent.futures.as_completed(future_to_employee):
                emp_data = future_to_employee[future]
                try:
                    result = future.result()
                    batch_results.append({
                        'employee': emp_data['full_name'],
                        'success': True,
                        'id': result['id']
                    })
                except Exception as e:
                    batch_results.append({
                        'employee': emp_data['full_name'],
                        'success': False,
                        'error': str(e)
                    })
        
        results.extend(batch_results)
        
        # Add delay between batches to avoid rate limiting
        if i + batch_size < len(employees_data):
            time.sleep(2)
    
    return results

# Usage
employees_data = [
    {'full_name': 'John Doe', 'email': 'john@company.com', ...},
    {'full_name': 'Jane Smith', 'email': 'jane@company.com', ...},
    # ... more employees
]

results = create_employees_batch(client, employees_data)
successful = [r for r in results if r['success']]
failed = [r for r in results if not r['success']]

print(f"Successfully created {len(successful)} employees")
print(f"Failed to create {len(failed)} employees")
```

## Complete Integration Workflow Example

### Full Employee Onboarding Workflow

#### Python Example
```python
class EmployeeOnboardingWorkflow:
    def __init__(self, hrm_client):
        self.client = hrm_client
    
    def onboard_employee(self, employee_data, documents=None):
        """Complete employee onboarding workflow"""
        try:
            # Step 1: Create employee record
            print(f"Creating employee: {employee_data['full_name']}")
            employee_result = self.client.create_employee(employee_data)
            employee_id = employee_result['id']
            print(f"Employee created with ID: {employee_id}")
            
            # Step 2: Upload employee documents
            if documents:
                for doc in documents:
                    print(f"Uploading document: {doc['name']}")
                    doc_result = self.client.upload_employee_document(
                        employee_id, doc['name'], doc['folder_id'], 
                        doc['type_id'], doc['file_path']
                    )
                    print(f"Document uploaded with ID: {doc_result['id']}")
            
            # Step 3: Set up initial payroll transactions
            print("Setting up initial payroll transactions")
            initial_transactions = [
                {
                    'emp_id': employee_id,
                    'transaction_type': 'Allowance',
                    'transaction_subtype': 'Welcome Bonus',
                    'amount': 1000.00,
                    'date': employee_data['hire_date'],
                    'description': 'Welcome bonus for new employee',
                    'status': 'Approved'
                }
            ]
            
            for transaction in initial_transactions:
                trans_result = self.client.add_employee_transaction(transaction)
                print(f"Transaction created with ID: {trans_result['id']}")
            
            # Step 4: Record initial attendance (if hire date is today)
            from datetime import date
            if employee_data['hire_date'] == str(date.today()):
                print("Recording initial attendance")
                attendance_result = self.client.record_attendance(
                    employee_id, employee_data['full_name'], 
                    employee_data['hire_date'], 'P'
                )
                print(f"Attendance recorded with ID: {attendance_result['id']}")
            
            print(f"Employee onboarding completed successfully for {employee_data['full_name']}")
            return {
                'success': True,
                'employee_id': employee_id,
                'message': 'Onboarding completed successfully'
            }
            
        except Exception as e:
            print(f"Onboarding failed for {employee_data['full_name']}: {e}")
            return {
                'success': False,
                'error': str(e),
                'message': 'Onboarding failed'
            }

# Usage
workflow = EmployeeOnboardingWorkflow(client)

employee_data = {
    'full_name': 'John Doe',
    'phone_number': '+1234567890',
    'email': 'john.doe@company.com',
    'gender': 'Male',
    'hire_date': '2023-12-01',
    'branch_id': 1,
    'location_id': 1,
    'position': 'Software Developer',
    'salary': 75000
}

documents = [
    {
        'name': 'Resume',
        'folder_id': 1,
        'type_id': 1,
        'file_path': '/path/to/resume.pdf'
    },
    {
        'name': 'ID Copy',
        'folder_id': 2,
        'type_id': 2,
        'file_path': '/path/to/id_copy.pdf'
    }
]

result = workflow.onboard_employee(employee_data, documents)
print(result['message'])
```

## Performance Optimization Tips

### Connection Pooling and Session Management

#### PHP Example
```php
<?php
class HRMAPIClient {
    private $baseUrl;
    private $cookieJar;
    private $curlHandle;
    
    public function __construct($baseUrl) {
        $this->baseUrl = $baseUrl;
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'hrm_cookies');
        $this->curlHandle = curl_init();
        
        // Set common cURL options
        curl_setopt_array($this->curlHandle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $this->cookieJar,
            CURLOPT_COOKIEFILE => $this->cookieJar,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'HRM API Client 1.0'
        ]);
    }
    
    public function login($username, $password) {
        curl_setopt_array($this->curlHandle, [
            CURLOPT_URL => $this->baseUrl . '/login.php',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'username' => $username,
                'password' => $password
            ])
        ]);
        
        $response = curl_exec($this->curlHandle);
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        
        if ($httpCode !== 200) {
            throw new Exception("Login failed with HTTP code: $httpCode");
        }
        
        return json_decode($response, true);
    }
    
    public function makeRequest($endpoint, $data = []) {
        curl_setopt_array($this->curlHandle, [
            CURLOPT_URL => $this->baseUrl . '/' . $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ]);
        
        $response = curl_exec($this->curlHandle);
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        
        if ($httpCode !== 200) {
            throw new Exception("Request failed with HTTP code: $httpCode");
        }
        
        $result = json_decode($response, true);
        if ($result['error']) {
            throw new Exception("API Error: " . $result['msg']);
        }
        
        return $result;
    }
    
    public function __destruct() {
        if ($this->curlHandle) {
            curl_close($this->curlHandle);
        }
        if (file_exists($this->cookieJar)) {
            unlink($this->cookieJar);
        }
    }
}
?>
```

### Rate Limiting and Throttling

#### JavaScript Example
```javascript
class RateLimitedHRMClient {
  constructor(baseUrl, requestsPerSecond = 5) {
    this.baseUrl = baseUrl;
    this.requestQueue = [];
    this.isProcessing = false;
    this.requestInterval = 1000 / requestsPerSecond; // ms between requests
    this.lastRequestTime = 0;
  }
  
  async queueRequest(endpoint, data) {
    return new Promise((resolve, reject) => {
      this.requestQueue.push({ endpoint, data, resolve, reject });
      this.processQueue();
    });
  }
  
  async processQueue() {
    if (this.isProcessing || this.requestQueue.length === 0) {
      return;
    }
    
    this.isProcessing = true;
    
    while (this.requestQueue.length > 0) {
      const now = Date.now();
      const timeSinceLastRequest = now - this.lastRequestTime;
      
      if (timeSinceLastRequest < this.requestInterval) {
        await this.delay(this.requestInterval - timeSinceLastRequest);
      }
      
      const { endpoint, data, resolve, reject } = this.requestQueue.shift();
      
      try {
        const result = await this.makeDirectRequest(endpoint, data);
        resolve(result);
      } catch (error) {
        reject(error);
      }
      
      this.lastRequestTime = Date.now();
    }
    
    this.isProcessing = false;
  }
  
  async makeDirectRequest(endpoint, data) {
    // Direct HTTP request implementation
    const response = await fetch(`${this.baseUrl}/${endpoint}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(data),
      credentials: 'include'
    });
    
    const result = await response.json();
    if (result.error) {
      throw new Error(`API Error: ${result.msg}`);
    }
    
    return result;
  }
  
  delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }
  
  // Public methods that use the queue
  async createEmployee(employeeData) {
    return this.queueRequest('app/hrm_controller.php?action=save&endpoint=employee', employeeData);
  }
  
  async recordAttendance(attendanceData) {
    return this.queueRequest('app/atten_controller.php?action=save&endpoint=attendance', attendanceData);
  }
}

// Usage
const client = new RateLimitedHRMClient('http://your-domain.com', 3); // 3 requests per second

// These requests will be automatically rate-limited
Promise.all([
  client.createEmployee(employee1Data),
  client.createEmployee(employee2Data),
  client.createEmployee(employee3Data),
  client.recordAttendance(attendance1Data),
  client.recordAttendance(attendance2Data)
]).then(results => {
  console.log('All requests completed:', results);
}).catch(error => {
  console.error('Some requests failed:', error);
});
```

This comprehensive integration examples document provides practical, real-world examples of how to interact with the HRM application API using various programming languages and approaches. It covers authentication, CRUD operations, bulk processing, error handling, and performance optimization techniques.