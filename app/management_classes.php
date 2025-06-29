<?php 

class PromotionsClass extends Model {
    public function __construct() {
        parent::__construct('promotions', 'promotion_id');
    }
}

class TransfersClass extends Model {
    public function __construct() {
        parent::__construct('transfers', 'transfer_id');
    }
    public function get($id) {
        return get_data('transfers', ['transfer_id' => $id])[0];
    }
}

class ResignationsClass extends Model {
    public function __construct() {
        parent::__construct('resignations', 'resignation_id');
    }
    public function get($id) {
        return get_data('resignations', ['resignation_id' => $id])[0];
    }
}

class TerminationsClass extends Model {
    public function __construct() {
        parent::__construct('terminations', 'termination_id');
    }
    public function get($id) {
        return get_data('terminations', ['termination_id' => $id])[0];
    }
}

class WarningsClass extends Model {
    public function __construct() {
        parent::__construct('warnings', 'warning_id');
    }
    public function get($id) {
        return get_data('warnings', ['warning_id' => $id])[0];
    }
}

$GLOBALS['promotionsClass'] = $promotionsClass = new PromotionsClass();
$GLOBALS['transfersClass'] = $transfersClass = new TransfersClass();
$GLOBALS['resignationsClass'] = $resignationsClass = new ResignationsClass();
$GLOBALS['terminationsClass'] = $terminationsClass = new TerminationsClass();
$GLOBALS['warningsClass'] = $warningsClass = new WarningsClass();