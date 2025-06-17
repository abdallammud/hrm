<?php 
class accountsClass extends Model {
    public function __construct() {
        parent::__construct('bank_accounts');
    }
}

class TransactionsClass extends Model {
    public function __construct() {
        parent::__construct('fn_transactions');
    }
}

$GLOBALS['accountsClass'] = $accountsClass = new accountsClass();
$GLOBALS['transactionsClass'] = $transactionsClass = new TransactionsClass();