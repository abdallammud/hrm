<?php 
class Company extends Model {
    public function __construct() {
        parent::__construct('company');
    }
}

$GLOBALS['companyClass'] = $companyClass = new Company();