<?php 
class States extends Model {
    public function __construct() {
        parent::__construct('states');
    }
}

$GLOBALS['statesClass'] = $statesClass = new States();