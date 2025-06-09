<?php 
class Performance extends Model {
    public function __construct() {
        parent::__construct('performance', 'id');
    }

}

$GLOBALS['performanceClass'] = $performanceClass = new Performance();