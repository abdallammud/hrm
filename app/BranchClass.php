<?php 

class Branch extends Model {
    public function __construct() {
        parent::__construct('branches');
    }
}


$GLOBALS['branchClass'] = $branchClass = new Branch();