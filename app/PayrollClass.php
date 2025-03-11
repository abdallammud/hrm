<?php 
class EmployeeTransactions extends Model {
    public function __construct() {
        parent::__construct('employee_transactions', 'transaction_id');
    }
}

$GLOBALS['employeeTransactionsClass'] = $employeeTransactionsClass = new EmployeeTransactions();


// Payroll
class Payroll extends Model {
    public function __construct() {
        parent::__construct('payroll');
    }

    public function update_payrollRelatedTables($month, $payroll_id, $isDelete = false) {
        $conn = $GLOBALS['conn'];

        if($isDelete) $payroll_id = 0;

        $attendance = $conn->prepare("UPDATE `attendance` SET `payroll_id`=? WHERE `atten_date` LIKE '$month%'");
        $attendance->bind_param("s", $payroll_id);
        $attendance->execute();

        // Timesheet
        $timesheet = $conn->prepare("UPDATE `timesheet` SET `payroll_id`=? WHERE `ts_date` LIKE '$month%'");
        $timesheet->bind_param("s", $payroll_id);
        $timesheet->execute();

         // Transactions
        $transactions = $conn->prepare("UPDATE `employee_transactions` SET `payroll_id`=? WHERE `date` LIKE '$month%'");
        $transactions->bind_param("s", $payroll_id);
        $transactions->execute();

    }

    public function update_bankAccount($payroll_id, $payroll_detId = '') {
        $conn = $GLOBALS['conn'];
        $status = 'Paid';
        // Get the sum of all approved salaries for the payroll
        $query = "SELECT `id`, `bank_id`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `payroll_id` = ? AND `status` = 'Paid'";
        if (isset($payroll_detId) && $payroll_detId) {
            $query .= " AND `id` = $payroll_detId";
        }

        $net_salary = 0;
        $salaryStmt = $conn->prepare($query);
        $salaryStmt->bind_param("i", $payrollId);
        $salaryStmt->execute();
        $salaryResult = $salaryStmt->get_result();

        while ($row = $salaryResult->fetch_assoc()) {
            $net_salary = $row['net_salary'];
            $bank_id += $row['bank_id'];

            $bankInfo = get_data('bank_accounts', ['id' => $bank_id]);
            if ($bankInfo) {
                $bank_name = $bankInfo[0]['bank_name'];
                $account = $bankInfo[0]['account'];
                $balance = $bankInfo[0]['balance'];

                $new_balance = $balance + $net_salary;
                $updateBankQuery = "UPDATE `bank_accounts` SET `balance` = ?, `updated_by` = ?, `updated_date` = ? WHERE `id` = ?";
                $bankStmt = $conn->prepare($updateBankQuery);
                $updated_date = date("Y-m-d H:i:s");
                $bankStmt->bind_param("dsis", $new_balance, $paid_by, $updated_date, $slcBank);

                if (!$bankStmt->execute()) {
                    throw new Exception("Failed to update bank balance: " . $bankStmt->error);
                }
            }
        }
    }
}

$GLOBALS['payrollClass'] = $payrollClass = new Payroll();

// Payroll details
class PayrollDetailsClass extends Model {
    public function __construct() {
        parent::__construct('payroll_details');
    }
}

$GLOBALS['payrollDetailsClass'] = $payrollDetailsClass = new PayrollDetailsClass();