<?php 
class BankAccounts extends Model {
    public function __construct() {
        parent::__construct('bank_accounts');
    }
}

$GLOBALS['bankAccountClass'] = $bankAccountClass = new BankAccounts();