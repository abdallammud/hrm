<?php 
class Trainers extends Model {
    public function __construct() {
        parent::__construct('trainers');
    }

}

class TrainingList extends Model {
    public function __construct() {
        parent::__construct('training_list');
    }
    public function get($where = []) {
        return get_data('training_list', $where)[0];
    }
}

$GLOBALS['trainersClass']     = $trainersClass = new Trainers();
$GLOBALS['trainingListClass'] = $trainingListClass = new TrainingList();