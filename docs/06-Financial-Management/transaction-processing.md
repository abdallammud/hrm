# Transaction Processing Workflows

## Overview

This document details the technical workflows for processing financial transactions in the HRM application, including income recording, expense management, payroll payments, and bank account operations. Each workflow includes step-by-step processes, error handling, and integration points.

## Bank Account Management Workflow

### Create Bank Account Process

#### Step 1: User Input Validation
```php
// Sanitize input data
$post = escapePostData($_POST);

// Prepare account data
$data = array(
    'bank_name' => $post['name'], 
    'account' => isset($post['account']) ? $post['account']: "" ,  
    'balance' => isset($post['balance']) ? $post['balance']: "" , 
    'added_by' => $_SESSION['user_id']
);
```

#### Step 2: Business Logic Validation
```php
// Check for duplicate bank account names
check_exists('bank_accounts', ['bank_name' => $post['name']]);

// Verify user permissions
check_auth('create_bank_accounts');
```

#### Step 3: Database Operation
```php
try {
    // Create bank account record
    $result['id'] = $accountsClass->create($data);
    
    if($result['id']) {
        $result['msg'] = 'Bank account created successfully';
        $result['error'] = false;
    } else {
        $result['msg'] = 'Something went wrong, please try again';
        $result['error'] = true;
    }
} catch (Exception $e) {
    $result['msg'] = 'Error: Something went wrong';
    $result['sql_error'] = $e->getMessage();
    $result['error'] = true;
}
```

#### Step 4: Response Generation
```php
// Return JSON response
echo json_encode($result);
```

### Update Bank Account Process

#### Step 1: Data Preparation
```php
$updated_date = date('Y-m-d H:i:s');
$data = array(
    'bank_name' => $post['name'], 
    'account' => isset($post['account']) ? $post['account']: "" ,  
    'balance' => isset($post['balance']) ? $post['balance']: "" , 
    'status' => isset($post['slcStatus']) ? $post['slcStatus']: "Active" , 
    'updated_by' => $_SESSION['user_id'],
    'updated_date' => $updated_date
);
```

#### Step 2: Validation and Update
```php
// Check for duplicate names (excluding current record)
check_exists('bank_accounts', ['bank_name' => $post['name']], ['id' => $post['id']]);

// Verify permissions
check_auth('edit_bank_accounts');

// Update record
$result['id'] = $accountsClass->update($post['id'], $data);
```

## Expense Transaction Workflow

### Create Expense Process

#### Step 1: Input Processing and Validation
```php
// Sanitize input data
$post = escapePostData($_POST);

// Retrieve bank and financial account information
$bankInfo = get_data('bank_accounts', ['id' => $post['bank_id']])[0];
$fnAccountInfo = get_data('financial_accounts', ['id' => $post['fn_account_id']])[0];
```

#### Step 2: Balance Validation
```php
// Check if bank has sufficient balance
if($bankInfo['balance'] < $post['amount']) {
    $result['msg'] = 'Insufficient bank balance';
    $result['error'] = true;
    echo json_encode($result);
    exit();
}
```

#### Step 3: Transaction Data Preparation
```php
$data = array(
    'type' => 'Expense',
    'bank_id' => $post['bank_id'],
    'bank_name' => $bankInfo['bank_name'],
    'bank_account' => $bankInfo['account'],
    'amount' => $post['amount'],
    'fn_account_id' => $post['fn_account_id'],
    'fn_account_name' => $fnAccountInfo['name'],
    'payee_payer' => $post['payee_payer'],
    'description' => isset($post['description']) ? $post['description'] : "",
    'ref_number' => isset($post['refNumber']) ? $post['refNumber'] : "",
    'added_by' => $_SESSION['user_id'],
    'added_date' => $post['paid_date'] . ' ' . date('H:i:s')
);
```

#### Step 4: Authorization Check
```php
check_auth('create_expenses');
```

#### Step 5: Database Transaction Processing
```php
// Start database transaction
$GLOBALS['conn']->begin_transaction();

try {
    // Create expense record
    $result['id'] = $transactionsClass->create($data);
    
    // Update bank balance
    $newBalance = $bankInfo['balance'] - $post['amount'];
    $accountsClass->update($post['bank_id'], ['balance' => $newBalance]);
    
    // Commit transaction
    $GLOBALS['conn']->commit();
    
    if($result['id']) {
        $result['msg'] = 'Expense recorded successfully';
        $result['error'] = false;
    } else {
        $result['msg'] = 'Something went wrong, please try again';
        $result['error'] = true;
    }
} catch (Exception $e) {
    // Rollback on error
    $GLOBALS['conn']->rollback();
    throw $e;
}
```

### Update Expense Process

#### Step 1: Retrieve Current Data
```php
// Get current expense data
$currentExpense = get_data('fn_transactions', ['id' => $post['id']]);
if(!$currentExpense) {
    $result['msg'] = 'Expense not found';
    $result['error'] = true;
    echo json_encode($result);
    exit();
}
$currentExpense = $currentExpense[0];
```

#### Step 2: Calculate Balance Impact
```php
// Get bank and financial account info
$bankInfo = get_data('bank_accounts', ['id' => $post['slcBank']]);
$fnAccountInfo = get_data('financial_accounts', ['id' => $post['slcFinancialAccount']]);

// Calculate balance change
$oldAmount = $currentExpense['amount'];
$newAmount = $post['amount'];
$amountDifference = $newAmount - $oldAmount;
```

#### Step 3: Validate Balance Changes
```php
// Check if bank has sufficient balance for increase
if($amountDifference > 0 && $bankInfo['balance'] < $amountDifference) {
    $result['msg'] = 'Insufficient bank balance for the increase';
    $result['error'] = true;
    echo json_encode($result);
    exit();
}
```

#### Step 4: Update Transaction and Balance
```php
$transactionsClass->beginTransaction();
try {
    // Update expense record
    $result['id'] = $transactionsClass->update($post['id'], $data);
    
    // Update bank balance based on difference
    if($amountDifference != 0) {
        $newBalance = $bankInfo['balance'] - $amountDifference;
        $accountsClass->update($post['slcBank'], ['balance' => $newBalance]);
    }
    
    $transactionsClass->commit();
} catch (Exception $e) {
    $transactionsClass->rollback();
    throw $e;
}
```

## Income Transaction Workflow

### Create Income Process

#### Step 1: Data Collection and Validation
```php
$post = escapePostData($_POST);
$bankInfo = get_data('bank_accounts', ['id' => $post['bank_id']])[0];
$fnAccountInfo = get_data('financial_accounts', ['id' => $post['fn_account_id']])[0];
```

#### Step 2: Income Data Preparation
```php
$data = array(
    'type' => 'Income',
    'bank_id' => $post['bank_id'],
    'bank_name' => $bankInfo['bank_name'],
    'bank_account' => $bankInfo['account'],
    'amount' => $post['amount'],
    'fn_account_id' => $post['fn_account_id'],
    'fn_account_name' => $fnAccountInfo['name'],
    'payee_payer' => $post['payee_payer'],
    'description' => isset($post['description']) ? $post['description'] : "",
    'ref_number' => isset($post['refNumber']) ? $post['refNumber'] : "",
    'added_by' => $_SESSION['user_id'],
    'added_date' => $post['paid_date'] . ' ' . date('H:i:s')
);
```

#### Step 3: Authorization and Processing
```php
check_auth('create_income');

$GLOBALS['conn']->begin_transaction();
try {
    // Create income record
    $result['id'] = $transactionsClass->create($data);
    
    // Increase bank balance
    $newBalance = $bankInfo['balance'] + $post['amount'];
    $accountsClass->update(['balance' => $newBalance], ['id' => $post['bank_id']]);
    
    $GLOBALS['conn']->commit();
    $result['msg'] = 'Income added successfully';
    $result['error'] = false;
} catch (Exception $e) {
    $GLOBALS['conn']->rollback();
    $result['msg'] = 'Error: Something went wrong';
    $result['sql_error'] = $e->getMessage();
    $result['error'] = true;
}
```

### Update Income Process

#### Step 1: Current Data Retrieval
```php
$currentIncome = get_data('fn_transactions', ['id' => $post['id']]);
if(!$currentIncome) {
    $result['msg'] = 'Income not found';
    $result['error'] = true;
    echo json_encode($result);
    exit();
}
$currentIncome = $currentIncome[0];
```

#### Step 2: Balance Calculation and Update
```php
$bankInfo = get_data('bank_accounts', ['id' => $post['slcBankIncome']]);
$fnAccountInfo = get_data('financial_accounts', ['id' => $post['slcFinancialAccountIncome']]);

$oldAmount = $currentIncome['amount'];
$newAmount = $post['amountIncome'];
$amountDifference = $newAmount - $oldAmount;

$transactionsClass->beginTransaction();
try {
    $result['id'] = $transactionsClass->update($post['id'], $data);
    
    if($amountDifference != 0) {
        $newBalance = $bankInfo['balance'] + $amountDifference;
        $accountsClass->update($post['slcBankIncome'], ['balance' => $newBalance]);
    }
    
    $transactionsClass->commit();
} catch (Exception $e) {
    $transactionsClass->rollback();
    throw $e;
}
```

## Payroll Payment Workflow

### Single/Bulk Payroll Payment Process

#### Step 1: Payment Parameters Processing
```php
$post = escapePostData($_POST);
$status = 'Paid';

$payrollId = $post['payroll_id'];
$payroll_detId = isset($post['payroll_detId']) ? $post['payroll_detId'] : null;
$payroll_detIds = isset($post['payroll_detIds']) ? $post['payroll_detIds'] : null;
$slcBank = $post['slcBank'];
$payDate = $post['payDate'] . date(" H:i:s");
```

#### Step 2: Bank Information Validation
```php
// Fetch bank information
$bankInfo = get_data('bank_accounts', ['id' => $slcBank]);
if (!$bankInfo) {
    $result['msg'] = 'Something went wrong with the bank.';
    $result['error'] = true;
    echo json_encode($result);
    exit();
}

$bank_name = $bankInfo[0]['bank_name'];
$account = $bankInfo[0]['account'];
$balance = $bankInfo[0]['balance'];
```

#### Step 3: Salary Calculation
```php
// Get the sum of all approved salaries for the payroll
$query = "SELECT `id`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `payroll_id` = ? AND `status` = 'Approved'";

// Handle single or bulk payment
if ($payroll_detId) {
    $query .= " AND `id` = ?";
} else if ($payroll_detIds) {
    $ids = implode(',', array_map('intval', explode(',', $payroll_detIds)));
    $query .= " AND `id` IN ($ids)";
}

$net_salary = 0;
$salaryStmt = $conn->prepare($query);
if ($payroll_detId) {
    $salaryStmt->bind_param("ii", $payrollId, $payroll_detId);
} else {
    $salaryStmt->bind_param("i", $payrollId);
}
$salaryStmt->execute();
$salaryResult = $salaryStmt->get_result();

while ($row = $salaryResult->fetch_assoc()) {
    $net_salary += $row['net_salary'];
}
```

#### Step 4: Balance Validation
```php
// Check if bank has sufficient balance
if ($balance < $net_salary) {
    $result['msg'] = 'Insufficient bank balance. Required: ' . number_format($net_salary, 2) . ', Available: ' . number_format($balance, 2);
    $result['error'] = true;
    echo json_encode($result);
    exit();
}
```

#### Step 5: Payment Processing
```php
check_auth('manage_payroll');

$paid_through = $bank_name . ", " . $account;
$paid_by = $_SESSION['user_id'];

// Start MySQL transaction
$conn->begin_transaction();

// Update payroll details based on payroll_detId or bulk IDs
$detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `pay_date` = ?, `paid_by` = ?, `paid_through` = ?, `bank_id` = ? WHERE `payroll_id` = ? AND `status` = 'Approved'";

if ($payroll_detId) {
    $detailsQuery .= " AND `id` = ?";
    $detailsStmt = $conn->prepare($detailsQuery);
    $detailsStmt->bind_param("sssssii", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId, $payroll_detId);
} else if ($payroll_detIds) {
    $ids = implode(',', array_map('intval', explode(',', $payroll_detIds)));
    $detailsQuery .= " AND `id` IN ($ids)";
    $detailsStmt = $conn->prepare($detailsQuery);
    $detailsStmt->bind_param("sssssi", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId);
} else {
    $detailsStmt = $conn->prepare($detailsQuery);
    $detailsStmt->bind_param("sssssi", $status, $payDate, $paid_by, $paid_through, $slcBank, $payrollId);
}

if (!$detailsStmt->execute()) {
    throw new Exception("Failed to update payroll details: " . $detailsStmt->error);
}
```

#### Step 6: Bank Balance Update
```php
// Update bank balance
$new_balance = $balance - $net_salary;
$updateBankQuery = "UPDATE `bank_accounts` SET `balance` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` = ?";
$bankStmt = $conn->prepare($updateBankQuery);
$updated_date = date("Y-m-d H:i:s");
$bankStmt->bind_param("dsis", $new_balance, $paid_by, $updated_date, $slcBank);

if (!$bankStmt->execute()) {
    throw new Exception("Failed to update bank balance: " . $bankStmt->error);
}

// Commit transaction
$conn->commit();

$result['msg'] = 'Payroll paid successfully. Amount: ' . number_format($net_salary, 2);
$result['error'] = false;
```

### Payroll Rejection Workflow

#### Step 1: Rejection Parameters
```php
$post = escapePostData($_POST);
$status = 'Rejected';
$payroll_detId = isset($post['payroll_detId']) ? $post['payroll_detId'] : null;
$payroll_detIds = isset($post['payroll_detIds']) ? $post['payroll_detIds'] : null;
```

#### Step 2: Authorization and Processing
```php
check_auth('manage_payroll');

// Start MySQL transaction
$conn->begin_transaction();

// Update payroll details status to rejected
if ($payroll_detId) {
    $detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` = ? AND `status` = 'Approved'";
    $detailsStmt = $conn->prepare($detailsQuery);
    $detailsStmt->bind_param("sssi", $status, $_SESSION['user_id'], $updated_date, $payroll_detId);
} else if ($payroll_detIds) {
    $ids = implode(',', array_map('intval', explode(',', $payroll_detIds)));
    $detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` IN ($ids) AND `status` = 'Approved'";
    $detailsStmt = $conn->prepare($detailsQuery);
    $detailsStmt->bind_param("sss", $status, $_SESSION['user_id'], $updated_date);
}

if (!$detailsStmt->execute()) {
    throw new Exception("Failed to reject payroll details: " . $detailsStmt->error);
}

// Commit transaction
$conn->commit();
```

## Data Loading Workflows

### Bank Accounts Data Loading

#### Step 1: Parameter Processing
```php
$length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
$searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
```

#### Step 2: Sorting Configuration
```php
if (isset($_POST['order']) && isset($_POST['order'][0])) {
    $orderColumnMap = ['bank_name', 'account', 'balance', 'status'];
    $orderByIndex = (int)$_POST['order'][0]['column'];
    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
}
```

#### Step 3: Query Construction and Execution
```php
// Base query
$query = "SELECT * FROM `bank_accounts` WHERE `id` IS NOT NULL";

// Add search functionality
if ($searchParam) {
    $query .= " AND (`bank_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `account` LIKE '%" . escapeStr($searchParam) . "%')";
}

// Add ordering
$query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

// Execute query
$bank_accounts = $GLOBALS['conn']->query($query);

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM `bank_accounts` WHERE `id` IS NOT NULL";
if ($searchParam) {
    $countQuery .= " AND (`bank_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `account` LIKE '%" . escapeStr($searchParam) . "%')";
}

$totalRecordsResult = $GLOBALS['conn']->query($countQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
```

### Transaction Data Loading (Expenses/Income)

#### Step 1: Transaction Type Filtering
```php
// Base query for expenses
$query = "SELECT * FROM `fn_transactions` WHERE `type` = 'Expense'";

// Base query for income
$query = "SELECT * FROM `fn_transactions` WHERE `type` = 'Income'";
```

#### Step 2: Search and Filter Application
```php
// Add search functionality
if ($searchParam) {
    $query .= " AND (`fn_account_name` LIKE '%" . escapeStr($searchParam) . "%' OR `payee_payer` LIKE '%" . escapeStr($searchParam) . "%' OR `bank_name` LIKE '%" . escapeStr($searchParam) . "%' OR `ref_number` LIKE '%" . escapeStr($searchParam) . "%')";
}

// Add ordering
$query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";
```

## Error Handling and Recovery

### Transaction Rollback Patterns

#### Database Transaction Safety
```php
$GLOBALS['conn']->begin_transaction();
try {
    // Multiple database operations
    $transactionsClass->create($data);
    $accountsClass->update($bankId, $balanceData);
    
    $GLOBALS['conn']->commit();
} catch (Exception $e) {
    $GLOBALS['conn']->rollback();
    $result['msg'] = 'Error: Something went wrong';
    $result['sql_error'] = $e->getMessage();
    $result['error'] = true;
}
```

### Validation Error Handling

#### Business Logic Validation
```php
// Balance validation
if($bankInfo['balance'] < $post['amount']) {
    $result['msg'] = 'Insufficient bank balance';
    $result['error'] = true;
    echo json_encode($result);
    exit();
}

// Record existence validation
if(!$currentExpense) {
    $result['msg'] = 'Expense not found';
    $result['error'] = true;
    echo json_encode($result);
    exit();
}
```

### Response Standardization

#### Success Response Format
```php
$result = [
    'id' => $recordId,
    'msg' => 'Operation completed successfully',
    'error' => false
];
echo json_encode($result);
```

#### Error Response Format
```php
$result = [
    'msg' => 'Error description',
    'error' => true,
    'sql_error' => $exception->getMessage() // Optional for debugging
];
echo json_encode($result);
```

## Performance Considerations

### Query Optimization

#### Indexed Searches
- Bank account searches use indexed fields (`bank_name`, `account`)
- Transaction searches utilize composite indexes on type and date fields
- Pagination limits result sets for better performance

#### Prepared Statements
- All user input uses prepared statements to prevent SQL injection
- Parameter binding ensures type safety and performance optimization
- Reusable statement patterns for similar operations

### Transaction Management

#### Minimal Transaction Scope
- Database transactions kept as short as possible
- Only critical operations included in transaction blocks
- Immediate rollback on any failure condition

#### Balance Update Optimization
- Single balance update per transaction
- Differential calculations for expense/income updates
- Atomic balance operations to prevent race conditions