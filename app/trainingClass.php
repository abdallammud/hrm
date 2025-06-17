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
}

$GLOBALS['trainersClass']     = $trainersClass = new Trainers();
$GLOBALS['trainingListClass'] = $trainingListClass = new TrainingList();