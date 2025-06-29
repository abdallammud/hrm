<?php 
class Designations extends Model {
    public function __construct() {
        parent::__construct('designations');
    }
}

$GLOBALS['designationsClass'] = $designationsClass = new Designations();


class Projects extends Model {
    public function __construct() {
        parent::__construct('projects');
    }
}

$GLOBALS['projectsClass'] = $projectsClass = new Projects();

class ContractTypes extends Model {
    public function __construct() {
        parent::__construct('contract_types');
    }
}

$GLOBALS['contractTypesClass'] = $contractTypesClass = new ContractTypes();


class BudgetCodes extends Model {
    public function __construct() {
        parent::__construct('budget_codes');
    }
}

$GLOBALS['budgetCodesClass'] = $budgetCodesClass = new BudgetCodes();


class BanksClass extends Model {
    public function __construct() {
        parent::__construct('banks');
    }
}

$GLOBALS['banksClass'] = $banksClass = new BanksClass();

class TransSubTypesClass extends Model {
    public function __construct() {
        parent::__construct('trans_subtypes');
    }
}

$GLOBALS['transSubTypesClass'] = $transSubTypesClass = new TransSubTypesClass();


class GoalTypesClass extends Model {
    public function __construct() {
        parent::__construct('goal_types');
    }
}

$GLOBALS['goalTypesClass'] = $goalTypesClass = new GoalTypesClass();


class AwardTypesClass extends Model {
    public function __construct() {
        parent::__construct('award_types');
    }
}

$GLOBALS['awardTypesClass'] = $awardTypesClass = new AwardTypesClass();

class FinancialAccountsClass extends Model {
    public function __construct() {
        parent::__construct('financial_accounts');
    }
}

class TrainingOptionsClass extends Model {
    public function __construct() {
        parent::__construct('training_options');
    }
}

class TrainingTypesClass extends Model {
    public function __construct() {
        parent::__construct('training_types');
    }
}

$GLOBALS['financialAccountsClass'] = $financialAccountsClass = new FinancialAccountsClass();
$GLOBALS['trainingOptionsClass'] = $trainingOptionsClass = new TrainingOptionsClass();
$GLOBALS['trainingTypesClass'] = $trainingTypesClass = new TrainingTypesClass();
