<?php 
class Company extends Model {
    public function __construct() {
        parent::__construct('company');
    }

    public function get($id) {
        return $this->read($id);
    }
}

$GLOBALS['companyClass'] = $companyClass = new Company();