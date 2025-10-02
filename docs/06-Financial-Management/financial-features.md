# Financial Management Features

## Overview

The HRM application includes a comprehensive financial management system that handles income, expenses, bank account management, and integration with payroll processing. The system provides real-time balance tracking, transaction categorization, and complete audit trails for all financial operations.

## Core Financial Features

### 1. Bank Account Management

#### User Story
As a financial administrator, I want to manage company bank accounts so that I can track balances and process financial transactions through the appropriate accounts.

#### Key Features

**Account Creation and Management**
- Add new bank accounts with name, account number, and initial balance
- Edit existing account information including status changes
- View all bank accounts with current balances and status
- Deactivate accounts while preserving historical data

**Balance Tracking**
- Real-time balance updates with every transaction
- Automatic balance calculations for income and expense transactions
- Balance validation before processing expenses or payroll payments
- Historical balance tracking through transaction records

**Account Information**
- Bank name and account number storage
- Current balance display with currency formatting
- Account status management (Active/Inactive)
- Creation and update timestamps with user tracking

#### Technical Implementation

**Database Structure**
```sql
CREATE TABLE bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(255) NOT NULL,
    account VARCHAR(255),
    balance DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    added_by INT,
    updated_by INT,
    updated_date TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**PHP Class Integration**
```php
class accountsClass extends Model {
    public function __construct() {
        parent::__construct('bank_accounts');
    }
}
```

### 2. Expense Management

#### User Story
As a financial administrator, I want to record and track company expenses so that I can maintain accurate financial records and ensure proper budget management.

#### Key Features

**Expense Recording**
- Record expenses with amount, date, and description
- Categorize expenses using financial account codes
- Link expenses to specific bank accounts
- Add payee information and reference numbers
- Validate bank balance before recording expenses

**Expense Categories**
- Integration with financial_accounts table for categorization
- Support for different expense types (Asset, Liability, Income, Expense)
- Flexible account structure for detailed expense tracking
- Custom descriptions for specific expense details

**Balance Integration**
- Automatic bank balance deduction upon expense creation
- Balance validation to prevent overdrafts
- Real-time balance updates across the system
- Transaction rollback on validation failures

#### Technical Implementation

**Expense Creation Process**
```php
// Validate bank balance
if($bankInfo['balance'] < $post['amount']) {
    $result['msg'] = 'Insufficient bank balance';
    $result['error'] = true;
    exit();
}

// Create expense record
$data = array(
    'type' => 'Expense',
    'bank_id' => $post['bank_id'],
    'bank_name' => $bankInfo['bank_name'],
    'amount' => $post['amount'],
    'fn_account_id' => $post['fn_account_id'],
    'fn_account_name' => $fnAccountInfo['name'],
    'payee_payer' => $post['payee_payer'],
    'description' => $post['description'],
    'ref_number' => $post['refNumber'],
    'added_by' => $_SESSION['user_id'],
    'added_date' => $post['paid_date'] . ' ' . date('H:i:s')
);

// Process with transaction safety
$GLOBALS['conn']->begin_transaction();
try {
    $transactionsClass->create($data);
    $newBalance = $bankInfo['balance'] - $post['amount'];
    $accountsClass->update($post['bank_id'], ['balance' => $newBalance]);
    $GLOBALS['conn']->commit();
} catch (Exception $e) {
    $GLOBALS['conn']->rollback();
    throw $e;
}
```

**Expense Modification**
- Edit existing expense records with differential balance calculation
- Update bank balances based on amount changes
- Maintain audit trail with update timestamps and user tracking
- Validate balance changes to prevent negative balances

### 3. Income Management

#### User Story
As a financial administrator, I want to record company income so that I can track revenue streams and maintain accurate financial records.

#### Key Features

**Income Recording**
- Record income with amount, date, and source information
- Categorize income using financial account codes
- Link income to specific bank accounts for deposit tracking
- Add payer information and reference numbers
- Automatic bank balance increases

**Income Categories**
- Integration with financial account structure
- Support for different income types and categories
- Flexible categorization for various revenue streams
- Custom descriptions for income source details

**Balance Integration**
- Automatic bank balance increase upon income recording
- Real-time balance updates across all system components
- Transaction safety with rollback capabilities
- Integration with financial reporting systems

#### Technical Implementation

**Income Creation Process**
```php
$data = array(
    'type' => 'Income',
    'bank_id' => $post['bank_id'],
    'bank_name' => $bankInfo['bank_name'],
    'amount' => $post['amount'],
    'fn_account_id' => $post['fn_account_id'],
    'fn_account_name' => $fnAccountInfo['name'],
    'payee_payer' => $post['payee_payer'],
    'description' => $post['description'],
    'ref_number' => $post['refNumber'],
    'added_by' => $_SESSION['user_id'],
    'added_date' => $post['paid_date'] . ' ' . date('H:i:s')
);

$GLOBALS['conn']->begin_transaction();
try {
    $transactionsClass->create($data);
    $newBalance = $bankInfo['balance'] + $post['amount'];
    $accountsClass->update(['balance' => $newBalance], ['id' => $post['bank_id']]);
    $GLOBALS['conn']->commit();
} catch (Exception $e) {
    $GLOBALS['conn']->rollback();
    throw $e;
}
```

### 4. Payroll Payment Integration

#### User Story
As a payroll administrator, I want to process employee salary payments through the financial system so that I can ensure accurate payment processing and maintain proper financial records.

#### Key Features

**Payroll Payment Processing**
- Process individual or bulk payroll payments
- Validate bank balance against total payroll amount
- Update payroll status from 'Approved' to 'Paid'
- Record payment details including bank account and payment date
- Maintain complete audit trail for all payments

**Balance Management**
- Automatic bank balance deduction for payroll payments
- Real-time balance validation before payment processing
- Support for partial payments and payment scheduling
- Integration with payroll calculation system

**Payment Tracking**
- Record payment method and bank account used
- Track payment dates and processing user
- Link payments to specific payroll periods
- Support for payment reversals and corrections

#### Technical Implementation

**Payroll Payment Process**
```php
// Calculate total payment amount
$query = "SELECT `id`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `payroll_id` = ? AND `status` = 'Approved'";

// Validate bank balance
if ($balance < $net_salary) {
    $result['msg'] = 'Insufficient bank balance. Required: ' . number_format($net_salary, 2) . ', Available: ' . number_format($balance, 2);
    $result['error'] = true;
    exit();
}

// Process payment with transaction safety
$conn->begin_transaction();
try {
    // Update payroll status
    $detailsQuery = "UPDATE `payroll_details` SET `status` = ?, `pay_date` = ?, `paid_by` = ?, `paid_through` = ?, `bank_id` = ? WHERE `payroll_id` = ? AND `status` = 'Approved'";
    
    // Update bank balance
    $new_balance = $balance - $net_salary;
    $updateBankQuery = "UPDATE `bank_accounts` SET `balance` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` = ?";
    
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    throw $e;
}
```

### 5. Transaction Management

#### User Story
As a financial administrator, I want to view and manage all financial transactions so that I can maintain oversight of company financial activities.

#### Key Features

**Transaction Listing**
- View all income and expense transactions
- Filter transactions by type, date, amount, or account
- Search transactions by payee, description, or reference number
- Sort transactions by various criteria with pagination support

**Transaction Details**
- Complete transaction information including amounts and dates
- Bank account and financial account categorization
- User tracking for creation and modification
- Status management for transaction lifecycle

**Data Export and Reporting**
- Export transaction data for external analysis
- Integration with reporting systems
- Real-time balance calculations and summaries
- Historical transaction analysis capabilities

## Financial Account Structure

### Account Categories

The system supports a flexible financial account structure with the following types:

**Asset Accounts**
- Bank accounts and cash equivalents
- Fixed assets and equipment
- Accounts receivable and investments

**Liability Accounts**
- Accounts payable and accrued expenses
- Loans and long-term debt
- Employee benefit obligations

**Income Accounts**
- Revenue from operations
- Interest and investment income
- Other income sources

**Expense Accounts**
- Operating expenses and overhead
- Payroll and employee costs
- Administrative and general expenses

### Account Integration

**Transaction Categorization**
- All transactions linked to appropriate financial accounts
- Automatic account name resolution for transaction records
- Support for custom account structures and hierarchies
- Integration with reporting and analysis systems

## Security and Access Control

### Permission-Based Access

**Bank Account Management**
- `create_bank_accounts` - Create new bank accounts
- `edit_bank_accounts` - Modify existing bank accounts
- `view_bank_accounts` - View bank account information

**Transaction Management**
- `create_expenses` - Record new expense transactions
- `edit_expenses` - Modify existing expense records
- `create_income` - Record new income transactions
- `edit_income` - Modify existing income records

**Payroll Integration**
- `manage_payroll` - Process payroll payments
- `view_payroll` - View payroll payment information

### Data Validation and Security

**Input Validation**
- Sanitization of all user inputs using `escapePostData()`
- Validation of numeric amounts and date formats
- Prevention of SQL injection through prepared statements
- Cross-site scripting (XSS) protection

**Business Logic Validation**
- Bank balance validation before expense processing
- Duplicate bank account name prevention
- Transaction amount and date validation
- User permission verification for all operations

**Audit Trail**
- Complete user tracking for all financial operations
- Timestamp recording for creation and modification
- Transaction history preservation
- Change logging for compliance and analysis

## Integration with Other Systems

### Payroll System Integration

**Payment Processing**
- Direct integration with payroll approval workflow
- Automatic balance updates during salary payments
- Support for bulk payment processing
- Payment status synchronization

**Employee Financial Records**
- Link to employee salary and transaction records
- Integration with deduction and benefit processing
- Tax calculation and payment processing
- Historical payment tracking

### Reporting System Integration

**Financial Reports**
- Real-time balance reporting
- Transaction history and analysis
- Income and expense categorization
- Budget tracking and variance analysis

**Dashboard Integration**
- Current balance display on financial dashboards
- Recent transaction summaries
- Payment status indicators
- Financial health metrics

### External System Integration

**Bank Integration Readiness**
- Structured data format for bank file imports
- Reference number tracking for bank reconciliation
- Transaction matching capabilities
- Export formats for accounting systems

**Accounting System Integration**
- Chart of accounts compatibility
- Transaction export capabilities
- Balance sheet and income statement data
- General ledger integration support